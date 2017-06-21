<?php

class Rowset {
	
	private $rows = array();
	
	public function __construct(Query $qry) {
		$rows = $qry->fetchAll(true, true);
		
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$this->rows[] = new Row($row);
			}
		}
	}
	
	public function __toString() {
		return $this->rows;
	}
	
}