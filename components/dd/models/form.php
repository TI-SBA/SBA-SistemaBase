<?php
class Model_dd_form extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->dd_formatos_doc;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_reporte(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('nomb'=>1,'desc'=>1,'fecreg'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
	//			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','sigl'));
			}
		}
		$sort = array('_id'=>-1);
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
	protected function delete_form(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>