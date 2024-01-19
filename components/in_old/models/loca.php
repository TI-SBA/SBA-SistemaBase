<?php
class Model_in_old_loca extends Model {
	private $local;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->local = $this->db->in_locales;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->local->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		if(isset($this->params['estado']))
			$filter['estado'] = $this->params['estado'];
		$fields = array(
			"_id"=>true,
			"nomb"=>true,
			"ubic"=>true,
			"tipo"=>true,
			"fecreg"=>true,
			"conserv"=>true,
			"imagen"=>true,
			'estado'=>true
		);
		$order = array('estado'=>-1);
		$data = $this->local->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search(){
		global $f;
		if($this->params["text"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["text"];
			$criteria = $helper->paramsSearch($this->params["text"], array('nomb','ubic.direc'));
		}else $criteria = array();
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		$fields = array(
			"_id"=>true,
			"nomb"=>true,
			"ubic"=>true,
			"tipo"=>true,
			"fecreg"=>true,
			"conserv"=>true,
			"imagen"=>true,
			'estado'=>true
		);
		$cursor = $this->local->find($criteria,$fields)->sort( array('estado'=>-1,'fecreg'=>-1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$filter = array();
		$order = array('estado'=>-1);
		$data = $this->local->find($filter)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_insert(){
		global $f;
		$this->local->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->local->update(array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_custom(){
		global $f;
		$this->local->update(array('_id'=>$this->params['_id'] ),$this->params['data']);
	}
}
?>