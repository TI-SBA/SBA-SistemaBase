<?php
class Model_pe_asis extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pe_asistencia;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_trab(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti']);
		$fields = array();
		$order = array('programado.inicio'=>1);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_trab_horario(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti'],'programado'=>array('$exists'=>true));
		$fields = array();
		$order = array('programado.inicio'=>1);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_trab_hor(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti']);
		$fields = array();
		$order = array('programado.inicio'=>1);
		if(isset($this->params['start'])){
			$filter['programado.inicio'] = array('$gte'=>$this->params['start'],'$lte'=>$this->params['end']);
			$filter['programado.fin'] = array('$gte'=>$this->params['start'],'$lte'=>$this->params['end']);
		}
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_trab_day(){
		global $f;
		$filter = array(
			'trabajador._id'=>$this->params['trab'],
			'ejecutado'=>array('$exists'=>false),
			'programado.inicio'=>array(
				'$gt'=>new MongoDate(strtotime($this->params['day'])),
				'$lt'=>new MongoDate(strtotime($this->params['day']." +1 day"))
			)
		);
		$fields = array();
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_trab_day_all(){
		global $f;
		$fields = array();
		$filter = array(
			'trabajador._id'=>$this->params['trab'],
			'programado.inicio'=>array(
				'$gt'=>new MongoDate(strtotime($this->params['day'])),
				'$lt'=>new MongoDate(strtotime($this->params['day']." +1 day"))
			)
		);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$filter = array(
			'trabajador._id'=>$this->params['trab'],
			'ejecutado.entrada.fecreg'=>array(
				'$gt'=>new MongoDate(strtotime($this->params['day'])),
				'$lt'=>new MongoDate(strtotime($this->params['day']." +1 day"))
			)
		);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_bole(){
		global $f;
		$filter = array(
			'trabajador._id'=>$this->params['enti'],
			'$or'=>array(
				array('programado.inicio'=>array(
					'$gt'=>$this->params['fecini'],
					'$lt'=>$this->params['fecfin']
				)),
				array('ejecutado.entrada.fecreg'=>array(
					'$gt'=>$this->params['fecini'],
					'$lt'=>$this->params['fecfin']
				))
			)
		);
		$fields = array();
		$order = array('programado.inicio'=>1,'ejecutado.entrada'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_marc(){
		global $f;
		//$filter = array('trabajador._id'=>$this->params['enti'],'ejecutado'=>array('$exists'=>true));
		$filter = array('trabajador._id'=>$this->params['enti']);
		if(isset($this->params['ini'])){
			$filter['ejecutado.entrada.fecreg'] = array(
					'$gt'=>$this->params['ini'],
					'$lt'=>$this->params['fin']
			);
		}
		$fields = array();
		$order = array('ejecutado.entrada.fecreg'=>1,'programado.inicio'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_marc_periodo(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti'],
			'$or'=>array(
				array('ejecutado.entrada.fecreg'=>array(
					'$gt'=>$this->params['ini'],
					'$lt'=>$this->params['fin']
				)),
				array('programado.inicio'=>array(
					'$gt'=>$this->params['ini'],
					'$lt'=>$this->params['fin']
				))
			)
		);
		//$filter = array('trabajador._id'=>$this->params['enti'],'ejecutado'=>array('$exists'=>true));
		$fields = array();
		$order = array('ejecutado.entrada.fecreg'=>1,'programado.inicio'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_inci(){
		global $f;
		$filter = array('trabajador._id'=>$this->params['enti'],'ejecutado.entrada'=>array('$exists'=>true),'ejecutado.salida'=>array('$exists'=>true));
		$fields = array();
		$order = array('programado.inicio'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('abrev'=>1);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev','salario'));
		}else $criteria = array();
		$criteria['estado'] = $this->params['estado'];
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
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$array = $this->params['data'];
		$this->db->insert( $array );
		$this->obj = $array;
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->obj = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function delete_data(){
		global $f;
		$this->db->remove( array('_id'=>$this->params['_id']) );
	}
}
?>