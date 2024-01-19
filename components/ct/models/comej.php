<?php
class Model_ct_comej extends Model {
	private $tipo;
	public $items;
	
	public function __construct() {
		global $f;
		$this->tipo = $f->datastore->ct_cuadros_gastos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->tipo->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array(
			"periodo.ano"=>$this->params["ano"],
			"periodo.mes"=>$this->params["mes"]
			//"tipo"=>$this->params["tipo"]
		);
		if(isset($this->params["tipo"]))$filter["tipo"]=$this->params["tipo"];
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->tipo->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
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
		$cursor = $this->tipo->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->tipo->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->tipo->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->tipo->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_cuadro(){
		global $f;
		$this->items = array(
		    '_id' => $this->params["_id"],
		);
		$this->tipo->remove($this->items);
	}
}
?>