<?php
class Model_ad_paho extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->mh_pacientes_hospitalizados;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
		
	}
	protected function get_ingreso(){
		global $f;
		$filter = array();
		if(isset($this->params)){
			$filter = $this->params;
		}
		$data = $this->db->find($filter)->sort(array('ning'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_alta(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		//$data = $this->db->find($filter, array('hist_cli'=>1,'paciente'=>1,'cie10'=>1,'diag'=>1,'cate'=>1,'tipo_hosp'=>1,'cant'=>1,'monto'=>1,'pabellon'=>1,'fec_inicio'=>1,'fec_fin'=>1,'sala'=>1,'autoriza'=>1,'fec_alta'=>1,'cate'=>1,'talta'=>1));
		$obj = $this->db->findOne($filter, array('hist_cli'=>1,'paciente'=>1,'cie10'=>1,'diag'=>1,'cate'=>1,'tipo_hosp'=>1,'cant'=>1,'monto'=>1,'pabellon'=>1,'fec_inicio'=>1,'fec_fin'=>1,'sala'=>1,'autorizado'=>1,'fec_alta'=>1,'cate'=>1,'talta'=>1,'autor'=>1));
		//foreach ($data as $obj) {
		$this->items = $obj;
		//}
//		print_r($obj);
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				//$criteria = $helper->paramsSearch($this->params["texto"], array('paciente.nomb','paciente.appat','paciente.apmat','his_cli'));
				$criteria = array(
					'$or'=>array(
						array('hist_cli'=>floatval($this->params['texto'])),
						array('paciente.fullname'=>new MongoRegex('/'.$parametro.'/i')),
						array('paciente.nomb'=>new MongoRegex('/'.$parametro.'/i')),
						array('paciente.appat'=>new MongoRegex('/'.$parametro.'/i')),
						array('paciente.apmat'=>new MongoRegex('/'.$parametro.'/i'))
					)
				);
			}
		}
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all2(){
		global $f;
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$filter = array();
		if(isset($this->params['ini'])){
			$filter = array(
				'$or'=>array(
					array('fec_alta'=>array(
						'$gte'=>new MongoDate(strtotime($this->params['ini'])),
						'$lte'=>new MongoDate(strtotime($this->params['fin']))
					)),
					array('fec_alta'=>array(
						'$gte'=>new MongoDate(strtotime($this->params['ini'])),
						'$lte'=>new MongoDate(strtotime($this->params['fin']))
					))
				)
			);
		}
		if(isset($this->params['altas'])){
			if($this->params['altas']==true)
				$filter['fec_alta'] = array('$exists'=>true);
			else
				$filter['fec_alta'] = array('$exists'=>false);
		}
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all(){
		global $f;
		$fields = array();
		$filter = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		$data = $this->db->find($filter,$fields);
		if(isset($this->params['limit'])) $data->limit($this->params['limit']);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_codigo(){
		global $f;
		$data = $this->db->find()->sort(array('his_cli'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
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
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function delete_paho(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}


}
?>