<?php
class Model_ts_saldlibr extends Model {
	private $col;
	public $items;
	
	public function __construct() {
		global $f;
		$this->col = $f->datastore->ts_saldos_libros;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->col->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_activo(){
		global $f;
		$params = array(
			'tipo'=>$this->params['tipo'],
			'estado'=>'A'
		);
		if(isset($this->params['periodo'])) $params['periodo'] = $this->params['periodo'];
		if(isset($this->params['cuenta'])) $params['cuenta_banco._id'] = $this->params['cuenta'];
		$this->items = $this->col->findOne($params);
	}
	protected function get_sald(){
		global $f;
		$this->items = $this->col->findOne(array('tipo'=>$this->params["tipo"],'cuenta_banco._id'=>$this->params['_id']));
	}
	protected function get_rein(){
		global $f;
		$data = $this->col->find(array('tipo'=>$this->params['tipo'],'periodo'=>$this->params['periodo']))->sort(array('fecreg'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->items = $this->items[0];
	}
	protected function get_last_rein(){
		global $f;
		$filter = array();
		if(isset($this->params["cuenta"])) $filter["cuenta_banco._id"]=$this->params["cuenta"];
		$filter["tipo"] = $this->params['tipo'];
		$data = $this->col->find($filter)->sort(array('periodo'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->items = $this->items[0];
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		if($this->params["tipo"]==""){
			$filter["tipo"]=array('$exists'=>true);
		}else{
			$filter["tipo"]=$this->params["tipo"];
		}
		$order = array('periodo'=>-1);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb'));
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
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();	
		//$filter['estado']="A";
		$data = $this->col->find(array("tipo"=>$this->params["tipo"]),$fields);
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
	}
	protected function save_custom(){
		global $f;
		$this->col->update( $this->params['filter'] , $this->params['data'] );
	}
	protected function save_editar(){
		global $f;
		unset($this->params['data']['_id']);
		$this->col->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
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