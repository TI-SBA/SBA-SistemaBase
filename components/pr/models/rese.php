<?php
class Model_pr_rese extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pr_reservas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$filter['periodo.ano'] = $this->params['ano'];
		if($this->params['mes']!="0")$filter['periodo.mes'] = $this->params['mes'];
		$fields = array();
		$order = array('nomb'=>1);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb'));
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
		$filter = array();
		if(isset($this->params["ano"]))$filter["periodo.ano"] = $this->params["ano"];
		if(isset($this->params["mes"]))$filter["periodo.mes"] = $this->params["mes"];
		if(isset($this->params["organizacion"]))$filter["organizacion._id"] = new MongoId($this->params["organizacion"]);
		if(isset($this->params["fuente"]))$filter["fuente._id"] = new MongoId($this->params["fuente"]);
		if(isset($this->params["clasificador"]))$filter["clasificador._id"] = new MongoId($this->params["clasificador"]);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->db->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => $this->params['_id'],
		);
		$this->db->remove($this->items);
	}
}
?>