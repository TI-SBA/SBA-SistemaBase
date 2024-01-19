<?php
class Model_cm_hope extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->cm_hist_oper;
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
					'espacio.nomb',
					'ocupante',
					'propietario',
					'recibo'
				));
			}
		}
		if(isset($this->params['tipo'])) $criteria['tipo'] = $this->params['tipo'];
		if(isset($this->params['ini'])){
			if(isset($this->params['fin'])){
				$criteria['fecoper'] = array(
					'$gte'=>$this->params['ini'],
					'$lte'=>$this->params['fin']
				);
			}
		}
		$fields = array();
		$sort = array('fecoper'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		if(isset($this->params['page_rows']))
			$data = $this->db->find($criteria,$fields)->sort($sort)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->limit( $this->params['page_rows'] );
		else
			$data = $this->db->find($criteria,$fields)->sort($sort);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		if(isset($this->params['page_rows']))
			$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array(
					'espacio.nomb',
					'ocupante',
					'propietario',
					'recibo'
				));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_foto(){
		global $f;
		$this->items = $this->db->findOne(array('foto'=>$this->params['foto']));
	}
	protected function get_fec_num(){
		global $f;
		$this->items = $this->db->findOne(array('recibo'=>$this->params['recibo'],'fecoper'=>$this->params['fecoper']));
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$filter = array();
		if(isset($this->params["espacio"])) $filter['espacio._id'] = $this->params["espacio"];
		$data = $this->db->find($filter,$fields)->sort(array('fecoper'=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
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
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($this->params['_id']),
		);
		$this->db->remove($this->items);
	}
	protected function get_arreglar(){
		$data = $this->db->find(array('url_imagen'=>array('$type'=>2)));
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}	
	}
}
?>