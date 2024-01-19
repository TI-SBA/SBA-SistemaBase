<?php
class Model_pe_inci extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pe_incidencias;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
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
	protected function get_bole(){
		global $f;
		$filter = array(
			//'trabajador._id'=>$this->params['enti'],
			'$or'=>array(
				array('fecini'=>array(
					'$gte'=>$this->params['fecini'],
					'$lt'=>$this->params['fecfin']
				)),
				array('fecfin'=>array(
					'$gte'=>$this->params['fecini'],
					'$lt'=>$this->params['fecfin']
				))
			)
		);
		if(isset($this->params['enti'])){
			$filter['trabajador._id'] = $this->params['enti'];
		}
		$fields = array();
		$order = array('fecini'=>1,'fecfin'=>1);
		//$filter['programada'] = false;
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
		$data = $this->db->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_trab(){
		global $f;
		$data = $this->db->find(array('trabajador._id'=>$this->params['_id'],'programada'=>false));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_trab_periodo(){
		global $f;
		$data = $this->db->find(array('trabajador._id'=>$this->params['_id'],'programada'=>false,'fecini'=>array(
			'$gt'=>$this->params['ini'],
			'$lt'=>$this->params['fin']
		)));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$data = $this->params['data'];
		$data['programada'] = false;
		$this->db->insert( $data );
		$this->obj = $data;
	}
	protected function save_update(){
		global $f;
		$this->obj = $this->params['data'];
		$this->params['data']['programada'] = false;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
}
?>