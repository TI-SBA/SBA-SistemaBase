<?php
class Model_ch_cmmo extends Model {
	private $db;
	public $items;
	
	public function __construct(){
		global $f;
		$this->db = $f->datastore->mh_camas_movimientos;
	}

	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}

	protected function get_lista(){
	 	global $f;
	 	$criteria = array();
	 	if(isset($this->params['texto'])){
	 		if($this->params["texto"]!=''){
	 			$f->library('helpers');
	 			$helper=new helper();
	 			$parametro = $this->params["texto"];
	 			//$criteria = $helper->paramsSearch($this->params["texto"], array('camaente.nomb','camaente.appat','camaente.apmat','his_cli'));
	 			$criteria = array(
	 				'$or'=>array(
	 					array('his_cli'=>floatval($this->params['texto'])),
	 					array('camaente.fullname'=>new MongoRegex('/'.$parametro.'/i')),
	 					array('camaente.nomb'=>new MongoRegex('/'.$parametro.'/i')),
	 					array('camaente.appat'=>new MongoRegex('/'.$parametro.'/i')),
	 					array('camaente.apmat'=>new MongoRegex('/'.$parametro.'/i'))
	 				)
	 			);
	 		}
	 	}
		if(isset($this->params['_id']))
		{
			$filter= array(($this->params["_id"]));
		}
		else
			$filter= array();
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria,$filter)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}

	protected function get_fecreg(){
		global $f;
		$data = $this->db->find(array())->sort(array('fecreg'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}

	protected function get_cama(){
		global $f;
		//if(isset($this->$params['pabellon'])) 
			$filter['pabellon']=$this->params['pabellon'];
		//if(isset($this->$params['sala'])) 
			$filter['sala']=$this->params['sala'];
		//if(isset($this->$params['cama'])) 
			$filter['cama._id']=$this->params['_id'];
		$this->items = $this->db->findOne($filter);
	}

	protected function get_all(){
		global $f;
		$fields = array();
		$filter = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		$data = $this->db->find($filter,$fields);
		if(isset($this->params['limit'])) $data->limit($this->params['limit']);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	
	protected function get_custom(){
		global $f;
		$filter = array();
		$fields = array();
		if(isset($this->params['filter'])) 
			$filter = $this->params['filter'];
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_vacias_intensivo_varones(){
		global $f;
		$filter = array(
			'pabellon' => 'INTENSIVO',
			'sala' => 'VARONES',
			'estado' => 0,
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_vacias_intensivo_damas(){
		global $f;
		$filter = array(
			'estado' => 0,
			'pabellon' => "INTENSIVO",
			'sala' => "DAMAS"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_vacias_intermedio_varones(){
		global $f;
		$filter = array(
			'estado' => 0,
			'pabellon' => "INTERMEDIO",
			'sala' => "VARONES"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_vacias_intermedio_damas(){
		global $f;
		$filter = array(
			'estado' => 0,
			'pabellon' => "INTERMEDIO",
			'sala' => "DAMAS"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_usadas_intensivo_varones(){
		global $f;
		$filter = array(
			'estado' => 1,
			'pabellon' => "INTENSIVO",
			'sala' => "VARONES"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_usadas_intensivo_damas(){
		global $f;
		$filter = array(
			'estado' => 1,
			'pabellon' => "INTENSIVO",
			'sala' => "DAMAS"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_usadas_intermedio_varones(){
		global $f;
		$filter = array(
			'estado' => 1,
			'pabellon' => "INTERMEDIO",
			'sala' => "VARONES"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function get_usadas_intermedio_damas(){
		global $f;
		$filter = array(
			'estado' => 1,
			'pabellon' => "INTERMEDIO",
			'sala' => "DAMAS"
		);
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}

	protected function save_insert(){
		global $f;
		$this->db->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
//		isset($this->$data["his_cli"] = floatval($data["his_cli"]));

	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function delete_cmmo(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}

}
?>