<?php
class Model_al_expd extends Model {
	private $expd;
	public $items;
	private $expd_hist;
	
	public function __construct() {
		global $f;
		$this->expd = $f->datastore->al_expedientes;
		$this->expd_hist = $f->datastore->al_expedientes_hist;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->expd->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		if(isset($this->params["archivado"])){
			if($this->params["archivado"]=="true"){
				$filter["archivado"] = array('$exists'=>true);
			}elseif($this->params["archivado"]=="false"){
				$filter["archivado"] = array('$exists'=>false);;
			}
		}
		if($this->params["tipo"]=="0"){
			$filter["tipo"] = array('$exists' => true);
		}else{
			$filter["tipo"] = $this->params["tipo"];
		}
		if($this->params["encargado"]==null){
			$filter["encargado"] = array('$exists' => true);
		}else{
			$filter["encargado"] = $this->params["encargado"];
		}
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->expd->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_hist(){
		global $f;
		$filter = array("numero"=>$this->params["numero"]);
		$fields = array();
		$order = array();
		$data = $this->expd_hist->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all_hist(){
		global $f;
		$filter = array("numero"=>$this->params["numero"]);
		$fields = array();
		$order = array("fecactualizacion"=>-1);
		$data = $this->expd_hist->find($filter,$fields)->sort($order);
		foreach ($data as $obj){
		    $this->items[] = $obj;
		}
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('numero','demandado'));
		}else $criteria = array();		
		$fields = array();
		$cursor = $this->expd->find($criteria,$fields)->sort( array('nomb'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$filter = array();
		if(isset($this->params["materia"])){
			if($this->params["materia"]!=""){
				$f->library('helpers');
				$helper=new helper();
				$filter = $helper->paramsSearch($this->params["materia"], array('materia'));
			}
		}
		if(isset($this->params['archivado'])){
			if($this->params['archivado']=="true"){
				$filter["archivado"] = array('$exists'=>true);
			}elseif($this->params['archivado']=="false"){
				$filter["archivado"] = array('$exists'=>false);;
			}
		}
		if(isset($this->params["tipo"])){
			if($this->params["tipo"]=="0"){
				$filter["tipo"] = array('$exists' => true);
			}else{
				$filter["tipo"] = $this->params["tipo"];
			}
		}
		if(isset($this->params["encargado"])){
			$filter["encargado"] = $this->params["encargado"];
		}
		$data = $this->expd->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->expd->insert( $this->params['data'] );
	}
	protected function save_insert_hist(){
		global $f;
		$this->expd_hist->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->expd->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_expd(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->expd->remove($this->items);
	}
}
?>