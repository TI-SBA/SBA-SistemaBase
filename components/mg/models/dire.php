<?php
class Model_mg_dire extends Model {
	private $dire;
	public $items;
	
	public function __construct() {
		global $f;
		$this->dire = $f->datastore->mg_directorios;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->dire->findOne(array("_id"=>$this->params["_id"]));		
	}
	protected function get_oper(){
		global $f;
		$this->items = $this->dire->findOne(array("operacion"=>$this->params["oper"]));		
	}
	protected function get_lista(){
		global $f;
  		if(!isset($this->params['dir'])){
			$tmp = $f->datastore->mg_directorios->findOne(array('descr'=>'/'));
			$this->params['dir'] = $tmp['_id'];
  		}
		$criteria = array ( "dir" => $this->params['dir'] );
		$data = $this->dire->find($criteria)->sort(array('descr'=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_data(){
		global $f;
		if(!isset($this->params['dir'])){
			$tmp = $f->datastore->mg_directorios->findOne(array('descr'=>'/'));
			$this->params['dir'] = $tmp['_id'];
		}
		$data = array(
			'descr'=>$this->params['descr'],
			'dir'=>$this->params['dir']
		);
		if(isset($this->params['_id'])){
			$this->dire->update(array( '_id' => $this->params['_id'] ),array( '$set'=>$data ));
		}else
			$this->dire->insert($data);
		
	}
	protected function delete_item(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($this->params["_id"]),
		);
		$this->dire->remove($this->items);
	}
}
?>