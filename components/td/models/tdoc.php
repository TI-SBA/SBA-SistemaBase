<?php
class Model_td_tdoc extends Model {
	private $tdoc;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore;
		$this->tdoc = $this->db->td_tipos_documento;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->tdoc->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_by_nomb(){
		global $f;
		$this->items = $this->tdoc->findOne(array('nomb'=>$this->params['nomb']));
	}
	protected function get_lista(){
		global $f;
  		$i = 0;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb'));
		}else $criteria = array();
  		/*if(isset($f->request->data['filter']))
			$criteria = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$criteria = array ( "_id" => 1);*/
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->tdoc->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($sort)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function save_datos(){
		global $f;
		$array = (array)$f->request->data;
		if(isset($f->request->data['_id'])){
			$array['_id'] = new MongoId($f->request->data['_id']);
			$this->tdoc->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else
			$this->tdoc->insert($f->request->data);
	}
	protected function save_insert(){
		global $f;
		$this->tdoc->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->tdoc->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->tdoc->remove($this->items);
	}
	protected function get_search(){
		global $f;
		$i = 0;
		$parametro=$f->request->nomb;
		$criteria = array(
    		'nomb' => new MongoRegex('/^'.$parametro.'/i')
  		);
		$data = $this->tdoc->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->tdoc->find(array('estado'=>'H'))->sort(array('nomb'=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
}
?>