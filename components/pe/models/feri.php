<?php
class Model_pe_feri extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->pe_feriados;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_periodo(){
		global $f;
		$filter = array('fec'=>array(
  			'$gte'=>new MongoDate(strtotime($this->params['year']."-01-01")),
  			'$lt'=>new MongoDate(strtotime(((int)$this->params['year']+1)."-01-01"))
		));
		$data = $this->db->find($filter);
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
		$params = array();
		if(isset($this->params['start'])){
			$params['fec'] = array(
				'$gte'=>$this->params['start'],
				'$lte'=>$this->params['end']
			);
		}
		$data = $this->db->find($params)->sort(array('fec'=>1));
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
	protected function delete_year(){
		global $f;
		$this->db->remove( array('fec'=>array(
			'$gte'=>new MongoDate(strtotime($this->params['year']."-01-01")),
  			'$lt'=>new MongoDate(strtotime(((int)$this->params['year']+1)."-01-01"))
		)) );
	}
}
?>