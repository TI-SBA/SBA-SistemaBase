<?php
class Controller_cm_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("cm/repo.grid");
	}
	function execute_tras(){
		global $f;
		$model = $f->model("cm/oper")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all_tras");
		if(count($model->items)>0){
			foreach($model->items as $i=>$item){
				$ctpc = $f->model("cj/cuen")->params(array("_id"=>$item["cuentas_cobrar"]))->get("one")->items;
				if(count($ctpc["comprobantes"])>0){
					foreach($ctpc["comprobantes"] as $compr){
						$comp = $f->model("cj/comp")->params(array("_id"=>$compr))->get("one")->items;
						$model->items[$i]["recibos"]=array();
						array_push($model->items[$i]["recibos"], $comp["serie"]."-".$comp["num"]);
					}
				}else{
					$model->items[$i]["recibos"]=array();
					array_push($model->items[$i]["recibos"], "--");
				}
				$espacio = $f->model("cm/espa")->params(array("_id"=>$item["espacio"]["_id"]))->get("one2")->items;
				$model->items[$i]["espacio"]["pabellon"] = $espacio["nicho"]["pabellon"]["nomb"]." ".$espacio["nicho"]["pabellon"]["num"];
				$model->items[$i]["espacio"]["nicho"] = $espacio["nicho"]["num"];
				$model->items[$i]["espacio"]["fila"] = $espacio["nicho"]["fila"];
			}
		}
		$model->filter = array(
					"ano"=>$f->request->ano,
					"mes"=>$f->request->mes
		);
		$f->response->view("cm/tras.print",$model);
	}
	/*
	 * Ficha de inventario de mausoleo
	 * 
	 * Estado: incompleto
	 * 
	 * Requerimientos
	 * -Seleccionar un mausoleo
	 */
	function execute_inventario(){
		global $f;
		$rpta = array(
			'oper'=>$f->model("cm/oper")->params(array("_id"=>new MongoId($f->request->data['id'])))->get("construccion")->items,
			'espa'=>$f->model("cm/espa")->params(array("_id"=>new MongoId($f->request->data['id'])))->get("one2")->items
		);
		$rpta['espa']['propietario'] = $f->model("mg/entidad")->params(array("_id"=>$rpta['espa']['propietario']["_id"]))->get("one")->items;
		$f->response->view("cm/repo.invent",$rpta);
	}
	/*
	 * Trabajo de campo - constatacion in situ
	 *
	 * Requerimientos
	 * -Seleccionar un mausoleo 
	 */
	function execute_trabajo_in_situ(){
		global $f;
		$rpta = array(
			'oper'=>$f->model("cm/oper")->params(array("_id"=>new MongoId($f->request->data['id'])))->get("construccion")->items,
			'espa'=>$f->model("cm/espa")->params(array("_id"=>new MongoId($f->request->data['id'])))->get("one2")->items
		);
		$rpta['oper']['propietario'] = $f->model("mg/entidad")->params(array("_id"=>$oper['propietario']["_id"]))->get("one")->items;
		$f->response->view("cm/repo.const",$rpta);
	}
	/*
	 * Inventario-Estado de Tramite Mausoleos
	 */
	function execute_inventario_estado_tramite(){
		global $f;
		$data = array();
		$operacion = $f->model("cm/oper")->params(array(
			"mes"=>$f->request->data['mes'],
			"ano"=>$f->request->data['ano']
		))->get("concesiones_periodo")->items;
		foreach($operacion as $i=>$item){
			$espa = $f->model('cm/espa')->params(array('_id'=>$item['espacio']['_id']))->get('one2')->items;
			if(isset($espa['mausoleo'])){
				$data[$i] = array(
					0=>$item['fecreg'],
					1=>'--',
					5=>'--',
					8=>'--',
					9=>'--',
					10=>'--'
				);
				$data[$i][2] = $espa['mausoleo']['lote'];
				$data[$i][3] = $espa['mausoleo']['denominacion'];
				$data[$i][4] = $item['propietario']['nomb'];
				if(isset($item['propietario']['appat'])){
					$data[$i][4] .= ' '.$item['propietario']['appat'].' '.$item['propietario']['apmat'];
				}
				$data[$i][6] = intval($espa['mausoleo']['medidas']['largo'])*intval($espa['mausoleo']['medidas']['ancho']);
				if($espa['mausoleo']['zona']=='N')
					$data[$i][7] = 'Normal';
				if($espa['mausoleo']['zona']=='P')
					$data[$i][7] = 'Preferencial';
				/*
				 * Se jalan todas las cuentas por cobrar
				 */
				if(is_array($item['cuentas_cobrar'])){
					foreach($item['cuentas_cobrar'] as $cuenta){
						$cpc = $f->model('cj/cuen')->params(array('_id'=>$cuenta))->get('one')->items;
						if(isset($cpc['comprobantes'])){
							foreach ($cpc['comprobantes'] as $comp){
								$comp = $f->model('cj/comp')->params(array('_id'=>$comp))->get('one')->items;
								if($data[$i][1]!='--') $data[$i][1] .= '<br />';
								else $data[$i][1] = '';
								$data[$i][1] .= $comp['serie'].'-'.$comp['num'];
								if($data[$i][5]!='--') $data[$i][5] .= '<br />';
								else $data[$i][5] = '';
								if(isset($cpc['observ'])) $data[$i][5] .= $cpc['observ'];
								else $data[$i][5] .= $cpc['servicio']['nomb'];
								if($comp['moneda']=='D'){
									if($data[$i][8]!='--') $data[$i][8] .= '<br />';
									else $data[$i][8] = '';
									$data[$i][8] .= $comp['total'];
									if($data[$i][9]!='--') $data[$i][9] .= '<br />';
									else $data[$i][9] = '';
									$data[$i][9] .= $comp['tc'];
								}else{
									if($data[$i][8]!='--') $data[$i][8] .= '<br />';
									else $data[$i][8] = '';
									$data[$i][8] .= '--';
									if($data[$i][9]!='--') $data[$i][9] .= '<br />';
									else $data[$i][9] = '';
									$data[$i][9] .= '--';
								}
								if($data[$i][10]!='--') $data[$i][10] .= '<br />';
								else $data[$i][10] = '';
								$data[$i][10] .= $comp['total_soles'];
							}
						}
					}
				}else{
					$cpc = $f->model('cj/cuen')->params(array('_id'=>$item['cuentas_cobrar']))->get('one')->items;
					if(isset($cpc['comprobantes'])){
						foreach ($cpc['comprobantes'] as $comp){
							$comp = $f->model('cj/comp')->params(array('_id'=>$comp))->get('one')->items;
							if($data[$i][1]!='--') $data[$i][1] .= '<br />';
							else $data[$i][1] = '';
							$data[$i][1] .= $comp['serie'].'-'.$comp['num'];
							if($data[$i][5]!='--') $data[$i][5] .= '<br />';
							else $data[$i][5] = '';
							if(isset($cpc['observ'])) $data[$i][5] .= $cpc['observ'];
							else $data[$i][5] .= $cpc['servicio']['nomb'];
							if($comp['moneda']=='D'){
								if($data[$i][8]!='--') $data[$i][8] .= '<br />';
								else $data[$i][8] = '';
								$data[$i][8] .= $comp['total'];
								if($data[$i][9]!='--') $data[$i][9] .= '<br />';
								else $data[$i][9] = '';
								$data[$i][9] .= $comp['tc'];
							}else{
								if($data[$i][8]!='--') $data[$i][8] .= '<br />';
								else $data[$i][8] = '';
								$data[$i][8] .= '--';
								if($data[$i][9]!='--') $data[$i][9] .= '<br />';
								else $data[$i][9] = '';
								$data[$i][9] .= '--';
							}
							if($data[$i][10]!='--') $data[$i][10] .= '<br />';
							else $data[$i][10] = '';
							$data[$i][10] .= $comp['total_soles'];
						}
					}
				}
			}
		}
		$f->response->view("cm/repo.inet.export",$data);
		//return $data;
	}
	/*
	 * Relacion de Comprobantes de Sepelios Atendidos
	 *
	 * Requerimientos
	 * -Seleccionar un mes y anio
	 */
	function execute_comp_sepelio(){
		global $f;
		$data = array();
		$operacion = $f->model("cm/oper")->params(array(
			"mes"=>$f->request->data['mes'],
			"ano"=>$f->request->data['ano']
		))->get("inhumaciones")->items;
		if(isset($operacion)){
			foreach($operacion as $i=>$item){
				$data[$i] = array(
					0=>$i+1,
					1=>date('Y-m-d h:i',$item['programacion']['fecprog']->sec),
					3=>'--',
					4=>'--',
					6=>'Copia Certif. Def.',
					7=>'--',
					8=>'--'
				);
				$ocup = $f->model('mg/entidad')->params(array('_id'=>$item['ocupante']['_id']))->get('enti')->items;
				$data[$i][2] = $ocup['nomb'];
				if($ocup['tipo_enti']=='P'){
					$data[$i][2] .= ' '.$ocup['appat'].' '.$ocup['apmat'];
				}
				foreach ($ocup['docident'] as $doc){
					if($doc['tipo']=='DNI')
						$data[$i][3] = $doc['num'];
				}
				if(isset($item['inhumacion']['folio']))
					$data[$i][4] = $item['inhumacion']['folio'];
				if(isset($item['inhumacion']['municipalidad'])){
					$data[$i][5] = $item['inhumacion']['municipalidad']['nomb'];
				}else{
					$data[$i][5] = "";
				}
				if(isset($item['inhumacion']['municipalidad']['appat'])){
					$data[$i][5] .= ' '.$item['inhumacion']['municipalidad']['appat'].' '.$item['inhumacion']['municipalidad']['apmat'];
				}
				if(isset($item['inhumacion']['folio']))
					$data[$i][6] .= ' '.$item['inhumacion']['folio'];
				if(isset($item['inhumacion']['edad']))
					$data[$i][7] = $item['inhumacion']['edad'];
				if(isset($item['inhumacion']['causa']))
					$data[$i][8] = $item['inhumacion']['causa'];
			}
		}
		//print_r($data);die();
		$f->response->view("cm/repo.rcsa.export",array("items"=>$data));
	}
	/*
	 * Estado de deudores
	 *
	 */
	function execute_estado_deudores(){
		global $f;
		$data = array();
		$operacion = $f->model("cm/oper")->params(array(
			'filter'=>array(
				'cuentas_cobrar_mul'=>array('$exists'=>true)
			)
		))->get("custom")->items;
		$cuentas = array();
		if(isset($operacion)){
			foreach ($operacion as $oper){
				foreach ($oper['cuentas_cobrar'] as $i=>$cta){
					$cta = $f->model('cj/cuen')->params(array('_id'=>$cta))->get('one')->items;
					$cta["oper"] = $oper;
					if($cta['estado']=='P'){
						/*if(isset($oper['cuentas']))
							$oper['cuentas'] = array();
						$oper['cuentas'][] = $cta;*/
						array_push($cuentas,$cta);
					}
				}
				/*if(isset($oper['cuentas']))
					$data[] = $oper;*/
			}
		}
		
		$array = array();
		foreach($cuentas as $cuenta){
			if(!isset($array[$cuenta["servicio"]["_id"]->{'$id'}])){
				$array[$cuenta["servicio"]["_id"]->{'$id'}] = $cuenta["servicio"];
				$array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"] = array();
			}
			if(!isset($array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}])){
				$array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}] = $cuenta["cliente"];
				$array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}]["espacios"] = array();
			}
			if(!isset($array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}]["espacios"][$cuenta["oper"]["espacio"]["_id"]->{'$id'}])){
				$array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}]["espacios"][$cuenta["oper"]["espacio"]["_id"]->{'$id'}] = $cuenta["oper"]["espacio"];
				$array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}]["espacios"][$cuenta["oper"]["espacio"]["_id"]->{'$id'}]["items"] = array();
			}
			array_push($array[$cuenta["servicio"]["_id"]->{'$id'}]["clientes"][$cuenta["cliente"]["_id"]->{'$id'}]["espacios"][$cuenta["oper"]["espacio"]["_id"]->{'$id'}]["items"],$cuenta);
		}
		$f->response->view("cm/repo.deud.print",array("items"=>$array));		
	}
	/*
	 * LISTADO DE ESPACIOS
	 */
	function execute_print_espacios(){
		global $f;
		$espa = $f->model("cm/espa")->params(array("texto"=>$f->request->data['nomb']))->get("search")->items;
		//print_r($rede);die();
		$f->response->view("cm/repo.espacios.print",array(
			'data'=>$espa,'params'=>$f->request->data
		));
	}
	function execute_espa_vend(){
		global $f;
		$conc = $f->model("cm/oper")->params(array(
			"mes"=>$f->request->data['mes'],
			"ano"=>$f->request->data['ano'],
			"tipo"=>$f->request->data['tipo']
		))->get("all_conc")->items;
		$total = array(
			'dinero'=>0,
			'nichos'=>0
		);
		foreach ($conc as $key => $value) {
			$conc[$key]['espacio'] = $f->model('cm/espa')->params(array('_id'=>$value['espacio']['_id']))->get('one2')->items;
			if(isset($conc[$key]['espacio']['nicho'])){
				if(isset($conc[$key]['recibos'])){
					foreach ($conc[$key]['recibos'] as $key_c => $comp) {
						$conc[$key]['recibos'][$key_c] = $f->model('cj/comp')->params(array('_id'=>$comp['_id']))->get('one')->items;
						$total['dinero'] = $total['dinero']+floatval($conc[$key]['recibos'][$key_c]['total']);
					}
					$total['nichos']++;
				}
			}
		}
		//print_r($conc);die();
		$f->response->view("cm/repo.espa_vend.print",array(
			'data'=>$conc,'params'=>$f->request->data
		));
	}
	function execute_print_pabellon(){
		global $f;
		$nichos = $f->datastore->cm_espacios->find(array(
			'nicho.pabellon._id'=>new MongoId('56bdef888e73587c0700005a')
		));
		$f->library('excel');
		$f->library('helpers');
		$helper=new helper();
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cm/nichos_pabellon.xlsx');
		$row = 2;
		foreach($nichos as $i=>$nicho){
			$propietario = '';
			if(isset($nicho['propietario']))
				$propietario = $nicho['propietario']['nomb'].' '.$nicho['propietario']['appat'].' '.$nicho['propietario']['apmat'];
			$ocupantes = '';
			if(isset($nicho['ocupantes'])){
				foreach ($nicho['ocupantes'] as $ocup) {
					$ocupantes .= $ocup['nomb'].' '.$ocup['appat'].' '.$ocup['apmat'].', ';
				}
			}
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $i)
										->setCellValue('B'.$row, $nicho['nomb'])
										->setCellValue('C'.$row, $propietario)
										->setCellValue('D'.$row, $ocupantes);
			$row++;
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Relacion de Nichos.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
		$objWriter->save('php://output');
	}
	function execute_estadistica_espacios(){
		global $f;
		$data = $f->request->data;
		$a_espa=array();
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		$nichos=array();
		$mausoleos=array();
		$tumbas=array();
		$estadistica=array();
		try{
			if(isset($data['nicho'])) {
				if(empty($data['nicho']) || is_null($data['nicho'])) throw new Exception("Error: no se entendio nicho");
				if(isset($data['sector']) && !is_null($data['sector']) && !empty($data['sector'])) $a_espa['sector']=$data['sector'];
				//if(!empty($a_espa))
				{
					$nias=$f->datastore->cm_espacios->count(array_merge(array_merge($a_espa,array('nicho'=>array('$exists' => true))),array('estado'=>'C')));
					$nidi=$f->datastore->cm_espacios->count(array_merge(array_merge($a_espa,array('nicho'=>array('$exists' => true))),array('estado'=>'D')));
					$nito=$f->datastore->cm_espacios->count(array_merge($a_espa,array('nicho'=>array('$exists' => true))));
					$nichos['nichos ocupados'] = $nias;
					$nichos['nichos disponibles'] = $nidi;
					$nichos['nichos totales'] = $nito;
					$nichos['discrepancia'] = $nito-($nidi+$nias);
					$estadistica['nichos']= $nichos;	
				}
			}
			if(isset($data['mausoleo'])){
				if(empty($data['mausoleo']) || is_null($data['mausoleo'])) throw new Exception("Error: no se entendio mausoleo");
				if(isset($data['sector']) && !is_null($data['sector']) && !empty($data['sector'])) $a_espa['sector']=$data['sector'];
				//if(!empty($a_espa))
				{
					$maas=$f->datastore->cm_espacios->count(array_merge(array_merge($a_espa,array('mausoleo'=>array('$exists' => true))),array('estado'=>'C')));
					$madi=$f->datastore->cm_espacios->count(array_merge(array_merge($a_espa,array('mausoleo'=>array('$exists' => true))),array('estado'=>'D')));
					$mato=$f->datastore->cm_espacios->count(array_merge($a_espa,array('mausoleo'=>array('$exists' => true))));
					$mausoleos['mausoleos ocupados'] = $maas;
					$mausoleos['mausoleos disponibles'] = $madi;
					$mausoleos['mausoleos totales'] = $mato;
					$mausoleos['discrepancia'] = $mato-($madi+$maas);
					$estadistica['mausoleos']=$mausoleos;
				}
			}
			if(isset($data['tumba'])){
				if(empty($data['tumba']) || is_null($data['tumba'])) throw new Exception("Error: no se entendio tumba");
				if(isset($data['sector']) && !is_null($data['sector']) && !empty($data['sector'])) $a_espa['sector']=$data['sector'];
				//if(!empty($a_espa))
				{
					$tuas=$f->datastore->cm_espacios->count(array_merge(array_merge($a_espa,array('tumba'=>array('$exists' => true))),array('estado'=>'C')));
					$tudi=$f->datastore->cm_espacios->count(array_merge(array_merge($a_espa,array('tumba'=>array('$exists' => true))),array('estado'=>'D')));
					$tuto=$f->datastore->cm_espacios->count(array_merge($a_espa,array('tumba'=>array('$exists' => true))));
					$tumbas['mausoleos ocupados'] = $tuas;
					$tumbas['mausoleos disponibles'] = $tudi;
					$tumbas['mausoleos totales'] = $tuto;
					$tumbas['discrepancia'] = $tuto-($tudi+$tuas);
					$estadistica['tumbas']=$tumbas;
				}
			}	
			$response['status'] = 'success';
			$response['message'] = 'Se hizo la consulta correctamente.';
			$response['data'] = $estadistica;
		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		//print_r("holi"."</br>");
		echo "<pre>";
		print_r($response);
		echo "</pre>";
		//die();
		//$f->response->json($response);
	}

	function execute_estadistica_pabellon(){
		global $f;
		$data = $f->request->data;
		$estadistica=array();
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			$cupab=$f->datastore->cm_pabellones->find();
			foreach($cupab as $pabel){
				$estadistica[$pabel['nomb']][$pabel['num']]['asignados']=$f->datastore->cm_espacios->count(array(
					'nicho.pabellon._id'=>$pabel['_id'],
					'estado'=>'C'
				));
				$estadistica[$pabel['nomb']][$pabel['num']]['disponibles']=$f->datastore->cm_espacios->count(array(
					'nicho.pabellon._id'=>$pabel['_id'],
					'estado'=>'D'
				));
				$estadistica[$pabel['nomb']][$pabel['num']]['totales']=$f->datastore->cm_espacios->count(array(
					'nicho.pabellon._id'=>$pabel['_id'],
				));
			}
			$response['status'] = 'success';
			$response['message'] = 'Se hizo la consulta correctamente.';
			$response['data'] = $estadistica;
		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		//print_r("holi"."</br>");
		echo "<pre>";
		print_r($response);
		echo "</pre>";
		//die();
		//$f->response->json($response);
	}


	function execute_ocupacion_excel(){
		global $f;
		$cupab=$f->datastore->cm_pabellones->find();
		$f->library('excel');
		$f->library('helpers');
		$helper=new helper();
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cm/pabellon_ocupacion.xlsx');
		$row = 2;
		foreach($cupab as $i=>$pabel){
				$estadistica['ocupados']=$f->datastore->cm_espacios->count(array(
					'nicho.pabellon._id'=>$pabel['_id'],
					'estado'=>'C'
				));
				$estadistica['totales']=$f->datastore->cm_espacios->count(array(
					'nicho.pabellon._id'=>$pabel['_id'],
				));

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$pabel['nomb'])
										->setCellValue('B'.$row,$pabel['num'])
										->setCellValue('C'.$row,$estadistica['ocupados'])
										->setCellValue('D'.$row,$estadistica['totales'])
										->setCellValue('E'.$row,$estadistica['ocupados']/$estadistica['totales']*100);
			$row++;
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="porcentaje_ocupacion.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
		$objWriter->save('php://output');
	}

}
?>