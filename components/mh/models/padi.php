<?php
class Model_mh_padi extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->mh_ParteDiario;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_num_string(){
		$data = $this->db->find(array('num'=>array('$type'=>2)));
		foreach ($data as $obj) {
			$this->items[] = $obj;
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
						array('num'=>floatval($this->params['texto'])),
						array('medico.nomb'=>new MongoRegex('/^'.$parametro.'/i')),
						array('medico.appat'=>new MongoRegex('/^'.$parametro.'/i')),
						array('medico.apmat'=>new MongoRegex('/^'.$parametro.'/i'))
					)
				);
			}
		}
		if(isset($this->params['modulo']))
			$criteria['modulo'] = $this->params['modulo'];
		$sort = array('fech'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_parte(){
		global $f;
		$data = $this->db->find(array())->sort(array('num'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_reporte(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'consulta'=>1,'modulo'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_reporte_pacientes(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'consulta'=>1))->limit(300);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_reporte_diagnostico(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'consulta'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_mensual(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'consulta'=>1,'medico'=>1,'modulo'=>1,'num'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_dash_(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'consulta'=>1,'medico'=>1,'modulo'=>1,'num'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_dash(){
		global $f;
		$fields = array();
		if(isset($this->params['fields']))
			$fields = $this->params['fields'];
		$data = $this->db->find($this->params['filter'],$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_cubanas(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'consulta'=>1,'medico'=>1,'modulo'=>1,'num'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_doctor(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('fech'=>1,'modulo'=>1,'medico'=>1,'consulta'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	
	protected function get_all(){
		global $f;
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$data = $this->db->find(array(),$fields);
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
	protected function delete_padi(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>