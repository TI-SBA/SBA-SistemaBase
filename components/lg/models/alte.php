<?php
class Model_lg_alte extends Model {
	private $alte;
	public $items;

	public function __construct() {
		global $f;
		$this->alte = $f->datastore->lg_log_patrimonio;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->alte->findOne(array('_id'=>$this->params['_id']));
	}

	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('nomb'=>1);
		$data = $this->alte->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}


	protected function save_insert(){
		global $f;
		$this->alte->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->alte->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
}
?>
