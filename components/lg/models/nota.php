<?php
class Model_lg_nota extends Model {
	private $nota;
	public $items;
	
	public function __construct() {
		global $f;
		$this->nota = $f->datastore->lg_notas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->nota->findOne(array('_id'=>$this->params['_id']));
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
		$sort = array('_id'=>-1);
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		if(isset($this->params['trabajador']))
			$criteria['trabajador._id'] = $this->params['trabajador'];
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->nota->find($criteria,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('cod','trabajador.nomb'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->nota->find(array_merge($criteria,$this->params['filter']),$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->nota->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->nota->find(array(),array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
		}
	}
	protected function get_periodo(){
		global $f;
		$mes = (int)$this->params['mes'];
		$nextY = (int)$this->params['ano'];
		$next = $mes + 1;
		if($mes<10) $mes = '0'+$mes;
		if($next<10) $next = '0'+$next;
		elseif($next>12){
			$next = '01';
			$nextY = $nextY + 1;
		}
		$filter = array('estado'=>'A','fecreg'=>array(
			'$gte'=>new MongoDate(strtotime($this->params['ano']."-".$mes."-01")),
			'$lt'=>new MongoDate(strtotime($nextY."-".$next."-01"))
		));
		$fields = array('productos'=>true,'motivo'=>true);
		$order = array('fecreg'=>-1);
		$data = $this->nota->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_insert(){
		global $f;
		$this->nota->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->nota->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_push(){
		global $f;
		$this->nota->update( array('_id'=>$this->params['_id']) , array('$push'=>array('revisiones'=>$this->params['data'])) );
	}
}
?>