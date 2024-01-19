<?php
class Controller_al_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("al/repo.grid");
	}
	function execute_dili_prog(){
		global $f;
		$model = $f->model("al/dili")->params(array("periodo"=>$f->request->periodo,"estado"=>$f->request->estado))->get("all");
		if(count($model->items)>0){
			foreach($model->items as $i=>$item){
				$expd = $f->model("al/expd")->params(array("_id"=>$item["expediente"]["_id"]))->get("one");
				if($expd->items!=null){
					$model->items[$i]["expediente"] = $expd->items;
				}
			}
			$model->filtros = array("periodo"=>$f->request->periodo,"estado"=>$f->request->estadonomb);
			$f->response->view("al/repo.dili.print",$model);
		}else{
			$f->response->print("No se encontrar&oacute;n resultados");
		}		
	}
	function execute_expd_activ(){
		global $f;
		$model = $f->model("al/expd")->params(array("archivado"=>"false"))->get("all");
		if(count($model->items)>0){
			$f->response->view("al/repo.expdacti.print",$model);
		}else{
			$f->response->print("No se encontrar&oacute;n resultados");
		}	
	}
	function execute_expd_arch(){
		global $f;
		$model = $f->model("al/expd")->params(array("archivado"=>"true"))->get("all");
		if(count($model->items)>0){
			$f->response->view("al/repo.expdarch.print",$model);
		}else{
			$f->response->print("No se encontrar&oacute;n resultados");
		}	
	}
	function execute_cont(){
		global $f;
		$model = $f->model("al/cont")->params(array("clasificacion"=>$f->request->clasificacion,"periodo"=>$f->request->periodo))->get("all");
		if(count($model->items)>0){
			$model->filtros = array("periodo"=>$f->request->periodo,"clasificacion"=>$f->request->clasifnomb);
			$f->response->view("al/repo.cont.print",$model);
		}else{
			$f->response->print("No se encontrar&oacute;n resultados");
		}
	}
	function execute_conv(){
		global $f;
		$model = $f->model("al/conv")->get("all");
		if(count($model->items)>0){
			$f->response->view("al/repo.conv.print",$model);
		}else{
			$f->response->print("No se encontrar&oacute;n resultados");
		}
	}
	function execute_expd(){
		global $f;
		$model = $f->model("al/expd")->params(array("archivado"=>"false","tipo"=>$f->request->tipo,"encargado"=>$f->request->encargado,"materia"=>$f->request->materia,"archivado"=>$f->request->archivado))->get("all");
		$model->filtros = $f->request->data;
		$f->response->view("al/repo.expd.export",$model);
	}
}
?>