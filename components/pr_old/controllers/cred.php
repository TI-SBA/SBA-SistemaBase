<?php
class Controller_pr_cred extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pr/pres.modi_cred");
	}
	function execute_lista(){
		/*global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>null,"id"=>null,"estado"=>"H"))->get("lista");
		if(count($model->items)>0){
			for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>1,"page_rows"=>100000,"cod"=>$model->items[$i]["cod"],"tipo"=>null,"periodo"=>$f->request->periodo,"mes"=>"0","organizacion"=>null,"etapa"=>"","num_credito"=>floatval($f->request->num_credito)))->get("search");
					$index=0;
					$model->items[$i]["importes"] = array();
					if(isset($cursor2->items)){
						for($j=0;$j<count($cursor2->items);$j++){
							array_push($model->items[$i]["importes"],$cursor2->items[$j]["importe"]);
						}
					}else{
						$model->items[$i]=null;
					}
			}		
			$model->items = array_values(array_filter($model->items));
		}		
		$f->response->json( $model );*/
		global $f;
		$model = $f->model("pr/clas")->params(array("id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"ano"=>$f->request->periodo,
			"etapa"=>"M",
			"num_credito"=>floatval($f->request->num_credito)
		);
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])==1)continue;
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(strlen($partida["clasificador"]["cod"])==1)continue;
					if(!isset($array[$item["_id"]->{'$id'}])){
						$array[$item["_id"]->{'$id'}] = $item;
						$array[$item["_id"]->{'$id'}]["importes"] = array();
					}
					array_push($array[$item["_id"]->{'$id'}]["importes"],$partida["importe"]);
				}
			}
		}
		$array = array_values($array);
		$f->response->json(array("items"=>$array));
	}
	function execute_print(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->periodo,
			"etapa"=>"M",
			"num_credito"=>floatval($f->request->num_credito)
		);
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])==1)continue;
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(strlen($partida["clasificador"]["cod"])==1)continue;
					if(!isset($array[$partida["fuente"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
						$array[$partida["fuente"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["total"] = 0;
					}
					if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["orga"] = $f->model("mg/orga")->params(array("_id"=>new MongoId($partida["organizacion"]["_id"])))->get("one")->items;
						$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"] = array();
						$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["total"] = 0;
					}
					if(isset($partida["meta"])){
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]])){
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"] = array();
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["total"] = 0;
						}
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}] = $item;
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = 0;
						}
						$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"]+=$partida["importe"];
						
						/* Totales*/
						if(strlen($item["cod"])==3){
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["total"]+=$partida["importe"];
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["total"]+=$partida["importe"];
							$array[$partida["fuente"]["_id"]]["total"]+=$partida["importe"];
						}
					}else{
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["items"][$item["_id"]->{'$id'}] = $item;
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = 0;
						}
						$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"]+=$partida["importe"];
						/* Totales*/
						if(strlen($item["cod"])==3){
							$array[$partida["fuente"]["_id"]]["items"][$partida["organizacion"]["_id"]]["total"]+=$partida["importe"];
							$array[$partida["fuente"]["_id"]]["total"]+=$partida["importe"];
						}
					}
				}
			}
		}
		//print_r($array);die();
		$filtros = array(
			"periodo"=>$f->request->periodo,
			"num_credito"=>$f->request->num_credito,
			"tipo"=>$f->request->tipo
		);
		//$f->response->json($array);	
		$f->response->view( "pr/repo.cred.print", array("items"=>$array,"filtros"=>$filtros) );
	}
	function execute_print_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("tipo"=>$f->request->tipo,"id"=>"","estado"=>"H"))->get("lista");
		$params = array(
			"tipo"=>$f->request->tipo,
			"ano"=>$f->request->periodo,
			"etapa"=>"M",
			"num_credito"=>floatval($f->request->num_credito)
		);
		$cursor = $f->model("pr/pres")->params($params)->get("all_part")->items;
		$part = array();
		foreach($cursor as $item){
			$part[$item["clasificador"]["cod"]."_".$item["_id"]->{'$id'}] = $item;
		}
		//print_r($part);die();
		$array = array();
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])==1)continue;
			$find = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $part ));		
			if(count($find)>0){
				foreach($find as $row){
					$partida = $part[$row];
					if(strlen($partida["clasificador"]["cod"])==1)continue;
					if(!isset($array[$partida["fuente"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["fuente"] = $partida["fuente"];
						$array[$partida["fuente"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["total"] = 0;
					}
					if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]])){
						$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["orga"] = $f->model("mg/prog")->params(array("_id"=>new MongoId($partida["programa"]["_id"])))->get("one")->items;
						$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["items"] = array();
						$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"] = array();
						$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["total"] = 0;
					}
					if(isset($partida["meta"])){
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]])){
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["meta"] = $partida["meta"];
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"] = array();
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["total"] = 0;
						}
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}] = $item;
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = 0;
						}
						$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"]+=$partida["importe"];
						
						/* Totales*/
						if(strlen($item["cod"])==3){
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["proyectos"][$partida["meta"]["_id"]]["total"]+=$partida["importe"];
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["total"]+=$partida["importe"];
							$array[$partida["fuente"]["_id"]]["total"]+=$partida["importe"];
						}
					}else{
						if(!isset($array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["items"][$item["_id"]->{'$id'}])){
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["items"][$item["_id"]->{'$id'}] = $item;
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"] = 0;
						}
						$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["items"][$item["_id"]->{'$id'}]["importes"]+=$partida["importe"];
						/* Totales*/
						if(strlen($item["cod"])==3){
							$array[$partida["fuente"]["_id"]]["items"][$partida["programa"]["_id"]]["total"]+=$partida["importe"];
							$array[$partida["fuente"]["_id"]]["total"]+=$partida["importe"];
						}
					}
				}
			}
		}
		//print_r($array);die();
		$filtros = array(
			"periodo"=>$f->request->periodo,
			"num_credito"=>$f->request->num_credito,
			"tipo"=>$f->request->tipo
		);
		//$f->response->json($array);	
		$f->response->view( "pr/repo.cred.print", array("items"=>$array,"filtros"=>$filtros) );
	}
}
?>