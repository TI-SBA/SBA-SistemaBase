<?php
class Model_lg_orse extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->lg_ordenes_servicio;
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
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
			}
		}
		$sort = array('_id'=>-1);
		/*if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];*/
		if(isset($this->params['etapa']))
			switch($this->params['etapa']){
				case "SOL":
					$criteria['solicitud'] = array('$exists'=>true);
					if(isset($this->params['estado']))
						$criteria['solicitud.estado'] = $this->params['estado'];
					break;
				case "CER":
					$criteria['certificacion'] = array('$exists'=>true);
					if(isset($this->params['estado']))
						$criteria['certificacion.estado'] = $this->params['estado'];
					break;
				case "ORD":
					$criteria['orden'] = array('$exists'=>true);
					if(isset($this->params['estado']))
						$criteria['orden.estado'] = $this->params['estado'];
					break;
				case "ORS":
					$criteria['orden_servicio'] = array('$exists'=>true);
					if(isset($this->params['estado']))
						$criteria['orden_servicio.estado'] = $this->params['estado'];
					break;
				case "REC":
					$criteria['recepcion'] = array('$exists'=>true);
					if(isset($this->params['estado']))
						$criteria['recepcion.estado'] = $this->params['estado'];
					break;
			}
		if(isset($this->params['trabajador']))
			$criteria['trabajador._id'] = $this->params['trabajador'];
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find(array(),array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
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
	protected function save_push(){
		global $f;
		$this->db->update( array('_id'=>$this->params['_id']) , array('$push'=>array('revisiones'=>$this->params['data'])) );
	}
}
?>