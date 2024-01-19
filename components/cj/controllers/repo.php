<?php
class Controller_cj_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("cj/repo.grid");
	}
	function execute_nivi(){
		global $f;
		//52b45e41a4b5c36008000091
		
		
		
		$fin_ano = intval($f->request->data['ano']);
		$fin_mes = intval($f->request->data['mes'])+1;
		if($fin_mes==13){
			$fin_mes--;
			$fin_ano++;
		}
		if($fin_mes<10) $fin_mes = '0'.$fin_mes;
		$fin_mes .= '';

		
		
		
		$fecini = new MongoDate(strtotime($f->request->data['ano']."-".$f->request->data['mes']."-01 -1 hour"));
		$fecfin = new MongoDate(strtotime($fin_ano."-".$fin_mes."-01 -1 hour"));
		$comp = $f->model('cj/comp')->params(array(
			'filter'=>array(
				'items.cuenta_cobrar.servicio._id'=>new MongoId('52b45e41a4b5c36008000091'),
				'fecreg'=>array('$gt' => $fecini, '$lte' => $fecfin),
				'estado'=>array('$ne'=>'X')
			)
		))->get('all')->items;
		/*foreach ($comp as $key => $item) {
			echo $item['num']."<br />";
		}
		die();
		print_r($comp);die();*/
		foreach ($comp as $key => $item) {
			$true = false;
			foreach ($item['items'] as $it) {
				if($true==false){
					if($it['cuenta_cobrar']['servicio']['_id']==new MongoId('52b45e41a4b5c36008000091')){
						$cta = $f->model('cj/cuen')->params(array('_id'=>$it['cuenta_cobrar']['_id']))->get('one')->items;
						$oper = $f->model('cm/oper')->params(array('_id'=>$cta['operacion']))->get('one')->items;
						$comp[$key]['operacion'] = $oper;
						$comp[$key]['espacio'] = $f->model('cm/espa')->params(array('_id'=>$oper['espacio']['_id']))->get('one2')->items;
						if($comp[$key]['espacio']==null){
							$comp[$key]['espacio'] = $f->model('cm/espa')->params(array('_id'=>$cta['espacio']['_id']))->get('one2')->items;
							if(isset($comp[$key]['espacio']['nicho'])){
								$comp[$key]['espacio']['nicho']['pabellon'] = $f->model('cm/pabe')->params(array('_id'=>$comp[$key]['espacio']['nicho']['pabellon']['_id']))->get('one')->items;
							}
						}
						$true = true;
					}else if($it['cuenta_cobrar']['servicio']['_id']==new MongoId('529f7a28a4b5c36009000039')){
						$cta = $f->model('cj/cuen')->params(array('_id'=>$it['cuenta_cobrar']['_id']))->get('one')->items;
						$oper = $f->model('cm/oper')->params(array('_id'=>$cta['operacion']))->get('one')->items;
						$comp[$key]['operacion'] = $oper;
						$comp[$key]['espacio'] = $f->model('cm/espa')->params(array('_id'=>$oper['espacio']['_id']))->get('one2')->items;
						if($comp[$key]['espacio']==null){
							$comp[$key]['espacio'] = $f->model('cm/espa')->params(array('_id'=>$cta['espacio']['_id']))->get('one2')->items;
							if(isset($comp[$key]['espacio']['nicho'])){
								$comp[$key]['espacio']['nicho']['pabellon'] = $f->model('cm/pabe')->params(array('_id'=>$comp[$key]['espacio']['nicho']['pabellon']['_id']))->get('one')->items;
							}
						}
						$true = true;
					}
				}
			}
		}
		//print_r($comp);die();
		$f->response->view("cj/repo.nicho_vida.print",array(
			'data'=>$comp,'params'=>$f->request->data
		));
	}
	/*function execute_nivi(){
		global $f;
		$model = $f->model("cm/oper")->get("all_oper");//esto se debe optimizar por que al haber mas data sera mas lento
		$array["items"] = array();
		$array["filtros"] = array(
					"mes"=>$f->request->mes,
					"ano"=>$f->request->ano
		);
		if($model->items!=null){
			foreach($model->items as $item){
				$cuentas_cobrar = $f->model("cj/cuen")->params(array("_id"=>$item["cuentas_cobrar"]))->get("one")->items;
				if($cuenta_cobrar["estado"]=="C"){
					if(isset($cuenta_cobrar["comprobantes"])){
						foreach($cuenta_cobrar["comprobantes"] as $comp){
							$comprobante = $f->model("cj/comp")->params(array("_id"=>$comp,"fecini"=>new MongoDate(strtotime($f->request->ano."-".$f->request->mes."-01")),"fecfin"=>new MongoDate(strtotime($f->request->ano."-".(floatval($f->request->mes)+1)."-01"))))->get("one")->items;
							if($comprobante!=null){
								$espacio = $f->model("cm/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one2")->items;
								array_push($array["items"], array(
									"nicho_num"=>$espacio["nicho"]["num"],
									"fila"=>$espacio["nicho"]["fila"],
									"pabellon"=>$espacio["nicho"]["pabellon"]["nomb"],
									"paga"=>$model->items["propietario"],
									"para"=>"esto falta no se que es",
									"fecha"=>$comprobante["fecreg"],
									"serie"=>$comprobante["serie"],
									"num"=>$comprobante["serie"],
									"valor"=>$cuentas_cobrar["total"]
								));
							}
						}
					}			
				}
			}
		}
		if(count($array["items"])>0){
			$f->response->view("cj/repo.nivi.print",$array);
		}else{
			$f->response->print("No hay data disponible para este Periodo");
			//$f->response->view("cj/repo.nivi.print",$array);
		}		
	}*/
	function execute_daot_cm(){
		global $f;
		$model = $f->model("cj/cuen")->params(array("modulo"=>"CM","estado"=>"C","ano"=>$f->request->ano))->get("all");
		$tcam = $f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items;
		$model->filtros = array(
					"ano"=>$f->request->ano
		);
		$array = array();
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				if(floatval($item['total'])==0) continue;
				//$model->items[$i]["cliente"] = $f->model("mg/entidad")->params(array("_id"=>$item["cliente"]["_id"]))->get("one")->items;
				if(!isset($array[$item["cliente"]["_id"]->{'$id'}])){
					$array[$item["cliente"]["_id"]->{'$id'}]=array();
					$array[$item["cliente"]["_id"]->{'$id'}]['importe'] = 0;
					$array[$item["cliente"]["_id"]->{'$id'}]['pagos'] = array();
				}
				if($item['moneda']=='S'){
					$array[$item["cliente"]["_id"]->{'$id'}]['importe']+=$item['total'];
				}else{
					$array[$item["cliente"]["_id"]->{'$id'}]['importe']+=($item['total']*$tcam['valor']);
				}
				array_push($array[$item["cliente"]["_id"]->{'$id'}]['pagos'],$item);
			}
			$response = array();
			foreach($array as $key=>$cliente){
				if($cliente['importe']>=7000){
					array_push($response,array(
						"cliente"=>$f->model("mg/entidad")->params(array("_id"=>new MongoId($key)))->get("one")->items,
						"moneda"=>'S',
						"total"=>$cliente['importe']
					));
					/*foreach($cliente['pagos'] as $cpc){
						array_push($response,$cpc);
					}*/
				}
			}
			//$f->response->view("cj/repo.daot_cm.print",$model);
			$f->response->view("cj/repo.daot_cm.print",array('items'=>$response));
		}else{
			$f->response->print("No hay data disponible para este periodo");
		}
	}
	function execute_daot_in(){
		global $f;
		$model = $f->model("cj/cuen")->params(array("modulo"=>"IN","estado"=>"C","ano"=>$f->request->ano))->get("all");
		$model->filtros = array(
			"ano"=>$f->request->ano
		);
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				$model->items[$i]["cliente"] = $f->model("mg/entidad")->params(array("_id"=>$item["cliente"]["_id"]))->get("one")->items;
			}		
			$f->response->view("cj/repo.daot_in.print",$model);
		}else{
			$f->response->print("No hay data disponible para este periodo");
		}
	}
	function execute_ingr_anu(){
		global $f;
		$model = $f->model("cj/cuen")->params(array("modulo"=>"IN","estado"=>"C","ano"=>$f->request->ano))->get("all");
		if($model->items!=null){
			$data = array(
				"ano"=>$f->request->ano,
				"cant_s"=>array(0,0,0,0,0,0,0,0,0,0,0,0),
				"cant_d"=>array(0,0,0,0,0,0,0,0,0,0,0,0),
				"meses"=>array('ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SET','OCT','NOV','DIC')
			);
			foreach($model->items as $item){
				if($item["moneda"]=="S")$data["cant_s"][floatval(Date::format($item["fecreg"]->sec, 'm'))]+=floatval($item["total"]);
				else $data["cant_d"][floatval(Date::format($item["fecreg"]->sec, 'm'))-1]+=floatval($item["total"]);
			}
			$f->response->view("cj/repo.ingr_anu_graph.print",$data);
		}else{
			$f->response->print("No hay data disponible");
		}
	}
	function execute_ingr_com(){
		global $f;
		$ano1 = $f->model("cj/cuen")->params(array("modulo"=>"IN","estado"=>"C","ano"=>$f->request->ano1))->get("all");
		$ano2 = $f->model("cj/cuen")->params(array("modulo"=>"IN","estado"=>"C","ano"=>$f->request->ano2))->get("all");
		$data = array(
			"ano1"=>$f->request->ano1,
			"ano2"=>$f->request->ano2,
			"cant_1_s"=>array(0,0,0,0,0,0,0,0,0,0,0,0),
			"cant_1_d"=>array(0,0,0,0,0,0,0,0,0,0,0,0),
			"cant_2_s"=>array(0,0,0,0,0,0,0,0,0,0,0,0),
			"cant_2_d"=>array(0,0,0,0,0,0,0,0,0,0,0,0),
			"meses"=>array('ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SET','OCT','NOV','DIC')
		);
		if($ano1->items!=null){
			foreach($ano1->items as $item){
				if($item["moneda"]=="S")$data["cant_1_s"][floatval(Date::format($item["fecreg"]->sec, 'm'))]+=floatval($item["total"]);
				else $data["cant_1_d"][floatval(Date::format($item["fecreg"]->sec, 'm'))-1]+=floatval($item["total"]);
			}
		}
		if($ano2->items!=null){
			foreach($ano2->items as $item){
				if($item["moneda"]=="S")$data["cant_2_s"][floatval(Date::format($item["fecreg"]->sec, 'm'))]+=floatval($item["total"]);
				else $data["cant_2_d"][floatval(Date::format($item["fecreg"]->sec, 'm'))-1]+=floatval($item["total"]);
			}
		}
		$f->response->view("cj/repo.ingr_com_graph",$data);		
	}
	function execute_deud(){
		global $f;
		$model = $f->model("cj/cuen")->params(array("organizacion"=>new MongoId($f->request->organizacion)))->get("deudores");
		$array = array();
		/*foreach($model->items as $item){
			if(!isset($array[$item["cliente"]["_id"]->{'$id'}])){
				$array[$item["cliente"]["_id"]->{'$id'}] = $item["cliente"];
				$array[$item["cliente"]["_id"]->{'$id'}]["operaciones"] = array();
			}
			if(!isset($array[$item["cliente"]["_id"]->{'$id'}]["operaciones"][$item["operacion"]->{'$id'}])){
				$array[$item["cliente"]["_id"]->{'$id'}]["operaciones"][$item["operacion"]->{'$id'}] = $f->model("in/oper")->params(array("_id"=>$item["operacion"]))->get("one")->items;
				$array[$item["cliente"]["_id"]->{'$id'}]["operaciones"][$item["operacion"]->{'$id'}]["items"] = array();
			}
			array_push($array[$item["cliente"]["_id"]->{'$id'}]["operaciones"][$item["operacion"]->{'$id'}]["items"],$item);
		}
		$f->response->view("cj/repo.deud.print",array("items"=>$array));*/
		foreach($model->items as $item){
			if(!isset($array[$item["operacion"]->{'$id'}])){
				$array[$item["operacion"]->{'$id'}] = $f->model("in/oper")->params(array("_id"=>$item["operacion"]))->get("one")->items;
				$array[$item["operacion"]->{'$id'}]["items"] = array();
			}
			array_push($array[$item["operacion"]->{'$id'}]["items"],$item);
		}
		$f->response->view("cj/repo.deud.print",array("items"=>$array));
	}
	function execute_cont(){
		global $f;
		$model = $f->model("in/oper")->params(array("arren"=>new MongoId($f->request->arrendatario),"espa"=>new MongoId($f->request->espacio)))->get("all_filter");
		foreach($model->items as $i=>$item){
			$model->items[$i]["debe"] = 0;
			$model->items[$i]["items"] = array();
			foreach($item["arrendamiento"]["rentas"] as $j=>$renta){
				$model->items[$i]["debe"]+=$renta["importe"];
				if($renta["estado"]=="PG"){
					$renta["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$renta["cuenta_cobrar"]))->get("one")->items;
					//$renta["cuenta_cobrar"]["comp"] = 
					$compr = array();
					foreach($renta["cuenta_cobrar"]["comprobantes"] as $comp){
						array_push($compr,$f->model("cj/comp")->params(array("_id"=>$comp))->get("one")->items);
					}
					$renta["cuenta_cobrar"]["comprobantes"] = $compr;
					array_push($model->items[$i]["items"],$renta);
				}
			}
		}
		$model->filtros = array(
			"arre"=>$f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->arrendatario)))->get("one")->items,
			"espa"=>$f->model("in/espa")->params(array("_id"=>new MongoId($f->request->espacio)))->get("one")->items
		);
		$f->response->view("cj/repo.cont.print",$model);
	}
	function execute_reci_ing(){
		global $f;
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;
		//DETALLE
		foreach ($recibo['detalle'] as $k => $row) {
		    $ctas[$k] = $row['cuenta']['cod'];
		}
		array_multisort($ctas, SORT_ASC, $recibo['detalle']);
		//CONTABILIDAD PATRIMONIAL
		foreach ($recibo['cont_patrimonial'] as $k => $row) {
		    $ctasp[$k] = $row['cuenta']['cod'];
		    $ctast[$k] = $row['tipo'];
		}
		array_multisort($ctast,SORT_ASC,$ctasp,SORT_ASC,$recibo['cont_patrimonial']);
		//print_r($recibo);die();
		$f->response->view("cj/repo.rein.print",array('recibo'=>$recibo));
	}
	function execute_registro_ventas(){
		global $f;
		$fec = $f->request->data['ano'].'-'.$f->request->data['mes'].'-01';
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'items.cuenta_cobrar.servicio.organizacion._id'=>new MongoId($f->request->data['orga']),
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			)/*,
			'estado'=>array('$ne'=>'X')*/
		),'fields'=>array(
			'fecreg'=>true,
			'tipo'=>true,
			'serie'=>true,
			'num'=>true,
			'cliente'=>true,
			'total'=>true,
			'estado'=>true,
			'cliente_nuevo'=>true
		),'sort'=>array(
			'fecreg'=>1,
			'serie'=>1,
			'num'=>1
		)))->get("all")->items;
		//echo date('Y-m-d H:i:s',strtotime($fec.' +1 month -1 hour'));die();
		$rede = $f->model("cj/rede")->params(array("filter"=>array(
			'fec_db'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 hour'))
			)
		)))->get("all")->items;
		//print_r($rede);die();
		$f->response->view("cj/repo.registro_ventas.print",array(
			'data'=>$comp,'rede'=>$rede,'params'=>$f->request->data
		));
	}
}
?>