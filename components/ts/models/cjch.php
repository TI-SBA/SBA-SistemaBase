<?php
class Model_ts_cjch extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ts_cajas_chicas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('rubro'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('rubro','cod'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->db->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_by_orga(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->db->find(array('organizacion._id'=>$this->params['orga']),$fields);
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
		$this->params['data']['_id'] = $this->params['_id'];
		$this->items = $this->params['data'];
	}
	protected function delete_fuen(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->db->remove($this->items);
	}
}
?>