<?php
class Model_ac_log extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ac_log;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array(
					'entidad.fullname',
					'entidad.cargo.organizacion.nomb',
					'bandeja',
					'descr',
					'usuario.userid'
				));
			}
		}
		if(isset($this->params['usuario'])){
			$criteria['entidad._id'] = $this->params['usuario'];
		}
		if(isset($this->params["filter"])){
			foreach ($this->params['filter'] as $i=>$filter){
				$criteria[$filter['nomb']] = $filter['value'];
			}
		}
		$fields = array();
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('entidad.nomb','entidad.appat','entidad.apmat','bandeja','descr'));
		}else $criteria = array();
		//$criteria['estado'] = $this->params['estado'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('fecreg'=>-1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$filter = array();
		if(isset($this->params["modulo"]))$filter["modulo"] =  $this->params["modulo"];
		if(isset($this->params["desde"]))
			$filter["fecreg"]['$gte'] = new MongoDate(strtotime($this->params["desde"]));
		if(isset($this->params["hasta"]))
			$filter["fecreg"]['$lte'] = new MongoDate(strtotime($this->params["hasta"].' +23 hours + 59 minutes'));
		if(isset($this->params["trabajador"]))$filter["entidad._id"] = new MongoId($this->params["trabajador"]);
		$data = $this->db->find($filter)->sort(array("fecreg"=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}   
	}
	protected function save_insert(){
		global $f;
		$data = $this->params;
		$data['fecreg'] = new MongoDate();
		$data['usuario'] = array(
			'_id'=>$f->session->user['_id'],
			'userid'=>$f->session->user['userid']
		);
		$ip = '0.0.0.0';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$data['ip']=$ip;
		if(isset($f->session->user['oficina']))
			$data['usuario']['oficina'] = $f->session->user['oficina'];
		$data['entidad'] = $f->session->userDBMin;
		$this->db->insert( $data );
	}
	protected function save_loggindown()
	{
		global $f;
		$data = $this->params;
		$data['fecreg'] = new MongoDate();
		$ip = '0.0.0.0';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$data['ip']=$ip;
		$this->db->insert( $data );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>