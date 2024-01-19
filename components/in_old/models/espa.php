<?php
class Model_in_old_espa extends Model {
	private $espa;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->espa = $this->db->in_espacios;
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array("_id"=>true,"nomb"=>true,"ubic"=>true,"tipo"=>true,"fecreg"=>true,"conserv"=>true,"imagen"=>true);
		$order = array('nomb'=>1);
		$data = $this->espa->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('nomb'=>1);
		$data = $this->espa->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_search(){
		global $f;
		$criteria = array();
		if($this->params["text"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["text"];
			$criteria = $helper->paramsSearch($this->params["text"], array('descr','ubic.ref'));
		}
		$criteria['ubic.local._id'] = $this->params['local'];
		$fields = array('_id'=>true,'descr'=>true,'ubic'=>true,'habilitado'=>true,'ocupado'=>true,'arrendatario'=>true);
		$cursor = $this->espa->find($criteria,$fields)->sort( array('descr'=>1,'fecreg'=>-1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_search_all(){
		global $f;
		$filter = array();
		if($this->params["text"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["text"];
			$filter = $helper->paramsSearch($this->params["text"], array('descr','ubic.local.nomb','ubic.local.direc','ubic.local.ref','ubic.ref'));
		}
		if(isset($this->params['uso'])){
			$filter['uso'] = $this->params['uso'];
		}
		$filter['ocupado'] = array('$ne'=>true);
		//$fields = array("_id"=>true,"nomb"=>true,"ubic"=>true,"tipo"=>true,"fecreg"=>true,"conserv"=>true,"imagen"=>true);
		$fields = array();
		$order = array('nomb'=>1);
		$data = $this->espa->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_espa(){
		global $f;
		$filter = array('ubic.local._id'=>$this->params['_id']);
		$fields = array('_id'=>true,'descr'=>true,'ubic.ref'=>true,'conserv'=>true,'uso'=>true,'habilitado'=>true,'arrendatario'=>true,'ocupado'=>true);
		$order = array('fecreg'=>-1);
		$data = $this->espa->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_one(){
		global $f;
		$this->items = $this->espa->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_one_all(){
		global $f;
		$this->items = $this->espa->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_mobil(){
		global $f;
		$this->items = $this->espa->findOne(array('_id'=>$this->params['_id']),array('mobiliario'));
	}
	protected function get_ocupados(){
		global $f;
		$data = $this->espa->find(array('ocupado'=>true));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_custom(){
		global $f;
		$data = $this->espa->find($this->params['filter']);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_insert(){
		global $f;
		$this->espa->insert( $this->params['data'] );
	}
	protected function save_act(){
		global $f;
		$this->espa->update(
			array('_id'=>$this->params['espacio']),
			array('$set'=>array(
				'ocupado'=>$this->params['ocupado'],
				'valor'=>array(
					'renta'=>$this->params['renta'],
					'garantia'=>$this->params['garantia'],
					'moneda'=>$this->params['moneda']
				),
				'arrendatario'=>$this->params['arrendatario'],
				'mobiliario'=>$this->params['mobiliario']
			))
		);
	}
	protected function save_act2(){
		global $f;
		$this->espa->update(
			array('_id'=>$this->params['espacio']),
			array('$set'=>array(
				'ubic.ref'=>$this->params['ref'],
				'uso'=>$this->params['uso'],
				'conserv'=>$this->params['conserv'],
				'descr'=>$this->params['descr'],
				'valor'=>array(
					'renta'=>$this->params['renta'],
					'garantia'=>$this->params['garantia'],
					'moneda'=>$this->params['moneda']
				),
				'mobiliario'=>$this->params['mobiliario']
			))
		);
	}
	protected function save_upd(){
		global $f;
		$this->espa->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
	protected function save_upd_custom(){
		global $f;
		$this->espa->update( $this->params['filter'] , $this->params['data'] , array("multiple" => true) );
	}
}
?>