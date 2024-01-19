<?php
/**
 * Clase base para manejo de bases de datos
 * @name Datastore
 * @version 1.2
 */
abstract class Datastore {
	const count_rows = 0x2;
	const exec_procedure = 0x4;
	
	const transaction_commit = 0x1;
	const transaction_rollback = 0x2;
	
	protected $config = null;
	protected $connectionString = null;
	
	/**
	 * @var PDO
	 */
	protected $cn = null;
	
	public $flags = 0;

	public $results;
	public $lastID;
	public $totalRows;
	public $affectedRows;
	
	public function connect() {}
	public function close() {}
	public function clear() {
		$this->results = array();
		$this->lastID = null;
		$this->totalRows = null;
		$this->affectedRows = null;
	}
	public function quote($string) {}
	/**
	 * @param $sql string
	 * @param $flags mixed
	 * @return Dataset
	 */
	public function query($sql, $flags) {}
	/**
	 * @param $sql string
	 * @param $flags mixed
	 * @return array
	 */
	public function execute($sql, $flags) {}
}
?>