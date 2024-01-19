<?php
class Model_po_visi extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->po_visitas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		if(isset($this->params["texto"])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
			}else $criteria = array();
		}else $criteria = array();
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
        protected function get_search(){
		global $f;
		$criteria = array();
		if(isset($this->params["filter"])){
			foreach ($this->params['filter'] as $i=>$filter){
				$criteria[$filter['nomb']] = $filter['value'];
				if(substr($filter['nomb'],-3)=='_id'){
					$criteria[$filter['nomb']] = new MongoId($filter['value']);
				}
			}
		}
		if(isset($this->params['fecha']))
			$params['fec'] = $this->params['fecha'];
		$fields = array();
		$sort = array('fecent'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		if(isset($this->params["page"])){
			$cursor = $this->db->find($criteria,$fields)->sort($sort)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
			foreach ($cursor as $obj) {
				$this->items[] = $obj;
			}
			$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
		}else{
			$cursor = $this->db->find($criteria,$fields)->sort($sort);
			foreach ($cursor as $obj) {
				$this->items[] = $obj;
			}
		}
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
