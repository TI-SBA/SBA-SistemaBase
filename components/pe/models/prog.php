<?php
class Model_pe_prog extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pe_incidencias;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_trab(){
		global $f;
		$filter= array();
		$filter['trabajador._id'] = $this->params['enti'];
		$filter['tipo.tipo'] = $this->params['tipo'];
		$filter['programada'] = true;
		$fields = array();
		$order = array('fecini'=>1);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_trab_prog(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti']);
		$filter['programada'] = true;
		//$filter = array('tipo.tipo'=>$this->params['tipo']);
		$fields = array();
		$order = array('fecini'=>1);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_marc(){
		global $f;
		//$filter = array('trabajador._id'=>$this->params['enti'],'ejecutado'=>array('$exists'=>true));
		$filter = array('trabajador._id'=>$this->params['enti']);
		$filter['programada'] = true;
		$fields = array();
		$order = array('ejecucion.entrada.fecreg'=>1,'programado.inicio'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_inci(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti'],'ejecutado.entrada'=>array('$exists'=>true),'ejecutado.salida'=>array('$exists'=>true));
		$filter['programada'] = true;
		$fields = array();
		$order = array('programado.inicio'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('abrev'=>1);
		$filter['programada'] = true;
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev','salario'));
		}else $criteria = array();
		$criteria['estado'] = $this->params['estado'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
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
		$this->params['data']['programada'] = true;
		$this->db->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->params['data']['programada'] = true;
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_data(){
		global $f;
		$this->db->remove( array('_id'=>$this->params['_id']) );
	}
}
?>