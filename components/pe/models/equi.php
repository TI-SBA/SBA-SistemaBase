<?php
class Model_pe_equi extends Model {
	private $equipo;
	public $items;
	
	public function __construct() {
		global $f;
		$this->equipo = $f->datastore->pe_equipo;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->equipo->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('cod','descr'));
			}
		}
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->equipo->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->equipo->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cod(){
		global $f;
		$this->items = $this->equipo->findOne(array('cod'=>$this->params['cod']));
	}
	protected function save_insert(){
		global $f;
		$this->equipo->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->equipo->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
}
?>