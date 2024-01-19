<?php
class Controller_ts_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ts/repo.grid");
	}
	function execute_rendi(){
		global $f;
		$model = $f->model("ts/sald")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$model0 = $f->model("ts/cjch")->params(array("_id"=>$model->items["caja_chica"]["_id"]))->get("one");
			$model1 = $f->model("ts/sald")->params(array("caja"=>$model->items["caja_chica"]["_id"],"ultimo"=>$model->items["fecreg"],"limit"=>1))->get("rendi");
			$movs = $f->model("ts/mocj")->params(array(
				"saldo"=>new MongoId($f->request->id)
			))->get("all_saldo");
			$filtros = array(
				"fecha"=>$model->items["fecren"],
				"num"=>$model->items["cod"]
			);
			$acti = array();
			$data = array();
			$afecta = $model->items['afectacion'];
			$index = 0;
			if(count($afecta)>0){
				foreach ($afecta as $item){
					if(!isset($acti[$item["organizacion"]["actividad"]["_id"]->{'$id'}])){
						$acti[$item["organizacion"]["actividad"]["_id"]->{'$id'}]["actividad"] = $item["organizacion"]["actividad"];
						$acti[$item["organizacion"]["actividad"]["_id"]->{'$id'}]["index"] = $index;
						$acti[$item["organizacion"]["actividad"]["_id"]->{'$id'}]["monto"] = 0;
						$index++;
					}
				}	
				foreach($afecta as $item){
					foreach($item["gasto"] as $clas){
						if(!isset($data[$clas["clasificador"]["_id"]->{'$id'}])){
							$data[$clas["clasificador"]["_id"]->{'$id'}]["clasificador"] = $clas["clasificador"];
							$data[$clas["clasificador"]["_id"]->{'$id'}]["importe"] = $acti;
						}
						if(!isset($data[$clas["clasificador"]["_id"]->{'$id'}]["importe"][$item["organizacion"]["actividad"]["_id"]->{'$id'}])){
							$data[$clas["clasificador"]["_id"]->{'$id'}]["importe"][$item["organizacion"]["actividad"]["_id"]->{'$id'}]["actividad"] = $item["organizacion"]["actividad"];
							$data[$clas["clasificador"]["_id"]->{'$id'}]["importe"][$item["organizacion"]["actividad"]["_id"]->{'$id'}]["monto"] = 0;
						}
						$data[$clas["clasificador"]["_id"]->{'$id'}]["importe"][$item["organizacion"]["actividad"]["_id"]->{'$id'}]["monto"]+=$clas["monto"];
					}
				}
			}
			$data = array_values($data);		
			//print_r($data);die();
			
			$f->response->view("ts/rendi.print",array(
				'saldo'=>$model->items,
				'movs'=>$movs->items,
				'ultimo'=>array($model1->items),
				'filtros'=>$filtros,
				"afectacion"=>$data
			));
			
		}else{
			$f->response->print("Ha ocurrido un error: No hay data disponible");
		}
	}
	function execute_movi_efe(){
		global $f;
		$model = $f->model("ts/moef")->params(array('periodo'=>$f->request->data['periodo']))->get("all_periodo");
		if($model->items!=null){
			$model->periodo = $f->request->data['periodo'];
			$f->response->view("ts/repo.mefe.export",$model);
		}else{
			$f->response->print("No hay data disponible para generar este Reporte");
		}	
	}
	function execute_movi_cue(){
		global $f;
		$model = $f->model("ts/movcue")->params(array('periodo'=>$f->request->data['periodo'],'ctban'=>new MongoId($f->request->data['ctban'])))->get("all_periodo");
		if($model->items!=null){
			$model->periodo = $f->request->data['periodo'];
			$f->response->view("ts/repo.mcue.export",$model);
		}else{
			$f->response->print("No hay data disponible para generar este Reporte");
		}	
	}
	function execute_movi_ban(){
		global $f;
		$model = $f->model("ts/moba")->params(array('periodo'=>$f->request->data['periodo'],'ctban'=>new MongoId($f->request->data['ctban'])))->get("all_periodo");
		if($model->items!=null){
			$model->periodo = $f->request->data['periodo'];
			$f->response->view("ts/repo.mban.export",$model);
		}else{
			$f->response->print("No hay data disponible para generar este Reporte");
		}	
	}
	function execute_movi_cjb()
	{
		global $f;
		$model = $f->model("ts/cjba")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all");
		if($model->items!=null){
			$array["cuentas_debe"] = array();
			$array["cuentas_haber"] = array();
			$array["items"] = array();
			if($model->items!=null){
				foreach($model->items as $i=>$item){
					if(count($item["organizacion"])>1){
						foreach($item["organizacion"] as $j=>$org){
							$orga = $f->model("mg/orga")->params(array("_id"=>$org["_id"]))->get("one");
							$item["organizacion"][$j] = $orga;
						}
					}
					$item["cuentas_debe"] = array();
					$item["cuentas_haber"] = array();	
					$i_d = 1;
					$i_h = 1;			
					foreach($item["cuentas"] as $cuenta){
						if($cuenta["tipo"]=="D"){
							if(!isset($array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}])){
								$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}] = $cuenta["cuenta"];
								$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}]["col"] = $i_d;
								$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] = floatval($cuenta["monto"]);
								$i_d++;
							}else{
								$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] += floatval($cuenta["monto"]);
							}
							array_push($item["cuentas_debe"],$cuenta);			
						}else{
							if(!isset($array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}])){
								$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}] = $cuenta["cuenta"];
								$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}]["col"] = $i_h;
								$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] = floatval($cuenta["monto"]);
								$i_h++;
							}else{
								$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] += floatval($cuenta["monto"]);
							}
							array_push($item["cuentas_haber"],$cuenta);	
						}				
					}
					array_push($array["items"],$item);
				}
			}
			$array["params"] = array("mes"=>$f->request->mes,"ano"=>$f->request->ano);
			$array["cuentas_debe"] = array_values($array["cuentas_debe"]);
			$array["cuentas_haber"] = array_values($array["cuentas_haber"]);
			$f->response->view("ts/repo.cjba.export",$array);
		}else{
			$f->response->print("No hay data disponible para generar este Reporte");
		}
	}
	function execute_auxs_caja(){
		global $f;
		$id = new MongoId($f->request->data['id']);
		$saldo = $f->model('ts/sald')->params(array('_id'=>$id))->get('one')->items;
		$saldo_old = $f->model('ts/sald')->params(array('cod'=>$saldo['cod'],'caja'=>$saldo['caja_chica']['_id']))->get('by_cod')->items;
		$movs = $f->model('ts/mocj')->params(array('saldo'=>$id))->get('all_saldo')->items;
		$f->response->view("ts/repo.auxs_caja.export",array('saldo'=>$saldo,'saldo_old'=>$saldo_old,'movs'=>$movs));
	}
	function execute_index2() {
		global $f;
		$f->response->view("ts/repo.view");
	}	
}
?>