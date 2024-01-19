<?php
class Model_lg_bien extends Model {
	private $bien;
	public $items;
	
	public function __construct() {
		global $f;
		$this->bien = $f->datastore->lg_bienes;
	}
	protected function get_one(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->bien->findOne(array('_id'=>$this->params['_id']),$fields);
	}
	protected function get_lista(){
		global $f;
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		else $filter = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$order = array('cod'=>-1);
		$data = $this->bien->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->bien->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search_prod(){
		global $f;
		$criteria = array('producto._id'=>$this->params['prod'],'almacen._id'=>$this->params['alma']);
		$fields = array();
		$cursor = $this->bien->find($criteria,$fields)->sort( array('fecreg'=>1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_all(){
		global $f;
		$filter = array();
		if(isset($this->params["trab"]))$filter["responsable._id"]=$this->params["trab"];
		if(isset($this->params["mes"])&&isset($this->params["ano"])){
			$mes = (int)$this->params['mes'];
			$nextY = (int)$this->params['ano'];
			$next = $mes + 1;
			if($mes<10) $mes = '0'+$mes;
			if($next<10) $next = '0'+$next;
			elseif($next>12){
				$next = '01';
				$nextY = $nextY + 1;
			}
			$filter["fecreg"] = array(
				'$gte'=>new MongoDate(strtotime($this->params['ano']."-".$mes."-01")),
				'$lt'=>new MongoDate(strtotime($nextY."-".$next."-01"))
			);
		}
		if(isset($this->params["cuenta"]))$filter["cuenta._id"]=$this->params["cuenta"];
		if(isset($this->params["orga"]))$filter["responsable.cargo.organizacion._id"]=$this->params["orga"];
		$data = $this->bien->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->bien->find(array(),array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
		}
	}
	protected function save_insert(){
		global $f;
		$this->bien->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->bien->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_custom(){
		global $f;
		unset($this->params['data']['_id']);
		$this->bien->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
}
?>