<?php
class Controller_ac_repo extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("ac/repo");
	}
	function execute_logs(){
		global $f;
		$params = array();
		if($f->request->data["modulo"]!="0"){
			$params["modulo"] = $f->request->modulo;
		}
		if($f->request->data["desde"]!=""){
			$params["desde"] = $f->request->desde;
		}
		if($f->request->data["hasta"]!=""){
			$params["hasta"] = $f->request->hasta;
		}
		if(isset($f->request->data["trabajador"])){
			if($f->request->data["trabajador"]!='undefined')
				$params["trabajador"] = $f->request->trabajador;
		}
		$model = $f->model("ac/log")->params($params)->get("all");
		$model->filtros = array(
			"modulo"=>$f->request->modulo,
			"desde"=>$f->request->desde,
			"hasta"=>$f->request->hasta,
		);
		if(isset($f->request->data["trabajador"])){
			if($f->request->data["trabajador"]!='undefined'){
				$trab = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->trabajador)))->get("one")->items;
				$model->filtros["trabajador"] = $trab["nomb"]." ".$trab["appat"]." ".$trab["apmat"];
			}
		}else{
			$model->filtros["trabajador"] = "--";
		}
		$f->response->view("ac/repo.logs.export",$model);
	}
}
?>