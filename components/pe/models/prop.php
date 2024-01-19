<?php
class Model_pe_prop extends Model {
	private $prop;
	public $items;
	
	public function __construct() {
		global $f;
		$this->prop = $f->datastore->pe_propinas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->prop->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_one_prop(){
		global $f;
		$filtro = array(
			"periodo.ano"=>$this->params["periodo"],
			"periodo.mes"=>$this->params["mes"],
			"practicante._id"=>$this->params["_id"]
		);
		$this->items = $this->prop->findOne($filtro);
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('nomb'=>1);
		$data = $this->prop->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod','abrev'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->prop->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$filter = array("practicante._id"=>$this->params["_id"]);
		$data = $this->prop->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->prop->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->prop->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
}
?>