<?php
class Model_ct_pcon extends Model {
	private $cuenta;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->cuenta = $f->datastore->ct_cuenta;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->cuenta->findOne(array('_id'=>$this->params['_id']));
	}
	/*protected function get_lista(){
		global $f;
		if($this->params["id"]==null){
			if(isset($this->params['tipo'])){
				$tipo = $this->params['tipo'];
			}else{
				$tipo = array('$exists' => true);
			}
			$filter = array('cuentas.padre' => array('$exists' => false),"tipo"=>$tipo);		
			$fields = array();
			$order = array('cod'=>1);
			$data = $this->cuenta->find($filter,$fields)->sort($order);
			foreach ($data as $obj) {
			    $this->items[] = $obj;
			    if(isset($obj['cuentas'])){
				if(isset($obj['cuentas']['hijos'])){
			    		for($i = 0; $i < count($obj['cuentas']['hijos']); $i++){
						$this->get_children($obj['cuentas']['hijos'][$i]['id']);
					}
				}
			    }
			}			
		}else{
			$filter = array('_id' => new MongoId($this->params["id"]));
			if(isset($this->params['tipo'])) $filter['tipo'] = $this->params['tipo'];
			$fields = array();
			$order = array('cod'=>1);
			$data = $this->cuenta->find($filter,$fields)->sort($order);
			foreach ($data as $obj) {
			    $this->items[] = $obj;	    
				if($obj['cuentas']['hijos']){
		    		for($i = 0; $i < count($obj['cuentas']['hijos']); $i++){
						$this->get_children($obj['cuentas']['hijos'][$i]['id']);
					}
			    }
			}
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
	}*/
		protected function get_lista(){
		global $f;
		if (!isset($this->params["id"])) {
			if(isset($this->params['tipo'])){
				$tipo = $this->params['tipo'];
			}else{
				$tipo = array('$exists' => true);
			}
			$filter = array('cuentas.padre' => array('$exists' => false),"tipo"=>$tipo,'estado'=>array('$ne' => 'D'));		
			$fields = array();
			$order = array('cod'=>1);
			$data = $this->cuenta->find($filter,$fields)->sort($order);
			foreach ($data as $obj) {
				//$this->items[] = $obj;
				if(!is_null($obj)){
			    	$this->items[] = $obj;
				}
			    if(isset($obj['cuentas'])){
					if(isset($obj['cuentas']['hijos'])){
				    	for($i = 0; $i < count($obj['cuentas']['hijos']); $i++){
			    			$this->get_children($obj['cuentas']['hijos'][$i]['id']);
						}
					}
			    }
			}
		} else {
			if($this->params["id"]==null){
				if(isset($this->params['tipo'])){
					$tipo = $this->params['tipo'];
				}else{
					$tipo = array('$exists' => true);
				}
				$filter = array('cuentas.padre' => array('$exists' => false),"tipo"=>$tipo,'estado'=>array('$ne' => 'D'));		
				$fields = array();
				$order = array('cod'=>1);
				$data = $this->cuenta->find($filter,$fields)->sort($order);
				foreach ($data as $obj) {
					//$this->items[] = $obj;
					if(!is_null($obj)){
				    	$this->items[] = $obj;
					}
				    if(isset($obj['cuentas'])){
						if(isset($obj['cuentas']['hijos'])){
					    	for($i = 0; $i < count($obj['cuentas']['hijos']); $i++){
				    			$this->get_children($obj['cuentas']['hijos'][$i]['id']);
							}
						}
				    }
				}			
			}else{
				$filter = array('_id' => new MongoId($this->params["id"]));
				if(isset($this->params['tipo'])) $filter['tipo'] = $this->params['tipo'];
				$fields = array();
				$order = array('cod'=>1);
				$data = $this->cuenta->find($filter,$fields)->sort($order);
				foreach ($data as $obj) {
				    //$this->items[] = $obj;
					if(!is_null($obj)){
				    	$this->items[] = $obj;
					}    
					if($obj['cuentas']['hijos']){
			    		for($i = 0; $i < count($obj['cuentas']['hijos']); $i++){
							$this->get_children($obj['cuentas']['hijos'][$i]['id']);
						}
				    }
				}
			}
		}
		
		
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
	}
	protected function get_lista2(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f-> library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('cod','descr'));
			}
		}
		$data = $this->cuenta->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	private function get_children($parent_id){
		global $f;
		$data2 = $this->cuenta->find(array( '_id' => new MongoId($parent_id)),array())->sort(array('cod'=>1));
		foreach ($data2 as $obj2) {
			//$this->items[] = $obj2;	
			if(!is_null($obj2)){
			    	$this->items[] = $obj2;
			}  
			if(isset($obj2['cuentas']['hijos'])){
		    		for($i = 0; $i < count($obj2['cuentas']['hijos']); $i++){
		    			$ref = array(
					   '$ref' => 'ct_cuenta',
					   '$id' => $obj2['cuentas']['hijos'][$i]['id']
					 );
					$temp[$i] = $f->datastore->getDBRef($ref);
					if(!is_null($temp[$i])){
			    		$this->items[] = $temp[$i];
					}  
					//$this->items[] = $temp[$i];
					if(isset($temp[$i]["cuentas"]["hijos"])){
						for($e = 0; $e < count($temp[$i]["cuentas"]["hijos"]); $e++){
							$this->get_children($temp[$i]["cuentas"]["hijos"][$e]["id"]);
						}
					}
				}
		    }
		}
	}
	protected function get_search(){
		global $f;
		$criteria = array();
		if(isset($this->params["texto"])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('cod','descr'));
			}
		}
		if(isset($this->params['logistica']))
			$criteria['logistica'] = array('$exists'=>true);
		$sort = array('cod'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$fields = array();
		$cursor = $this->cuenta->find($criteria,$fields)->sort($sort)->limit( $this->params["page_rows"] )->skip( $this->params['page_rows'] * ($this->params['page']-1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_code(){
		global $f;
		$filter['cod']=$this->params['cod'];
		$filter['estado']=array('$ne'=>'D');
		$data = $this->cuenta->find($filter,array('cod'=>true));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cod(){
		global $f;
		$filter['cod']=$this->params['cod'];
		$filter['estado']=array('$ne'=>'D');
		$this->items = $this->cuenta->findOne($filter);
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array('cod'=>true,'descr'=>true);
		$data = $this->cuenta->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		$filter = $this->params['filter'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array('cod'=>true,'descr'=>true);
		$data = $this->cuenta->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->cuenta->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->cuenta->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_push(){
		global $f;
		$this->cuenta->update( array('_id'=>$this->params['_id']) , array('$push'=>array('cuentas.hijos' => $this->params['data'])) );
	}
	protected function delete_clas(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($this->params['_id']),
		);
		$this->cuenta->remove($this->items);
	}
	protected function save_pull_clas(){
		global $f;
		$this->cuenta->update( array('_id'=>new MongoId($this->params['amodificar'])) , array('$pull'=>array('clasificadores.hijos'=>array('id'=>new MongoId($this->params['aeliminar'])))));
	}
}
?>