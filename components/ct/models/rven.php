<?php
class Model_ct_rven extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ct_registros_ventas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$fecha = new DateTime();
		$fecha->setDate($f->request->data['ano'], $f->request->data['mes'], 1);
		$fecha = $fecha->format('Ym00');
		$filter = array('periodo'=>$fecha);
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('periodo','num'));
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
		$filter = array();
		$fields = array();
		if(isset($this->params["ano"])&&isset($this->params["mes"])){
			$fecha = new DateTime();
			$fecha->setDate($this->params["ano"], $this->params["mes"], 1);
			$fecha = $fecha->format('Ym00');
			$filter["periodo"] = $fecha;
		}
		$data = $this->db->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find(array('periodo'=>date('Ym00')),array('num_correlativo'=>true))->sort(array('fecreg'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['num_correlativo'];
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
		$this->db->update( $this->params['filter'] ,$this->params['data'],array("multiple" => true));
	}
}
?>