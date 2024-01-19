<?php
class Model_ct_cban extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ct_conciliaciones;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_conc(){
		global $f;
		//$periodo = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01"));
		$this->items = $this->db->findOne(array('cuenta_banco._id'=>$this->params['_id'],'periodo.ano'=>$this->params['ano'],'periodo.mes'=>$this->params['mes']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		if($this->params["mes"]!="")$filter["periodo.mes"]=$this->params["mes"];
		else $filter["periodo"]=array('$exists'=>true);
		if($this->params["ano"]!="")$filter["periodo.ano"]=$this->params["ano"];
		else $filter["periodo"]=array('$exists'=>true);
		$order = array('fecreg'=>-1);
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
		$filter = array();
		$fields = array();
		if(isset($this->params["mes"]))$filter["periodo.mes"] = $this->params["mes"];
		if(isset($this->params["ano"]))$filter["periodo.ano"] = $this->params["ano"];
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
	protected function delete_tipo(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->db->remove($this->items);
	}
}
?>