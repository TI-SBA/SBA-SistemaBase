<?php
class Controller_ct_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/repo.grid");
	}
	function execute_index2(){
		global $f;
		$f->response->view("ct/repo.view");
	}
	function execute_proci(){
		global $f;
		$fuentes = $f->model("pr/fuen")->get("all");
		$data["items"] = array();
		foreach($fuentes->items as $j=>$fuente){
			$model = $f->model("ct/proc")->params(array(
				"mes"=>floatval($f->request->mes),
				"ano"=>$f->request->ano,
				"fuente"=>$fuente["_id"],
				"organizacion"=>new MongoId($f->request->organizacion)
			))->get("lista");	
			if($model->items!=null){
				$data["items"][$j] = $fuente;
				$data["items"][$j]["items"] = array();
				$data["items"][$j]["total_ppto"] = array();
				$data["items"][$j]["total_ingr"] = array();
				$data["items"][$j]["total_acum"] = array();
				$data["items"][$j]["total_sald"] = array();
				foreach($model->items as $i=>$item){
					$cursor2 = $f->model("pr/clas")->params(
								array(
									"_id"=>$item["clasificador"]["_id"],						
								)
					)->get("one")->items;
					$model->items[$i]["clasificador"] = $cursor2;
					array_push($data["items"][$j]["items"],$model->items[$i]);
					if(!isset($cursor2["clasificadores"]["hijos"])){
						$tot_ppto = 0;
						for($k=0;$k<count($model->items[$i]["pim"]);$k++){
							$tot_ppto = $tot_ppto + floatval($model->items[$i]["pim"][$k]["importe"]);
						}
						array_push($data["items"][$j]["total_ppto"],$tot_ppto);
						array_push($data["items"][$j]["total_ingr"],$model->items[$i]["ingreso"]);
						array_push($data["items"][$j]["total_acum"],$model->items[$i]["acumulado"]);
						array_push($data["items"][$j]["total_sald"],$model->items[$i]["saldo"]);
					}
				}			
			}
		}
		$data["filtros"] = array(
					"mes"=>$f->request->mes,
					"ano"=>$f->request->ano,
					"organomb"=>$f->request->organomb
		);
		$f->response->view("ct/repo.proci.print",$data);			
	}
	function execute_proc_ing(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"I","id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>"I",
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["organizacion"]))$params["organizacion"]=$f->request->data["organizacion"];
		//if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$eje = $f->model("ct/auxi")->params($params)->get("all");
		$part = array();
		if($cursor->items!=null){
			foreach($cursor->items as $item){
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "PRE";
			}
		}
		if($eje->items!=null){
			foreach($eje->items as $item){
				$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
				$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
			}
		}else{
			return $f->response->print("No se ha encontrado ningun auxiliar de gasto");
		}
		$array = array();
		foreach($model->items as $i=>$item){
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					switch($partida["area"]){
						case "PRE":
							if(!isset($array[$partida["fuente"]["_id"]])){
								$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
								$array[$partida["fuente"]["_id"]]["acti"] = array();
								$array[$partida["fuente"]["_id"]]["proy"] = array();
							}
							if(isset($partida["meta"])){
								if(!isset($array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]])){
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"] = array();
								}
								if(!isset($array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"]+=$partida["importe"];
								if($partida["etapa"]=="A"){
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
								}
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["clasificador"]["cod"]){
										unset($array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]);
									}
								}
							}else{
								if(!isset($array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}])){
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pim"]+=$partida["importe"];
								if($partida["etapa"]=="A"){
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
								}
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["clasificador"]["cod"]){
										unset($array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]);
									}
								}
							}
							break;
						case "CON":
							if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}])){
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["fuente"] = $partida["saldo"]["fuente"];
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"] = array();
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"] = array();
							}
							if(isset($partida["saldo"]["meta"])){
								if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}])){
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["meta"] = $partida["saldo"]["meta"];
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"] = array();
								}
								if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}])){
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["eje"][$partida["saldo"]["periodo"]["mes"]]+=$partida["ejec_pres"]["monto"];
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["saldo"]["subespecifica"]["cod"]){
										unset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]);
									}
								}
							}else{
								if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}])){
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["eje"][$partida["saldo"]["periodo"]["mes"]]+=$partida["ejec_pres"]["monto"];
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["saldo"]["subespecifica"]["cod"]){
										unset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]);
									}
								}
							}
							break;
					}
				}
			}
		}
		//print_r($array);die();
		$filtros = array(
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["organizacion"]))$filtros["organizacion"]=$f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->data["organizacion"])))->get("one")->items;
		
		switch($f->request->periodo){
			case "A":
				$f->response->view("ct/repo.ejec_pres.print",array("items"=>$array,"filtros"=>$filtros));
				break;
			case "1S":
				$meses = array(1,2,3,4,5,6);
				$f->response->view("ct/repo.ejec_pres_semes.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"I Semestre"));
				break;
			case "2S":
				$meses = array(7,8,9,10,11,12);
				$f->response->view("ct/repo.ejec_pres_semes.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"II Semestre"));
				break;
			case "1T":
				$meses = array(1,2,3);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"I Trimestre"));
				break;
			case "2T":
				$meses = array(4,5,6);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"II Trimestre"));
				break;
			case "3T":
				$meses = array(7,8,9);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"III Trimestre"));
				break;
			case "4T":
				$meses = array(10,11,12);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"IV Trimestre"));
				break;
		}
	}
	function execute_proc_gas(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"G","id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>"G",
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["organizacion"]))$params["organizacion"]=$f->request->data["organizacion"];
		//if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$eje = $f->model("ct/auxg")->params($params)->get("all");
		$part = array();
		if($eje->items!=null){
			foreach($cursor->items as $item){
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "PRE";
			}
		}
		if($eje->items!=null){
			foreach($eje->items as $item){
				$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
				$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
			}
		}else{
			return $f->response->print("No se ha encontrado ningun auxiliar de gasto");
		}
		$array = array();
		foreach($model->items as $i=>$item){
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					switch($partida["area"]){
						case "PRE":
							if(!isset($array[$partida["fuente"]["_id"]])){
								$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
								$array[$partida["fuente"]["_id"]]["acti"] = array();
								$array[$partida["fuente"]["_id"]]["proy"] = array();
							}
							if(isset($partida["meta"])){
								if(!isset($array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]])){
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"] = array();
								}
								if(!isset($array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"]+=$partida["importe"];
								if($partida["etapa"]=="A"){
									$array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
								}
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["clasificador"]["cod"]){
										unset($array[$partida["fuente"]["_id"]]["proy"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]);
									}
								}
							}else{
								if(!isset($array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}])){
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pim"]+=$partida["importe"];
								if($partida["etapa"]=="A"){
									$array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
								}
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["clasificador"]["cod"]){
										unset($array[$partida["fuente"]["_id"]]["acti"][$item["_id"]->{'$id'}]);
									}
								}
							}
							break;
						case "CON":
							if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}])){
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["fuente"] = $partida["saldo"]["fuente"];
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"] = array();
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"] = array();
							}
							if(isset($partida["saldo"]["meta"])){
								if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}])){
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["meta"] = $partida["saldo"]["meta"];
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"] = array();
								}
								if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}])){
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["eje"][$partida["saldo"]["periodo"]["mes"]]+=$partida["ejec_pres"]["monto"];
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["saldo"]["especifica"]["cod"]){
										unset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["proy"][$partida["saldo"]["meta"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]);
									}
								}
							}else{
								if(!isset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}])){
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["pia"] = 0;
									$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["pim"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]["eje"][$partida["saldo"]["periodo"]["mes"]]+=$partida["ejec_pres"]["monto"];
								if(!isset($item["clasificadores"]["hijos"])){
									if($item["cod"]!=$partida["saldo"]["especifica"]["cod"]){
										unset($array[$partida["saldo"]["fuente"]["_id"]->{'$id'}]["acti"][$item["_id"]->{'$id'}]);
									}
								}
							}
							break;
					}
				}
			}
		}
		//print_r($array);die();
		$filtros = array(
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["organizacion"]))$filtros["organizacion"]=$f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->data["organizacion"])))->get("one")->items;
		switch($f->request->periodo){
			case "A":
				$f->response->view("ct/repo.ejec_pres.print",array("items"=>$array,"filtros"=>$filtros));
				break;
			case "1S":
				$meses = array(1,2,3,4,5,6);
				$f->response->view("ct/repo.ejec_pres_semes.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"I Semestre"));
				break;
			case "2S":
				$meses = array(7,8,9,10,11,12);
				$f->response->view("ct/repo.ejec_pres_semes.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"II Semestre"));
				break;
			case "1T":
				$meses = array(1,2,3);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"I Trimestre"));
				break;
			case "2T":
				$meses = array(4,5,6);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"II Trimestre"));
				break;
			case "3T":
				$meses = array(7,8,9);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"III Trimestre"));
				break;
			case "4T":
				$meses = array(10,11,12);
				$f->response->view("ct/repo.ejec_pres_trim.print",array("items"=>$array,"filtros"=>$filtros,"meses"=>$meses,"title"=>"IV Trimestre"));
				break;
		}
	}
	function execute_procg(){
		global $f;
		$model = $f->model("ct/procg")->params(array(
				"trimestre"=>$f->request->trimestre,
				"ano"=>$f->request->ano
		))->get("lista");
		$trimestre = $f->request->trimestre;
		if($trimestre=="1"){
			$mes1 = 1;
			$mes2 = 2;
			$mes3 = 3;
		}elseif($trimestre=="2"){
			$mes1 = 4;
			$mes2 = 5;
			$mes3 = 6;
		}elseif($trimestre=="3"){
			$mes1 = 7;
			$mes2 = 8;
			$mes3 = 9;
		}elseif($trimestre=="4"){
			$mes1 = 10;
			$mes2 = 11;
			$mes3 = 12;
		}
		$model2 = array();
		$model2["items"] = array();
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				$cursor2 = $f->model("pr/clas")->params(
							array(
								"_id"=>$item["clasificador"]["_id"],						
							)
				)->get("one")->items;
				$model->items[$i]["clasificador"] = $cursor2;
				if(!isset($model2["items"][$item["organizacion"]["_id"]->{'$id'}])){
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}] = $item["organizacion"];
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["funcion"] = $item["funcion"];
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["programa"] = $item["programa"];
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["subprograma"] = $item["subprograma"];
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"] = array();
				}
				if(!isset($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}])){
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}] = $item["fuente"];
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"] = array();
				}
				if(!isset($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}])){
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}] = $cursor2;
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["comp_mes1"] = array();
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["comp_mes2"] = array();
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["comp_mes3"] = array();
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ejec_mes1"] = array();
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ejec_mes2"] = array();
					$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ejec_mes3"] = array();
				}
				if($mes1==$item["periodo"]["mes"]){
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["comp_mes1"],$item["compromiso"]);
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ejec_mes1"],$item["ejecucion"]);
				}elseif($mes2==$item["periodo"]["mes"]){
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["comp_mes2"],$item["compromiso"]);
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ejec_mes2"],$item["ejecucion"]);
				}elseif($mes3==$item["periodo"]["mes"]){
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["comp_mes3"],$item["compromiso"]);
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ejec_mes3"],$item["ejecucion"]);
				}
				//ppto del ano
				$cursor3 = $f->model("pr/pres")->params(array(
					"ano"=>$item["periodo"]["ano"],
					"organizacion"=>$item["organizacion"]["_id"]->{'$id'},
					"clasificador"=>$item["clasificador"]["cod"],
					"fuente"=>$item["fuente"]["_id"]->{'$id'}				
				))->get("ultimo")->items;
				$model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ppto"] = array();
				if($cursor3=="zero"){
					array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ppto"],"0.00");
				}else{
					foreach($cursor3 as $i_ppto){
						array_push($model2["items"][$item["organizacion"]["_id"]->{'$id'}]["fuentes"][$item["fuente"]["_id"]->{'$id'}]["items"][$item["clasificador"]["_id"]->{'$id'}]["ppto"],$i_ppto["importe"]);
					}
				}
			}
		}
		$model2["filtros"] = array(
					"ano"=>$f->request->ano,
					"trimestre"=>$f->request->trimestre
		);
		//$f->response->json($model2);
		$f->response->view("ct/repo.procg.print",$model2);
	}
	function execute_bala(){
		global $f;
		$model = array();
		$model["items"] = array();
		$model1 = $f->model("ct/proc")->params(array("mes"=>floatval($f->request->mes),"ano"=>$f->request->ano))->get("lista");
		$model2 = $f->model("ct/procg")->params(array("mes"=>floatval($f->request->mes),"ano"=>$f->request->ano))->get("lista");
		$index = 0;
		if($model1->items!=null){
			foreach($model1->items as $obj1){
				$model["items"][$index] = $obj1;
				$model["items"][$index]["tipo"] = "I";
				$index++;
			}
		}
		if($model2->items!=null){
			foreach($model2->items as $obj2){
				$model["items"][$index] = $obj2;
				$model["items"][$index]["tipo"] = "G";
				$index++;
			}
		}
		if(count($model["items"])){
			$array["items"] = array();
			foreach($model["items"] as $i=>$item){
				$cursor2 = $f->model("pr/clas")->params(
							array(
								"_id"=>$item["clasificador"]["_id"],						
							)
				)->get("one")->items;
				if(!isset($array["items"][$item["organizacion"]["_id"]->{'$id'}])){
					$array["items"][$item["organizacion"]["_id"]->{'$id'}] = $item["organizacion"];
					$array["items"][$item["organizacion"]["_id"]->{'$id'}]["ingr"] = array();
					$array["items"][$item["organizacion"]["_id"]->{'$id'}]["acum_ingr"] = array();
					$array["items"][$item["organizacion"]["_id"]->{'$id'}]["gast"] = array();
					$array["items"][$item["organizacion"]["_id"]->{'$id'}]["acum_gast"] = array();
				}
				if(!isset($cursor2["clasificadores"]["hijos"])){
					if($item["tipo"]=="G"){//Gastos
						array_push($array["items"][$item["organizacion"]["_id"]->{'$id'}]["gast"],$item["compromiso"]);
						array_push($array["items"][$item["organizacion"]["_id"]->{'$id'}]["acum_gast"],$item["acumulado_comp"]);
					}elseif($item["tipo"]=="I"){//Ingresos
						array_push($array["items"][$item["organizacion"]["_id"]->{'$id'}]["ingr"],$item["ingreso"]);
						array_push($array["items"][$item["organizacion"]["_id"]->{'$id'}]["acum_ingr"],$item["acumulado"]);
					}
				}
			}
		}
		$array["filtros"] = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);
		$f->response->view("ct/repo.bala.print",$array);
	}
	function execute_bala2(){
		global $f;
		$ing = $f->model("ct/auxi")->params(array(
			"ano"=>$f->request->ano,
			"hasta_mes"=>$f->request->mes,
			//"fuente"=>$f->request->fuente,
			//"clasificador"=>$f->request->clasificador
		))->get("all");
		$gas = $f->model("ct/auxg")->params(array(
			"ano"=>$f->request->ano,
			"hasta_mes"=>$f->request->mes,
			//"fuente"=>$f->request->fuente,
			//"clasificador"=>$f->request->clasificador
		))->get("all");
		$aux = array();
		$index = 0;
		if($ing->items!=null){
			foreach($ing->items as $item){
				$aux[$index] = $item;
				$aux[$index]["tipo"] = "I";
				$index++;
			}
		}
		if($gas->items!=null){
			foreach($gas->items as $item){
				$aux[$index] = $item;
				$aux[$index]["tipo"] = "G";
				$index++;
			}
		}	
		/** Data Final */
		$array = array();
		foreach($aux as $item){
			if(!isset($array[$item["saldo"]["organizacion"]["_id"]->{'$id'}])){
				$array[$item["saldo"]["organizacion"]["_id"]->{'$id'}] = $item["saldo"]["organizacion"];
				$array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["ingr"] = array();
				$array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["acum_ingr"] = array();
				$array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["gast"] = array();
				$array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["acum_gast"] = array();
			}
			switch($item["tipo"]){
				case "I":
					if($item["saldo"]["periodo"]["mes"]==floatval($f->request->mes)){
						array_push($array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["ingr"],$item["ejec_pres"]["monto"]);
					}
					array_push($array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["acum_ingr"],$item["ejec_pres"]["monto"]);
					break;
				case "G":
					if($item["saldo"]["periodo"]["mes"]==floatval($f->request->mes)){
						array_push($array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["gast"],$item["ejec_pres"]["monto"]);
					}
					array_push($array[$item["saldo"]["organizacion"]["_id"]->{'$id'}]["acum_gast"],$item["ejec_pres"]["monto"]);
					break;
			}
		}
		/** ./Data Final */
		//print_r($array);die();
		$filtros = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);
		$f->response->view("ct/repo.bala.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_est_situ(){
		global $f;
		$array["items"] = array();
		$model = $f->model("ct/nota")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano,"documento"=>"S"))->get("all_num");
		$array["items"]["activo_co"] = array();
		$array["items"]["activo_no"] = array();
		$array["items"]["pasivo_co"] = array();
		$array["items"]["pasivo_no"] = array();
		$array["items"]["pasivo_pa"] = array();
		foreach($model->items as $i=>$item){
			if(isset($item["otros"])){
				$monto = 0;
				foreach($item["otros"] as $otros){
					if($otros["ultimo"]==false){
						$monto = $monto + $otros["monto"];
					}
				}
			}elseif(isset($item["activos"])){
				$monto = 0;
				foreach($item["activos"] as $activos){
					if($activos["ultimo"]==false){
						$monto = $monto + $activos["valor_neto"];
					}
				}
			}
			$model->items[$i]["monto"] = $monto;
			if($item["clase"]=="A" && $item["subclase"]=="C"){
				array_push($array["items"]["activo_co"],$model->items[$i]);
			}elseif($item["clase"]=="A" && $item["subclase"]=="N"){
				array_push($array["items"]["activo_no"],$model->items[$i]);
			}elseif($item["clase"]=="P" && $item["subclase"]=="C"){
				array_push($array["items"]["pasivo_co"],$model->items[$i]);
			}elseif($item["clase"]=="P" && $item["subclase"]=="N"){
				array_push($array["items"]["pasivo_no"],$model->items[$i]);
			}elseif($item["clase"]=="P" && $item["subclase"]=="P"){
				array_push($array["items"]["pasivo_pa"],$model->items[$i]);
			}
		}
		$array["filtros"] = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);	
		$f->response->view("ct/repo.est.situ",$array);
	}
	function execute_est_gest(){
		global $f;
		$array["items"] = array();
		$model = $f->model("ct/nota")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano,"documento"=>"G"))->get("all_num");
		$array["items"]["ingresos"] = array();
		$array["items"]["gastos"] = array();
		$array["items"]["otros"] = array();
		foreach($model->items as $i=>$item){
			if(isset($item["otros"])){
				$monto = 0;
				foreach($item["otros"] as $otros){
					if($otros["ultimo"]==false){
						$monto = $monto + $otros["monto"];
					}
				}
			}elseif(isset($item["activos"])){
				$monto = 0;
				foreach($item["activos"] as $activos){
					if($activos["ultimo"]==false){
						$monto = $monto + $activos["valor_neto"];
					}
				}
			}
			$model->items[$i]["monto"] = $monto;
			if($item["clase"]=="I"){
				array_push($array["items"]["ingresos"],$model->items[$i]);
			}elseif($item["clase"]=="G"){
				array_push($array["items"]["gastos"],$model->items[$i]);
			}elseif($item["clase"]=="O"){
				array_push($array["items"]["otros"],$model->items[$i]);
			}
		}
		$array["filtros"] = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);	
		$f->response->view("ct/repo.est.gest",$array);
	}
	function execute_est_lit(){
		global $f;
		$model = $f->model("ct/nota")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all_lit");
		$model->filtros = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);
		$f->response->view("ct/repo.est.lit",$model);
	}
	function execute_est_num(){
		global $f;
		$model = $f->model("ct/nota")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all_num");
		$model->filtros = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);
		$f->response->view("ct/repo.est.num",$model);
	}
	function execute_aux_gast(){
		global $f;
		$model = $f->model("ct/auxg")->params(array(
			"ano"=>$f->request->ano,		
			"organizacion"=>$f->request->organizacion,
			"fuente"=>$f->request->fuente,
			"clasificador"=>$f->request->clasificador
		))->get("all");
		if($model->items==null)$model->items = array();
		$model->filtros = array(
			"ano"=>$f->request->ano,		
			"organizacion"=>$f->request->orga_nomb,
			"fuente"=>$f->request->fuen_nomb,
			"clasificador"=>$f->request->clas_nomb
		);
		$f->response->view("ct/repo.auxg.export",$model);
	}
	function execute_aux_ingr(){
		global $f;
		$model = $f->model("ct/auxi")->params(array(
			"ano"=>$f->request->ano,		
			"organizacion"=>$f->request->organizacion,
			"fuente"=>$f->request->fuente,
			"clasificador"=>$f->request->clasificador
		))->get("all");
		if($model->items==null)$model->items = array();
		$model->filtros = array(
			"ano"=>$f->request->ano,		
			"organizacion"=>$f->request->orga_nomb,
			"fuente"=>$f->request->fuen_nomb,
			"clasificador"=>$f->request->clas_nomb
		);
		$f->response->view("ct/repo.auxi.print",$model);
	}
	function execute_notc(){
		global $f;
		$model = $f->model("ct/notc")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$model->filtros = array(
				"nro"=>$model->items["num"],
				"mes"=>$model->items["periodo"]["mes"],
				"ano"=>$model->items["periodo"]["ano"]
			);
			$f->response->view("ct/repo.notc.print",$model);
		}else{
			$f->response->print("Ha ocurrido un Error: No se ha encontrado la nota de contabilidad");
		}
	}
	function execute_cuad_comg(){
		global $f;
		$model = $f->model("ct/comej")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("lista");
		if($model->items!=null){
			$array["items"] = array();
			$array["items"][0] = array();//compromiso
			$array["items"][1] = array();//ejecucion
			foreach($model->items as $item){
				if($item["tipo"]=="C"){
					array_push($array["items"][0],$item);
				}elseif($item["tipo"]=="E"){
					array_push($array["items"][1],$item);
				}
			}
			$array["filtros"] = array(
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano
			);
			$f->response->view("ct/repo.cuad.comg",$array);
		}else{
			$f->response->print("No hay data disponible");
		}
	}
	function execute_efin_cierre(){
		global $f;
		$model1 = $f->model("ct/nota")->params(array("ano"=>$f->request->ano))->get("all_num");
		$model2 = $f->model("ct/nota")->params(array("ano"=>"".floatval($f->request->ano-1)))->get("all_num");
			$array["items"] = array();
			if($model1->items!=null){
				foreach($model1->items as $item){
					if(!isset($array["items"][$item["num"]])){
						$array["items"][$item["num"]] = array();
						$array["items"][$item["num"]]["num"] = $item["num"];
						$array["items"][$item["num"]]["nomb"] = $item["nomb"];
						$array["items"][$item["num"]]["cuentas"] = array();
					}
					if(isset($item["otros"])){
						foreach($item["otros"] as $otros){
							if(!isset($array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}])){
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}] = array();
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["cuenta"] = $otros["cuenta"];
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["ano1"] = array();
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["ano2"] = array();
							}
							array_push($array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["ano1"],$otros["monto"]);
						}
					}elseif(isset($item["activos"])){
						foreach($item["activos"] as $activos){
							if(!isset($array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}])){
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}] = array();
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["cuenta"] = $activos["cuenta"];
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["ano1"] = array();
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["ano2"] = array();
							}
							array_push($array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["ano1"],$activos["valor_bruto"]);
						}
					}
				}
			}
			if($model2->items!=null){
				foreach($model2->items as $item){
					if(!isset($array["items"][$item["num"]])){
						$array["items"][$item["num"]] = array();
						$array["items"][$item["num"]]["num"] = $item["num"];
						$array["items"][$item["num"]]["nomb"] = $item["nomb"];
						$array["items"][$item["num"]]["cuentas"] = array();
					}
					if(isset($item["otros"])){
						foreach($item["otros"] as $otros){
							if(!isset($array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}])){
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}] = array();
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["cuenta"] = $otros["cuenta"];
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["ano1"] = array();
								$array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["ano2"] = array();
							}
							array_push($array["items"][$item["num"]]["cuentas"][$otros["cuenta"]["_id"]->{'$id'}]["ano2"],$otros["monto"]);
						}
					}elseif(isset($item["activos"])){
						foreach($item["activos"] as $activos){
							if(!isset($array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}])){
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}] = array();
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["cuenta"] = $activos["cuenta"];
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["ano1"] = array();
								$array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["ano2"] = array();
							}
							array_push($array["items"][$item["num"]]["cuentas"][$activos["cuenta"]["_id"]->{'$id'}]["ano2"],$activos["valor_bruto"]);
						}
					}
				}
			}
			$array["filtros"] = array(
					"ano1"=>$f->request->ano,
					"ano2"=>$f->request->ano-1
			);
			$f->response->view("ct/repo.efin.cierre",$array);		
	}
	function execute_cban(){
		global $f;
		$model = $f->model("ct/cban")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all");
		$model->filtros = array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);
		if($model->items!=null){
			$f->response->view("ct/repo.cban.print",$model);
		}else{
			$f->response->print("No hay data disponible");
		}
	}
	function execute_reg_comp(){
		global $f;
		$model = $f->model("ct/rcom")->params(array("ano"=>$f->request->ano,"mes"=>$f->request->mes))->get("all");
		if($model->items!=null){
			$model->filtros = array(
				"ano"=>$f->request->ano,
				"mes"=>$f->request->mes
			);
			$f->response->view("ct/repo.rcom.export",$model);
		}else{
			$f->response->print("No hay data Disponible");
		}
	}
	function execute_reg_vent(){
		global $f;
		$model = $f->model("ct/rven")->params(array("ano"=>$f->request->ano,"mes"=>$f->request->mes))->get("all");
		if($model->items!=null){
			$model->filtros = array(
				"ano"=>$f->request->ano,
				"mes"=>$f->request->mes
			);
			$f->response->view("ct/repo.rven.export",$model);
		}else{
			$f->response->print("No hay data Disponible");
		}
	}
	function execute_auxs(){
		global $f;
		$data = $f->request->data;
		if(isset($data['orga'])) $data['orga'] = new MongoId($data['orga']);
		if(isset($data['inmueble'])) $data['inmueble'] = new MongoId($data['inmueble']);
		if(isset($data['cuma'])) $data['cuma'] = new MongoId($data['cuma']);
		if(isset($data['cusu'])) $data['cusu'] = new MongoId($data['cusu']);
		$params = array(
			'ano'=>$data['ano'],
			'cuenta_mayor'=>$data['cuma'],
			'sub_cuenta'=>$data['cusu']
		);
		if(isset($data['orga'])) $params['organizacion'] = $data['orga'];
		if(isset($data['inmueble'])) $params['inmueble'] = $data['inmueble'];
		$saldo = $f->model('ct/saux')->params($params)->get('saldo')->items;
		$items = $f->model("ct/auxs")->params($params)->get("lista")->items;
		if($saldo!=null){
			$f->response->view("ct/repo.auxs.print",array(
				'saldo'=>$saldo,
				'items'=>$items
			));
		}else{
			$f->response->print("No hay data disponible");
		}
	}
	function execute_auxs_export(){
		global $f;
		$data = $f->request->data;
		if(isset($data['orga'])) $data['orga'] = new MongoId($data['orga']);
		if(isset($data['inmueble'])) $data['inmueble'] = new MongoId($data['inmueble']);
		if(isset($data['cuma'])) $data['cuma'] = new MongoId($data['cuma']);
		if(isset($data['cusu'])) $data['cusu'] = new MongoId($data['cusu']);
		$params = array(
			'ano'=>$data['ano'],
			'cuenta_mayor'=>$data['cuma'],
			'sub_cuenta'=>$data['cusu']
		);
		if(isset($data['orga'])) $params['organizacion'] = $data['orga'];
		if(isset($data['inmueble'])) $params['inmueble'] = $data['inmueble'];
		$saldo = $f->model('ct/saux')->params($params)->get('saldo')->items;
		$items = $f->model("ct/auxs")->params($params)->get("lista")->items;
		if($saldo!=null){
			$f->response->view("ct/repo.auxs.export",array(
				'saldo'=>$saldo,
				'items'=>$items,
				'periodo'=>$data['ano']
			));
		}else{
			$f->response->print("No hay data disponible");
		}
	}
	function execute_movi(){
		global $f;
		$model = $f->model("ct/movi")->params(array(
			"mes"=>floatval($f->request->mes),
			"ano"=>$f->request->ano
		))->get("lista");
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				$cursor2 = $f->model("ct/pcon")->params(
					array(
						"_id"=>$item["cuenta"]["_id"],						
					)
				)->get("one")->items;
				$model->items[$i]["cuenta"] = $cursor2;
			}
		}
		$model->filtros = array(
			"mes"=>floatval($f->request->mes),
			"ano"=>$f->request->ano
		);
		$f->response->view("ct/repo.movi.export",$model);
	}
	function execute_bala_cons2(){
		global $f;
		$model = $f->model("ct/pcon")->get("lista");
		$periodo = $f->request->periodo;
		$last_periodo = "".(floatval($periodo)-1);
		foreach($model->items as $i=>$item){
			$cod = $item['cod'];
			$sig = substr_count($cod,'.');
			$tot = (strlen($cod)-$sig);
			if($tot!=1){
				$auxs = $f->model("ct/auxs")->params(array("ano"=>$periodo,"cuenta"=>$cod))->get("lista_bala")->items;
				$auxs_last = $f->model("ct/auxs")->params(array("ano"=>$last_periodo,"cuenta"=>$cod))->get("lista_bala")->items;
				$model->items[$i]["last_d"] = array();
				$model->items[$i]["last_h"] = array();
				if($auxs_last!=null){
					foreach($auxs_last as $auxl){
						if($auxl["tipo"]=="D")array_push($model->items[$i]["last_d"],$auxl["monto"]);
						elseif($auxl["tipo"]=="H")array_push($model->items[$i]["last_h"],$auxl["monto"]);
					}
				}
				$model->items[$i]["this_d"] = array();
				$model->items[$i]["this_h"] = array();
				if($auxs!=null){
					foreach($auxs as $aux){
						if($aux["tipo"]=="D")array_push($model->items[$i]["this_d"],$aux["monto"]);
						elseif($aux["tipo"]=="H")array_push($model->items[$i]["this_h"],$aux["monto"]);
					}
				}else{
					$model->items[$i] = null;
					//eliminamos cuenta para no imprimirla
				}
			}else{
				$model->items[$i] = null;
			}
			
		}
		$model->items = array_values(array_filter($model->items));
		$model->filtros = array(
			"periodo"=>$f->request->periodo
		);
		$f->response->view("ct/repo.bala_cons.print", $model);
	}
	function execute_epresi(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"I","estado"=>"H"))->get("lista");
		$array = array();
		foreach($model->items as $i=>$item){
			$auxs = $f->model("ct/auxi")->params(array(
				"clasificador"=>$item["cod"],
				"organizacion"=>new MongoId($f->request->organizacion),
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"estado"=>"C"
			))->get("all_filter");
			$acum = $f->model("ct/auxi")->params(array(
				"clasificador"=>$item["cod"],
				"organizacion"=>new MongoId($f->request->organizacion),
				"hasta_mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"estado"=>"C"
			))->get("all_filter");
			$pim = $cursor2=$f->model("pr/pres")->params(array(
				"page"=>"1",
				"page_rows"=>999999,
				"cod"=>$item["cod"],
				"tipo"=>null,
				"periodo"=>$f->request->ano,
				"mes"=>"0",
				"organizacion"=>$f->request->organizacion,
				"etapa"=>""
			))->get("search");
			if($pim->items!=null){			
				$model->items[$i]["pim"] = array();			
				foreach($pim->items as $pim_row){
					if(!isset($array[$pim_row["fuente"]["_id"]])){
						$array[$pim_row["fuente"]["_id"]] = $pim_row["fuente"];
						$array[$pim_row["fuente"]["_id"]]["items"] = array();
						$array[$pim_row["fuente"]["_id"]]["metas"] = array();
						$array[$pim_row["fuente"]["_id"]]["total_pim"] = array();
						$array[$pim_row["fuente"]["_id"]]["total_eje"] = array();
						$array[$pim_row["fuente"]["_id"]]["total_acu"] = array();
					}
					if(isset($pim_row["meta"])){
						if(!isset($array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]])){
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["meta"] = $pim_row["meta"];
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"] = array();
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["ejecucion"] = array();
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["acumulado"] = array();
						}
						array_push($array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"],$pim_row["importe"]);
					}else{
						if(!isset($array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"] = array();
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["ejecucion"] = array();
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["acumulado"] = array();
						}				
						array_push($array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"],$pim_row["importe"]);
						if(strlen($item["cod"])==3){
							array_push($array[$pim_row["fuente"]["_id"]]["total_pim"],$pim_row["importe"]);
						}			
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$pim_row["clasificador"]["cod"]){
								unset($array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}	
				}
				if($auxs->items!=null){
					//$model->items[$i]["ejecucion"] = array();
					foreach($auxs->items as $j=>$aux){
						//array_push($model->items[$i]["ejecucion"],$aux["ejec_pres"]["monto"]);
						array_push($array[$aux["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["ejecucion"],$aux["ejec_pres"]["monto"]);
						if(strlen($item["cod"])==3){
							array_push($array[$aux["saldo"]["fuente"]["_id"]->{'$id'}]["total_eje"],$aux["ejec_pres"]["monto"]);
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$aux["saldo"]["subespecifica"]["cod"]){
								unset($array[$aux["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}
				if($acum->items!=null){
					//$model->items[$i]["acumulado"] = array();
					foreach($acum->items as $k=>$ac){
						//array_push($model->items[$i]["acumulado"],$ac["ejec_pres"]["monto"]);
						array_push($array[$ac["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["acumulado"],$ac["ejec_pres"]["monto"]);
						if(strlen($item["cod"])==3){
							array_push($array[$ac["saldo"]["fuente"]["_id"]->{'$id'}]["total_acu"],$ac["ejec_pres"]["monto"]);
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$ac["saldo"]["subespecifica"]["cod"]){
								unset($array[$ac["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}	
			}else{
				$model->items[$i] = null;
			}
		}
		$model->items = array_values(array_filter($model->items));
		$f->response->view("ct/repo.proi.print",array("items"=>$array,"filtros"=>array("ano"=>$f->request->ano,"mes"=>$f->request->mes,"organomb"=>$f->request->organomb)));
	}
	function execute_epresg(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"G","estado"=>"H"))->get("lista");
		$array = array();
		foreach($model->items as $i=>$item){
			$auxs = $f->model("ct/auxg")->params(array(
				"clasificador"=>$item["cod"],
				"organizacion"=>new MongoId($f->request->organizacion),
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"estado"=>"C"
			))->get("all_filter");
			$acum = $f->model("ct/auxg")->params(array(
				"clasificador"=>$item["cod"],
				"organizacion"=>new MongoId($f->request->organizacion),
				"hasta_mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"estado"=>"C"
			))->get("all_filter");
			$pim = $cursor2=$f->model("pr/pres")->params(array(
				"page"=>"1",
				"page_rows"=>999999,
				"cod"=>$item["cod"],
				"tipo"=>null,
				"periodo"=>$f->request->ano,
				"mes"=>"0",
				"organizacion"=>$f->request->organizacion,
				"etapa"=>""
			))->get("search");
			if($pim->items!=null){			
				$model->items[$i]["pim"] = array();			
				foreach($pim->items as $pim_row){
					if(!isset($array[$pim_row["fuente"]["_id"]])){
						$array[$pim_row["fuente"]["_id"]] = $pim_row["fuente"];
						$array[$pim_row["fuente"]["_id"]]["items"] = array();
						$array[$pim_row["fuente"]["_id"]]["metas"] = array();
						$array[$pim_row["fuente"]["_id"]]["total_pim"] = array();
						$array[$pim_row["fuente"]["_id"]]["total_eje"] = array();
						$array[$pim_row["fuente"]["_id"]]["total_acu"] = array();
					}
					if(isset($pim_row["meta"])){
						if(!isset($array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]])){
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["meta"] = $pim_row["meta"];
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"] = array();
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["ejecucion"] = array();
							$array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["acumulado"] = array();
						}
						array_push($array[$pim_row["fuente"]["_id"]]["metas"][$pim_row["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"],$pim_row["importe"]);
					}else{
						if(!isset($array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"] = array();
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["ejecucion"] = array();
							$array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["acumulado"] = array();
						}				
						array_push($array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pim"],$pim_row["importe"]);
						if(strlen($item["cod"])==3){
							array_push($array[$pim_row["fuente"]["_id"]]["total_pim"],$pim_row["importe"]);
						}			
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$pim_row["clasificador"]["cod"]){
								unset($array[$pim_row["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}	
				}
				if($auxs->items!=null){
					//$model->items[$i]["ejecucion"] = array();
					foreach($auxs->items as $j=>$aux){
						//array_push($model->items[$i]["ejecucion"],$aux["ejec_pres"]["monto"]);
						array_push($array[$aux["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["ejecucion"],$aux["ejec_pres"]["monto"]);
						if(strlen($item["cod"])==3){
							array_push($array[$aux["saldo"]["fuente"]["_id"]->{'$id'}]["total_eje"],$aux["ejec_pres"]["monto"]);
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$aux["saldo"]["especifica"]["cod"]){
								unset($array[$aux["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}
				if($acum->items!=null){
					//$model->items[$i]["acumulado"] = array();
					foreach($acum->items as $k=>$ac){
						//array_push($model->items[$i]["acumulado"],$ac["ejec_pres"]["monto"]);
						array_push($array[$ac["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]["acumulado"],$ac["ejec_pres"]["monto"]);
						if(strlen($item["cod"])==3){
							array_push($array[$ac["saldo"]["fuente"]["_id"]->{'$id'}]["total_acu"],$ac["ejec_pres"]["monto"]);
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$ac["saldo"]["especifica"]["cod"]){
								unset($array[$ac["saldo"]["fuente"]["_id"]->{'$id'}]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}	
			}else{
				$model->items[$i] = null;
			}
		}
		$model->items = array_values(array_filter($model->items));
		$f->response->view("ct/repo.proi.print",array("items"=>$array,"filtros"=>array("ano"=>$f->request->ano,"mes"=>$f->request->mes,"organomb"=>$f->request->organomb)));
	}
	function execute_bala_cons(){
		global $f;
		$model = $f->model("ct/pcon")->get("lista");
		$periodo = floatval($f->request->ano);
		$hasta_mes = floatval($f->request->hasta_mes);
		$aper_ct = array();
		$acum_ct = array();	
		$aper = $f->model("ct/notc")->params(array("ano"=>$periodo,"num"=>"1"))->get("lista_bala")->items;
		$acum = $f->model("ct/notc")->params(array("ano"=>$periodo,"hasta_mes"=>$hasta_mes))->get("lista_bala")->items;
		if($aper!=null){
			foreach($aper as $nota){
				foreach($nota["cuentas"] as $cuenta){
					if($cuenta["ultimo"]==true){
						if(!isset($aper_ct[$cuenta["cuenta"]["cod"]])){
							$aper_ct[$cuenta["cuenta"]["cod"]] = $cuenta;
							$aper_ct[$cuenta["cuenta"]["cod"]]["monto_d"] = array();
							$aper_ct[$cuenta["cuenta"]["cod"]]["monto_h"] = array();
						}
						if($cuenta["tipo"]=="D"){
							array_push($aper_ct[$cuenta["cuenta"]["cod"]]["monto_d"],$cuenta["monto"]);
						}elseif($cuenta["tipo"]=="H"){
							array_push($aper_ct[$cuenta["cuenta"]["cod"]]["monto_h"],$cuenta["monto"]);
						}
					}
				}
			}
		}
		if($acum!=null){
			foreach($acum as $nota){
				foreach($nota["cuentas"] as $cuenta){
					if($cuenta["ultimo"]==true){
						if(!isset($acum_ct[$cuenta["cuenta"]["cod"]])){
							$acum_ct[$cuenta["cuenta"]["cod"]] = $cuenta;
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_d"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_h"] = array();
						}
						if($cuenta["tipo"]=="D"){
							array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_d"],$cuenta["monto"]);
						}elseif($cuenta["tipo"]=="H"){
							array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_h"],$cuenta["monto"]);
						}
					}
				}
			}
		}
		$array["items"] = array();		
		$c = 0;
		foreach($model->items as $i=>$item){
			$find_acum = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $acum_ct ));
			if(count($find_acum)>0){
				$cuenta = $item;
				$cuenta["aper_d"] = 0;
				$cuenta["aper_h"] = 0;
				$cuenta["acum_d"] = 0;
				$cuenta["acum_h"] = 0;
				foreach($find_acum as $row){
					$cuenta["acum_d"] += array_sum($acum_ct[$row]["monto_d"]);
					$cuenta["acum_h"] += array_sum($acum_ct[$row]["monto_h"]);
				}
				$find_aper = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $aper_ct ));
				if(count($find_aper)>0){
					foreach($find_aper as $row){
						$cuenta["aper_d"] += array_sum($aper_ct[$row]["monto_d"]);
						$cuenta["aper_h"] += array_sum($aper_ct[$row]["monto_h"]);
					}
				}
				$array["items"][$item["cod"]] = $cuenta;
			}
		}
		$array["filtros"] = array(
			"ano"=>$periodo,
			"mes"=>$hasta_mes
		);
		$f->response->view("ct/repo.bala_cons.print",$array);
	}
	function execute_bala_comp(){
		global $f;
		$model = $f->model("ct/pcon")->get("lista");
		$periodo = floatval($f->request->ano);
		$hasta_mes = floatval($f->request->hasta_mes);
		$acum_ct = array();	
		$acum = $f->model("ct/notc")->params(array("ano"=>$periodo,"hasta_mes"=>$hasta_mes))->get("lista_bala")->items;
		if($acum!=null){
			foreach($acum as $nota){
				foreach($nota["cuentas"] as $cuenta){
					if($cuenta["ultimo"]==true){
						if(!isset($acum_ct[$cuenta["cuenta"]["cod"]])){
							$acum_ct[$cuenta["cuenta"]["cod"]] = $cuenta;
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_d"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_h"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_d"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_h"] = array();
						}
						if($cuenta["tipo"]=="D"){
							if($nota["periodo"]["mes"]==floatval($hasta_mes)){
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_d"],$cuenta["monto"]);
							}else{
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_d"],$cuenta["monto"]);
							}	
						}elseif($cuenta["tipo"]=="H"){
							if($nota["periodo"]["mes"]==floatval($hasta_mes)){
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_h"],$cuenta["monto"]);
							}else{
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_h"],$cuenta["monto"]);
							}	
						}
					}
				}
			}
		}
		$array["items"] = array();		
		$c = 0;
		foreach($model->items as $i=>$item){
			$find_acum = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $acum_ct ));
			if(count($find_acum)>0){
				$cuenta = $item;
				$cuenta["last_d"] = 0;
				$cuenta["last_h"] = 0;
				$cuenta["this_d"] = 0;
				$cuenta["this_h"] = 0;
				foreach($find_acum as $row){
					$cuenta["last_d"] += array_sum($acum_ct[$row]["monto_last_d"]);
					$cuenta["last_h"] += array_sum($acum_ct[$row]["monto_last_h"]);
					$cuenta["this_d"] += array_sum($acum_ct[$row]["monto_this_d"]);
					$cuenta["this_h"] += array_sum($acum_ct[$row]["monto_this_h"]);
				}
				$array["items"][$item["cod"]] = $cuenta;
			}
		}
		$array["filtros"] = array(
			"ano"=>$periodo,
			"mes"=>$hasta_mes
		);
		$f->response->view("ct/repo.bala_comp.print",$array);
	}
	function execute_movi_cuen(){
		global $f;
		$model = $f->model("ct/pcon")->params(array("tipo"=>$f->request->tipo))->get("lista");
		$periodo = $f->request->ano;
		$hasta_mes = $f->request->hasta_mes;
		$acum_ct = array();	
		$acum = $f->model("ct/notc")->params(array("ano"=>$periodo,"hasta_mes"=>$hasta_mes))->get("lista_bala")->items;
		if($acum!=null){
			foreach($acum as $nota){
				foreach($nota["cuentas"] as $cuenta){
					if($cuenta["ultimo"]==true){
						if(!isset($acum_ct[$cuenta["cuenta"]["cod"]])){
							$acum_ct[$cuenta["cuenta"]["cod"]] = $cuenta;
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_d"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_h"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_d"] = array();
							$acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_h"] = array();
						}
						if($cuenta["tipo"]=="D"){
							if($nota["periodo"]["mes"]==floatval($hasta_mes)){
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_d"],$cuenta["monto"]);
							}else{
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_d"],$cuenta["monto"]);
							}	
						}elseif($cuenta["tipo"]=="H"){
							if($nota["periodo"]["mes"]==floatval($hasta_mes)){
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_this_h"],$cuenta["monto"]);
							}else{
								array_push($acum_ct[$cuenta["cuenta"]["cod"]]["monto_last_h"],$cuenta["monto"]);
							}	
						}
					}
				}
			}
		}
		$array["items"] = array();		
		$c = 0;
		foreach($model->items as $i=>$item){
			$find_acum = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $acum_ct ));
			if(count($find_acum)>0){
				$cuenta = $item;
				$cuenta["last_d"] = 0;
				$cuenta["last_h"] = 0;
				$cuenta["this_d"] = 0;
				$cuenta["this_h"] = 0;
				foreach($find_acum as $row){
					$cuenta["last_d"] += array_sum($acum_ct[$row]["monto_last_d"]);
					$cuenta["last_h"] += array_sum($acum_ct[$row]["monto_last_h"]);
					$cuenta["this_d"] += array_sum($acum_ct[$row]["monto_this_d"]);
					$cuenta["this_h"] += array_sum($acum_ct[$row]["monto_this_h"]);
				}
				$array["items"][$item["cod"]] = $cuenta;
			}
		}
		$array["filtros"] = array(
			"tipo"=>$f->request->tipo,
			"ano"=>floatval($periodo),
			"mes"=>floatval($hasta_mes)
		);
		$f->response->view("ct/repo.movi_cuen.print",$array);
	}
	function execute_notas(){
		global $f;
		$model = $f->model("ct/notc")->get("all");
		foreach($model->items as $item){
			$flag = false;
			foreach($item["cuentas"] as $cuenta){
				if($cuenta["ultimo"]==true)$flag = true;
			}
			if(!$flag){
				$f->response->print($item["num"]);
				echo "\n";
			}
		}
	}
}
?>