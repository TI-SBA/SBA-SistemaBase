<?php
class Model_lg_stck extends Model {
	private $alma;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->lg_stocks;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_one_custom(){
		global $f;
		$filter = array();
		$fields = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$this->items = $this->db->findOne($filter, $fields);
	}
	protected function get_prod(){
		global $f;
		$insert = array(
			'producto'=>$this->params['producto'],
			'almacen'=>$this->params['almacen']
		);
		$this->items = $this->db->findOne($insert);
		/*if($this->items==null){
			$insert['stock'] = 0;
			$insert['costo'] = 0;
			$this->db->insert($insert);
			$this->items = $insert;
		}*/
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		if(isset($this->params['almacen'])) $filter['almacen'] = $this->params['almacen'];
		$fields = array();
		$order = array('nomb'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj){
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
		}else $criteria = array();
		$criteria['estado'] = array('$ne'=>'D');
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$filter = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		$data = $this->db->find($filter,$fields);
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
	protected function save_stock_add(){
		global $f;
		$stck = array(
			'producto'=>$this->params['prod'],
			'almacen'=>$this->params['almacen']
		);
		$cant = 0;
		$total = 0;
		$stck = $this->db->findOne($stck);
		if(is_null($stck)){
			$almc = $f->model('lg/alma')->params(array('_id'=>$this->params['almacen']))->get('one')->items;
			$prod = $f->model('lg/prod')->params(array('_id'=>$this->params['prod']))->get('one')->items;
			$this->db->insert(array(
				'stock'=>doubleval($this->params['cant']),
				'costo'=>doubleval($this->params['total']),	
				'producto'=>$this->params['prod'],
				'almacen'=>$this->params['almacen'],
			));
		}else{
			$this->db->update( array('_id'=>$stck['_id']) , array('$inc'=>array(
				'stock'=>doubleval($this->params['cant']),
				'costo'=>doubleval($this->params['total']),
			)),array("upsert"=>true));
		}
		$this->items = $this->db->findOne(array('_id'=>$this->params['prod']));
	}

	protected function save_custom(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update(array('_id'=>$this->params['_id']),$this->params['data']);
	}
	protected function save_custom_all(){
		global $f;
		$this->db->update($this->params['filter'] ,$this->params['data'],array('multiple'=>true));
	}
	protected function save_custom_al_pr(){
		global $f;
		//$dato=$this->params;
		foreach ($this->params as $ob) {
			$this->db->update($ob, $this->params['data']);
		}
	}
}
?>