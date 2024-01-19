<?php
class Controller_ct_procg extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres.procg");
	}
	function execute_lista_saved(){
		global $f;
		$model = $f->model("ct/procg")->params(array(
			"mes"=>floatval($f->request->mes),
			"ano"=>$f->request->ano,
			"organizacion"=>new MongoId($f->request->organizacion),
			"fuente"=>new MongoId($f->request->fuente)
		))->get("lista");
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				$cursor2 = $f->model("pr/clas")->params(
							array(
								"_id"=>$item["clasificador"]["_id"],						
							)
				)->get("one")->items;
				$model->items[$i]["clasificador"] = $cursor2;
			}
		}
		$f->response->json( $model );
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>1,"page_rows"=>100000,"tipo"=>"G","id"=>""))->get("lista");
		for($i=0;$i<count($model->items);$i++){
			$cursor= $f->model("ct/auxg")->params(array(
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"organizacion"=>$f->request->organizacion,
				"clasificador"=>$model->items[$i]["cod"],
				"fuente"=>$f->request->fuente			
			))->get("lista_ot")->items;
			$cursor2 = $f->model("pr/pres")->params(array(
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"organizacion"=>$f->request->organizacion,
				"clasificador"=>$model->items[$i]["cod"],
				"fuente"=>$f->request->fuente				
			))->get("ultimo")->items;
			$model->items[$i]["ppto"] = array();
			if($cursor2=="zero"){
				//$model->items[$i]["ppto"]=array("_id"=>null,"importe"=>"0.00");
				array_push($model->items[$i]["ppto"],array("_id"=>null,"importe"=>"0.00"));
			}else{
				//$model->items[$i]["ppto"]=array("_id"=>$cursor2["_id"]->{'$id'},"importe"=>$cursor2["importe"]);
				foreach($cursor2 as $i_ppto){
					$ppto = array("_id"=>$i_ppto["_id"]->{'$id'},"importe"=>$i_ppto["importe"]);
					array_push($model->items[$i]["ppto"],$ppto);
				}
			}
			$cursor3 = $f->model("ct/procg")->params(array(
				"mes"=>floatval($f->request->mes),
				"ano"=>$f->request->ano,
				"organizacion"=>new MongoId($f->request->organizacion),
				"subespecifica"=>$model->items[$i]["_id"],
				"fuente"=>new MongoId($f->request->fuente)				
			))->get("ultimo")->items;
			if($cursor3=="reinicio"){
				$model->items[$i]["acum_comp_ant"]="0.00";
				$model->items[$i]["acum_ejec_ant"]="0.00";
			}else{
				$model->items[$i]["acum_comp_ant"]=$cursor3["acumulado_comp"];
				$model->items[$i]["acum_ejec_ant"]=$cursor3["acumulado_ejec"];
			}
			$model->items[$i]["compromiso"]=array();
			$model->items[$i]["ejecucion"]=array();
			if($cursor!=null){	
				for($j=0;$j<count($cursor);$j++){
					if($cursor[$j]["clase"]=="CP"){
						array_push($model->items[$i]["ejecucion"],$cursor[$j]["ejec_pres"]["monto"]);
					}else{
						array_push($model->items[$i]["compromiso"],$cursor[$j]["ejec_pres"]["monto"]);
					}
				}				
			}else{
				$model->items[$i]=null;
			}
		}
		//rearm the array 
		$output->items = null;
		foreach($model->items as $item){
			if($item!=null){
				$output->items[] = $item;
			}
		}
		$f->response->json( $output );
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/procg")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
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
			$data["organizacion"]["_id"] = new MongoId($data["organizacion"]["_id"]);
			$data["organizacion"]["actividad"]["_id"] = new MongoId($data["organizacion"]["actividad"]["_id"]['$id']);
			$data["organizacion"]["componente"]["_id"] = new MongoId($data["organizacion"]["componente"]["_id"]['$id']);
			$data["fuente"]["_id"] = new MongoId($data["fuente"]["_id"]);
			$data["funcion"]["_id"] = new MongoId($data["funcion"]["_id"]);
			$data["programa"]["_id"] = new MongoId($data["programa"]["_id"]);
			$data["subprograma"]["_id"] = new MongoId($data["subprograma"]["_id"]);
			for($i=0;$i<count($data["filas"]);$i++){
				$fila = array();
				$fila["estado"] = $data["estado"];
				$fila["fecreg"] = new MongoDate();
				$fila["autor"] = $data["autor"];
				$fila["periodo"] = $data["periodo"];
				$fila["periodo"]["mes"] = floatval($fila["periodo"]["mes"]);
				$fila["organizacion"] = $data["organizacion"];
				$fila["fuente"] = $data["fuente"];
				$fila["funcion"] = $data["funcion"];
				$fila["programa"] = $data["programa"];
				$fila["subprograma"] = $data["subprograma"];
				$fila["clasificador"] = $data["filas"][$i]["clasificador"];
				$fila["clasificador"]["_id"] = new MongoId($fila["clasificador"]["_id"]);
				$fila["ppto"] = $data["filas"][$i]["ppto"];
				if(isset($fila["ppto"])){
					if(count($fila["ppto"])>0){
						for($j=0;$j<count($fila["ppto"]);$j++){
							if($fila["ppto"][$j]["_id"]=="null"){
								$fila["ppto"][$j]["_id"] = null;
							}else{
								$fila["ppto"][$j]["_id"] = new MongoId($fila["ppto"][$j]["_id"]);
							}					
						}
					}
				}
				/*if($fila["ppto"]["_id"]=="null"){
					$fila["ppto"]["_id"] = null;
				}else{
					$fila["ppto"]["_id"] = new MongoId($fila["ppto"]["_id"]);
				}*/
				$fila["compromiso"] = floatval($data["filas"][$i]["compromiso"]);
				$fila["acumulado_comp"] = floatval($data["filas"][$i]["acumulado_comp"]);
				$fila["ejecucion"] = floatval($data["filas"][$i]["ejecucion"]);
				$fila["acumulado_ejec"] = floatval($data["filas"][$i]["acumulado_ejec"]);
				$fila["saldo"] = floatval($data["filas"][$i]["saldo"]);	
				$f->model("ct/procg")->params(array('data'=>$fila))->save("insert");		
			}			
		}else{
			$f->model("ct/procg")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
}
?>