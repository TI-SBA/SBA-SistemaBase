<?php
class Model_al_cont extends Model {
	private $cont;
	public $items;
	
	public function __construct() {
		global $f;
		$this->cont = $f->datastore->al_contigencias;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->cont->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$filter['clasificacion'] = $this->params['clasificacion'];
		$filter['tipo'] = $this->params['tipo'];
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->cont->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('numero'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->cont->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
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
		if(isset($this->params['clasificacion'])) $filter["clasificacion"] = $this->params["clasificacion"];
		if(isset($this->params["periodo"])){
			$regex = new MongoRegex("/".$this->params["periodo"]."/");
			$filter["numero"] = $regex;
			/*$ini = new MongoDate(strtotime($this->params['periodo']."-01-01"));
			$fin = new MongoDate(strtotime((floatval($this->params['periodo'])+1)."-01-01"));
			$filter["fecreg"] = array('$gte' => $ini, '$lt' => $fin);*/
		}
		$data = $this->cont->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->cont->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->cont->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_cont(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->cont->remove($this->items);
	}
}
?>