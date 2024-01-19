<?php
class Controller_pr_nota extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pr/pres.modi_nota");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/pres")->params(array("num_nota"=>floatval($f->request->num_nota),"periodo"=>$f->request->periodo))->get("notas");
		$array["items"] = array();
		if($model->items!=null){
			foreach($model->items as $item){
				$orga = $f->model("mg/orga")->params(array("_id"=>new MongoId($item["organizacion"]["_id"])))->get("one")->items;
				$acti = $orga["actividad"]["_id"];
				$comp = $orga["componente"]["_id"];
				$actividad = $f->model("pr/acti")->params(array("_id"=>$acti))->get("one")->items;
				$componente = $f->model("pr/acti")->params(array("_id"=>$comp))->get("one")->items;
				$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
				//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
				$proyecto = $actividad;
				$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
				$obra = $componente;
				$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
				$codigo = $item["clasificador"]["cod"];
				$codigo = explode(".", $codigo);
				$estr = array(
					"funcion"=>$pliego,
					"programa"=>$programa,
					"subprograma"=>$subprograma,
					"actividad"=>$proyecto,
					"componente"=>$obra,
					"meta"=>"001",
					"descr_meta"=>$orga["objetivo"],
					"fuente"=>$item["fuente"]["cod"],
					"tt"=>$codigo[0],
					"gen"=>$codigo[1],
					"sg1"=>$codigo[2],
					"sg2"=>$codigo[3],
					"e1"=>$codigo[4],
					"e2"=>$codigo[5],
					"hab"=>(floatval($item["importe"])>0)?$item["importe"]:"",
					"anu"=>(floatval($item["importe"])<0)?$item["importe"]:""
				);
				array_push($array["items"],$estr);
			}
			$array["filtros"] = array(
				"num_nota"=>$f->request->num_nota,
				"ano"=>$f->request->periodo,
				"mes_modif"=>$model->items[0]["periodo"]["mes"]
			);
			$f->response->json($array);
		}
	}
	function execute_lista_v1(){
		global $f;
		$model = $f->model("pr/pres")->params(array("num_nota"=>floatval($f->request->num_nota),"periodo"=>$f->request->periodo))->get("notas");
		$array["items"] = array();
		if($model->items!=null){
			foreach($model->items as $item){
				$prog = $f->model("mg/prog")->params(array("_id"=>new MongoId($item["programa"]["_id"])))->get("one")->items;
				$acti = $prog["actividad"]["_id"];
				$comp = $prog["componente"]["_id"];
				$actividad = $f->model("pr/acti")->params(array("_id"=>$acti))->get("one")->items;
				$componente = $f->model("pr/acti")->params(array("_id"=>$comp))->get("one")->items;
				$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
				//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
				$proyecto = $actividad;
				//$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
				$obra = $componente;
				$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($prog["funcion"])))->get("one")->items;
				$codigo = $item["clasificador"]["cod"];
				$codigo = explode(".", $codigo);
				$estr = array(
					"funcion"=>$pliego,
					"programa"=>$prog,
					"subprograma"=>$subprograma,
					"actividad"=>$proyecto,
					"componente"=>$obra,
					"meta"=>"001",
					"descr_meta"=>$orga["objetivo"],
					"fuente"=>$item["fuente"]["cod"],
					"tt"=>$codigo[0],
					"gen"=>$codigo[1],
					"sg1"=>$codigo[2],
					"sg2"=>$codigo[3],
					"e1"=>$codigo[4],
					"e2"=>$codigo[5],
					"hab"=>(floatval($item["importe"])>0)?$item["importe"]:"",
					"anu"=>(floatval($item["importe"])<0)?$item["importe"]:""
				);
				array_push($array["items"],$estr);
			}
			$array["filtros"] = array(
				"num_nota"=>$f->request->num_nota,
				"ano"=>$f->request->periodo,
				"mes_modif"=>$model->items[0]["periodo"]["mes"]
			);
			$f->response->json($array);
		}
	}
	function execute_print(){
		global $f;
		$model = $f->model("pr/pres")->params(array("num_nota"=>floatval($f->request->num_nota),"periodo"=>$f->request->periodo))->get("notas");
		$array["items"] = array();
		if($model->items!=null){
			foreach($model->items as $item){
				$orga = $f->model("mg/orga")->params(array("_id"=>new MongoId($item["organizacion"]["_id"])))->get("one")->items;
				$acti = $orga["actividad"]["_id"];
				$comp = $orga["componente"]["_id"];
				$actividad = $f->model("pr/acti")->params(array("_id"=>$acti))->get("one")->items;
				$componente = $f->model("pr/acti")->params(array("_id"=>$comp))->get("one")->items;
				$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
				//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
				$proyecto = $actividad;
				$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
				$obra = $componente;
				$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
				$codigo = $item["clasificador"]["cod"];
				$codigo = explode(".", $codigo);
				$estr = array(
					"funcion"=>$pliego,
					"programa"=>$programa,
					"subprograma"=>$subprograma,
					"actividad"=>$proyecto,
					"componente"=>$obra,
					"meta"=>"001",
					"descr_meta"=>$orga["objetivo"],
					"fuente"=>$item["fuente"]["cod"],
					"tt"=>$codigo[0],
					"gen"=>$codigo[1],
					"sg1"=>$codigo[2],
					"sg2"=>$codigo[3],
					"e1"=>$codigo[4],
					"e2"=>$codigo[5],
					"hab"=>(floatval($item["importe"])>0)?$item["importe"]:"",
					"anu"=>(floatval($item["importe"])<0)?$item["importe"]:""
				);
				array_push($array["items"],$estr);
			}
			$array["filtros"] = array(
				"num_nota"=>$f->request->num_nota,
				"ano"=>$f->request->periodo,
				"mes_modif"=>$model->items[0]["periodo"]["mes"]
			);
			$f->response->view("pr/repo.nota.export",$array);
		}
		//$f->response->json($array);
	}
	function execute_print_v1(){
		global $f;
		$model = $f->model("pr/pres")->params(array("num_nota"=>floatval($f->request->num_nota),"periodo"=>$f->request->periodo))->get("notas");
		$array["items"] = array();
		if($model->items!=null){
			foreach($model->items as $item){
				$prog = $f->model("mg/prog")->params(array("_id"=>new MongoId($item["programa"]["_id"])))->get("one")->items;
				$acti = $orga["actividad"]["_id"];
				$comp = $orga["componente"]["_id"];
				$actividad = $f->model("pr/acti")->params(array("_id"=>$acti))->get("one")->items;
				$componente = $f->model("pr/acti")->params(array("_id"=>$comp))->get("one")->items;
				$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
				//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
				$proyecto = $actividad;
				//$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
				$obra = $componente;
				$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($prog["funcion"])))->get("one")->items;
				$codigo = $item["clasificador"]["cod"];
				$codigo = explode(".", $codigo);
				$estr = array(
					"funcion"=>$pliego,
					"programa"=>$prog,
					"subprograma"=>$subprograma,
					"actividad"=>$proyecto,
					"componente"=>$obra,
					"meta"=>"001",
					"descr_meta"=>$orga["objetivo"],
					"fuente"=>$item["fuente"]["cod"],
					"tt"=>$codigo[0],
					"gen"=>$codigo[1],
					"sg1"=>$codigo[2],
					"sg2"=>$codigo[3],
					"e1"=>$codigo[4],
					"e2"=>$codigo[5],
					"hab"=>(floatval($item["importe"])>0)?$item["importe"]:"",
					"anu"=>(floatval($item["importe"])<0)?$item["importe"]:""
				);
				array_push($array["items"],$estr);
			}
			$array["filtros"] = array(
				"num_nota"=>$f->request->num_nota,
				"ano"=>$f->request->periodo,
				"mes_modif"=>$model->items[0]["periodo"]["mes"]
			);
			$f->response->view("pr/repo.nota.export",$array);
		}
		//$f->response->json($array);
	}
}
?>