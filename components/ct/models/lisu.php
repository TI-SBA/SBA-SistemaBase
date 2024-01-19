<?php
class Model_ct_lisu extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ct_diarios_sunat;
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find(array('periodo'=>$this->params['periodo']),array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
		}
		if($this->items==null) $this->items = 1;
		else $this->items = floatval($this->items)+1;
	}
	protected function get_custom(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->db->find($this->params['filter'],$fields)->sort(array('cod'=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
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
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function delete_item(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>