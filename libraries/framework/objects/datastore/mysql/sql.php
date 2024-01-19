<?php
/**
 * Motor MySQL: Clase con funciones de apoyo para manejar sentencias SQL
 * @name sql
 * @version 1.2
 */
abstract class sql implements iSQL {
	
	static public function parse($value) {
		$Application = Application::getInstance();
		switch (true) {
			case (is_array($value)):
				foreach ($value as $k=>$v) {
					$value[$k] = self::parse($v);
				}
				return $value;
				break;
			case (is_null($value)):
				return "null";
				break;
			case (is_numeric($value) && ( strval($value) === strval(floatval($value)) || strval($value) === strval(intval($value)) )):
				return $value;
				break;
			default:
				// Soporte Blob:
				// String::latin1 devuelve cadena vacía cuando el string es binario
				if (String::latin1($value) != '') {
					return $Application->datastore->quote(String::latin1($value));
				} else {
					return $Application->datastore->quote($value);
				}
		}
	}

	static public function sel($table_name, $fields = array(), $join = array(), $where = array(), $group = array(), $sort = array(), $limit = array()) {
		$sql = "select " . (count($fields) == 0 ? " * " : implode(", ", $fields)) . "\nfrom " . $table_name;
		if (is_array($join)) { $sql .= "\n" . implode("\n", $join); }
		if (is_array($where)) { $sql .= "\nwhere " . self::cond($where, false); }
		if (is_array($group)) { $sql .= "\ngroup by " . implode(", ", $group); }
		if (is_array($sort)) { $sql .= "\norder by " . implode(", ", $sort); }
		if (is_array($limit)) {
			$limit[1] = isset($limit[1]) ? $limit[1] : 0;
			$sql .= "\nlimit " . $limit[0] . ($limit[1] != 0 ? ", " . $limit[1] : "");
		}
		return $sql;
	}
	
	static public function ins($table_name, $values = array()) {
		$fields = array_keys($values);
		$sql = "insert into " . $table_name . " (" . implode(", ", $fields) . ") values\n";
		if (!is_array($values[$fields[0]])) {
			$sql .= "(" . implode(", ", self::parse($values)) . ")";
		} else {
			$row_count = count($values[$fields[0]]);
			for ($r=0; $r < $row_count; $r++) {
				$row = array();
				foreach ($fields as $field) {
					$row[] = $values[$field][$r];
				}
				$rows[] = "(" . implode(", ", self::parse($row)) . ")";
			}
			$sql .= implode(",\n", $rows);
		}
		return $sql;
	}
	
	static public function upd($table_name, $values = array(), $where = array()) {
		$fields = array_keys($values);
		$sql = "update " . $table_name . " set ";
		array_walk($values, create_function('&$i,$k','$i= "$k = " . sql::parse($i);'));
		$sql .= implode(", ", $values);
		if (is_array($where)) { $sql .= "\nwhere " . self::cond($where, false); }
		return $sql;
	}
	
	static public function del($table_name, $where = array()) {
		$sql = "delete from " . $table_name;
		if (is_array($where)) { $sql .= "\nwhere " . self::cond($where, false); }
		return $sql;
	}
	
	/** @todo llamada a procedimiento */
	static public function sp() {}
	
	static public function cond($conditions, $enclose = true) {
		if (array_key_exists('oper', $conditions)) {
			$oper = " " . $conditions['oper'] . " ";
			unset($conditions['oper']);
		} else {
			$oper = " and ";
		}
		foreach ($conditions as $k => $condition) {
			if (is_array($condition)) {
				$conditions[$k] = self::cond($condition);
			}
		}
		if ($enclose) {
			return "\n(" . implode($oper, $conditions) . ")";
		} else {
			return implode($oper, $conditions);
		}
	}
	
	static public function fn($function_name, $params) {
		if (is_array($params)) {
			$params = implode(",", self::parse($params));
		}
		return $function_name."(".$params.")";
	}
	
	static public function eq($field, $value) {
		if (! is_null($value)) {
			return $field." = ".self::parse($value);
		} else {
			return $field." is null";
		}
	}
	
	static public function neq($field, $value) {
		if (! is_null($value)) {
			return $field." != ".self::parse($value);
		} else {
			return "not ".$field." is null";
		}
	}
	
	static public function in($field, $values) {
		return $field." in (".implode(",", self::parse($values)).")";
	}
	
	static public function nin($field, $values) {
		return $field." not in (".implode(",", self::parse($values)).")";
	}
	
	static public function like($field, $value) {
		return $field." like ".self::parse("%".$value."%");
	}
	
	static public function llike($field, $value) {
		return $field." like ".self::parse($value."%");
	}
	
	static public function rlike($field, $value) {
		return $field." like ".self::parse("%".$value);
	}
	
	static public function btw($field, $lower, $higher) {
		return $field." between ".self::parse($lower)." and ".self::parse($higher);
	}
	
	static public function lt($field, $value) {
		return $field." < ".self::parse($value);
	}
	
	static public function elt($field, $value) {
		return $field." <= ".self::parse($value);
	}
	
	static public function gt($field, $value) {
		return $field." > ".self::parse($value);
	}
	
	static public function egt($field, $value) {
		return $field." >= ".self::parse($value);
	}
	
	static public function sub($field, $subquery) {
		return $field." in (".$subquery.")";
	}

	static public function nsub($field, $subquery) {
		return $field." not in (".$subquery.")";
	}
	
	static public function src($fields, $value) {
		return "match (".implode(",", $fields).") against (".self::parse($value).")";
	}
	
	static public function regexp($field, $regexp) {
		return $field." regexp ".$regexp;
	}
	
	static public function bwand($field, $value) {
		return $field." & ".self::parse($value);
	}
	
	static public function bwor($field, $value) {
		return $field." | ".self::parse($value);
	}
	
	static public function bwxor($field, $value) {
		return $field." ^ ".self::parse($value);
	}
	
	static public function bwnot($field, $value) {
		return $field." & ~".self::parse($value);
	}
	
	public function whand() {
		$params = func_get_args();
		return "(" . implode(" and ", $params) . ")";
	}
		
	public function whor() {
		$params = func_get_args();
		return "(" . implode(" or ", $params) . ")";
	}
}
?>