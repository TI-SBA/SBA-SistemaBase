<?php
class Model_ti_sist extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ti_acceso_sistema;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','local.descr','encargado','oficina','ip'));
			}
		}
		$sort = array('order'=>1);
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
		$sort = array();
		$filter = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		if(isset($this->params['sort'])) {
			$sort = $this->params['sort'];
			$data = $this->db->find($filter,$fields)->sort($sort);
		}else{
			$data = $this->db->find($filter,$fields);
		}
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
}
?>