<?php
class Controller_pr_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pr/repo.grid");
	}
	/*	function execute_acti(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>"","periodo"=>$f->request->periodo))->get("lista");
		$rows->items = array();
		foreach($model->items as $item){
			if(isset($rows->items[$item["organizacion"]["_id"]])){
				array_push($rows->items[$item["organizacion"]["_id"]]["items"],$item);
			}else{
				//$rows->items[$item["organizacion"]["_id"]] = $item["organizacion"];
				$rows->items[$item["organizacion"]["_id"]] = $f->model("mg/orga")->params(array("_id"=>new MongoId($item["organizacion"]["_id"])))->get("one")->items;
				$rows->items[$item["organizacion"]["_id"]]["items"] = array();
				array_push($rows->items[$item["organizacion"]["_id"]]["items"],$item);
			}
		}	
		$model->items = array_values($rows->items);	
		$model->filtros = $f->request->data;	
		//$f->response->json($model->items);
		$f->response->view("pr/repo.acti.print",$model);
	}
	*/
	function execute_acti_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>"","periodo"=>$f->request->periodo))->get("lista_v1");
		$rows = new stdClass();
		$rows->items = array();
		foreach($model->items as $item){
			//if(isset($rows->items[$item["programa"]["_id"]])){
			if(isset($rows->items[$item["programa"]["_id"]->{'$id'}])){
				//array_push($rows->items[$item["programa"]["_id"]]["items"],$item);
				array_push($rows->items[$item["programa"]["_id"]->{'$id'}]["items"],$item);
			}else{
				//$rows->items[$item["programa"]["_id"]] = $item["programa"];

				//$rows->items[$item["programa"]["_id"]] = $f->model("mg/prog")->params(array("_id"=>new MongoId($item["programa"]["_id"])))->get("one")->items;
				$rows->items[$item["programa"]["_id"]->{'$id'}] = $f->model("mg/prog")->params(array("_id"=>new MongoId($item["programa"]["_id"])))->get("one")->items;
				//$rows->items[$item["programa"]["_id"]]["items"] = array();
				$rows->items[$item["programa"]["_id"]->{'$id'}]["items"] = array();
				//array_push($rows->items[$item["programa"]["_id"]]["items"],$item);
				array_push($rows->items[$item["programa"]["_id"]->{'$id'}]["items"],$item);
			}
		}	
		$model->items = array_values($rows->items);	
		$model->filtros = $f->request->data;	
		//$f->response->json($model->items);
		$f->response->view("pr/repo.acti.print",$model);
	}
	/*function execute_pmen(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		if($f->request->fuente==""){
			$fuentes->items = array(
				0=>array(
					"cod"=>"",
					"rubro"=>"TODAS LAS FUENTES DE FINANCIAMIENTO"
				)
			);
		}		
		for($i=0;$i<count($model->items);$i++){
			$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"A","clasificador"=>$f->request->clasificador);
			if($f->request->fuente!="") $params["fuente"] = $f->request->fuente;
			if(isset($f->request->data["meta"])){
				$params["meta"] = $f->request->meta;
			}
			$cursor2=$f->model("pr/pres")->params($params)->get("search");
			$model->items[$i]["importes"]=array();
			if(isset($cursor2->items)){
				foreach($fuentes->items as $index=>$fuen){					
					$model->items[$i]["importes"][$index]=array();
					for($k=1;$k<=12;$k++){
						$model->items[$i]["importes"][$index][$k]=array();
					}
					for($j=0;$j<count($cursor2->items);$j++){
						if($f->request->fuente==""){
							$cursor2->items[$j]["fuente"]["cod"]="";
						}
						if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
							array_push($model->items[$i]["importes"][$index][$cursor2->items[$j]["periodo"]["mes"]], $cursor2->items[$j]["importe"]);						
						}else{
							array_push($model->items[$i]["importes"][$index][$cursor2->items[$j]["periodo"]["mes"]], "0");
						}
					}
				}
			}else{
				$model->items[$i]=null;
			}
		}
		$model->items = array_values(array_filter($model->items));
		$model->p_fuentes = $fuentes->items;
		$model->filtros = $f->request->data;
		$model->num_fuen = count($fuentes->items)+3;
		$f->response->view('pr/repo.pmen.print',$model );
	}*/
	function execute_pmen(){
		global $f;
		set_time_limit(0);
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$orga = $f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->organizacion)))->get("one")->items;
		$orga_filtro = null;
		if(isset($orga["organizaciones"]["hijos"])){
			$orga_filtro = array();
			foreach($orga["organizaciones"]["hijos"] as $i=>$hijo){
				$orga_filtro[$i]["organizacion._id"] = $hijo->{'$id'};
			}
		}

		$array = array();
		foreach($model->items as $i=>$item){
			$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"etapa"=>$f->request->etapa,"clasificador"=>$f->request->clasificador);
			if($f->request->fuente!="") $params["fuente"] = $f->request->fuente;
			if(isset($f->request->data["meta"])){
				$params["meta"] = $f->request->meta;
			}
			if($orga_filtro==null){
				$params["organizacion"] = $f->request->organizacion;
			}else{
				$params["orga_group"] = $orga_filtro;
			}
			$pres=$f->model("pr/pres")->params($params)->get("search");
			if(isset($pres->items)){
				foreach($pres->items as $part){
					if(!isset($array[$part["fuente"]["_id"]])){
						$array[$part["fuente"]["_id"]]["fuente"] = $part["fuente"];
						$array[$part["fuente"]["_id"]]["items"] = array();
						$array[$part["fuente"]["_id"]]["metas"] = array();
					}
					if(isset($part["meta"])){
						if(!isset($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]])){
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["meta"] = $part["meta"];
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = array();
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = array();
							for($k=1;$k<=12;$k++){
								$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$k]=array();
							}
						}
						array_push($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$part["periodo"]["mes"]],$part["importe"]);
						if($part["etapa"]=="A"){
							array_push($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"],$part["importe"]);	
						}
					}else{
						if(!isset($array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = array();
							$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = array();
							for($k=1;$k<=12;$k++){
								$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$k]=array();
							}
						}
						array_push($array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$part["periodo"]["mes"]],$part["importe"]);
						if($part["etapa"]=="A"){
							array_push($array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"],$part["importe"]);	
						}
					}
				}
			}
		}
		//$model->filtros = $f->request->data;
		$f->response->view('pr/repo.pmen.print',array("items"=>$array,"filtros"=>$f->request->data) );
		//$f->response->json($array);
	}
	function execute_pmen_v1(){
		global $f;
		set_time_limit(0);
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$prog = $f->model("mg/prog")->params(array("_id"=>new MongoId($f->request->programa)))->get("one")->items;
		$orga_filtro = null;
		if(isset($orga["programa"]["hijos"])){
			$orga_filtro = array();
			foreach($orga["programa"]["hijos"] as $i=>$hijo){
				$orga_filtro[$i]["programa._id"] = $hijo->{'$id'};
			}
		}
	 
		$array = array();
		foreach($model->items as $i=>$item){
			$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"etapa"=>$f->request->etapa,"clasificador"=>$f->request->clasificador);
			if($f->request->fuente!="") $params["fuente"] = $f->request->fuente;
			if(isset($f->request->data["meta"])){
				$params["meta"] = $f->request->meta;
			}
			if($orga_filtro==null){
				$params["programa"] = $f->request->programa;
			}else{
				$params["orga_group"] = $orga_filtro;
			}
			$pres=$f->model("pr/pres")->params($params)->get("search");
			if(isset($pres->items)){
				foreach($pres->items as $part){
					if(!isset($array[$part["fuente"]["_id"]])){
						$array[$part["fuente"]["_id"]]["fuente"] = $part["fuente"];
						$array[$part["fuente"]["_id"]]["items"] = array();
						$array[$part["fuente"]["_id"]]["metas"] = array();
					}
					if(isset($part["meta"])){
						if(!isset($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]])){
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["meta"] = $part["meta"];
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = array();
							$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = array();
							for($k=1;$k<=12;$k++){
								$array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$k]=array();
							}
						}
						array_push($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$part["periodo"]["mes"]],$part["importe"]);
						if($part["etapa"]=="A"){
							array_push($array[$part["fuente"]["_id"]]["metas"][$part["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"],$part["importe"]);	
						}
					}else{
						if(!isset($array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = array();
							$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = array();
							for($k=1;$k<=12;$k++){
								$array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$k]=array();
							}
						}
						array_push($array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$part["periodo"]["mes"]],$part["importe"]);
						if($part["etapa"]=="A"){
							array_push($array[$part["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"],$part["importe"]);	
						}
					}
				}
			}
		}
		//$model->filtros = $f->request->data;
		$f->response->view('pr/repo.pmen.print',array("items"=>$array,"filtros"=>$f->request->data) );
		//$f->response->json($array);
	}
	function execute_pmen_pia(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano,
			"etapa"=>"A"
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		if(isset($f->request->data["organizacion"]))$params["organizacion"] = $f->request->organizacion;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(!isset($array[$partida["fuente"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
						$array[$partida["fuente"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["proyectos"] = array();
					}
					if(isset($partida["meta"])){
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}	
						$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}else{
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}
						$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}
			}
		}
		$filtros = array(
			"tipo"=>$f->request->tipo,
			"periodo"=>$f->request->ano
		);
		if($f->request->fuente!=""){
			$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		}
		if(isset($f->request->data["organizacion"])){
			$filtros["organizacion"] = $f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->organizacion)))->get("one")->items;
		}
		//print_r($array);die();
		$f->response->view("pr/repo.pmen_pia.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_pmen_pia_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano,
			"etapa"=>"A"
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		if(isset($f->request->data["programa"]))$params["programa"] = $f->request->programa;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(!isset($array[$partida["fuente"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
						$array[$partida["fuente"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["proyectos"] = array();
					}
					if(isset($partida["meta"])){
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}	
						$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}else{
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}
						$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}
			}
		}
		$filtros = array(
			"tipo"=>$f->request->tipo,
			"periodo"=>$f->request->ano
		);
		if($f->request->fuente!=""){
			$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		}
		if(isset($f->request->data["programa"])){
			$filtros["programa"] = $f->model("mg/prog")->params(array("_id"=>new MongoId($f->request->programa)))->get("one")->items;
		}
		//print_r($array);die();
		$f->response->view("pr/repo.pmen_pia.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_pmen_pim(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		if(isset($f->request->data["organizacion"]))$params["organizacion"] = $f->request->organizacion;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(!isset($array[$partida["fuente"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
						$array[$partida["fuente"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["proyectos"] = array();
					}
					if(isset($partida["meta"])){
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]=0;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}	
						$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if($partida["etapa"]=="A"){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
						}
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}else{
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = 0;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}
						$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if($partida["etapa"]=="A"){
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
						}
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}
			}
		}
		$filtros = array(
			"tipo"=>$f->request->tipo,
			"periodo"=>$f->request->ano
		);
		if($f->request->fuente!=""){
			$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		}
		if(isset($f->request->data["organizacion"])){
			$filtros["organizacion"] = $f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->organizacion)))->get("one")->items;
		}
		//print_r($array);die();
		$f->response->view("pr/repo.pmen_pim.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_pmen_pim_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		if(isset($f->request->data["programacion"]))$params["programacion"] = $f->request->programacion;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(!isset($array[$partida["fuente"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
						$array[$partida["fuente"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["proyectos"] = array();
					}
					if(isset($partida["meta"])){
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"] = array();
						}
						if(!isset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]=0;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}	
						$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if($partida["etapa"]=="A"){
							$array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
						}
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}else{
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["clasificador"] = $item;
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"] = 0;
							for($n=1;$n<=12;$n++){
								$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$n] = 0;
							}
						}
						$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"][$partida["periodo"]["mes"]]+=$partida["importe"];
						if($partida["etapa"]=="A"){
							$array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]["pia"]+=$partida["importe"];
						}
						if(!isset($item["clasificadores"]["hijos"])){
							if($item["cod"]!=$partida["clasificador"]["cod"]){
								unset($array[$partida["fuente"]["_id"]]["items"][$item["_id"]->{'$id'}]);
							}
						}
					}
				}
			}
		}
		$filtros = array(
			"tipo"=>$f->request->tipo,
			"periodo"=>$f->request->ano
		);
		if($f->request->fuente!=""){
			$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		}
		if(isset($f->request->data["programacion"])){
			$filtros["programacion"] = $f->model("mg/prog")->params(array("_id"=>new MongoId($f->request->programacion)))->get("one")->items;
		}
		//print_r($array);die();
		$f->response->view("pr/repo.pmen_pim.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_pmen2(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		for($i=0;$i<count($model->items);$i++){
			$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>"","etapa"=>"","clasificador"=>""))->get("search");
			$model->items[$i]["importes"]=array();
			if(isset($cursor2->items)){
				for($k=1;$k<=12;$k++){
					$model->items[$i]["importes"][$k]=array();
				}
				$model->items[$i]["tot_pia"]=array();
				$model->items[$i]["tot_pim"]=array();
				for($j=0;$j<count($cursor2->items);$j++){
					array_push($model->items[$i]["importes"][$cursor2->items[$j]["periodo"]["mes"]], $cursor2->items[$j]["importe"]);						
					if($cursor2->items[$j]["etapa"]=="A"){
						array_push($model->items[$i]["tot_pia"],$cursor2->items[$j]["importe"]);
					}elseif($cursor2->items[$j]["etapa"]=="M"){
						array_push($model->items[$i]["tot_pim"],$cursor2->items[$j]["importe"]);
					}
				}
			}else{
				$model->items[$i]=null;
			}
		}
		$model->items = array_values(array_filter($model->items));
		$model->filtros = $f->request->data;
		$f->response->view('pr/repo.pmen2.print',$model );
	}
	function execute_pmen2_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		for($i=0;$i<count($model->items);$i++){
			$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programacion"=>"","etapa"=>"","clasificador"=>""))->get("search");
			$model->items[$i]["importes"]=array();
			if(isset($cursor2->items)){
				for($k=1;$k<=12;$k++){
					$model->items[$i]["importes"][$k]=array();
				}
				$model->items[$i]["tot_pia"]=array();
				$model->items[$i]["tot_pim"]=array();
				for($j=0;$j<count($cursor2->items);$j++){
					array_push($model->items[$i]["importes"][$cursor2->items[$j]["periodo"]["mes"]], $cursor2->items[$j]["importe"]);						
					if($cursor2->items[$j]["etapa"]=="A"){
						array_push($model->items[$i]["tot_pia"],$cursor2->items[$j]["importe"]);
					}elseif($cursor2->items[$j]["etapa"]=="M"){
						array_push($model->items[$i]["tot_pim"],$cursor2->items[$j]["importe"]);
					}
				}
			}else{
				$model->items[$i]=null;
			}
		}
		$model->items = array_values(array_filter($model->items));
		$model->filtros = $f->request->data;
		$f->response->view('pr/repo.pmen2.print',$model );
	}
	function execute_estr(){
		global $f;
		$comp = $f->model("pr/acti")->params(array("nivel"=>"CO"))->get("all");
		$model->items = array();
		foreach($comp->items as $i=>$c){
			//$model->items[$i] = $c;
			$acti = $f->model("pr/acti")->params(array("_id"=>new MongoId($c["actividad"])))->get("one");		
			$subp = $f->model("pr/estr")->params(array("_id"=>new MongoId($c["subprograma"]["id"])))->get("one");
			$prog = $f->model("pr/estr")->params(array("_id"=>new MongoId($subp->items["programa"])))->get("one");
			$func = $f->model("pr/estr")->params(array("_id"=>new MongoId($prog->items["funcion"])))->get("one");
			//$model->items[$i]["actividad"] = $acti->items;
			//$model->items[$i]["subprograma"] = $subp->items;
			//$model->items[$i]["programa"] = $prog->items;
			//$model->items[$i]["funcion"] = $func->items;
			if(!isset($model->items[$func->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}] = $func->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}] = $prog->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}] = $subp->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}] = $subp->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}] = $c;
			}
		}
		//$f->response->json($model->items);
		$f->response->view("pr/repo.estr.print",$model);
	}
	function execute_anexo1(){
		global $f;
		$comp = $f->model("pr/meta")->get("all");
		//$comp = $f->model("pr/acti")->params(array('nivel'=>'CO'))->get("all");
		$array["items"] = array();
		//$model = new stdClass();
		$model->items = array();
		foreach($comp->items as $c){
			$acti = $f->model("pr/acti")->params(array("_id"=>$c["actividad"]["_id"]))->get("one");		
			$subp = $f->model("pr/estr")->params(array("_id"=>$c["subprograma"]["_id"]))->get("one");
			$prog = $f->model("pr/estr")->params(array("_id"=>new MongoId($subp->items["programa"])))->get("one");
			$func = $f->model("pr/estr")->params(array("_id"=>new MongoId($prog->items["funcion"])))->get("one");
			$proy = $f->model("pr/eprog")->params(array("_id"=>$c["proyecto"]["_id"]))->get("one");
			$prog_pres = $f->model("pr/eprog")->params(array("_id"=>new MongoId($proy->items["programa"])))->get("one");
			$cate = $f->model("pr/eprog")->params(array("_id"=>new MongoId($prog_pres->items["categoria"])))->get("one");
			/*array_push($array["items"],array(
				"cate"=>$cate->items,
				"prog_pres"=>$prog_pres->items,
				"proy"=>$proy->items,
				"acti"=>$acti->items,
				"func"=>$func->items,
				"prog"=>$prog->items,
				"subp"=>$subp->items,
				"comp"=>$c
			));*/
			if(!isset($model->items[$func->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}] = $func->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}] = $prog->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}] = $subp->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}] = $acti->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"] = array();
			}
			if(!isset($model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}])){
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}] = $c;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}]["categoria"] = $cate->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}]["prog_pres"] = $prog_pres->items;
				$model->items[$func->items["_id"]->{'$id'}]["programas"][$prog->items["_id"]->{'$id'}]["subprogramas"][$subp->items["_id"]->{'$id'}]["actividades"][$acti->items["_id"]->{'$id'}]["componentes"][$c["_id"]->{'$id'}]["prod_proy"] = $proy->items;
			}
		}
		$f->response->view("pr/repo.anexo1.print",$model);
	}
	/*function execute_poe(){
		global $f;
		$data = $f->request->data;
		$model = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo))->get("all_filter");
		//print_r($model);die();
		$array["items"] = array();
		$modelo["filtros"] = array(
			"periodo"=>$f->request->periodo
		);
		foreach($model->items as $i=>$item){
			$array["items"][$i] = $item;
			switch($data["filtro"]){
				case "0":
					$i_ini=0;
					$i_fin=11;
					$modelo["filtros"]["title"] = "ANUAL";
					$modelo["filtros"]["filtro"] = "ANUAL";
					break;
				case "1":
					$i_ini=0;
					$i_fin=5;
					$modelo["filtros"]["title"] = "SEMESTRAL";
					$modelo["filtros"]["filtro"] = "PRIMER SEMESTRE";
					break;
				case "2":
					$i_ini=6;
					$i_fin=11;
					$modelo["filtros"]["title"] = "SEMESTRAL";
					$modelo["filtros"]["filtro"] = "SEGUNDO SEMESTRE";
					break;
				case "3":
					$i_ini=0;
					$i_fin=2;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "PRIMER TRIMESTRE";
					break;
				case "4":
					$i_ini=3;
					$i_fin=5;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "SEGUNDO TRIMESTRE";
					break;
				case "5":
					$i_ini=6;
					$i_fin=8;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "TERCER TRIMESTRE";
					break;
				case "6":
					$i_ini=9;
					$i_fin=11;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "CUARTO TRIMESTRE";
					break;
			}
			$prog = 0;
			$ejec = 0;
			for($k=$i_ini;$k<=$i_fin;$k++){
				$prog +=$item["metas"]["programadas"][$k];
				$ejec +=$item["metas"]["ejecutadas"][$k];
			}
			$array["items"][$i]["programacion"] = $prog;
			$array["items"][$i]["ejecucion"] = $ejec;
		}
		$modelo["items"] = array();
		foreach($array["items"] as $i=>$row){
			if(!isset($modelo["items"][$row["organizacion"]["actividad"]["_id"]])){
				$modelo["items"][$row["organizacion"]["actividad"]["_id"]]["actividad"] = $row["organizacion"]["actividad"];
				$modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"] = array();
			}
			if(!isset($modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]])){
				$modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]]["componente"] = $row["organizacion"]["componente"];
				$modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]]["organizaciones"] = array();
			}
			if(!isset($modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]]["organizaciones"][$row["organizacion"]["_id"]])){
				$modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]]["organizaciones"][$row["organizacion"]["_id"]]["organizacion"] = $row["organizacion"];
				$modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]]["organizaciones"][$row["organizacion"]["_id"]]["items"] = array();
			}
			array_push($modelo["items"][$row["organizacion"]["actividad"]["_id"]]["componentes"][$row["organizacion"]["componente"]["_id"]]["organizaciones"][$row["organizacion"]["_id"]]["items"],$row);
		}
		//print_r($modelo["items"]);die();
		$f->response->view("pr/repo.poe.export",$modelo);
	}*/
	function execute_poe_v1(){
		global $f;
		$data = $f->request->data;
		$model = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo))->get("all_filter");
		//print_r($model);die();
		$array["items"] = array();
		$modelo["filtros"] = array(
			"periodo"=>$f->request->periodo
		);
		foreach($model->items as $i=>$item){
			$array["items"][$i] = $item;
			switch($data["filtro"]){
				case "0":
					$i_ini=0;
					$i_fin=11;
					$modelo["filtros"]["title"] = "ANUAL";
					$modelo["filtros"]["filtro"] = "ANUAL";
					break;
				case "1":
					$i_ini=0;
					$i_fin=5;
					$modelo["filtros"]["title"] = "SEMESTRAL";
					$modelo["filtros"]["filtro"] = "PRIMER SEMESTRE";
					break;
				case "2":
					$i_ini=6;
					$i_fin=11;
					$modelo["filtros"]["title"] = "SEMESTRAL";
					$modelo["filtros"]["filtro"] = "SEGUNDO SEMESTRE";
					break;
				case "3":
					$i_ini=0;
					$i_fin=2;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "PRIMER TRIMESTRE";
					break;
				case "4":
					$i_ini=3;
					$i_fin=5;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "SEGUNDO TRIMESTRE";
					break;
				case "5":
					$i_ini=6;
					$i_fin=8;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "TERCER TRIMESTRE";
					break;
				case "6":
					$i_ini=9;
					$i_fin=11;
					$modelo["filtros"]["title"] = "TRIMESTRAL";
					$modelo["filtros"]["filtro"] = "CUARTO TRIMESTRE";
					break;
			}
			$prog = 0;
			$ejec = 0;
			for($k=$i_ini;$k<=$i_fin;$k++){
				$prog +=$item["metas"]["programadas"][$k];
				$ejec +=$item["metas"]["ejecutadas"][$k];
			}
			$array["items"][$i]["programacion"] = $prog;
			$array["items"][$i]["ejecucion"] = $ejec;
		}
		$modelo["items"] = array();
		foreach($array["items"] as $i=>$row){
			#SE DEBE MEJORAR EN PROGRAMAS LLAME A UN _ID DE ACTIVIDAD Y COMPONENTE APARTE DEL COD, ACTUALMENTE NO HAY _ID, SOLO COD
			/*
			if(!isset($modelo["items"][$row["programa"]["actividad"]["_id"]])){
				$modelo["items"][$row["programa"]["actividad"]["_id"]]["actividad"] = $row["programa"]["actividad"];
				$modelo["items"][$row["programa"]["actividad"]["_id"]]["componentes"] = array();
			}
			if(!isset($modelo["items"][$row["programa"]["actividad"]["_id"]]["componentes"][$row["programa"]["componente"]["_id"]])){
				$modelo["items"][$row["programa"]["actividad"]["_id"]]["componentes"][$row["programa"]["componente"]["_id"]]["componente"] = $row["programa"]["componente"];
				$modelo["items"][$row["programa"]["actividad"]["_id"]]["componentes"][$row["programa"]["componente"]["_id"]]["programas"] = array();
			}
			if(!isset($modelo["items"][$row["programa"]["actividad"]["_id"]]["componentes"][$row["programa"]["componente"]["_id"]]["programas"][$row["programas"]["_id"]])){
				$modelo["items"][$row["programas"]["actividad"]["_id"]]["componentes"][$row["programas"]["componente"]["_id"]]["programas"][$row["programas"]["_id"]]["programa"] = $row["programa"];
				$modelo["items"][$row["programa"]["actividad"]["_id"]]["componentes"][$row["programa"]["componente"]["_id"]]["programa"][$row["programa"]["_id"]]["items"] = array();
			}
			*/
			//print_r($row);
			if(!isset($modelo["items"][$row["programa"]["actividad"]["cod"]])){
				$modelo["items"][$row["programa"]["actividad"]["cod"]]["actividad"] = $row["programa"]["actividad"];
				$modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"] = array();
			}
			if(!isset($modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]])){
				$modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]]["componente"] = $row["programa"]["componente"];
				$modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]]["programas"] = array();
			}
			if(!isset($modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]]["programas"][$row["programa"]["_id"]->{'$id'}])){
				$modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]]["programas"][$row["programa"]["_id"]->{'$id'}]["programa"] = $row["programa"];
				$modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]]["programas"][$row["programa"]["_id"]->{'$id'}]["items"] = array();
			}
			array_push($modelo["items"][$row["programa"]["actividad"]["cod"]]["componentes"][$row["programa"]["componente"]["cod"]]["programas"][$row["programa"]["_id"]->{'$id'}]["items"],$row);
		}
		//print_r($modelo["items"]);die();
		$f->response->view("pr/repo.poe.export",$modelo);
	}
	function execute_c1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"I","id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>"I",
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$eje = $f->model("ct/auxi")->params($params)->get("all");
		$part = array();
		foreach($cursor->items as $item){
			if(!isset($part[$item["clasificador"]["cod"]])){
				$part[$item["clasificador"]["cod"]]["partida"] = $item;
				$part[$item["clasificador"]["cod"]]["pia"] = array();
				$part[$item["clasificador"]["cod"]]["pim"] = array();
				$part[$item["clasificador"]["cod"]]["eje"] = array();
			}
			if($item["etapa"]=="A"){
				array_push($part[$item["clasificador"]["cod"]]["pia"],$item["importe"]);
			}elseif($item["etapa"]=="M"){
				array_push($part[$item["clasificador"]["cod"]]["pim"],$item["importe"]);
			}
		}
		foreach($eje->items as $item){
			if(!isset($part[$item["saldo"]["subespecifica"]["cod"]])){
				$part[$item["saldo"]["subespecifica"]["cod"]]["partida"] = $item;
				$part[$item["saldo"]["subespecifica"]["cod"]]["pia"] = array();
				$part[$item["saldo"]["subespecifica"]["cod"]]["pim"] = array();
				$part[$item["saldo"]["subespecifica"]["cod"]]["eje"] = array();
			}
			array_push($part[$item["saldo"]["subespecifica"]["cod"]]["eje"],$item["ejec_pres"]["monto"]);
		}
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])<=3){
				$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
				$_item = array();
				$_item["clasificador"] = $item;
				$_item["pia"] = 0;
				$_item["pim"] = 0;
				$_item["eje"] = 0;
				if(count($find)>0){
					foreach($find as $row){
						$_item["pia"]+=array_sum($part[$row]["pia"]);
						$_item["pim"]+=array_sum($part[$row]["pim"]);
						$_item["eje"]+=array_sum($part[$row]["eje"]);
					}
				}
				array_push($array,$_item);
			}
		}
		$filtros = array(
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		$f->response->view("pr/repo.c1.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_c3(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"G","id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>"G",
			"ano"=>$f->request->ano,
			"etapa"=>"M"
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		//$eje = $f->model("ct/auxi")->params($params)->get("all");
		$part = array();
		foreach($cursor->items as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$cred = array();
		$nota = array();
		foreach($model->items as $i=>$item){
			//if(strlen($item["clasificador"]["cod"])!=2)continue;
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(isset($partida["num_credito"])){
						if(!isset($cred[$partida["num_credito"]])){
							$cred[$partida["num_credito"]]["num_credito"] = $partida["num_credito"];
							$cred[$partida["num_credito"]]["resolucion"] = $partida["ref"];
							$cred[$partida["num_credito"]]["importes"] = array(
								"2.1"=>0,
								"2.2"=>0,
								"2.3"=>0,
								"2.4"=>0,
								"2.5"=>0,
								"2.6"=>0
							);
						}
						if(isset($cred[$partida["num_credito"]]["importes"][$item["cod"]])){
							$cred[$partida["num_credito"]]["importes"][$item["cod"]]+=$partida["importe"];
						}
					}elseif(isset($partida["num_nota"])){
						if(!isset($nota[$partida["num_nota"]])){
							$nota[$partida["num_nota"]]["num_credito"] = $partida["num_nota"];
							$nota[$partida["num_nota"]]["resolucion"] = $partida["ref"];
							$nota[$partida["num_nota"]]["importes"] = array(
								"2.1"=>0,
								"2.2"=>0,
								"2.3"=>0,
								"2.4"=>0,
								"2.5"=>0,
								"2.6"=>0
							);
						}
						if(isset($nota[$partida["num_nota"]]["importes"][$item["cod"]])){
							$nota[$partida["num_nota"]]["importes"][$item["cod"]]+=$partida["importe"];
						}	
					}					
				}
			}
		}
		$filtros = array(
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		$f->response->view("pr/repo.c3.print",array("items"=>array("cred"=>$cred,"nota"=>$nota),"filtros"=>$filtros));
	}
	function execute_c4(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>"G","id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>"G",
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$eje = $f->model("ct/auxg")->params($params)->get("all");
		$part = array();
		foreach($cursor->items as $item){
			if(!isset($part[$item["clasificador"]["cod"]])){
				$part[$item["clasificador"]["cod"]]["partida"] = $item;
				$part[$item["clasificador"]["cod"]]["pia"] = array();
				$part[$item["clasificador"]["cod"]]["pim"] = array();
				$part[$item["clasificador"]["cod"]]["eje"] = array();
			}
			if($item["etapa"]=="A"){
				array_push($part[$item["clasificador"]["cod"]]["pia"],$item["importe"]);
			}elseif($item["etapa"]=="M"){
				array_push($part[$item["clasificador"]["cod"]]["pim"],$item["importe"]);
			}
		}
		foreach($eje->items as $item){
			if(!isset($part[$item["saldo"]["subespecifica"]["cod"]])){
				$part[$item["saldo"]["subespecifica"]["cod"]]["partida"] = $item;
				$part[$item["saldo"]["subespecifica"]["cod"]]["pia"] = array();
				$part[$item["saldo"]["subespecifica"]["cod"]]["pim"] = array();
				$part[$item["saldo"]["subespecifica"]["cod"]]["eje"] = array();
			}
			array_push($part[$item["saldo"]["subespecifica"]["cod"]]["eje"],$item["ejec_pres"]["monto"]);
		}
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])<=3){
				$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
				$_item = array();
				$_item["clasificador"] = $item;
				$_item["pia"] = 0;
				$_item["pim"] = 0;
				$_item["eje"] = 0;
				if(count($find)>0){
					foreach($find as $row){
						$_item["pia"]+=array_sum($part[$row]["pia"]);
						$_item["pim"]+=array_sum($part[$row]["pim"]);
						$_item["eje"]+=array_sum($part[$row]["eje"]);
					}
				}
				array_push($array,$_item);
			}
		}
		$filtros = array(
			"ano"=>$f->request->ano
		);
		if($f->request->fuente!="")$filtros["fuente"] = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->fuente)))->get("one")->items;
		$f->response->view("pr/repo.c4.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_c5(){
		global $f;
		$fuentes = $f->model("pr/fuen")->get("all");
		$model = $f->model("pr/clas")->params(array("tipo"=>"G","id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>"G",
			"ano"=>$f->request->ano
		);
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$eje = $f->model("ct/auxg")->params($params)->get("all");
		$part = array();
		foreach($cursor->items as $item){
			if(!isset($part[$item["clasificador"]["cod"]])){
				$part[$item["clasificador"]["cod"]]["partida"] = $item;
				$part[$item["clasificador"]["cod"]]["fuentes"] = array();
				foreach($fuentes->items as $fuente){
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["fuente"] = $fuente;
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["pia"] = array();
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["pim"] = array();
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["eje"] = array();
				}
			}
			if($item["etapa"]=="A"){
				array_push($part[$item["clasificador"]["cod"]]["fuentes"][$item["fuente"]["_id"]]["pia"],$item["importe"]);
			}elseif($item["etapa"]=="M"){
				array_push($part[$item["clasificador"]["cod"]]["fuentes"][$item["fuente"]["_id"]]["pim"],$item["importe"]);
			}
		}
		foreach($eje->items as $item){
			if(!isset($part[$item["saldo"]["subespecifica"]["cod"]])){
				$part[$item["saldo"]["subespecifica"]["cod"]]["partida"] = $item;
				$part[$item["saldo"]["subespecifica"]["cod"]]["fuentes"] = array();
				foreach($fuentes->items as $fuente){
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["fuente"] = $fuente;
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["pia"] = array();
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["pim"] = array();
					$part[$item["clasificador"]["cod"]]["fuentes"][$fuente["_id"]->{'$id'}]["eje"] = array();
				}
			}
			array_push($part[$item["saldo"]["subespecifica"]["cod"]]["fuentes"][$item["saldo"]["_id"]->{'$id'}]["eje"],$item["ejec_pres"]["monto"]);
		}
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])<=3){
				$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
				$_item = array();
				$_item["clasificador"] = $item;
				$_item["fuentes"] = array();
				foreach($fuentes->items as $fuente){
					$_item["fuentes"][$fuente["_id"]->{'$id'}]["fuente"] = $fuente;
					$_item["fuentes"][$fuente["_id"]->{'$id'}]["pia"] = 0;
					$_item["fuentes"][$fuente["_id"]->{'$id'}]["pim"] = 0;
					$_item["fuentes"][$fuente["_id"]->{'$id'}]["eje"] = 0;
				}
				if(count($find)>0){
					foreach($find as $row){
						foreach($part[$row]["fuentes"] as $key=>$fuen){
							$_item["fuentes"][$key]["pia"]+=array_sum($part[$row]["fuentes"][$key]["pia"]);
							$_item["fuentes"][$key]["pim"]+=array_sum($part[$row]["fuentes"][$key]["pim"]);
							$_item["fuentes"][$key]["eje"]+=array_sum($part[$row]["fuentes"][$key]["eje"]);
						}
					}
				}
				array_push($array,$_item);
			}
		}
		//print_r($array);die();
		$filtros = array(
			"ano"=>$f->request->ano,
			"fuentes"=>$fuentes->items
		);
		$f->response->view("pr/repo.c5.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_mefi(){
		global $f;
		$model = $f->model("pr/mefi")->params(array("periodo"=>$f->request->periodo))->get("all");
		$array = array();
		if($model->items!=null){
			foreach($model->items as $item){
				$componente = $f->model("pr/acti")->params(array("_id"=>$item["componente"]["_id"]))->get("one")->items;
				/*$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($componente["actividad"])))->get("one")->items;
				if($actividad)*/
				if(!isset($array[$componente["actividad"]])){
					$array[$componente["actividad"]]["actividad"] = $f->model("pr/acti")->params(array("_id"=>new MongoId($componente["actividad"])))->get("one")->items;;
					$array[$componente["actividad"]]["componentes"] = array();
				}
				if(!isset($array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}])){
					$array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}]["componente"] = $componente;
					$array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}]["metas"] = array();
				}
				array_push($array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}]["metas"],$item);
			}
		}
		//print_r($array);
		$filtros = array(
			"periodo"=>$f->request->periodo,
			"etapa"=>$f->request->etapa
		);
		$f->response->view("pr/repo.mefi.export",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_cuadro1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["organizacion"]))$params["organizacion"]=$f->request->data["organizacion"];
		//if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$part = array();
		foreach($cursor->items as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "PRE";
		}
		if($f->request->tipo=="I"){
			$eje = $f->model("ct/auxi")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}
		}else{
			$eje = $f->model("ct/auxg")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}		
		}
		
		$array = array();
		foreach($part as $partida){
			switch($partida["area"]){
				case "PRE":
					$orga = $f->model("mg/orga")->params(array("_id"=>new MongoId($partida["organizacion"]["_id"])))->get("one")->items;
					if(!isset($partida["actividad"]["_id"])){
						$actividad = array(
							"_id"=>$orga["actividad"]["_id"]->{'$id'},
							"cod"=>$orga["actividad"]["cod"],
							"nomb"=>$orga["actividad"]["nomb"]
						);
					}else{
						$actividad = $partida["actividad"];
					}
					if(!isset($array[$partida["actividad"]["_id"]])){
						$array[$actividad["_id"]]["actividad"] = $partida["actividad"];
						$array[$actividad["_id"]]["pia"] = 0;
						$array[$actividad["_id"]]["pim"] = 0;
						$array[$actividad["_id"]]["eje"] = 0;
					}
					if($partida["etapa"]=="A"){
						$array[$actividad["_id"]]["pia"]+=$partida["importe"];
					}
					$array[$actividad["_id"]]["pim"]+=$partida["importe"];		
					break;
				case "CON":
					if(!isset($array[$partida["saldo"]["organizacion"]["actividad"]["_id"]->{'$id'}])){
						$array[$partida["saldo"]["organizacion"]["actividad"]["_id"]->{'$id'}]["acitivdad"] = $partida["saldo"]["organizacion"]["actividad"];
						$array[$partida["saldo"]["organizacion"]["actividad"]["_id"]->{'$id'}]["pia"] = 0;
						$array[$partida["saldo"]["organizacion"]["actividad"]["_id"]->{'$id'}]["pim"] = 0;
						$array[$partida["saldo"]["organizacion"]["actividad"]["_id"]->{'$id'}]["eje"] = 0;
					}
					$array[$partida["saldo"]["organizacion"]["actividad"]["_id"]->{'$id'}]["eje"]+=$partida["ejec_pres"]["monto"];
					break;
			}
		}
		$array = array_values($array);
		//print_r($array);die();
		//$f->response->view("pr/repo.cuadro1.export",array("items"=>$array));
		$f->response->json($array);
	}
	function execute_cuadro1_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["programa"]))$params["programa"]=$f->request->data["programa"];
		//if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$part = array();
		foreach($cursor->items as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "PRE";
		}
		if($f->request->tipo=="I"){
			$eje = $f->model("ct/auxi")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}
		}else{
			$eje = $f->model("ct/auxg")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}		
		}
		
		$array = array();
		foreach($part as $partida){
			switch($partida["area"]){
				case "PRE":
					$orga = $f->model("mg/prog")->params(array("_id"=>new MongoId($partida["programa"]["_id"])))->get("one")->items;
					if(!isset($partida["actividad"]["_id"])){
						$actividad = array(
							"_id"=>$orga["actividad"]["_id"]->{'$id'},
							"cod"=>$orga["actividad"]["cod"],
							"nomb"=>$orga["actividad"]["nomb"]
						);
					}else{
						$actividad = $partida["actividad"];
					}
					if(!isset($array[$partida["actividad"]["_id"]])){
						$array[$actividad["_id"]]["actividad"] = $partida["actividad"];
						$array[$actividad["_id"]]["pia"] = 0;
						$array[$actividad["_id"]]["pim"] = 0;
						$array[$actividad["_id"]]["eje"] = 0;
					}
					if($partida["etapa"]=="A"){
						$array[$actividad["_id"]]["pia"]+=$partida["importe"];
					}
					$array[$actividad["_id"]]["pim"]+=$partida["importe"];		
					break;
				case "CON":
					if(!isset($array[$partida["saldo"]["programa"]["actividad"]["_id"]->{'$id'}])){
						$array[$partida["saldo"]["programa"]["actividad"]["_id"]->{'$id'}]["acitivdad"] = $partida["saldo"]["programa"]["actividad"];
						$array[$partida["saldo"]["programa"]["actividad"]["_id"]->{'$id'}]["pia"] = 0;
						$array[$partida["saldo"]["programa"]["actividad"]["_id"]->{'$id'}]["pim"] = 0;
						$array[$partida["saldo"]["programa"]["actividad"]["_id"]->{'$id'}]["eje"] = 0;
					}
					$array[$partida["saldo"]["programa"]["actividad"]["_id"]->{'$id'}]["eje"]+=$partida["ejec_pres"]["monto"];
					break;
			}
		}
		$array = array_values($array);
		//print_r($array);die();
		//$f->response->view("pr/repo.cuadro1.export",array("items"=>$array));
		$f->response->json($array);
	}
	function execute_cuadro2(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["organizacion"]))$params["organizacion"]=$f->request->data["organizacion"];
		//if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$part = array();
		if($cursor->items!=null){
			foreach($cursor->items as $item){
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "PRE";
			}
		}
		if($f->request->tipo=="I"){
			$eje = $f->model("ct/auxi")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}
		}else{
			$eje = $f->model("ct/auxg")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}
		}
		
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])==3){
				$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
				if(count($find)>0){
					foreach($find as $row){
						$partida = $part[$row];
						switch($partida["area"]){
							case "PRE":
								if(!isset($array[$item["_id"]->{'$id'}])){
									$array[$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$item["_id"]->{'$id'}]["tot_pia"] = 0;
									$array[$item["_id"]->{'$id'}]["tot_pim"] = 0;
									$array[$item["_id"]->{'$id'}]["tot_eje"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$item["_id"]->{'$id'}]["pia"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["pim"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								if($partida["etapa"]=="A"){
									$array[$item["_id"]->{'$id'}]["pia"][$partida["periodo"]["mes"]]+=$partida["importe"];
									$array[$item["_id"]->{'$id'}]["tot_pia"]+=$partida["importe"];
								}
								$array[$item["_id"]->{'$id'}]["tot_pim"]+=$partida["importe"];
								$array[$item["_id"]->{'$id'}]["pim"][$partida["periodo"]["mes"]]+=$partida["importe"];
								break;
							case "CON":
								if(!isset($array[$item["_id"]->{'$id'}])){
									$array[$item["_id"]->{'$id'}]["clasificador"] = $item;
									for($n=1;$n<=12;$n++){
										$array[$item["_id"]->{'$id'}]["pia"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["pim"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$item["_id"]->{'$id'}]["eje"][$partida["saldo"]["periodo"]["mes"]]+=$partida["ejec_pres"]["monto"];
								$array[$item["_id"]->{'$id'}]["tot_eje"]+=$partida["ejec_pres"]["monto"];
								break;
						}
					}
				}
			}
		}
		$array = array_values($array);
		//print_r($array);die();
		//$f->response->view("pr/repo.cuadro1.export",array("items"=>$array));
		$f->response->json($array);
	}
	function execute_cuadro2_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->ano
		);
		if(isset($f->request->data["programa"]))$params["organizacion"]=$f->request->data["programa"];
		//if($f->request->fuente!="")$params["fuente"] = $f->request->fuente;
		$cursor = $f->model("pr/pres")->params($params)->get("all_part");
		$part = array();
		if($cursor->items!=null){
			foreach($cursor->items as $item){
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
				$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "PRE";
			}
		}
		if($f->request->tipo=="I"){
			$eje = $f->model("ct/auxi")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["subespecifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}
		}else{
			$eje = $f->model("ct/auxg")->params($params)->get("all");
			if($eje->items!=null){
				foreach($eje->items as $item){
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
					$part[$item["saldo"]["especifica"]["cod"]."_".$item["_id"]->{'$id'}]["area"] = "CON";
				}
			}
		}
		
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])==3){
				$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));
				if(count($find)>0){
					foreach($find as $row){
						$partida = $part[$row];
						switch($partida["area"]){
							case "PRE":
								if(!isset($array[$item["_id"]->{'$id'}])){
									$array[$item["_id"]->{'$id'}]["clasificador"] = $item;
									$array[$item["_id"]->{'$id'}]["tot_pia"] = 0;
									$array[$item["_id"]->{'$id'}]["tot_pim"] = 0;
									$array[$item["_id"]->{'$id'}]["tot_eje"] = 0;
									for($n=1;$n<=12;$n++){
										$array[$item["_id"]->{'$id'}]["pia"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["pim"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								if($partida["etapa"]=="A"){
									$array[$item["_id"]->{'$id'}]["pia"][$partida["periodo"]["mes"]]+=$partida["importe"];
									$array[$item["_id"]->{'$id'}]["tot_pia"]+=$partida["importe"];
								}
								$array[$item["_id"]->{'$id'}]["tot_pim"]+=$partida["importe"];
								$array[$item["_id"]->{'$id'}]["pim"][$partida["periodo"]["mes"]]+=$partida["importe"];
								break;
							case "CON":
								if(!isset($array[$item["_id"]->{'$id'}])){
									$array[$item["_id"]->{'$id'}]["clasificador"] = $item;
									for($n=1;$n<=12;$n++){
										$array[$item["_id"]->{'$id'}]["pia"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["pim"][$n] = 0;
										$array[$item["_id"]->{'$id'}]["eje"][$n] = 0;
									}
								}
								$array[$item["_id"]->{'$id'}]["eje"][$partida["saldo"]["periodo"]["mes"]]+=$partida["ejec_pres"]["monto"];
								$array[$item["_id"]->{'$id'}]["tot_eje"]+=$partida["ejec_pres"]["monto"];
								break;
						}
					}
				}
			}
		}
		$array = array_values($array);
		//print_r($array);die();
		//$f->response->view("pr/repo.cuadro1.export",array("items"=>$array));
		$f->response->json($array);
	}
	function execute_mefi2(){
		global $f;
		$model = $f->model("pr/mefi")->params(array("periodo"=>$f->request->periodo))->get("all");
		$array = array();
		if($model->items!=null){
			foreach($model->items as $item){
				$componente = $f->model("pr/acti")->params(array("_id"=>$item["componente"]["_id"]))->get("one")->items;
				/*$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($componente["actividad"])))->get("one")->items;
				if($actividad)*/
				if(!isset($array[$componente["actividad"]])){
					$array[$componente["actividad"]]["actividad"] = $f->model("pr/acti")->params(array("_id"=>new MongoId($componente["actividad"])))->get("one")->items;;
					$array[$componente["actividad"]]["componentes"] = array();
				}
				if(!isset($array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}])){
					$array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}]["componente"] = $componente;
					$array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}]["metas"] = array();
				}
				$item["prog_total"] = 0;
				$item["ejec_total"] = 0;
				if(floatval($f->request->data["filtro"])>12){
					switch(floatval($f->request->data["filtro"])){
						case "13"://anual
							$item["prog_total"] = array_sum($item["programado"]);
							$item["ejec_total"] = array_sum($item["ejecutado"]);
							break;
						case "14"://primer trimestre
							$item["prog_total"] = $item["programado"][0]+$item["programado"][1]+$item["programado"][2];
							$item["ejec_total"] = $item["ejecutado"][0]+$item["ejecutado"][1]+$item["ejecutado"][2];
							break;
						case "15"://segundo trimestre
							$item["prog_total"] = $item["programado"][3]+$item["programado"][4]+$item["programado"][5];
							$item["ejec_total"] = $item["ejecutado"][3]+$item["ejecutado"][4]+$item["ejecutado"][5];
							break;
						case "16"://tercer trimestre
							$item["prog_total"] = $item["programado"][6]+$item["programado"][7]+$item["programado"][8];
							$item["ejec_total"] = $item["ejecutado"][6]+$item["ejecutado"][7]+$item["ejecutado"][8];
							break;
						case "17"://cuarto trimestre
							$item["prog_total"] = $item["programado"][9]+$item["programado"][10]+$item["programado"][11];
							$item["ejec_total"] = $item["ejecutado"][9]+$item["ejecutado"][10]+$item["ejecutado"][11];
							break;
					}
				}else{
					$item["prog_total"] = $item["programado"][floatval($f->request->data["filtro"])-1];
					$item["ejec_total"] = $item["ejecutado"][floatval($f->request->data["filtro"])-1];
				}
				array_push($array[$componente["actividad"]]["componentes"][$componente["_id"]->{'$id'}]["metas"],$item);
			}
		}
		//print_r($array);die();
		$filtros = array(
			"periodo"=>$f->request->periodo,
			"filtro"=>$f->request->filtro
		);
		$f->response->view("pr/repo.mefi2.export",array("items"=>$array,"filtros"=>$filtros));
	}
}
?>