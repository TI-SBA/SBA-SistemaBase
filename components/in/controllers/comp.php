<?php
class Controller_in_comp extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('in/comp.main');
	}
	function execute_lista(){
		global $f;
		$params = array(
			"modulo"=>'IN'
		);
		if(isset($f->request->data['page']))
			if($f->request->data['page']!='')
				$params['page'] = $f->request->data['page'];
		if(isset($f->request->data['page_rows']))
			if($f->request->data['page_rows']!='')
				$params['page_rows'] = $f->request->data['page_rows'];
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['cliente']))
			if($f->request->data['cliente']!='')
				$params['cliente'] = new MongoId($f->request->data['cliente']);
		if(isset($f->request->data['alquileres']))
			$params['alquileres'] = true;
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/comp")->params($params)->get("search") );
	}
	function execute_all(){
		global $f;
		$model = $f->model('cj/comp')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		foreach ($comp['items'] as $i=>$item){
			$comp['items'][$i]['cuenta_cobrar'] = $f->model("cj/cuen")->params(array("_id"=>$item['cuenta_cobrar']['_id']))->get("one")->items;
		}
		if(isset($f->request->data['forma'])){
			$comp['ctban'] = $f->model("ts/ctban")->get("all")->items;
		}
		$f->response->json( $comp );
	}
	function execute_get_info_comp_cambiar(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		if(isset($comp['cliente_nuevo'])){
			$comp['cliente_nuevo'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente_nuevo']['_id']))->get("enti")->items;
		}
		foreach ($comp['items'] as $i=>$item){
			$comp['items'][$i]['cuenta_cobrar'] = $f->model("cj/cuen")->params(array("_id"=>$item['cuenta_cobrar']['_id']))->get("one")->items;
		}
		$cajas = array();
		if(isset($f->session->enti['roles']['cajero'])){
			foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja){
				$cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
			}
		}
		$tasa = $f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items;
		$ctban = $f->model("ts/ctban")->get("all")->items;
		$f->response->json(array(
			'comp'=>$comp,
			'cajas'=>$cajas,
			'tasa'=>$tasa,
			'ctban'=>$ctban
		));
	}
	function execute_get_info_comp(){
		global $f;
		$ctban = $f->model("ts/ctban")->get("all")->items;
		$talo = $f->model("cj/talo")->params(array('filter'=>array(
			//'serie'=>'001',
			'serie' => array('$in' => array('001')),
			"caja._id" => new MongoId('51a752e14d4a132807000023')
		)))->get("custom")->items;
		$f->response->json(array(
			'ctban'=>$ctban,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items,
			'talo'=>$talo,
			'igv'=>$f->model('mg/vari')->params(array('cod'=>'IGV'))->get('by_cod')->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['modulo'] = 'IN';
		$data['periodo'] = date('ym00');
		$data['num'] = intval($data['num']);
		$data['fecreg'] = new MongoDate(strtotime($data['fec']));
		$data['cliente']['_id'] = new MongoId($data['cliente']['_id']);
		$caja = array(
			"_id"=>new MongoId("51a752e14d4a132807000023"),
			"local"=>array(
				"_id"=>new MongoId("519d35d29c7684f0050000c2"),
 				"descr"=>"Administración Central",
     			"direccion"=>"Av. Piérola 201 - Arequipa, Arequipa"
  			),
   			"nomb"=>"Recaudación de Inmuebles"
		);
		if(!isset($data['moneda'])) $data['moneda'] = 'S';
		if(isset($data['servicio']['_id'])) $data['servicio']['_id'] = new MongoId($data['servicio']['_id']);
    	if(isset($data['servicio']['organizacion']['_id'])) $data['servicio']['organizacion']['_id'] = new MongoId($data['servicio']['organizacion']['_id']);
    	if(isset($data['total'])) $data['total'] = floatval($data['total']);
		if(isset($data['conceptos'])){
	    	foreach ($data['conceptos'] as $j=>$con){
	      		if(isset($con['concepto']['_id'])) $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
	      		if(isset($con['concepto']['clasificador']['_id'])) $data['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
	      		if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $data['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
	      		if(isset($con['concepto']['cuenta']['_id'])) $data['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
	      		if(isset($con['saldo'])) $data['conceptos'][$j]['saldo'] = floatval($con['saldo']);
	      		if(isset($con['monto'])) $data['conceptos'][$j]['monto'] = floatval($con['monto']);
	    	}
		}
		if(isset($data['items'])){
			foreach ($data['items'] as $i=>$item){
				if(isset($item['cuenta_cobrar'])){
					$data['items'][$i]['cuenta_cobrar']['_id'] = new MongoId($item['cuenta_cobrar']['_id']);
					$data['items'][$i]['cuenta_cobrar']['servicio']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['_id']);
					$data['items'][$i]['cuenta_cobrar']['servicio']['organizacion']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['organizacion']['_id']);
				}
				if(isset($item['conceptos'])){
					foreach ($item['conceptos'] as $j=>$conc){
						if(isset($conc['concepto'])){
							if(gettype($conc['concepto'])=='array'){
								$data['items'][$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($conc['concepto']['_id']);
							}
						}
						if(isset($conc['cuenta']))
							if(isset($conc['cuenta']['_id'])){
								$data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($conc['cuenta']['_id']);
							}
						if(isset($conc['monto'])){
							$data['items'][$i]['conceptos'][$j]['monto'] = floatval($conc['monto']);
						}
					}
				}
			}
		}
		foreach ($data['efectivos'] as $i=>$ef){
			$data['efectivos'][$i]['monto'] = floatval($ef['monto']);
		}
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $key => $vou) {
				$data['vouchers'][$key]['monto'] = floatval($vou['monto']);
				$data['vouchers'][$key]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
      		$data['fecreal'] = new MongoDate();
      		$data['estado'] = 'R';
      		$data['autor'] = $f->session->userDB;
			$data = $f->model("cj/comp")->params(array('data'=>$data))->save("insert")->items;
		}else{
			$f->model("cj/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$data['_id'] = new MongoId($f->request->data['_id']);
		}
		$f->model('cj/talo')->params(array(
			'serie'=>$data['serie'],
			'tipo'=>$data['tipo'],
			'num'=>floatval($data['num']),
			'caja'=>$caja['_id']
		))->save('num');
		$f->response->json(array('_id'=>$data['_id']));
	}
	function execute_save_comp(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$cliente = $data['cliente'];
		$cliente['_id'] = new MongoId($cliente['_id']);
		$caja = $data['caja'];
		$caja['_id'] = new MongoId($caja['_id']);
		$caja['local']['_id'] = new MongoId($caja['local']['_id']);
		$items = $data['items'];
		foreach ($items as $i=>$item){
			unset($items[$i]['total']);
			if(isset($item['cuenta_cobrar'])){
				$items[$i]['cuenta_cobrar']['_id'] = new MongoId($item['cuenta_cobrar']['_id']);
				$items[$i]['cuenta_cobrar']['servicio']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['_id']);
				$items[$i]['cuenta_cobrar']['servicio']['organizacion']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['organizacion']['_id']);
			}
			if(isset($item['conceptos'])){
				foreach ($item['conceptos'] as $j=>$conc){
					if(isset($conc['concepto'])){
						if(gettype($conc['concepto'])=='array'){
							$items[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($conc['concepto']['_id']);
						}
					}
					if(isset($conc['cuenta']))
						if(isset($conc['cuenta']['_id'])){
							$items[$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($conc['cuenta']['_id']);
						}
					if(isset($conc['monto'])){
						$items[$i]['conceptos'][$j]['monto'] = floatval($conc['monto']);
					}
				}
			}
		}
		if(isset($data['efectivos'])){
			$efec = $data['efectivos'];
			foreach ($efec as $i=>$ef){
				$efec[$i]['monto'] = floatval($ef['monto']);
			}
		}
		if(isset($data['vouchers'])){
			$vouchers = $data['vouchers'];
			foreach ($vouchers as $i=>$vou){
				$vouchers[$i]['monto'] = floatval($vou['monto']);
				$vouchers[$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($data['fecreg'])){
			if(isset($data['fecemi'])) $data['fecreg'] = $data['fecemi'];
			if(isset($data['fec'])) $data['fecreg'] = $data['fec'];
			else $data['fecreg'] = new MongoDate();
		}
		if(!isset($data['moneda'])) $data['moneda'] = 'S';
		$comp = array(
			'modulo'=>'IN',
			'fecreg'=>new MongoDate(strtotime($data['fecreg'])),
			'fecreal'=>new MongoDate(),
			'estado'=>'R',
			'periodo'=>date('ym00'),
			'autor'=>$trabajador,
			'cliente'=>$cliente,
			'caja'=>$caja,
			'tipo'=>$data['tipo'],
			'serie'=>$data['serie'],
			'num'=>floatval($data['num']),
			'moneda'=>$data['moneda'],
			'observ'=>$data['observ'],
			'items'=>$items,
			'total'=>floatval($data['total']),
			'tc'=>floatval($data['tc'])
		);
		if(isset($data['sunat'])){
			$comp['sunat'] = $data['sunat'];
		}
		if(isset($data['efectivos'])){
			$comp['efectivos'] = $efec;
		}
		if($data['moneda']=='D'){
			$comp['total_soles'] = floatval($data['total_soles']);
		}
		if(isset($vouchers)){
			$comp['vouchers'] = $vouchers;
		}
		if(isset($data['contrato'])){
			$comp['contrato'] = new MongoId($data['contrato']);
		}
		if(isset($data['acta_conciliacion'])){
			$comp['acta_conciliacion'] = new MongoId($data['acta_conciliacion']);
		}
		if(isset($data['inmueble'])){
			$comp['inmueble'] = $data['inmueble'];
			$comp['inmueble']['_id'] = new MongoId($comp['inmueble']['_id']);
		}
		if(isset($data['alquiler'])){
			$comp['alquiler'] = true;
		}
		if(isset($data['compensacion'])){
			$comp['compensacion'] = true;
		}
		if(isset($data['acta'])){
			$comp['acta'] = true;
		}
		if(isset($data['parcial'])){
			$comp['parcial'] = true;
		}
		if(isset($data['tipo_pago'])){
			$comp['tipo_pago'] = $data['tipo_pago'];
		}
		if(isset($data['combinacion_alq'])){
			$comp['combinacion_alq'] = true;
		}
		if(isset($data['valor_igv'])){
			$comp['valor_igv'] = floatval($data['valor_igv']);
		}
		if(isset($data['igv'])){
			$comp['igv'] = floatval($data['igv']);
		}
		if(isset($data['subtotal'])){
			$comp['subtotal'] = floatval($data['subtotal']);
		}
		if(isset($data['fecemi'])){
			$data['fecreg'] = new MongoDate(strtotime($data['fecemi']));
			$comp['fecreg'] = new MongoDate(strtotime($data['fecemi']));
		}
		$compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
		$f->model('cj/talo')->params(array(
			'serie'=>$data['serie'],
			'tipo'=>$data['tipo'],
			'num'=>floatval($data['num']),
			'caja'=>$caja['_id']
		))->save('num');
		if(isset($comp['alquiler'])){
			$contrato = $f->model('in/cont')->params(array('_id'=>new MongoId($data['contrato'])))->get('one')->items;
		}
		if(isset($comp['acta'])){
			$acta = $f->model('in/acta')->params(array('_id'=>new MongoId($data['acta_conciliacion'])))->get('one')->items;
		}
		foreach ($items as $i=>$item){
			if(isset($item['cuenta_cobrar'])){
				$total = 0;
				/*foreach ($item['conceptos'] as $w=>$conc){
					$upd['conceptos.'.$w.'.saldo'] = -floatval($conc['monto']);
					$total = $total + (float)$conc['monto'];
				}
				$upd['saldo'] = -floatval($total);
				$f->model('cj/cuen')->params(array(
					'_id'=>$item['cuenta_cobrar']['_id'],
					'data'=>array('$inc'=>$upd)
				))->save('custom');*/
				$f->model('cj/cuen')->params(array(
					'_id'=>$item['cuenta_cobrar']['_id'],
					'data'=>array('$push'=>array('comprobantes'=>$compro['_id']))
				))->save('custom');
				$cuenta = $f->model('cj/cuen')->params(array('_id'=>$item['cuenta_cobrar']['_id']))->get('one')->items;
				//if(floatval($cuenta['saldo'])<=0){
					$f->model('cj/cuen')->params(array(
						'_id'=>$item['cuenta_cobrar']['_id'],
						'data'=>array('$set'=>array(
							'estado'=>'C',
							'saldo'=>0,
							//'total'=>floatval($cuenta['total'])+abs($cuenta['saldo'])
						))
					))->save('custom');
				//}
			}
			/*************************************************************************************************
			* EN CASO SEA UN PAGO DE ALQUILER DE INMUEBLES
			*************************************************************************************************/
			if(isset($comp['alquiler'])){
				$tmp_pay = array(
					'alquiler'=>$item['conceptos'][0]['monto'],
					'igv'=>$item['conceptos'][1]['monto']
				);
				if(isset($item['conceptos'][2])){
					$tmp_pay['moras'] = $item['conceptos'][2]['monto'];
				}
				foreach ($contrato['pagos'] as $kp=>$pago) {
					if($pago['mes']==$item['pago']['mes']&&$pago['ano']==$item['pago']['ano']){
						if(!isset($data['parcial'])){
							$f->model('in/cont')->params(array(
								'_id'=>$contrato['_id'],
								'data'=>array(
									'pagos.'.$kp.'.estado'=>'C',
									'pagos.'.$kp.'.comprobante'=>array(
										'_id'=>$compro['_id'],
										'tipo'=>$compro['tipo'],
										'serie'=>$compro['serie'],
										'num'=>$compro['num']
									),
									'pagos.'.$kp.'.item_c'=>$i,
									'pagos.'.$kp.'.detalle'=>$tmp_pay
								)
							))->save('update');
						}elseif(isset($data['parcial'])){
							$estado_tmp = 'P';
							$total_tmp = floatval($item['conceptos'][0]['monto']);
							if(isset($pago['total'])) $total_tmp += floatval($pago['total']);
							if($total_tmp==floatval($contrato['importe'])) $estado_tmp = 'C';
							$f->model('in/cont')->params(array(
								'_id'=>$contrato['_id'],
								'data'=>array(
									'pagos.'.$kp.'.estado'=>$estado_tmp,
									'pagos.'.$kp.'.total'=>$total_tmp,
									'pagos.'.$kp.'.item_c'=>$i
								)
							))->save('update');
							if(isset($pago['comprobante'])){
								$pago['comprobante']['detalle'] = $pago['detalle'];
								$f->model('in/cont')->params(array(
									'_id'=>$contrato['_id'],
									'data'=>array('$push'=>
										array(
											'pagos.'.$kp.'.comprobantes'=>$pago['comprobante']
										)
									)
								))->save('custom');
								$f->model('in/cont')->params(array(
									'_id'=>$contrato['_id'],
									'data'=>array('$unset'=>
										array(
											'pagos.'.$kp.'.detalle'=>true,
											'pagos.'.$kp.'.comprobante'=>true
										)
									)
								))->save('custom');
								$estado_tmp = 'P';
								$total_tmp = floatval($item['conceptos'][0]['monto']);
								if(isset($pago['detalle']['alquiler'])) $total_tmp += floatval($pago['detalle']['alquiler']);
								if($total_tmp==floatval($contrato['importe'])) $estado_tmp = 'C';
								$f->model('in/cont')->params(array(
									'_id'=>$contrato['_id'],
									'data'=>array(
										'pagos.'.$kp.'.estado'=>$estado_tmp,
										'pagos.'.$kp.'.total'=>$total_tmp,
										'pagos.'.$kp.'.item_c'=>$i
									)
								))->save('update');
							}elseif(isset($pago['historico'])){






















/*
								foreach ($pago['historico'] as $k=>$tmp_pay) {
									# code...
								}





								$estado_tmp = 'P';
								$total_tmp = floatval($item['conceptos'][0]['monto']);
								if(isset($pago['detalle']['alquiler'])) $total_tmp += floatval($pago['detalle']['alquiler']);
								if($total_tmp==floatval($contrato['importe'])) $estado_tmp = 'C';
								$f->model('in/cont')->params(array(
									'_id'=>$contrato['_id'],
									'data'=>array(
										'pagos.'.$kp.'.estado'=>$estado_tmp,
										'pagos.'.$kp.'.total'=>$total_tmp,
										'pagos.'.$kp.'.item_c'=>$i
									)
								))->save('update');
*/


















							}
							$f->model('in/cont')->params(array(
								'_id'=>$contrato['_id'],
								'data'=>array('$push'=>
									array(
										'pagos.'.$kp.'.comprobantes'=>array(
											'_id'=>$compro['_id'],
											'tipo'=>$compro['tipo'],
											'serie'=>$compro['serie'],
											'num'=>$compro['num'],
											'detalle'=>$tmp_pay
										)
									)
								)
							))->save('custom');
						}
					}
				}
			}
			/*************************************************************************************************
			* EN CASO SEA UN PAGO DE ACTA DE CONCILIACION DE INMUEBLES
			*************************************************************************************************/
			if(isset($comp['acta'])){
				foreach ($acta['items'] as $kp=>$pago) {
					if($pago['num']==$item['pago']['num']){
						$f->model('in/acta')->params(array(
							'_id'=>$acta['_id'],
							'data'=>array(
								'items.'.$kp.'.estado'=>'C',
								'items.'.$kp.'.comprobante'=>array(
									'_id'=>$compro['_id'],
									'tipo'=>$compro['tipo'],
									'serie'=>$compro['serie'],
									'num'=>$compro['num']
								)
							)
						))->save('update');
					}
				}
			}
		}
		$enti = $compro['cliente']['nomb'];
		if($compro['cliente']['tipo_enti']=='P')
			$enti .= ' '.$compro['cliente']['appat'].' '.$compro['cliente']['apmat'];
		if($compro['moneda']=='S') $total = 'S/.'.$compro['total'];
		else $total = '$'.$compro['total'];
		switch ($compro['tipo']){
			case 'B': $word = 'Boleta de Venta'; break;
			case 'R': $word = 'Recibo de Caja'; break;
			case 'F': $word = 'Factura'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Cuentas por Cobrar',
			'descr'=>'Se cre&oacute; un comprobante <b>'.$word.'</b> a nombre de <b>'.$enti.'</b>.'.
				' con serie y n&uacute;mero <b>'.$compro['serie'].'-'.$compro['num'].'</b>'.
				' por un total de <b>'.$total.'</b>'
		))->save('insert');
		$f->response->json($compro);
	}
	function execute_save_combinar(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$cliente = $data['cliente'];
		$cliente['_id'] = new MongoId($cliente['_id']);
		$caja = $data['caja'];
		$caja['_id'] = new MongoId($caja['_id']);
		$caja['local']['_id'] = new MongoId($caja['local']['_id']);
		$items = $data['items'];
		foreach ($items as $i=>$item){//comp
			if(isset($item['_id'])) $items[$i]['_id'] = new MongoId($item['_id']);
			if(isset($item['acta_conciliacion'])) $items[$i]['acta_conciliacion'] = new MongoId($item['acta_conciliacion']);
			if(isset($item['contrato'])) $items[$i]['contrato'] = new MongoId($item['contrato']);
			if(isset($item['tipo_pago'])) $items[$i]['tipo_pago'] = new MongoId($item['tipo_pago']);
			foreach ($item['items'] as $k=>$it) {//items
				if(isset($it['cuenta_cobrar'])){
					$items[$i]['items'][$k]['cuenta_cobrar']['_id'] = new MongoId($it['cuenta_cobrar']['_id']);
					$items[$i]['items'][$k]['cuenta_cobrar']['servicio']['_id'] = new MongoId($it['cuenta_cobrar']['servicio']['_id']);
					$items[$i]['items'][$k]['cuenta_cobrar']['servicio']['organizacion']['_id'] = new MongoId($it['cuenta_cobrar']['servicio']['organizacion']['_id']);
				}
				foreach ($it['conceptos'] as $l=>$conc) {
					if(isset($conc['concepto'])){
						if(gettype($conc['concepto'])=='array'){
							$items[$i]['items'][$k]['conceptos'][$l]['concepto']['_id'] = new MongoId($conc['concepto']['_id']);
						}
					}
					if(isset($conc['cuenta']))
						if(isset($conc['cuenta']['_id'])){
							$items[$i]['items'][$k]['conceptos'][$l]['cuenta']['_id'] = new MongoId($conc['cuenta']['_id']);
						}
					if(isset($conc['monto'])){
						$items[$i]['items'][$k]['conceptos'][$l]['monto'] = floatval($conc['monto']);
					}
				}
			}
		}
		$efec = $data['efectivos'];
		foreach ($efec as $i=>$ef){
			$efec[$i]['monto'] = floatval($ef['monto']);
		}
		if(isset($data['vouchers'])){
			$vouchers = $data['vouchers'];
			foreach ($vouchers as $i=>$vou){
				$vouchers[$i]['monto'] = floatval($vou['monto']);
				$vouchers[$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($data['fecreg'])){
			if(isset($data['fecemi'])) $data['fecreg'] = $data['fecemi'];
			if(isset($data['fec'])) $data['fecreg'] = $data['fec'];
			else $data['fecreg'] = new MongoDate();
		}
		$comp = array(
			'combinar_alq'=>true,
			'modulo'=>'IN',
			'fecreg'=>new MongoDate(strtotime($data['fecreg'])),
			'fecreal'=>new MongoDate(),
			'estado'=>'R',
			'periodo'=>date('ym00'),
			'autor'=>$trabajador,
			'cliente'=>$cliente,
			'caja'=>$caja,
			'tipo'=>$data['tipo'],
			'serie'=>$data['serie'],
			'num'=>floatval($data['num']),
			'moneda'=>$data['moneda'],
			'observ'=>$data['observ'],
			'items'=>$items,
			'total'=>floatval($data['total']),
			'tc'=>floatval($data['tc']),
			'efectivos'=>$efec
		);
		if(isset($data['sunat'])){
			$comp['sunat'] = $data['sunat'];
		}
		if(isset($data['valor_igv'])){
			$comp['valor_igv'] = floatval($data['valor_igv']);
		}
		if(isset($data['igv'])){
			$comp['igv'] = floatval($data['igv']);
		}
		if(isset($data['subtotal'])){
			$comp['subtotal'] = floatval($data['subtotal']);
		}
		if(isset($data['fecemi'])){
			$data['fecreg'] = new MongoDate(strtotime($data['fecemi']));
			$comp['fecreg'] = new MongoDate(strtotime($data['fecemi']));
		}
		if($data['moneda']=='D'){
			$comp['total_soles'] = floatval($data['total_soles']);
		}
		if(isset($vouchers)){
			$comp['vouchers'] = $vouchers;
		}
		$compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
		$f->model('cj/talo')->params(array(
			'serie'=>$data['serie'],
			'num'=>floatval($data['num']),
			'caja'=>$caja['_id']
		))->save('num');
		foreach ($items as $i=>$item){
			if(isset($item['_id'])) $items[$i]['_id'] = new MongoId($item['_id']);
			if(isset($item['acta_conciliacion'])) $items[$i]['acta_conciliacion'] = new MongoId($item['acta_conciliacion']);
			if(isset($item['contrato'])) $items[$i]['contrato'] = new MongoId($item['contrato']);
			foreach ($item['items'] as $k=>$it) {
				if(isset($it['cuenta_cobrar'])){
					$cuenta_cobrar = $f->model('cj/cuen')->params(array('_id'=>$it['cuenta_cobrar']))->get('one')->items;
					foreach ($cuenta_cobrar['comprobantes'] as $key => $comp_t) {
						if($comp_t==$items[$i]['_id']){
							$f->model('cj/cuen')->params(array(
								'_id'=>$cuenta_cobrar['_id'],
								'data'=>array(
									'comprobantes.'.$key=>$compro['_id']
								)
							))->save('update');
						}
					}
				}
			}
			if(isset($item['contrato'])){
				$contrato = $f->model('in/cont')->params(array('_id'=>$items[$i]['contrato']))->get('one')->items;
				foreach($contrato['pagos'] as $i_cont=>$pago){
					if(isset($pago['comprobante'])){
						if($pago['comprobante']['_id']==$items[$i]['_id']){
							$f->model('in/cont')->params(array(
								'_id'=>$contrato['_id'],
								'data'=>array(
									'pagos.'.$i_cont.'.comprobante'=>array(
										'_id'=>$compro['_id'],
										'tipo'=>$compro['tipo'],
										'serie'=>$compro['serie'],
										'num'=>$compro['num']
									)
								)
							))->save('update');
						}
					}elseif(isset($pago['comprobantes'])){
						foreach ($pago['comprobantes'] as $i_cont_m=>$pago_m) {
							if($pago['comprobantes'][$i_cont_m]['_id']==$items[$i]['_id']){
								$f->model('in/cont')->params(array(
									'_id'=>$contrato['_id'],
									'data'=>array(
										'pagos.'.$i_cont.'.comprobantes.'.$i_cont_m.'._id'=>$compro['_id'],
										'pagos.'.$i_cont.'.comprobantes.'.$i_cont_m.'.tipo'=>$compro['tipo'],
										'pagos.'.$i_cont.'.comprobantes.'.$i_cont_m.'.serie'=>$compro['serie'],
										'pagos.'.$i_cont.'.comprobantes.'.$i_cont_m.'.num'=>$compro['num']
									)
								))->save('update');
							}
						}
					}
				}
			}
			if(isset($item['acta_conciliacion'])){
				$acta_conciliacion = $f->model('in/acta')->params(array('_id'=>$items[$i]['acta_conciliacion']))->get('one')->items;
				foreach($acta_conciliacion['items'] as $i_cont=>$pago){
					if(isset($pago['comprobante'])){
						if($pago['comprobante']['_id']==$items[$i]['_id']){
							$f->model('in/acta')->params(array(
								'_id'=>$acta_conciliacion['_id'],
								'data'=>array(
									'items.'.$i_cont.'.comprobante'=>array(
										'_id'=>$compro['_id'],
										'tipo'=>$compro['tipo'],
										'serie'=>$compro['serie'],
										'num'=>$compro['num']
									)
								)
							))->save('update');
						}
					}
				}
			}
			$dele = $f->model('cj/comp')->params(array('_id'=>$items[$i]['_id']))->get('one')->items;
			$dele['feceli'] = new MongoDate();
			$dele['coleccion'] = 'cj_comprobantes';
			$dele['razon'] = 'Combinado';
			$dele['trabajador_delete'] = $f->session->userDB;
			$f->datastore->temp_del->insert($dele);
			$f->datastore->cj_comprobantes->remove(array('_id'=>$dele['_id']));
		}
		$f->response->json($compro);
	}
	function execute_anular(){
		global $f;
		$data = $f->request->data;
		$f->model("cj/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'X'
		)))->save("update");
		$data['_id'] = new MongoId($f->request->data['_id']);
		$comp = $f->model('cj/comp')->params(array('_id'=>$data['_id']))->get('one')->items;
		if(isset($comp['alquiler'])){
			$index = -1;
			$index_p = -1;
			$tmp = array();
			$contrato = $f->model('in/cont')->params(array('_id'=>$comp['contrato']))->get('one')->items;
			if(isset($comp['parcial'])){
				foreach ($contrato['pagos'] as $key => $value) {
					if(isset($value['comprobantes'])){
						foreach ($value['comprobantes'] as $k => $comp_p) {
							if($comp['_id']==$comp_p['_id']){
								$index = $key;
								$index_p = $k;
								$tmp = $value;
								$tmp['estado'] = 'P';
								unset($tmp['comprobantes'][$k]);
								if(sizeof($tmp['comprobantes'])==0){
									unset($tmp['comprobantes']);
									unset($tmp['total']);
								}
								break;
							}
						}
					}
				}
				$f->model('in/cont')->params(array('_id'=>$comp['contrato'],'data'=>array(
					'pagos.'.$index=>$tmp
				)))->save('update')->items;
			}elseif(isset($comp['combinar_alq'])){






				//





			}else{
				foreach ($contrato['pagos'] as $key => $value) {
					if(isset($value['comprobante'])){
						if($comp['_id']==$value['comprobante']['_id']){
							$index = $key;
							$tmp = $value;
							$f->model('in/cont')->params(array('_id'=>$comp['contrato'],'data'=>array(
								'pagos.'.$index=>array(
									'item'=>$tmp['item'],
									'ano'=>$tmp['ano'],
									'mes'=>$tmp['mes']
								)
							)))->save('update')->items;
							//break;
						}
					}
				}
			}
		}elseif(isset($comp['items'][0]['cuenta_cobrar'])){
			$f->model('cj/cuen')->params(array(
				'_id'=>$comp['items'][0]['cuenta_cobrar']['_id'],
				'data'=>array('$pull'=>array('comprobantes'=>$comp['_id']))
			))->save('custom');
			$f->model('cj/cuen')->params(array(
				'_id'=>$comp['items'][0]['cuenta_cobrar']['_id'],
				'data'=>array('$set'=>array('estado'=>'P'))
			))->save('custom');
		}
		$f->response->json(array('_id'=>$data['_id']));
	}
	function execute_save_anul(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		foreach($comp['items'] as $item){
			$total = 0;
			$upd_cuenta = array();
			$cuenta = $f->model("cj/cuen")->params(array("_id"=>$item['cuenta_cobrar']['_id']))->get("one")->items;
			foreach($cuenta['conceptos'] as $i=>$conc){
				$upd_cuenta['conceptos.'.$i.'.saldo'] = floatval($item['conceptos'][$i]['monto']);
				$total += floatval($item['conceptos'][$i]['monto']);
			}
			$upd_cuenta['saldo'] = $total;
			$f->model('cj/cuen')->params(array(
				'_id'=>$cuenta['_id'],
				'data'=>array('$pull'=>array('comprobantes'=>$comp['_id']))
			))->save('custom');
			$f->model('cj/cuen')->params(array(
				'_id'=>$cuenta['_id'],
				'data'=>array('$set'=>array('estado'=>'P'))
			))->save('custom');
			$f->model('cj/cuen')->params(array(
				'_id'=>$cuenta['_id'],
				'data'=>array('$inc'=>$upd_cuenta)
			))->save('custom');
		}
		$f->model('cj/comp')->params(array(
			'_id'=>$comp['_id'],
			'data'=>array('$set'=>array('estado'=>'X'))
		))->save('custom');
		$f->response->print("true");
	}
	function execute_save_rein(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['fecfin'])) $data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
		if(isset($data['total'])) $data['total'] = floatval($data['total']);
		if(isset($data['fuente']['_id'])) $data['fuente']['_id'] = new MongoId($data['fuente']['_id']);
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['componente']['_id'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(isset($data['organizacion']['actividad']['_id'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['subprograma']['_id'])) $data['organizacion']['subprograma']['_id'] = new MongoId($data['organizacion']['subprograma']['_id']);
		if(isset($data['organizacion']['programa']['_id'])) $data['organizacion']['programa']['_id'] = new MongoId($data['organizacion']['programa']['_id']);
		if(isset($data['organizacion']['funcion']['_id'])) $data['organizacion']['funcion']['_id'] = new MongoId($data['organizacion']['funcion']['_id']);
		if(isset($data['detalle'])){
			foreach ($data['detalle'] as $i=>$det){
				if(isset($det['cuenta']['_id'])) $data['detalle'][$i]['cuenta']['_id'] = new MongoId($det['cuenta']['_id']);
				if(isset($det['comprobante']['_id'])) $data['detalle'][$i]['comprobante']['_id'] = new MongoId($det['comprobante']['_id']);
				if(isset($det['cuenta_cobrar'])) $data['detalle'][$i]['cuenta_cobrar'] = new MongoId($det['cuenta_cobrar']);
			}
		}
		if(isset($data['comprobantes_anulados'])){
			foreach ($data['comprobantes_anulados'] as $i=>$det){
				if(isset($det['_id'])) $data['comprobantes_anulados'][$i]['_id'] = new MongoId($det['_id']);
			}
		}
		if(isset($data['cont_patrimonial'])){
			foreach ($data['cont_patrimonial'] as $i=>$det){
				if(isset($det['cuenta']['_id'])) $data['cont_patrimonial'][$i]['cuenta']['_id'] = new MongoId($det['cuenta']['_id']);
			}
		}
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $i=>$det){
				if(isset($det['cuenta_banco']['_id'])) $data['vouchers'][$i]['cuenta_banco']['_id'] = new MongoId($det['cuenta_banco']['_id']);
				if(isset($det['cliente']['_id'])) $data['vouchers'][$i]['cliente']['_id'] = new MongoId($det['cliente']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'RG';
			$data['autor'] = $f->session->userDB;
			$rein = $f->model("cj/rein")->params(array('data'=>$data))->save("insert")->items;
		}else{
			$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->json($rein);
	}
	function execute_save_cambiar(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$cliente = $data['cliente'];
		$cliente['_id'] = new MongoId($cliente['_id']);
		$servicio = $data['servicio'];
		$servicio['_id'] = new MongoId($servicio['_id']);
		if(isset($servicio['organizacion']))
			$servicio['organizacion']['_id'] = new MongoId($servicio['organizacion']['_id']);
		if(isset($data['fecven'])) $data['fecven'] = new MongoDate(strtotime($data['fecven']));
    	if(isset($data['saldo'])) $data['saldo'] = floatval($data['saldo']);
    	if(isset($data['total'])) $data['total'] = floatval($data['total']);
		foreach ($data['conceptos'] as $j=>$con){
      		if(isset($con['concepto']['_id'])) $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
      		if(isset($con['concepto']['_id'])) $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
      		if(isset($con['concepto']['clasificador']['_id'])) $data['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
      		if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $data['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
      		if(isset($con['concepto']['cuenta']['_id'])) $data['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
      		if(isset($con['saldo'])) $data['conceptos'][$j]['saldo'] = 0;
      		if(isset($con['total'])) $data['conceptos'][$j]['total'] = floatval($con['total']);
    	}
    	$comp_ori = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		switch ($comp_ori['tipo']){
			case 'B': $word = 'Boleta de Venta'; break;
			case 'R': $word = 'Recibo de Caja'; break;
			case 'F': $word = 'Factura'; break;
		}
		$cuenta = array(
			'fecreg'=>new MongoDate(),
			'estado'=>'C',
			'autor'=>$trabajador,
			'cliente'=>$cliente,
			'conceptos'=>$data['conceptos'],
			'fecven'=>$data['fecven'],
			'moneda'=>'S',
			'observ'=>'Cobro de Cambio de Nombre de '.$word.
				' con serie y n&uacute;mero <b>'.$comp_ori['serie'].'-'.$comp_ori['num'].'</b>',
			'saldo'=>0,
			'total'=>$data['total'],
   			'servicio'=>$servicio
		);
		$cuenta = $f->model("cj/cuen")->params(array('data'=>$cuenta))->save("insert")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Cuentas por Cobrar',
			'descr'=>'Se hizo el <b>Cambio de Nombre de '.$word.'</b>.'.
				' con serie y n&uacute;mero <b>'.$comp_ori['serie'].'-'.$comp_ori['num'].'</b>'
		))->save('insert');
		/*
		 * Se realiza la creacion del comprobante
		 */
		$caja = $data['comp']['caja'];
		$caja['_id'] = new MongoId($caja['_id']);
		$caja['local']['_id'] = new MongoId($caja['local']['_id']);
		$items = array(
			array(
				'cuenta_cobrar'=>array(
					'_id'=>$cuenta['_id'],
					'servicio'=>$servicio
				),
				'conceptos'=>array()
			)
		);
		foreach ($data['conceptos'] as $conc){
			$items[0]['conceptos'][] = array(
				'concepto'=>array(
					'_id'=>$conc['concepto']['_id'],
					'nomb'=>$conc['concepto']['nomb']
				),
				'monto'=>$conc['monto']
			);
		}
		$efec = $data['comp']['efectivos'];
		foreach ($efec as $i=>$ef){
			$efec[$i]['monto'] = floatval($ef['monto']);
		}
		if(isset($data['comp']['vouchers'])){
			$vouchers = $data['comp']['vouchers'];
			foreach ($vouchers as $i=>$vou){
				$vouchers[$i]['monto'] = floatval($vou['monto']);
				$vouchers[$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($data['fecreg']))
			$data['fecreg'] = date('Y-m-d');
		$comp = array(
			'fecreg'=>new MongoDate(strtotime($data['fecreg'])),
			'estado'=>'R',
			'periodo'=>date('ym00'),
			'autor'=>$trabajador,
			'cliente'=>$cliente,
			'caja'=>$caja,
			'tipo'=>$data['comp']['tipo'],
			'serie'=>$data['comp']['serie'],
			'num'=>floatval($data['comp']['num']),
			'moneda'=>$data['comp']['moneda'],
			'items'=>$items,
			'total'=>floatval($data['comp']['total']),
			'tc'=>floatval($data['comp']['tc']),
			'efectivos'=>$efec
		);
		if(isset($vouchers)){
			$comp['vouchers'] = $vouchers;
		}
		$compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
		$f->model('cj/cuen')->params(array(
			'_id'=>$cuenta['_id'],
			'data'=>array('$push'=>array('comprobantes'=>$compro['_id']))
		))->save('custom');
		/*
		 * Se actualiza el comprobante original
		 */
    	$f->model('cj/comp')->params(array('_id'=>$comp_ori['_id'],'data'=>array(
    		'cliente_nuevo'=>$cliente,
    		'estado'=>'P'
    	)))->save('update');
		$f->response->print('true');
	}
	function execute_save_confirmar_camb(){
		global $f;
		$data = $f->request->data;
		$comp_ori = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		/*
		 * Se actualiza el comprobante original
		 */
    	$f->model('cj/comp')->params(array('_id'=>$comp_ori['_id'],'data'=>array(
    		'estado'=>'X'
    	)))->save('update');
		/*
		 * Se crea un nuevo comprobante
		 */
		$new = $comp_ori;
		//$new['fecreg'] = new MongoDate();
		//$new['_id'] = new MongoId();
		$new['fecreg'] = new MongoDate(strtotime($data['comp']['fecreg']));
		$new['estado'] = 'R';//C
		$new['autor'] = $f->session->userDB;
		$new['caja'] = $data['comp']['caja'];
		$new['cliente'] = $comp_ori['cliente_nuevo'];
		$new['serie']= $data['comp']['serie'];
		$new['num']= floatval($data['comp']['num']);
		$new['comprobante_cambiado'] = array(
			'_id'=>$comp_ori['_id'],
			'serie'=>$comp_ori['serie'],
			'num'=>$comp_ori['num']
		);
		if(isset($new['caja']['_id']))
			$new['caja']['_id'] = new MongoId($new['caja']['_id']);
		if(isset($new['caja']['local']['_id']))
			$new['caja']['local']['_id'] = new MongoId($new['caja']['local']['_id']);
		unset($new['cliente_nuevo']);
		unset($new['_id']);
		$new['periodo'] = date('ym00');
		$compro = $f->model('cj/comp')->params(array('data'=>$new))->save('insert')->items;
		switch ($comp_ori['tipo']){
			case 'B': $word = 'Boleta de Venta'; break;
			case 'R': $word = 'Recibo de Caja'; break;
			case 'F': $word = 'Factura'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Comprobantes',
			'descr'=>'Se confirm&oacute; el <b>Cambio de Nombre de '.$word.'</b>.'.
				' con serie y n&uacute;mero <b>'.$new['serie'].'-'.$new['num'].'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_delete_cobro(){
		global $f;
		$acta = $f->model('cj/cuen')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$acta['feceli'] = new MongoDate();
		$acta['coleccion'] = 'cj_cuentas_cobrar';
		$f->datastore->temp_del->insert($acta);
		$f->datastore->cj_cuentas_cobrar->remove(array('_id'=>$acta['_id']));
		$f->response->print(true);
	}
	function execute_eliminar(){
		global $f;
		$item = $f->model('cj/comp')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'cj_comprobantes';
		$item['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($item);
		$f->datastore->cj_comprobantes->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_print(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($comp['cliente']['_id']))
			$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		if(isset($comp['playa']))
			$comp['playa'] = $f->model("in/play")->params(array("_id"=>$comp['playa']['_id']))->get("one")->items;
		if(isset($comp['contrato']))
			$comp['contrato'] = $f->model("in/cont")->params(array("_id"=>$comp['contrato']))->get("one")->items;
		if(isset($comp['acta_conciliacion']))
			$comp['acta_conciliacion'] = $f->model("in/acta")->params(array("_id"=>$comp['acta_conciliacion']))->get("one")->items;
		if(isset($comp['inmueble']))
			$comp['inmueble'] = $f->model("in/inmu")->params(array("_id"=>$comp['inmueble']['_id']))->get("one")->items;
		if(isset($comp['items'])){
			foreach($comp["items"] as $i=>$item){
				if(isset($item["cuenta_cobrar"])){
					$comp["items"][$i]["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$item["cuenta_cobrar"]["_id"]))->get("one")->items;
					//$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("in/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
				}
				if(isset($comp["combinar_alq"])){
					if(isset($item['acta_conciliacion'])){
						$comp['items'][$i]['acta_conciliacion'] = $f->model("in/acta")->params(array("_id"=>$item['acta_conciliacion']))->get("one")->items;
					}
				}
			}
			if(isset($comp["combinar_alq"])){
				if(isset($comp['items'][0]["contrato"])){
					$comp["contrato"] = $f->model("in/cont")->params(array("_id"=>$comp['items'][0]["contrato"]))->get("one")->items;
				}
			}
		}
		$print = false;
		if(isset($f->request->data['debug'])){
			header("Content-type:application/json");
			echo json_encode($comp);
			die();
		}
		if(isset($f->request->data['print'])) $print = true;
		if(isset($f->request->data['xls'])){
			switch ($comp['tipo']) {
				case 'B':
					$f->response->view("in/comp.bole.xls",array("data"=>$comp));
					break;
				case 'F':
					$f->response->view("in/comp.fact.xls",array("data"=>$comp));
					break;
				case 'R':
					$f->response->view("in/comp.reci.xls",array("data"=>$comp));
					break;
			}
		}else{
			switch ($comp['tipo']) {
				case 'B':
					$f->response->view("in/comp.bole.print",array("items"=>$comp,'print'=>$print));
					break;
				case 'F':
					$f->response->view("in/comp.fact.print",array("items"=>$comp,'print'=>$print));
					break;
				case 'R':
					$f->response->view("in/comp.reci.print",array("items"=>$comp,'print'=>$print));
					break;
				case 'RD':
					$f->response->view("in/comp.reci.print",array("items"=>$comp,'print'=>$print));
					break;
			}
		}
	}
	function execute_planilla(){
		global $f;
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;

		# COMPROBANTES MANUALES
		$filter = array(
			'modulo'=>'IN',
			'fecreg'=>array(
				'$gte'=>$recibo['fec'],
				'$lte'=>$recibo['fecfin']
			)
		);
		if($recibo['tipo_inm']=='A'){
			$filter['playa'] = array('$exists'=>false);
		}elseif($recibo['tipo_inm']=='P'){
			$filter['playa'] = array('$exists'=>true);
		}else{
			$filter['playa_azul'] = array('$exists'=>true);
		}
		$comprobantes = $f->model("cj/comp")->params(array(
			"filter"=>$filter,
			'sort'=>array('serie'=>1,'tipo'=>-1,'num'=>1)
		))->get("all")->items;
		# COMPROBANTES ELECTRONICOS
		$fields= array(
			'fecemi' => 1,
			'tipo' => 1,
			'serie' => 1,
			'estado' => 1,
			'numero' => 1,
			'cliente_nomb' => 1,
			'cliente_doc' => 1,
			'moneda' => 1,
			'items' => 1,
			'autor' => 1,
			'total_ope_gravadas' => 1,
			'total_igv' => 1,
			'total' => 1,
			"tipo_comprobante" => 1,
		 );

		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR INMUEBLES Y PLAYAS
		if($recibo['tipo_inm']=='A'){
			$inm=array('$in' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar','servicio'));
			#Alquileres utiliza la serie 001
			$serie=array('$in' => array('B001','F001'));
		}elseif($recibo['tipo_inm']=='P'){
			$inm=array('$in' => array('servicio'));
			#Playas utiliza la serie 004
			$serie=array('$in' => array('B004','F004'));
		}elseif($recibo['tipo_inm']=='PA'){
			$inm=array('$in' => array('servicio'));
			#Playas Azules utiliza la serie 006
			$serie=array('$in' => array('B006','F006'));
		}
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>$recibo['fec'],
				'$lte'=>$recibo['fecfin']
			),
			'estado' => array('$in' => array('FI','X','ES')),
			'items.tipo' => $inm,
			'serie' => $serie,
		);
		$ecom = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$fields,
			'sort'=>array('serie'=>-1,'numero'=>1)
		))->get("all")->items;
		
		if($ecom!=null){
			foreach($ecom as $i=>$co){
				if($co['estado']!=='X' || $co['estado']!=='BO' || $co['tipo']!=='RA'){
					if(isset($co['items'])){
						foreach($co['items'] as $j=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $k=>$conc){
									//if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
									//	$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
									//}else{
									//	# No tener cuenta por cobrar es una falla
									//	echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene cuenta por cobrar en uno de sus conceptos";die();
									//}
									//$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
									#EN CASO DE CUENTAS_COBRAR
									//if($item['tipo']=='cuenta_cobrar'){
									//	if($conc["descr"]!="IGV"){
									//		$caja_cuen_cobr=$f->model("cj/cuen")->params(array("_id"=>$conc['cuenta_cobrar']['_id']))->get("one")->items;
									//		foreach ($caja_cuen_cobr['conceptos'] as $l => $caja_cuen_conc){
									//			$ecom[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$caja_cuen_conc['concepto']['_id']))->get("one")->items;
									//		}
									//	}else{
									//		$ecom[$i]['items'][$j]['conceptos'][$k]['concepto']="IGV (18%)";
									//	}
									//}

									#EN CASO DE LAS PLAYAS SE DETECTARA EL AUTOR Y SE AGREGARA LA PLAYA CORRESPONDIENTE.
									# "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON EL STRING DE PLAYA "cuenta._id":ObjectId("51c20a9d4d4a13740b00000d"),
									# "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON "cuenta._id":ObjectId("58629f2f3e6037531d8b4567"),
									# "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON "cuenta._id":ObjectId("51c20adc4d4a13740b00000f"),
									# "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE CON 'cuenta._id' => new MongoId("55846d54cc1e90500900006e"),
									# "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON "cuenta._id":ObjectId("51c20aaf4d4a13c80600000e"),
									# "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON "cuenta._id":ObjectId("51c20abd4d4a13740b00000e"),
									# Reemplazaran esta cuenta
									# "cuenta._id":ObjectId("51acf3314d4a136011000031"),
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
										if(($cuenta_id->{'$id'}=='51acf3314d4a136011000031')){
											if($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbea13e603742398b456a'){
												$cuenta_id=new MongoId('51c20a9d4d4a13740b00000d');
												#PLAYA SANTA FE
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbecf3e603748398b4568'){
												$cuenta_id=new MongoId('55846d54cc1e90500900006e');
												#PLAYA LA PAZ
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71df613e603745448b4568'){
												$cuenta_id=new MongoId('58629f2f3e6037531d8b4567');
												#PLAYA PAUCARPATA
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71dead3e603728448b4568'){
												$cuenta_id=new MongoId('51c20adc4d4a13740b00000f');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbf3b3e603758398b456e'){
												$cuenta_id=new MongoId('51c20aaf4d4a13c80600000e');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbe3e3e603734398b4568'){
												$cuenta_id=new MongoId('51c20abd4d4a13740b00000e');
											}
											$playa=$f->model("in/play")->params(array("_id"=>$cuenta_id))->get("por_cuenta")->items;
											if(!is_null($playa)){
												$ecom[$i]['playa']=array(
													'_id' => $playa['_id'],
													'nomb' => $playa['nomb'],
													'cuenta' => $playa['cuenta'],
												);
											}else{
												$ecom[$i]['playa']=array(
													'_id' => $ecom[$i]['autor']['_id'],
													'nomb' => $ecom[$i]['autor']['nomb'] + $ecom[$i]['autor']['appat'] + $ecom[$i]['autor']['apmat'],
													'cuenta._id' => $cuenta_id,
												);
											}


											//print_r($playa);

										}
									}


									#EN CASO DE QUE TENGA UN CAMPO CONTRATO, SE OBTIENE EL INMUEBELE (ID Y DIRECCION) (SE ASUME QUE EL COPROBANTE SEA DE UN MISMO INMUEBLE)
									if(isset($conc['alquiler']['contrato'])){
										$inmu_cntr=$f->model("in/cont")->params(array("_id"=>$conc['alquiler']['contrato']))->get("one")->items;
										$ecom[$i]['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
										$ecom[$i]['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
									}

								}
							}else{
								# No tener concepto es una falla
								echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";die();
							}
						}
					}
					else{
						# No tener el elemento items es una falla
						echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento items";die();
					}
				}else{
					$ecom[$i]['playa']=array(
						'nomb' => "ANULADO o BORRADOR",
					);
											}
			}
		}

		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR OTROS PROGRAMAS CON LA SERIES DE INMUEBLES
		if($recibo['tipo_inm']=='A'){
			$inm=array('$ne' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar'));
			#Alquileres utiliza la serie 001
			$serie=array('$in' => array('B001','F001'));
		}elseif($recibo['tipo_inm']=='P'){
			$inm=array('$ne' => array('servicio'));
			#Playas utiliza la serie 004
			$serie=array('$in' => array('B004','F004'));
		}
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>$recibo['fec'],
				'$lte'=>$recibo['fecfin']
			),
			'estado' => 'X',
			'items.tipo' => $inm,
			'serie' => $serie,
		);
		$ecom_anul = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$fields,
			'sort'=>array('numero'=>-1)
		))->get("all")->items;



		if (is_null($ecom) && !is_null($ecom_anul)) {
			$ecom = $ecom_anul;
		} elseif (is_null($ecom_anul) && !is_null($ecom)) {
			$ecom = $ecom;
		} elseif (!is_null($ecom_anul) && !is_null($ecom)) {
			$ecom = array_merge_recursive($ecom, $ecom_anul);
		}



		//var_dump($ecom);
		//echo "<br>";
		//var_dump($ecom_anul);
		//die();


		if($recibo['tipo_inm']=='A'){
			if(!is_null($ecom)){
				foreach ($ecom as $k=>$ecom_unic){
					//print_r($ecom);
					if(isset($ecom_unic['inmueble'])){
						//print_r($ecom_unic['inmueble']['_id']);
						$ecom[$k]['inmueble'] = $f->model('in/inmu')->params(array('_id'=>$ecom_unic['inmueble']['_id']))->get('one')->items;
						//print_r($ecom[$k]['inmueble']);
					}
				}
				//die();
			}
		}



		if (is_null($ecom) && !is_null($comprobantes)) {
			$comprobantes = $comprobantes;
		} elseif (!is_null($ecom) && is_null($comprobantes)) {
			$comprobantes = $ecom;
		} elseif (!is_null($ecom) && !is_null($comprobantes)) {
			$comprobantes = array_merge_recursive($comprobantes, $ecom);
		}
		$unicos = array();
		foreach($comprobantes as $j => $comp){
			if(isset($comp['numero'])){
				$num = $comp['numero'];
				if(!in_array($num,array_column($unicos,'numero'))){
					$unicos[] = $comp;
				}
			}else if(isset($comp['num'])){
				$num = $comp['num'];
				if(!in_array($num,array_column($unicos,'num'))){
					$unicos[] = $comp;
				}
			}
			
		}
		
		$comprobantes = $unicos;
		$f->response->view("in/repo.planilla.print",array('recibo'=>$recibo,'comp'=>$comprobantes));
	}
	function execute_reci_ing(){
		global $f;
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;
		foreach ($recibo['detalle'] as $k => $row) {
			$recibo['detalle'][$k]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$row['comprobante']['_id']))->get('one')->items;
			if(isset($recibo['detalle'][$k]['comprobante']['inmueble'])){
				$recibo['detalle'][$k]['comprobante']['inmueble'] = $f->model('in/inmu')->params(array('_id'=>$recibo['detalle'][$k]['comprobante']['inmueble']['_id']))->get('one')->items;
			}
			if(isset($recibo['detalle'][$k]['comprobante']['items'][0]['cuenta_cobrar'])){
				$recibo['detalle'][$k]['comprobante']['items'][0]['cuenta_cobrar']['servicio'] = $f->model('mg/serv')->params(array('_id'=>$row['comprobante']['items'][0]['cuenta_cobrar']['servicio']['_id']))->get('one')->items;
			}
		}
		//DETALLE
		foreach ($recibo['detalle'] as $k => $row) {
		    $ctasd[$k] = $row['comprobante']['tipo'];
		    $ctas[$k] = $row['cuenta']['cod'];
		    $ctasc[$k] = $row['comprobante']['num'];
		}
		array_multisort($ctas,SORT_ASC,$ctasd, SORT_DESC, $ctasc, SORT_ASC, $recibo['detalle']);
		/*foreach ($recibo['detalle'] as $k => $row) {
		    $ctas[$k] = $row['cuenta']['cod'];
		}
		array_multisort($ctas, SORT_ASC, $recibo['detalle']);*/
		//CONTABILIDAD PATRIMONIAL
		foreach ($recibo['cont_patrimonial'] as $k => $row) {
		    $ctasp[$k] = substr($row['cuenta']['cod'],0,9);//$row['cuenta']['cod'];
		    $ctast[$k] = $row['tipo'];
		}
		array_multisort($ctast,SORT_ASC,$ctasp,SORT_DESC,$recibo['cont_patrimonial']);
		//print_r($recibo);die();
		if($recibo['tipo_inm']=='P'){
			$f->response->view("in/repo.rein.print.playa",array('recibo'=>$recibo));
		}else{
			$f->response->view("in/repo.rein.print",array('recibo'=>$recibo));
		}
	}
	function execute_reci_ing2(){
		global $f;
		//error_reporting( E_ALL );
		$f->library('helpers');
		$helper=new helper();
		$meses = array("Ene.","Feb.","Mar.","Abr.","May.","Jun.","Jul.","Ago.","Set.","Oct.","Nov.","Dic.");
		$model = $f->model("ct/pcon")->params(array('oficial'=>true))->get("lista");
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;
		$total_detraccion = 0;
		foreach ($recibo['detalle'] as $k => $row) {
			$cmpbnt=$f->model('cj/comp')->params(array('_id'=>$row['comprobante']['_id']))->get('one')->items;
			if(is_null($cmpbnt)){
				$cmpbnt=$f->model('cj/ecom')->params(array('_id'=>$row['comprobante']['_id']))->get('one')->items;
				#CAMPOS INNECESARIOS
				unset($cmpbnt['codigo_barras_pdf']);
				unset($cmpbnt['estado_resumen']);
				unset($cmpbnt['estado_baja']);
				unset($cmpbnt['sunat_note']);
				unset($cmpbnt['sunat_description']);
				unset($cmpbnt['sunat_responsecode']);
				unset($cmpbnt['sunat_faultcode']);
				unset($cmpbnt['sunat_soap_error']);
				unset($cmpbnt['digest_value']);
				unset($cmpbnt['signature_value']);
				unset($cmpbnt['codigo_barras']);
				unset($cmpbnt['codigo_barras_pdf']);
				unset($cmpbnt['ruta_zip_firmado']);
				unset($cmpbnt['ruta_xml_firmado']);
				unset($cmpbnt['ruta_cdr_xml']);
				unset($cmpbnt['ruta_pdf']);
					if(isset($cmpbnt['items'])){
						foreach($cmpbnt['items'] as $l=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $m=>$conc){

									#EN CASO DE QUE TENGA UN CAMPO CONTRATO, SE OBTIENE EL INMUEBELE (ID Y DIRECCION) (SE ASUME QUE EL COPROBANTE SEA DE UN MISMO INMUEBLE)
									if(isset($conc['alquiler']['contrato'])){
										$inmu_cntr=$f->model("in/cont")->params(array("_id"=>$conc['alquiler']['contrato']))->get("one")->items;
										$cmpbnt['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
										$cmpbnt['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
									}
									if(isset($conc['acta_conciliacion'])){
										$inmu_cntr=$f->model("in/acta")->params(array("_id"=>$conc['acta_conciliacion']))->get("one")->items;
										$cmpbnt['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
										$cmpbnt['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
									}

								}
							}else{
								# No tener concepto es una falla
								echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";die();
							}
						}
					}

			}
			$recibo['detalle'][$k]['comprobante'] = $cmpbnt;
			//$recibo['detalle'][$k]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$row['comprobante']['_id']))->get('one')->items;
			if(isset($recibo['detalle'][$k]['comprobante']['inmueble'])){
				$recibo['detalle'][$k]['comprobante']['inmueble'] = $f->model('in/inmu')->params(array('_id'=>$recibo['detalle'][$k]['comprobante']['inmueble']['_id']))->get('one')->items;
			}
		}
		if (isset($recibo['vouchers'])) {
			foreach ($recibo['vouchers'] as $voucher) {
				if($voucher['cuenta_banco']['cod']=='101-089983'){
					$total_detraccion+=floatval($voucher['monto']);
					//echo $voucher['monto']."<br />";
				}
			}
		}


		//DETALLE
		$detalle_tmp = array();
		foreach($recibo['detalle'] as $i=>$item){
			//print_r($item);
			if(isset($item['cuenta'])){
				if(!isset($detalle_tmp[$item['cuenta']['cod']])){
					$detalle_tmp[$item['cuenta']['cod']]=$item['cuenta'];
					$detalle_tmp[$item['cuenta']['cod']]['total'] = 0;
					$detalle_tmp[$item['cuenta']['cod']]['items'] = array();
				}
				//var_dump($item['cuenta']['cod']);
				//var_dump($item);
				//var_dump($item['comprobante']['_id']);
				if(!isset($detalle_tmp[$item['cuenta']['cod']]['items'][$item['comprobante']['tipo']."_".$item['comprobante']['_id']->{'$id'}.$i])){
					$detalle_tmp[$item['cuenta']['cod']]['items'][$item['comprobante']['tipo']."_".$item['comprobante']['_id']->{'$id'}.$i] = $item;
				}
				$detalle_tmp[$item['cuenta']['cod']]['total']+=floatval($item['monto']);
			}
		}

		//print_r($detalle_tmp['1201.0303.44.01']);
		//die();

		if($total_detraccion>0){
			if(!isset($detalle_tmp['2101.010501.'])){
				$detalle_tmp['2101.010501.']=array(
					'_id'=>new MongoId("53346aefee6f96841d000113"),
					'cod'=>'2101.010501.',
					'descr'=>'Unidad de Inmuebles - detraccion'
				);
				$detalle_tmp['2101.010501.']['total'] = $total_detraccion;
				$detalle_tmp['2101.010501.']['items']['45s56as45sq']=array(
					'_id'=>new MongoId(),
					'detraccion'=>true,
					'monto'=>$total_detraccion,
					'cuenta'=>array(
						'_id'=>new MongoId("53346aefee6f96841d000113"),
						'cod'=>'2101.010501.',
						'descr'=>'Unidad de Inmuebles - detraccion'
					)
				);
			}else{
				$detalle_tmp['2101.010501.']['total']+=$total_detraccion;
				$detalle_tmp['2101.010501.']['items']['45s56as45sq']=array(
					'_id'=>new MongoId(),
					'detraccion'=>true,
					'monto'=>$total_detraccion,
					'cuenta'=>array(
						'_id'=>new MongoId("53346aefee6f96841d000113"),
						'cod'=>'2101.010501.',
						'descr'=>'Unidad de Inmuebles - detraccion'
					)
				);
			}
		}
		//print_r($detalle_tmp);die();
		$detalle = array();
		$detrac_flag = 0;
		foreach($model->items as $i=>$item){
			if(strlen($item["cod"])>2){
				$find_acum = preg_filter("/^".$item["cod"]."/", '$0', array_keys( $detalle_tmp ));
				if(count($find_acum)>0){
					//echo $item["cuenta"]["cod"].' '.count($find_acum);
					$cuenta = $item;
					$cuenta['total'] = 0;
					$cuenta['items'] = array();
					foreach($find_acum as $row){
						/*if($item["cod"]=='2103'){
							print_r($detalle_tmp[$row]);
						}*/
						/*if($item["cod"]=='1201'){
							echo $item['cod']." - ".$row."<br />";
							//echo ."<br />";
						}*/

						if(count($detalle_tmp[$row]['items'])>0){
							/*echo $item['cod']." - ".$row."<br />";
							if($item['cod']=='2103.03.47.1'){
								print_r($item);
							}*/
							if(!isset($item['cuentas']['hijos'])){
								if($item["cod"]!=$row){
									continue;
								}
							}
						}
						if($item["cod"]=='2101.010501'){
							if($row!='2101.010501.'){
								$cuenta["total"] += $detalle_tmp[$row]['total'];
								if($item["cod"] == $row){
									$sort_items = $detalle_tmp[$row]['items'];
									krsort($sort_items);
									array_push($cuenta['items'], $sort_items);
									//array_push($cuenta['items'], $detalle_tmp[$row]['items']);
								}
							}
						}else{
							//print_r($detalle_tmp[$row]);
							$cuenta["total"] += $detalle_tmp[$row]['total'];
							if($item["cod"] == $row){
								$sort_items = $detalle_tmp[$row]['items'];
								krsort($sort_items);
								array_push($cuenta['items'], $sort_items);
							}
						}
						if($item["cod"]=='2101.010501'){
							if($detrac_flag==0&&$total_detraccion>0){
								array_push($detalle, array(
									'_id'=>new MongoId("53346aefee6f96841d000113"),
									'cod'=>'2101.010501.',
									'descr'=>'Unidad de Inmuebles - detraccion',
									'total'=>$detalle_tmp['2101.010501.']['total'],
									'items'=>array($detalle_tmp['2101.010501.']['items'])
								));
								$detrac_flag++;
							}
						}
					}
					if($cuenta['total']>0){
						array_push($detalle, $cuenta);
					}
				}
			}
		}
		/*if($item["cod"]!="2101.010501."){
			if(startsWith($item["cod"], "2101.010501")){
				$cuenta["total"]-=
			}
		}*/

		foreach($detalle as $i=>$item){
			if($this->execute_startsWith($item["cod"], "2101")){
				if($item["cod"]!="2101.010501."){
					$detalle[$i]["total"]-=$total_detraccion;
				}
			}
		}
		//print_r($detalle);die();
		$recibo['detalle2'] = array();
		if($recibo['modulo']=='IN'){
			$_item = array(
				'col_1'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>9,
						'align'=>'L',
						'w'=>25
					),
					'value'=>''
				),
				'col_2'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'L',
						'w'=>64
					),
					'value'=>''
				),
				'col_3'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'L',
						'w'=>80
					),
					'value'=>'RECAUDADO HOY SEGUN PLANILLA '.$recibo['planilla']
				),
				'col_4'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'L',
						'w'=>35
					),
					'value'=>''
				),
				'col_5'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'L',
						'w'=>25
					),
					'value'=>''
				),
				'col_6'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'R',
						'w'=>25
					),
					'value'=>''
				),
				'col_7'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'R',
						'w'=>25
					),
					'value'=>''
				),
				'col_8'=>array(
					'opt'=>array(
						'type'=>'',
						'size'=>8,
						'align'=>'R',
						'w'=>25
					),
					'value'=>''
				),
				'partial'=>true,
				'partial_monto'=>0,
				'hidden'=>false
			);
			array_push($recibo['detalle2'], $_item);
		}
		foreach($detalle as $item){
			//echo $item['cod'].' '.$item['descr'].' =============================>'.$item['total'].'<br />';
			switch($recibo['modulo']){
				case "IN":
					$col_1 = '';
					$col_2 = $item['cod'];
					$col_3 = $item['descr'];
					$col_4 = '';
					$col_5 = '';
					$col_6 = '';
					$col_7 = '';
					//if(count($item['items'])>0){
						//$_item['hide_ammount'] = true;
						$col_7 = number_format($item['total'],2);
					//}
					$col_8 = '';
					$_item = array(
						'col_1'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>25
							),
							'value'=>$col_1
						),
						'col_2'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>30
							),
							'value'=>$col_2
						),
						'col_3'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>110//96
							),
							'value'=>$col_3
						),
						'col_4'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>80//25
							),
							'value'=>$col_4
						),
						'col_5'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>25
							),
							'value'=>$col_5
						),
						'col_6'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>25
							),
							'value'=>$col_6
						),
						'col_7'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>10,
								//'size'=>8,
								'align'=>'R',
								'w'=>18
							),
							'value'=>$col_7
						),
						'col_8'=>array(
							'opt'=>array(
								'type'=>'B',
								//'size'=>8,
								'size'=>10,
								'align'=>'R',
								'w'=>18
							),
							'value'=>$col_8
						),
						'cuenta'=>true,
						'cuenta_detalle'=>$col_2,
						'cuenta_monto'=>$col_7,
						'hidden'=>false
					);
					if(strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47' || $col_2=='2101.010501'){
						$_item['col_2']['opt']['type'] = 'BU';
						$_item['col_3']['opt']['type'] = 'BU';
					}
					if(strlen($item['cod'])==4){
						$_item['col_7']['value'] = '';
						$_item['col_8']['value'] = number_format($item['total'],2);
						//echo $total;
					}
					break;
				case "MH":
					$col_1 = $item['cod'];
					$col_2 = $item['descr'];
					$col_3 = '';
					$col_4 = '';
					$col_5 = '';
					$col_6 = '';
					//if(count($item['items'])>0){
						$col_7 = number_format($item['total'],2);
					/*}else{
						$col_7 = '';
					}*/
					$col_8 = '';

					$_item = array(
						'col_1'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>28
							),
							'value'=>$col_1
						),
						'col_2'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>96
							),
							'value'=>$col_2
						),
						'col_3'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_3
						),
						'col_4'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_4
						),
						'col_5'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_5
						),
						'col_6'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_6
						),
						'col_7'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_7
						),
						'col_8'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_8
						)
					);
					if(strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47'){
						$_item['col_2']['opt']['type'] = 'BU';
						$_item['col_3']['opt']['type'] = 'BU';
					}
					if(strlen($item['cod'])==4){
						$_item['col_7']['value'] = '';
						$_item['col_8']['value'] = number_format($item['total'],2);
					}
					break;
				case "AD":
					$col_1 = $item['cod'];
					$col_2 = $item['descr'];
					$col_3 = '';
					$col_4 = '';
					$col_5 = '';
					$col_6 = '';
					//if(count($item['items'])>0){
						$col_7 = number_format($item['total'],2);
					/*}else{
						$col_7 = '';
					}*/
					$col_8 = '';

					$_item = array(
						'col_1'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>28
							),
							'value'=>$col_1
						),
						'col_2'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>96
							),
							'value'=>$col_2
						),
						'col_3'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_3
						),
						'col_4'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_4
						),
						'col_5'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_5
						),
						'col_6'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_6
						),
						'col_7'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_7
						),
						'col_8'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_8
						)
					);
					if(strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47'){
						$_item['col_2']['opt']['type'] = 'BU';
						$_item['col_3']['opt']['type'] = 'BU';
					}
					if(strlen($item['cod'])==4){
						$_item['col_7']['value'] = '';
						$_item['col_8']['value'] = number_format($item['total'],2);
					}
					break;
				case "LM":
					$col_1 = $item['cod'];
					$col_2 = $item['descr'];
					$col_3 = '';
					$col_4 = '';
					$col_5 = '';
					$col_6 = '';
					//if(count($item['items'])>0){
						$col_7 = number_format($item['total'],2);
					/*}else{
						$col_7 = '';
					}*/
					$col_8 = '';

					$_item = array(
						'col_1'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>28
							),
							'value'=>$col_1
						),
						'col_2'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>96
							),
							'value'=>$col_2
						),
						'col_3'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_3
						),
						'col_4'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_4
						),
						'col_5'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_5
						),
						'col_6'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_6
						),
						'col_7'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_7
						),
						'col_8'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_8
						)
					);
					if(strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47'){
						$_item['col_2']['opt']['type'] = 'BU';
						$_item['col_3']['opt']['type'] = 'BU';
					}
					if(strlen($item['cod'])==4){
						$_item['col_7']['value'] = '';
						$_item['col_8']['value'] = number_format($item['total'],2);
						echo $total;
					}
					break;
					case "TD":
					$col_1 = $item['cod'];
					$col_2 = $item['descr'];
					$col_3 = '';
					$col_4 = '';
					$col_5 = '';
					$col_6 = '';
					//if(count($item['items'])>0){
						$col_7 = number_format($item['total'],2);
					/*}else{
						$col_7 = '';
					}*/
					$col_8 = '';

					$_item = array(
						'col_1'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>28
							),
							'value'=>$col_1
						),
						'col_2'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>96
							),
							'value'=>$col_2
						),
						'col_3'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_3
						),
						'col_4'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_4
						),
						'col_5'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_5
						),
						'col_6'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_6
						),
						'col_7'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_7
						),
						'col_8'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_8
						)
					);
					if(strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47'){
						$_item['col_2']['opt']['type'] = 'BU';
						$_item['col_3']['opt']['type'] = 'BU';
					}
					if(strlen($item['cod'])==4){
						$_item['col_7']['value'] = '';
						$_item['col_8']['value'] = number_format($item['total'],2);
						echo $total;
					}
					break;
				case "FA":
					$col_1 = $item['cod'];
					$col_2 = $item['descr'];
					$col_3 = '';
					$col_4 = '';
					$col_5 = '';
					$col_6 = '';
					//if(count($item['items'])>0){
						$col_7 = number_format($item['total'],2);
					/*}else{
						$col_7 = '';
					}*/
					$col_8 = '';

					$_item = array(
						'col_1'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>28
							),
							'value'=>$col_1
						),
						'col_2'=>array(
							'opt'=>array(
								'type'=>'B',
								'size'=>9,
								'align'=>'L',
								'w'=>96
							),
							'value'=>$col_2
						),
						'col_3'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_3
						),
						'col_4'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_4
						),
						'col_5'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_5
						),
						'col_6'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>11,
								'align'=>'L',
								'w'=>20
							),
							'value'=>$col_6
						),
						'col_7'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_7
						),
						'col_8'=>array(
							'opt'=>array(
								'type'=>'',
								'size'=>9,
								'align'=>'R',
								'w'=>20
							),
							'value'=>$col_8
						)
					);
					if(strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47'){
						$_item['col_2']['opt']['type'] = 'BU';
						$_item['col_3']['opt']['type'] = 'BU';
					}
					if(strlen($item['cod'])==4){
						$_item['col_7']['value'] = '';
						$_item['col_8']['value'] = number_format($item['total'],2);
						echo $total;
					}
					break;
			}
			array_push($recibo['detalle2'], $_item);
			if(count($item['items'])>0){
				foreach($item['items'] as $comp){
					switch($recibo['modulo']){
						case "IN":
							foreach($comp as $det){
								$col_1 = '';
								$col_2 = '';
								$col_3 = '';
								$col_4 = '';
								$col_5 = '';
								$col_6 = '';
								$col_7 = '';
								$col_8 = '';
								if(isset($det['comprobante']['inmueble'])){
									$col_5 = $det['comprobante']['inmueble']['abrev'];
								}
								if(isset($det['comprobante']['alquiler'])){
									if(sizeof($det['comprobante']['items'])==1){
										if(isset($det['comprobante']['items'])){
											if(isset($det['comprobante']['items'][0]['dia_ini'])){
												if($det['comprobante']['items'][0]['dia_ini']=='16'){
													$mes = intval($det['comprobante']['items'][0]['pago']['mes'])-1;
													$ano = intval(substr($det['comprobante']['items'][0]['pago']['ano'],2));
													if($mes==-1){
														$mes = 11;
														$ano--;
													}
													$mes_2 = $mes;
													if($mes==0){
														$mes=12;
														$mes_2=1;
													}else{
														$mes_2+=1;
													}
													$descr = '16/'.$mes.' al 15/'.($mes_2).'/'.$ano;
												}elseif($det['comprobante']['items'][0]['dia_ini']=='1'){
													$mes = intval($det['comprobante']['items'][0]['pago']['mes'])-1;
													$ano = intval(substr($det['comprobante']['items'][0]['pago']['ano'],2));
													if($mes==-1){
														$mes = 11;
														$ano--;
													}
													$descr = $meses[$mes].' \''.$ano;
												}else{
													$dia_ini = intval($det['comprobante']['items'][0]['dia_ini']);
													$dia_fin = $dia_ini;

													$mes = intval($det['comprobante']['items'][0]['pago']['mes'])-1;
													$ano = intval(substr($det['comprobante']['items'][0]['pago']['ano'],2));
													if($mes==-1){
														$mes = 11;
														$ano--;
													}
													$mes_2 = $mes+1;
													if($mes==0){
														$mes=12;
														$mes_2-=1;
													}
													if(isset($det['comprobante']['items'][0]['dia_fin'])){
														$dia_fin = intval($det['comprobante']['items'][0]['dia_fin']);
													}
													$descr = $dia_ini.'/'.$mes.' al '.($dia_fin).'/'.($mes_2).'/'.$ano;
												}
											}else{
												$mes = intval($det['comprobante']['items'][0]['pago']['mes'])-1;
												$ano = intval(substr($det['comprobante']['items'][0]['pago']['ano'],2));
												if($mes==-1){
													$mes = 11;
													$ano--;
												}
												$descr = $meses[$mes].' \''.$ano;
											}
											$col_5 = $descr;
										}
									}else{
										$tmp_size = sizeof($det['comprobante']['items']);
										$mes_ini = intval($det['comprobante']['items'][0]['pago']['mes'])-1;
										$ano_ini = intval(substr($det['comprobante']['items'][0]['pago']['ano'],2));
										if($mes_ini==-1){
											$mes_ini = 11;
											$ano_ini--;
										}
										$dia_ini = $det['comprobante']['items'][0]['dia_ini'];
										if($dia_ini=="16" || $dia_ini=="15"){
											$mes_ini--;
										}

										$mes_fin = intval($det['comprobante']['items'][$tmp_size-1]['pago']['mes'])-1;
										$ano_fin = intval(substr($det['comprobante']['items'][$tmp_size-1]['pago']['ano'],2));
										if($mes_fin==-1){
											$mes_fin = 11;
											$ano_fin--;
										}
										if($dia_ini=="16" || $dia_ini=="15"){
											$mes_fin--;
										}
										$cada = ' cm S/.';

										foreach ($det['comprobante']['items'][0]['conceptos'] as $ik=>$conc_tmp) {
											if($conc_tmp['cuenta']['cod']!='1202.0901.47' && $conc_tmp['cuenta']['cod']!='2101.010503.47' && $conc_tmp['cuenta']['cod']!='2101.010501'){
												$cada .= $conc_tmp['monto'];
											}
										}
										if($ano_ini==$ano_fin){
											$col_5 = $meses[$mes_ini].' a '.$meses[$mes_fin].' \''.$ano_ini.$cada;
											

										}else{
											$col_5 = $meses[$mes_ini].' \''.$ano_ini.' a '.$meses[$mes_fin].' \''.$ano_fin.$cada;
											
										}

										/**
										*	EXEPCIONES DE RECIBOS MOSTRADOS POR EL SEÑOR ANGEL
										*/
										//print_r($det['comprobante']);
										//if(isset($det['comprobante']['num'])){
										//	if($det['comprobante']['num']==526 && $det['comprobante']['tipo']=='B'){
										//		print_r($det['comprobante']);
										//		$col_5 = "Ene".' a '.$meses[$mes_fin].' \''.$ano_ini.$cada;
										//		//$col_5 = "Ene".' a '.$meses[$mes_fin].' \''.$ano_ini.$cada;
										//	}
										//}
									}
									$col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['num'];
									$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['nomb'];
									if($det['comprobante']['cliente']['tipo_enti']=='P')
										$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['appat'].' '.$det['comprobante']['cliente']['apmat'].', '.$det['comprobante']['cliente']['nomb'];
									$col_2 = $det['comprobante']['cliente']['fullname'];
									//$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
									if(isset($det['comprobante']['parcial'])){
										$col_4 = $helper->format_word($det['comprobante']['items'][0]['conceptos'][0]['concepto']);
									}else{
										$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
									}
									//$col_5 = "";
									$col_6 = number_format($det['monto'],2);
								}elseif(isset($det['comprobante']['acta_conciliacion'])){
									$col_5 = "";
									/*if(count($det['comprobante']['items'][0]['conceptos'])>0){
										for($ii=0;$ii<count($det['comprobante']['items'][0]['conceptos']);$ii++){
											if($det['comprobante']['items'][0]['conceptos'][$ii]['cuenta']['cod']!='2101.010503.47'){
												$col_5.=$helper->format_word($det['comprobante']['items'][0]['conceptos'][$ii]['concepto'])." \n";
											}
										}
									}*/
									$col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['num'];
									$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['nomb'];
									if($det['comprobante']['cliente']['tipo_enti']=='P')
										$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['appat'].' '.$det['comprobante']['cliente']['apmat'].', '.$det['comprobante']['cliente']['nomb'];
									$col_2 = $det['comprobante']['cliente']['fullname'];
									//$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
									if(isset($det['comprobante']['parcial'])){
										$col_4 = $helper->format_word($det['comprobante']['items'][0]['conceptos'][0]['concepto']);
									}else{
										$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
									}
									//$col_5 = "";
									$col_6 = number_format($det['monto'],2);
								}elseif(!isset($det['comprobante']['playa'])){
									/**/
									if(isset($det['comprobante']['items'])){
										if(count($det['comprobante']['items'])>0){
											$col_4 = "";
											$col_5 = "";
											//echo "<pre>";
											//print_r($det['comprobante']);
											//echo "</pre>";
											//if(!isset($det['comprobante']['parcial'])){
											//	$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
											//}else{
											//	$col_4 = $helper->format_word($det['comprobante']['observ']);
											//}
											/*foreach($det['comprobante']['items'] as $k=>$_it){
												if(isset($_it['alquiler']) && $_it['alquiler']==true){
													$contrato = $f->model('in/cont')->params(array('_id'=>$_it['contrato']))->get('one')->items;
													if(isset($det['comprobante']['items'][$k]['dia_ini'])){
														if($det['comprobante']['items'][$k]['dia_ini']=='16'){
															$mes = intval($det['comprobante']['items'][$k]['pago']['mes'])-1;
															$ano = intval(substr($det['comprobante']['items'][$k]['pago']['ano'],2));
															if($mes==-1){
																$mes = 11;
																$ano--;
															}
															$mes_2 = $mes;
															if($mes==0){
																$mes=12;
																$mes_2=1;
															}else{
																$mes_2+=1;
															}
															$descr = '16/'.$mes.' al 15/'.($mes_2).'/'.$ano;
														}elseif($det['comprobante']['items'][$k]['dia_ini']=='1'){
															$mes = intval($det['comprobante']['items'][$k]['pago']['mes'])-1;
															$ano = intval(substr($det['comprobante']['items'][$k]['pago']['ano'],2));
															if($mes==-1){
																$mes = 11;
																$ano--;
															}
															$descr = $meses[$mes].' \''.$ano;
														}else{
															$dia_ini = intval($det['comprobante']['items'][$k]['dia_ini']);
															$mes = intval($det['comprobante']['items'][$k]['pago']['mes'])-1;
															$ano = intval(substr($det['comprobante']['items'][$k]['pago']['ano'],2));
															if($mes==-1){
																$mes = 11;
																$ano--;
															}
															$mes_2 = $mes;
															if($mes==0){
																$mes=12;
																$mes_2-=1;
															}
															$descr = $dia_ini.'/'.$mes.' al '.($dia_ini-1).'/'.($mes_2).'/'.$ano;
														}
													}else{
														$mes = intval($det['comprobante']['items'][$k]['pago']['mes'])-1;
														$ano = intval(substr($det['comprobante']['items'][$k]['pago']['ano'],2));
														if($mes==-1){
															$mes = 11;
															$ano--;
														}
														$descr = $meses[$mes].' \''.$ano;
													}
													$col_4.= $contrato['inmueble']['direccion']."\n";
													$col_5.=$descr."\n";
												}
											}*/
										}
									}
									/*if(isset($det['comprobante']['items'][0]['cuenta_cobrar']['servicio']['abrev']))
										$col_5 = $helper->format_word($det['comprobante']['items'][0]['cuenta_cobrar']['servicio']['abrev']);
									else
										$col_5 = $helper->format_word($det['comprobante']['items'][0]['cuenta_cobrar']['servicio']['nomb']);*/
									//EXCEPCIONES DE RECIBOS DEFNITIVOS
									//if(isset($det['comprobante']['num']) && $det['comprobante']['num'] == 27305) $col_1 = "RD".' '.$det['comprobante']['serie'].'-'.$det['comprobante']['num'];
									//else if(isset($det['comprobante']['num']) && $det['comprobante']['num'] == 27306) $col_1 = "RD".' '.$det['comprobante']['serie'].'-'.$det['comprobante']['num'];
									if(isset($det['comprobante']['num'])) $col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['num'];
									else if(isset($det['comprobante']['numero'])) $col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['numero'];
									//if(!isset($det['comprobante']['cliente']['nomb']))	$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente_nomb'];

									if(isset($det['comprobante']['cliente']['tipo_enti'])){
										if($det['comprobante']['cliente']['tipo_enti']=='P'){
											$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['appat'].' '.$det['comprobante']['cliente']['apmat'].', '.$det['comprobante']['cliente']['nomb'];
											$col_2 = $det['comprobante']['cliente']['fullname'];
										}
										if($det['comprobante']['cliente']['tipo_enti']=='E'){
											$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['nomb'];
											$col_2 = $det['comprobante']['cliente']['fullname'];
										}

									}else if(isset($det['comprobante']['cliente_nomb'])){
										$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente_nomb'];
										$col_2 = $det['comprobante']['cliente']['fullname'];
									}
									#$col_2 = $det['comprobante']['cliente']['fullname'];
									//$col_5 = "";
									$col_6 = number_format($det['monto'],2);
								}else{
									$col_5 = $det['comprobante']['items'][0]['conceptos'][0]['concepto'];
									$col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['num'];
									$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['nomb'];
									if($det['comprobante']['cliente']['tipo_enti']=='P')
										$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['appat'].' '.$det['comprobante']['cliente']['apmat'].', '.$det['comprobante']['cliente']['nomb'];
									//if($det['comprobante']['cliente']['tipo_enti']=='E')
									//	$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['nomb'];
									$col_2 = $det['comprobante']['cliente']['fullname'];
									//$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
									if(isset($det['comprobante']['parcial'])){
										$col_4 = $helper->format_word($det['comprobante']['items'][0]['conceptos'][0]['concepto']);
									}else{
										$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
									}
									//$col_5 = "";
									$col_6 = number_format($det['monto'],2);
								}
								if(isset($det['comprobante']['parcial'])){
									//$col_4 = $det['comprobante']['items'][0]['conceptos'][0]['concepto'];
									$col_4=array();
									if(isset($det['comprobante']['inmueble']['direccion']))
										$col_4[0]=$det['comprobante']['inmueble']['direccion'];
									elseif(isset($det['comprobante']['items'][0]['contrato'])) {
										$contr = $f->model("in/cont")->params(array("_id"=>$det['comprobante']['items'][0]['contrato']))->get("one")->items;
									 	$col_4[0]=$contr['inmueble']['direccion'];
									 	unset($contr);
									}
									foreach ($det['comprobante']['sunat'] as $s => $array_sunat) {
										$col_4[$s+1]=$array_sunat["descr"];
									}
									//$col_4 = $det['comprobante']['sunat'][0]["descr"];
								}elseif(isset($det['comprobante']['inmueble']['_id'])){
									$col_4 = $det['comprobante']['inmueble']['direccion'];
								}elseif(isset($det['comprobante']['sunat'])){
									//$col_4 = $det['comprobante']['sunat'][0]["descr"];
									$col_4=array();
									if(isset($det['comprobante']['inmueble']['direccion'])) {
										$col_4[0]=$det['comprobante']['inmueble']['direccion'];
										$c_col4=1;
									}elseif(isset($det['comprobante']['items'][0]['contrato'])){
										$contr = $f->model("in/cont")->params(array("_id"=>$det['comprobante']['items'][0]['contrato']))->get("one")->items;
									 	$col_4[0]=$contr['inmueble']['direccion'];
									 	unset($contr);
										$c_col4=1;
									}
									else $c_col4=0;
									foreach ($det['comprobante']['sunat'] as $s => $array_sunat) {
										$col_4[$c_col4+$s]=$array_sunat["descr"];
									}
								}

								if(isset($det['detraccion'])){
									$col_6 = number_format($det['monto'],2);
								}else{
									/**/
								}
								if(isset($det['comprobante']['playa'])){
									if(is_string($det['comprobante']["cliente"])){
										$col_2 = $det['comprobante']["cliente"];
									}else{
										$col_2 = $det['comprobante']["cliente"]["nomb"];
										if($det['comprobante']["cliente"]["tipo_enti"]=="P"){
											$col_2.=$det['comprobante']["cliente"]["appat"]." ".$det['comprobante']["cliente"]["apmat"];
										}
									}
									$col_5 = "";
								}

								if(isset($det['comprobante']['moneda']) && $det['comprobante']['moneda']=='D'){
									$col_5.=" T.C. ".$det['comprobante']['tc'];
								}elseif(isset($det['comprobante']['moneda']) && $det['comprobante']['moneda']=='USD'){
									$col_5.=" T.C. ".$det['comprobante']['tipo_cambio'];
								}

								#COMPROBANTES ELECTRÓNICOS DE ALQUILERES Y ACTAS DE CONCILIACION
								if(isset($det['comprobante']['tipo_comprobante'])){
									if($det['comprobante']['tipo_comprobante']=="ELECTRONICO"){
										$pago_meses=0;
										$pago_parcial=0;
										foreach ($det['comprobante']['items'] as $i => $det_item) {
											if($det_item['tipo']=='pago_meses'){
												$pago_meses++;
											}
											else if($det_item['tipo']=='pago_parcial'){
												$pago_parcial++;
											}
										}
										
										foreach ($det['comprobante']['items'] as $i => $det_item) {
											if($det_item['tipo']=='pago_meses' || $det_item['tipo']=='pago_parcial'){
												if(isset($det_item['conceptos'])){
													foreach ($det_item['conceptos'] as $j => $item_conc) {
														
														if(sizeof($det['comprobante']['items'])==1 || ($pago_meses + $pago_parcial == 1) ){
															
															if(isset($det['comprobante']['items'])){
																{
																	/**
																	* PARA OBTENER INFORMACION EXACTA DE QUINCENA SE CONSULTA EL CONTRATO
																	*/

																	if(isset($det['comprobante']['items'][0]['conceptos'][0]['alquiler']['contrato'])){
																		$reci_cont = $f->model("in/cont")->params(array("_id"=>$det['comprobante']['items'][0]['conceptos'][0]['alquiler']['contrato']))->get("one")->items;

																		$dia_ini = date('d',$reci_cont['fecini']->sec);
																		$mes_ini = date('m',$reci_cont['fecini']->sec);
																		$ano_ini = date('Y',$reci_cont['fecini']->sec);
																		$dia_fin = intval(date('d',$reci_cont['fecfin']->sec));
																		$mes_fin = intval(date('m',$reci_cont['fecfin']->sec));
																		$ano_fin = intval(date('Y',$reci_cont['fecfin']->sec));
																		$fecha_fin = date('Y-m-d',$reci_cont['fecfin']->sec);
																		
																	}	
																	
																	if($dia_ini=='16'){
																		/**
																		*	EN CASO DE QUE EL PAGO SEA EN QUINCENA
																		*/
																		$mes = intval($det['comprobante']['items'][0]['conceptos'][0]['pago']['mes'])-1;
																		$ano = intval(substr($det['comprobante']['items'][0]['conceptos'][0]['pago']['ano'],2));
																		if($mes==-1){
																			$mes = 11;
																			$ano--;
																		}
																		$mes_2 = $mes;
																		if($mes==0){
																			$mes=12;
																			$mes_2=1;
																		}else{
																			$mes_2+=1;
																		}
																		$col_5 = '16/'.$mes.' al 15/'.($mes_2).'/'.$ano;
																		
																	}elseif($dia_ini=='15'){
																		/**
																		*	EN CASO DE QUE EL PAGO SEA EN QUINCENA (NOVEDAD)
																		*/
																		$mes = intval($det['comprobante']['items'][0]['conceptos'][0]['pago']['mes'])-1;
																		$ano = intval(substr($det['comprobante']['items'][0]['conceptos'][0]['pago']['ano'],2));
																		if($mes==-1){
																			$mes = 11;
																			$ano--;
																		}
																		$mes_2 = $mes;
																		if($mes==0){
																			$mes=12;
																			$mes_2=1;
																		}else{
																			$mes_2+=1;
																		}
																		$col_5 = '15/'.$mes.' al 14/'.($mes_2).'/'.$ano;
																		
																	}elseif($dia_ini=='1'){
																		/**
																		*	EN CASO DE QUE EL PAGO SEA EN INICIO DE MES, PUEDE QUE TERMINE EN FIN DE MES O NO
																		*/
																		$mes = intval($det['comprobante']['items'][0]['conceptos'][0]['pago']['mes'])-1;
																		$ano = intval(substr($det['comprobante']['items'][0]['conceptos'][0]['pago']['ano'],2));
																		if($mes==-1){
																			$mes = 11;
																			$ano--;
																		}
																		if($dia_fin != date("t", strtotime($fecha_fin)) && $ano == $ano_fin){
																			$col_5 = $meses[$mes].' \''.$ano.' al '.$dia_fin.' \''.$meses[$mes_fin-1].' \''.$ano_fin;
																		}else{
																			$col_5 = $meses[$mes].' \''.$ano;
																		}
																		
																	}else{
																		
																		if(isset($det['comprobante']['items'][0]['conceptos'][0]['pago'])){
																			/**
																				*	PAGO CUANDO EL DIA INICIAL NO ES 1 NI QUINCENA
																			*/
																			$mes = intval($det['comprobante']['items'][0]['conceptos'][0]['pago']['mes'])-1;
																			$ano = intval(substr($det['comprobante']['items'][0]['conceptos'][0]['pago']['ano'],2));
																			
																			if($mes==-1){
																				$mes = 11;
																				$ano--;
																			}
																			$dia = $dia_ini-1;
																			$mes_2 = $mes;
																			if($mes==0){
																				$mes=12;
																				$mes_2=1;
																			}else{
																				$mes_2+=1;

																			}
																			//$descr = $meses[$mes].' \''.$ano;
																			//$descr = $dia_ini.' '.$meses[$mes].' al '.$dia.' '.($meses[$mes_2]).' \''.$ano;
																			
																			$col_5 = $dia_ini.' '.$meses[$mes-1].' al '.$dia.' '.$meses[$mes_2-1];

																			//	SI EL CONTRATO ES POR DIAS DENTRO DEL MISMO MES
																			if($mes_ini == $mes_fin && $ano_ini == $ano_fin){
																				$mes_ini = intval($mes_ini);
																				$cant_dias = date('t', strtotime($ano_fin . '-' . $mes_fin . '-01'));
																				$col_5 = $dia_ini.' '.$meses[$mes_ini-1].' al '.$cant_dias.' '.$meses[$mes_fin-1];
																			}
																		}else{
																			
																			$col_5 = $meses[$mes].' \''.$ano;
																		}

																		
																		
																	}
																																		


																}
																//$col_5 = $descr;
															}
														}else{
															$tmp_size = sizeof($det['comprobante']['items']);
															//$mes_ini = intval($det['comprobante']['items'][$i]['conceptos'][0]['pago']['mes'])-1;
															//$ano_ini = intval(substr($det['comprobante']['items'][$i]['conceptos'][0]['pago']['ano'],2));
															$mes_ini = intval($det['comprobante']['items'][0]['conceptos'][0]['pago']['mes'])-1;
															$ano_ini = intval(substr($det['comprobante']['items'][0]['conceptos'][0]['pago']['ano'],2));


															if($mes_ini==-1){
																$mes_ini = 11;
																$ano_ini--;

															}
															if(isset($det['comprobante']['items'][0]['conceptos'][0]['alquiler']['contrato'])){
																$reci_cont = $f->model("in/cont")->params(array("_id"=>$det['comprobante']['items'][0]['conceptos'][0]['alquiler']['contrato']))->get("one")->items;

																$dia_ini = date('d',$reci_cont['fecini']->sec);
																$dia_fin = date('d',$reci_cont['fecfin']->sec);
															}
															if($dia_ini=="16" || $dia_ini=="15"){
																$mes_ini--;
															}
															if($tmp_size>2)
															{
																$mes_fin = intval($det['comprobante']['items'][$tmp_size-2]['conceptos'][0]['pago']['mes']);
																$ano_fin = intval(substr($det['comprobante']['items'][$tmp_size-2]['conceptos'][0]['pago']['ano'],2));
															}
															else{
																$mes_fin = intval($det['comprobante']['items'][$tmp_size-1]['conceptos'][0]['pago']['mes'])-1;
																$ano_fin = intval(substr($det['comprobante']['items'][$tmp_size-1]['conceptos'][0]['pago']['ano'],2));
															}

															//$mes_fin = intval($det['comprobante']['items'][$tmp_size-1]['conceptos'][0]['pago']['mes'])-1;
															//$ano_fin = intval(substr($det['comprobante']['items'][$tmp_size-1]['conceptos'][0]['pago']['ano'],2));


															if($mes_fin==-1){
																$mes_fin = 11;
																$ano_fin--;
															}

															if($dia_ini=="16" || $dia_ini=="15"){
																$mes_fin--;
															}

															$cada = ' cm S/.';
															foreach ($det['comprobante']['items'][0]['conceptos'] as $ik=>$conc_tmp) {
																if( $conc_tmp['cuenta']['cod']!='1202.0901.47' &&
																	$conc_tmp['cuenta']['cod']!='2101.010503.47' &&
																	$conc_tmp['cuenta']['cod']!='2101.010501' &&
																	$conc_tmp['cuenta']['cod']!='1202.0902' //SANCIONES
																){
																	$cada .= $conc_tmp['monto'].' ';
																}
															}
															if($ano_ini==$ano_fin){
																//SI NO ES QUINCENA NI PRIMERO
																$col_5 = $meses[$mes_ini].' a '.$meses[$mes_fin].' \''.$ano_ini.$cada;
																$fin_ano = date('y',$reci_cont['fecfin']->sec);
																$ini_ano = date('y',$reci_cont['fecini']->sec);

																//SI EL AÑO DE PAGO Y EL AÑO DE CONTRATO SON DIFERENTES
																if($ini_ano != $fin_ano){
																	if($mes_fin > 11){
																		$mes_fin = 0;
																		$ano_fin++;
																		$col_5 = $meses[$mes_ini].' \''.$ano_ini.' a '.$meses[$mes_fin].' \''.$ano_fin.$cada;
																	}
																	if($dia_ini>15){
																		$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin+1).$cada;
																	}
																	
																}

																	/*$fin_mes = date('m',$reci_cont['fecfin']->sec);
																if($fin_mes == '02' && $dia_ini == 1){
																	$cant_dias = date('t', strtotime($ano_ini . '-' . $mes_fin . '-01'));
																	$mes_fin++;
																	if($mes_fin>12){
																		$mes_fin = '01';
																	}

																	$col_5 = $dia_ini.'/'.($mes_ini+1).$ano_ini.' a '.$cant_dias.'/'.($mes_fin).$ano_fin.$cada;	
																}*/
																if($dia_fin>1 && $dia_fin<15){
																	$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin+1).$cada;	
																}
																if($dia_ini>18 && $dia_fin < 19){
																	$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).$cada;
																}
																
																if($det_item['tipo']=='pago_meses'){
																	$itemsArray=$det['comprobante']['items'];
																	$firtsItem=reset($itemsArray);
																	$lastItem=end($itemsArray);
																	$mes_ini=$firtsItem['conceptos'][0]['pago']['mes'];
																	$mes_fin=$lastItem['conceptos'][0]['pago']['mes'];
																	$ano_fin=$lastItem['conceptos'][0]['pago']['ano'];
																	
																	if($dia_ini==15)
																		$mes_ini--;
																	
																	$dia_ini = date('d',$reci_cont['fecini']->sec);	
																	$dia_fin = date('d',$reci_cont['fecfin']->sec);
																	$año_ini = date('y',$reci_cont['fecini']->sec);
																	//FECHA CON DIA DE COMPROBANTE
																	$cant_dias = date('t', strtotime($ano_fin . '-' . $mes_fin . '-01'));
																	$new_fin = substr($ano_fin,2);


																	if($cant_dias > $dia_fin){
																			//PAGO DICIEMBRE - ENERO
																			if( $mes_ini == 1 && $año_ini != $new_fin){
																				$mes_ini--;
																				if($mes_ini<1){
																					$mes_ini = '12';
																				}
																				$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).$cada;	
																			}
																			/*------------------------------ */
																			/*MES FEBRERO*/
																			if($mes_fin == 2 && $dia_fin > 28){
																				$dia_fin = date('t', strtotime($ano_fin . '-' . $mes_fin . '-01'));
																				$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).$cada;
																			}
																			//EN CASO QUE SEA QUINCENA
																			if($dia_ini>14 && $dia_fin < 15){
																				$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).' '.$cada;
																			}
																			
																			/*------------------------------ */
																			$dia_fin_ = date('t', strtotime($ano_fin . '-' . $mes_fin . '-01'));

																			
																	}else{
																		//PARA LOS CASOS QUE EL MES ES DICIEMBRE Y EL DIA ES DIFERENTE DE 1 O QUINCENA
																		if($mes_ini == 1 && intval($dia_fin) > intval($dia_ini) && intval($dia_ini) > 1 && intval($dia_ini) < 29){
																			$dia_fin = intval($dia_ini) - 1;
																			$mes_ini--;
																			if($mes_ini <1){
																				$mes_ini = 12;
																				
																			}
																			$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).$cada;
																		}
																		if($cant_dias>$dia_fin){
																			$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).$cada;	
																		}else{
																			$col_5 = $dia_ini.'/'.($mes_ini).' a '.$cant_dias.'/'.($mes_fin).$cada;
																		}
																		
																	}
																	
																}
																if($det_item['tipo']=='pago_parcial'){
																	$col_5 = 'Canc '.$meses[$mes_ini].' a cta '.$meses[$mes_fin].' \''.$ano_ini.$cada;
																}
																if($dia_ini=='16'){
																	if($dia_ini=="16" || $dia_ini=="15"){
																		$mes_ini--;
																		$mes_fin--;
																		
																	}
																	$col_5 = '16 '.$meses[$mes_ini-1].' al 15 '.($meses[$mes_fin]).' '.$ano_ini.$cada;
																}
																//NUEVO CASO - DIA 13
																if($dia_ini=='13'){
																	$mes_ini--;
																	$dia_fin--;
																	$col_5 = $dia_ini.'/'.($mes_ini).' a '.$dia_fin.'/'.($mes_fin).$cada;
																}
																
																
															}else{
																//$col_5 = $meses[$mes_ini].' \''.$ano_ini.' y '.$meses[$mes_fin].' \''.$ano_fin.$cada; //COMENT JOVAD
																//$col_5 = $meses[$mes_ini-1].'\''.$ano_ini.' y '.$meses[$mes_fin-1].'\''.$ano_fin.$cada;
																$col_5 = $dia_ini.'/'.$mes_ini.'/'.$ano_ini.' - '.$dia_fin.'/'.$mes_fin.'/'.$ano_fin.$cada;
																if($det_item['tipo']=='pago_meses'){
																	$itemsArray=$det['comprobante']['items'];
																	$firtsItem=reset($itemsArray);
																	$lastItem=end($itemsArray);
																	$mes_ini=$firtsItem['conceptos'][0]['pago']['mes'];
																	$mes_fin=$lastItem['conceptos'][0]['pago']['mes'];
																	if($dia_ini==15)
																		$mes_ini--;
																	$dia_ini = date('d',$reci_cont['fecini']->sec);
																	$dia_fin = date('d',$reci_cont['fecfin']->sec);
																	if($dia_ini=='13'){
																		$mes_ini--;
																		$col_5 = $dia_ini.'/'.($mes_ini).'/'.$ano_ini.' a '.$dia_fin.'/'.($mes_fin).'/'.$ano_fin.$cada;	
																	}
																	$col_5 = $dia_ini.'/'.($mes_ini).'/'.$ano_ini.' a '.$dia_fin.'/'.($mes_fin).'/'.$ano_fin.$cada;
																}
																$nro_meses= 13 - $mes_fin + 12*($ano_fin - $ano_ini - 1) + $mes_ini;
																if ($nro_meses === count($det['comprobante']['items']) ) {
																	$col_5 = $meses[$mes_ini].' \''.$ano_ini.' a '.$meses[$mes_fin].' \''.$ano_fin.$cada;
																}
																if($dia_ini=='16'){
																	$col_5 = '16\''.$meses[$mes_ini].' \''.$ano_ini.' al 15\''.$meses[$mes_fin+1].' \''.$ano_fin.$cada;
																}
															}
														}
														$col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['numero'];
														if (isset($det['comprobante']['cliente_nomb'])) $det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente_nomb'];
														if (isset($det['comprobante']['cliente']['tipo_enti'])) {
															if($det['comprobante']['cliente']['tipo_enti']=='P'){
																$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente']['appat'].' '.$det['comprobante']['cliente']['apmat'].', '.$det['comprobante']['cliente']['nomb'];
																$col_2 = $det['comprobante']['cliente']['fullname'];
															}
														}


														//$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
														if(isset($det['comprobante']['parcial'])){
															$col_4 = $helper->format_word($det['comprobante']['items'][0]['conceptos'][0]['concepto']);
														}else{
															$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
														}
														//$col_5 = "";
														$col_6 = number_format($det['monto'],2);
													}
												}
											}else if($det_item['tipo']=='pago_acta'){
												if(isset($det_item['conceptos'])){
													foreach ($det_item['conceptos'] as $j => $item_conc) {
														if(sizeof($det['comprobante']['items'])==1){
															if(isset($det['comprobante']['items'])){
																$col_5 = "";
																if(count($det_item['conceptos'])>0){
																	for($ii=0;$ii<count($det_item['conceptos']);$ii++){
																		if( $det_item['conceptos'][$ii]['cuenta']['cod']!='2101.010503.47' &&
																			$det_item['conceptos'][$ii]['cuenta']['cod']!='2101.010501' &&
																			$det_item['conceptos'][$ii]['cuenta']['cod']!='1202.0902'
																		){
																			$col_5.=$helper->format_word($det['comprobante']['items'][0]['conceptos'][$ii]['descr'])." ";
																		}
																	}
																}
																$col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['numero'];
																$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente_nomb'];
																$col_2 = $det['comprobante']['cliente']['fullname'];
																if(isset($det['comprobante']['parcial'])){
																	$col_4 = $helper->format_word($det['comprobante']['items'][0]['conceptos'][0]['concepto']);
																}else{
																	$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
																}
																//$col_5 = "";
																$col_6 = number_format($det['monto'],2);
															}
														}else{
															$col_5 = "";
															if(count($det_item['conceptos'])>0){
																for($ii=0;$ii<count($det_item['conceptos']);$ii++){
																	if( $det_item['conceptos'][$ii]['cuenta']['cod']!='2101.010503.47' &&
																		$det_item['conceptos'][$ii]['cuenta']['cod']!='2101.010501' &&
																		$det_item['conceptos'][$ii]['cuenta']['cod']!='2101.010503.47' &&
																		$det_item['conceptos'][$ii]['cuenta']['cod']!='1202.0902'
																	){
																		$col_5.=$helper->format_word($det['comprobante']['items'][0]['conceptos'][$ii]['descr'])." ";
																	}
																}
															}
															$col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['numero'];
															$det['comprobante']['cliente']['fullname'] = $det['comprobante']['cliente_nomb'];
															$col_2 = $det['comprobante']['cliente']['fullname'];
															if(isset($det['comprobante']['parcial'])){
																$col_4 = $helper->format_word($det['comprobante']['items'][0]['conceptos'][0]['concepto']);
															}else{
																$col_4 = $helper->format_word($det['comprobante']['inmueble']['abrev']);
															}
															$col_6 = number_format($det['monto'],2);

														}
													}
												}
											}else{
												if(($det['comprobante']['serie'] == 'B001'  || $det['comprobante']['serie'] == 'F001') && $det_item['tipo']=='cuenta_cobrar' && $det['comprobante']['tipo'] != 'NC'){
													if(isset($det_item['conceptos'])){
														foreach ($det_item['conceptos'] as $j => $item_conc) {
															if(sizeof($det['comprobante']['items'])==1){

																$cuco = $f->model("cj/cuen")->params(array("_id"=>$item_conc['cuenta_cobrar']['_id']))->get("one")->items;
																$coin = $f->model("in/inmu")->params(array("_id"=>$cuco['inmueble']['_id']))->get("one")->items;
																$col_4 = $helper->format_word($coin['abrev']);

															}else{

																$cuco = $f->model("cj/cuen")->params(array("_id"=>$item_conc['cuenta_cobrar']['_id']))->get("one")->items;
																$coin = $f->model("in/inmu")->params(array("_id"=>$cuco['inmueble']['_id']))->get("one")->items;
																$col_4 = $helper->format_word($coin['abrev']);
															}
														}
													}else{
														$tmp_size = sizeof($det['comprobante']['items']);
														$mes_ini = intval($det['comprobante']['items'][0]['pago']['mes'])-1;
														$ano_ini = intval(substr($det['comprobante']['items'][0]['pago']['ano'],2));
														if($mes_ini==-1){
															$mes_ini = 11;
															$ano_ini--;
														}
														$dia_ini = $det['comprobante']['items'][0]['dia_ini'];
														if($dia_ini=="16" || $dia_ini=="15"){
															$mes_ini--;
														}
														$mes_fin = intval($det['comprobante']['items'][$tmp_size-1]['pago']['mes'])-1;
														$ano_fin = intval(substr($det['comprobante']['items'][$tmp_size-1]['pago']['ano'],2));
														if($mes_fin==-1){
															$mes_fin = 11;
															$ano_fin--;
														}
														if($dia_ini=="16" || $ºdia_ini=="15"){
															$mes_fin--;
														}
														$cada = ' cm S/.';

														foreach ($det['comprobante']['items'][0]['conceptos'] as $ik=>$conc_tmp) {
															if($conc_tmp['cuenta']['cod']!='1202.0901.47' && $conc_tmp['cuenta']['cod']!='2101.010503.47' && $conc_tmp['cuenta']['cod']!='2101.010501'){
																$cada .= $conc_tmp['monto'];
															}
														}
														if($ano_ini==$ano_fin){
															$col_5 = $meses[$mes_ini].' a '.$meses[$mes_fin].' \''.$ano_ini.$cada;

														}else{
															$col_5 = $meses[$mes_ini].' \''.$ano_ini.' a '.$meses[$mes_fin].' \''.$ano_fin.$cada;
														}
													}

												}

											}
										}
										//print_r($det['comprobante']['items'][0]['conceptos'][0]['cuenta_cobrar']);
										if(isset($det['comprobante']['items'][0]['conceptos'][0]['cuenta_cobrar'])){
											$col_5 = $meses[$mes].' \''.$ano;
										}
									}

								}


								$_item = array(
									'col_1'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>9,
											'align'=>'R',
											'w'=>25
										),
										'value'=>$col_1
									),
									'col_2'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>7,
											'align'=>'L',
											'w'=>60
										),
										'value'=>$helper->format_word($col_2)
									),
									'col_3'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>8,
											'align'=>'L',
											'w'=>25
										),
										'value'=>$col_3
									),
									'col_4'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>6,
											'align'=>'L',
											'w'=>57
										),
										'value'=>($col_4),
										//'value'=>$helper->format_word($col_4)
									),
									'col_5'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>7,
											'align'=>'L',
											'w'=>25
										),
										'value'=>$helper->format_word($col_5)
									),
									'col_6'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>9,
											'align'=>'R',
											'w'=>25
										),
										'value'=>$col_6
									),
									'col_7'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>9,
											'align'=>'R',
											'w'=>25
										),
										'value'=>$col_7
									),
									'col_8'=>array(
										'opt'=>array(
											'type'=>'',
											'size'=>9,
											'align'=>'R',
											'w'=>25
										),
										'value'=>$col_8
									),
									'partial'=>true,
									'partial_monto'=>$det['monto'],
									'hidden'=>false
								);
								if($det['cuenta']['cod']=='2101.010503.47') $_item['hidden'] = true;
								if($det['cuenta']['cod']=='2101.0105') $_item['hidden'] = true;
								if($det['cuenta']['cod']=='1202.0901.47') $_item['hidden'] = true;
								if($det['cuenta']['cod']=='2101.010501') $_item['hidden'] = true;
								if($det['cuenta']['cod']=='1202.0902') $_item['hidden'] = true;
								if(isset($det['detraccion'])){
									$_item['hidden'] = true;
								}
								array_push($recibo['detalle2'], $_item);
							}
							break;
						case "MH":
							$mh_items = array();
							$categorias = array(
								"2"=>"PP",
								"3"=>"P",
								"4"=>"A",
								"5"=>"B",
								"6"=>"C",
								"7"=>"E",
								"8"=>"Indigente",
								"9"=>"Privado",
								"10"=>"Nuevo",
								"11"=>"Continuador",
								"12"=>"Categoría B",
								"13"=>"Categoría C",
							);
							$modalidad = array(
								'M'=>'mensual',
								'D'=>'diario'
							);
							$modalidad_tipo = array(
								'M'=>'mes(es)',
								'D'=>'dia(s)'
							);
							$tipo_hosp = array(
								'C'=>'completa',
								'P'=>'parcial'
							);
							foreach($comp as $det){
								//print_r($det);
								if(isset($det['comprobante']['hospitalizacion'])){
									$fecini = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecini']->sec);
									$fecfin = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecfin']->sec);
									$col_1 = '';
									$col_2 = 'Hosp. '.$tipo_hosp[$det['comprobante']['hospitalizacion']['tipo_hosp']].' x'.$det['comprobante']['hospitalizacion']['cant'].' '.$modalidad_tipo[$det['comprobante']['hospitalizacion']['modalidad']].' del '.$fecini.' al '.$fecfin.' Cat. '.$categorias[$det['comprobante']['hospitalizacion']['categoria']].' con Rec. '.$det['comprobante']['num'];
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($det['monto'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';
									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}else{
									if(!isset($mh_items[$det['concepto']])){
										$mh_items[$det['concepto']] = array(
											'concepto'=>$det['concepto'],
											'comprobantes'=>array(),
											'cantidad'=>0,
											'importe'=>0
										);
									}
									if(count($mh_items[$det['concepto']]['comprobantes'])>0){
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}else{
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}

									$mh_items[$det['concepto']]['cantidad']++;
									$mh_items[$det['concepto']]['importe']+=$det['monto'];
									/*if($det['comprobante']['num']==51103){
										print_r($det);
									}*/
									//echo $det['monto']."<br />";
								}
							}
							if(count($mh_items)>0){
								foreach($mh_items as $_mh_items){
									$col_1 = '';
									$col_2 = $_mh_items['cantidad'].' '.$_mh_items['concepto'].' Rec: '.implode(', ', $_mh_items['comprobantes']);
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($_mh_items['importe'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';

									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'R',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}
							}
							break;
						case "AD":
							$mh_items = array();
							$categorias = array(
								"2"=>"PP",
								"3"=>"P",
								"4"=>"A",
								"5"=>"B",
								"6"=>"C",
								"7"=>"E",
								"8"=>"Indigente",
								"10"=>"Nuevo",
								"11"=>"Continuador",
								"12"=>"Categoría B",
								"13"=>"Categoría C",
							);
							$modalidad = array(
								'M'=>'mensual',
								'D'=>'diario'
							);
							$modalidad_tipo = array(
								'M'=>'mes(es)',
								'D'=>'dia(s)'
							);
							$tipo_hosp = array(
								'C'=>'completa',
								'P'=>'parcial'
							);
							foreach($comp as $det){
								//print_r($det);
								if(isset($det['comprobante']['hospitalizacion'])){
									$fecini = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecini']->sec);
									$fecfin = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecfin']->sec);
									$col_1 = '';
									$col_2 = 'Hosp. '.$tipo_hosp[$det['comprobante']['hospitalizacion']['tipo_hosp']].' x'.$det['comprobante']['hospitalizacion']['cant'].' '.$modalidad_tipo[$det['comprobante']['hospitalizacion']['modalidad']].' del '.$fecini.' al '.$fecfin.' Cat. '.$categorias[$det['comprobante']['hospitalizacion']['categoria']].' con Rec. '.$det['comprobante']['num'];
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($det['monto'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';
									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}else{
									if(!isset($mh_items[$det['concepto']])){
										$mh_items[$det['concepto']] = array(
											'concepto'=>$det['concepto'],
											'comprobantes'=>array(),
											'cantidad'=>0,
											'importe'=>0
										);
									}
									if(count($mh_items[$det['concepto']]['comprobantes'])>0){
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}else{
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}

									$mh_items[$det['concepto']]['cantidad']++;
									$mh_items[$det['concepto']]['importe']+=$det['monto'];
									/*if($det['comprobante']['num']==51103){
										print_r($det);
									}*/
									//echo $det['monto']."<br />";
								}
							}
							if(count($mh_items)>0){
								foreach($mh_items as $_mh_items){
									$col_1 = '';
									$col_2 = $_mh_items['cantidad'].' '.$_mh_items['concepto'].' Rec: '.implode(', ', $_mh_items['comprobantes']);
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($_mh_items['importe'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';

									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'R',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}
							}
							break;
							case "TD":
							$mh_items = array();
							$categorias = array(
								"2"=>"PP",
								"3"=>"P",
								"4"=>"A",
								"5"=>"B",
								"6"=>"C",
								"7"=>"E",
								"8"=>"Indigente",
								"10"=>"Nuevo",
								"11"=>"Continuador"
							);
							$modalidad = array(
								'M'=>'mensual',
								'D'=>'diario'
							);
							$modalidad_tipo = array(
								'M'=>'mes(es)',
								'D'=>'dia(s)'
							);
							$tipo_hosp = array(
								'C'=>'completa',
								'P'=>'parcial'
							);
							foreach($comp as $det){
								//print_r($det);
								if(isset($det['comprobante']['hospitalizacion'])){
									$fecini = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecini']->sec);
									$fecfin = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecfin']->sec);
									$col_1 = '';
									$col_2 = 'Hosp. '.$tipo_hosp[$det['comprobante']['hospitalizacion']['tipo_hosp']].' x'.$det['comprobante']['hospitalizacion']['cant'].' '.$modalidad_tipo[$det['comprobante']['hospitalizacion']['modalidad']].' del '.$fecini.' al '.$fecfin.' Cat. '.$categorias[$det['comprobante']['hospitalizacion']['categoria']].' con Rec. '.$det['comprobante']['num'];
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($det['monto'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';
									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}else{
									if(!isset($mh_items[$det['concepto']])){
										$mh_items[$det['concepto']] = array(
											'concepto'=>$det['concepto'],
											'comprobantes'=>array(),
											'cantidad'=>0,
											'importe'=>0
										);
									}
									if(count($mh_items[$det['concepto']]['comprobantes'])>0){
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}else{
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}

									$mh_items[$det['concepto']]['cantidad']++;
									$mh_items[$det['concepto']]['importe']+=$det['monto'];
									/*if($det['comprobante']['num']==51103){
										print_r($det);
									}*/
									//echo $det['monto']."<br />";
								}
							}
							if(count($mh_items)>0){
								foreach($mh_items as $_mh_items){
									$col_1 = '';
									$col_2 = $_mh_items['cantidad'].' '.$_mh_items['concepto'].' Rec: '.implode(', ', $_mh_items['comprobantes']);
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($_mh_items['importe'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';

									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'R',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}
							}
							break;
						case "LM":
							$mh_items = array();
							$categorias = array(
								"8"=>"Indigente",
								"10"=>"Nuevo",
								"11"=>"Continuador",
								""=>"Particular"
							);
							$modalidad = array(
								'M'=>'mensual',
								'D'=>'diario'
							);
							$modalidad_tipo = array(
								'M'=>'mes(es)',
								'D'=>'dia(s)'
							);
							$tipo_hosp = array(
								'C'=>'completa',
								'P'=>'parcial'
							);
							foreach($comp as $det){
								//print_r($det);
								if(isset($det['comprobante']['hospitalizacion'])){
									$fecini = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecini']->sec);
									$fecfin = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecfin']->sec);
									$col_1 = '';
									$col_2 = 'Hosp. '.$tipo_hosp[$det['comprobante']['hospitalizacion']['tipo_hosp']].' x'.$det['comprobante']['hospitalizacion']['cant'].' '.$modalidad_tipo[$det['comprobante']['hospitalizacion']['modalidad']].' del '.$fecini.' al '.$fecfin.' Cat. '.$categorias[$det['comprobante']['hospitalizacion']['categoria']].' con Rec. '.$det['comprobante']['num'];
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($det['monto'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';
									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}else{
									if(!isset($mh_items[$det['concepto']])){
										$mh_items[$det['concepto']] = array(
											'concepto'=>$det['concepto'],
											'comprobantes'=>array(),
											'cantidad'=>0,
											'importe'=>0
										);
									}
									if(count($mh_items[$det['concepto']]['comprobantes'])>0){
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}else{
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['num'];
									}

									$mh_items[$det['concepto']]['cantidad']++;
									$mh_items[$det['concepto']]['importe']+=$det['monto'];
									/*if($det['comprobante']['num']==51103){
										print_r($det);
									}*/
									//echo $det['monto']."<br />";
								}
							}
							if(count($mh_items)>0){
								foreach($mh_items as $_mh_items){
									$col_1 = '';
									$col_2 = $_mh_items['cantidad'].' '.$_mh_items['concepto'].' Rec: '.implode(', ', $_mh_items['comprobantes']);
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($_mh_items['importe'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';

									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'R',
												'w'=>20
											),
											'value'=>$col_8
										)
									);

									array_push($recibo['detalle2'], $_item);
								}
							}
							break;
						case "FA":
							$mh_items = array();
							$categorias = array(
								"2"=>"PP",
								"3"=>"P",
								"4"=>"A",
								"5"=>"B",
								"6"=>"C",
								"7"=>"E",
								"8"=>"Indigente",
								"10"=>"Nuevo",
								"11"=>"Continuador"
							);
							$modalidad = array(
								'M'=>'mensual',
								'D'=>'diario'
							);
							$modalidad_tipo = array(
								'M'=>'mes(es)',
								'D'=>'dia(s)'
							);
							$tipo_hosp = array(
								'C'=>'completa',
								'P'=>'parcial'
							);
							foreach($comp as $det){
								//print_r($det);
								if(isset($det['comprobante']['hospitalizacion'])){
									$fecini = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecini']->sec);
									$fecfin = date('d/m/Y', $det['comprobante']['hospitalizacion']['fecfin']->sec);
									$col_1 = '';
									$col_2 = 'Hosp. '.$tipo_hosp[$det['comprobante']['hospitalizacion']['tipo_hosp']].' x'.$det['comprobante']['hospitalizacion']['cant'].' '.$modalidad_tipo[$det['comprobante']['hospitalizacion']['modalidad']].' del '.$fecini.' al '.$fecfin.' Cat. '.$categorias[$det['comprobante']['hospitalizacion']['categoria']].' con Rec. '.$det['comprobante']['numero'];
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($det['monto'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';
									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}else{
									if(!isset($mh_items[$det['concepto']])){
										$mh_items[$det['concepto']] = array(
											'concepto'=>$det['concepto'],
											'comprobantes'=>array(),
											'cantidad'=>0,
											'importe'=>0
										);
									}

									if(count($mh_items[$det['concepto']]['comprobantes'])>0){
										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['numero'];
									}else{

										$mh_items[$det['concepto']]['comprobantes'][] = $det['comprobante']['numero'];
									}
									//print_r($det['comprobante']['numero']);
									$mh_items[$det['concepto']]['cantidad']++;
									$mh_items[$det['concepto']]['importe']+=$det['monto'];
									/*if($det['comprobante']['num']==51103){
										print_r($det);
									}*/
									//echo $det['monto']."<br />";
								}
							}
							if(count($mh_items)>0){
								foreach($mh_items as $_mh_items){
									$col_1 = '';
									//print_r($_mh_items);
									$col_2 = $_mh_items['cantidad'].' '.$_mh_items['concepto'].' Rec: '.implode(', ', $_mh_items['comprobantes']);
									//print_r($col_2);
									$col_3 = '';
									$col_4 = '';
									$col_5 = number_format($_mh_items['importe'],2);
									$col_6 = '';
									$col_7 = '';
									$col_8 = '';
									$_item = array(
										'col_1'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_1
										),
										'col_2'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'L',
												'w'=>110
											),
											'value'=>$col_2
										),
										'col_3'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_3
										),
										'col_4'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_4
										),
										'col_5'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>8,
												'align'=>'R',
												'w'=>25
											),
											'value'=>$col_5
										),
										'col_6'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_6
										),
										'col_7'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'L',
												'w'=>20
											),
											'value'=>$col_7
										),
										'col_8'=>array(
											'opt'=>array(
												'type'=>'',
												'size'=>11,
												'align'=>'R',
												'w'=>20
											),
											'value'=>$col_8
										)
									);
									array_push($recibo['detalle2'], $_item);
								}
							}
							break;
					}
				}
			}
		}
		unset($recibo['detalle']);
		$ctasp = array();
		$ctast = array();
		foreach ($recibo['cont_patrimonial'] as $k => $row) {
		    $ctasp[$k] = substr($row['cuenta']['cod'],0,9);//$row['cuenta']['cod'];
		    $ctast[$k] = $row['tipo'];
		}
		array_multisort($ctast,SORT_ASC,$ctasp,SORT_DESC,$recibo['cont_patrimonial']);
		
		if(isset($f->request->data['debug_recibo'])){
			echo "<pre>";
			print_r($recibo);
			echo "</pre>";
			die();
		}
		if(isset($f->request->data['debug'])){
			die();
		}
		$f->response->view("cj/repo.rein2.print",array('recibo'=>$recibo));

	}
	function execute_startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
	function execute_edit_comp(){
		global $f;
		$f->response->view("in/comp.edit");
	}
	function execute_edit(){
		global $f;
		$f->response->view("cj/comp.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("cj/comp.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("cj/comp.details");
	}
	function execute_anul(){
		global $f;
		$f->response->view("cj/comp.anular");
	}
	function execute_gen(){
		global $f;
		$f->response->view("cj/comp.gen");
	}
	function execute_cambiar(){
		global $f;
		$f->response->view("cj/comp.cambiar");
	}
	function execute_confirmar(){
		global $f;
		$f->response->view("cj/comp.confirmar");
	}
	function execute_voucher(){
		global $f;
		$f->response->view("cj/comp.voucher");
	}
	function execute_iframe(){
		global $f;
		$f->response->view("in/comp.iframe");
	}
	function execute_iframe_playa(){
		global $f;
		$f->response->view("in/comp.iframe.playa");
	}
	function execute_moras_a_saciones(){
		/*CONVIERTE LOS COMPROBANTES DE MORAS A SANCIONES DADO A QUE NUNCA FUERON INFORMADAS A LA SUNAT*/
		global $f;
		$serie=array('$in' => array('B001','F001'));
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime(date('2018-06-13'))),
				'$lte'=>new MongoDate(strtotime(date('2018-07-14')))
			),
			'serie' => $serie,
			'items.conceptos.cuenta._id' => new MongoId("536bbcecee6f96e4050000b9"),
		);
		$ecoms = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>array(
				"_id" => true,
				"items.conceptos.cuenta" => true,
			),
			'sort'=>array('numero'=>1),
		))->get("all")->items;
		$update=[];
		foreach ($ecoms as $e => $ecom) {
            foreach ($ecom['items'] as $i => $item) {
            	foreach ($item['conceptos'] as $c => $concepto) {
            		if(isset($concepto['cuenta'])){
            			if($concepto['cuenta']['_id']->{'$id'}=="536bbcecee6f96e4050000b9"){
            				$concepto['mod_cuenta'] = $concepto['cuenta'];
            				$concepto['cuenta'] = array(
            					'_id' => new MongoId("51a901bc4d4a13540a0000c5"),
            					'descr' => "Sanciones",
            					'cod' => "1202.0902",
            				);
            				$ecoms[$e]["items"][$i]["conceptos"][$c] = $concepto;
            				$update[$e]=array(
            					'items.'.$i.'.conceptos.'.$c.'.cuenta'=> $concepto['cuenta'],
            					'items.'.$i.'.conceptos.'.$c.'.mod_cuenta'=> $concepto['mod_cuenta'],
            				);
            				$f->model("cj/ecom")->params(array('_id'=> $ecom['_id'],'data'=>$update[$e]))->save("update");
            			}
            		}
            	}
            }
        }

		header("Content-type:application/json");
    	echo json_encode($update);
    	die();
	}
}
?>
