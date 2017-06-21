<?php

abstract class Model {
	
	/**
	 * The current row if "found"
	 **/
	protected $currentRow = array();
	
	/**
	 * The current rows whom found
	 **/
	protected $currentRows = array();
	
	/**
	 * If multiple rows were returned in the last query, then true
	 * Otherwise false
	 * Use to determine for save method
	 **/
	protected $multiple = false;
	
	/**
	 * Use a table or not, if false then don't use a table
	 **/
	public $useTable = false;
	
	/**
	 * The primary key for the table
	 **/
	public $primaryKey = 'id';
	
	/**
	 * PDO instance for database communication
	 **/
	protected $pdo = null;
	
	/**
	 * Integer to store total results for pagination calculation
	 **/
	protected $pages = null;

	/**
	 * Property to store temporary data for global use
	 */
	public $data = null;
	
	/**
	 * Constructor
	 **/
	public function __construct() {
		if ($this->useTable !== false && is_string($this->useTable)) {
			$this->pdo = App::getPDO();
		}
		else {
			$this->pdo = App::getPDO();
		}
	}

	/**
	 * Searches a single row by the ID
	 *
	 * @param $id
	 * @param null $criteria
	 * @return Array
	 */
	public final function findByID($id, $criteria = null) {
		$where = $this->primaryKey.' = '.$id;
		
		// Indien er where-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['where'])) {
			$keywords = array('and', 'or', '=', '!=','like', 'between');
			$j = 0;
			
			foreach($criteria['where'] as $i => $v) {
				$spkws = false;
				
				foreach($keywords as $kw) {
					if (strstr(strtolower($v), $kw)) {
						$spkws = true;
					}
				}
				
				$v = (is_string($v) && !$spkws) ? "'".$v."'" : $v;
				
				if (strstr(strtolower($v), $keywords)) {
					$where .= $v;
				}
				else {
					if (!is_numeric($i)) {
						$where .= ($j++ <= 0) ? 'WHERE '.$i.' = '.$v : ' AND '.$i.' = '.$v;
					}
					else {
						$where .= ($j++ <= 0) ? 'WHERE '.$v : ' AND '.$v;
					}
				}
			}
		}
		
		$sql = "SELECT _SELECT_ FROM $this->useTable WHERE $where LIMIT 1;";
		
		// Indien er select-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['select'])) {
			$select = '';
			$t = count($criteria['select'])-1;
			$i = 0;
			
			foreach($criteria['select'] as $s) {
				$select .= ($i > $t) ? $s.',' : $s;
			}
			
			$sql = str_replace('_SELECT_', $select, $sql);
		}
		else {
			$sql = str_replace('_SELECT_', '*', $sql);
		}
		
		$qry = $this->pdo->query($sql);
		
		if ($qry !== false) {
			$this->currentRow = $qry->fetch(PDO::FETCH_ASSOC);;
			return $this->currentRow;
		}
		else {
			return array();
		}
	}

	/**
	 * Search in the db
	 *
	 * @param $criteria
	 * @return Array $ret
	 */
	public final function find($criteria) {
		$sql = "SELECT _select_ FROM _from_ _where_ _groupby_ _having_ _orderby_ _limit_;";
		$sql = $this->_formatSQL($sql, $criteria);

		$qry = $this->pdo->query($sql);
		
		if ($qry instanceof PDOStatement && $qry !== false) {
			$ret = $qry->fetchAll(PDO::FETCH_ASSOC);

			// Currentrows instellen indien 'save' wordt gecalled :)
			
			if (count($ret) > 1) { 
				$this->multiple = true;
				$this->currentRows = $ret;
			}
			else {
				$this->multiple = false;
				$this->currentRow = $ret;
			}
			
			return $ret;
		}
		else {
			echo "Er is een fout in de query syntax.<br />Qry: $sql";
			return false;
		}
	}

	/**
	 * Format the sql with the given criteria
	 *
	 * @param $sql
	 * @param $criteria
	 * @return String $sql
	 */
	private final function _formatSQL($sql, $criteria) {
		$select = '*';
		$from = '';
		$where = '';
		$groupby = '';
		$having = '';
		$orderby = '';
		$limit = '';
		
		// Indien er select-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['select'])) {
			$select = '';
			$t = count($criteria['select'])-1;
			$i = 0;
			
			foreach($criteria['select'] as $s) {
				$select .= ($i++ < $t) ? $s.',' : $s;
			}
		}
		
		// Indien er from-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['from'])) {
			// Als de from-index een array is dan anders verwerken
			// Behandelen als relational from-clause
			if (is_array($criteria['from'])) {
				// Als er meerdere indexen zijn, dan pas behandelen als join query's
				if (isset($criteria['from']['type'])) {
					$join_type = $criteria['from']['type'];
					
					// De index unsetten omdat die niet mag meegeteld worden
					unset($criteria['from']['type']);
				}
				else {
					$join_type = 'INNER JOIN';
				}

				if (count($criteria['from']) > 1) {
					$i = 0;
					$j = 0;
					//$t = count($criteria['from']) -1;

					foreach($criteria['from'] as $f) {
						if ($i++ == 0) {
							$from .= $f['table'].' ';
						}
						else {
							$primary_key_first = isset($criteria['from'][$j-1]['primary']) ? $criteria['from'][$j-1]['primary'] : 'id';
							$primary_key_last =  isset($f['primary']) ? $f['primary'] : 'id';


							$from .= $join_type.' '.$f['table'];
							$from .= ' ON '.$criteria['from'][$j-1]['table'].'.'.$primary_key_first.' = ';
							$from .= $f['table'].'.'.$primary_key_last;
						}
						
						$j++;
					}
				}
				else {
					$from .= $criteria['from'][0]['table'];
				}
			}
			else {
				$from .= $criteria['from'];
			}
		}
		else {
			// Als er geen tabel is meegegeven dan de useTable gebruiken
			$from = $this->useTable;
		}
		
		// Indien er where-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['where'])) {
			$keywords = array('and', 'or', '=', '!=','like', 'between');
			$j = 0;
			
			foreach($criteria['where'] as $i => $v) {
				$spkws = false;

				// Loop through each SQL-keyword
				// Search SQL-kw match within where item
				foreach($keywords as $kw) {
					if (strstr($v, strtoupper($kw))) {
						$spkws = true;
					}
				}
				
				$v = (is_string($v) && !$spkws) ? "'".$v."'" : $v;
				
				if (@strstr(strtolower($v), $keywords)) {
					$where .= $v;
				}
				else {
					if (!is_numeric($i)) {
						$where .= ($j++ <= 0) ? 'WHERE '.$i.' = '.$v : ' AND '.$i.' = '.$v;
					}
					else {
						$where .= ($j++ <= 0) ? 'WHERE '.$v : ' AND '.$v;
					}
				}
			}
		}
		
		// Indien er groupby-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['groupby'])) {
			$groupby = 'GROUP BY ';
			$t = count($criteria['groupby'])-1;
			$i = 0;
			
			foreach($criteria['groupby'] as $g) {
				$groupby .= ($i++ < $t) ? $g.',' : $g;
			}
		}
		
		// Indien er having-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['having'])) {
			$keywords = array('and', 'or', '=', '!=','like', 'between');
			
			foreach($criteria['having'] as $i => $v) {
				$v = (is_string($v)) ? "'".$v."'" : $v;
				
				if (strstr(strtolower($v), $keywords)) {
					$having .= $v;
				}
				else {
					$having .= ' AND '.$i.' = '.$v;	
				}
			}
		}
		
		// Indien er een orderby-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['orderby'])) {
			$orderby = 'ORDER BY ';
			if (!is_array($criteria['orderby'])) {
				$orderby .= $criteria['orderby'];
			}
			else {
				$i=0;
				foreach($criteria['orderby'] as $o) {
					$orderby .= ($i++ <= 0) ? $o['field'].' '.$o['order'] : ','.$o['field'].' '.$o['order'];
				}
			}
		}
		
		// Indien er een limit-clause criteria is meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['limit']) && !isset($criteria['pagination'])) {
			if (!is_array($criteria['limit'])) {

				$limit = 'LIMIT '.$criteria['limit'];
			}
			else {
				$limit = 'LIMIT '.$criteria['limit'][0].','.$criteria['limit'][1];
			}
		}
		
		// Indien er pagination gegevens zijn meegegeven
		if ($criteria !== null && is_array($criteria) && isset($criteria['pagination'])) {
			$pp = isset($criteria['pagination']['results_per_page']) ? $criteria['pagination']['results_per_page'] : Config::PAGINATION_DEFAULT_RESULTS_PER_PAGE; 
			$page = isset($criteria['pagination']['page']) ? $criteria['pagination']['page'] : 1;
			
			$limit = 'LIMIT '.($page-1)*$pp.', '.$pp;
			
			// Unset limit & pagination criteria to get all results for pagination calculation
			unset($criteria['limit']); unset($criteria['pagination']);
			
			// Getting total results
			$results = count($this->find($criteria));
			$this->pages = array();

			for ($i = 1; $i < ceil($results/$pp)+1; $i++) {
				// Nieuwe array insteken
				$temp = array();

				$temp['current'] = ($i == $page) ? true : false;
				$temp['num'] = $i;
				$this->pages[] = $temp;

				unset($temp);
			}
		}
		
		// Alle _tags_ replacen
		$sql = str_replace('_select_', $select, $sql);
		$sql = str_replace('_from_', $from, $sql);
		$sql = str_replace('_where_', $where, $sql);
		$sql = str_replace('_groupby_', $groupby, $sql);
		$sql = str_replace('_having_', $having, $sql);
		$sql = str_replace('_orderby_', $orderby, $sql);
		$sql = str_replace('_limit_', $limit, $sql);

		//echo $sql;
		return $sql;
	}

	/**
	 * Returns the pages array for pagination formatting
	 * @return Array
	 */
	public function getPaginationArray() {
		return $this->pages;
	}

	/**
	 * Saves the last gotten row(s)
	 * Uses an associative array for the data
	 *
	 * @param $data
	 * @param bool $force
	 * @return bool
	 */
	public final function save($data, $force = false) {
		// Assign temp property with our data array
		$this->data = $data;

		$data = $this->beforeSave($data);

		// Check if the data isn't null, if so use the data property
		$data = ($data === null) ? $this->data : $data;

		if (!is_array($data)) {
			return false;
		}
		else {
			if ($this->multiple) {
				if (count($data) == count($this->currentRows) || $force === true) {
					$j = 0;
					
					foreach($this->currentRows as $row) {
						$i = 0;
						$where = '';
						$update = '';
						$qry = "UPDATE $this->useTable SET _update_ WHERE _where_";
						
						foreach($row as $field => $value) {
							$where .= ($i++ == 0) ? $field.' = \''.$value.'\'' : ' AND '.$field.' = \''.$value.'\'';
						}
						
						$where .= ' LIMIT 1;';
						
						$x = 0;
						if (count($data) > 1) {
							foreach($data[$j++] as $field => $value) {
								$update .= ($x++ == 0) ? $field.' = \''.$value.'\'' : ', '.$field.' = \''.$value.'\'';
							}
						}
						else {
							if (isset($data[0])) {
								foreach($data[0] as $field => $value) {
									$update .= ($x++ == 0) ? $field.' = \''.$value.'\'' : ', '.$field.' = \''.$value.'\'';
								}
							}
							else {
								return false;
							}
						}
						
						$sql = str_replace(array('_update_', '_where_'), array($update, $where), $qry);
						$qry = $this->pdo->exec($sql);
					}
				}
				else {
					return false;
				}
			}
			else {
				// Zeker voor zijn dat er maar één array is :)
				if (count($data) > 1) {
					if (isset($data[0])) {
						$data = $data[0];
					}
					else {
						foreach($data as $data_key) {
							unset($data);
							$data = $data_key;
							break;
						}
					}
				}
				
				// Controleren of de laatste query wel resultaten opleverde
				// Anders false returnen
				if (count($this->currentRow) == 0 || !isset($this->currentRow[0])) {
					return false;
				}
				
				$i = 0;
				$x = 0;
				$where = '';
				$update = '';
				$qry = "UPDATE $this->useTable SET _update_ WHERE _where_ LIMIT 1;";
				
				foreach($this->currentRow[0] as $field => $value) {
					$where .= ($i++ == 0) ? $field.' = \''.$value.'\'' : ' AND '.$field.' = \''.$value.'\'';
				}
				
				foreach($data[0] as $field => $value) {
					$update .= ($x++ == 0) ? $field.' = \''.$value.'\'' : ', '.$field.' = \''.$value.'\'';
				}
				
				$sql = str_replace(array('_update_', '_where_'), array($update, $where), $qry);
				$qry = $this->pdo->exec($sql);
			}
		}
		
		if ($qry > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Insert's a new row into the database table
	 *
	 * @param $data
	 * @return bool
	 */
	public final function create($data) {
		if (!$this->pdo instanceof PDO) {
			$this->pdo = App::getPDO();
		}

		if (isset($data['use_table'])) {
			$useTable = $data['use_table'];
			unset($data['use_table']);
		}
		else {
			$useTable = $this->useTable;
		}

		$sql = "INSERT INTO $useTable (_FIELDS_) VALUES(_VALUES_);";
		$fields = '';
		$values = '';
		
		$i = 0;
		foreach($data as $field => $value) {
			$fields .= ($i++ == 0) ? $field : ','.$field;
		}
		
		$i = 0;
		foreach($data as $field => $value) {
			
			if (!is_numeric($value)) {
				$value = "'".$value."'";
			}
			
			$values .= ($i++ == 0) ? $value : ','.$value;
		}
		
		$sql = str_replace(array('_FIELDS_', '_VALUES_'), array($fields, $values), $sql);
		$success = $this->pdo->exec($sql);
		
		// Checks if the query has been successfully executed
		// If at least one row was altered, then it's a successful query
		if ($success > 0) {
			// Do settings in case another save-method is called
			$this->multiple = false;
			$this->currentRow = $data;

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Method with a purpose to be overwritten
	 * Is called before saving data using the save-method
	 *
	 * Assign $this->data with new data array when using filter
	 * Or return the new data
	 */
	public function beforeSave($data) {
		return $data;
	}
}