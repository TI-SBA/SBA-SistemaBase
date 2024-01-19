<?php
class Model_ct_cpat extends Model {
	private $cpat;
	public $items;
	
	public function __construct() {
		global $f;
		$this->cpat = $f->datastore->ct_registros_patrimonio;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->cpat->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array(
			'periodo.mes'=>$this->params['mes'],
			'periodo.ano'=>$this->params['ano'],
			'pabellon._id'=>$this->params['pabellon']
		);
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->cpat->find($filter,$fields)->sort($order);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('rubro','cod'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->cpat->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->cpat->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->cpat->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->cpat->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_tipo(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->cpat->remove($this->items);
	}
}
?>