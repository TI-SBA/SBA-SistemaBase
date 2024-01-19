<?php
class Model_pr_eprog extends Model {
	private $eprog;
	public $items;
	
	public function __construct() {
		global $f;
		$this->eprog = $f->datastore->pr_estruc_prog;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->eprog->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array('nivel'=>'CT');
		$fields = array();
		$order = array('cod'=>1);
		$data = $this->eprog->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		    if($obj['programas']){
		    		for($i = 0; $i < count($obj['programas']); $i++){
						$ref = array(
						   '$ref' => 'pr_estruc_prog',
						   '$id' => $obj['programas'][$i]['id']
						 );
						$temp[$i] = $f->datastore->getDBRef($ref);
						$this->items[] = $temp[$i];
						if($temp[$i]['proyectos']){
							for($e = 0; $e < count($temp[$i]['proyectos']); $e++){
							$ref2 = array(
						   	'$ref' => 'pr_estruc_prog',
						   	'$id' => $temp[$i]['proyectos'][$e]['id']
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
		$cursor = $this->eprog->find($criteria,$fields)->sort( array('nomb'=>1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_searchproy(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = array();
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
		}else $criteria = array();
		$criteria["nivel"]="PY";
		$fields = array();
		$cursor = $this->eprog->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array('cod'=>true,'nomb'=>true);
		if(isset($this->params['nivel'])) $filter['nivel'] = $this->params['nivel'];
		else $filter = array();
		$data = $this->eprog->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->eprog->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->eprog->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_push(){
		global $f;
		$this->eprog->update( array('_id'=>$this->params['_id']) , array('$push'=>$this->params['data']) );
	}
	protected function delete_eprog(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->eprog->remove($this->items);
	}
	protected function save_pull_proy(){
		global $f;
		$this->eprog->update( array('_id'=>new MongoId($this->params['amodificar'])) , array('$pull'=>array('proyectos'=>array('id'=>new MongoId($this->params['aeliminar'])))));
	}
	protected function save_pull_prog(){
		global $f;
		$this->eprog->update( array('_id'=>new MongoId($this->params['amodificar'])) , array('$pull'=>array('programas'=>array('id'=>new MongoId($this->params['aeliminar'])))));
	}
}
?>