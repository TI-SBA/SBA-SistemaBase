<?php
class Model_mg_orga extends Model {
	private $orga;
	public $items;
	
	public function __construct() {
		global $f;
		$this->orga = $f->datastore->mg_organizaciones;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->orga->findOne( array('_id'=>$this->params['_id']));
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
		$data = $this->orga->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listanodos(){
		global $f;
		$i = 0;
		$va = $this->orga->find();
		if($va==null){
			$this->items = array();
		}
		else{
			if($f->request->ide != "0"){
				//Mostrar los hijos
				$criteria = array(
	    			'_id' => new MongoId($f->request->ide)
	  			);
		  		$data = $this->orga->findOne($criteria,array('organizaciones'=>true));
				if(isset($data['organizaciones']['hijos'])){
					for($i = 0; $i < count($data['organizaciones']['hijos']); $i++){
						$ref = array(
						   '$ref' => 'mg_organizaciones',
						   '$id' => $data['organizaciones']['hijos'][$i]
						 );
						$temp[$i] = $f->datastore->getDBRef($ref);
						$this->items[$i]->_id = $temp[$i]['_id'];
						$this->items[$i]->nomb = $temp[$i]['nomb'];
						$this->items[$i]->sigla = $temp[$i]['sigla'];
					}
				}
				else{
					$this->items = array();
				}
			}else{
				//Mostrar la Raiz
				foreach ($va as $ob) {
					if($ob['organizaciones'] == null){
						$this->items[$i]->_id = $ob['_id'];
						$this->items[$i]->nomb = $ob['nomb'];
						$this->items[$i]->sigla = $ob['sigla'];
						$i++;
					}
			  		else{
						if(isset($ob['organizaciones']['hijos'])&&!isset($ob['organizaciones']['padre'])){
							$this->items[$i]->_id = $ob['_id'];
							$this->items[$i]->nomb = $ob['nomb'];
							$this->items[$i]->sigla = $ob['sigla'];
							$i++;
						}
		  			}
				}
			}
		}
  		$this->paging($this->params["page"],$this->params["page_rows"],$i);
	}
	protected function get_all(){
		global $f;
  		$i = 0;
		$data = $this->orga->find()->sort(array('nomb'=>1));
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
	}
	protected function save_datos(){
		global $f;
		if(isset($f->request->data['_id'])){//Actualizacion
			$array = (array)$f->request->data['data'];
			if(isset($f->request->data['data']['actividad']['_id']))
				$array['actividad']['_id'] = new MongoId($f->request->data['data']['actividad']['_id']);
			if(isset($f->request->data['data']['componente']['_id']))
				$array['componente']['_id'] = new MongoId($f->request->data['data']['componente']['_id']);
			$this->orga->update(
				array('_id'=>new MongoId($f->request->data['_id'])),
				array( '$set' => $array)
			);
			$this->obj = $array;
		}else{//Nuevo dato
			if($f->request->padre==0){
				$array = (array)$f->request->data['data'];
				if(isset($f->request->data['data']['actividad']['_id']))
					$array['actividad']['_id'] = new MongoId($f->request->data['data']['actividad']['_id']);
				if(isset($f->request->data['data']['componente']['_id']))
					$array['componente']['_id'] = new MongoId($f->request->data['data']['componente']['_id']);
				$this->orga->insert($array);
				$this->obj = $array;
			}
			else{
				$array = (array)$f->request->data['data'];
				if(isset($f->request->data['data']['actividad']['_id']))
					$array['actividad']['_id'] = new MongoId($f->request->data['data']['actividad']['_id']);
				if(isset($f->request->data['data']['componente']['_id']))
					$array['componente']['_id'] = new MongoId($f->request->data['data']['componente']['_id']);
					$array['organizaciones']['padre'][0] = new MongoId($f->request->padre);
					$this->orga->insert($array);
					$hijo = $this->orga->findOne((array)$f->request->data['data'],array('_id'));
					$d = (array)$hijo['_id'];
					$id = new MongoId($d['$id']);
					$this->orga->update(
						array('_id'=>new MongoId($f->request->padre)) ,
						array( '$push' => array( 'organizaciones.hijos' => $id ))
					);
				
			}
		}
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->orga->remove($this->items);
	}
	/*protected function get_search(){
		global $f;
		$i = 0;
		$parametro=$f->request->nomb;
		$criteria = array(
    		'nomb' => new MongoRegex('/^'.$parametro.'/i')
  		);
		$data = $this->orga->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}*/

	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb'));
		}else $criteria = array();
		//$criteria['estado'] = $this->params['estado'];
		if(isset($this->params['oficina']))
			$criteria['oficina'] = $this->params['oficina'];
		$fields = array();
		$cursor = $this->orga->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lo(){
		global $f;
		$i = 0;
		$va = $this->orga->find();
		if($va==null){
			$this->items = array();
		}
		else{
			$data = $this->orga->find();
			foreach ($data as $obj) {
				if($obj['organizaciones']){
					$d = (array)$obj['organizaciones'];
					$so = (array)$obj['_id'];
					if(!isset($d['padre'])){
						$this->items[$i]->data = $obj['nomb'];
						$this->items[$i]->attributes->id = $so['$id'];
						$this->items[$i]->attributes->name = $obj['nomb'];
					    	$this->items[$i]->icons = "images/n1.png";
					    	$i++;
					}
				}
				else {
					$so = (array)$obj['_id'];
					$this->items[$i]->data = $obj['nomb'];
					$this->items[$i]->attributes->id = $so['$id'];
					$this->items[$i]->attributes->name = $obj['nomb'];
					$this->items[$i]->icons = "images/n1.png";
					$i++;
				}
			}
		}
	}
	protected function get_nodos(){
		global $f;
		$i = 0;
		$nodos;
		$criteria = array(
    		'_id' => new MongoId($f->request->id)
  		);
  		$data = $this->orga->findOne($criteria);
  		
  		if(!isset($data['organizaciones']['hijos']))
  			$this->items = array();
  		else{
			for($i = 0; $i < count($data['organizaciones']['hijos']); $i++){
				$ref = array(
				   '$ref' => 'mg_organizaciones',
				   '$id' => $data['organizaciones']['hijos'][$i]
				 );
				$nodos[$i] = $f->datastore->getDBRef($ref);
			}
			$i = 0;
			foreach ($nodos as $obj) {
				$so = (array)$obj['_id'];
				$this->items[$i]->data = $obj['nomb'];
				$this->items[$i]->attributes->id = $so['$id'];
				$this->items[$i]->attributes->name = $obj['nomb'];
				if($i==count($nodos)-1)
					$this->items[$i]->attributes->class = "last leaf";
				else
					$this->items[$i]->attributes->class = "leaf";
				$this->items[$i]->icons = "images/n1.png";
			    $i++;
			}
  		}
	}
	protected function get_ordenar(){
		//ordenado de procedimientos segun organizaciones XD
		global $f;
		$m = new Mongo('localhost');
		$dbb = $m->beneficencia;
		$orgaa = $dbb->td_tupa;
		$i = 0;
		$data = $orgaa->findOne(array('_id' => new MongoId('4f4543bf9c7684f80f000001')),array('procedimientos'=>true));
		foreach ($data as $obj) {
		    $ta[$i] = $obj;
			$i++;
		}
		$i = 0;
		foreach ($ta[1] as $obj) {
			$this->items[$i]['titulo'] = $obj['titulo'];
			$temp = (array)$obj['organizacion'];
			$this->items[$i]['organizacion'] = $temp['$id'];
			$i++;
		};
		foreach ($this->items as $key => $row) {
	    	$titulo[$key]  = $row['titulo'];
	    	$organizacion[$key] = $row['organizacion'];
		}
		array_multisort($organizacion, SORT_DESC, $this->items);
	}
	
}
?>