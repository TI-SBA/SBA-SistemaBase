<?php
class Model_cm_ocup extends Model {
	private $entidad;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new MongoClient('localhost');
		$this->db = $m->beneficencia;
		$this->entidad = $this->db->mg_entidades;
	}
	protected function save_ocup(){
		global $f;
		$array = $this->params['data'];
		if(isset($array['nomb'])){
			if(isset($array['appat'])){
				$array['fullname'] = trim($array['nomb']).' '.trim($array['appat']).' '.trim($array['apmat']);
			}else{
				$array['fullname'] = trim($array['nomb']);
			}
		}
		$this->entidad->insert( $array );
		$this->obj = $array;
	}
	protected function save_update(){
		global $f;
		$array = $this->params['data'];
		$tmp = $this->entidad->findOne(array('_id'=>new MongoId($array['_id'])));
		if(isset($tmp)){
			if(sizeof($tmp['roles'])==0){
				$this->entidad->update(array( '_id' => new MongoId($array['_id']) ), array('$set'=>array(
					'roles'=>array('ocupante'=>true)
				)) );
			}
		}
		$ocupante = array(
			"difunto"=>false,
			"espacio"=>$array['espacio']
		);
		if(isset($array['propietario'])){
			$ocupante["propietario"] = $array['propietario'];
			$ocupante['propietario']['_id'] = new MongoId($ocupante['propietario']['_id']);
		}
		$ocupante['espacio']['_id'] = new MongoId($ocupante['espacio']['_id']);
		$set = array("roles.ocupante"=>$ocupante);
		$upd = array( '$set'=>$set );
		$this->entidad->update(array( '_id' => new MongoId($array['_id']) ), $upd );
	}
	protected function save_upd_espacio(){
		global $f;
		$array = $this->params['data'];
		$espacio = $array['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$set = array("roles.ocupante.espacio"=>$espacio);
		$upd = array( '$set'=>$set );
		$this->entidad->update(array( '_id' => new MongoId($array['_id']) ), $upd );
	}
	protected function save_upd_prop(){
		global $f;
		$prop = $this->params['propietario'];
		$prop['_id'] = new MongoId($prop['_id']);
		$set = array("roles.ocupante.propietario"=>$prop);
		$upd = array( '$set'=>$set );
		$this->entidad->update(array( '_id' => new MongoId($this->params['_id']) ), $upd );
	}
	protected function save_estado_difunto(){
		global $f;
		if(!isset($this->params['fecnac']))
			$this->params['fecnac'] = '';
		if(!isset($this->params['fecdef']))
			$this->params['fecdef'] = '';
		$array = array( '$set' => array(
			"roles.ocupante.difunto" => true,
			'fecnac' => $this->params['fecnac'],
			'fecdef' => $this->params['fecdef']
		));
		if(isset($this->params['fecdef'])) $array['$set']['fecdef'] = $this->params['fecdef'];
		$this->entidad->update(
			array('_id'=>$this->params['_id']) ,
			$array
		);
	}
	protected function delete_espacio(){
		global $f;
		$this->entidad->update(array( '_id' => new MongoId($this->params['_id']) ), array( '$unset'=>array('roles.ocupante.espacio'=>true) ) );
	}
	protected function delete_rol(){
		global $f;
		$this->entidad->update(array( '_id' => $this->params['_id'] ), array( '$unset'=>array('roles.ocupante'=>true) ) );
	}
	protected function get_all_ocu_pro(){
		global $f;
		$criterio = array(
			'roles.ocupante.propietario._id'=>new MongoId($this->params['_id']),
			'roles.ocupante.espacio'=>array('$exists'=>true)
		);
		$result = $this->entidad->find($criterio,array('_id'=>true,'nomb'=>true,'appat'=>true,'apmat'=>true,'tipo_enti'=>true,'roles.ocupante'=>true));
		foreach ($result as $obj) {
			//if(!$obj['roles']['ocupante']['difunto']){
				$this->items[] = $obj;
			//}
		}
	}
	protected function save_custom(){
		global $f;
		$this->entidad->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>