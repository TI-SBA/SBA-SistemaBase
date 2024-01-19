<?php
class Model_pe_conc extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pe_conceptos;
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
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','descr','cod','contrato.cod'));
			}
		}
		if(isset($this->params["tipo"]))
			$criteria["contrato._id"] = $this->params["tipo"];
		if(isset($this->params["contrato"]))
			$criteria["contrato.cod"] = $this->params["contrato"];
		$sort = array('contrato.cod'=>1,'tipo'=>1,'nomb'=>1);
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
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$filter = array('estado'=>'H');
		if(isset($this->params['doc'])) $filter[$this->params['doc']] = true;
		if(isset($this->params['tipo'])) $filter['contrato.cod'] = $this->params['tipo'];
		if(isset($this->params["contrato"]))$filter["contrato._id"] = $this->params["contrato"];
		$data = $this->db->find($filter,$fields)->sort(array('orden'=>1));
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
		$this->db->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
}
?>