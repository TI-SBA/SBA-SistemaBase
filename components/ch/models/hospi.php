<?php
class Model_ch_hospi extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ho_hospitalizaciones;
	}
	protected function get_one(){
		global $f;
		if(isset($this->params['_id']))
			$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
		else
			$this->items = $this->db->findOne($this->params['filter']);
	}

	protected function get_one_entidad(){
		global $f;
		$this->items = $this->db->findOne(array('paciente._id'=>$this->params['_id']));

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
		if(isset($this->params['modulo']))
			$criteria['modulo'] = $this->params['modulo'];
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
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
	protected function delete_hospi(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}


}
?>