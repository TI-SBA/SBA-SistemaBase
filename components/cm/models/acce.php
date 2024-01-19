<?php
class Model_cm_acce extends Model {
	private $acce;
	public $items;
	
	public function __construct() {
		global $f;
		$this->acce = $f->datastore->cm_accesorios;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->acce->findOne(array('_id'=>$this->params["_id"]));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
			}
		}
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->acce->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function save_datos(){
		global $f;
		$array = (array)$f->request->data;
		if(isset($f->request->data['_id'])){
			$array['_id'] = new MongoId($f->request->data['_id']);
			$this->acce->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else{
			$this->acce->insert($array);
			$this->obj = $array;
		}
	}
	protected function save_insert(){
		global $f;
		$this->acce->insert( $this->params['data'] );
		$this->obj = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->acce->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->params['data']['_id'] = $this->params['_id'];
		$this->obj = $this->params['data'];
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->acce->remove($this->items);
	}
	protected function get_search(){
		global $f;
		$i = 0;
		$parametro=$f->request->nomb;
		$criteria = array(
    		'nomb' => new MongoRegex('/^'.$parametro.'/i')
  		);
  		$criteria['estado'] = 'H';
		$data = $this->acce->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort( array('nomb'=>1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
  		$i = 0;
		$data = $this->acce->find();
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
	}
}
?>