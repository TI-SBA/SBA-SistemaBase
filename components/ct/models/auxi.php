<?php
class Model_ct_auxi extends Model {
	private $col;
	public $items;
	
	public function __construct() {
		global $f;
		$this->col = $f->datastore->ct_auxiliares_ingresos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->col->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$fields = array();
		$filter = array(
			'saldo._id' => new MongoId($this->params['saldo'])		
		);
		$order = array('fecreg'=>1);
		$data = $this->col->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_lista_ri(){
		global $f;
		$fields = array();
		$cod = $this->params["clasificador"];
		$regex = new MongoRegex("/^".$cod."/");
		$codigo= $regex;
		$filter = array(
			'clase' => "RI",
			'saldo.periodo.ano' => $this->params["ano"],
			'saldo.periodo.mes' => floatval($this->params["mes"]),
			'saldo.organizacion._id' => new MongoId($this->params["organizacion"]),
			'saldo.fuente._id' => new MongoId($this->params["fuente"]),
			'saldo.subespecifica.cod' => $codigo
		);
		$order = array('fecreg'=>1);
		$data = $this->col->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all_filter(){
		global $f;
		$fields = array();
		$cod = $this->params["clasificador"];
		$regex = new MongoRegex("/^".$cod."/");
		$codigo= $regex;
		$filter = array(
			'clase' => "RI",
			'saldo.periodo.ano' => $this->params["ano"],
			//'saldo.periodo.mes' => floatval($this->params["mes"]),
			'saldo.organizacion._id' => new MongoId($this->params["organizacion"]),
			//'saldo.fuente._id' => new MongoId($this->params["fuente"]),
			'saldo.subespecifica.cod' => $codigo
		);
		if(isset($this->params["mes"])) $filter["saldo.periodo.mes"] = floatval($this->params["mes"]);
		if(isset($this->params["estado"])) $filter["estado"] = $this->params["estado"];
		if(isset($this->params["hasta_mes"])) $filter["saldo.periodo.mes"] = array('$lte'=>floatval($this->params["hasta_mes"]));
		/*if(isset($this->params["meta"])){
			$filter["meta"] = $this->params["meta"];
		}else{
			$filter["meta"] = array('$exists'=>false);
		}*/
		$data = $this->col->find($filter,$fields);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->col->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$filter = array();
		$fields = array();
		//if(isset($this->params["mes"]))$filter["saldo.periodo.mes"] = $this->params["mes"];
		if(isset($this->params["ano"]))$filter["saldo.periodo.ano"] = $this->params["ano"];
		if(isset($this->params["organizacion"]))$filter["saldo.organizacion._id"] = new MongoId($this->params["organizacion"]);
		if(isset($this->params["fuente"]))$filter["saldo.fuente._id"] = new MongoId($this->params["fuente"]);
		if(isset($this->params["clasificador"]))$filter["saldo.subespecifica._id"] = new MongoId($this->params["clasificador"]);
		if(isset($this->params["hasta_mes"])) $filter["saldo.periodo.mes"] = array('$lte'=>floatval($this->params["hasta_mes"]));		
		$data = $this->col->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->col->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->col->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_fuen(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->col->remove($this->items);
	}
}
?>