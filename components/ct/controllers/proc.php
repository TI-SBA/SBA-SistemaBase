<?php
class Controller_ct_proc extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres.proc");
	}
	function execute_lista_saved(){
		global $f;
		$model = $f->model("ct/proc")->params(array(
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
		$model = $f->model("pr/clas")->params(array("page"=>1,"page_rows"=>100000,"tipo"=>"I","id"=>""))->get("lista");
		for($i=0;$i<count($model->items);$i++){
			$cursor= $f->model("ct/auxi")->params(array(
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano,
				"organizacion"=>$f->request->organizacion,
				"clasificador"=>$model->items[$i]["cod"],
				"fuente"=>$f->request->fuente			
			))->get("lista_ri")->items;
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
			$cursor3 = $f->model("ct/proc")->params(array(
				"mes"=>floatval($f->request->mes),
				"ano"=>$f->request->ano,
				"organizacion"=>new MongoId($f->request->organizacion),
				"subespecifica"=>$model->items[$i]["_id"],
				"fuente"=>new MongoId($f->request->fuente)				
			))->get("ultimo")->items;
			if($cursor3=="reinicio"){
				$model->items[$i]["acum_ant"]="0.00";
			}else{
				$model->items[$i]["acum_ant"]=$cursor3["acumulado"];
			}
			$model->items[$i]["monto"]=array();
			if($cursor!=null){	
				for($j=0;$j<count($cursor);$j++){
					array_push($model->items[$i]["monto"],$cursor[$j]["ejec_pres"]["monto"]);
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
		$model = $f->model("ct/proc")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
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
			for($i=0;$i<count($data["filas"]);$i++){
				$fila = array();
				$fila["estado"] = $data["estado"];
				$fila["fecreg"] = new MongoDate();
				$fila["autor"] = $data["autor"];
				$fila["periodo"] = $data["periodo"];
				$fila["periodo"]["mes"] = floatval($fila["periodo"]["mes"]);
				$fila["organizacion"] = $data["organizacion"];
				$fila["fuente"] = $data["fuente"];
				$fila["clasificador"] = $data["filas"][$i]["clasificador"];
				$fila["clasificador"]["_id"] = new MongoId($fila["clasificador"]["_id"]);
				$fila["pim"] = $data["filas"][$i]["pim"];
				if(isset($fila["pim"])){
					if(count($fila["pim"])>0){
						for($j=0;$j<count($fila["pim"]);$j++){
							if($fila["pim"][$j]["_id"]=="null"){
								$fila["pim"][$j]["_id"] = null;
							}else{
								$fila["pim"][$j]["_id"] = new MongoId($fila["pim"][$j]["_id"]);
							}					
						}
					}
				}
				$fila["ingreso"] = floatval($data["filas"][$i]["ingreso"]);
				$fila["acumulado"] = floatval($data["filas"][$i]["acumulado"]);
				$fila["saldo"] = floatval($data["filas"][$i]["saldo"]);	
				$f->model("ct/proc")->params(array('data'=>$fila))->save("insert");		
			}			
		}else{
			$f->model("ct/proc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
}
?>