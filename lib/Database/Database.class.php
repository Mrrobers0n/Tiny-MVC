<?php
/**
 * Database Class
 * Alles om te connecteren met de database, en query's gemakkelijk af te handelen
 *
 * @author             Robbe I.
 * @copyright         Robbe I. - Alle rechten voorbehouden
 * @created         23/05/2011
 * @last-update     14/02/2012
 * @email            robbe@westdesign.be
 **/

// Executeerbare DBstatements moeten deze interface implementeren
interface iDBExecutableStatement {
    public function launch();
}

// (Select) DBStatements moeten deze interface implementeren
interface iDBSelectableStatement {
    public function fetch();
    public function fetchAll();
    public function getRowCount();
}

class Database {

    const mysql_host = 'localhost';
    const mysql_username = 'carparkm_dev2';
    const mysql_password = 'AgM3+=pS#q_&%$9gVT';
    const mysql_database = 'carparkm_dev2';

    // MySQL Connection
    private $conn;

    private $sql;

	/**
	 * Constructor
	 * Initialiseerde MySQL connectie, en selecteerde db
	 *
	 * @since 1.0.0
	 *
	 * @acces public
	 * @throws DBException
	 * @return \Database
	 */
    public function __construct() {
        $conn = mysql_connect(Database::mysql_host, Database::mysql_username, Database::mysql_password);

        if (!$conn) {
            throw new DBException('Could not connect to MySQL Database.');
        }
        else {
            $db = mysql_select_db(Database::mysql_database, $conn);

            if (!$db) {
                throw new DBException('De databank "'.Database::mysql_database.'" kon niet gevonden worden.<br />'
                                     .'Gelieve de correcte gegevens in te voeren.');
            }
            else {
                $this->conn = $conn;
            }
        }
    }

	/**
	 * select()
	 * Methode die een nieuwe DBSelectStatement instantie maakt, en deze retourneerd
	 *
	 * @since 1.0.0
	 *
	 * @param String $sql                | De SQL
	 * @param Array $fields                | Associatieve array met waarden
	 * @param null $page
	 * @param null $resultsPerPage
	 * @acces public
	 * @return DBSelectStatement
	 */
    public function select($sql, $fields, $page = null, $resultsPerPage = null) {
        $this->sql = $sql;

        if (!strstr($sql, '_SELECT_')) {
            $this->throwException("Uw select query moet \"_SELECT_\" bevatten waar de geselecteerde waarden moeten komen.");
        }
        else {
            if ($page === null && $resultsPerPage === null) {
                return new DBSelectStatement($sql, $fields);
            }
            else {
                return new DBSelectPaginationStatement($sql, $fields, $page, $resultsPerPage);
            }
        }
    }

    /**
     * update()
     * Methode die een nieuwe DBUpdateStatement instantie maakt, en deze retourneerd
     *
     * @since 1.0.0
     *
     * @param String $sql                | De SQL
     * @param Array $fields                | Associatieve array met waarden die moeten worden geüpdate, en de waarden
     * @acces public
     * @return DBUpdateStatement
     **/
    public function update($sql, $fields) {
        $this->sql = $sql;

        if (!strstr($sql, '_UPDATE_')) {
            $this->throwException("Uw update query moet \"_UPDATE_\" bevatten waar de waarden moeten komen die worden geüpdate.");
        }
        else {
            return new DBUpdateStatement($sql, $fields);
        }
    }

    /**
     * insert()
     * Methode die een nieuwe DBInsertStatement instantie maakt, en deze retourneerd
     *
     * @since 1.0.0
     *
     * @param String $sql                | De SQL
     * @param Array $fields                | Associatieve array met de velden die moeten worden ingevoerd, en de waarden die deze velden moeten bevatten
     * @acces public
     * @return DBInsertStatement
     **/
    public function insert($sql, $fields) {
        $this->sql = $sql;

        if (!strstr($sql, '_FIELDS_') || !strstr($sql, '_VALUES_')) {
            $this->throwException("Uw insert query moet \"_FIELDS_\" en \"_VALUES_\" bevatten waar de velden en waarden moeten komen.");
        }
        else {
            return new DBInsertStatement($sql, $fields);
        }
    }

	/**
	 * throwException()
	 * Gooit een DBException
	 *
	 * @since 1.0.0
	 *
	 * @param String $message            | Bericht v/d uitzondering
	 * @param int $code                    | Errorcode v/d uitzondering
	 * @throws DBException
	 * @access public
	 * @return void
	 */
    public function throwException($message = null, $code = 500) {
        if ($message === null) {
            throw new DBException('Er is een fout MySQL fout opgetreden.<br />'
                                 .'Query: '.$this->sql.'<br />'
                                 .'Melding: '.mysql_error, 100);
        }
        else {
            throw new DBException($message, $code);
        }
    }

}

class DBException extends Exception {}

/**
 * SelectStatement
 * Child-class of Database which provies extended methods specific for select query's
 */
class DBSelectStatement extends Database {

    protected $query;
    protected $rown = 0;

    protected $tempRow;

	/**
	 * Constructor
	 * Initialiseerd alles om de select-query uit te voeren
	 *
	 * @since 1.0.0
	 *
	 * @param String $sql            | SQL
	 * @param Array $fields            | Array met de te selecteren waarden
	 * @acces public
	 * @return \DBSelectStatement
	 */
    public function __construct($sql, $fields) {
        $select = $this->getFields($fields);
        $sql = str_replace('_SELECT_', $select, $sql);

        $this->query = @mysql_query($sql);
    }

    /**
     * fetch()
     * Haalt een bepaalde rij op v/e bepaalde query, en retourneerd deze
     *
     * @since 1.0.0
     *
     * @param bool $asArray        | Retourneer als array ja/nee
     * @param int $rown            | Rijnummer v/h resultaat (indien niet als array)
     * @acces public
     * @return String $result
     **/
    public function fetch($asArray = false, $xss = true, $rown = 0) {
        if (!$asArray) {
            if ($xss) {
                $result = @htmlspecialchars(@mysql_result($this->query, $rown));
            }
            else {
                $result = @mysql_result($this->query, $rown);
            }
        }
        else {
            if (!$xss) {
                $result = @mysql_fetch_array($this->query);
            }
            else {
                $ret = @mysql_fetch_array($this->query);
                $result = array();

                foreach($ret as $i => $v) {
                    $result[$i] = @htmlspecialchars($v);
                }
            }
        }

        return $result;
    }

    /**
     * fetchAll()
     * Retourneerd een associatieve array met gevraagde resultaten, indien deze als array word opgevraagd.
     * Anders wordt er door iedere row geloopt, indien gevraagd, tegen xss beveiligd. En geretourneerd
     *
     * @since 1.0.0
     *
     * @param bool $array            | retourneer als array
     * @param bool $multi            | retourneer als multi-dimensionale array, ook als er maar één rij uit de opgegeven query komt
     * @param bool $xss                | filter op xss ja/nee
     * @acces public
     * @return Array
     **/
    public function fetchAll($array = false, $multi = false, $xss = true) {

        // Word alles liever als array opgevraagd
        if ($array) {
            $ret = array();

            if ($multi || $this->getRowCount() > 1) {
                while($data = @mysql_fetch_array($this->query)) {
                    $ret[] = $data;
                }

                return $ret;
            }
            else {
                return @mysql_fetch_array($this->query);
            }
        }
        else { // Door iedere row loopen en deze retourneren
            while($this->tempRow = @mysql_fetch_array($this->query)) {
                $data = array();

                // Als beveiligen tegen XSS aan staat, een htmlspecialchars over alle waarden gebruiken.
                if ($xss) {
                    foreach($this->tempRow as $field => $value) {
                        // Alleen als het veld geen numerieke waarde is, deze toevoegen aan de return array
                        if (!is_numeric($field)) {
                            $data[$field] = htmlspecialchars($value);
                        }
                    }

                    return $data;
                }
                else {
                    return $this->tempRow;
                }
            }
        }
    }


    /**
     * getRowCount()
     * Retourneerd het aantal rijen dat een bepaalde query heeft opgeleverd
     *
     * @since 1.0.0
     *
     * @acces public
     * @return int [rows]
     **/
    public function getRowCount() {
        $rows = @mysql_num_rows($this->query);

        return $rows;
    }

    /**
     * getFields()
     * Maakt aan de hand van een array met waarden, een stukje SQL voor de select-query
     * en retourneerd deze.
     *
     * @since 1.0.0
     *
     * @param Array $fields                | Array met waarden
     * @acces private
     * @return String [sql]
     **/
    private function getFields($fields) {
        $fieldsCount = count($fields);
        $ret = "";

        for($i=0; $i < $fieldsCount; $i++) {
            $ret .= ($i == $fieldsCount-1) ? $fields[$i] : $fields[$i].', ';
        }

        return $ret;
    }
}

class DBUpdateStatement extends Database implements iDBExecutableStatement{

    private $sql;

    /**
     * Constructor
     * Initialiseerd de class voor het uitvoeren v/d query
     *
     * @since 1.0.0
     *
     * @param String $sql                | De SQL
     * @param Array $fields                | Array met de velden & waarden die moeten worden geüpdate
     * @acces public
     * @return void
     **/
    public function __construct($sql, $fields) {
        $this->sql = $this->formatSQL($sql, $fields);
    }

    /**
     * launch()
     * Voert de query uit, en retourneerd true indien de query geslaagd is, anders false
     *
     * @since 1.0.0
     *
     * @acces public
     * @return bool [gelukt]
     **/
    public function launch() {
        $qry = @mysql_query($this->sql);

        if (@mysql_affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * formatSQL()
     * Maakt de SQL-Query op aan de hand van de gegeven waarden
     *
     * @since 1.0.0
     *
     * @param String $sql                | De SQL
     * @param Array $fields                | Array met de velden & waarden die moeten worden geüpdate
     * @acces private
     * @return String [sql]
     **/
    private function formatSQL($sql, $fields) {
        $fieldsCount = count($fields);
        $i = 0;
        $update = "";

        foreach($fields as $field => $value) {
            if ($i++ == $fieldsCount-1) {
                if (!is_numeric($value) && !strstr($value, $field)) {
                    $update .= $field.' = \''.mysql_real_escape_string($value).'\'';
                }
                else {
                    $update .= $field.' = '.mysql_real_escape_string($value);
                }
            }
            else {
                if (!is_numeric($value) && !strstr($value, $field)) {
                    $update .= $field.' = \''.mysql_real_escape_string($value).'\', ';
                }
                else {
                    $update .= $field.' = '.mysql_real_escape_string($value).', ';
                }
            }
        }

        $sql = str_replace('_UPDATE_', $update, $sql);

        return $sql;
    }
}

class DBInsertStatement extends Database implements iDBExecutableStatement {

    private $sql;
    private $qry;

	/**
	 * Constructor
	 * Initialiseerd de instantie voor het uitvoeren v/d query
	 *
	 * @since 1.0.0
	 *
	 * @param String $sql                | De SQL
	 * @param Array $fields                | Array met de te invoeren velden & zijn waarden.
	 * @acces public
	 * @return \DBInsertStatement
	 */
    public function __construct($sql, $fields) {
        $fs = $this->getFields($fields);
        $vs = $this->getValues($fields);

        $this->sql = $this->getSQL($sql, $fs, $vs);
    }

    /**
     * launch()
     * Voert de query uit, en retourneerd true indien deze geslaagd is, anders false
     *
     * @since 1.0.0
     *
     * @acces public
     * @return bool [gelukt]
     **/
    public function launch() {
        $qry = @mysql_query($this->sql);
        $this->qry = $qry;

        if (@mysql_affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * getLastRowID()
     * Retourneerd de ID van de laatste insert-query
     *
     * @since 1.0.0
     *
     * @acces public
     * @return int [id]
     **/
    public function getLastRowID() {
        return @mysql_insert_id($this->qry);
    }

    /**
     * getFields()
     * Filtert de insert gegevens uit de meegegeven array, en zet deze om in een stukje SQL.
     * Tenslotte wordt dit geretourneerd
     *
     * @since 1.0.0
     *
     * @param Array $fields                | Array met waarden
     * @acces private
     * @return String [sql]
     **/
    private function getFields($fields) {
        $i = 0;
        $isert_fields = "";

        foreach($fields as $field => $value) {
            if ($i++ == 0) {
                $isert_fields .= $field;
            }
            else {
                $isert_fields .= ', '.$field;
            }
        }

        return $isert_fields;
    }

    /**
     * getValues()
     * Filtert de waarden v/d insert gegevens uit de meegegeven array, en zet deze om in een stukje SQL.
     * Tenslotte wordt dit geretourneerd
     *
     * @since 1.0.0
     *
     * @param Array $fields                | Array met waarden
     * @acces private
     * @return String [sql]
     **/
    private function getValues($fields) {
        $i = 0;
        $vals = "";

        foreach($fields as $field => $value) {
            if ($i++ == 0) {
                $vals .= '\''.@mysql_real_escape_string($value).'\'';;
            }
            else {
                $vals .= ', \''.@mysql_real_escape_string($value).'\'';
            }
        }

        return $vals;
    }

    /**
     * getSQL()
     * Maakt de afgewerkte SQL-code met de meegeven stukjes SQL-code,
     * en retourneerd deze
     *
     * @since 1.0.0
     *
     * @param String $sql                | De SQL
     * @param String $fields            | De SQL (met insert-velden)
     * @param String $fields            | De SQL (met de waarden v/d insert velden)
     * @acces private
     * @return String [sql]
     **/
    private function getSQL($sql, $fields, $values) {
        $search = array(
            '_FIELDS_',
            '_VALUES_'
        );

        $replace = array(
            $fields,
            $values
        );

        return str_replace($search, $replace, $sql);
    }
}

/**************************************************************************************
 * EXTENSIES
 * Alle DBStatements die standaard niet in de class zijn ingebouwd kunnen hier.
 **************************************************************************************/

/**
 * DBSelectPaginationStatement
 * Child-class of Database which provies extended methods specific for select query's
 * Pagination v1.0.0
 */
class DBSelectPaginationStatement extends Database {

    protected $query;
    protected $pQuery;
    protected $rown = 0;

    protected $tempRow;

    protected $page;
    protected $resultsPerPage;

    /**
     * Constructor
     * Initialiseerd alles om de select-query uit te voeren
     *
     * @since 1.0.0
     *
     * @param String $sql            | SQL
     * @param Array $fields            | Array met de te selecteren waarden
     * @acces public
     * @return void
     **/
    public function __construct($sql, $fields, $page, $resultsPerPage = 20) {
        $select = $this->getFields($fields);
        $nsql = str_replace(array('_SELECT_', '_LIMIT_'), array($select,''), $sql);

        $this->page = $page - 1;
        $this->resultsPerPage = $resultsPerPage;

        $this->query = @mysql_query($nsql);

        $limit = $this->getLimit();
        $psql = str_replace(array('_SELECT_', '_LIMIT_'), array($select, $limit), $sql);
        $this->pQuery = @mysql_query($psql);
    }

    /**
     * calculatePages()
     * Bereken het aantal paginas
     *
     * @since v1.0.0
     * @acces private
     * @return int [pages]
     **/
    private function calculatePages() {
        $results = $this->getRowCount();

        return round($results/$this->resultsPerPage);
    }

    /**
     * getPages()
     * Retourneerd een array met alle pagina's die moeten worden getoond.
     *
     * @since v1.0.0
     * @acces public
     * @return Array [paginas]
     **/
    public function getPages() {
        $pages = $this->calculatePages();
        $ret = array();

        $firstP = false;
        $lastP = false;

        $this->page;

        for ($i=1; $i <= $pages; $i++) {
            if ($this->page > 0 && !$firstP) {
                $ret[] = array('num' => 'first', 'current' => false, 'page' => 1);
                $firstP = true;
            }

            if ($i > $this->page -3 && $i < $this->page +5) {
                $ret[] = array(
                    'num' => $i,
                    'current' => (($i == $this->page+1) ? true : false)
                );
            }

            if ($this->page < $pages-1 && $i == $pages && !$lastP) {
                $ret[] = array('num' => 'last', 'current' => false, 'page' => $pages);
                $lastP = true;
            }
        }

        return $ret;
    }

    /**
     * fetch()
     * Haalt een bepaalde rij op v/e bepaalde query, en retourneerd deze
     *
     * @since 1.0.0
     *
     * @param bool $asArray        | Retourneer als array ja/nee
     * @param int $rown            | Rijnummer v/h resultaat
     * @acces public
     * @return String $result
     **/
    public function fetch($asArray = false, $xss = true, $rown = 0) {
        if (!$asArray) {
            if ($xss) {
                $result = @htmlspecialchars(@mysql_result($this->pQuery, $rown));
            }
            else {
                $result = @mysql_result($this->pQuery, $rown);
            }
        }
        else {
            if (!$xss) {
                $result = @mysql_fetch_array($this->pQuery);
            }
            else {
                $ret = @mysql_fetch_array($this->pQuery);
                $result = array();

                foreach($ret as $i => $v) {
                    $result[$i] = @htmlspecialchars($v);
                }
            }
        }

        return $result;
    }

    /**
     * fetchAll()
     * Retourneerd een associatieve array met gevraagde resultaten, indien deze als array word opgevraagd.
     * Anders wordt er door iedere row geloopt, indien gevraagd, tegen xss beveiligd. En geretourneerd
     *
     * @since 1.0.0
     *
     * @param bool $array            | retourneer als array
     * @param bool $multi            | retourneer als multi-dimensionale array, ook als er maar één rij uit de opgegeven query komt
     * @param bool $xss                | filter op xss ja/nee
     * @acces public
     * @return Array
     **/
    public function fetchAll($array = false, $multi = false, $xss = true) {

        // Word alles liever als array opgevraagd
        if ($array) {
            $ret = array();

            if ($multi || $this->getRowCount() > 1) {
                while($data = @mysql_fetch_array($this->pQuery)) {
                    $ret[] = $data;
                }

                return $ret;
            }
            else {
                return @mysql_fetch_array($this->pQuery);;
            }
        }
        else { // Door iedere row loopen en deze retourneren
            while($this->tempRow = @mysql_fetch_array($this->pQuery)) {
                $data = array();

                // Als beveiligen tegen XSS aan staat, een htmlspecialchars over alle waarden gebruiken.
                if ($xss) {
                    foreach($this->tempRow as $field => $value) {
                        // Alleen als het veld geen numerieke waarde is, deze toevoegen aan de return array
                        if (!is_numeric($field)) {
                            $data[$field] = htmlspecialchars($value);
                        }
                    }

                    return $data;
                }
                else {
                    return $this->tempRow;
                }
            }
        }
    }


    /**
     * getRowCount()
     * Retourneerd het aantal rijen dat een bepaalde query heeft opgeleverd
     *
     * @since 1.0.0
     *
     * @acces public
     * @return int [rows]
     **/
    public function getRowCount() {
        $rows = @mysql_num_rows($this->query);

        return $rows;
    }

    /**
     * getFields()
     * Maakt aan de hand van een array met waarden, een stukje SQL voor de select-query
     * en retourneerd deze.
     *
     * @since 1.0.0
     *
     * @param Array $fields                | Array met waarden
     * @acces private
     * @return String [sql]
     **/
    private function getFields($fields) {
        $fieldsCount = count($fields);
        $ret = "";

        for($i=0; $i < $fieldsCount; $i++) {
            $ret .= ($i == $fieldsCount-1) ? $fields[$i] : $fields[$i].', ';
        }

        return $ret;
    }

    public function getLimit() {
        $start = $this->page*$this->resultsPerPage;
        $end = $this->resultsPerPage;

        return "LIMIT $start, $end";
    }
}

?>