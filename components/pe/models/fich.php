<?php
class Model_pe_fich extends Model {
	private $ficha;
	public $items;
	
	public function __construct() {
		global $f;
		$this->ficha = $f->datastore->pe_fichas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->ficha->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_enti(){
		global $f;
		$this->items = $this->ficha->findOne(array('entidad._id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('abrev'=>1);
		$data = $this->ficha->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev','salario'));
		}else $criteria = array();
		$criteria['estado'] = $this->params['estado'];
		$fields = array();
		$cursor = $this->ficha->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->ficha->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$array = $this->params['data'];
		$this->ficha->insert( $array );
		$this->obj = $array;
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->ficha->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_custom(){
		global $f;
		$this->ficha->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
}
?>