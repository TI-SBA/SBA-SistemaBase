<?php
class Model_ac_grup extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ac_groups;
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('groupid','descr'));
			}
		}
		$sort = array('groupid'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne( array('_id'=>new MongoId($this->params['_id'])));
	}
	protected function get_validar(){
		global $f;
		$count = $this->db->find( array('groupid'=>$this->params['groupid']) )->count();
		if($count==0) $this->obj = array( 'msj'=>true );
		else $this->obj = array( 'msj'=>false );
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
	protected function save_reset(){
		global $f;
		$this->db->update( array('members._id'=>$this->params['_id']) , array( '$pull' => array( 'members' => array('_id'=>$this->params['_id']) )  ),array("upsert" => true) );
		foreach($this->params['groups'] as $obj){
			$this->db->update( array('_id'=>$obj['_id']) , array( '$push'=>array( "members"=>array(
				"_id"=>$this->params['_id'],
				"userid"=>$this->params['userid']
			) ) ) );
		}
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->db->remove($this->items);
	}
	protected function get_all(){
		global $f;
		$i = 0;
		$fields = array( "_id"=>true , "groupid"=>true , "enabled"=>true , "descr"=>true );
		$cursor = $this->db->find( array() , $fields );
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
		    $i++;
		}
	}
}
?>