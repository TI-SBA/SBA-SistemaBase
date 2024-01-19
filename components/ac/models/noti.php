<?php
class Model_ac_noti extends Model {
	private $db;
	private $noti;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->noti = $this->db->ac_notifications;
	}
	protected function get_last(){
		global $f;
		$i = 0;
		$fields = array( "_id" );
		//$filter = array( "user._id"=>$this->params['_id'] , "sended"=>false );
		$filter = array( "organizacion._id"=>$this->params['_id'] , "sended"=>false );
		$cursor = $this->noti->find( $filter , $fields );
		$this->count = $cursor->count();
	}
	protected function get_lista(){
		global $f;
		/*
		$this->noti->insert( array("fecreg"=>new MongoDate(),"message"=>"Notificacion 4","readed"=>false,"sended"=>false,"user"=>array("_id"=>$f->session->user['_id'],"userid"=>$f->session->user['userid'])) );
		$this->noti->insert( array("fecreg"=>new MongoDate(),"message"=>"Notificacion 5","readed"=>false,"sended"=>false,"user"=>array("_id"=>$f->session->user['_id'],"userid"=>$f->session->user['userid'])) );
		$this->noti->insert( array("fecreg"=>new MongoDate(),"message"=>"Notificacion 6","readed"=>false,"sended"=>false,"user"=>array("_id"=>$f->session->user['_id'],"userid"=>$f->session->user['userid'])) );
		*/
		
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('message'));
		}else $criteria = array();
		$i = 0;
		$criteria["organizacion._id"] = $this->params['_id'];
		$fields = array(  );
		$sort = array ( "fecreg" => -1);
		$cursor = $this->noti->find($criteria,$fields)->skip($this->params['page_rows']*($this->params['page']-1))->sort($sort)->limit($this->params['page_rows']);
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function save_one(){
		global $f;
		$array = (array)$this->params['data'];
		//print_r($array);die();
		$this->noti->insert($array);
	}
	protected function save_sended(){
		global $f;
		$filter = array( "organizacion._id"=>$this->params['_id'] , "sended"=>false );
		$this->noti->update( $filter , array('$set'=>array("sended"=>true)) , array("multiple" => true) );
	}
	protected function save_readed(){
		global $f;
		$filter = array( "organizacion._id"=>$this->params['_id'] , "readed"=>false );
		$this->noti->update( $filter , array('$set'=>array("readed"=>true)) , array("multiple" => true) );
	}
}
?>