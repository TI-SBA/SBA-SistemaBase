<?php
class Model_cj_rein extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->cj_recibos_ingresos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$filter = $helper->paramsSearch($this->params["texto"], array('organizacion.nomb','cliente.nomb','cliente.appat','cliente.apmat'));
			}
		}
		if(isset($this->params['por'])){
			if($this->params['por']=='true'){
				$filter['organizacion._id'] = $f->session->enti['roles']['trabajador']['organizacion']['_id'];
			}
		}
		if(isset($this->params['organizacion'])){
			$filter['organizacion._id'] = $this->params['organizacion'];
		}
		if(isset($this->params['modulo'])){
			$filter['modulo'] = $this->params['modulo'];
		}
		if(isset($this->params['tipo_inm'])){
			$filter['tipo_inm'] = $this->params['tipo_inm'];
		}
		$fields = array();
		if(isset($this->params['fields'])){
			$fields = $this->params['fields'];
		}
		$order = array('fec'=>-1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_2(){
		global $f;
		$filter = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$filter = $helper->paramsSearch($this->params["texto"], array('organizacion.nomb','cliente.nomb','cliente.appat','cliente.apmat'));
			}
		}
		if(isset($this->params['por'])){
			if($this->params['por']=='true'){
				$filter['organizacion._id'] = $f->session->enti['roles']['trabajador']['organizacion']['_id'];
			}
		}
		if(isset($this->params['organizacion'])){
			$filter['organizacion._id'] = $this->params['organizacion'];
		}
		if(isset($this->params['modulo'])){
			$filter['modulo'] = $this->params['modulo'];
		}else{
			$filter["modulo"] = array('$in'=>array("LM","AD","MH","TD"));
		}
		if(isset($this->params['tipo_inm'])){
			$filter['tipo_inm'] = $this->params['tipo_inm'];
		}
		$fields = array();
		if(isset($this->params['fields'])){
			$fields = $this->params['fields'];
		}
		$order = array('fec'=>-1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('organizacion.nomb','cliente.nomb','cliente.appat','cliente.apmat'));
		}else $criteria = array();
		$criteria['tipo'] = $this->params['tipo'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('fec'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		else $filter = array();
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all_ingreso(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('modulo'=>1,'cod'=>1,'tipo'=>1,'fec'=>1,'fecfin'=>1,'detalle'=>1,'glosa'=>1,'cont_patrimonial'=>1,'total'=>1,'efectivos'=>1,'fuente'=>1,'fecreg'=>1,'estado'=>1,'autor'=>1,'comprobantes_anulados'=>1,'cod'=>1,'cod'=>1,'cod'=>1))->limit(300);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find(array(),array('cod'=>true))->sort(array('fec'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
		}
	}
	protected function get_planilla(){
		global $f;
		$cursor = $this->db->find(array('organizacion._id'=>new MongoId('51a50edc4d4a13441100000e')),array('planilla'=>true))->sort(array('fec'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['planilla'];
		}
	}
	protected function save_insert(){
		global $f;
		$item = null;
		if(isset($this->params['data']["organizacion"])){
			if($this->params['data']["organizacion"]['_id']==new MongoId('51a50f0f4d4a13c409000013')){
				/*CEMENTERIO*/
				$item = $this->db->findOne(array(
					'fec'=>$this->params['data']["fec"],
					'fecfin'=>$this->params['data']["fecfin"],
					'organizacion._id'=>$this->params['data']["organizacion"]['_id']
				));
			}elseif($this->params['data']["organizacion"]['_id']==new MongoId('51a50edc4d4a13441100000e')){
				/*INMUEBLES*/
				$item = $this->db->findOne(array(
					'planilla'=>$this->params['data']["planilla"],
					'organizacion._id'=>$this->params['data']["organizacion"]['_id']
				));
			}
		}elseif(isset($this->params['data']['modulo'])){
			$params = array(
				'fec'=>$this->params['data']["fec"],
				'fecfin'=>$this->params['data']["fecfin"],
				'modulo'=>$this->params['data']['modulo']
			);
			if($this->params['data']['modulo']=='IN'){
				$params['tipo_inm'] = $this->params['data']['tipo_inm'];
			}
			$item = $this->db->findOne($params);
		}
		if($item!=null){
			$this->db->update( array('_id'=>$item['_id']) , array('$set'=>$this->params['data']) );
			$this->params['data']['_id'] = $item['_id'];
		}else{
			$this->db->insert( $this->params['data'] );
		}
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		//unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->params['data'];
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>