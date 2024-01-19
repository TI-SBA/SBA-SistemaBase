<?php
class Controller_in_repo extends Controller {	
	function execute_index() {
		global $f;
		$f->response->view("in/repo.grid");
	}
	/*
	 * Situacion de los inmuebles
	 * 
	 * No requiere selecci�n de data
	 * 
	 * Observaciones
	 * -Preguntar por campo ficha
	 */	 
	function execute_situ_inm() {
		global $f;
		$data = $f->model('in/espa')->get('ocupados')->items;
		foreach ($data as $i=>$item){
			$data[$i]['arrendamiento'] = $f->model('in/oper')->params(array(
				'espacio'=>$item['_id'],
				'arrendatario'=>$item['arrendatario']['_id']
			))->get('arrendamiento')->items;
		}
		$f->response->view("in/repo.situ.export",array("items"=>$data));
		//$f->response->json( $data );
	}
	/*
	 * Compromiso de alquileres sin contrato
	 * 
	 * No requiere selecci�n de data
	 * 
	 * Observaciones
	 * -preguntar por fechas de tramite y sin contrato
	 */
	function execute_comp_alq_sin(){
		global $f;
		$data = $f->model('in/oper')->get('rent_ven_sin_cont')->items;
		if($data!=null){
			$array = array();
			$array["TI"] = array();
			$array["OF"] = array();
			$array["HO"] = array();
			$array["ST"] = array();
			$array["CI"] = array();
			$array["ES"] = array();
			$array["CO"] = array();
			$array["VI"] = array();
			$array["OT"] = array();
			foreach($data as $item){
				$espa = $f->model("in/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one")->items;
				if(!isset($array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}])){
					$array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}] = $item["espacio"]["ubic"]["local"];	
					$array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}]["items"] = array();				
				}
				array_push($array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}]["items"],$item);
			}
			//$f->response->json($array);
			$f->response->view("in/repo.comp_alq_sin.print",array("items"=>$array) );
		}else{
			$f->response->print("No se han encontrado resultados");
		}
		
	}
	/*
	 * Compromiso de alquileres con contrato
	 * 
	 * No requiere selecci�n de data
	 */
	function execute_comp_alq_con(){
		global $f;
		$data = $f->model('in/oper')->get('rent_ven_con_cont')->items;
		if($data!=null){
			$array = array();
			$array["TI"] = array();
			$array["OF"] = array();
			$array["HO"] = array();
			$array["ST"] = array();
			$array["CI"] = array();
			$array["ES"] = array();
			$array["CO"] = array();
			$array["VI"] = array();
			$array["OT"] = array();
			foreach($data as $item){
				$espa = $f->model("in/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one")->items;
				if(!isset($array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}])){
					$array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}] = $item["espacio"]["ubic"]["local"];	
					$array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}]["items"] = array();				
				}
				array_push($array[$espa["uso"]][$item["espacio"]["ubic"]["local"]["_id"]->{'$id'}]["items"],$item);
			}
			//$f->response->json($array);
			$f->response->view("in/repo.comp_alq_con.print",array("items"=>$array) );
		}else{
			$f->response->print("No se han encontrado resultados");
		}
	}
	/*
	 * Lista de contratos por fecha de inicio del contrato
	 * 
	 * No requiere selecci�n de data
	 */
	 function execute_nuev_contr(){
	 	global $f;
		$data = $f->model('in/oper')->params(array("feccon"=>$f->request->data["feccon"]))->get('arre_nuev');
		if($data!=null){
			foreach($data->items as $i=>$item){
				$data->items[$i]["espacio"] = $f->model("in/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one")->items;
			}
			$f->response->view("in/nuev.contr.export",$data);
		}else{
			$f->response->print("No se encontraron resultados");
		}
	 }
	 /*
	 * Reporte de Morosidad
	 * 
	 * No requiere selección de data
	 */
	 function execute_moro(){
	 	global $f;
		$data = $f->model('in/oper')->get('rentven');
		if($data->items!=null){
			$f->response->view("in/repo.moro.print",$data);
			//$f->response->view("in/repo.moro.export",$data);
		}else{
			$f->response->print("No se han encontrado resultados");
		}
	 }
	 function execute_deud(){
	 	global $f;
	 	$model = $f->model('in/oper')->params(array("estado"=>$f->request->data["estado"]))->get('rentven');
		$array = array();
		$now = strtotime(date("Y-m-d"));
		foreach($model->items as $i=>$item){
			$model->items[$i]["espacio"] = $f->model("in/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one")->items;
			$espacio = $model->items[$i]["espacio"];
			if(!isset($array[$espacio["uso"]])){
				$array[$espacio["uso"]]["nomb"] = $espacio["uso"];
				$array[$espacio["uso"]]["items"] = array();
			}
			$rentas = array();
			
			foreach($item["arrendamiento"]["rentas"] as $rent){
				if((strtotime(Date::format($rent["fecpago"]->sec,"Y-m-d"))<$now)&&($rent["estado"]=="CR")){
					array_push($rentas,$rent);
				}
			}
			$model->items[$i]["arrendamiento"]["rentas"] = $rentas;
			array_push($array[$espacio["uso"]]["items"],$model->items[$i]);
		}
		$f->response->view("in/repo.deud.print",array("items"=>$array));
	 }
	 function execute_marg(){
	 	global $f;
		$model = $f->model("in/loca")->get("all");
		$f->response->view("in/repo.marg.print",$model);
	 }
	 /*
	  * LISTADO SISBEN
	  * 
	  * -Seleccionar el periodo
	  * 
	  */
	 function execute_listado_sisben(){
	 	global $f;
	 	$params = array(
	 		'filter'=>array(
	 			'arrendamiento.estado'=>'A'
	 		),
	 		'fields'=>array()
	 	);
	 	$arre = $f->model('in/arre')->params($params)->get('custom_data')->items;
	 	foreach ($arre as $ar){
	 		$data[] = array(
	 			'entidad'
	 		);
	 	}
	 }
	 /*
	  * LISTADO SISBEN
	  * 
	  * -Seleccionar el periodo
	  * 
	  */
	 function execute_liqu(){
	 	global $f;
		$model = $f->model("in/oper")->params(array("arren"=>new MongoId($f->request->arren)))->get("liquidaciones");
		$array = array();
		$now = strtotime(date("Y-m-d"));
		foreach($model->items as $i=>$item){
			if(!isset($array[$item["espacio"]["_id"]->{'$id'}])){
				$array[$item["espacio"]["_id"]->{'$id'}] = $f->model("in/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one")->items;
				$array[$item["espacio"]["_id"]->{'$id'}]["items"] = array();
			}
			if(isset($item["arrendamiento"])){
				$rentas = array();
				foreach($item["arrendamiento"]["rentas"] as $rent){
					if((strtotime(Date::format($rent["fecpago"]->sec,"Y-m-d"))<$now)&&($rent["estado"]=="CR")){
						if(!isset($rentas[Date::format($rent["fecpago"]->sec,"Y")])){
							$rentas[Date::format($rent["fecpago"]->sec,"Y")]["ano"] = Date::format($rent["fecpago"]->sec,"Y");
							$rentas[Date::format($rent["fecpago"]->sec,"Y")]["rentas"] = array();
						}
						array_push($rentas[Date::format($rent["fecpago"]->sec,"Y")]["rentas"],$rent);
					}
				}
				$model->items[$i]["arrendamiento"]["rentas"] = $rentas;
				array_push($array[$item["espacio"]["_id"]->{'$id'}]["items"],$model->items[$i]);
				/*$item["arrendamiento"]["rentas"] = $rentas;
				array_push($array[$item["espacio"]["_id"]->{'$id'}]["items"],$item);*/
			}
		}
		$f->response->view("in/repo.liqu.print",array("items"=>$array,"arrendatario"=>$model->items[0]["arrendatario"]));
		//print_r($array);
	 }
	 /*
	  * REPORTE DE SERVICIOS
	  * 
	  * -Seleccionar el arrendatario "arren"
	  * -Seleccionar el servicio "serv"
	  * 
	  */
	 function execute_servicio(){
	 	global $f;
	 	$data = array();
		$arre = $f->model("in/oper")->params(array("arren"=>new MongoId($f->request->arren)))->get("liquidaciones")->items;
		if(isset($arre)){
			foreach ($arre as $j=>$oper){
				$cuentas = $f->model('cj/cuen')->params(array(
			 		'operacion'=>$oper['_id'],
			 		'servicio'=>new MongoId($f->request->data['serv'])
			 	))->get('oper_servicio')->items;
			 	if(isset($cuentas)){
			 		foreach ($cuentas as $cta){
			 			if(isset($cta['comprobantes'])){
			 				foreach($cta['comprobantes'] as $i=>$comp){
			 					$cta['comprobantes'][$i] = $f->model('cj/comp')->params(array('_id'=>$comp))->get('one')->items;
			 				}
			 			}
			 			if(!isset($data[$j]))
			 				$data[$j] = $oper;
			 			$data[$j]['cuentas'][] = $cta;
			 		}
			 	}
			}
		}
		$filtros = array(
			"arren"=>$f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->arren)))->get("one")->items,
			"serv"=>$f->model("mg/serv")->params(array("_id"=>new MongoId($f->request->data['serv'])))->get("one")->items
		);
	 	$f->response->view("in/repo.serv.print",array("items"=>$data,"filtros"=>$filtros));
	 }
	 /*
	  * Reporte de inquilinos
	  * 
	  * -Se selecciona la condicion, sino se busca por todos
	  */
	 function execute_listado_inquilinos(){
	 	global $f;
	 	$params = array(
	 		'filter'=>array('arrendamiento.estado'=>'A'),
	 		'sort'=>array('arrendamiento.condicion'=>1)
	 	);
	 	if($f->request->data['condic']!='')
	 		$params['filter']['arrendamiento.condicion'] = $f->request->data['condic'];
	 	$arre = $f->model('in/oper')->params($params)->get('custom_data')->items;
	 	if(isset($arre)){
		 	foreach ($arre as $i=>$ar){
		 		$arre[$i]['espacio'] = $f->model('in/espa')->params(array('_id'=>$ar['espacio']['_id']))->get('one')->items;
		 	}
	 	}
		$f->response->view("in/repo.listado_inquilinos.export",array("items"=>$arre));
	 }
	 /*
	  * Planilla de cobranza
	  */
	 function execute_planilla_cobranza(){
	 	global $f;
	 	$data = array(
	 		'alquileres'=>array(),
	 		'otros'=>array(),
	 		'tupa'=>array(),
	 		'pagar'=>array(),
	 		'facturas_anuladas'=>array(),
	 		'boletas_anuladas'=>array(),
	 		'recibos_anulados'=>array()
	 	);
	 	/*
	 	 * Se cobra el tupa
	 	 */
	 	$operaciones = $f->model('cj/comp')->params(array('filter'=>array(
	 		'items.cuenta_cobrar.servicio.organizacion._id'=>new MongoId('51a50fff4d4a134411000011'),
	 		'fecreg'=>array(
	 			'$gt'=>new MongoDate(strtotime($f->request->data['dia'].' -1 minute')),
	 			'$lt'=>new MongoDate(strtotime($f->request->data['dia'].' +1 day -1 minute'))
	 		)
	 	)))->get('all')->items;
	 	foreach ($operaciones as $oper){
	 		if($oper['estado']=='X'){
	 			switch($oper['tipo']){
	 				case 'R': $data['recibos_anulados'][] = array('serie'=>$oper['serie'],'num'=>$oper['num']); break;
	 				case 'B': $data['boletas_anuladas'][] = array('serie'=>$oper['serie'],'num'=>$oper['num']); break;
	 				case 'F': $data['facturas_anuladas'][] = array('serie'=>$oper['serie'],'num'=>$oper['num']); break;
	 			}
	 		}else{
		 		foreach ($oper['items'] as $item){
		 			$total = 0;
		 			foreach ($item['conceptos'] as $conc){
		 				$total += floatval($conc['monto']);
		 			}
		 			$data['tupa'][] = array(
		 				'tipo'=>$oper['tipo'],
		 				'serie'=>$oper['serie'],
		 				'num'=>$oper['num'],
		 				'concepto'=>$oper['cliente'],
		 				'moneda'=>$oper['moneda'],
		 				'total'=>$total,
		 				'subtotal'=>$total,
	 					'mora'=>0,
	 					'igv'=>0,
	 					'alquiler'=>0
		 			);
		 		}
	 		}
	 	}
	 	/*
	 	 * Se cobran alquileres y otros
	 	 */
	 	$operaciones = $f->model('cj/comp')->params(array('filter'=>array(
	 		'items.cuenta_cobrar.servicio.organizacion._id'=>new MongoId('51a50edc4d4a13441100000e'),
	 		'fecreg'=>array(
	 			'$gt'=>new MongoDate(strtotime($f->request->data['dia'].' -1 minute')),
	 			'$lt'=>new MongoDate(strtotime($f->request->data['dia'].' +1 day -1 minute'))
	 		)
	 	)))->get('all')->items;
	 	foreach ($operaciones as $oper){
	 		if($oper['estado']=='X'){
	 			switch($oper['tipo']){
	 				case 'R': $data['recibos_anulados'][] = array('serie'=>$oper['serie'],'num'=>$oper['num']); break;
	 				case 'B': $data['boletas_anuladas'][] = array('serie'=>$oper['serie'],'num'=>$oper['num']); break;
	 				case 'F': $data['facturas_anuladas'][] = array('serie'=>$oper['serie'],'num'=>$oper['num']); break;
	 			}
	 		}else{
		 		foreach ($oper['items'] as $item){
		 			if($item['cuenta_cobrar']['servicio']['_id']==new MongoId('51a8d1994d4a13540a000070')||$item['cuenta_cobrar']['servicio']['_id']==new MongoId('529f8d60a4b5c3d40600005d')||$item['cuenta_cobrar']['servicio']['_id']==new MongoId('52fbc917a4b5c3f40b00012e')){
		 				$alquiler = array(
		 					'tipo'=>$oper['tipo'],
			 				'serie'=>$oper['serie'],
			 				'num'=>$oper['num'],
			 				'concepto'=>$oper['cliente'],
			 				'moneda'=>$oper['moneda'],
		 					'total'=>0,
		 					'subtotal'=>0,
		 					'mora'=>0,
		 					'igv'=>0,
		 					'alquiler'=>0
		 				);
			 			foreach ($item['conceptos'] as $conc){
			 				$concepto = strtoupper($conc['concepto']['nomb']);
				 			$pos = strrpos($concepto, "IGV");
							if ($pos === false) 0;
							else{
							    $alquiler['igv'] += floatval($conc['monto']);
							    $alquiler['total'] += floatval($conc['monto']);
							}
			 				$pos = strrpos($concepto, "ALQUILER");
							if ($pos === false) 0;
							else {
							    $alquiler['alquiler'] += floatval($conc['monto']);
							    $alquiler['subtotal'] += floatval($conc['monto']);
							    $alquiler['total'] += floatval($conc['monto']);
							}
			 				$pos = strrpos($concepto, "MORA");
							if ($pos === false) 0;
							else {
							    $alquiler['mora'] += floatval($conc['monto']);
							    $alquiler['subtotal'] += floatval($conc['monto']);
							    $alquiler['total'] += floatval($conc['monto']);
							}
							$pos = strrpos($concepto, "INTERES");
							if ($pos === false) 0;
							else {
							    $alquiler['mora'] += floatval($conc['monto']);
							    $alquiler['subtotal'] += floatval($conc['monto']);
							    $alquiler['total'] += floatval($conc['monto']);
							}						
			 			}
			 			$cuenta = $f->model('cj/cuen')->params(array('comprobante'=>$oper['_id']))->get('by_comp')->items[0];
			 			$inmueble = $f->model('in/oper')->params(array(
			 				'_id'=>$cuenta['operacion']
			 			))->get('one')->items['espacio'];
			 			$alquiler['tipo_inmueble'] = $f->model('in/espa')->params(array('_id'=>$inmueble['_id']))->get('one')->items['uso'];
						$data['alquileres'][] = $alquiler;
		 			}else{
		 				$otros = array(
		 					'tipo'=>$oper['tipo'],
			 				'serie'=>$oper['serie'],
			 				'num'=>$oper['num'],
			 				'concepto'=>$oper['cliente'],
			 				'moneda'=>$oper['moneda'],
		 					'total'=>0,
		 					'subtotal'=>0,
		 					'mora'=>0,
		 					'igv'=>0,
		 					'alquiler'=>0
		 				);
		 				foreach ($item['conceptos'] as $conc){
			 				$concepto = strtoupper($conc['concepto']['nomb']);
				 			$pos = strrpos($concepto, "IGV");
							if ($pos === false){
								$otros['alquiler'] += floatval($conc['monto']);
								$otros['subtotal'] += floatval($conc['monto']);
							    $otros['total'] += floatval($conc['monto']);
							}else{
							    $otros['igv'] += floatval($conc['monto']);
							    $otros['total'] += floatval($conc['monto']);
							}
			 			}
			 			$data['otros'][] = $otros;
		 			}
		 		}
	 		}
	 	}
	 	/*
	 	 * Se pagan alquileres CUENTAS POR PAGAR
	 	 */
	 	$operaciones = $f->model('ts/ctpp')->params(array('filter'=>array(
	 		'texto'=>'GARANT',
	 		'filter'=>array('motivo')
	 	)))->get('search_all')->items;
	 	foreach ($operaciones as $oper){
	 		if($oper['estado']!='X'){
 				$concepto = strtoupper($oper['motivo']);
	 			$pos = strrpos($concepto, "INMUEBLE");
				if ($pos === false){ 0;
				}else{
			 		foreach ($oper['items'] as $item){
			 			$ctpp = array(
			 				'tipo'=>'',
			 				'serie'=>'',
			 				'num'=>'',
			 				'concepto'=>$oper['beneficiario'],
			 				'moneda'=>'S',
			 				'total'=>$oper['total'],
			 				'subtotal'=>$oper['total'],
		 					'mora'=>0,
		 					'igv'=>0,
		 					'alquiler'=>0
			 			);
			 			if(isset($oper['comprobante'])){
			 				$comp = $f->model('ts/comp')->params(array('_id'=>$oper['comprobante']))->get('one')->items;
			 				if(isset($comp['comprobante'])){
			 					$ctpp['tipo'] = $comp['comprobante']['tipo'];
			 					$ctpp['serie'] = $comp['comprobante']['serie'];
			 					$ctpp['num'] = $comp['comprobante']['num'];
			 				}
			 			}
			 			$data['tupa'][] = $ctpp;
			 		}
	 			}
	 		}
	 	}
	 	$date = explode("-",$f->request->data['dia']);
	 	$filtros = array(
			"ano"=>$date[0],
			"mes"=>$date[1],
			"dia"=>$date[2]
		);
		$f->response->view("in/repo.plan.export",array("items"=>$data,"filtros"=>$filtros));
	}
	function execute_cobranza_dudosa(){
		global $f;
		$model = $f->model("cj/cuen")->get("cobranza_dudosa");
		//print_r($model);
		foreach($model->items as $i=>$item){
			$model->items[$i]["cliente"] = $f->model("mg/entidad")->params(array("_id"=>$model->items[$i]["cliente"]["_id"]))->get("one")->items;
			$model->items[$i]["operacion"] = $f->model("in/oper")->params(array("_id"=>$model->items[$i]["operacion"]))->get("one")->items;
		}
		$f->response->view("in/repo.cobranza_dudosa.print",$model);
	}
}
?>