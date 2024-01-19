<?php
class Model_ac_task extends Model {
	private $db;
	private $task;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->task = $this->db->ac_tasks;
	}
	protected function get_lista(){
		global $f;
		$i = 0;
		$cursor = $this->task->find( array() );
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
		    $i++;
		}
	}
	protected function get_one(){
		global $f;
		$this->items = $this->task->findOne( array('_id'=>new MongoId($this->params['_id'])));
	}
	protected function save_datos(){
		global $f;
		$array = (array)$f->request->data;
		if(isset($f->request->data['_id'])){
			$array['_id'] = new MongoId($array['_id']);
			$this->task->update(array( '_id' => $array['_id'] ),$array);
		}else
			$this->task->insert($array);
		$this->obj = $array;
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->task->remove($this->items);
	}
}
?>