<?php
/**
 * Colección de Objetos de Datos
 * @name DataObjectCollection
 * @version 1.2
 */
class DataObjectCollection extends DataObject {
	private $_structure = array(
		'dataobject' => null,
		'key' => null
	);
	private $_items = array();
	private $_found = null;
	private $_cursor = -1;
	
	public function __construct() {
		$args = func_get_args();
		$dataobject = isset($args[0]) ? $args[0] : null;
		$key = isset($args[1]) ? $args[1] : null;
		$this->define($dataobject, $key);
	}
	
	public function define($dataobject = null, $key = null) {
		if ( $dataobject ) {
			if (! class_exists($dataobject, false) ) { throw new Exception(); }
			$this->_structure['dataobject'] = $dataobject;
		}
		if ( $key ) { $this->_structure['key'] = $key; }
	}
	
	public function add() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		$new_member = new $this->_structure['dataobject'];
		$this->_items[] = $new_member;
		return $new_member;
	}
	
	public function append($new_element) {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		if (! $new_element instanceof $this->_structure['dataobject'] ) { throw new Exception(); }
		$this->_items[] = $new_element;
	}
	
	public function load($ids = array(), $id_field, $load_func = "load") {
		if (! is_array($ids)) { throw new Exception(); }
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		if (! property_exists($this->_structure['dataobject'], $id_field)) { throw new Exception(); }
		if (! method_exists($this->_structure['dataobject'], $load_func)) { throw new Exception(); }
		$this->_found = null;
		foreach ($ids as $id) {
			$new_member = new $this->_structure['dataobject'];
			$new_member->$id_field = $id;
			$new_member->$load_func();
			$this->_items[] = $new_member;
		}
	}
	
	final public function bind(Dataset $from, $apply_use = false) {
		if (! (is_object($from) || is_array($from))) { throw new Exception(); }
		if (! $from instanceof Dataset ) { throw new Exception(); }
		$this->_found = null;
		
		foreach ($from->rows as $row) {
			$new_member = new $this->_structure['dataobject'];
			foreach ($row as $key => $cell) {
				if (property_exists($new_member, $key)) {
					$new_member->$key = (strlen($cell) > 0) ? $cell : null;
				}
			}
			// si $apply_use es verdadero, quitar propiedades del objeto que no se encuentran en $from
			if ($apply_use) { $new_member->use($from->colums); }
			$this->_items[] = $new_member;
		}
		
		$this->_found = $from->foundCount;
	}
	
	public function count() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		return count($this->_items);
	}
	
	public function found() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		return (is_null($this->_found)) ? count($this->_items) : $this->_found;
	}
	
	public function clear() {
		$this->_items = array();
		$this->_found = null;
		$this->_cursor = -1;
	}
	
	public function item($index) {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		if (! isset($this->_items[$index])) { throw new Exception(); }
		return $this->_items[$index];
	}
	
	public function items() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		return $this->_items;
	}
	
	public function first() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		$this->_cursor = 0;
		return true;
	}
	
	public function next() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		$this->_cursor++;
		return $this->_cursor < count($this->_items);
	}
	
	public function prev() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		$this->_cursor--;
		return $this->_cursor >= 0;
	}
	
	public function last() {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		$this->_cursor = count($this->_items) - 1;
		return true;
	}
	
	public function each($property) {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		if (! property_exists($this->_structure['dataobject'], $property)) { throw new Exception(); }
		$values = array();
		foreach ($this->_items as $item) {
			$values[] = $item->$property;
		}
		return $values;
	}
	
	public function __get($property) {
		if (! isset($this->_structure['dataobject']) ) { throw new Exception(); }
		if ($property == 'className') { return $this->_structure['dataobject']; }
		if (! property_exists($this->_structure['dataobject'], $property) ) { throw new Exception(); }
		if (! ($this->_cursor >= 0 && $this->_cursor < count($this->_items))) { throw new Exception(); }
		return $this->_items[$this->_cursor]->$property;
	}
	
	public function __set($property, $value) {
		if (! property_exists($this->_structure['dataobject'], $property) ) { throw new Exception(); }
		if (! ($this->_cursor >= 0 && $this->_cursor < count($this->_items))) { throw new Exception(); }
		$this->_items[$this->_cursor]->$property = $value;
	}
	
	public function __call($property, $params) {
		if (! property_exists($this->_structure['dataobject'], $property) ) { throw new Exception(); }
		if (! ($this->_cursor >= 0 && $this->_cursor < count($this->_items))) { throw new Exception(); }
		$this->_items[$this->_cursor]->__call($property, $params);
	}
}
?>