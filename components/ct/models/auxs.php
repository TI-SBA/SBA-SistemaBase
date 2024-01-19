<?php
class Model_ct_auxs extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ct_auxiliares;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array(
			'saldos.periodo.ano'=>$this->params['ano'],
			'saldos.cuenta._id'=>$this->params['cuenta']
		);
		if(isset($this->params['mes']))$filter['saldos.periodo.mes'] = $this->params['mes'];
		if(isset($this->params['programa'])) $filter['saldos.programa._id'] = $this->params['programa'];
		if(isset($this->params['inmueble'])) $filter['saldos.inmueble._id'] = $this->params['inmueble'];
		$fields = array();
		$order = array('fecreg'=>1);
		//print_r($filter);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_lista_periodo(){
		global $f;
		$filter = array(
			'saldos.periodo.ano'=>$this->params['ano'],
			'saldos.periodo.mes'=>$this->params['mes'],
			'saldos.cuenta.tipo'=>$this->params['tipo']
		);
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_lista_bala(){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$cod = $this->params["cuenta"];
		$regex = new MongoRegex("/^".$cod."/");
		$criteria["clasificador.cod"]= $regex;
		$filter = array(
			'saldos.periodo.ano'=>$this->params['ano'],
			'saldos.cuenta.cod'=>$regex
		);
		if(isset($this->params['mes']))$filter['saldos.periodo.mes'] = $this->params['mes'];
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_periodo(){
		global $f;
		$filter = array(
			'saldos._id'=>$this->params['saldo']
		);
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev','salario'));
		}else $criteria = array();
		$criteria['estado'] = $this->params['estado'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('fecreg'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->db->find();
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
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>