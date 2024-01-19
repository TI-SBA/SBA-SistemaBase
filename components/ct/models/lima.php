<?php
class Model_ct_lima extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ct_libros_mayores;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_periodo(){
		global $f;
		$filter = array(
			'periodo.mes'=>$this->params['mes'],
			'periodo.ano'=>$this->params['ano'],
			'cuenta._id'=>$this->params['cuenta']
		);
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne($filter,$fields);
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
	protected function get_custom(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->db->find($this->params['filter'],$fields);
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
}
?>