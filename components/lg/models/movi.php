<?php
class Model_lg_movi extends Model {
	private $movi;
	public $items;
	
	public function __construct() {
		global $f;
		$this->movi = $f->datastore->lg_movimientos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->movi->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_ultimo_movimiento(){
		global $f;
		$filter = array();
		$fields = array();
		$sort = array();
		if(isset($this->params['producto'])) $filter['producto']['_id'] = $this->params['producto'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$sort = -1;
		$data = $this->movi->find($filter, $fields)->sort($sort);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
			}
		}
		if(isset($this->params['producto']))
			$criteria['producto._id'] = $this->params['producto'];
		if(isset($this->params['almacen']))
			$criteria['almacen._id'] = $this->params['almacen'];
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->movi->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_periodo(){
		global $f;
		$mes = (int)$this->params['mes'];
		$nextY = (int)$this->params['ano'];
		$next = $mes + 1;
		if($mes<10){
			$mes = '0'.$mes;
		}
		if($next<10) $next = '0'.$next;
		elseif($next>12){
			$next = '01';
			$nextY = $nextY + 1;
		}
		$filter = array('fecreg'=>array(
			'$gte'=>new MongoDate(strtotime($this->params['ano']."-".$mes."-01")),
			'$lt'=>new MongoDate(strtotime($nextY."-".$next."-01"))
		));
		if(!isset($this->params['init'])){
			if($this->params['tipo']=='S') $filter['tipo'] = 'S';
			elseif($this->params['tipo']=='E') $filter['tipo'] = 'E';
			else $filter['tipo'] = array('$ne'=>'I');
		}
		if(isset($this->params['prod'])) $filter['producto._id'] = $this->params['prod'];
		if(isset($this->params['alma'])) $filter['almacen._id'] = $this->params['alma'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->movi->find($filter,$fields);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->movi->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_custom(){
		global $f;
		if(!isset($this->params['filter']))
			$this->params['filter'] = array();
		if(!isset($this->params['sort']))
			$this->params['sort'] = array();
		if(!isset($this->params['fields']))
			$this->params['fields'] = array();
		$data = $this->movi->find($this->params['filter'],$this->params['fields'])->sort($this->params['sort'])->limit(1);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all(){
		global $f;
		$filter = array();
		$fields = array();
		$sort = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		if(isset($this->params['sort'])) $sort = $this->params['sort'];
		$data = $this->movi->find($filter, $fields)->sort($sort);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->movi->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->movi->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
}
?>