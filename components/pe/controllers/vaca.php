<?php
class Controller_pe_vaca extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pe/hora.calen");
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/prog")->params(array("tipo"=>"VA","enti"=>new MongoId($f->request->data['enti'])))->get("trab");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['del'])){
			foreach ($data['del'] as $blo){
				$f->model("pe/prog")->params(array('_id'=>new MongoId($blo)))->delete("data");
			}
		}
		if(isset($data['data'])){
			foreach ($data['data'] as $blo){
				$blo['trabajador'] = $data['trabajador'];
				$blo['tipo']['_id'] = new MongoId($blo['tipo']['_id']);
				$blo['fecini'] = new MongoDate(strtotime($blo['fecini']));
				$blo['fecfin'] = new MongoDate(strtotime($blo['fecfin']));
				if(!isset($blo['_id'])){
					$blo['fecreg'] = new MongoDate();
					$f->model("pe/prog")->params(array('data'=>$blo))->save("insert");
				}else{
					$f->model("pe/prog")->params(array('_id'=>new MongoId($blo['_id']),'data'=>$blo))->save("update");
				}
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/vaca.edit");
	}
	function execute_modal(){
		global $f;
		$f->response->view("pe/vaca.modal");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/vaca.select");
	}
	function execute_conectar(){
		global $f;
		$f->library('nusoap');
		$cliente = new nusoap_client("http://ws.pide.gob.pe/ConsultaRuc?wsdl");
		$error = $cliente->getError();
		if ($error) {
			echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
		}
		$result = $cliente->call("getDatosPrincipales", array("numruc" => "20120958136"));
		//$result = $cliente->call("getDatosPrincipales", array("numruc" => "10702307738"));
		//$result = $cliente->call("getDomicilioLegal", array("numruc" => "10702307738"));
		//$result = $cliente->call("getRepLegales", array("numruc" => "20120958136"));
		$result = $cliente->call("getRepLegales", array("numruc" => "10702307738"));
		if ($cliente->fault) {
			echo "<h2>Fault</h2><pre>";
			print_r($result);
			echo "</pre>";
		}else{
			$error = $cliente->getError();
			if ($error) {
				echo "<h2>Error</h2><pre>" . $error . "</pre>";
			}else{
				echo "<h2>Libros</h2><pre>";
				print_r($result);
				echo "</pre>";
			}
		}
	}
	function execute_skm(){
		global $f;
		$data = $f->datastore->ho_tarifas->find(array('categoria'=>array('$exists'=>true)));
		foreach($data as $item){
			$item['feceli'] = new MongoDate();
			$item['coleccion'] = 'ho_tarifas';
			$f->datastore->temp_del->insert($item);
			$f->datastore->ho_tarifas->remove($item['_id']);
		}
	}
}
?>