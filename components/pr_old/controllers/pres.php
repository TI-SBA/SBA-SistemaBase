<?php
class Controller_pr_pres extends Controller {
	function execute_index() {
	}
	function execute_index_aper(){
		global $f;
		$f->response->view("pr/presaper");
		/*$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>30 ),
			array( "nomb"=>"C&oacute;digo","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>300 ),
			array( "nomb"=>"09","w"=>150 ),
			array( "nomb"=>"13","w"=>150 ),
			array( "nomb"=>"Total","w"=>150 )
		));*/
		$header_grid = array();
		$header_grid["cols"][]= array( "nomb"=>"&nbsp;","w"=>30 );
		$header_grid["cols"][]= array( "nomb"=>"C&oacute;digo","w"=>150 );
		$header_grid["cols"][]= array( "nomb"=>"Nombre","w"=>300 );
		$model = $f->model("pr/fuen")->get("all");
		if($model->items!=null){
			foreach($model->items as $obj){
				$header_grid["cols"][]= array( "nomb"=>$obj["cod"],"w"=>150 );
			}
		}		
		$header_grid["cols"][]= array( "nomb"=>"Total","w"=>150 );
		$f->response->view("ci/ci.grid",$header_grid);
	}
	function execute_index_modi(){
		global $f;
		$f->response->view("pr/presmodi");
		$header_grid = array();
		$header_grid["cols"][]= array( "nomb"=>"&nbsp;","w"=>30 );
		$header_grid["cols"][]= array( "nomb"=>"C&oacute;digo","w"=>150 );
		$header_grid["cols"][]= array( "nomb"=>"Nombre","w"=>300 );
		$model = $f->model("pr/fuen")->get("all");
		if($model->items!=null){
			foreach($model->items as $obj){
				$header_grid["cols"][]= array( "nomb"=>$obj["cod"],"w"=>150 );
			}
		}
		$header_grid["cols"][]= array( "nomb"=>"Total","w"=>150 );
		$f->response->view("ci/ci.grid",$header_grid);
	}
	function execute_aper_lista(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador,"estado"=>"H"))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		if(count($model->items)>0){
			for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}
			}		
			$model->items = array_values(array_filter($model->items));
			$model->importes = array_values(array_filter($model->importes));
			$model->num_fuen = count($fuentes->items)+3;
		}		
		$f->response->json( $model );
	}
	function execute_aper_lista_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador,"estado"=>"H"))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		if(count($model->items)>0){
			for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}
			}		
			$model->items = array_values(array_filter($model->items));
			$model->importes = array_values(array_filter($model->importes));
			$model->num_fuen = count($fuentes->items)+3;
		}		
		$f->response->json( $model );
	}
	function execute_modi_lista(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador,"estado"=>"H"))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		if(count($model->items)>0){
			for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();							
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}
			}
		
			$model->items = array_values(array_filter($model->items));
			$model->importes = array_values(array_filter($model->importes));
			$model->num_fuen = count($fuentes->items)+3;			
		}
		$f->response->json( $model );
	}
	function execute_modi_lista_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador,"estado"=>"H"))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		if(count($model->items)>0){
			for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();							
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}
			}
		
			$model->items = array_values(array_filter($model->items));
			$model->importes = array_values(array_filter($model->importes));
			$model->num_fuen = count($fuentes->items)+3;			
		}
		$f->response->json( $model );
	}
	function execute_aper_print(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();							
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->filtros = $f->request->data;
		$model->filtros = array(
			"periodo"=>$f->request->periodo,
			"mes"=>$f->request->mes,
			"tipo"=>$f->request->tipo,
			"organizacion"=>$f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->organizacion)))->get("one")->items,
			"clasificador"=>$f->model("pr/clas")->params(array("_id"=>new MongoId($f->request->clasificador)))->get("one")->items
		);
		$model->num_fuen = count($fuentes->items)+3;
		$f->response->view('pr/presaper.print',$model );
	}
	function execute_aper_print_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();							
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->filtros = $f->request->data;
		$model->filtros = array(
			"periodo"=>$f->request->periodo,
			"mes"=>$f->request->mes,
			"tipo"=>$f->request->tipo,
			"programa"=>$f->model("mg/prog")->params(array("_id"=>new MongoId($f->request->programa)))->get("one")->items,
			"clasificador"=>$f->model("pr/clas")->params(array("_id"=>new MongoId($f->request->clasificador)))->get("one")->items
		);
		$model->num_fuen = count($fuentes->items)+3;
		$f->response->view('pr/presaper.print',$model );
	}
	function execute_modi_print(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}		
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->filtros = $f->request->data;
		$model->num_fuen = count($fuentes->items)+3;
		$f->response->view('pr/presmodi.print',$model );
	}
	function execute_modi_print_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
						if(!isset($model->items[$i]["clasificadores"]["hijos"])){
							if($model->items[$i]["cod"]!=$cursor2->items[0]["clasificador"]["cod"]){
								$model->items[$i]=null;
								$model->importes[$i]=null;
							}
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}		
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->filtros = $f->request->data;
		$model->num_fuen = count($fuentes->items)+3;
		$f->response->view('pr/presmodi.print',$model );
	}
	function execute_get_num_credito(){
		global $f;
		$params = array();
		if(isset($f->request->data['periodo'])) $params['periodo'] = $f->request->data['periodo'];
		$model = $f->model("pr/pres")->params($params)->get("all");
		$f->response->json( $model->items );
	}
	function execute_get_num_nota(){
		global $f;
		$params = array();
		if(isset($f->request->data['periodo'])) $params['periodo'] = $f->request->data['periodo'];
		$model = $f->model("pr/pres")->params($params)->get("all_nota");
		$f->response->json( $model->items );
	}
	function execute_aper_export(){
		global $f;
				$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}	
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->num_fuen = count($fuentes->items);
		$f->response->view("pr/presaper.export",$model);
	}
	function execute_aper_export_v1(){
		global $f;
				$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
					$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("search");
					$model->importes[$i]=array();
					$index=0;
					if(isset($cursor2->items)){
						foreach($fuentes->items as $fuen){
							$model->importes[$i]["importe"][$index]=array();
							for($j=0;$j<count($cursor2->items);$j++){
								if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
									array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
								}else{
									array_push($model->importes[$i]["importe"][$index], "0");
								}
							}
							$index++;
						}
					}else{
						$model->items[$i]=null;
						$model->importes[$i]=null;
					}	
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->num_fuen = count($fuentes->items);
		$f->response->view("pr/presaper.export",$model);
	}
	function execute_modi_export(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
			$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"","clasificador"=>$f->request->clasificador))->get("search");
			$model->importes[$i]=array();
			$index=0;
			if(isset($cursor2->items)){
				foreach($fuentes->items as $fuen){
					$model->importes[$i]["importe"][$index]=array();
					for($j=0;$j<count($cursor2->items);$j++){
						if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
							array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
						}else{
							array_push($model->importes[$i]["importe"][$index], "0");
						}
					}
					$index++;
				}
			}else{
				$model->items[$i]=null;
				$model->importes[$i]=null;
			}		
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->num_fuen = count($fuentes->items);
		$f->response->view("pr/presmodi.export",$model);
	}
	function execute_modi_export_v1(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"id"=>$f->request->clasificador))->get("lista");
		$fuentes = $f->model("pr/fuen")->get("all");
		for($i=0;$i<count($model->items);$i++){
			$cursor2=$f->model("pr/pres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"cod"=>$model->items[$i]["cod"],"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"","clasificador"=>$f->request->clasificador))->get("search");
			$model->importes[$i]=array();
			$index=0;
			if(isset($cursor2->items)){
				foreach($fuentes->items as $fuen){
					$model->importes[$i]["importe"][$index]=array();
					for($j=0;$j<count($cursor2->items);$j++){
						if($cursor2->items[$j]["fuente"]["cod"]==$fuen["cod"]){
							array_push($model->importes[$i]["importe"][$index], $cursor2->items[$j]["importe"]);	
						}else{
							array_push($model->importes[$i]["importe"][$index], "0");
						}
					}
					$index++;
				}
			}else{
				$model->items[$i]=null;
				$model->importes[$i]=null;
			}		
		}
		$model->items = array_values(array_filter($model->items));
		$model->importes = array_values(array_filter($model->importes));
		$model->p_fuentes = $fuentes->items;
		$model->num_fuen = count($fuentes->items);
		$f->response->view("pr/presmodi.export",$model);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['origen']="PM";
			$data['etapa']="A";
			$data['num_credito']=null;
			$f->model("pr/pres")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Presupuesto Institucional : Apertura',
				'descr'=>'Se Habilit&oacute; una partida de <b>S/. '.$data["importe"].'</b> de la fuente de financiamiento <b>'.$data["fuente"]["nomb"].'</b> y clasificador '.$data["clasificador"]["cod"].' para <b>'.$data["organizacion"]["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/pres")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_v1(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['origen']="PM";
			$data['etapa']="A";
			$data['num_credito']=null;

			$prog=$data['prog']['_id'];
			$data['actividad'] = $data['prog']['actividad'];
			$data['componente'] = $data['prog']['componente'];
			$data['prog']=array();
			$data['prog']['_id']=$prog;

			$f->model("pr/pres")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Presupuesto Institucional : Apertura',
				'descr'=>'Se Habilit&oacute; una partida de <b>S/. '.$data["importe"].'</b> de la fuente de financiamiento <b>'.$data["fuente"]["nomb"].'</b> y clasificador '.$data["clasificador"]["cod"].' para <b>'.$data["programa"]["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/pres")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_modi(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			if($f->request->data['ampliaciones']!=0){
				for($i=0;$i<count($data['ampliaciones']);$i++){
					$data['ampliaciones'][$i]['fecreg'] = new MongoDate();
					$data['ampliaciones'][$i]['origen'] = "PM";
					$data['ampliaciones'][$i]['etapa'] = "M";
					$data['ampliaciones'][$i]['num_credito'] = (int)$data['num_credito'];
					$f->model("pr/pres")->params(array('data'=>$data['ampliaciones'][$i]))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'PR',
						'bandeja'=>'Presupuesto Institucional : Modificaci&oacute;n',
						'descr'=>'Se Habilit&oacute; una partida de <b>S/. '.$data['ampliaciones'][$i]["importe"].'</b> de la fuente de financiamiento <b>'.$data['ampliaciones'][$i]["fuente"]["nomb"].'</b> y clasificador '.$data['ampliaciones'][$i]["clasificador"]["cod"].' para <b>'.$data['ampliaciones'][$i]["organizacion"]["nomb"].'</b>.'
					))->save('insert');
				}
			}
			if($f->request->data['transferencias']!=0){
				for($i=0;$i<count($data['transferencias']);$i++){
					$data['transferencias'][$i]['fecreg'] = new MongoDate();
					$data['transferencias'][$i]['origen'] = "PM";
					$data['transferencias'][$i]['etapa'] = "M";
					$data['transferencias'][$i]['num_credito'] = (int)$data['num_credito'];
					//print_r($data['ampliaciones'][$i]);
					//array para clasificador de origen
					$origen = array();
					$origen['transferencias']['fecreg'] = new MongoDate();
					$origen['transferencias']['ref'] = $data['transferencias'][$i]['ref'];
					$origen['transferencias']['origen'] = "PM";
					$origen['transferencias']['etapa'] = "M";
					$origen['transferencias']['num_nota'] = (int)$data['num_nota'];
					$origen['transferencias']['periodo'] = $data['transferencias'][$i]['periodo'];
					$origen['transferencias']['fuente']=$data['transferencias'][$i]['fuente'];
					$origen['transferencias']['importe']="-".$data['transferencias'][$i]['importe'];
					$origen['transferencias']['clasificador']=$f->request->data['transferencias'][$i]['origen']['clasificador'];	
					$origen['transferencias']['organizacion']=$f->request->data['transferencias'][$i]['origen']['organizacion'];
					$origen['transferencias']['actividad']=$f->request->data['transferencias'][$i]['origen']['actividad'];
					$origen['transferencias']['componente']=$f->request->data['transferencias'][$i]['origen']['componente'];
					if(isset($f->request->data['transferencias'][$i]['origen']['meta'])){
						$origen['transferencias']['meta'] = $f->request->data['transferencias'][$i]['origen']['meta'];
					}
					$f->model("pr/pres")->params(array('data'=>$origen['transferencias']))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'PR',
						'bandeja'=>'Presupuesto Institucional : Modificaci&oacute;n',
						'descr'=>'Se Habilit&oacute; una partida de <b>S/. '.$origen['transferencias']["importe"].'</b> de la fuente de financiamiento <b>'.$origen['transferencias']["fuente"]["nomb"].'</b> y clasificador '.$origen['transferencias']["clasificador"]["cod"].' para <b>'.$origen['transferencias']["organizacion"]["nomb"].'</b>.'
					))->save('insert');
					$destino = array();
					$destino['transferencias']['fecreg'] = new MongoDate();
					$destino['transferencias']['ref'] = $data['transferencias'][$i]['ref'];
					$destino['transferencias']['origen'] = "PM";
					$destino['transferencias']['etapa'] = "M";
					$destino['transferencias']['num_nota'] = (int)$data['num_nota'];
					$destino['transferencias']['periodo'] = $data['transferencias'][$i]['periodo'];
					$destino['transferencias']['importe']=$data['transferencias'][$i]['importe'];
					$destino['transferencias']['clasificador']=$data['transferencias'][$i]['destino']['clasificador'];
					$destino['transferencias']['organizacion']=$data['transferencias'][$i]['destino']['organizacion'];
					$destino['transferencias']['actividad']=$data['transferencias'][$i]['destino']['actividad'];
					$destino['transferencias']['componente']=$data['transferencias'][$i]['destino']['componente'];
					$destino['transferencias']['fuente']=$data['transferencias'][$i]['fuente'];
					if(isset($data['transferencias'][$i]['destino']['meta'])){
						$destino['transferencias']['meta'] = $data['transferencias'][$i]['destino']['meta'];
					}
					$f->model("pr/pres")->params(array('data'=>$destino['transferencias']))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'PR',
						'bandeja'=>'Presupuesto Institucional : Modificaci&oacute;n',
						'descr'=>'Se Inhabilit&oacute; una partida de <b>S/. '.$destino['transferencias']["importe"].'</b> de la fuente de financiamiento <b>'.$destino['transferencias']["fuente"]["nomb"].'</b> y clasificador '.$destino['transferencias']["clasificador"]["cod"].' para <b>'.$destino['transferencias']["organizacion"]["nomb"].'</b>.'
					))->save('insert');
				}
			}
			$f->response->json($f->request->data);
			//$f->model("pr/pres")->params(array('data'=>$data))->save("insert");
		}else{
			//$f->model("pr/pres")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_modi_v1(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			if($f->request->data['ampliaciones']!=0){
				for($i=0;$i<count($data['ampliaciones']);$i++){
					$data['ampliaciones'][$i]['fecreg'] = new MongoDate();
					$data['ampliaciones'][$i]['origen'] = "PM";
					$data['ampliaciones'][$i]['etapa'] = "M";
					$data['ampliaciones'][$i]['num_credito'] = (int)$data['num_credito'];
					$f->model("pr/pres")->params(array('data'=>$data['ampliaciones'][$i]))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'PR',
						'bandeja'=>'Presupuesto Institucional : Modificaci&oacute;n',
						'descr'=>'Se Habilit&oacute; una partida de <b>S/. '.$data['ampliaciones'][$i]["importe"].'</b> de la fuente de financiamiento <b>'.$data['ampliaciones'][$i]["fuente"]["nomb"].'</b> y clasificador '.$data['ampliaciones'][$i]["clasificador"]["cod"].' para <b>'.$data['ampliaciones'][$i]["programa"]["nomb"].'</b>.'
					))->save('insert');
				}
			}
			if($f->request->data['transferencias']!=0){
				for($i=0;$i<count($data['transferencias']);$i++){
					$data['transferencias'][$i]['fecreg'] = new MongoDate();
					$data['transferencias'][$i]['origen'] = "PM";
					$data['transferencias'][$i]['etapa'] = "M";
					$data['transferencias'][$i]['num_credito'] = (int)$data['num_credito'];
					//print_r($data['ampliaciones'][$i]);
					//array para clasificador de origen
					$origen = array();
					$origen['transferencias']['fecreg'] = new MongoDate();
					$origen['transferencias']['ref'] = $data['transferencias'][$i]['ref'];
					$origen['transferencias']['origen'] = "PM";
					$origen['transferencias']['etapa'] = "M";
					$origen['transferencias']['num_nota'] = (int)$data['num_nota'];
					$origen['transferencias']['periodo'] = $data['transferencias'][$i]['periodo'];
					$origen['transferencias']['fuente']=$data['transferencias'][$i]['fuente'];
					$origen['transferencias']['importe']="-".$data['transferencias'][$i]['importe'];
					$origen['transferencias']['clasificador']=$f->request->data['transferencias'][$i]['origen']['clasificador'];	
					$origen['transferencias']['programa']=$f->request->data['transferencias'][$i]['origen']['programa'];
					$origen['transferencias']['actividad']=$f->request->data['transferencias'][$i]['origen']['actividad'];
					$origen['transferencias']['componente']=$f->request->data['transferencias'][$i]['origen']['componente'];
					if(isset($f->request->data['transferencias'][$i]['origen']['meta'])){
						$origen['transferencias']['meta'] = $f->request->data['transferencias'][$i]['origen']['meta'];
					}
					$f->model("pr/pres")->params(array('data'=>$origen['transferencias']))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'PR',
						'bandeja'=>'Presupuesto Institucional : Modificaci&oacute;n',
						'descr'=>'Se Habilit&oacute; una partida de <b>S/. '.$origen['transferencias']["importe"].'</b> de la fuente de financiamiento <b>'.$origen['transferencias']["fuente"]["nomb"].'</b> y clasificador '.$origen['transferencias']["clasificador"]["cod"].' para <b>'.$origen['transferencias']["programa"]["nomb"].'</b>.'
					))->save('insert');
					$destino = array();
					$destino['transferencias']['fecreg'] = new MongoDate();
					$destino['transferencias']['ref'] = $data['transferencias'][$i]['ref'];
					$destino['transferencias']['origen'] = "PM";
					$destino['transferencias']['etapa'] = "M";
					$destino['transferencias']['num_nota'] = (int)$data['num_nota'];
					$destino['transferencias']['periodo'] = $data['transferencias'][$i]['periodo'];
					$destino['transferencias']['importe']=$data['transferencias'][$i]['importe'];
					$destino['transferencias']['clasificador']=$data['transferencias'][$i]['destino']['clasificador'];
					$destino['transferencias']['programa']=$data['transferencias'][$i]['destino']['programa'];
					$destino['transferencias']['actividad']=$data['transferencias'][$i]['destino']['actividad'];
					$destino['transferencias']['componente']=$data['transferencias'][$i]['destino']['componente'];
					$destino['transferencias']['fuente']=$data['transferencias'][$i]['fuente'];
					if(isset($data['transferencias'][$i]['destino']['meta'])){
						$destino['transferencias']['meta'] = $data['transferencias'][$i]['destino']['meta'];
					}
					$f->model("pr/pres")->params(array('data'=>$destino['transferencias']))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'PR',
						'bandeja'=>'Presupuesto Institucional : Modificaci&oacute;n',
						'descr'=>'Se Inhabilit&oacute; una partida de <b>S/. '.$destino['transferencias']["importe"].'</b> de la fuente de financiamiento <b>'.$destino['transferencias']["fuente"]["nomb"].'</b> y clasificador '.$destino['transferencias']["clasificador"]["cod"].' para <b>'.$destino['transferencias']["programa"]["nomb"].'</b>.'
					))->save('insert');
				}
			}
			$f->response->json($f->request->data);
			//$f->model("pr/pres")->params(array('data'=>$data))->save("insert");
		}else{
			//$f->model("pr/pres")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_aper_update(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
		if(!isset($f->request->data['partidasq'])){
				for($i=0;$i<count($data['partidase']);$i++){
					$model = $f->model('pr/pres')->params(array("id"=>$data['partidase'][$i]))->delete('part');
				}
		}else{
				$array = array_diff($data['partidase'], $data['partidasq']);
				$todelete = array_values($array);
				for($i=0;$i<count($todelete);$i++){
				 	$model = $f->model('pr/pres')->params(array( "id"=> $todelete[$i] ))->delete('part');
				}
		}
		}else{
		}
		$f->response->print("true");
	}
	function execute_all_partidas_filter(){
		global $f;
		$model = $f->model("pr/pres")->params(array("_id"=>$f->request->id,"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"organizacion"=>$f->request->organizacion,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("partidas_filter_edit");
		$f->response->json( $model->items );
	}
	function execute_all_partidas_filter_v1(){
		global $f;
		$model = $f->model("pr/pres")->params(array("_id"=>$f->request->id,"tipo"=>$f->request->tipo,"periodo"=>$f->request->periodo,"mes"=>$f->request->mes,"programa"=>$f->request->programa,"etapa"=>"A","clasificador"=>$f->request->clasificador))->get("partidas_filter_edit");
		$f->response->json( $model->items );
	}
	function execute_details(){
		global $f;
		$f->response->view("pr/clas.details");
	}
	function execute_aperpartnew(){
		global $f;
		$f->response->view("pr/presaper.new.part");
	}
	function execute_aperpartedit(){
		global $f;
		$f->response->view("pr/presaper.edit.part");
	}
	function execute_editmodi(){
		global $f;
		$f->response->view("pr/pres.modi.edit");
	}
	function execute_edit_nota(){
		global $f;
		$f->response->view("pr/pres.modi_nota.edit");
	}
	function execute_newampli(){
		global $f;
		$f->response->view("pr/pres.ampli.new");
	}
	function execute_newtrans(){
		global $f;
		$f->response->view("pr/pres.trans.new");
	}
	function execute_select(){
		global $f;
		$f->response->view("pr/clas.select");
	}
}
?>