<?php
/**
 * Seentencia de base de datos
 * @name Statement
 * @version 1.2
 */
final class Statement {
	const sort_asc = 'asc';
	const sort_desc = 'desc';
	
	const limit_skip = 'skip';
	const limit_page = 'page';
	
	const join_left = 'left';
	const join_inner = 'inner';
	const join_right = 'right';
	
	const type_select = 'select';
	const type_insert = 'insert';
	const type_update = 'update';
	const type_delete = 'delete';
	const type_callsp = 'callsp';
	
	const where_and = 'and';
	const where_or = 'or';
	const where_xor = 'xor';
	
	private $type = null;
	private $structure = array();
	
	public $affectedRows;
	public $lastID;
	public $lastError;
	
	public function select($table_name, $fields = null) {
		$this->structure['table'] = $table_name;
		if (is_string($fields)) {
			$this->structure['fields'][] = $fields;
		} elseif (is_array($fields)) {
			$this->structure['fields'] = $fields;
		}
		$this->type = self::type_select;
		return $this;
	}
	
	public function insert($table_name) {
		$this->structure['table'] = $table_name;
		$this->type = self::type_insert;
		return $this;
	}
	
	public function update($table_name) {
		$this->structure['table'] = $table_name;
		$this->type = self::type_update;
		return $this;
	}
	
	public function delete($table_name) {
		$this->structure['table'] = $table_name;
		$this->type = self::type_delete;
		return $this;
	}
	
	public function call($proc_name) {
		$this->structure['call'] = $proc_name;
		$this->type = self::type_callsp;
		return $this;
	}
	
	public function params($values) {
		if (is_string($values)) {
			$this->structure['params'][] = $values;
		} elseif (is_array($values)) {
			array_merge($this->structure['params'], $values);
		}
		return $this;
	}
	
	public function join($table_name, $from_key, $join_key, $type = self::join_left) {
		if ($this->type != self::type_select) { throw new Exception(); }
		$this->structure['join'][] = "$type join $table_name on $from_key = $join_key";
		return $this;
	}
	
	/** @todo where anidable con opcion de operacion */
	public function where($conditions, $oper = self::where_and) {
		if (is_string($conditions)) {
			$this->structure['where'][] = $conditions;
			$this->structure['where']['oper'] = $oper;
		} elseif (is_array($conditions)) {
			$conditions['oper'] = $oper;
			$this->structure['where'][] = $conditions;
			
			/* opcion de anidación, pero no se pueden aplicar ciertas condiciones
			foreach ($conditions as $condition) {
				if ($condition instanceof Statement) {
					$w = $this->structure['where'];
					$this->structure['where'] = array($w);
				} elseif (is_string($condition)) {
					$this->structure['where'][] = $condition;
				}
			}*/
		}
		return $this;
	}
	
	public function values($values = array()) {
		foreach ($values as $field => $value) {
			$this->structure['values'][$field] = $value;
		}
		return $this;
	}
	
	public function sort($field, $order = self::sort_asc) {
		if ($this->type != self::type_select) { throw new Exception(); }
		$this->structure['sort'][] = $field . ' ' . $order;
		return $this;
	}
	
	public function limit($count, $offset = null, $type = self::limit_skip) {
		if($this->type != self::type_select) { throw new Exception(); }
		$this->structure['limit'][1] = $count;
		if (! is_null($offset) ) {
			switch ($type) {
				case self::limit_skip: $this->structure['limit'][0] = $offset; break;
				case self::limit_page: $this->structure['limit'][0] = $count * ($offset - 1); break;
			}
		}
		return $this;
	}
	
	public function group($field) {
		if($this->type != self::type_select) { throw new Exception(); }
		$this->structure['group'][] = $field;
		return $this;
	}
	
	/**
	 * ejecuta una sentencia
	 * @return Dataset
	 * @throws Exception
	 */
	public function execute() {
		if(is_null($this->type)) { throw new Exception(); }
		$Application = Application::getInstance();
		
		switch ($this->type) {
			case self::type_select:
				$sql = sql::sel(
					$this->structure['table'],
					isset($this->structure['fields']) ? $this->structure['fields'] : null,
					isset($this->structure['join']) ? $this->structure['join'] : null,
					isset($this->structure['where']) ? $this->structure['where'] : null,
					isset($this->structure['group']) ? $this->structure['group'] : null,
					isset($this->structure['sort']) ? $this->structure['sort'] : null,
					isset($this->structure['limit']) ? $this->structure['limit'] : null
				);
				$flags = isset($this->structure['limit']) ? Datastore::count_rows : 0;
				return $Application->datastore->query($sql, $flags);
				break;
			case self::type_insert:
				$sql = sql::ins(
					$this->structure['table'],
					isset($this->structure['values']) ? $this->structure['values'] : array()
				);
				$call = $Application->datastore->execute($sql);
				if (! $call['success']) { $this->lastError = $call['lastError']; return false; }
				$this->affectedRows =  $call['affectedRows'];
				$this->lastID =  $call['lastID'];
				return true;
				break;
			case self::type_update:
				$sql = sql::upd(
					$this->structure['table'],
					isset($this->structure['values']) ? $this->structure['values'] : array(),
					isset($this->structure['where']) ? $this->structure['where'] : null
				);
				$call = $Application->datastore->execute($sql);
				if (! $call['success']) { $this->lastError = $call['lastError']; return false; }
				$this->affectedRows =  $call['affectedRows'];
				$this->lastID =  $call['lastID'];
				return true;
				break;
			case self::type_delete:
				$sql = sql::del(
					$this->structure['table'],
					isset($this->structure['where']) ? $this->structure['where'] : null
				);
				$call = $Application->datastore->execute($sql);
				if (! $call['success']) { $this->lastError = $call['lastError']; return false; }
				$this->affectedRows =  $call['affectedRows'];
				$this->lastID =  $call['lastID'];
				return true;
				break;
			case self::callsp:
				$sql = sql::sp(
					$this->structure['call'],
					isset($this->structure['params']) ? $this->structure['params'] : null
				);
				break;
		}
	}
}
?>