<?php
class Model_mg_serv extends Model {
	private $db;
	public $items;

	public function __construct() {
		global $f;
		$this->db = $f->datastore->mg_servicios;
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
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname','codigo'));

			}
		}
		if(isset($this->params['_id']))
			$criteria['_id'] = $this->params['_id'];
		if(isset($this->params['modulo']))
			$criteria['modulo'] = $this->params['modulo'];
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		if(isset($this->params['organizacion']))
			$criteria['organizacion._id'] = $this->params['organizacion'];
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_cm(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname','codigo'));

			}
		}
		if(isset($this->params['_id']))
			$criteria['_id'] = $this->params['_id'];
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search_lista(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb'));
		}else $criteria = array();
		if(isset($this->params['modulo']))
			$criteria['modulo'] = $this->params['modulo'];
		if(isset($this->params['nomb']))
			$criteria['nomb'] = $this->params['nomb'];
		if(isset($this->params['organizacion']))
			$criteria['organizacion._id'] = $this->params['organizacion'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search(){
		global $f;
		$criteria['organizacion._id'] = $this->params['orga'];
		$criteria['tipo'] = $this->params['tipo'];
		if(isset($this->params['aplicacion']))
			$criteria['aplicacion'] = $this->params['aplicacion'];
		$criteria['estado'] = $this->params['estado'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort(array('nomb'=>1));
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_all(){
		global $f;
		$data = $this->db->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all_orga(){
		global $f;
		$keys = array("organizacion" => 1);
		$initial = array("items" => array());
		$reduce = "function (obj, prev) { prev.items.push(obj.nomb); }";
		$this->items = $f->datastore->mg_servicios->group($keys, $initial, $reduce);
		$this->items = $this->items['retval'];
	}
	public function get_alll_orga()
	{
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
}
?>
