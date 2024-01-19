<?php
class Model_ts_mocj extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ts_movimientos_caja_chica;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array('caja_chica._id'=>$this->params['caja'],'saldo'=>$this->params['num']);
		$fields = array();
		$order = array('fecreg'=>1);
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
			$criteria = $helper->paramsSearch($this->params["texto"], array('rubro','cod'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->db->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	/*protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->db->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}*/
	protected function get_all(){
		global $f;
		$filter = array('caja_chica._id'=>$this->params['caja'],'saldo'=>$this->params['num']);
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$order = array('cod'=>-1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all_saldo(){
		global $f;
		$filter = array('saldo'=>$this->params['saldo']);
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$order = array('cod'=>-1);
		$data = $this->db->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find(array(
			/*'caja_chica._id'=>$this->params['caja'],
			'documento'=>$this->params['doc'],*/
			'saldo'=>$this->params['saldo']
		))->sort(array('item'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['item'];
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
		$this->params['data']['_id'] = $this->params['_id'];
		$this->items = $this->params['data'];
	}
	protected function delete_fuen(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->db->remove($this->items);
	}
}
?>