<?php
class Model_pr_pres extends Model {
	private $pres;
	private $fuen;
	public $items;
	
	public function __construct() {
		global $f;
		$this->pres = $f->datastore->pr_partidas;
		$this->fuen = $f->datastore->pr_fuentes;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->pres->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_prueba(){
		global $f;
		$filter = array();
		$fields = array();		
		$data = $this->pres->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_ultimo(){
		global $f;
		$fields = array();
		$cod = $this->params["clasificador"];
		$regex = new MongoRegex("/^".$cod."/");
		$filter = array(
			//'periodo.mes'=>$this->params["mes"],
			'periodo.ano'=>$this->params["ano"],
			'fuente._id'=>$this->params["fuente"],
			'organizacion._id'=>$this->params["organizacion"],
			'clasificador.cod'=>$regex,
			'etapa'=>array('$in'=>array("A","M"))
		);
		if(isset($this->params["mes"]))$filter["periodo.mes"] = $this->params["mes"];
		$data = $this->pres->find($filter,$fields);
		if($data->count()!=0){
			foreach($data as $ob){
				$this->items[] = $ob;
			}
		}else{		
			$this->items="zero";
		}
	}
	protected function get_lista(){
		global $f;
		$keys = array("clasificador._id" => true);
		$initial["item"]= array();
		$fuentes = $this->fuen->find();
		$index = 0;

		foreach($fuentes as $fuen){
			$initial["importe"][$index]= array();
			$jav_reduce = "if(doc.fuente.cod=='".$fuen[cod]."'){
							out.importe[".$index."].push(doc.importe);
			   				}".$jav_reduce;
			$index++;
			}
			$initial["numfuen"] = $index+3;
			$reduce = "function (doc, out)
						{ 			
						".$jav_reduce."
						out.item.push(doc);
						}
						";
		$condition = array();
		if($this->params["mes"]=='0'){
			$mes = array('$exists' => true);
		}else{
			$mes = $this->params["mes"];
		}
		if($this->params["organizacion"]==null){
			$organizacion = array('$exists' => true);
		}else{
			$organizacion = $this->params["organizacion"];
		}
		if($this->params["tipo"]==null){
			$tipo = array('$exists' => true);
		}else{
			$tipo = $this->params["tipo"];
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
		if($this->params["clasificador"]==null){
			$clasificador = array('$exists' => true);
		}else{
			$clasificador = $this->params["clasificador"];
		}
		$condition = array(
		"periodo.mes" => $mes,
		"periodo.ano" => $periodo,
		"organizacion._id" => $organizacion,
		"clasificador.tipo" => $tipo,
		"etapa" => $etapa,
		"clasificador._id" => $clasificador
		);
		
		$data = $this->pres->group($keys, $initial, $reduce, array('condition'=>$condition));
		//echo json_encode($data["retval"]);
		foreach ($data["retval"] as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],count($data["retval"]));
	}

	protected function get_search(){
		global $f;
		if($this->params["cod"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$cod = $this->params["cod"];
			$regex = new MongoRegex("/^".$cod."/");
			$criteria["clasificador.cod"]= $regex;
			if($this->params["tipo"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["clasificador.tipo"] = $this->params["tipo"];
			}		
			if($this->params["mes"]=='0'){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["periodo.mes"] = $this->params["mes"];
			}
			if($this->params["periodo"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["periodo.ano"] = $this->params["periodo"];
			}
			if(isset($this->params["orga_group"])){
				$criteria['$or'] = $this->params["orga_group"];
			}
			if($this->params["organizacion"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["organizacion._id"] = $this->params["organizacion"];
			}
			if($this->params["etapa"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["etapa"] = $this->params["etapa"];
			}
			if(isset($this->params["num_nota"])){
				$criteria["num_nota"] = $this->params["num_nota"];
			}
			if(isset($this->params["num_credito"])){
				$criteria["num_credito"] = $this->params["num_credito"];
			}
			if(isset($this->params["fuente"])){
				$criteria["fuente._id"] = $this->params["fuente"];
			}
			if(isset($this->params["meta"])){
				if($this->params["meta"]=="1"){
					$criteria["meta"] = array('$exists'=>false);
				}elseif($this->params["meta"]=="2"){
					$criteria["meta"] = array('$exists'=>true);
				}
			}
		}else $criteria = array();
		$fields = array();
		$cursor = $this->pres->find($criteria,$fields)->sort( array('cod'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_all_part(){
		global $f;
		$fields = array();
		$filter = array();
		if(isset($this->params["ano"]))$filter["periodo.ano"] = $this->params["ano"];
		if(isset($this->params["mes"]))$filter["periodo.mes"] = $this->params["mes"];
		if(isset($this->params["tipo"]))$filter["clasificador.tipo"] = $this->params["tipo"];
		if(isset($this->params["fuente"]))$filter["fuente._id"] = $this->params["fuente"];
		if(isset($this->params["organizacion"]))$filter["organizacion._id"] = $this->params["organizacion"];
		if(isset($this->params["etapa"]))$filter["etapa"] = $this->params["etapa"];
		if(isset($this->params["num_credito"]))$filter["num_credito"] = $this->params["num_credito"];
		if(isset($this->params["num_nota"]))$filter["num_nota"] = $this->params["num_nota"];
		$data = $this->pres->find($filter,$fields);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all(){
		global $f;
		$filter = array("num_credito"=>array('$gt' => 0));
		if(isset($this->params["periodo"])) $filter["periodo.ano"] = $this->params["periodo"];
		$data = $this->pres->find($filter)->sort(array('num_credito'=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all_nota(){
		global $f;
		$filter = array("num_nota"=>array('$gt' => 0));
		if(isset($this->params["periodo"])) $filter["periodo.ano"] = $this->params["periodo"];
		$data = $this->pres->find($filter)->sort(array('num_nota'=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_partidas_all(){
		global $f;
		$data = $this->pres->find(array("codigo"=>$this->params["codigo"]),array())->sort(array('num_credito'=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_notas(){
		global $f;
		$criteria = array();
		$criteria["num_nota"] = $this->params["num_nota"];
		$criteria["periodo.ano"] = $this->params["periodo"];
		$data = $this->pres->find($criteria);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		
	}
	protected function get_partidas_filter_edit(){
		global $f;
			$criteria["clasificador._id"] = $this->params["_id"];
			if($this->params["tipo"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["clasificador.tipo"] = $this->params["tipo"];
			}
			if($this->params["mes"]=='0'){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["periodo.mes"] = $this->params["mes"];
			}
			if($this->params["periodo"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["periodo.ano"] = $this->params["periodo"];
			}
			if($this->params["organizacion"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["organizacion._id"] = $this->params["organizacion"];
			}
			if($this->params["etapa"]==null){
				$criteria["_id"] = array('$exists' => true);
			}else{
				$criteria["etapa"] = $this->params["etapa"];
			}
		$data = $this->pres->find($criteria,array());
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->pres->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->pres->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_part(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($this->params['id']),
		);
		$this->pres->remove($this->items);
	}
}
?>