<?php
class Controller_in_repo extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('in/repo');
	}
	function execute_daot(){
		global $f;
		$ini = new MongoDate(strtotime($f->request->data['ano'].'-01-01'));
		$fin = new MongoDate(strtotime((intval($f->request->data['ano'])+1).'-01-01'));
		$uit = $f->model('mg/vari')->params(array('cod'=>'UIT'))->get('by_cod')->items['valor'];
		$data = $f->model('cj/comp')->params(array(
			'modulo'=>'IN',
			'uit'=>$uit,
			'ini'=>$ini,
			'fin'=>$fin
		))->get('daot')->items;
		if($f->request->data['type']=='xls'){
			$f->response->view("in/repo.daot.xls",array(
				'data'=>$data,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("in/repo.daot.pdf",array(
				'data'=>$data,'params'=>$f->request->data
			));
		}
	}
	function execute_registro_ventas(){
		global $f;
		$fec = $f->request->data['ano'].'-'.$f->request->data['mes'].'-01';
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'modulo'=>'IN',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			)
			/*,
			'estado'=>array('$-'=>'X')*/
		),'fields'=>array(
			'fecreg'=>true,
			'tipo'=>true,
			'serie'=>true,
			'num'=>true,
			'cliente'=>true,
			'subtotal'=>true,
			'igv'=>true,
			'sunat'=>true,
			'total'=>true,
			'estado'=>true,
			'cliente_nuevo'=>true
		),'sort'=>array(
			'fecreg'=>1,
			'serie'=>1,
			'num'=>1
		)))->get("all")->items;
		//echo date('Y-m-d H:i:s',strtotime($fec.' +1 month -1 hour'));die();

		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			),
			//'items.tipo' => array('$in' => array('agua_chapi')),
			'serie' => array('$in' => array('B001','F001','B004','F004')),
			'estado' => array('$in' => array('FI','ES','X')),
		);

		$ecom = $f->model("cj/ecom")->params(array("filter"=>$efilter,
			'fields'=>array(
				'fecemi'=>true,
				'fecreg'=>true,
				'tipo'=>true,
				'serie'=>true,
				'numero'=>true,
				'cliente_nomb'=>true,
				'cliente_doc'=>true,
				'tipo_doc'=>true,
				'total'=>true,
				'total_ope_gravadas'=>true,
				'total_ope_inafectas'=>true,
				'total_igv'=>true,
				'tipo_comprobante'=>true,
				'estado'=>true,
			),'sort'=>array(
				'fecemi'=>1,
				'serie'=>1,
				'numero'=>1
		)))->get("all")->items;

		if(is_null($comp) && !is_null($ecom)){
			$comp=$ecom;
		}elseif (is_null($ecom) && !is_null($comp)) {
			$comp=$comp;
		}elseif (!is_null($ecom) && !is_null($comp)) {
			$comp=array_merge($comp,$ecom);
		}

		//COMPROBANTES
		foreach ($comp as $k => $row) {
		    $freg[$k] = $row['fecreg'];
		    $tipos[$k] = $row['tipo'];
		    //$numer[$k] = $row['num'];
		}

		array_multisort($freg,SORT_ASC,$tipos, SORT_DESC, $comp);

		if($f->request->data['type']=='xls'){
			$f->response->view("in/repo.registro_ventas.xls",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("in/repo.registro_ventas.pdf",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}
	}
	function execute_listado_inmuebles(){
		global $f;
		$data = $f->model('in/inmu')->params(array('fields'=>array(
			'tipo.nomb'=>true,
			'sublocal.nomb'=>true,
			'direccion'=>true
		)))->get('all')->items;
		$f->response->view("in/repo.inmueble.listado.xls",array('data'=>$data));
	}
    public function execute_listado_arrendatarios()
    {
        global $f;
        $data = $f->model('in/cont')->params(array('fields'=>array(
            'inmueble._id'       => true,
            'inmueble.direccion' => true,
            'inmueble.sublocal'  => true,
            'inmueble.tipo'      => true,
            'titular._id'        => true,
            'titular.nomb'       => true,
            'titular.appat'      => true,
            'titular.apmat'      => true,
            'titular.docident'   => true,
            'titular.tipo_enti'  => true,
        )))->get('all')->items;

        foreach ($data as $c => $cont) {
            $contrato = [];
            $contrato['doctipo'] = '';
            $contrato['docnum']  = '';
            $contrato['sublocal']  = $cont['inmueble']['sublocal']['nomb'];
            $contrato['tipo']  = $cont['inmueble']['tipo']['nomb'];
            $contrato['direccion']  = $cont['inmueble']['direccion'];
            if ($cont['titular']['tipo_enti'] == 'P') {
                $contrato['titular'] = $cont['titular']['nomb'].' '.$cont['titular']['appat'].' '.$cont['titular']['apmat'];
            } else {
                $contrato['titular'] = $cont['titular']['nomb'];
            }
            if (isset($cont['titular']['docident'])) {
                foreach ($cont['titular']['docident'] as $d => $docident) {
                    $contrato['doctipo'] = $contrato['doctipo'].$docident['tipo'].'/';
                    $contrato['docnum']  = $contrato['docnum'].$docident['num'].'/';
                }
            }



            /**
             * $inmueble description
             * @var mixed[] se guarda el valor del contrato en inmueble, descomentar la primera linea para incluir el contrato
             */
            //$inmueble[$data[$c]['inmueble']['sublocal']['_id']->{'$id'}][$data[$c]['inmueble']['tipo']['_id']->{'$id'}][$data[$c]['inmueble']['_id']->{'$id'}][$data[$c]['titular']['_id']->{'$id'}][$data[$c]['_id']->{'$id'}] = $contrato;
            $inmueble[$data[$c]['inmueble']['sublocal']['_id']->{'$id'}][$data[$c]['inmueble']['tipo']['_id']->{'$id'}][$data[$c]['inmueble']['_id']->{'$id'}][$data[$c]['titular']['_id']->{'$id'}][0] = $contrato;
        }
        if (isset($f->request->data['json'])) {
            header("Content-type:application/json");
            echo json_encode($inmueble);
            die();
        }

        $f->response->view("in/repo.inmueble.arrendatario.listado.xls", array('data'=>$inmueble));
    }
	function execute_ingresos_playas(){
		global $f;
		$ano = intval($f->request->data['ano']);
		$mes = intval($f->request->data['mes']);
		$mes_n = $mes+1;
		$ano_n = $ano;
		if($mes<10) $mes = '0'.$mes;
		if($mes_n==13){
			$mes_n = 1;
			$ano_n++;
		}
		if($mes_n<10) $mes_n = '0'.$mes_n;
		$ini = new MongoDate(strtotime($ano.'-'.$mes.'-01'));
		$fin = new MongoDate(strtotime($ano_n.'-'.$mes_n.'-01'));
		$comp = $f->model('cj/comp')->params(array(
			'filter'=>array(
				'modulo'=>'IN',
				'playa'=>array('$exists'=>true),
				'fecreg'=>array(
					'$gte'=>$ini,
					'$lt'=>$fin
				)
			)
		))->get('custom')->items;

		$data = array();
		$data_tmp = array();
		foreach($comp as $item){
			$id = $item['playa']['_id']->{'$id'};
			if(!isset($data_tmp[$id])){
				$data_tmp[$id] = $item['playa'];
				$data_tmp[$id]['total'] = floatval($item['total']);
			}else{
				$data_tmp[$id]['total'] += floatval($item['total']);
			}
		}
		foreach($data_tmp as $key){
			$data[] = $key;
		}
		if($f->request->data['type']=='xls'){
			$f->response->view("in/repo.playa.resumen.xls",array('data'=>$data,'ini'=>$ini));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("in/repo.playa.resumen.pdf",array('data'=>$data,'ini'=>$ini));
		}else{
			$f->response->json($data);
		}
	}
	function execute_listado_playas(){
		global $f;
		$ano = intval($f->request->data['ano']);
		$mes = intval($f->request->data['mes']);
		$mes_n = $mes+1;
		if($mes<10) $mes = '0'.$mes;
		if($mes_n==13){
			$mes_n = 1;
			$ano++;
		}
		if($mes_n<10) $mes_n = '0'.$mes_n;
		$ini = new MongoDate(strtotime($ano.'-'.$mes.'-01'));
		$fin = new MongoDate(strtotime($ano.'-'.$mes_n.'-01'));
		$data = $f->model('cj/comp')->params(array(
			'filter'=>array(
				'modulo'=>'IN',
				'playa'=>array('$exists'=>true),
				'fecreg'=>array(
					'$gte'=>$ini,
					'$lt'=>$fin
				)
			)
		))->get('custom')->items;
		if($f->request->data['type']=='xls'){
			$f->response->view("in/repo.playa.listado.xls",array('data'=>$data));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("in/repo.playa.listado.pdf",array('data'=>$data));
		}
	}
	/*	function execute_liquidaciones_legacy()
	{
		global $f;
		$model = $f->model('in/cont')->params(
			array(
				'titular'=>new MongoId($f->request->data['titular']),
				'inmueble'=>new MongoId($f->request->data['inmueble'])
			)
		)->get('all')->items;
		$contratos = array();
		$pagos = array();
		$now_year = date('Y');
		$mayor_fecha = 0;
		$mora_extra = 0;
		$hoy = time();
		$fecha = time();
		if($model!=null)
		{

			foreach ($model as $i=>$item)
			{
				//echo $item['_id']->{'$id'}.'<br>';
				//if($item['_id']->{'$id'}=='5684416e8e73587807000e2f'){
				//	print_r($item);
				//	die();
				//}

				if(isset($f->request->data['fecfin_mora'])){
					$fecha = strtotime($f->request->data['fecfin_mora']);
				}else{
					$fecha = time();
				}

				//if($f->request->data['titular']=='568440818e7358c00700018b' && $f->request->data['inmueble']=='5653563c8e73587807000186'){
				//	$fecha = strtotime("2017-06-06");
				//	//echo $fecha;
				//}else{
				//	$fecha = time();
				//}


				//$liquidacion_penalidades = 0;
				//if($f->request->data['titular']=='53fcd7a7ee6f96741d0000f2' && $f->request->data['inmueble']=='5653563c8e735878070001a3'){
				//	$liquidacion_penalidades = 1;
				//	//echo $fecha;
				//}
				//echo 'CONTRATO '.$i.'<br />';
				$dia_pago = date('d',$item['fecini']->sec);
				$fechafin = $item['fecfin']->sec;
				$fecdesoc = 0;
				$tope = $fechafin;
				if(isset($item['fecdes'])){
					$fecdesoc = $item['fecdes']->sec;
					if($item['fecdes']->sec>0){
						$tope = $fecdesoc;
					}
				}
				if(isset($item['pagos']))
				{
					$total_pagos = 0;
					$verify_year = 0;
					foreach ($item['pagos'] as $j=>$pago)
					{
						$pago_mes = $pago['mes'];
						if(floatval($dia_pago)>=15){
							if($pago['mes']==1){
								$pago['mes'] = 12;
								$pago['ano'] = $pago['ano']-1;
							}else{
								$pago['mes'] = $pago['mes']-1;
							}
						}
						if(!isset($pagos[$pago['ano']]))
						{
							$pagos[$pago['ano']] = array();
							for($k=1;$k<=12;$k++)
							{
								//echo $item['porcentaje'];
								$pagos[$pago['ano']][$k]=array(
									"alquiler"=>0,
									"mora_2"=>0,
									"igv"=>0,
									"moneda"=>$item['moneda'],
									"intervalo"=>0,
									"porcentaje_contrato"=>0
								);
								//echo $pagos[$pago['ano']][$k]['porcentaje_contrato'];
							}
						}

						if(isset($pagos[$pago['ano']][$pago['mes']])){
							$pagos[$pago['ano']][$pago['mes']]['moneda'] = $item['moneda'];
						}

						if(isset($pagos[$pago['ano']][$pago['mes']])){
							$pagos[$pago['ano']][$pago['mes']]['porcentaje_contrato'] = (isset($item['porcentaje'])?$item['porcentaje']:$item['_id']->{'$id'});
						}

						$fecha_pago = strtotime($pago['ano'].'-'.$pago['mes'].'-'.$dia_pago);
					//	if($f->request->data['titular']=='54734124ee6f9620190000e7' && $f->request->data['inmueble']=='5653563c8e73587807000032'){
					//		if($fecha_pago<strtotime('2017-02-01')){
					//			continue;
					//		}
					//	}

						if(isset($f->request->data['fecini_pagos'])){
							$fecini_pagos = strtotime($f->request->data['fecini_pagos']);
							if($fecha_pago<=$fecini_pagos){
								continue;
							}
						}

						if(isset($f->request->data['fecfin_pagos'])){
							$fecfin_pagos = strtotime($f->request->data['fecfin_pagos']);
							if($fecha_pago>=$fecfin_pagos){
								continue;
							}
						}


						if($fecha_pago<$fecha){
							if($tope>=$fecha_pago){
								if(isset($pago['estado']))
								{
									if($pago['estado']=='P')
									{
										$total = floatval($item['importe'])-floatval($pago['total']);
										if(isset($pago['detalle'])){
											$total-=floatval($pago['detalle']['alquiler']);
										}
										if(isset($pago['historico'])){
											foreach($pago['historico'] as $hist){
												$total-=floatval($hist['total']);
											}
										}
										if($fecdesoc>0){
											$diferencia_dias = $fecdesoc-$fecha_pago;
											$diferencia_dias = floor($diferencia_dias/(60*60*24))+1;
											if($diferencia_dias<30){
												$total = ($total/30)*$diferencia_dias;
											}
										}
										$total = round($total,2);
										$pagos[$pago['ano']][$pago['mes']]['alquiler']+=$total;
										$pagos[$pago['ano']][$pago['mes']]['igv']+=round($total*0.18,3);
										$total_pagos++;
									}
								}
								else
								{
									$total = floatval($item['importe']);
									if($fecdesoc>0){
										$diferencia_dias = $fecdesoc-$fecha_pago;
										$diferencia_dias = floor($diferencia_dias/(60*60*24))+1;
										if($diferencia_dias<30){
											$total = ($total/30)*$diferencia_dias;
										}
									}
									$total = round($total,2);
									$pagos[$pago['ano']][$pago['mes']]['alquiler']+=$total;
									$pagos[$pago['ano']][$pago['mes']]['igv']+=round($total*0.18,3);
									$total_pagos++;
								}
								$params_pago = array(
									'ano'=>$pago['ano'],
									'mes'=>$pago['mes'],
									'fecini'=>date('Y-m-d',$item['fecini']->sec),
									'contrato'=>$item['_id'],
									"fec"=>$fecha
								);
								//if($liquidacion_penalidades==1){
								//	$get_mora = array(
								//		'mora_2'=>0,
								//		'mora_porc'=>0
								//	);

							//	}else{
									$get_mora = $f->model('in/cont')->params($params_pago)->get('mora')->items;
								//}

								//print_r($get_mora);die();
								$pagos[$pago['ano']][$pago['mes']]['mora_2']=$get_mora['mora'];
								//print_r($pagos[$pago['ano']][$pago['mes']]['mora_2']);
								$pagos[$pago['ano']][$pago['mes']]['mora_porc']=$get_mora['mora_porc'];
								$pagos[$pago['ano']][$pago['mes']]['mora_2']=round($pagos[$pago['ano']][$pago['mes']]['mora_2'],2);
								$pagos[$pago['ano']][$pago['mes']]['params_pago'] = $params_pago;
							}
						}
					}
					if($total_pagos>0)
					{
						array_push($contratos, $item);
					}
				}
			}
			foreach ($pagos as $ano => $pago) {
				//print_r($pago);die();
				$total_ano = 0;
				foreach($pago as $mes => $item){
					//$pagos[$ano][$mes]['mora_2']+=$mora_extra;
					if($item['alquiler']==0 || $item['mora_2']==0){
						if(isset($item['params_pago'])){
							if(intval($item['params_pago']['ano'])==date('Y') && intval($item['params_pago']['mes'])==date('m'))
								$total_ano+=$item['alquiler'];
						}
					}else{
						$total_ano+=$item['alquiler'];
					}
				}
				if($total_ano==0){
					unset($pagos[$ano]);
				}
			}
		}
		//print_r($pagos);
		$arrendatario = $f->model('mg/entidad')->params(array('_id'=>new MongoId($f->request->data['titular'])))->get('one')->items;
		$inmueble = $f->model('in/inmu')->params(array('_id'=>new MongoId($f->request->data['inmueble'])))->get('one')->items;
		//die();
		$f->response->view('in/repo.liqu.print',array('liquidacion_penalidades'=>$liquidacion_penalidades,'contratos'=>$contratos, 'pagos'=>$pagos, 'arrendatario'=>$arrendatario,"inmueble"=>$inmueble));
	}
	*/
	function execute_liquidaciones()
	{
		global $f;
		$model = $f->model('in/cont')->params(
			array(
				'titular'=>new MongoId($f->request->data['titular']),
				'inmueble'=>new MongoId($f->request->data['inmueble'])
			)
		)->get('all')->items;
		$contratos = array();
		$pagos = array();
		$now_year = date('Y');
		$mayor_fecha = 0;
		$mora_extra = 0;
		$hoy = time();
		$fecha = time();
		if($model!=null){
			foreach ($model as $i=>$item){
				//echo $item['_id']->{'$id'}.'<br>';
				/*if($item['_id']->{'$id'}=='5684416e8e73587807000e2f'){
					print_r($item);
					die();
				}*/

				if(isset($f->request->data['fecfin_mora'])){
					$fecha = strtotime($f->request->data['fecfin_mora']);
				}else{
					$fecha = time();
				}

				/*if($f->request->data['titular']=='568440818e7358c00700018b' && $f->request->data['inmueble']=='5653563c8e73587807000186'){
					$fecha = strtotime("2017-06-06");
					//echo $fecha;
				}else{
					$fecha = time();
				}*/

				/*$liquidacion_penalidades = 0;
				if($f->request->data['titular']=='53fcd7a7ee6f96741d0000f2' && $f->request->data['inmueble']=='5653563c8e735878070001a3'){
					$liquidacion_penalidades = 1;
					//echo $fecha;
				}*/
				//echo 'CONTRATO '.$i.'<br />';

				$dia_pago = date('d',$item['fecini']->sec);
				$fechafin = $item['fecfin']->sec;
				$fecdesoc = 0;
				$tope = $fechafin;
				/*MODIFICACION POR TOPE*/
				//$tope=strtotime(date('Y-m-d H:i:s',$tope).' +1 day');
				//$fecha=strtotime(date('Y-m-d H:i:s',$fecha).' +1 month');
				//$fecha=strtotime(date('Y-m-d H:i:s',$fecha).' -1 month');
				//$fecha=strtotime(date('Y-m-d H:i:s',$fecha).' +2 month');
				/*MODIFICACION POR TOPE*/
				if(isset($item['fecdes'])){
					$fecdesoc = $item['fecdes']->sec;
					if($item['fecdes']->sec>0){
						$tope = $fecdesoc;
					}
				}
				//$tope=strtotime(date('Y-m-d H:i:s',$tope).' +1 day');

				if(isset($item['pagos']))
				{
					$total_pagos = 0;
					$verify_year = 0;
					foreach ($item['pagos'] as $j=>$pago)
					{
						$pago_mes = $pago['mes'];
						if(floatval($dia_pago)>=15){
							if($pago['mes']==1){
								$pago['mes'] = 12;
								$pago['ano'] = $pago['ano']-1;
							}else{
								$pago['mes'] = $pago['mes']-1;
							}
						}
						if(!isset($pagos[$pago['ano']]))
						{
							$pagos[$pago['ano']] = array();
							for($k=1;$k<=12;$k++)
							{
								//echo $item['porcentaje'];
								$pagos[$pago['ano']][$k]=array(
									"alquiler"=>0,
									"mora_2"=>0,
									"igv"=>0,
									"moneda"=>$item['moneda'],
									"intervalo"=>0,
									"porcentaje_contrato"=>0
								);
								//echo $pagos[$pago['ano']][$k]['porcentaje_contrato'];
							}
						}

						if(isset($pagos[$pago['ano']][$pago['mes']])){
							$pagos[$pago['ano']][$pago['mes']]['moneda'] = $item['moneda'];
						}

						if(isset($pagos[$pago['ano']][$pago['mes']])){
							$pagos[$pago['ano']][$pago['mes']]['porcentaje_contrato'] = (isset($item['porcentaje'])?$item['porcentaje']:$item['_id']->{'$id'});
						}

						$fecha_pago = strtotime($pago['ano'].'-'.$pago['mes'].'-'.$dia_pago);
						/*if($f->request->data['titular']=='54734124ee6f9620190000e7' && $f->request->data['inmueble']=='5653563c8e73587807000032'){
							if($fecha_pago<strtotime('2017-02-01')){
								continue;
							}
						}*/
						//PARCHE, LA FECHA DE PAGO SERA LA FECHA DE DESOCUPACION PARA MOSTRAR MES FALTANTE
						if(isset($f->request->data['fecdes']) && count($item['pagos']) == $pago['item']) {
							$fecha_pago = $item['fecdes']->sec;
						}


						if(isset($f->request->data['fecini_pagos'])){
							$fecini_pagos = strtotime($f->request->data['fecini_pagos']);
							if($fecha_pago<=$fecini_pagos){
								continue;
							}
						}

						if(isset($f->request->data['fecfin_pagos'])){
							$fecfin_pagos = strtotime($f->request->data['fecfin_pagos']);
							if($fecha_pago>=$fecfin_pagos){
								continue;
							}
						}

						if($fecha_pago<$fecha){

							if($tope>=$fecha_pago){
								if(isset($pago['estado']))
								{
									if($pago['estado']=='P')
									{
										$total = floatval($item['importe'])-floatval($pago['total']);
										if(isset($pago['detalle'])){
											$total-=floatval($pago['detalle']['alquiler']);
										}
										if(isset($pago['historico'])){
											foreach($pago['historico'] as $hist){
												$total-=floatval($hist['total']);
											}
										}
										if($fecdesoc>0){
											$diferencia_dias = $fecdesoc-$fecha_pago;
											$diferencia_dias = floor($diferencia_dias/(60*60*24))+1;
											if($diferencia_dias<30){
												$total = ($total/30)*$diferencia_dias;
											}
										}
										$total = round($total,2);
										$pagos[$pago['ano']][$pago['mes']]['alquiler']+=$total;
										$pagos[$pago['ano']][$pago['mes']]['igv']+=round($total*0.18,3);
										$total_pagos++;
									}
								}
								else
								{

									$total = floatval($item['importe']);
									if($fecdesoc>0){
										$diferencia_dias = $fecdesoc-$fecha_pago;
										$diferencia_dias = floor($diferencia_dias/(60*60*24))+1;
										# CALCULO PREVIO PARA CALCULAR LA DIFERENCIA DE DIAS
										//if($diferencia_dias<30){
										//	$total = ($total/30)*$diferencia_dias;
										//}
										if(isset($f->request->data['fecdes'])) {
											if ((count($item['pagos'])-1) == $pago['item']){
												if($diferencia_dias < 30){
													$temp_total = ($total/30)*$diferencia_dias;
												}
											}
											if (count($item['pagos']) == $pago['item']){
												$total = $temp_total;
											}
											//$total = ($total/30)*$diferencia_dias;
										} elseif ($diferencia_dias<30){
											$total = ($total/30)*$diferencia_dias;
										}
									}
									$total = round($total,2);

									/*if(isset($f->request->data['debug'])){
										print_r($pago);
									}*/

									/*INAFECTO AL IGV por C.I. N?784-2017-SBPA-OGA por penalidad*/
									if ($item['motivo']['_id']->{'$id'}=="55316553bc795ba801000035") {
										$pagos[$pago['ano']][$pago['mes']]['alquiler']+=$total;
										$pagos[$pago['ano']][$pago['mes']]['igv']+=0;
										
									} else {
										$pagos[$pago['ano']][$pago['mes']]['alquiler']+=$total;
										//$pagos[$pago['ano']][$pago['mes']]['igv']+=round($total*0.18,3);
										$pagos[$pago['ano']][$pago['mes']]['igv']+=round($total*0.18,2);
									}

									//$pagos[$pago['ano']][$pago['mes']]['alquiler']+=$total;
									//$pagos[$pago['ano']][$pago['mes']]['igv']+=round($total*0.18,3);
									$total_pagos++;

								}
								$params_pago = array(
									'ano'=>$pago['ano'],
									'mes'=>$pago['mes'],
									'item_pago'=>$pago['item'],
									'fecini'=>date('Y-m-d',$item['fecini']->sec),
									'contrato'=>$item['_id'],
									"fec"=>$fecha
								);
								/*if($liquidacion_penalidades==1){
									$get_mora = array(
										'mora_2'=>0,
										'mora_porc'=>0
									);

								}else{*/
									$get_mora = $f->model('in/cont')->params($params_pago)->get('mora_final')->items;
								//}
								$pagos[$pago['ano']][$pago['mes']]['mora_2']=$get_mora['mora'];

								//$pagos[$pago['ano']][$pago['mes']]['mora_porc']=$get_mora['mora_porc'];
								//$pagos[$pago['ano']][$pago['mes']]['mora_2']=round($pagos[$pago['ano']][$pago['mes']]['mora_2'],2);
								/*INAFECTO AL IGV por C.I. N?784-2017-SBPA-OGA por penalidad*/
								if ($item['motivo']['_id']->{'$id'}=="55316553bc795ba801000035") {
									$pagos[$pago['ano']][$pago['mes']]['mora_porc']=0;
									$pagos[$pago['ano']][$pago['mes']]['mora_2']=0;
									$pagos[$pago['ano']][$pago['mes']]['penalidad']=true;
								}else{
									$pagos[$pago['ano']][$pago['mes']]['mora_porc']=$get_mora['mora_porc'];
									$pagos[$pago['ano']][$pago['mes']]['mora_2']=round($pagos[$pago['ano']][$pago['mes']]['mora_2'],2);
								}

								$pagos[$pago['ano']][$pago['mes']]['params_pago'] = $params_pago;
							}
						}
					}
					if($total_pagos>0)
					{
						array_push($contratos, $item);
					}
				}
			}
			foreach ($pagos as $ano => $pago) {
				$total_ano = 0;
				foreach($pago as $mes => $item){
					if($item['alquiler']==0 || $item['mora_2']==0){
						if(isset($item['params_pago'])){
							if(intval($item['params_pago']['ano'])==date('Y') && intval($item['params_pago']['mes'])==date('m'))
								$total_ano+=$item['alquiler'];
						}
						/*INAFECTO AL IGV por C.I. N?784-2017-SBPA-OGA por penalidad*/
						if(isset($item['penalidad']) && $item['penalidad']==true ){
								$total_ano+=$item['alquiler'];
						}
					}else{
						$total_ano+=$item['alquiler'];
					}
				}
				if($total_ano==0){
					unset($pagos[$ano]);
				}
			}
		}
		if(isset($f->request->data['debug'])){
            echo "<pre>";
            if(isset($liquidacion_penalidades)) print_r($liquidacion_penalidades);
			print_r($contratos);
			print_r($pagos);
            echo "</pre>";
            die();
        }
		$arrendatario = $f->model('mg/entidad')->params(array('_id'=>new MongoId($f->request->data['titular'])))->get('one')->items;
		$inmueble = $f->model('in/inmu')->params(array('_id'=>new MongoId($f->request->data['inmueble'])))->get('one')->items;

		if(!isset($liquidacion_penalidades)) $liquidacion_penalidades=array();

		$f->response->view('in/repo.liqu.print',array('liquidacion_penalidades'=>$liquidacion_penalidades,'contratos'=>$contratos, 'pagos'=>$pagos, 'arrendatario'=>$arrendatario,"inmueble"=>$inmueble));
	}
	public function execute_record_pago(){
		global $f;
		$model = $f->model('in/cont')->params(
			array(
				'_id'=>new MongoId($f->request->data['contrato'])
			)
		)->get('one');
		if($model!=null){
			foreach ($model->items['pagos'] as $i => $item) {
				if(isset($item['comprobante'])){
					$model->items['pagos'][$i]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$item['comprobante']['_id']))->get('one')->items;
					if(is_null($model->items['pagos'][$i]['comprobante'])){
						$model->items['pagos'][$i]['comprobante'] = $f->model('cj/ecom')->params(array('_id'=>$item['comprobante']['_id']))->get('one')->items;
					}
				}
				if(isset($item['comprobantes'])){
					foreach($item['comprobantes'] as $j=>$comp){
						$model->items['pagos'][$i]['comprobantes'][$j]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$comp['_id']))->get('one')->items;
						if(is_null($model->items['pagos'][$i]['comprobantes'][$j]['comprobante'])){
							$model->items['pagos'][$i]['comprobantes'][$j]['comprobante'] = $f->model('cj/ecom')->params(array('_id'=>$comp['_id']))->get('one')->items;
						}
					}
				}
			}
			//print_r($model->items);
			if($f->request->data['formato']=='xls'){
				$f->response->view('in/repo.repa.export',$model);
			}else{
				$f->response->view('in/repo.repa.print',$model);
			}

		}else{
			$f->response->print('Ha ocurrido un error al buscar el contrato');
		}
	}
	public function execute_record_pago_actas(){
		global $f;
		$model = $f->model('in/acta')->params(
			array(
				'_id'=>new MongoId($f->request->data['contrato'])
			)
		)->get('one');
		if($model!=null){
			foreach ($model->items['items'] as $i => $item) {
				if(isset($item['comprobante'])){
					$model->items['items'][$i]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$item['comprobante']['_id']))->get('one')->items;
				}
			}
			//print_r($model->items);
			if($f->request->data['formato']=='xls'){
				$f->response->view('in/repo.repa.export',$model);
			}else{
				$f->response->view('in/repo.repa_actas.print',$model);
			}

		}else{
			$f->response->print('Ha ocurrido un error al buscar el contrato');
		}
	}
	public function execute_situacion_actual(){
		global $f;
		$inmuebles = $f->model('in/inmu')->params(array(
			'sublocal'=>new MongoId($f->request->data['sublocal']),
			'fields'=>array('direccion'=>true)
		))->get('all')->items;
		foreach($inmuebles as $i=>$inmu){
			$inmuebles[$i]['contratos'] = $f->model('in/cont')->params(array(
				'inmueble'=>$inmu['_id'],
				'estado'=>'P',
				'fields'=>array(
					'fecini'=>true,
					'fecfin'=>true,
					'titular'=>true,
					'pagos'=>true,
					'motivo'=>true,
					'importe'=>true
				)
			))->get('all')->items;
			if(sizeof($inmuebles[$i]['contratos'])==0){
				$inmuebles[$i]['contratos'] = $f->model('in/cont')->params(array(
					'inmueble'=>$inmu['_id'],
					'fields'=>array(
						'fecini'=>true,
						'fecfin'=>true,
						'titular'=>true,
						'pagos'=>true,
						'motivo'=>true,
						'importe'=>true
					)
				))->get('all')->items;
				$inmuebles[$i]['contrato'] = $inmuebles[$i]['contratos'][sizeof($inmuebles[$i]['contratos'])-1];
				$inmuebles[$i]['desocupado'] = true;
			}
		}
		if($f->request->data['type']=='pdf'){
			$f->response->view('in/repo.siac.pdf',array(
				'sublocal'=>$f->model('in/subl')->params(array('_id'=>new MongoId($f->request->data['sublocal'])))->get('one')->items,
				'inmuebles'=>$inmuebles
			));
		}else{
			$f->response->view('in/repo.siac.xls',array(
				'sublocal'=>$f->model('in/subl')->params(array('_id'=>new MongoId($f->request->data['sublocal'])))->get('one')->items,
				'inmuebles'=>$inmuebles
			));
		}
	}
    /*public function execute_deudores()
    {
		global $f;
		$mes_tmp = $f->request->data['mes'];
		if($mes_tmp!=''){
			$params = array(
				'fec'=>new MongoDate(strtotime($f->request->data['ano'].'-'.$f->request->data['mes'].'-01')),
				'estado'=>'P',
				'fields'=>array(
					'inmueble'=>true,
					'titular'=>true,
					//'pagos'=>true,
					'moneda'=>true,
					'importe'=>true
				)
			);
			$mes = intval($f->request->data['mes']);
			$ano = intval($f->request->data['ano']);
		}else{
			$params = array(
				//'inmueble'=>new MongoId('5653563d8e73587807000216'),
				'estado'=>'P',
				'fields'=>array(
					'fecfin'=>true,
					'fecdes'=>true,
					'inmueble'=>true,
					'titular'=>true,
					'aval'=>true,
					'pagos'=>true,
					'moneda'=>true,
					'importe'=>true
				)
			);
		}
		$mes_actual = intval(date('m'));
		$ano_actual = intval(date('Y'));
		$contratos = $f->model('in/cont')->params($params)->get('all')->items;
		$titu = array();
		$titulares = array();
		foreach ($contratos as $cont) {
			$inmu = $f->model('in/inmu')->params(array('_id'=>$cont['inmueble']['_id'],'fields'=>array('_id'=>true)))->get('one')->items;
			if($inmu!=null){
				$i = array_search($cont['titular']['_id']->{'$id'}, $titu);
				if($i===FALSE){
					$tit = $f->model('mg/entidad')->params(array('_id'=>$cont['titular']['_id']))->get('one')->items;
					if($tit==null) $tit = $cont['titular'];
					$titu[] = $cont['titular']['_id']->{'$id'};
					$titulares[] = array(
						'titular'=>$tit,
						'total'=>0,
						'total_d'=>0,
						'inmuebles'=>'',
						'aval'=>'',
						'ids'=>'',
						'ult'=>0
					);
					$i = sizeof($titu)-1;
				}
				$titulares[$i]['inmuebles'] .= $cont['inmueble']['direccion'].', ';
				$titulares[$i]['ids'] .= $cont['_id']->{'$id'}.', ';
				if(isset($cont['aval'])){
					//print_r($cont);die();
					$titulares[$i]['aval'] .= $cont['aval']['nomb'].' '.$cont['aval']['appat'].' '.$cont['aval']['apmat'].', ';
				}
				if($titulares[$i]['ult']<$cont['fecdes']->sec){
					$titulares[$i]['ult'] = $cont['fecdes'];
				}
				if($titulares[$i]['ult']<$cont['fecfin']->sec){
					$titulares[$i]['ult'] = $cont['fecfin'];
				}
				foreach ($cont['pagos'] as $pago) {
					if(intval($pago['ano']<$ano_actual)){
						if($cont['moneda']=='S'){
							//echo intval($pago['mes']).'>='.$mes_actual.' --- '.intval($pago['ano'].'<='.$ano_actual);
							if($pago['estado']!='C'){
								if($pago['estado']=='P'){
									$titulares[$i]['total'] += floatval($cont['importe'])-floatval($pago['total']);
								}else{
									$titulares[$i]['total'] += floatval($cont['importe']);
								}
							}
						}else{
							if(isset($pago['estado'])){
								if($pago['estado']!='C'){
									if($pago['estado']=='P'){
										$titulares[$i]['total_d'] += floatval($cont['importe'])-floatval($pago['total']);
									}else{
										$titulares[$i]['total_d'] += floatval($cont['importe']);
									}
								}
							}
						}
					}else{
						if(intval($pago['ano']==$ano_actual)){
							if($cont['moneda']=='S'){
								if(intval($pago['mes'])<$mes_actual){
									//echo intval($pago['mes']).'>='.$mes_actual.' --- '.intval($pago['ano'].'<='.$ano_actual);
									if($pago['estado']!='C'){
										if($pago['estado']=='P'){
											$titulares[$i]['total'] += floatval($cont['importe'])-floatval($pago['total']);
										}else{
											$titulares[$i]['total'] += floatval($cont['importe']);
										}
									}
								}
							}else{
								if(intval($pago['mes'])<$mes_actual){
									if($pago['estado']!='C'){
										if($pago['estado']=='P'){
											$titulares[$i]['total_d'] += floatval($cont['importe'])-floatval($pago['total']);
										}else{
											$titulares[$i]['total_d'] += floatval($cont['importe']);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		//die();
		$f->response->view('in/repo.deudores',array(
			'titulares'=>$titulares
        ));
    }*/
	function execute_cont_por(){
		global $f;
		if(isset($f->request->data['ano']) && isset($f->request->data['mes'])){
			$ano = intval($f->request->data['ano']);
			$mes = intval($f->request->data['mes']);
			$mes_n = $mes+1;
			$ano_n = $ano;
			if($mes<10) $mes = '0'.$mes;
			if($mes_n==13){
				$mes_n = 1;
				$ano_n++;
			}
			if($mes_n<10) $mes_n = '0'.$mes_n;
			$ini = new MongoDate(strtotime($ano.'-'.$mes.'-01'));
			$fin = new MongoDate(strtotime($ano_n.'-'.$mes_n.'-01'));
			$model = $f->model('in/cont')->params(array(
				'filter'=>array(
					'fecfin'=>array(
						'$gte'=>$ini,
						'$lt'=>$fin
					),
					'motivo._id'=>array(
						//EN TEORIA ESTO NO DEBERIA VARIAR
						'$in'=>array(
							new MongoId("5531656fbc795ba801000039"),
							new MongoId("5531654cbc795ba801000033"),
							new MongoId("5531652fbc795ba80100002d")
						)
					)
				)
			))->get('all')->items;
			$rpta = array();
			if($model!=null){
				foreach($model as $i=>$item){
					$contrato_next = $f->model('in/cont')->params(array(
						'filter'=>array(
							'fecini'=>array(
								'$gt'=>$fin
							),
							'motivo._id'=>array(
								'$in'=>array(
									new MongoId("5531656fbc795ba801000039"),
									new MongoId("5531654cbc795ba801000033"),
									new MongoId("5531652fbc795ba80100002d")
								)
							)
						)
					))->items;
					//print_r($contrato_next);
					if($contrato_next==null){
						//echo 'paso filtro';
						array_push($rpta, $item);
					}
				}
			}
			$f->response->json($rpta);
		}else{
			$f->response->json(null);
		}
	}
	function execute_verificar_contrato_inmueble(){
		global $f;
		$i = 0;
		$aux = 0;
		echo '<table>';
		echo '<tbody>';

		while($aux<=6000){
			$params = array('limit'=>1000,'skip'=>$aux);
			$contratos = $f->model('in/cont')->params($params)->get('all')->items;
			$titu = array();
			$titulares = array();
			foreach ($contratos as $cont) {
				$inmu = $f->model('in/inmu')->params(array('_id'=>$cont['inmueble']['_id'],'fields'=>array('_id'=>true)))->get('one')->items;
				if($inmu==null){
					echo '<tr><td>'.$cont['_id']->{'$id'}.'</td></tr>';
					//echo $cont['inmueble']['_id']->{'$id'};
					//echo '<br />';
				}
			}
			$aux+=1000;
		}
		echo '</tbody>';
		echo '</table>';
	}
	function execute_generar_resumen_contingencia(){
		global $f;
		$data = $f->request->data;
		$params = array(
			'filter'=>array(
				'tipo'=>array(
					'$in'=>array("F","B"),
				),
				'estado'=>'R',
				"fecreg"=>array(
					'$gte'=>new MongoDate(strtotime($data['fecha']." 00:00:00")),
					'$lte'=>new MongoDate(strtotime($data['fecha']." 23:59:59"))
				)
			),
			'fields'=>array(
				"fecreg"=>1,
				"modulo"=>1,
				"items"=>1,
				"conceptos"=>1,
				"playa"=>1,
				"tipo"=>1,
				"serie"=>1,
				"num"=>1,
				"cliente"=>1,
				"doc_cliente"=>1,
				"moneda"=>1,
				"tc"=>1,
				"total"=>1,
				"igv"=>1,
				"subtotal"=>1
			)
		);
		$comp = $f->model('cj/comp')->params($params)->get('all')->items;
		//print_r($comp);die();
		$tipo_docs = array(
			"F"=>"01",
			"B"=>"03"
		);
		if($comp!=null){
			$libre = false;
			$intento = "01";
			$content = "";
			$filename = "20120958136-RF-".date('dmY',strtotime($data['fecha']))."-".$intento;
			while($libre==false){
				if(file_exists("temp/".$filename.".zip")){
					$intento = intval($intento)+1;
					if($intento<10) $intento = '0'.$intento;
					$filename = "20120958136-RF-".date('dmY',strtotime($data['fecha']))."-".$intento;
				}else{
					$libre = true;
				}
			}
			foreach($comp as $j_ind=>$item){
				//echo $j_ind."<br />";
				$inafecto = 0;
				$tipo_doc = "ERROR";
				$num_doc = "ERROR";
				$cliente = $item["cliente"]["nomb"];
				if($item["cliente"]["tipo_enti"]=="E"){
					$cliente.=" ".$item["cliente"]["appat"]." ".$item["cliente"]["apmat"];
				}
				$cliente = trim($cliente);
				foreach($item["cliente"]["docident"] as $doc){
					if($item["tipo"]=="F"){
						if($doc["tipo"]=="RUC"){
							$tipo_doc = "6";
							$num_doc = $doc["num"];
						}
					}elseif($item["tipo"]=="B"){
						if($doc["tipo"]=="RUC"){
							$tipo_doc = "6";
							$num_doc = $doc["num"];
						}elseif($doc["tipo"]=="DNI"){
							$tipo_doc = "1";
							$num_doc = $doc["num"];
						}
					}
				}
				if($num_doc=='20120958136'){
					$tipo_doc = '0';
					$num_doc = '0';
					$cliente = 'VARIOS';
				}
				if(gettype($item["cliente"])=='string'){
					$tipo_doc = '0';
					$num_doc = '0';
					$cliente = $item['cliente'];
					if($item['doc_cliente']){
						$num_doc = $item['doc_cliente'];
						if(strlen($item['doc_cliente'])==8) $tipo_doc = '1';
						if(strlen($item['doc_cliente'])==11) $tipo_doc = '6';
					}
				}
				if($item['modulo']=='IN'){
					if(isset($item['items'])){
						$item['igv'] = 0;
						$item['subtotal'] = 0;
						$inafecto = 0;
						foreach($item['items'] as $itm){
							if(isset($itm['conceptos'])){
								$rpta = $this->recuperar_conc($itm);
								$item['igv'] += $rpta['igv'];
								$item['subtotal'] += $rpta['subtotal'];
								$inafecto += $rpta['inafecto'];
							}
							if(isset($itm['items'])){
								foreach($itm['items'] as $itmm){
									if(isset($itmm['conceptos'])){
										$rpta = $this->recuperar_conc($itmm);
										$item['igv'] += $rpta['igv'];
										$item['subtotal'] += $rpta['subtotal'];
										$inafecto += $rpta['inafecto'];
									}
								}
							}
						}
					}
					if(isset($item['conceptos'])){
						$item['igv'] = 0;
						$item['subtotal'] = 0;
						$inafecto = 0;
						foreach($item['conceptos'] as $conc){
							if(isset($conc['cuenta'])){
								if(isset($conc['cuenta']['cod'])){
									if($conc['cuenta']['cod']=='2101.010503.47'){
										$item['igv'] += $conc['monto'];
									}else{
										$item['subtotal'] += $conc['monto'];
									}
								}
							}
							if(isset($conc['concepto'])){
								if(isset($conc['concepto']['cuenta'])){
									if(isset($conc['concepto']['cuenta']['cod'])){
										if($conc['concepto']['cuenta']['cod']=='2101.010501'){
											$item['igv'] += $conc['monto'];
										}else{
											$item['subtotal'] += $conc['monto'];
										}
									}
								}
							}
						}
					}
					if(isset($item['playa'])){
						$inafecto = 0;
						$item['igv'] = $item['igv'];
						$item['subtotal'] = $item['subtotal'];
					}
				}
				if($item['moneda']=='D'){
					//print_r($item);die();
					if(!isset($item['tc'])){
						$item['tc'] = 3.364;
					}
					$item['igv'] = $item['igv']*$item['tc'];
					$item['subtotal'] = $item['subtotal']*$item['tc'];
				}
				/*$f->response->print("7|");
				$f->response->print(date("d/m/Y",$item['fecreg']->sec)."|");
				$f->response->print($tipo_docs[$item["tipo"]]."|");
				$f->response->print(str_pad($item["serie"], 4, "0", STR_PAD_LEFT)."|");
				$f->response->print($item["num"]."|");
				$f->response->print("|");
				$f->response->print($tipo_doc"|");
				$f->response->print($num_doc"|");
				$f->response->print($cliente"|");
				$f->response->print(number_format($item["subtotal"],2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");//ISC
				$f->response->print(number_format($item["igv"],2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");//ISC
				$f->response->print(number_format($item["total"],2,'.','')."|");
				$f->response->print("|");
				$f->response->print("|");
				$f->response->print("|");
				$f->response->print("\r\n");*/
				$content.="7|";
				$content.=date("d/m/Y",$item['fecreg']->sec)."|";
				$content.=$tipo_docs[$item["tipo"]]."|";
				$content.=str_pad($item["serie"], 4, "0", STR_PAD_LEFT)."|";
				$content.=$item["num"]."|";
				$content.="|";
				$content.=$tipo_doc."|";
				$content.=$num_doc."|";
				$content.=substr($cliente,0,150)."|";
				$content.=number_format($item["subtotal"],2,'.','')."|";
				$content.=number_format(0,2,'.','')."|";
				$content.=number_format($inafecto,2,'.','')."|";
				$content.=number_format(0,2,'.','')."|";//ISC
				$content.=number_format($item["igv"],2,'.','')."|";
				$content.=number_format(0,2,'.','')."|";//ISC
				$content.=number_format($item["total"],2,'.','')."|";
				$content.="|";
				$content.="|";
				$content.="|";
				echo $content.'<br />';
				$content.="\r\n";
				$content = "";
			}
			die();
			$fileLocation = "temp/".$filename.".txt";
			$zipLocation = "temp/".$filename.".zip";
			$file = fopen($fileLocation,"w");
			$content = iconv("ISO-8859-1", "WINDOWS-1252", $content);
			fwrite($file,$content);
			fclose($file);
			$zip = $this->create_zip(array($fileLocation), $zipLocation, false);
			sleep(5);
			echo $content;
			if($zip){
				header("Content-type: application/zip");
				header("Content-Disposition: attachment; filename=\"".$filename.".zip\"");
				header("Content-length: " . filesize($zipLocation));
				header("Pragma: no-cache");
				header("Expires: 0");
				readfile($zipLocation);
				//echo "El archivo fue creado satisfactoriamente";
			}else{
				echo "Ha ocurrido un error al crear el archivo";
			}
		}
	}
	private function create_zip($files = array(),$destination = '',$overwrite = false) {
        	if(file_exists($destination) && !$overwrite) { return false; }
	        $valid_files = array();
	        if(is_array($files)) {
	            foreach($files as $file) {
	                if(file_exists($file)) {
	                    $valid_files[] = $file;
	                }
	            }
	        }
	        if(count($valid_files)) {
	            $zip = new ZipArchive();
	            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
	                return false;
	            }
	            foreach($valid_files as $file) {
	                $new_filename = substr($file,strrpos($file,'/') + 1);
	                $zip->addFile($file,$new_filename);
	            }
	            $zip->close();
	            return file_exists($destination);
	        }
	        else
	        {
	            return false;
	        }
    }
    function recuperar_conc($item){
    	global $f;
		$rpta = array(
			'igv'=>0,
			'inafecto'=>0,
			'subtotal'=>0
		);
		if(isset($item['conceptos'])){
			foreach($item['conceptos'] as $conc){
				if($conc['cuenta']['cod']=='2101.010501'){
					$rpta['igv'] += $conc['monto'];
				}elseif($conc['cuenta']['cod']=='1202.0901.47'){
					$rpta['inafecto'] += $conc['monto'];
				}else{
					$rpta['subtotal'] += $conc['monto'];
				}
			}
		}
		return $rpta;
    }
    function execute_generar_resumen_contingencia_pei(){
		global $f;
		$data = $f->request->data;
		$params = array(
			'filter'=>array(
				'tipo'=>array(
					'$in'=>array("F","B"),
				),
				'estado'=>'R',
				"fecreg"=>array(
					'$gte'=>new MongoDate(strtotime($data['fecha']." 00:00:00")),
					'$lte'=>new MongoDate(strtotime($data['fecha']." 23:59:59"))
				)
			),
			'fields'=>array(
				"fecreg"=>1,
				"modulo"=>1,
				"items"=>1,
				"conceptos"=>1,
				"playa"=>1,
				"tipo"=>1,
				"serie"=>1,
				"num"=>1,
				"cliente"=>1,
				"doc_cliente"=>1,
				"moneda"=>1,
				"tc"=>1,
				"total"=>1,
				"igv"=>1,
				"subtotal"=>1
			)
		);
		$comp = $f->model('cj/comp')->params($params)->get('all')->items;
		//print_r($comp);die();
		$tipo_docs = array(
			"F"=>"01",
			"B"=>"03"
		);
		if($comp!=null){
			$libre = false;
			$intento = "01";
			$content = "";
			$filename = "20120958136-RF-".date('dmY',strtotime($data['fecha']))."-".$intento;
			while($libre==false){
				if(file_exists("temp/".$filename.".zip")){
					$intento = intval($intento)+1;
					if($intento<10) $intento = '0'.$intento;
					$filename = "20120958136-RF-".date('dmY',strtotime($data['fecha']))."-".$intento;
				}else{
					$libre = true;
				}
			}
			foreach($comp as $j_ind=>$item){
				//echo $j_ind."<br />";
				$inafecto = 0;
				$tipo_doc = "ERROR";
				$num_doc = "ERROR";
				//$cliente = $item["cliente"]["nomb"];
				if (isset($item["cliente"]["nomb"])) {
					$cliente = $item["cliente"]["nomb"];
				}else{
					$cliente = "ERROR";
				}

				//if($item["cliente"]["tipo_enti"]=="E"){
				//		$cliente.=" ".$item["cliente"]["appat"]." ".$item["cliente"]["apmat"];
				//}
				if (isset($item["cliente"]["tipo_enti"])){
					if($item["cliente"]["tipo_enti"]=="E"){
						$cliente.=" ".$item["cliente"]["appat"]." ".$item["cliente"]["apmat"];
					}
				}

				$cliente = trim($cliente);
				if(isset($item["cliente"]["docident"])){
					foreach($item["cliente"]["docident"] as $doc){
						if($item["tipo"]=="F"){
							if($doc["tipo"]=="RUC"){
								$tipo_doc = "6";
								$num_doc = $doc["num"];
							}
						}elseif($item["tipo"]=="B"){
							if($doc["tipo"]=="RUC"){
								$tipo_doc = "6";
								$num_doc = $doc["num"];
							}elseif($doc["tipo"]=="DNI"){
								$tipo_doc = "1";
								$num_doc = $doc["num"];
							}
						}
					}
				}else{
					//CASO DE PLAYAS
				}


				if($num_doc=='20120958136'){
					$tipo_doc = '0';
					$num_doc = '0';
					$cliente = 'VARIOS';
				}
				if(gettype($item["cliente"])=='string'){
					$tipo_doc = '0';
					$num_doc = '0';
					$cliente = $item['cliente'];
					if($item['doc_cliente']){
						$num_doc = $item['doc_cliente'];
						if(strlen($item['doc_cliente'])==8) $tipo_doc = '1';
						if(strlen($item['doc_cliente'])==11) $tipo_doc = '6';
					}
				}
				if($item['modulo']=='IN'){
					if(isset($item['items'])){
						$item['igv'] = 0;
						$item['subtotal'] = 0;
						$inafecto = 0;
						foreach($item['items'] as $itm){
							if(isset($itm['conceptos'])){
								$rpta = $this->recuperar_conc($itm);
								$item['igv'] += $rpta['igv'];
								$item['subtotal'] += $rpta['subtotal'];
								$inafecto += $rpta['inafecto'];
							}
							if(isset($itm['items'])){
								foreach($itm['items'] as $itmm){
									if(isset($itmm['conceptos'])){
										$rpta = $this->recuperar_conc($itmm);
										$item['igv'] += $rpta['igv'];
										$item['subtotal'] += $rpta['subtotal'];
										$inafecto += $rpta['inafecto'];
									}
								}
							}
						}
					}
					if(isset($item['conceptos'])){
						$item['igv'] = 0;
						$item['subtotal'] = 0;
						$inafecto = 0;
						foreach($item['conceptos'] as $conc){
							if(isset($conc['cuenta'])){
								if(isset($conc['cuenta']['cod'])){
									if($conc['cuenta']['cod']=='2101.010503.47'){
										$item['igv'] += $conc['monto'];
									}else{
										$item['subtotal'] += $conc['monto'];
									}
								}
							}
							if(isset($conc['concepto'])){
								if(isset($conc['concepto']['cuenta'])){
									if(isset($conc['concepto']['cuenta']['cod'])){
										if($conc['concepto']['cuenta']['cod']=='2101.010501'){
											$item['igv'] += $conc['monto'];
										}else{
											$item['subtotal'] += $conc['monto'];
										}
									}
								}
							}
						}
					}
					if(isset($item['playa'])){
						$inafecto = 0;
						$item['igv'] = $item['igv'];
						$item['subtotal'] = $item['subtotal'];
					}
				}
				if($item['moneda']=='D'){
					//print_r($item);die();
					if(!isset($item['tc'])){
						$item['tc'] = 3.364;
					}
					$item['igv'] = $item['igv']*$item['tc'];
					$item['subtotal'] = $item['subtotal']*$item['tc'];
				}
				/*$f->response->print("7|");
				$f->response->print(date("d/m/Y",$item['fecreg']->sec)."|");
				$f->response->print($tipo_docs[$item["tipo"]]."|");
				$f->response->print(str_pad($item["serie"], 4, "0", STR_PAD_LEFT)."|");
				$f->response->print($item["num"]."|");
				$f->response->print("|");
				$f->response->print($tipo_doc"|");
				$f->response->print($num_doc"|");
				$f->response->print($cliente"|");
				$f->response->print(number_format($item["subtotal"],2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");//ISC
				$f->response->print(number_format($item["igv"],2,'.','')."|");
				$f->response->print(number_format(0,2,'.','')."|");//ISC
				$f->response->print(number_format($item["total"],2,'.','')."|");
				$f->response->print("|");
				$f->response->print("|");
				$f->response->print("|");
				$f->response->print("\r\n");*/
				$content.="7|";//Motivo de contingencia
				$content.="01|";//Tipo de operación
				$content.=date("d/m/Y",$item['fecreg']->sec)."|";//Fecha de emisión del comprobante de pago
				$content.=$tipo_docs[$item["tipo"]]."|";//Tipo de comprobante de pago
				$content.=str_pad($item["serie"], 4, "0", STR_PAD_LEFT)."|";//Número de serie del comprobante de pago 
				$content.=$item["num"]."|";//Número correlativo del comprobante de pago
				$content.="|";//Número final del comprobante de pago
				$content.=$tipo_doc."|";//Tipo de documento de identidad del cliente.
				$content.=$num_doc."|";//Número de documento de identidad del cliente.
				$content.=substr($cliente,0,150)."|";//Apellidos y nombres
				$content.=($item["moneda"]=='S'?"PEN":"USD")."|";//MONEDA
				$content.=number_format($item["subtotal"],2,'.','')."|";//Total valor venta operaciones gravadas.
				$content.=number_format(0,2,'.','')."|";//Total valor venta operaciones gravadas.
				$content.=number_format($inafecto,2,'.','')."|";//Total valor venta operaciones inafectas.
				$content.=number_format(0,2,'.','')."|";//ISC Total operaciones exportación.
				$content.=number_format(0,2,'.','')."|";//ISC 
				$content.=number_format($item["igv"],2,'.','')."|";//Impuesto General a las Ventas
				$content.=number_format(0,2,'.','')."|";//ISC Otros tributos y cargos que no forman parte de la base imponible.
				$content.=number_format($item["total"],2,'.','')."|"; //Importe total del comprobante de pago.
				$content.="|";//Tipo del comprobante de pago que se modifica.
				$content.="|";//Número de serie del comprobante de pago que se modifica.
				$content.="|";//Número inicial del comprobante de pago que se modifica.
				$content.="|";//Régimen de percepción.
				$content.="0.00|";//Base imponible de la percepción.
				$content.="0.00|";//Monto de la percepción.
				$content.="0.00|";//Monto total incluida la percepción.

				echo $content.'<br />';
				$content.="\r\n";
				$content = "";
			}
			die();
		}
	}
	function execute_comprobantes_heresi(){
		global $f;
		$fecini = $f->request->data['fecini'];
		$fecfin = $f->request->data['fecfin'];
		$filter = array(
			'modulo'=>'AD',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fecini)),
				'$lte'=>new MongoDate(strtotime($fecfin.' +1 day -1 minute'))
			)
		);
		$comp = $f->model('cj/comp')->params(array(
			'filter'=>$filter,
			'fields'=>array(
				//'fecreg'=>true,
				'hist_cli'=>true,
				//'cliente'=>true,
				'num'=>true,
				'hospitalizacion'=>true,
				'efectivos.monto_old'=>true,
				'efectivos.monto'=>true
			),
			'sort'=>array(
				//'fecreg'=>1,
				'num'=>1,
				'hist_cli'=>1
			)
		))->get('all')->items;
		
		$f->response->view('ho/catego.php',array('catego'=>$comp));
		
		
	}
	function execute_continuidad_comprobantes(){
		/**
		* VERIFICA QUE EXISTA CONTINUIDAD ENTRE LOS COMPROBANTES MANUALES Y ELECTRONICOS DE INMUEBLES
		* - verifica continuidad
		* 	- verifica de no haber si fue registrado en otra fecha y lo observa
		*	- de lo contrario reporta su numeración
		* - verifica continuidad electrónica
		* 	- verifica de no haber si fue registrado como RA
		*	- verifica de no haber si fue registrado en otra fecha y lo observa
		*	- de lo contrario reporta su numeración
		*/
		global $f;
		$fecini = $f->request->data['fecini'];
		$fecfin = $f->request->data['fecfin'];

		# COMPROBANTES MANUALES
		$filter = array(
			'modulo'=>'IN',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fecini)),
				'$lte'=>new MongoDate(strtotime($fecfin.' +1 day -1 minute'))
			)
		);
		if($f->request->data['tipo_inm']=='A'){
			$filter['playa'] = array('$exists'=>false);
		}elseif($f->request->data['tipo_inm']=='P'){
			$filter['playa'] = array('$exists'=>true);
		}
		$comp = $f->model("cj/comp")->params(array(
			"filter"=>$filter,
			'fields'=>array(
				'fecreg'=>true,
				'tipo'=>true,
				'serie'=>true,
				'num'=>true,
			),
			'sort'=>array(
				'fecreg'=>1,
				'serie'=>1,
				'num'=>1
			)
		))->get("all")->items;

		# COMPROBANTES ELECTRONICOS
		$fields= array(
			'fecemi' => 1,
			'fecreg' => 1,
			'tipo' => 1,
			'serie' => 1,
			'estado' => 1,
			'numero' => 1,
			'items' => 1,
			'autor' => 1,
			"tipo_comprobante" => 1,
		);
		if($f->request->data['tipo_inm']=='A'){
			$inm=array('$in' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar','servicio'));
			$serie=array('$in' => array('B001','F001'));
		}elseif($f->request->data['tipo_inm']=='P'){
			$inm=array('$in' => array('servicio'));
			$serie=array('$in' => array('B004','F004'));
		}

		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($fecini)),
				'$lte'=>new MongoDate(strtotime($fecfin.' +1 day -1 minute'))
			),
			'estado' => array('$in' => array('FI','X','ES')),
			'items.tipo' => $inm,
			'serie' => $serie,
		);

		$ecom = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$fields,
			'sort'=>array(
				'fecemi'=>1,
				'serie'=>1,
				'numero'=>1
			)
		))->get("all")->items;

		$repeticiones=array(
			'manual'=>array(),
			'electronico'=>array(),
		);

		# PASO 1: PROCESADO DE LOS COMPROBANTES MANUALES SEAPRADOS EN UN ARBOL $MANUALES[TIPO][SERIE][NUM]
		$manu=[];
		foreach ($comp as $i => $solo_comp) {
			if(isset($elec[$solo_comp['tipo']][($solo_comp['serie'])][intval($solo_comp['num'])])){
				$observaciones['manual'][$solo_comp['serie']][$solo_comp['tipo']][$solo_comp['numero']]=$solo_comp;
				unset($observaciones['manual'][$solo_comp['serie']][$solo_comp['tipo']][$solo_comp['numero']]['serie']);
				unset($observaciones['manual'][$solo_comp['serie']][$solo_comp['tipo']][$solo_comp['numero']]['tipo']);
				unset($observaciones['manual'][$solo_comp['serie']][$solo_comp['tipo']][$solo_comp['numero']]['numero']);
			}else{
				$manu[$solo_comp['tipo']][($solo_comp['serie'])][intval($solo_comp['num'])]=$solo_comp;
				unset($manu[$solo_comp['tipo']][($solo_comp['serie'])][intval($solo_comp['num'])]['tipo']);
				unset($manu[$solo_comp['tipo']][($solo_comp['serie'])][intval($solo_comp['num'])]['serie']);
				unset($manu[$solo_comp['tipo']][($solo_comp['serie'])][intval($solo_comp['num'])]['num']);
			}
		}

		# PASO 2: PROCESADO DE LOS COMPROBANTES ELECTRONICOS SEAPRADOS EN UN ARBOL $ELECTRONICAS[SERIE][TIPO][NUMERO]
		$elec=[];
		foreach ($ecom as $i => $solo_ecom) {
			if(isset($elec[$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']])){
				$observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]=$solo_ecom;
				unset($observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['serie']);
				unset($observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['tipo']);
				unset($observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['numero']);
			}else{
				$elec[$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]=$solo_ecom;
				unset($elec[$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['tipo']);
				unset($elec[$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['serie']);
				unset($elec[$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['numero']);
			}
		}

		$observaciones=[];
		$faltantes=[];
		$errores=[];

		# PASO 3: VERIFICACION DE CONTINUIDAD DE LAS MANUALES Y FALTANTES EN UN ARBOL $FALTANTES[MANUAL][TIPO][SERIE][NUM]
		foreach($manu as $tipo => $solo_tipo){
			foreach ($solo_tipo as $serie => $solo_serie) {
				$faltantes["manual"][$tipo][$serie]=[];
				$nume_max=max(array_keys($solo_serie));
				$nume_min=min(array_keys($solo_serie));
				$nume_range=range($nume_min,$nume_max);
				$obsrv=array_values(array_diff($nume_range,array_keys($solo_serie)));
				if(count($obsrv)!==0){
					# DETECTAR SI LAS BOLETAS MANUALES FUERON REGISTRADAS EN OTRO MOMENTO COMO OBSERVACION O SI NUNCA FUERON REGISTRADAS
					$comp_obs = $f->model("cj/comp")->params(array(
						"filter"=>array(
							'num'=>array('$in'=>$obsrv),
							'serie'=>$serie,
							'tipo'=>$tipo,
						),
						'fields'=>array(
							'fecreg'=>true,
							'tipo'=>true,
							'serie'=>true,
							'num'=>true,
						),
					))->get("all_by_num")->items;
					if($comp_obs!==NULL){
						foreach ($comp_obs as $c => $solo_comp) {
							if(isset($solo_comp['tipo']) && isset($solo_comp['serie']) && isset($solo_comp['num'])){
								if(isset($observaciones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']])){
									$repeticiones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]=$solo_comp;
									unset($repeticiones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]['serie']);
									unset($repeticiones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]['tipo']);
									unset($repeticiones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]['num']);
								}else{
									$observaciones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]=$solo_comp;
									unset($observaciones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]['serie']);
									unset($observaciones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]['tipo']);
									unset($observaciones['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]['num']);
								}
							}
							/*else{
								$e_tipo='desconocido'; if(isset($solo_comp['tipo'])) $e_tipo=$solo_comp['tipo'];
								$e_serie='desconocido'; if(isset($solo_comp['serie'])) $e_tipo=$solo_comp['serie'];
								$e_num='desconocido'; if(isset($solo_comp['num'])) $e_tipo=$solo_comp['num'];
								$errores['manual'][$solo_comp['tipo']][$solo_comp['serie']][$solo_comp['num']]=$solo_comp;
							}*/
						}
						$faltantes['manual'][$solo_comp['tipo']][$solo_comp['serie']]=array_values(array_diff($obsrv,array_keys($observaciones['manual'][$tipo][$serie])));

					}else{
						$faltantes['manual'][$tipo][$serie]=$obsrv;
					}
				}
			}
		}
		# PASO 4: VERIFICACION DE CONTINUIDAD DE LAS ELECTRONICAS Y FALTANTES EN UN ARBOL $FALTANTES[ELECTRONICA][SERIE][TIPO][NUMERO]
		foreach ($elec as $serie => $solo_serie) {
			foreach($solo_serie as $tipo => $solo_tipo){
				if($tipo !== 'RA'){
					#SOLO SE REVISARAN TODOS LOS COMPROBANTES EXCEPTO RA
					$faltantes["electronica"][$serie][$tipo]=[];
					$nume_max=max(array_keys($solo_tipo));
					$nume_min=min(array_keys($solo_tipo));
					$nume_range=range($nume_min,$nume_max);
					$obsrv=array_values(array_diff($nume_range,array_keys($solo_tipo)));
					if(isset($solo_serie['RA'])){
						if(count($obsrv)!==0){
							$obsrv=array_values(array_diff($obsrv,array_keys($solo_serie['RA'])));
						}
					}
					if(count($obsrv)!==0){
						# DETECTAR SI LAS BOLETAS MANUALES FUERON REGISTRADAS EN OTRO MOMENTO COMO OBSERVACION O SI NUNCA FUERON REGISTRADAS
						$ecom_obs = $f->model("cj/ecom")->params(array(
							"filter"=>array(
								'numero'=>array('$in'=>$obsrv),
								'serie'=>$serie,
							),
							'fields'=>array(
								'fecreg'=>true,
								'tipo'=>true,
								'serie'=>true,
								'numero'=>true,
							),
						))->get("all_by_num")->items;

						if($ecom_obs!==NULL){
							# OBSERVADAS
							foreach ($ecom_obs as $c => $solo_ecom) {
								if(isset($solo_ecom['tipo']) && isset($solo_ecom['serie']) && isset($solo_ecom['numero'])){
									if(isset($observaciones['electronica'][$solo_ecom['tipo']][$solo_ecom['serie']][$solo_ecom['numero']])){
										$repeticiones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]=$solo_ecom;
										unset($repeticiones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['serie']);
										unset($repeticiones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['tipo']);
										unset($repeticiones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['numero']);
									}else{
										$observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]=$solo_ecom;
										unset($observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['serie']);
										unset($observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['tipo']);
										unset($observaciones['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']][$solo_ecom['numero']]['numero']);
									}
								}
							}
							# FALTANTES
							foreach ($observaciones['electronica'] as $o_serie => $solo_o_serie) {
								foreach ($solo_o_serie as $o_tipo => $solo_o_tipo) {
									$obsrv=array_values(array_diff($obsrv,array_keys($observaciones['electronica'][$o_serie][$o_tipo])));
									$faltantes['electronica'][$serie][$tipo]=array_values(array_diff($obsrv,array_keys($observaciones['electronica'][$o_serie][$o_tipo])));
								}
							}
						}else{
							#SOLO FALTANTES
							$faltantes['electronica'][$solo_ecom['serie']][$solo_ecom['tipo']]=$obsrv;
						}

					}
				}
			}
		}

		# PORCION DE DIAGNOSTICO DE INFORMATICA, ESCRIBIR EN LA PETICIÓN debug O debug=json(FIREFOX)
		if (isset($f->request->data['debug'])) {
			$debug=array(
				'faltantes'=>$faltantes,
				'observaciones'=>$observaciones,
				'duplicados'=>$repeticiones,
				'errores'=>$errores,
			);
			if($f->request->data['debug']==="json"){
				header("Content-type:application/json");
				echo json_encode($debug);
				die();
			}else{
				echo "<pre>";
				print_r($debug);
				echo "</pre>";
				die();
			}
		}


		if($f->request->data['type']=='xls'){
			$f->response->view("in/repo.continuidad_comprobantes.xls",array(
				'data'=>array(
					'faltantes'=>$faltantes,
					'observaciones'=>$observaciones,
					'duplicados'=>$repeticiones,
					'autor'=>$f->session->userDB,
					'modulo'=>"Gestión Inmobiliaria",
				),
				'params'=>$f->request->data,
			));
		}
		/*elseif($f->request->data['type']=='pdf'){
			$f->response->view("in/repo.registro_ventas.pdf",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}
		*/
	}
    //public function execute_estado_deudores()
    //{
    //    global $f;
    //    $f->response->view('in/repo.estado_deudores', array(
    //    ));
    //}
    public function execute_deudores()
    {
        global $f;
        $params = array(
            'pagos.estado'=>'P',
            'fields'=>array(
                'fecini'=>true,
                'fecfin'=>true,
                'fecdes'=>true,
                'inmueble'=>true,
                'titular'=>true,
                'aval'=>true,
                'pagos'=>true,
                'moneda'=>true,
                'importe'=>true
            )
        );
        ini_set('memory_limit', '-1');
        $dia_actual = intval(date('d'));
        $mes_actual = intval(date('m'));
        $ano_actual = intval(date('Y'));
        $contratos  = $f->model('in/cont')->params($params)->get('all')->items;
        $titu       = array();
        $titulares  = array();
        $sin_pago = array();
        $sin_fecdes = array();
        $sin_fecfin = array();
        $alquiler_negativo = array();

        foreach ($contratos as $cont) {
            $inmu = $f->model('in/inmu')->params(array('_id'=>$cont['inmueble']['_id'],'fields'=>array('_id'=>true)))->get('one')->items;
            if ($inmu!=null) {
                $i = array_search($cont['titular']['_id']->{'$id'}, $titu);
                if ($i===false) {
                    $tit = $f->model('mg/entidad')->params(array('_id'=>$cont['titular']['_id']))->get('one')->items;
                    if ($tit==null) {
                        $tit = $cont['titular'];
                    }
                    $titu[] = $cont['titular']['_id']->{'$id'};
                    $titulares[] = array(
                        'titular'=>$tit,
                        'total'=>0,
                        'total_d'=>0,
                        'inmuebles'=>'',
                        'aval'=>'',
                        'ids'=>'',
                        'ult'=>new MongoDate(0),
                    );
                    $i = sizeof($titu)-1;
                }
                $titulares[$i]['inmuebles'] .= $cont['inmueble']['direccion'].', ';
                $titulares[$i]['ids'] .= $cont['_id']->{'$id'}.', ';
                if (isset($cont['aval'])) {
                    $titulares[$i]['aval'] .= $cont['aval']['nomb'].' '.$cont['aval']['appat'].' '.$cont['aval']['apmat'].', ';
                }
                if (isset($cont["fecdes"])) {
                    if ($titulares[$i]['ult']->sec < $cont['fecdes']->sec) {
                        $titulares[$i]['ult'] = $cont['fecdes'];
                    }
                } else {
                    $sin_fecdes[$i] = $cont;
                }

                if (isset($cont["fecfin"])) {
                    if ($titulares[$i]['ult']->sec < $cont['fecfin']->sec) {
                        $titulares[$i]['ult'] = $cont['fecfin'];
                    }
                } else {
                    $sin_fecfin[$i] = $cont;
                }
                if (isset($cont['pagos'])) {
                    foreach ($cont['pagos'] as $pago) {
                        /**
                         * En caso de que la fecha de pago sea menor que aÃ±o actual (este debiendo)
                         * Lleva los campos de los pagos, de la siguiente forma
                         *  array['item_c']               float   se desconoce el campo
                         *  array['historico']            array   define los campos importados del antiguo sistema
                         *          ['oldid']             string  define el antiguo identificador en el antiguo sistema
                         *          ['moneda']            string  define la monenda con valores (D: dolar y S:soles)
                         *          ['tipo']              string  define el tipo de comprobante (B: boleta manual y F: factura manual)
                         *          ['fec']               float   define el numero del comprobante de pago
                         *          ['total']             float   total del pago
                         *  array['comprobantes']         array   define los campos importados del antiguo sistema
                         *          ['NumeroItem']        array   orden del numero de item
                         *              ['_id']           string  define el _id del comprobante manual o electrÃ³nico
                         *              ['tipo']          string  define el tipo de comprobante (B: Boleta y F: Factura)
                         *              ['serie']         string  define la serie del comprobante
                         *              ['num']           float   define el numero de comprobante
                         *              ['detalle']       array   array que contiene el detalle del comprobante
                         *                  ['alquiler']  float   valor del aquiler
                         *                  ['igv']       float   valor del igv
                         *                  ['moras']     float   valor de las moras
                         *  array['item']                 string  define el numero de orden de pago
                         *          ['fec']               float   define el numero del comprobante de pago
                         *          ['total']             float   total del pago
                         *  array['comprobante']          array   define los campos cuando se realiza el pago completo
                         *          ['_id']               MongoId define el _id del comprobante manual o electrÃ³nico
                         *          ['tipo']              string  define el tipo de comprobante (B: Boleta, F: Factura)
                         *          ['serie']             string  define la serie del comprobante
                         *          ['num']               float   define el numero de comprobante
                         *  array['detalle']              array   array que contiene el detalle del comprobante
                         *          ['alquiler']          float   valor del aquiler
                         *          ['igv']               float   valor del igv
                         *          ['moras']             float   valor de las moras
                         *  array['item']                 string  define el numero de orden de pago
                         *  array['mes']                  string  define el numero de mes de pago
                         *  array['ano']                  string  define el numero de aÃ±o de pago
                         *  array['estado']               string  define el estado de pago (P: Pendiente C: Cancelado)
                         *
                         * @var mixed[] $pago[]        pago del contrato
                         * @var int[]   $ano_actual    aÃ±o actual de pago
                         */
                        if (intval($pago['ano'] < $ano_actual)) {
                            if (isset($pago['comprobante'])) {
                                if ($pago['estado']!='C') {
                                    if ($cont['moneda']=='S') {
                                        /**
                                         * Verificar que la moneda sea soles
                                         * @var mixed[] $cont[] contrato
                                         */
                                        if ($pago['estado']=='P') {
                                            $titulares[$i]['total'] += floatval($cont['importe'])-floatval($pago['detalle']['alquiler']);
                                        } else {
                                            $titulares[$i]['total'] += floatval($cont['importe']);
                                        }
                                    } else {
                                        if ($pago['estado']=='P') {
                                            $titulares[$i]['total_d'] += floatval($cont['importe'])-floatval($pago['detalle']['alquiler']);
                                        } else {
                                            $titulares[$i]['total_d'] += floatval($cont['importe']);
                                        }
                                    }
                                }
                            } elseif (isset($pagos['comprobantes']) || isset($pagos['historico'])) {
                                if (!isset($pago['estado']) || $pago['estado']!='C') {
                                    if ($cont['moneda']=='S') {
                                        /**
                                         * Verificar que la moneda sea soles
                                         * @var mixed[] $cont[] contrato
                                         */
                                        if ($pago['estado']=='P') {
                                            $titulares[$i]['total'] += floatval($cont['importe'])-floatval($pago['total']);
                                        } else {
                                            $titulares[$i]['total'] += floatval($cont['importe']);
                                        }
                                    } else {
                                        if ($pago['estado']=='P') {
                                            $titulares[$i]['total_d'] += floatval($cont['importe'])-floatval($pago['total']);
                                        } else {
                                            $titulares[$i]['total_d'] += floatval($cont['importe']);
                                        }
                                    }
                                }
                            } else {
                                if (!isset($pago['estado']) || $pago['estado']!='C') {
                                    if ($cont['moneda']=='S') {
                                        /**
                                         * Verificar que la moneda sea soles
                                         * @var mixed[] $cont[] contrato
                                         */
                                        $titulares[$i]['total'] += floatval($cont['importe']);
                                    } else {
                                        $titulares[$i]['total_d'] += floatval($cont['importe']);
                                    }
                                }
                            }
                        } else {
                            if (intval($pago['ano']==$ano_actual)) {
                                if (intval($pago['mes'])<$mes_actual) {
                                    if (isset($pago['comprobante'])) {
                                        if ($pago['estado']!='C') {
                                            if ($cont['moneda']=='S') {
                                                /**
                                                 * Verificar que la moneda sea soles
                                                 * @var mixed[] $cont[] contrato
                                                 */
                                                if ($pago['estado']=='P') {
                                                    $titulares[$i]['total'] += floatval($cont['importe'])-floatval($pago['detalle']['alquiler']);
                                                } else {
                                                    $titulares[$i]['total'] += floatval($cont['importe']);
                                                }
                                            } else {
                                                if ($pago['estado']=='P') {
                                                    $titulares[$i]['total_d'] += floatval($cont['importe'])-floatval($pago['detalle']['alquiler']);
                                                } else {
                                                    $titulares[$i]['total_d'] += floatval($cont['importe']);
                                                }
                                            }
                                        }
                                    } elseif (isset($pagos['comprobantes']) || isset($pagos['historico'])) {
                                        if (!isset($pago['estado']) || $pago['estado']!='C') {
                                            if ($cont['moneda']=='S') {
                                                /**
                                                 * Verificar que la moneda sea soles
                                                 * @var mixed[] $cont[] contrato
                                                 */
                                                if ($pago['estado']=='P') {
                                                    $titulares[$i]['total'] += floatval($cont['importe'])-floatval($pago['total']);
                                                } else {
                                                    $titulares[$i]['total'] += floatval($cont['importe']);
                                                }
                                            } else {
                                                if ($pago['estado']=='P') {
                                                    $titulares[$i]['total_d'] += floatval($cont['importe'])-floatval($pago['total']);
                                                } else {
                                                    $titulares[$i]['total_d'] += floatval($cont['importe']);
                                                }
                                            }
                                        }
                                    } else {
                                        if (!isset($pago['estado']) || $pago['estado']!='C') {
                                            if ($cont['moneda']=='S') {
                                                /**
                                                 * Verificar que la moneda sea soles
                                                 * @var mixed[] $cont[] contrato
                                                 */
                                                $titulares[$i]['total'] += floatval($cont['importe']);
                                            } else {
                                                $titulares[$i]['total_d'] += floatval($cont['importe']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $sin_pago[$i] = $cont;
                }
            }
        }

        $f->response->view('in/repo.deudores', array(
            'titulares'=>$titulares
        ));
    }
    public function execute_ingresos_inmuebles()
    {
        global $f;
        $filter = array(
            'modulo'=>'IN',
            'fecreg'=>array(
                '$gte'=>new MongoDate(strtotime($f->request->data['fecini'])),
                '$lte'=>new MongoDate(strtotime($f->request->data['fecfin']))
            )
        );
        if ($f->request->data['tipo_inm']=='A') {
            $filter['playa'] = array('$exists'=>false);
        } else {
            $filter['playa'] = array('$exists'=>true);
        }
        $filter['sin_pago'] = array('$ne'=>true);
        $comp = $f->model("cj/comp")->params(array("filter"=>$filter))->get("all")->items;
        # FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR INMUEBLES Y PLAYAS
        $efields = array(
            'codigo_barras_pdf'  => 0,
            'cliente_email_1'    => 0,
            'estado_resumen'     => 0,
            'estado_baja'        => 0,
            'sunat_note'         => 0,
            'sunat_description'  => 0,
            'sunat_responsecode' => 0,
            'sunat_faultcode'    => 0,
            'sunat_soap_error'   => 0,
            'digest_value'       => 0,
            'signature_value'    => 0,
            'codigo_barras'      => 0,
            'codigo_barras_pdf'  => 0,
            'ruta_zip_firmado'   => 0,
            'ruta_xml_firmado'   => 0,
            'ruta_cdr_xml'       => 0,
            'ruta_pdf'           => 0,
            'supplier'           => 0,
            'feccon'             => 0,
            'autor_con'          => 0,
            'conflux_see_id'     => 0,
            'tipo_comprobante'   => 0,
            'fecmod'             => 0,
            'autor_modifcacion'  => 0,
        );
        if ($f->request->data['tipo_inm']=='A') {
            $inm=array('$in' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar','servicio'));
            #Alquileres utiliza la serie 001
            $serie=array('$in' => array('B001','F001'));
        } elseif ($f->request->data['tipo_inm']=='P') {
            $inm=array('$in' => array('servicio'));
            #Playas utiliza la serie 004
            $serie=array('$in' => array('B004','F004'));
        }
        $efilter = array(
            'fecemi'=>array(
                '$gte'=>new MongoDate(strtotime($f->request->data['fecini'])),
                '$lte'=>new MongoDate(strtotime($f->request->data['fecfin']))
            ),
            'estado' => array('$in' => array('FI','ES')),
            'tipo' => array('$in' => array('F','B')),
            'items.tipo' => $inm,
            'serie' => $serie,
        );
        $ecom = $f->model("cj/ecom")->params(array(
            "filter"=>$efilter,
            "fields"=>$efields,
            'sort'=>array('serie'=>-1,'numero'=>1)
        ))->get("all")->items;

        if ($ecom!=null) {
            foreach ($ecom as $i=>$co) {
                if ($co['estado']!=='X' || $co['estado']!=='BO' || $co['tipo']!=='RA') {
                    if (isset($co['items'])) {
                        foreach ($co['items'] as $j=>$item) {
                            if (isset($item['conceptos'])) {
                                foreach ($item['conceptos'] as $k=>$conc) {
                                    if (isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])) {
                                        $cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
                                    } else {
                                        # No tener cuenta por cobrar es una falla
                                        echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene cuenta por cobrar en uno de sus conceptos";
                                        die();
                                    }
                                    $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
                                    #EN CASO DE CUENTAS_COBRAR
                                    if ($item['tipo']=='cuenta_cobrar') {
                                        if ($conc["descr"]!="IGV") {
                                            $caja_cuen_cobr=$f->model("cj/cuen")->params(array("_id"=>$conc['cuenta_cobrar']['_id']))->get("one")->items;
                                            foreach ($caja_cuen_cobr['conceptos'] as $l => $caja_cuen_conc) {
                                                $ecom[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$caja_cuen_conc['concepto']['_id']))->get("one")->items;
                                            }
                                        } else {
                                            $ecom[$i]['items'][$j]['conceptos'][$k]['concepto']="IGV (18%)";
                                        }
                                    }
                                    #EN CASO DE QUE TENGA UN CAMPO CONTRATO, SE OBTIENE EL INMUEBELE (ID Y DIRECCION) (SE ASUME QUE EL COPROBANTE SEA DE UN MISMO INMUEBLE)
                                    if (isset($conc['alquiler']['contrato'])) {
                                        $inmu_cntr=$f->model("in/cont")->params(array("_id"=>$conc['alquiler']['contrato']))->get("one")->items;
                                        $ecom[$i]['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
                                        $ecom[$i]['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
                                    }
                                    #EN CASO DE LAS PLAYAS SE MODIFICARA LAS CUETAS POR COBRAR POR EL _ID DE USUARIO QUE REPRESENTARA CADA PLAYA, ESTO SE HACE YA QUE CADA AUTOR REPRESENTA UNA PLAYA.
                                    # "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON "cuenta._id":ObjectId("51c20a9d4d4a13740b00000d"),
                                    # "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON "cuenta._id":ObjectId("58629f2f3e6037531d8b4567"),
                                    # "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON "cuenta._id":ObjectId("51c20adc4d4a13740b00000f"),
                                    # "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE CON 'cuenta._id' => new MongoId("55846d54cc1e90500900006e"),
                                    # "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON "cuenta._id":ObjectId("51c20aaf4d4a13c80600000e"),
                                    # "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON "cuenta._id":ObjectId("51c20abd4d4a13740b00000e"),
                                    # Reemplazaran esta cuenta
                                    # "cuenta._id":ObjectId("51acf3314d4a136011000031"),
                                    #EN CASO DE QUE LA CUENTA IGV SEA LA 2101.010503 "cuenta._id":ObjectId("51a8f8e54d4a13a812000048") CON LA  "cuenta._id": ObjectId("51a8f8ac4d4a13a812000047"),  2101.010501
                                    if (isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])) {
                                        $cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
                                        if (($cuenta_id->{'$id'}=='51acf3314d4a136011000031')) {
                                            if ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbea13e603742398b456a') {
                                                $cuenta_id=new MongoId('51c20a9d4d4a13740b00000d');
                                            } elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbecf3e603748398b4568') {
                                                $cuenta_id=new MongoId('55846d54cc1e90500900006e');
                                            #PLAYA LA PAZ
                                            } elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71df613e603745448b4568') {
                                                $cuenta_id=new MongoId('58629f2f3e6037531d8b4567');
                                            #PLAYA PAUCARPATA
                                            } elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71dead3e603728448b4568') {
                                                $cuenta_id=new MongoId('51c20adc4d4a13740b00000f');
                                            } elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbf3b3e603758398b456e') {
                                                $cuenta_id=new MongoId('51c20aaf4d4a13c80600000e');
                                            } elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbe3e3e603734398b4568') {
                                                $cuenta_id=new MongoId('51c20abd4d4a13740b00000e');
                                            }
                                            $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
                                        } elseif (($cuenta_id->{'$id'}=='51a8f8e54d4a13a812000048')) {
                                            $cuenta_id=new MongoId('51a8f8ac4d4a13a812000047');
                                            $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
                                        }
                                    }
                                }
                            } else {
                                # No tener concepto es una falla
                                echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";
                                die();
                            }
                        }
                    } else {
                        # No tener el elemento items es una falla
                        echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento items";
                        die();
                    }
                }
            }
        }
        //$ecom = array_merge_recursive($ecom, $ecom_anul);
        $dates = array();
        $mdates = array();
        foreach ($comp as $c => $compo) {
            $fecreg =  $compo['fecreg']->toDateTime();
            $fecreg=$fecreg->format('Y-m-d');
            if (!isset($mdates[''.$fecreg]['total'])) {
                $mdates[''.$fecreg]['total']=0;
            }
            $mdates[''.$fecreg]['total']+=$compo['total'];
        }

        foreach ($ecom as $d => $elec) {
            $fecemi =  $elec['fecemi']->toDateTime();
            $fecemi=$fecemi->format('Y-m-d');
            if (!isset($dates[''.$fecemi]['total'])) {
                $dates[''.$fecemi]['total']=0;
            }
            $dates[''.$fecemi]['total']+=$elec['total'];
        }

        //die();
        #OBTENER LA ULTIMA PLANILLA
        //$ult_rein = $f->model("cj/rein")->get("planilla")->items;
        /*$f->response->json(array(
            'planilla'=>intval($ult_rein)+1,
            'prog'=>$prog,
            'comp'=>$comp,
            'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items
        ));
        */

        header("Content-type:application/json");
        echo json_encode(array(
                //'planilla'=>intval($ult_rein)+1,
                //'prog'=>$prog,
                //'comp'=>$comp,
                'dates'=>$dates,
                'mdates'=>$mdates,
                //'comp'=>$ecom,
                //'ecom'=>$ecom,
                //'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items
            ));
        die();
    }
	function get_month_comp($modulo,$ini,$fin,$tipo_inm=null){
		global $f;
		$rpta = 0;
		$filter = array(
			'modulo'=>$modulo,
			'estado'=>'R',
			'fecreg'=>array(
				'$gte'=>$ini,
				'$lt'=>$fin
			)
		);
		if($tipo_inm!=null){
			if($tipo_inm=='P'){
				$filter['playa'] = array('$exists'=>true);
			}else{
				$filter['playa'] = array('$exists'=>false);
			}
		}
		$num_ecom = 0;
		$num_comp = 0;
		$comps = $f->model('cj/comp')->params(array(
			'filter'=>$filter,
			'fields'=>array('total'=>true)
		))->get('custom')->items;

		if($comps!=null){
			/*foreach($comps as $k=>$comp) {
				$rpta += floatval($cont['total']);
			}*/
			$num_comp=count($comps);
		}
		if($tipo_inm!=null){
			$efilter =array (
				'estado' =>  array (
			    	'$ne' => 'X',
			  	),
			  	'fecemi'=>array(
					'$gte'=>$ini,
					'$lt'=>$fin
				)
			);

			if($tipo_inm=='P'){
				$efilter['serie'] = array('$in'=>["B004","F004"]);
			}else{
				$filter['serie'] = array('$in'=>["B001","F001"]);
			}
			$ecoms = $f->model('cj/ecom')->params(array(
				'filter'=>$efilter,
				'fields'=>array('total'=>true)
			))->get('custom')->items;
			if($ecoms!=null){
				/*foreach($ecoms as $k=>$ecom) {
					$rpta += floatval($ecom['total']);
				}*/
				$num_ecom=count($ecoms);
			}
		}
		$rpta = array(
			'electronicos'=>$num_ecom,
			'manuales'=>$num_comp,
		);
		return $rpta;
	}
	function execute_get_stats_comp(){
		global $f;
		$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$ini_mes = strtotime(date('Y-m-01'));
		$ini_mes_ = intval(date('m'));
		$data = array(
			'recaudacion'=>array(
				'legend'=>array(),
				'alquileres'=>array(0,0,0,0),
				'playas'=>array(0,0,0,0)
			),
			'promedio'=>array(
				'alquileres'=>array(0),
				'playas'=>array(0)
			),
			'porcentaje'=>array(
				'alquileres'=>array(
					'total'=>0,
					'total_manual'=>0,
					'total_electronicos'=>0,
				),
				'playas'=>array(
					'total'=>0,
					'total_manual'=>0,
					'total_electronicos'=>0,
				)
			),
		);
		$temp = array();
		for($i=8; $i>=0; $i--){
			$tmp = $ini_mes_-$i;
			$tmp_a = intval(date('y'));
			if($tmp<1){
				$tmp = 12+$tmp;
				$tmp_a--;
			}
			$data['recaudacion']['legend'][] = $meses[$tmp].' \''.$tmp_a;
		}
		/******************************************************************************************
		* RECAUDACION INMUEBLES ALQUILERES
		******************************************************************************************/
		//$data['recaudacion']['alquileres'][0] = $this->get_month_comp('IN',new MongoDate(strtotime('-8 month',$ini_mes)),new MongoDate(strtotime('-7 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][1] = $this->get_month_comp('IN',new MongoDate(strtotime('-7 month',$ini_mes)),new MongoDate(strtotime('-6 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][2] = $this->get_month_comp('IN',new MongoDate(strtotime('-6 month',$ini_mes)),new MongoDate(strtotime('-5 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][3] = $this->get_month_comp('IN',new MongoDate(strtotime('-5 month',$ini_mes)),new MongoDate(strtotime('-4 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][4] = $this->get_month_comp('IN',new MongoDate(strtotime('-4 month',$ini_mes)),new MongoDate(strtotime('-3 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][5] = $this->get_month_comp('IN',new MongoDate(strtotime('-3 month',$ini_mes)),new MongoDate(strtotime('-2 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][6] = $this->get_month_comp('IN',new MongoDate(strtotime('-2 month',$ini_mes)),new MongoDate(strtotime('-1 month'),$ini_mes),'A');
		$data['recaudacion']['alquileres'][7] = $this->get_month_comp('IN',new MongoDate(strtotime('-1 month',$ini_mes)),new MongoDate($ini_mes),'A');
		$data['recaudacion']['alquileres'][8] = $this->get_month_comp('IN',new MongoDate($ini_mes),new MongoDate(),'A');
		/******************************************************************************************
		* RECAUDACION INMUEBLES PLAYAS
		******************************************************************************************/
		//$data['recaudacion']['playas'][0] = $this->get_month_comp('IN',new MongoDate(strtotime('-8 month',$ini_mes)),new MongoDate(strtotime('-7 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][1] = $this->get_month_comp('IN',new MongoDate(strtotime('-7 month',$ini_mes)),new MongoDate(strtotime('-6 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][2] = $this->get_month_comp('IN',new MongoDate(strtotime('-6 month',$ini_mes)),new MongoDate(strtotime('-5 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][3] = $this->get_month_comp('IN',new MongoDate(strtotime('-5 month',$ini_mes)),new MongoDate(strtotime('-4 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][4] = $this->get_month_comp('IN',new MongoDate(strtotime('-4 month',$ini_mes)),new MongoDate(strtotime('-3 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][5] = $this->get_month_comp('IN',new MongoDate(strtotime('-3 month',$ini_mes)),new MongoDate(strtotime('-2 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][6] = $this->get_month_comp('IN',new MongoDate(strtotime('-2 month',$ini_mes)),new MongoDate(strtotime('-1 month'),$ini_mes),'P');
		$data['recaudacion']['playas'][7] = $this->get_month_comp('IN',new MongoDate(strtotime('-1 month',$ini_mes)),new MongoDate($ini_mes),'P');
		$data['recaudacion']['playas'][8] = $this->get_month_comp('IN',new MongoDate($ini_mes),new MongoDate(),'P');
		/******************************************************************************************
		* PROMEDIO RECAUDACION INMUEBLES ALQUILERES
		******************************************************************************************/
		foreach ($data['recaudacion']['playas'] as $key => $valor) {
			$data['porcentaje']['playas']['total']+=$valor['manuales'];
			$data['porcentaje']['playas']['total_manual']+=$valor['manuales'];
			$data['porcentaje']['playas']['total']+=$valor['electronicos'];
			$data['porcentaje']['playas']['total_electronicos']+=$valor['electronicos'];
			$temp['promedio']['playas']['manuales'][$key]=$valor['manuales'];
			$temp['promedio']['playas']['electronicos'][$key]=$valor['electronicos'];
		}
		foreach ($data['recaudacion']['alquileres'] as $key => $valor) {
			$data['porcentaje']['alquileres']['total']+=$valor['manuales'];
			$data['porcentaje']['alquileres']['total_manual']+=$valor['manuales'];
			$data['porcentaje']['alquileres']['total']+=$valor['electronicos'];
			$data['porcentaje']['alquileres']['total_electronicos']+=$valor['electronicos'];
			$temp['promedio']['alquileres']['manuales'][$key]=$valor['manuales'];
			$temp['promedio']['alquileres']['electronicos'][$key]=$valor['electronicos'];
		}
		$data['promedio']['playas']['manuales'] = ceil(array_sum($temp['promedio']['playas']['manuales'])/count($temp['promedio']['playas']['manuales']));
		$data['promedio']['playas']['electronicos'] = ceil(array_sum($temp['promedio']['playas']['electronicos'])/count($temp['promedio']['playas']['electronicos']));
		$data['promedio']['alquileres']['manuales'] = ceil(array_sum($temp['promedio']['alquileres']['manuales'])/count($temp['promedio']['alquileres']['manuales']));
		$data['promedio']['alquileres']['electronicos'] = ceil(array_sum($temp['promedio']['alquileres']['electronicos'])/count($temp['promedio']['alquileres']['electronicos']));
		$data['porcentaje']['alquileres']['manuales'] = ($data['porcentaje']['alquileres']['total_manual']/$data['porcentaje']['alquileres']['total'])*100;
		$data['porcentaje']['alquileres']['electronicos'] = ($data['porcentaje']['alquileres']['total_electronicos']/$data['porcentaje']['alquileres']['total'])*100;
		$data['porcentaje']['playas']['manuales'] = ($data['porcentaje']['playas']['total_manual']/$data['porcentaje']['playas']['total'])*100;
		$data['porcentaje']['playas']['electronicos'] = ($data['porcentaje']['playas']['total_electronicos']/$data['porcentaje']['playas']['total'])*100;

		echo "<pre>";
		print_r($data);
		echo "</pre>";
		//$f->response->json($data);
	}
}
?>