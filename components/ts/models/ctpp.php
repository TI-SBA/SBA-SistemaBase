<?php
class Model_ts_ctpp extends Model {
	private $col;
	public $items;
	
	public function __construct() {
		global $f;
		$this->col = $f->datastore->ts_cuentas_pagar;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->col->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		if($this->params["estado"]!=""){
			$filter = array('estado'=>$this->params["estado"]);
		}else{
			$filter = array();
		}
		if(isset($this->params["modulo"])) $filter["modulo"] = $this->params["modulo"];
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->col->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
				$criteria = $helper->paramsSearch($this->params["texto"], array('beneficiario.nomb','beneficiario.appat','beneficiario.apmat','motivo'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->col->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search_all(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], $this->params["fields"]);
		}else $criteria = array();
		$fields = array();
		$cursor = $this->col->find($criteria,$fields);
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->col->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->col->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->col->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->params['data']['_id'] = $this->params['_id'];
		$this->items = $this->params['data'];
	}
	protected function delete_conv(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->col->remove($this->items);
	}
}
?>