<?php
class Model_pr_unid extends Model {
	private $unid;
	public $items;
	
	public function __construct() {
		global $f;
		$this->unid = $f->datastore->pr_unidades;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->unid->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('nomb'=>1);
		$data = $this->unid->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
		$cursor = $this->unid->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->unid->find(array(),$fields)->sort(array("nomb"=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->unid->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->unid->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_unid(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->unid->remove($this->items);
	}
}
?>