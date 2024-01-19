<?php
class Model_in_arre extends Model {
	private $enti;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->enti = $this->db->mg_entidades;
	}
	protected function save_new_espa(){
		global $f;
		$this->enti->update(
			array('_id'=>$this->params['entidad']),
			array('$push'=>array(
				'roles.arrendatario.espacios'=>$this->params['espacio']
			))
		);
	}
	protected function delete_espacio(){
		global $f;
		$this->enti->update(
			array('_id'=>$this->params['_id']),
			array(
				'$pull' => array( 'roles.arrendatario.espacios' => array('_id'=>$this->params['espacio']) )
			)
		);
	}
}
?>