<?php
class Model_ac_user extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ac_users;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_validar(){
		global $f;
		$count = $this->db->find( array('userid'=>$this->params['userid']) )->count();
		if($count==0) $this->items = array( 'msj'=>true );
		else $this->items = array( 'msj'=>false );
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('userid','owner.fullname'));
			}
		}
		$sort = array('userid'=>1);
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
		$filter = array();
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		if(isset($this->params['online'])){
			$filter['online'] = $this->params['online'];
		}
		$data = $this->db->find($filter,$fields);
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	protected function get_login(){
		global $f;
		$this->items = $this->db->findOne( array('userid'=>$this->params['userid'] , 'passwd'=>sha1($this->params['passwd'])) );
	}
	protected function get_permisos(){
		global $f;
		$model = $f->datastore->ac_groups->find( array('members.userid'=>$this->params['userid'] ) );
		foreach($model as $obj){
			$this->items = array_merge((array)$obj['allowed'],(array)$this->items);
		}
	}
}
?>