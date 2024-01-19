<?php
class Model_pe_docs extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pe_documentos;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_custom(){
		global $f;
		$data = $this->db->find($this->params['filter'],$this->params['fields'])->sort(array('cod'=>-1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_custom_limit(){
		global $f;
		if(!isset($this->params['fields']))
			$this->params['fields'] = array();
		if(!isset($this->params['sort']))
			$this->params['sort'] = array();
		$data = $this->db->find($this->params['filter'],$this->params['fields'])->sort($this->params['sort'])->limit($this->params['limit']);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_lista(){
		global $f;
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		else $filter = array();
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$order = array('cod'=>-1);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('organizacion.nomb','trabajador.nomb','trabajador.appat','trabajador.apmat','salario'));
		}else $criteria = array();
		$criteria['estado'] = $this->params['estado'];
		$criteria[$this->params['doc']] = array('$exists'=>true);
		if(isset($this->params['bonificaciones_tipo']))
			$criteria['bonificaciones.tipo'] = $this->params['bonificaciones_tipo'];
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_all(){
		global $f;
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		else  $filter = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else  $fields = array();
		$order = array('cod'=>-1);
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
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
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find($this->params['filter'],array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
		}
	}
	protected function get_lista_bole_export(){
		global $f;
		$keys = array("trabajador.cargo.organizacion._id" => true);
		$initial["items"]= array();
		$initial["orga"]= array();
		$index = 0;
		$reduce = "function (doc, out)
					{
					out.orga.push(doc.trabajador.cargo.organizacion);
					out.items.push(doc);
					}
					";
		$condition = array(
			'boletas'=>array('$exists'=>true),
			'contrato.cod'=>$this->params["tipo"],
			'periodo.ano'=>$this->params["ano"],
			'periodo.mes'=>$this->params["mes"]
		);
		$data = $this->db->group($keys, $initial, $reduce, array('condition'=>$condition));
		foreach ($data["retval"] as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_bole_periodo_all(){
		global $f;
		$filter = array(
			//'boletas'=>array('$exists'=>true),
			'estado'=>array('$ne'=>'X'),
			'planilla.periodo.mes'=>$this->params['mes'],
			'planilla.periodo.ano'=>$this->params['ano']
		);
		if(isset($this->params["contrato"]))
			$filter['contrato.cod'] = $this->params["contrato"];
		/*if(isset($this->params["organizacion"]))
			$filter['trabajador.cargo.organizacion._id'] = $this->params["organizacion"];*/
		$data = $this->db->find($filter);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_insert(){
		global $f;
		$this->db->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_one(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
	protected function delete_custom(){
		$this->db->remove($this->params);
	}
}
?>