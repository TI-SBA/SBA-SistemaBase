<?php
class Model_dd_redo extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->dd_recepcion_documentaria;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_nro(){
		global $f;
		$data = $this->db->find(array())->sort(array('nro'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	
	protected function get_registro(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('nro'=>1,'titu'=>1,'cant'=>1,'remi'=>1,'dire'=>1,'tise'=>1,'tipo_'=>1,'ubic'=>1,'obse'=>1,'fecreg'=>1,'year'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
				
	}
	protected function get_num_string(){
		$data = $this->db->find(array('nro'=>array('$type'=>2)));
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}	
	}
	protected function get_num_string_2(){
		$data = $this->db->find(array('cant'=>array('$type'=>2)));
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
						array('nro'=>floatval($this->params['texto'])),
						array('titu'=>new MongoRegex('/'.$parametro.'/i')),
						array('remi'=>new MongoRegex('/'.$parametro.'/i'))
						)
				);
			}
		}
		$sort = array('_id'=>-1);
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
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$data = $this->db->find(array(),$fields);
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
	protected function delete_redo(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>