<?php
class Model_pr_estr extends Model {
	private $estr;
	public $items;
	
	public function __construct() {
		global $f;
		$this->estr = $f->datastore->pr_estruc_func;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->estr->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array('nivel'=>'FN');
		$fields = array();
		$order = array('cod'=>1);
		$data = $this->estr->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		    if($obj['programas']){
		    		for($i = 0; $i < count($obj['programas']); $i++){
						$ref = array(
						   '$ref' => 'pr_estruc_func',
						   '$id' => $obj['programas'][$i]['id']
						 );
						$temp[$i] = $f->datastore->getDBRef($ref);
						$this->items[] = $temp[$i];
						if($temp[$i]['subprogramas']){
							for($e = 0; $e < count($temp[$i]['subprogramas']); $e++){
							$ref2 = array(
						   	'$ref' => 'pr_estruc_func',
						   	'$id' => $temp[$i]['subprogramas'][$e]['id']
						 	);
							$temp2[$e] = $f->datastore->getDBRef($ref2);
							$this->items[] = $temp2[$e];											
							}	
						}	
						//$this->items[$i]->nomb = $temp[$i]['nomb'];
						//$this->items[$i]->sigla = $temp[$i]['sigla'];
					}	
		    }
		}
		$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->estr->find($criteria,$fields)->sort( array('cod'=>1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_searchsubprog(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = array();
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
		}else $criteria = array();
		$criteria["nivel"]="SP";
		$fields = array();
		$cursor = $this->estr->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		if(isset($this->params['nivel'])) $filter['nivel'] = $this->params['nivel'];
		else $filter = array();
		$data = $this->estr->find($filter,$fields);
		foreach($data as $ob){
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->estr->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->estr->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_push(){
		global $f;
		$this->estr->update( array('_id'=>$this->params['_id']) , array('$push'=>$this->params['data']) );
	}
	protected function delete_estr(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->estr->remove($this->items);
	}
	protected function save_pull_subprog(){
		global $f;
		$this->estr->update( array('_id'=>new MongoId($this->params['amodificar'])) , array('$pull'=>array('subprogramas'=>array('id'=>new MongoId($this->params['aeliminar'])))));
	}
	protected function save_pull_prog(){
		global $f;
		$this->estr->update( array('_id'=>new MongoId($this->params['amodificar'])) , array('$pull'=>array('programas'=>array('id'=>new MongoId($this->params['aeliminar'])))));
	}
}
?>