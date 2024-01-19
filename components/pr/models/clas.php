<?php
class Model_pr_clas extends Model {
	private $clasi;
	public $items;
	private $name = 'pr_clasificadores';
	
	public function __construct() {
		global $f;
		$this->clasi = $f->datastore->pr_clasificadores;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->clasi->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_node(){
		$filter = array();
		if(isset($this->params["nodeid"])){
			$filter = array('clasificadores.padre' => array('$exists' => false));
		}else{
			$filter = array('clasificadores.padre' => $this->params['nodeid']);
		}
		$fields = array();
		$order = array('cod'=>1);
		$data = $this->clasi->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;	
		}
	}
	/*protected function get_lista(){
		global $f;
		if($this->params["id"]==null){
			$filter = array('clasificadores.padre' => array('$exists' => false));
			if(isset($this->params["tipo"]))$filter['tipo'] = $this->params["tipo"];
			//else $filter['tipo']="I";
			if(isset($this->params["estado"]))$filter['estado'] = $this->params["estado"];
			$fields = array();
			$order = array('cod'=>1);
			$data = $this->clasi->find($filter,$fields)->sort($order);
			foreach ($data as $obj) {
			    $this->items[] = $obj;	    
				if($obj['clasificadores']['hijos']){
			    		for($i = 0; $i < count($obj['clasificadores']['hijos']); $i++){
							$this->get_children($obj['clasificadores']['hijos'][$i]['id'],$filter['estado']);
						}
			    }
			}
		}else{
			$filter = array('_id' => new MongoId($this->params["id"]));
			if(isset($this->params["tipo"]))$filter['tipo'] = $this->params["tipo"];
			//else $filter['tipo']="I";
			if(isset($this->params["estado"]))$filter['estado'] = $this->params["estado"];
			$fields = array();
			$order = array('cod'=>1);
			$data = $this->clasi->find($filter,$fields)->sort($order);
			foreach ($data as $obj) {
			    $this->items[] = $obj;	    
				if($obj['clasificadores']['hijos']){
			    		for($i = 0; $i < count($obj['clasificadores']['hijos']); $i++){
							$this->get_children($obj['clasificadores']['hijos'][$i]['id'],$filter['estado']);
						}
			    }
			}
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
	}*/
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
			}
		}
		if(isset($this->params['tipo']))
			$criteria['tipo'] = $this->params['tipo'];
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		if(isset($this->params['id'])){
			if($this->params['id']!=''){
				$criteria['_id'] = new MongoId($this->params["id"]);	
			}
		}else{
			$criteria['clasificadores.padre'] = array('$exists' => false);
		}
		$sort = array('cod'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->clasi->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	private function get_children($parent_id,$estado){
		global $f;
		if($estado!=null){
			$est = $estado;
		}else{
			$est = array('$exists'=>true);
		}
		$data2 = $this->clasi->find(array( '_id' => new MongoId($parent_id),"estado"=>$est),array())->sort(array('cod'=>1));
		foreach ($data2 as $obj2) {
		    $this->items[] = $obj2;	
			if(isset($obj2['clasificadores']['hijos'])){
		    		for($i = 0; $i < count($obj2['clasificadores']['hijos']); $i++){		    			
						$this->get_children($obj2['clasificadores']['hijos'][$i]["id"],$estado);						
					}
		    }
		}
	}
	protected function get_search(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				/*$f->library('helpers');
				$helper=new helper();
				$cod = $this->params["texto"];
				$criteria["cod"] = new MongoRegex("/^".$cod."/");*/
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod'));
			}
		}
		if(isset($this->params['tipo'])){
			if($this->params["tipo"]!=''){
				$criteria["tipo"] = $this->params["tipo"];
			}
		}
		$fields = array();
		$cursor = $this->clasi->find($criteria,$fields)->limit( $this->params["page_rows"] );
		foreach($cursor as $obj){
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array('cod'=>true,'nomb'=>true);
		$filter = array();
		if(isset($this->params['filter'])) $filter = $this->params['filter'];


		$data = $this->clasi->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->clasi->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->clasi->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_push(){
		global $f;
		$this->clasi->update( array('_id'=>$this->params['_id']) , array('$push'=>array('clasificadores.hijos' => $this->params['data'])) );
	}
	protected function delete_clas(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($this->params['_id']),
		);
		$this->clasi->remove($this->items);
	}
	protected function save_pull_clas(){
		global $f;
		$this->clasi->update( array('_id'=>new MongoId($this->params['amodificar'])) , array('$pull'=>array('clasificadores.hijos'=>array('id'=>new MongoId($this->params['aeliminar'])))));
	}
}
?>