<?php
/**
 * Registros de base de datos
 * @name Dataset
 * @version 1.2
 */
class Dataset {
	private $columns = null;
	private $rows = null;
	
	private $rowCount;
	private $foundCount;
	
	private $cursor;
	
	public function __construct(PDOStatement $rs) {
		if (! $rs instanceof PDOStatement ) { throw new Exception(); }
		$this->rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		$this->rowCount = count($this->rows);
		for ($c = 0; $c < $rs->columnCount(); $c++) {
	    	$col_info = $rs->getColumnMeta($c);
	    	$this->columns[] = $col_info['name'];
		}
		if ($this->rowCount > 0) { $this->cursor = 0; }
		$rs->closeCursor();
	}
	
	private function _each($field) {
		$values = array();
		foreach ($this->rows as $row) {
			$values[] = $row[$field];
		}
		return $values;
	}
	
	private function _subset($prefix) {
		$subset = new stdClass();
		foreach ($this->columns as $column) {
			$count = 0;
			$c = str_replace($prefix, "", $column, $count);
			if ($count > 0) {
				$subset->$c = $this->rows[$this->cursor][$column];
			}
		}
		return $subset;
	}
	
	public function __get($property) {
		if ( is_null($this->rows) ) { throw new Exception(); }
		switch ($property) {
			case 'rowCount':
				return $this->rowCount;
				break;
			case 'foundCount':
				return $this->foundCount;
				break;
			case 'rows':
				return $this->rows;
				break;
			case 'columns':
				return $this->columns;
				break;
			default:
				if (! in_array($property, $this->columns)) { throw new Exception(); }
				return $this->rows[$this->cursor][$property];
		}
	}
	
	public function __set($property, $value) {
		switch ($property) {
			case 'foundCount':
				$this->foundCount = $value;
				break;
		}
	}
	
	public function __call($function, $params) {
		if ( is_null($this->rows) ) { throw new Exception(); }
		switch ($function) {
			case 'first':
				$this->cursor = 0;
				return true;
				break;
			case 'next':
				$this->cursor++;
				return $this->cursor < $this->rowCount;
				break;
			case 'prev':
				$this->cursor--;
				return $this->cursor >= 0;
				break;
			case 'last':
				$this->cursor = $this->rowCount - 1;
				return true;
				break;
			case 'each':
				if (! in_array($params[0], $this->columns)) { throw new Exception(); }
				return $this->_each($params[0]);
				break;
			case 'subset':
				return $this->_subset($params[0]);
				break;
			case 'row':
				if (!($params[0] >= 0 && $params[0] < $this->rowCount)) { throw new Exception(); }
				return $this->rows[$params[0]];
				break;
		}
	}
}
?>