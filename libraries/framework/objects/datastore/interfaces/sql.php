<?php
/**
 * Interface para definir funciones de apoyo para manejar sentencias SQL
 * @name sql
 * @version 1.2
 */
interface iSQL {
	//public function escape($value);
	//public function quote($value);
	//public function objectQuote($object_name);
	
	static public function parse($value);
	
	static public function sel($table_name, $fields = array(), $join = array(), $where = array(), $group = array(), $sort = array(), $limit = array());
	static public function ins($table_name, $values = array());
	static public function upd($table_name, $values = array(), $where = null);
	static public function del($table_name, $where = null);
	static public function sp();
	
	static public function cond($conditions, $enclose = true);
	static public function fn($function_name, $params);
	static public function eq($field, $value);
	static public function neq($field, $value);
	static public function in($field, $values);
	static public function nin($field, $values);
	static public function like($field, $value);
	static public function llike($field, $value);
	static public function rlike($field, $value);
	static public function btw($field, $lower, $higher);
	static public function lt($field, $value);
	static public function let($field, $value);
	static public function gt($field, $value);
	static public function get($field, $value);
	static public function sub($field, $subquery);
	static public function nsub($field, $subquery);
	static public function src($fields, $value);
	static public function regexp($field, $regexp);
	static public function bwand($field, $value);
	static public function bwor($field, $value);
	static public function bwxor($field, $value);
	static public function bwnot($field, $value);
}
?>