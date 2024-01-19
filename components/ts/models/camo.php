<?php
class Model_ts_camo extends Model {
	private $db;
	public $items;
	
	#ts_caja_chica_mov
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ts_cajas_chicas_movimientos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
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
	protected function get_fecreg(){
		global $f;
		$data = $this->db->find(array())->sort(array('fecreg'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_lcaja(){																					//Obtiene el ultimo movimiento de esa caja 
		global $f;
		$data = $this->db->find(array('caja._id'=>$this->params['_id']))->sort(array('fecreg'=>-1))->limit(1);		//Recibir el _id de caja para encontrar el ultimo movimiento
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_ldocum(){																				//Obtiene el ultimo movimiento de ese documento 
		global $f;
		$data = $this->db->find(array('caja._id'=>$this->params['_id']))->sort(array('fecreg'=>-1))->limit(1);		//Recibir el _id de documento para encontrar el ultimo movimiento
		foreach ($data as $obj) {
		    $this->items = $obj;
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
	protected function delete_cjdo(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>