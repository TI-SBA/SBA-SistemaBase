<?php
/**
 * Manejo de bases de datos MongoBD
 * @name Datastore_mongo
 * @version 0.1
 */
class Datastore_mongo extends Datastore {
	protected $db;
	
	public function __construct( $config ) {
		
		$this->config = $config;
		try {
			$this->connectionString = sprintf("mongodb://%s%s:%s",
			isset($this->config['user']) ? $this->config['user'] . ":" . $this->config['password'] . "@": "",
			isset($this->config['server']) ? $this->config['server'] : "localhost",
			isset($this->config['port']) ? $this->config['port'] : 27017);
			
			$this->cn = new Mongo($this->connectionString, array('connect' => false));
		} catch (Exception $e) {
			throw Application::Exception('Dst003');
		}
	}
	
	public function connect() {
		if (tracing()) Trace::info("Estableciendo conexion a la Base de datos: " . $this->connectionString & ", " & $this->config['database'], 'Datastore');
		try {
			$this->cn->connect();
			$this->db = $this->cn->selectDB($this->config['database']);
		} catch (PDOException $e) {
   			throw Application::Exception('Dst004', array($e->getMessage()));
		}
	}
	
	public function close() {
		if (is_a($this->cn, 'Mongo')) { $this->cn->close(); }
		unset( $this->cn );
	}
	
	public function __get($collection) {
		if (count($collection) == 1 && is_a($this->cn, 'Mongo')) {
			return $this->db->$collection;
		}
	}
	
	public function __call($fn, $params) {
		if (is_a($this->cn, 'Mongo') && method_exists($this->db, $fn)) {
			return call_user_func_array(array($this->db, $fn), $params);
		} else {
			throw Application::Exception('Dst005', array($fn));
		}
	}
}
?>