<?php
class Model_ct_notc extends Model {
	private $notc;
	public $items;
	
	public function __construct() {
		global $f;
		$this->notc = $f->datastore->ct_notas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->notc->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_onenum(){
		global $f;
		$this->items = $this->notc->findOne(array('periodo.ano'=>$this->params['periodo'],'num'=>$this->params['numero']));
	}
	protected function get_lista(){
		global $f;
		$filter = array(
			'periodo.ano'=>$this->params['ano'],
			'periodo.mes'=>floatval($this->params['mes'])
		);
		$fields = array();
		if($this->params["tipo"]!="0"){
			$filter["tipo._id"]=new MongoId($this->params["tipo"]);
		}else{
			$filter["tipo"]=array('$exists',true);
		}
		$order = array('_id'=>-1);
		$data = $this->notc->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_bala(){
		global $f;
		$filter = array();
		if(isset($this->params["ano"]))$filter["periodo.ano"] = floatval($this->params['ano']);
		if(isset($this->params["mes"]))$filter["periodo.mes"] = floatval($this->params['mes']);
		if(isset($this->params["num"]))$filter["num"] = $this->params['num'];
		if(isset($this->params["hasta_mes"])) $filter["periodo.mes"] = array('$lte'=>floatval($this->params["hasta_mes"]));
		$data = $this->notc->find($filter,array());
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_search(){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$cod = $this->params["texto"];
		$regex = new MongoRegex("/^".$cod."/");
		$criteria["num"]= $regex;
		$fields = array();
		$cursor = $this->notc->find($criteria,$fields)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_num(){
		global $f;
		$cursor = $this->notc->find(array('periodo.ano'=>$this->params['periodo'],'tipo._id'=>$this->params["tipo"]),array('num'=>true))->sort(array('fecreg'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['num'];
		}
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->notc->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->notc->find($this->params['filter'],$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->notc->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->notc->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_tipo(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->notc->remove($this->items);
	}
}
?>