<?php
class Model_pr_plan extends Model {
	private $plan;
	private $plan_eval;
	public $items;
	
	public function __construct() {
		global $f;
		$this->plan = $f->datastore->pr_plan_activ;
		$this->plan_eval = $f->datastore->pr_plan_eval;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->plan->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_eval(){
		global $f;
		$this->items = $this->plan_eval->findOne(
		  array(
			'periodo'=>$this->params['periodo'],
			'organizacion._id'=>$this->params['organizacion'],
			'trimestre'=>$this->params['trimestre']
		  )
		);
	}
	protected function get_eval_v1(){
		global $f;
		$this->items = $this->plan_eval->findOne(
		  array(
			'periodo'=>$this->params['periodo'],
			'programa._id'=>$this->params['programa'],
			'trimestre'=>$this->params['trimestre']
		  )
		);
	}
	protected function get_one_eval(){
		global $f;
		$this->items = $this->plan_eval->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		if($this->params["organizacion"]==null){
			$organizacion = array('$exists' => true);
		}else{
			$organizacion = $this->params["organizacion"];
		}
		if($this->params["periodo"]==null){
			$periodo = array('$exists' => true);
		}else{
			$periodo = $this->params["periodo"];
		}
		if($this->params["etapa"]==null){
			$etapa = array('$exists' => true);
		}else{
			$etapa = $this->params["etapa"];
		}
		$filter = array(
		"organizacion._id" => $organizacion,
		"periodo" => $periodo,
		"etapa" => $etapa
		);
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->plan->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_v1(){
		global $f;
		$filter = array();
		//if($this->params["programa"]==null){
		if(!isset($this->params["programa"])){
			$programa = array('$exists' => true);
		}else{
			$programa = $this->params["programa"];
		}
		if($this->params["periodo"]==null){
			$periodo = array('$exists' => true);
		}else{
			$periodo = $this->params["periodo"];
		}
		if($this->params["etapa"]==null){
			$etapa = array('$exists' => true);
		}else{
			$etapa = $this->params["etapa"];
		}
		$filter = array(
			"programa._id" => $programa,
			"periodo" => $periodo,
			"etapa" => $etapa
		);
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->plan->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('rubro','cod'));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->plan->find($criteria,$fields)->sort( array('nomb'=>1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$data = $this->plan->find(array(),$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all_filter(){
		global $f;
		$filter = array();
		if(isset($this->params["organizacion"])){
			if($this->params["organizacion"]==null){
				$organizacion = array('$exists' => true);
			}else{
				$organizacion = $this->params["organizacion"];
			}
		}
		if(isset($this->params["periodo"])){
			if($this->params["periodo"]==null){
				$periodo = array('$exists' => true);
			}else{
				$periodo = $this->params["periodo"];
			}
		}
		if(isset($this->params["etapa"])){
			if($this->params["etapa"]==null){
				$etapa = array('$exists' => true);
			}else{
				$etapa = $this->params["etapa"];
			}
		}
		$filter = array(
			"periodo" => $periodo
		);
		if(isset($organizacion))
			$filter['organizacion._id'] = $organizacion;
		if(isset($etapa))
			$filter['etapa'] = $etapa;
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->plan->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all_filter_v1(){
		global $f;
		$filter = array();
		if(isset($this->params["programa"])){
			if($this->params["programa"]==null){
				$programa = array('$exists' => true);
			}else{
				$programa = $this->params["programa"];
			}
		}
		if(isset($this->params["periodo"])){
			if($this->params["periodo"]==null){
				$periodo = array('$exists' => true);
			}else{
				$periodo = $this->params["periodo"];
			}
		}
		if(isset($this->params["etapa"])){
			if($this->params["etapa"]==null){
				$etapa = array('$exists' => true);
			}else{
				$etapa = $this->params["etapa"];
			}
		}
		$filter = array(
			"periodo" => $periodo
		);
		if(isset($programa))
			$filter['programa._id'] = $programa;
		if(isset($etapa))
			$filter['etapa'] = $etapa;
		$fields = array();
		$order = array('fecreg'=>-1);
		$data = $this->plan->find($filter,$fields)->sort($order);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_insert(){
		global $f;
		$this->plan->insert( $this->params['data'] );
	}
	protected function save_eval_insert(){
		global $f;
		$this->plan_eval->insert( $this->params['data'] );
	}
	protected function save_insert_aprobar(){
		global $f;
		$data = $this->plan->find( array('organizacion' => $this->params["organizacion"],'periodo' => $this->params["periodo"]) );
		foreach ($data as $ob){
			$this->plan->update( array('_id'=>new MongoId($ob["_id"])) , array('$set'=>array('etapa'=>'E')) );
		}
		$this->plan->insert( $this->params['data'] );
	}
	protected function save_insert_aprobar_v1(){
		global $f;
		$data = $this->plan->find( array('programa' => $this->params["programa"],'periodo' => $this->params["periodo"]) );
		foreach ($data as $ob){
			$this->plan->update( array('_id'=>new MongoId($ob["_id"])) , array('$set'=>array('etapa'=>'E')) );
		}
		$this->plan->insert( $this->params['data'] );
	}
	protected function save_aprobar(){
		global $f;
		unset($this->params['data']['_id']);
		$this->plan->update( array('_id'=>$this->params['_id']) , array('$set'=>array('etapa'=>'E')) );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->plan->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function save_update_eval(){
		global $f;
		unset($this->params['data']['_id']);
		$this->plan_eval->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_plan(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->plan->remove($this->items);
	}
}
?>