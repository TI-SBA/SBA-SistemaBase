<?php
class Model_in_calp extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->in_calendario_pagos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params["texto"])){
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
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$data = $this->db->find(array(),$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_periodo(){
		global $f;
		$filter = array(
			'mes'=>$this->params['mes'],
			'ano'=>$this->params['ano']
		);
		if(isset($this->params['tipo'])){
			$filter['tipo'] = $this->params['tipo'];
		}
		$this->items = $this->db->findOne($filter);
	}
	protected function get_periodo_next(){
		global $f;
		$this->params['mes'] = intval($this->params['mes'])+1;
		if($this->params['mes']==13){
			$this->params['mes'] = 1;
			$this->params['ano'] = intval($this->params['ano'])+1;
		}
		$this->params['mes'] .= '';
		$this->params['ano'] .= '';
		$this->items = $this->db->findOne(array(
			'mes'=>$this->params['mes'],
			'ano'=>$this->params['ano']
		));
		if($this->params['mes']<10){
			$this->params['mes'] = '0'.$this->params['mes'];
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
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>