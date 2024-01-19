<?php
class Controller_ct_auxi extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres.auxi");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->print("</div>");
		
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/auxi")->params(array(
					"saldo"=>$f->request->saldo			
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/auxi")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/auxi')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/auxi")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			$data["autor"]["_id"] = new MongoId($data["autor"]["_id"]);
			$data["autor"]["cargo"]["_id"] = new MongoId($data["autor"]["cargo"]["_id"]);
			$data["autor"]["cargo"]["organizacion"]["_id"] = new MongoId($data["autor"]["cargo"]["organizacion"]["_id"]);
			$data["saldo"]["organizacion"]["_id"] = new MongoId($data["saldo"]["organizacion"]["_id"]);
			$data["saldo"]["organizacion"]["actividad"]["_id"] = new MongoId($data["saldo"]["organizacion"]["actividad"]["_id"]['$id']);
			$data["saldo"]["organizacion"]["componente"]["_id"] = new MongoId($data["saldo"]["organizacion"]["componente"]["_id"]['$id']);
			$data["saldo"]["fuente"]["_id"] = new MongoId($data["saldo"]["fuente"]["_id"]);
			//$data["saldo"]["generica"]["_id"] = new MongoId($data["saldo"]["generica"]["_id"]);
			//$data["saldo"]["especifica"]["_id"] = new MongoId($data["saldo"]["especifica"]["_id"]);
			$data["saldo"]["subespecifica"]["_id"] = new MongoId($data["saldo"]["subespecifica"]["_id"]);
			$data["saldo"]["periodo"]["mes"] = floatval($data["saldo"]["periodo"]["mes"]);
			if(isset($data["saldo"]["meta"])){
				$data["saldo"]["meta"]["_id"] = new MongoId($data["saldo"]["meta"]["_id"]);
			}
			if(isset($data["saldo"]["_id"])){
				$model = $f->model("ct/saldi")->params(array("_id"=>new MongoId($data["saldo"]["_id"])))->get("one")->items;
				$data["saldo"]["_id"]=$model["_id"];
				$data["saldo"]["periodo"]=$model["periodo"];
				$data["saldo"]["periodo"]["mes"] = floatval($model["periodo"]["mes"]);
				$data["saldo"]["organizacion"]=$model["organizacion"];
				$data["saldo"]["fuente"]=$model["fuente"];
				//$data["saldo"]["generica"]=$model["generica"];
				//$data["saldo"]["especifica"]=$model["especifica"];
				$data["saldo"]["subespecifica"]=$model["subespecifica"];
				if(isset($model["meta"])){
					$data["saldo"]["meta"]=$model["meta"];
				}
			}else{//se crea un nuevo saldo
				$params_sald_ultimo = array(
					"organizacion"=>$data["saldo"]["organizacion"]["_id"],
					"fuente"=>$data["saldo"]["fuente"]["_id"],
					"periodo"=>$data["saldo"]["periodo"]["ano"],
					"subespecifica"=>$data["saldo"]["subespecifica"]["_id"]
				);
				if(isset($data["saldo"]["meta"])){
					$params_sald_ultimo["meta"] = $data["saldo"]["meta"]["_id"];
				}
				$model = $f->model("ct/saldi")->params($params_sald_ultimo)->get("ultimo")->items;
				//print_r($model);				
				$sald = array();
				$sald["estado"] = "A";
				$sald["periodo"] = $data["saldo"]["periodo"];
				$sald["periodo"]["mes"] = floatval($sald["periodo"]["mes"]);
				$sald["organizacion"] = $data["saldo"]["organizacion"];
				$sald["fuente"] = $data["saldo"]["fuente"];
				//$sald["generica"] = $data["saldo"]["generica"];
				//$sald["especifica"] = $data["saldo"]["especifica"];
				$sald["subespecifica"] = $data["saldo"]["subespecifica"];
				if(isset($data["saldo"]["meta"])){
					$sald["meta"] = $data["saldo"]["meta"];
				}
				if($model=="reinico"){
					$sald["ejec_pres"]["debe_ini"] = floatval("0.00");
					$sald["ejec_pres"]["haber_ini"] = floatval("0.00");
					$sald["ejec_ingr"]["debe_ini"] = floatval("0.00");
					$sald["ejec_ingr"]["haber_ini"] = floatval("0.00");
				}else{	
					//$model = $model[0];		
					$sald["ejec_pres"]["debe_ini"] = floatval($model["ejec_pres"]["debe_fin"]);
					$sald["ejec_pres"]["haber_ini"] = floatval($model["ejec_pres"]["haber_fin"]);
					$sald["ejec_ingr"]["debe_ini"] = floatval($model["ejec_ingr"]["debe_fin"]);
					$sald["ejec_ingr"]["haber_ini"] = floatval($model["ejec_ingr"]["haber_fin"]);
				}
				$f->model("ct/saldi")->params(array('data'=>$sald))->save("insert");
				$data["saldo"]["_id"]=$sald["_id"];
			}
			$f->model("ct/auxi")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/auxi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/epres.auxi.edit");
	}
}
?>