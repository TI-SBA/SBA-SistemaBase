<?php
class Controller_fa_comp extends Controller {
/*	function execute_lista_legacy(){
		global $f;
		$params = array(
			"modulo"=>'FA'
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
		if(isset($f->request->data['sort'])){
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
			if($f->request->data['sort']=='serie'){
				$params['sort'] = array(
					'serie'=>floatval($f->request->data['sort_i']),
					'num'=>1
				);
			}elseif($f->request->data['sort']=='num'){
				$params['sort'] = array(
					'serie'=>floatval($f->request->data['sort_i']),
					'num'=>floatval($f->request->data['sort_i'])
				);
			}
		}else{
			$params['sort'] = array('serie'=>-1,'num'=>-1,'fecreg'=>-1);
		}
		$f->response->json( $f->model("cj/comp")->params($params)->get("search") );
	}
*/
/*	function execute_save_rein(){
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
*/
/*	function execute_save_comp(){
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
			if(isset($item['producto']))
				$items[$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
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
			'modulo'=>'FA',
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
		if($data['moneda']=='D'){
			$comp['total_soles'] = floatval($data['total_soles']);
		}
		if(isset($vouchers)){
			$comp['vouchers'] = $vouchers;
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
		$verify = $f->model('cj/comp')->params(array(
			'tipo'=>$comp['tipo'],
			'serie'=>$comp['serie'],
			'num'=>$comp['num']
		))->get('verify')->items;
		if($verify!=null){
			return $f->response->json(array('error'=>9));
		}
		$compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
		$f->model('cj/talo')->params(array(
			'tipo'=>$data['tipo'],
			'serie'=>$data['serie'],
			'num'=>floatval($data['num']),
			'caja'=>$caja['_id']
		))->save('num');
		foreach ($items as $i=>$item){
			if(isset($item['cuenta_cobrar'])){
				$total = 0;
				foreach ($item['conceptos'] as $w=>$conc){
					$upd['conceptos.'.$w.'.saldo'] = -floatval($conc['monto']);
					$total = $total + (float)$conc['monto'];
				}
				$upd['saldo'] = -floatval($total);
				$f->model('cj/cuen')->params(array(
					'_id'=>$item['cuenta_cobrar']['_id'],
					'data'=>array('$inc'=>$upd)
				))->save('custom');
				$f->model('cj/cuen')->params(array(
					'_id'=>$item['cuenta_cobrar']['_id'],
					'data'=>array('$push'=>array('comprobantes'=>$compro['_id']))
				))->save('custom');
				$cuenta = $f->model('cj/cuen')->params(array('_id'=>$item['cuenta_cobrar']['_id']))->get('one')->items;
				if(floatval($cuenta['saldo'])<=0){
					$f->model('cj/cuen')->params(array(
						'_id'=>$item['cuenta_cobrar']['_id'],
						'data'=>array('$set'=>array(
							'estado'=>'C',
							'saldo'=>0,
							'total'=>floatval($cuenta['total'])+abs($cuenta['saldo'])
						))
					))->save('custom');
				}
			}elseif(isset($item['producto'])){
				$producto = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;




				if(!isset($item['cant'])){
					$item['cant'] = floatval($item['monto']) / floatval($producto['precio']);
				}



				$f->model('fa/prod')->params(array(
					'_id'=>$item['producto']['_id'],
					'data'=>array('$inc'=>array(
						'stock'=>-$item['cant']
					))
				))->save('custom');





				$lote = $f->model('fa/lote')->params(array(
					'producto'=>$item['producto']['_id']
				))->get('lote')->items;


				$f->model('fa/lote')->params(array(
					'_id'=>$lote['_id'],
					'data'=>array('$inc'=>array(
						'cant'=>-$item['cant']
					))
				))->save('custom');



				$f->model("fa/movi")->params(array('data'=>array(
					'fecreg'=>new MongoDate(),
					'autor'=>$f->session->userDBMin,
					'producto'=>$item['producto'],
					'cant'=>-$item['cant'],
					'lote'=>$lote['_id'],
					'estado'=>'E',
					'comprobante'=>array(
						'_id'=>$compro['_id'],
						'serie'=>$compro['serie'],
						'num'=>$compro['num']
					)
				)))->save("insert")->items;
				break;
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
*/
/*function execute_save_anul(){
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
*/
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
			if(isset($item['producto']))
				$items[$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
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
			'modulo'=>'FA',
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
		if($data['moneda']=='D'){
			$comp['total_soles'] = floatval($data['total_soles']);
		}
		if(isset($vouchers)){
			$comp['vouchers'] = $vouchers;
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
		$verify = $f->model('cj/comp')->params(array(
			'tipo'=>$comp['tipo'],
			'serie'=>$comp['serie'],
			'num'=>$comp['num']
		))->get('verify')->items;
		if($verify!=null){
			return $f->response->json(array('error'=>9));
		}
		$compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
		$f->model('cj/talo')->params(array(
			'tipo'=>$data['tipo'],
			'serie'=>$data['serie'],
			'num'=>floatval($data['num']),
			'caja'=>$caja['_id']
		))->save('num');
		foreach ($items as $i=>$item){
			if(isset($item['cuenta_cobrar'])){
				$total = 0;
				foreach ($item['conceptos'] as $w=>$conc){
					$upd['conceptos.'.$w.'.saldo'] = -floatval($conc['monto']);
					$total = $total + (float)$conc['monto'];
				}
				$upd['saldo'] = -floatval($total);
				$f->model('cj/cuen')->params(array(
					'_id'=>$item['cuenta_cobrar']['_id'],
					'data'=>array('$inc'=>$upd)
				))->save('custom');
				$f->model('cj/cuen')->params(array(
					'_id'=>$item['cuenta_cobrar']['_id'],
					'data'=>array('$push'=>array('comprobantes'=>$compro['_id']))
				))->save('custom');
				$cuenta = $f->model('cj/cuen')->params(array('_id'=>$item['cuenta_cobrar']['_id']))->get('one')->items;
				if(floatval($cuenta['saldo'])<=0){
					$f->model('cj/cuen')->params(array(
						'_id'=>$item['cuenta_cobrar']['_id'],
						'data'=>array('$set'=>array(
							'estado'=>'C',
							'saldo'=>0,
							'total'=>floatval($cuenta['total'])+abs($cuenta['saldo'])
						))
					))->save('custom');
				}
			}elseif(isset($item['producto'])){
				$producto_get = $f->model('lg/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;

				/************************************************************************************
				*	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
				************************************************************************************/
				$alma=$f->model("lg/alma")->get("farmacia")->items;
				$id_almacen = new MongoId($alma['_id']);
				$nomb_almacen = $alma['nomb'];
				$fecreg = new MongoDate();

				$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
				if($stock==null){
					$stock = array(
						'_id'=>new MongoId(),
						'producto'=>$producto_get['_id'],
						'almacen'=>$id_almacen,
						'stock'=>0,
						'costo'=>0
					);
					$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
				}
				$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>-1)))->get('custom')->items;
				//$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>1)))->get('custom')->items;
				$saldo_cant = 0;
				$saldo_monto = 0;
				if($saldo!=null){
					if(count($saldo)>0){
						$saldo_cant = $saldo[0]['saldo'];
						$saldo_monto = $saldo[0]['saldo_imp'];
					}
				}

				$stock_actual = floatval($saldo_cant);
				$precio_unitario=$producto_get['precio'];
				/*Reducir stock de LOTES*/
				$lote = $f->model('fa/lote')->params(array(
					'producto'=>$producto_get['_id']
				))->get('lote')->items;
				$f->model('fa/lote')->params(array(
					'_id'=>$lote['_id'],
					'data'=>array('$inc'=>array(
						'cant'=>-$item['cant']
					))
				))->save('custom');
				/*Generar reducción del movimiento*/
				$f->model("fa/movi")->params(array('data'=>array(
					'fecreg'=>$comp['fecreg'],
					'fec'=>$comp['fecreg'],
					'autor'=>$f->session->userDBMin,
					'producto'=>$item['producto'],
					'cant'=>-$item['cant'],
					'lote'=>$lote['_id'],
					'estado'=>'E',
					'comprobante'=>array(
						'_id'=>$comp['_id'],
						'serie'=>$comp['serie'],
						'num'=>$comp['num']
					)
				)))->save("insert")->items;
					/************************************************************
					* REGISTRAR EL MOVIMIENTO DE SALIDA-> KARDEX				*
					************************************************************/
					/*COMO FUNCIONA LOS MOVIMIENTOS*/
					//ENTRADA FISICO O SALIDA FISICO = cant
					//ENTRADA VALORADO O SALIDA VALORADO = total
					//PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
					//SALDO FISICO = saldo
					//SALDO VALORADO = saldo_imp
					$f->model("lg/movi")->params(array('data'=>array(
						'glosa'=>'SALIDA DE PRODUCTOS CON COMPROBANTE MANUAL '.$comp['tipo'].' '.$comp['serie'].' '.$comp['num'],
						'organizacion'=>array(
		                    '_id'=>new MongoId('57b3250f8e73583808000038'),
		                    'nomb'=>utf8_encode('Actividades comerciales DGRE')
		             	),
		             	'documento'=>array(
							'_id'=>$comp['_id'],
							'cod'=>$comp['num'],
							'serie'=>$comp['serie'],
							'tipo'=>$comp['tipo'],
						),
		                'organizacion'=>array(
		                    '_id'=>new MongoId('57b325908e73582808000032'),
		                	'nomb'=>utf8_encode('Botica Moisés Heresi')   
		                ),
						'clasif'=>array(
							"_id" => new MongoId("51f281b04d4a13c4040000a9"),
		 					"cod" => "2.3.1.8.1.99",
							"nomb" => "OTROS PRODUCTOS SIMILARES",
						),
						'cuenta'=>array(
							"_id" => new MongoId("51a9088f4d4a1328070000d1"),
		 					"cod" => "1301.0801",
							"nomb" => "Productos Farmaceuticos",
						),
						'producto'=>array(
                        	"_id"=>$producto_get['_id'],
                        	"cod"=>$producto_get['cod'],
                        	"nomb"=>$producto_get['nomb']
                    	),
                    	"almacen"=>array(
							"_id"=>$id_almacen,
							"nomb"=>$nomb_almacen,
						),
                    	'modulo'=>"FA",
		                'stock'=>$stock['_id'],
		                'lote'=>$lote['_id'],
		                'tipo'=>'S',
		                'autor'=>$f->session->userDBMin,
						'trabajador'=>$f->session->userDBMin,
		                'fecreg'=>$comp['fecreg'],
						'fecmod'=>new MongoDate(),
		                'comprobante'=>array(
                        	'_id'=>$comp['_id'],
                            'serie'=>$comp['serie'],
                            'num'=>$comp['num'],
						),
		                'fec'=>$comp['fecreg'],
		                //COSTO PROMEDIO
		                'precio_unitario'=>floatval($precio_unitario),
		                //SALIDA FISICA
		                'cant'=>floatval($item['cant']),
		                //SALDO FISICO
		                'saldo'=>$stock_actual-floatval($item['cant']),
		                //SALIDA VALORADA
						'total'=>floatval($item['cant'])*floatval($precio_unitario),
		                //SALDO VALORADO
		                'saldo_imp'=>$saldo_monto-floatval($item['cant'])*floatval($precio_unitario),
		            )))->save("insert")->items;

					$f->model("lg/stck")->params(array(
						'_id'=>$stock['_id'],
						'data'=>array(
							'stock'=>$stock_actual-floatval($item['cant']),
							'costo'=>$saldo_monto-floatval($item['cant'])*floatval($precio_unitario),
						)
					))->save("update");

				break;
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
	function execute_lista(){
		global $f;
		$params = array(
			"modulo"=>'FA'
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
		if(isset($f->request->data['sort'])){
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
			if($f->request->data['sort']=='serie'){
				$params['sort'] = array(
					'serie'=>floatval($f->request->data['sort_i']),
					'num'=>1
				);
			}elseif($f->request->data['sort']=='num'){
				$params['sort'] = array(
					'serie'=>floatval($f->request->data['sort_i']),
					'num'=>floatval($f->request->data['sort_i'])
				);
			}
		}else{
			$params['sort'] = array('serie'=>-1,'num'=>-1,'fecreg'=>-1);
		}
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
	function execute_get_var_comp(){
		global $f;
		$tc;
		$igv;
		$vars = array();
		$varss = $f->model("mg/vari")->params(array("fields"=>array(
			'cod'=>true,
			'nomb'=>true,
			'valor'=>true
		)))->get("all");
		foreach ($varss->items as $item){
			$vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
			if($item['cod']=='TC')
				$tc = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
		}
		$cajas = array();
		if(isset($f->session->enti['roles']['cajero'])){
			foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja){
				$cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
			}
		}
		$rpta = array(
			'cajas'=>$cajas,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'FA'))->get('cod')->items,
			'tc'=>$tc,
			'vars'=>$vars,
			'ctban'=>$f->model("ts/ctban")->get("all")->items
		);
		if(isset($f->request->data['dia_ini'])){
			$rpta['calf'] = $f->model('in/calp')->params(array(
				'mes'=>date('n'),
				'ano'=>date('Y'),
				'tipo'=>$f->request->data['dia_ini']
			))->get('periodo')->items;
		}
		$rpta['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>new MongoId('51c20ceb4d4a13740b00001b')))->get('one')->items;
		$f->response->json($rpta);
	}
	function execute_get_info_comp(){
		global $f;
		$ctban = $f->model("ts/ctban")->get("all")->items;
		$talo = $f->model("cj/talo")->params(array('filter'=>array(
			'serie'=>'001',
			"caja._id"=>new MongoId('51a752e14d4a132807000023')
		)))->get("custom")->items;
		$f->response->json(array(
			'ctban'=>$ctban,
			'talo'=>$talo,
			'igv'=>$f->model('mg/vari')->params(array('cod'=>'IGV'))->get('by_cod')->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['modulo'] = 'FA';
		$data['periodo'] = date('ym00');
		$data['num'] = intval($data['num']);
		$data['fecreg'] = new MongoDate(strtotime($data['fec']));
		$data['cliente']['_id'] = new MongoId($data['cliente']['_id']);
		$caja = array(
			"_id"=>new MongoId("56cdef248e7358000700004d"),
			"local"=>array(
				"_id"=>new MongoId("51a7af094d4a13a812000003"),
 				"descr"=>"Centro de Salud Mental",
     			"direccion"=>"Pumacahua S/N"
  			),
   			"nomb"=>"Recaudación Farmacia Moisés Heresi"
		);
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
		foreach ($data['items'] as $i=>$item){
			if(isset($item['cuenta_cobrar'])){
				$data['items'][$i]['cuenta_cobrar']['_id'] = new MongoId($item['cuenta_cobrar']['_id']);
				$data['items'][$i]['cuenta_cobrar']['servicio']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['_id']);
				$data['items'][$i]['cuenta_cobrar']['servicio']['organizacion']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['organizacion']['_id']);
			}
			if(isset($item['producto']))
				$data['items'][$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
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
		$f->response->json(array('_id'=>$data['_id']));
	}
	function execute_anular(){
		global $f;
		$data = $f->request->data;
		$f->model("cj/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'X'
		)))->save("update");
		$data['_id'] = new MongoId($f->request->data['_id']);
		$comp = $f->model('cj/comp')->params(array('_id'=>$data['_id']))->get('one')->items;
		if(isset($comp['items'][0]['cuenta_cobrar'])){
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
	function execute_delete_cobro(){
		global $f;
		$acta = $f->model('cj/cuen')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$acta['feceli'] = new MongoDate();
		$acta['coleccion'] = 'cj_cuentas_cobrar';
		$f->datastore->temp_del->insert($acta);
		$f->datastore->cj_cuentas_cobrar->remove(array('_id'=>$acta['_id']));
		$f->response->print(true);
	}
	function execute_edit_comp(){
		global $f;
		$f->response->view("fa/comp.edit");
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
	function execute_venta(){
		global $f;
		$f->response->view("fa/comp.venta");
	}
	function execute_print(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($comp['cliente']['_id']))
			$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		foreach($comp["items"] as $i=>$item){
			if(isset($item["cuenta_cobrar"])){
				$comp["items"][$i]["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$item["cuenta_cobrar"]["_id"]))->get("one")->items;
				//$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("in/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
			}
		}
		$print = false;
		if(isset($f->request->data['print'])) $print = true;
		if(isset($f->request->data['xls'])){
			switch ($comp['tipo']) {
				case 'B':
					$f->response->view("fa/comp.bole.xls",array("data"=>$comp));
					break;
				case 'F':
					$f->response->view("fa/comp.fact.xls",array("data"=>$comp));
					break;
				case 'R':
					$f->response->view("fa/comp.reci.xls",array("data"=>$comp));
					break;
			}
		}else{
			switch ($comp['tipo']) {
				case 'B':
					$f->response->view("fa/comp.bole.print",array("items"=>$comp,'print'=>$print));
					break;
				case 'F':
					$f->response->view("fa/comp.fact.print",array("items"=>$comp,'print'=>$print));
					break;
				case 'R':
					$f->response->view("fa/comp.reci.print",array("items"=>$comp,'print'=>$print));
					break;
			}
		}
	}
	function execute_planilla(){
		global $f;
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;
		$filter = array(
			'modulo'=>'FA',
			'fecreg'=>array(
				'$gte'=>$recibo['fec'],
				'$lte'=>$recibo['fecfin']
			)
		);
		$comprobantes = $f->model("cj/comp")->params(array(
			"filter"=>$filter,
			'sort'=>array('serie'=>1,'tipo'=>-1,'num'=>1)
		))->get("all")->items;
		$f->response->view("fa/repo.planilla.print",array('recibo'=>$recibo,'comp'=>$comprobantes));
	}
	function execute_reci_ing(){
		global $f;
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;
		foreach ($recibo['detalle'] as $k => $row) {
			$recibo['detalle'][$k]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$row['comprobante']['_id']))->get('one')->items;
		}
		//DETALLE
		foreach ($recibo['detalle'] as $k => $row) {
		    $ctasd[$k] = $row['comprobante']['tipo'];
		    $ctas[$k] = $row['cuenta']['cod'];
		}
		array_multisort($ctas,SORT_ASC,$ctasd, SORT_DESC, $recibo['detalle']);
		/*foreach ($recibo['detalle'] as $k => $row) {
		    $ctas[$k] = $row['cuenta']['cod'];
		}
		array_multisort($ctas, SORT_ASC, $recibo['detalle']);*/
		//CONTABILIDAD PATRIMONIAL
		foreach ($recibo['cont_patrimonial'] as $k => $row) {
		    $ctasp[$k] = $row['cuenta']['cod'];
		    $ctast[$k] = $row['tipo'];
		}
		array_multisort($ctast,SORT_ASC,$ctasp,SORT_ASC,$recibo['cont_patrimonial']);
		//print_r($recibo);die();
		$f->response->view("fa/repo.rein.print",array('recibo'=>$recibo));
	}
}
?>