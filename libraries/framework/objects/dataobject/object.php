<?php
/**
 * Objeto de Datos
 * @name DataObject
 * @version 1.2
 */
class DataObject {
	private $_structure = array(
		'properties' => null,
		'table' => null,
		'key' => null,
		'new' => true
	);
	
	const ignore_key = 0x1;
	const ignore_nulls = 0x2;
	const ignore_objects = 0x4;
	
	public function define() {
		$args = func_num_args();
		if ( $args >= 1 ) { $this->_structure['table'] = func_get_arg(0); }
		if ( $args >= 2 ) { $this->_structure['key'] = func_get_arg(1); }
	}
	
	private function _use($properties) {
		if (! is_array($properties) ) { $properties = array($properties); }
		foreach (get_object_vars($this) as $p => $v) {
			if (! in_array($p, $properties) && $p != $this->_structure['key']) { unset($this->$p); }
		}
	}
	
	public function properties($subset = 0) {
		if (is_null($this->_structure['properties'])) {
			$this->_structure['properties'] = array_diff(array_keys(get_object_vars($this)),
				array("_structure"));
		}
		if ($subset == 0) { return $this->_structure['properties']; }
		$properties = array();
		foreach ( $this->_structure['properties'] as $property ) {
			if ($subset & self::ignore_key && $property == $this->_structure['key']) continue;
			if ($subset & self::ignore_nulls && is_object($this->$property)) {
				if ($this->$property instanceof DataObject && ! $this->$property instanceof DataObjectCollection) {
					$key = $this->{$property}->key();
					if (is_null($this->{$property}->$key)) continue;
				}
			}
			if ($subset & self::ignore_nulls && is_null($this->$property)) continue;
			if ($subset & self::ignore_objects && is_object($this->$property)) continue;
			$properties[] = $property;
		}
		return $properties;
	}
	
	public function values($subset = null) {
		$values = array();
		$properties = is_numeric($subset) ? $this->properties($subset) : $this->properties();
		foreach ($properties as $property) {
			if (is_array($subset)) {
				if (! in_array($property, $subset)) { continue; }
			}
			if (is_scalar($this->$property) || is_null($this->$property)) {
				$values[$property] = $this->$property;
			} elseif (is_object($this->$property)) {
				/*	verificar si pripiedad es un DataObject pero no un DataObjectCollection
			 	*	para que pueda asignar el valor de su propiedad clave */
				if ($this->$property instanceof DataObject && ! $this->$property instanceof DataObjectCollection) {
					$key = $this->{$property}->key();
					$values[$property] = $this->{$property}->$key;
				}
			 }
		}
		return $values;
	}
	
	public function key() {
		return $this->_structure['key'];
	}
	
	public function load() {
		if ( is_null($this->_structure['table']) || is_null($this->_structure['key']) ) { throw new Exception(); }
		if (! property_exists($this, $this->_structure['key']) ) { throw new Exception(); }
		$st = new Statement();
		$ds = $st->select($this->_structure['table'])
		->where(sql::eq($this->_structure['key'], $this->{$this->_structure['key']}))
		->execute();
		$this->bind($ds);
	}
	
	/** @todo aun si es objeto lo trata de incluir en query */
	public function store() {
		if ( is_null($this->_structure['table']) || is_null($this->_structure['key']) ) { throw new Exception(); }
		if (! property_exists($this, $this->_structure['key']) ) { throw new Exception(); }
		$st = new Statement();
		if ($this->_structure['new']) {
			if (is_null($this->{$this->_structure['key']})) { unset($values[$this->_structure['key']]); }
			$st->insert($this->_structure['table'])
			->values($this->values(self::ignore_key))
			->execute();
			if ($st->lastID) { $this->{$this->_structure['key']} = $st->lastID; }
		} else {
			unset($values[$this->_structure['key']]);
			$st->update($this->_structure['table'])
			->values($this->values(self::ignore_key))
			->where(sql::eq($this->_structure['key'], $this->{$this->_structure['key']}))
			->execute();
		}
		return true;
	}
	
	/** @todo accion en BD */
	public function remove() {
		if ( is_null($this->_structure['table']) || is_null($this->_structure['key']) ) { throw new Exception(); }
		if (! property_exists($this, $this->_structure['key']) ) { throw new Exception(); }
		$st = new Statement();
		$st->delete($this->_structure['table'])
		->where(sql::eq($this->_structure['key'], $this->{$this->_structure['key']}))
		->execute();
		return true;
	}
	
	public function bind($from, $apply_use = false) {
		if (! (is_object($from) || is_array($from))) { throw new Exception(); }
		if ( $from instanceof Dataset ) {
			$this->_structure['new'] = false;
			$vars = $from->rows[0];
		} elseif ( is_object($from) ) {
			$vars = get_object_vars($from);
		} else {
			$vars = $from;
		}
		
		// si $apply_use es verdadero, quitar propiedades del objeto que no se encuentran en $from
		if ($apply_use) { $this->_use(array_keys($vars)); }
		
		foreach ($vars as $k => $v) {
			if (property_exists($this, $k)) {
				// si está declarado como DataObject pasar el valor a la clave del objeto
				if ($this->$k instanceof DataObject && ! $this->$k instanceof DataObjectCollection) {
					$key = $this->$k->key();
					$this->$k->$key = (strlen($v) > 0) ? $v : null;
				} else {
					$this->$k = (strlen($v) > 0) ? $v : null;
					//si $k es la propiedad clave, el objeto ya existe
					if ($k == $this->_structure['key'] && $this->_structure['new']) { $this->_structure['new'] = false; }
				}
			}
		}
	}
	
	public function __call($fn, $params) {
		if ($fn == 'use') { $this->_use($params[0]); return; }
		if (! property_exists($this, $fn)) { throw new Exception(); }
		if (! isset($params[0])) { throw new Exception(); }
		$callback = array_shift($params);
		if (! is_string($callback)) { throw new Exception(); }
		if ( preg_match_all('/^(?<class>.+):(?<fn>.+)$/', $callback, $matches) != 1 ) { throw new Exception(); }
		$ClassName = $matches['class'][0];
		$MethodName = $matches['fn'][0];
		$ValidClasses = array("String", "Number", "Date", "Binary");
		array_unshift($params, $this->$fn);
		if (! in_array($ClassName, $ValidClasses) ) { throw new Exception(); }
		if (! method_exists($ClassName, $MethodName) ) { throw new Exception(); }
		/* agregar control para debug */
		return call_user_func_array(array($ClassName, $MethodName), $params);
	}
	
	public function __toString() {
		return $this->{$this->_structure['key']};
	}
	
	public function __toArray() {
		return (array) $this;
	}
}
?>