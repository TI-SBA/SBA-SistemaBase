<?php
class Model_ts_cjdo extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ts_cajas_chicas_documentos;
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
	//			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','sigl'));
			}
		}
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
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
	protected function get_lsesion(){																				//Obtiene el ultimo documento de esa sesion 
		global $f;
		$data = $this->db->find(array('sesion._id'=>$this->params['_id']))->sort(array('fecreg'=>-1))->limit(1);	//Recibir el _id de sesion para encontrar el ultimo documento registrado 
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_all(){
		global $f;
		$filter = array();
		$fields = array();
		$sort = array('_id'=>1);
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		if(isset($this->params['sort']))   $sort = $this->params['sort'];
		$data = $this->db->find($filter,$fields)->sort($sort);
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
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function delete_cjdo(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>