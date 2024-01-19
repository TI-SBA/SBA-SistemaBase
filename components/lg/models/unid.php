<?php
class Model_lg_unid extends Model {
	private $unidad;
	public $items;
	
	public function __construct() {
		global $f;
		$this->unidad = $f->datastore->lg_unidades;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->unidad->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_por_nomb(){
		global $f;
		$this->items = $this->unidad->findOne(array('nomb'=>$this->params['nomb']));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
			}
		}
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->unidad->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->unidad->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->unidad->find()->sort( array('nomb'=>1) );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->unidad->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->unidad->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
}
?>