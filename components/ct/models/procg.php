<?php
class Model_ct_procg extends Model {
	private $col;
	public $items;
	
	public function __construct() {
		global $f;
		$this->col = $f->datastore->ct_presupuestos_gastos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->col->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_ultimo(){
		global $f;	
		$data = $this->col->find(array(
						"periodo.ano"=>date("Y"),
						"organizacion._id"=>$this->params["organizacion"],
						"fuente._id"=>$this->params["fuente"],
						"clasificador._id"=>$this->params["subespecifica"]
		))->sort(array("periodo.mes"=>-1))->limit(1);		
		if($data->count()==0){//no hay saldos en este año
			$this->items = "reinicio";
		}else{
			//$this->items = $data;
			foreach ($data as $ob) {
			    $this->items = $ob;
			}
		}
	}
	protected function get_lista(){
		global $f;
		$filter = array(
			'periodo.ano'=>$this->params["ano"],
			//'periodo.mes'=>$this->params["mes"]
			//'fuente._id'=>$this->params["fuente"],
			//'organizacion._id'=>$this->params["organizacion"]
		);
		if(isset($this->params["mes"]))$filter["periodo.mes"]=$this->params["mes"];
		if(isset($this->params["fuente"]))$filter["fuente._id"]=$this->params["fuente"];
		if(isset($this->params["organizacion"]))$filter["organizacion._id"]=$this->params["organizacion"];
		if(isset($this->params["trimestre"])){
			if($this->params["trimestre"]=="1")$trimestre = array(1,2,3);
			elseif($this->params["trimestre"]=="2")$trimestre = array(4,5,6);
			elseif($this->params["trimestre"]=="3")$trimestre = array(7,8,9);
			elseif($this->params["trimestre"]=="4")$trimestre = array(10,11,12);
			$filter["periodo.mes"] = array('$in'=>$trimestre);
		}
		$fields = array();
		$order = array('fecreg'=>1);
		$data = $this->col->find($filter,$fields)->sort($order);
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
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		else $filter = array();
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