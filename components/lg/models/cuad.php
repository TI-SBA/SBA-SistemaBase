<?php
class Model_lg_cuad extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->lg_necesidades;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_orga(){
		global $f;
		$filter = array('organizacion._id'=>$this->params['_id']);
		if(!isset($this->params['todo']))
			$filter['vigente'] = true;
		//$this->items = $this->db->findOne($filter);
		$data= $this->db->find($filter)->sort(array("_id"=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_lista(){
		global $f;
		$filter = array('organizacion._id'=>$f->session->enti['roles']['trabajador']['oficina']['_id']);
		$fields = array('periodo'=>true,'trabajador'=>true,'fecreg'=>true,'estado'=>true,'vigente'=>true);
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_all(){
		global $f;
		$filter = array('estado'=>array('$ne'=>'P'),'periodo'=>$this->params['periodo']);
		$fields = array('organizacion'=>true,'periodo'=>true,'trabajador'=>true,'fecreg'=>true,'estado'=>true,'fecvig'=>true,'vigente'=>true);
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_cons(){
		global $f;
		//$filter = array('estado'=>'A','periodo'=>$this->params['periodo'],'vigente'=>true);
		$filter = array('estado'=>'A','periodo'=>$this->params['periodo']);
		$fields = array('organizacion'=>true,'totales_clasif'=>true,'precio_total'=>true);
		$order = array('fecreg'=>1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','abrev'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->db->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
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
	}
	protected function save_custom(){
		global $f;
		$this->db->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
	protected function save_reset_vig(){
		global $f;
		$this->db->update( array(
			'organizacion._id'=>$this->params['orga']
		) , array('$set'=>array('vigente'=>false)) , array("multiple" => true) );
	}
	protected function save_dec(){
		global $f;
		$this->db->update( array('_id'=>$this->params['_id']) , array('$inc'=>array($this->params['field']=>-$this->params['cant'])) );
	}
}
?>