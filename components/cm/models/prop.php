<?php
class Model_cm_prop extends Model {
	private $entidad;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new MongoClient('localhost');
		$this->db = $m->beneficencia;
		$this->entidad = $this->db->mg_entidades;
	}
	protected function save_update(){
		global $f;
		$ocupante = $this->params['data']['ocupante'];
		$ocupante['_id'] = new MongoId($ocupante['_id']);
		$set = array("roles.propietario.ocupantes"=>$ocupante);
		$upd = array( '$push'=>$set );
		$this->entidad->update(array( '_id' => new MongoId($this->params['data']['_id']) ), $upd );
	}
	protected function save_upd_espacio(){
		global $f;
		$esp = $this->params['espacio'];
		$esp['_id'] = new MongoId($esp['_id']);
		$upd = array( '$push'=>array("roles.propietario.espacios"=>$esp) );
		$this->entidad->update(array( '_id' => new MongoId($this->params['_id']) ), $upd );
	}
	protected function save_new_espa(){
		global $f;
		$espacio = $this->params['espacio'];
		$this->entidad->update(
			array('_id'=>$this->params['_id']) ,
			array( '$push' => array( "roles.propietario.espacios" => $espacio ))
		);
	}
	protected function delete_espacio(){
		global $f;
		$this->entidad->update( array('_id'=>new MongoId($this->params['_id'])) , array( '$pull' => array( 'roles.propietario.espacios' => array('_id'=>new MongoId($this->params['espacio'])) )  ) );
	}
	protected function delete_ocupante(){
		global $f;
		$this->entidad->update( array('_id'=>new MongoId($this->params['_id'])) , array( '$pull' => array( 'roles.propietario.ocupantes' => array('_id'=>new MongoId($this->params['ocupante'])) )  ) );
	}
	protected function get_all_ocu_pro(){
		global $f;
		$criterio = array(
			'_id'=>new MongoId($this->params['_id'])
		);
		$result = $this->entidad->find($criterio,array('roles.propietario.ocupantes'=>true));
		foreach ($result as $obj) {
			if(isset($obj['roles']['propietario']['ocupantes'])){
				foreach ($obj['roles']['propietario']['ocupantes'] as $ocupante) {
					$temp = $this->entidad->findOne(array('_id'=>$ocupante['_id']),
						array('_id'=>true, 'nomb'=>true, 'appat'=>true,'apmat'=>true,'roles.ocupante'=>true));
					if(!$temp['roles']['ocupante']['difunto']){
						$this->items[] = $temp;
						
					}
				}
			}
		}
	}
	protected function get_all_difu(){
		global $f;
		$criterio = array(
			'roles.ocupante.propietario._id'=>new MongoId($this->params['_id']),
			//'roles.ocupante.difunto'=>true
		);
		$result = $this->entidad->find($criterio,array('_id'=>true, 'nomb'=>true, 'appat'=>true,'apmat'=>true,'roles.ocupante'=>true));
		foreach ($result as $obj) {
			$this->items[] = $obj;
		}
	}
}
?>