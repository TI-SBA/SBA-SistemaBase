<?php
class Model_pr_acti extends Model {
	private $acti;
	public $items;
	
	public function __construct() {
		global $f;
		$this->acti = $f->datastore->pr_actividades;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->acti->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array('nivel'=>'AC');
		$fields = array();
		$order = array('cod'=>1);
		$data = $this->acti->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		    if(isset($obj['componentes'])){
		    		for($i = 0; $i < count($obj['componentes']); $i++){
						$ref = array(
						   '$ref' => 'pr_actividades',
						   '$id' => $obj['componentes'][$i]['id']
						 );
						$temp[$i] = $f->datastore->getDBRef($ref);
						$this->items[] = $temp[$i];
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
		$criteria['nivel'] = 'AC';
		$fields = array();
		$cursor = $this->acti->find($criteria,$fields)->sort( array('cod'=>1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
			if(isset($obj['componentes'])){
		    		for($i = 0; $i < count($obj['componentes']); $i++){
						$ref = array(
						   '$ref' => 'pr_actividades',
						   '$id' => $obj['componentes'][$i]['id']
						 );
						$temp[$i] = $f->datastore->getDBRef($ref);
						$this->items[] = $temp[$i];
						//$this->items[$i]->nomb = $temp[$i]['nomb'];
						//$this->items[$i]->sigla = $temp[$i]['sigla'];
					}	
		    }
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$filter = array(
			'estado'=>array('$ne'=>'D')
		);
		if(isset($this->params['nivel'])) $filter['nivel'] = $this->params['nivel'];
		if(isset($this->params['actividad'])) $filter['actividad'] = $this->params['actividad'];
		$data = $this->acti->find($filter,$fields)->sort(array('actividad'=>1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->acti->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->acti->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_push(){
		global $f;
		$this->acti->update( array('_id'=>$this->params['_id']) , array('$push'=>$this->params['data']) );
	}
	protected function delete_acti(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($this->params['_id']),
		);
		$cursor = $this->acti->findOne($this->items);
		if($cursor['componentes']){
			for($i=0;$i<count($cursor['componentes']);$i++){
				$this->acti->remove(array('_id'=>$cursor['componentes'][$i]['id']));
			}
		}
		$this->acti->remove($this->items);
	}
	protected function delete_comp(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$cursor = $this->acti->findOne($this->items);
		$cursor2 = $this->acti->findOne(array('_id'=>new MongoId($cursor['actividad'])));
			/*$item = array();
			for($i=0;$i<count($cursor2['componentes']);$i++){				
				$item['componentes'][$i]['id'] = $cursor2['componentes'][$i]['id'];
				if($item['componentes'][$i]['id']==$f->request->id){
					unset($item['componentes'][$i]);
				}				
			}*/
		$cursor3 = $this->acti->update( array('_id'=>new MongoId($cursor['actividad'])) , array('$pull'=>array('componentes'=>array('id'=>new MongoId($f->request->id)))));
		$this->acti->remove($this->items);
	}
}
?>