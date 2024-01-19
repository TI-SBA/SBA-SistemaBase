<?php
class Model_ct_saux extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ct_saldos_auxiliar;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('abrev'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_periodo(){
		global $f;
		$filter = array(
			'periodo.ano'=>$this->params['ano'],
			'periodo.mes'=>$this->params['mes'],
			'cuenta.tipo'=>$this->params['tipo']
		);
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_cuenta_mayor(){
		global $f;
		$filter = array(
			'periodo.ano'=>$this->params['ano'],
			'periodo.mes'=>$this->params['mes'],
			'cuenta._id'=>$this->params['cuenta']
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
	protected function get_saldo(){
		global $f;
		$filter = array(
			'periodo.ano'=>$this->params['ano'],
			'cuenta._id'=>$this->params['cuenta']
		);
		//if(isset($this->params['cuenta_mayor']))$filter['cuenta_mayor._id'] = $this->params['cuenta_mayor'];
		if(isset($this->params['mes']))$filter['periodo.mes'] = $this->params['mes'];
		if(isset($this->params['programa'])) $filter['programa._id'] = $this->params['programa'];
		if(isset($this->params['inmueble'])) $filter['inmueble._id'] = $this->params['inmueble'];
		$this->items = $this->db->findOne($filter);
	}
	protected function get_resu(){
		global $f;
		$filter = array(
			'estado'=>'C',
			'periodo.ano'=>$this->params['ano'],
			'cuenta.tipo'=>$this->params['tipo']
		);
		$data = $this->db->find($filter)->sort(array('mes'=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->db->find($this->params['filter'],$fields)->sort(array('periodo.mes'=>1,'periodo.ano'=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
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
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function save_mult(){
		global $f;
		$this->db->update(array( $this->params['filter'] , $this->params['data'] , array('multiple'=>true) ));
	}
	protected function delete_item(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>