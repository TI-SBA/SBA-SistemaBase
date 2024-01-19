<?php
class Model_cj_cuen extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->cj_cuentas_cobrar;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		if(isset($this->params["texto"])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$filter = $helper->paramsSearch($this->params["texto"], array(
					'servicio.nomb',
					'organizacion.nomb',
					'cliente.nomb',
					'cliente.appat',
					'cliente.apmat',
					'cliente.fullname',
					'cliente.docident',
					'cliente.docident.num',
					'cliente.ruc',
					'cliente.dni',
					'cliente.doc'
				));
			}
		}
		if(isset($this->params['estado'])) $filter['estado'] = $this->params['estado'];
		if(isset($this->params['orga'])) $filter['servicio.organizacion._id'] = $this->params['orga'];
		if(isset($this->params['inmueble'])) $filter['inmueble._id'] = $this->params['inmueble'];
		if(isset($this->params['espacio'])) $filter['espacio._id'] = $this->params['espacio'];
		if(isset($this->params['cliente'])) $filter['cliente._id'] = $this->params['cliente'];
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		//print_r($filter);die();
		$data = $this->db->find($filter)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('servicio.nomb','organizacion.nomb','cliente.nomb','cliente.appat','cliente.apmat'));
		}else $criteria = array();
		if(isset($this->params['estado'])) $criteria['estado'] = $this->params['estado'];
		if(isset($this->params['orga'])) $criteria['servicio.organizacion._id'] = $this->params['orga'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$filter = array();
		if(isset($this->params["modulo"])) $filter["modulo"] = $this->params["modulo"];
		if(isset($this->params["estado"])) $filter["estado"] = $this->params["estado"];
		if(isset($this->params["contrato"])) $filter["contrato"] = $this->params["contrato"];
		if(isset($this->params["ano"])){
			$filter["fecreg"] =array(
				'$gte'=>new MongoDate(strtotime($this->params["ano"]."-01-01")),
				'$lt'=>new MongoDate(strtotime((floatval($this->params["ano"])+1)."-01-01"))
			);
		}
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_oper_servicio(){
		global $f;
		$filter = array();
		if(isset($this->params["ano"])){
			$filter["fecven"] =array(
				'$gte'=>new MongoDate(strtotime($this->params["ano"]."-01-01")),
				'$lt'=>new MongoDate(strtotime((floatval($this->params["ano"])+1)."-01-01"))
			);
		}
		$filter['operacion'] = $this->params['operacion'];
		$filter['servicio._id'] = $this->params['servicio'];
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_espacio(){
		global $f;
		$data = $this->db->find(array('espacio._id'=>$this->params['espacio']))->sort(array('servicio._id'));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_by_comp(){
		global $f;
		$data = $this->db->find(array('comprobantes'=>$this->params['comprobante']));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cobranza_dudosa(){
		global $f;
		$date = date("Y-m-d");
		$fecha = strtotime($date." -13 month");
		$data = $this->db->find(array('modulo'=>'IN','estado'=>'P','fecven'=>array('$lte'=>new MongoDate($fecha))));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_deudores(){
		global $f;
		$now = date("Y-m-d");
		$fec = new MongoDate(strtotime($now));
		$data = $this->db->find(array("fecven"=>array('$lt'=>$fec),"servicio.organizacion._id"=>$this->params["organizacion"],"estado"=>"P"));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		$data = $this->db->find($this->params['data']);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_custom_misa(){
		global $f;
		$filter = array(
			'tipo' => 'MISA',
		);
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
			$this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->db->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->params['data'];
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>