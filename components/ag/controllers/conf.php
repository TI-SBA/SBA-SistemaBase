<?php
class Controller_ag_conf extends Controller {
	// function execute_reset_legacy(){
	// 	global $f;
	// 	$f->model('ag/prod')->params(array(
	// 		'filter'=>array('stock'=>array('$exists'=>true)),
	// 		'data'=>array('$set'=>array('stock'=>0))
	// 	))->save('custom_all');
	// 	$f->model('ag/lote')->save('clear_all');
	// 	$f->model('ag/movi')->save('clear_all');
	// 	$guias = $f->model('ag/guia')->get('all')->items;
 	// 	foreach ($guias as $guia){
 	// 		foreach ($guia['items'] as $i=>$item) {
 	//        		$lote = array(
	// 				'fecreg'=>$guia['fecreg'],
	// 				'autor'=>$f->session->userDBMin,
	// 				'guia'=>array(
	// 					'_id'=>$guia['_id'],
	// 					'num'=>$guia['num']
	// 				),
	// 				'local'=>array(
	// 					'cod'=>$guia['local']['cod'],
	// 					'nomb'=>$guia['local']['nomb']
	// 				),
	// 				'producto'=>$item['producto'],
	// 				'proveedor'=>$item['proveedor'],
	// 				'cant_ini'=>$item['cant'],
	// 				'cant'=>floatval($item['cant'])
	// 			);
	// 			if($item['fec']!='')
	// 				$lote['fecven'] = $item['fec'];
	// 			$lote = $f->model("ag/lote")->params(array('data'=>$lote))->save("insert")->items;
	// 			$f->model("ag/movi")->params(array('data'=>array(
	// 				'fecreg'=>$guia['fecreg'] ,
	// 				'autor'=>$f->session->userDBMin,
	// 				'producto'=>$item['producto'],
	// 				'cant'=>$item['cant'],
	// 				'lote'=>$lote['_id'],
	// 				'estado'=>'E',
	// 				'local'=>array(
	// 					'cod'=>$guia['local']['cod'],
	// 					'nomb'=>$guia['local']['nomb']
	// 				),
	// 				'guia'=>array(
	// 					'_id'=>$guia['_id'],
	// 					'num'=>$guia['num']
	// 				)
	// 			)))->save("insert")->items;
	// 			$producto = $f->model("ag/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
	// 			if(isset($producto['stock'])){
	// 				foreach ($producto['stock'] as $k=>$stock) {
	// 					if($stock['local']==$guia['local']['cod']){
	// 						$f->model("ag/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
	// 							'$inc'=>array('stock.'.$k.'.cant'=>floatval($item['cant']))
	// 						)))->save("custom");
	// 						break;
	// 					}
	// 				}
	// 			}else{
	// 				$f->model("ag/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
	// 					'$push'=>array('stock'=>array(
	// 						'local'=>$guia['local']['cod'],
	// 						'cant'=>$item['cant']
	// 					))
	// 				)))->save("custom");
	// 			}
	// 		}
	// 	}
	// 	$comprobantes = $f->model('cj/comp')->params(array('filter'=>array(
	// 		'estado'=>'R',
	// 		'modulo'=>'AG'
	// 	)))->get('all')->items;
	// 	foreach ($comprobantes as $i=>$comp) {
	// 		foreach ($comp['items'] as $j=>$item){				
	// 			$producto = $f->model("ag/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
	// 			foreach($producto['stock'] as $k=>$stock){
	// 				if($stock['local']==$comp['local']){
	// 					$f->model('ag/prod')->params(array(
	// 						'_id'=>$item['producto']['_id'],
	// 						'data'=>array('$inc'=>array(
	// 							'stock.'.$k.'.cant'=>-floatval($item['cant'])
	// 						))
	// 					))->save('custom');





	// 					$lote = $f->model('ag/lote')->params(array(
	// 						'producto'=>$item['producto']['_id'],
	// 						'local'=>$comp['local']
	// 					))->get('lote')->items;


	// 					$f->model('ag/lote')->params(array(
	// 						'_id'=>$lote['_id'],
	// 						'data'=>array('$inc'=>array(
	// 							'cant'=>-floatval($item['cant'])
	// 						))
	// 					))->save('custom');



	// 					$f->model("ag/movi")->params(array('data'=>array(
	// 						'fecreg'=>new MongoDate(),
	// 						'autor'=>$f->session->userDBMin,
	// 						'producto'=>$item['producto'],
	// 						'local'=>$comp['local'],
	// 						'cant'=>-$item['cant'],
	// 						'lote'=>$lote['_id'],
	// 						'estado'=>'E',
	// 						'comprobante'=>array(
	// 							'_id'=>$comp['_id'],
	// 							'serie'=>$comp['serie'],
	// 							'num'=>$comp['num']
	// 						)
	// 					)))->save("insert")->items;
	// 					break;
	// 				}
	// 			}
	// 		}
	// 	}
	// }
	function execute_index(){
		global $f;
		$f->response->view('ag/conf');
	}
	function execute_get(){
		global $f;
		$conf = $f->model('cj/conf')->params(array('cod'=>'AG'))->get('cod')->items;
		//$conf['almacenes'] = $f->model('lg/alma')->params(array('filter'=>array('aplicacion'=>'AG','estado'=>'H')))->get('all')->items;
		$conf['almacenes'] = $f->model('lg/alma')->params(array('filter'=>array(
			'estado'=>'H',
			'aplicacion'=>array('$in' => array('LG','AG')),
		)))->get('all')->items;
		$f->response->json($conf);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
  		$data['fecmod'] = new MongoDate();
  		$data['trabajador'] = $f->session->userDB;
		$conf = $f->model('cj/conf')->params(array('cod'=>'AG'))->get('cod')->items;
		if(isset($data['IGV']))
			$data['IGV']['_id'] = new MongoId($data['IGV']['_id']);
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['series'])){
			foreach ($data['series'] as $key => $value) {
				$data['series'][$key]['almacen'] = new MongoId($value['almacen']);
			}
		}
		if(!isset($conf)){
			$f->model("cj/conf")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("cj/conf")->params(array('_id'=>$conf['_id'],'data'=>$data))->save("update");
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Configuracion',
			'descr'=>'Se modifico la <b>Configuracion de Agua Chapi</b>'
		))->save('insert');
		$f->response->print('true');
	}
	/*
	* REINICIO DE ALMACEN
	*/
	function execute_reset(){
		global $f;
		$data=$f->request->data;
		$alma=$f->model("lg/alma")->params(array('_id'=>new MongoId($data['almacen'])))->get("one")->items;

		$alma_id = new MongoId($alma['_id']);
		$alma_nomb = $alma['nomb'];

		$fecreg = new MongoDate();
		$estadisticas=array(
			'Guias_entrada'=>0,
			'Guias_salida'=>0,
			'Guias_entrada_salida'=>0,
			'Comprobantes_manuales'=>0,
			'Comprobantes_electronico'=>0,
		);
		/********************************************************************************
		*	PASO 1: OBTENER PRODUCTOS ESPECIFICOS DE LA COLECCIoN DE AGUA CHAPI
		********************************************************************************/
			#AGUA
			$prods=$f->model('lg/prod')->params(array(
					'filter'=>array('modulo'=>'AG'),
			))->get('filter_all')->items;

		/********************************************************************************
		*	PASO 2: VACIAR STOCKS Y MOVIMIENTOS EN LOGISTICA (MANUAL)
		*********************************************************************************/
			#LIMPIAR LOS STOCKS EN LA COLECCION DE LOGISTICA POR PRODUCTO
			foreach ($prods as $item) {
				$f->model('lg/stck')->params(array(
					'filter'=>array(
						'almacen'=>$alma_id,
						'producto'=>$item['_id'],
					),
					'data'=>array('$set'=>array(
						'stock'=>0,
						'costo'=>0,
					))
				))->save('custom_all');

				$ag_movi = $f->datastore->lg_movimientos->remove(array(
					//'modulo'=>'AG',
					'almacen._id'=>$alma_id,
					'producto._id'=>$item['_id'],
				));
			}
		/********************************************************************************
		*	PASO 2: EXPORTAR GUIAS DE REMISION A TEMP
		*********************************************************************************/
			$temp_agua = $f->datastore->ag_temp_logistica->drop();
			//$guias = $f->model('ag/guia')->params(array('estado'=>'A'))->get('all')->items;
			$sort = array('fec'=>1);
			$guias = $f->datastore->ag_guias_remision->find(array(
				'estado'=>'A'
			))->sort($sort);
			if(!is_null($guias)){
				foreach ($guias as $guia){
					$guia['tipo_importacion']="GUIA";
					$guia['fecha_importacion'] = new MongoDate(strtotime($guia['fec']." ".date('h:i:s',$guia['fecreg']->sec)));
					$f->model('ag/temp')->params(array(
						'data'=>$guia,
					))->save('insert')->items;
				}
			}
		/********************************************************************************
		*	PASO 3: EXPORTAR COMPROBANTES A TEMP  
		*********************************************************************************/
			#$sort = array('_id'=>-1);
			$sort = array('fecreg'=>1);
			$comprobantes = $f->datastore->cj_comprobantes->find(array(
				'estado'=>'R',
				'modulo'=>'AG'
			))->sort($sort);
			foreach ($comprobantes as $i=>$comp) {
				$comp['fecha_importacion']= new MongoDate($comp['fecreg']->sec);
				//$comp['fecha_importacion'] = new MongoDate(strtotime(date('Y-M-d',$comp['fecreg']->sec)." ".date('h:i:s',$comp['fecreal']->sec)));
				$comp['tipo_importacion']="COMPROBANTE";
				$f->model('ag/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
		/********************************************************************************
		*	PASO 4: EXPORTAR ECOMPROBANTES A TEMP  
		*********************************************************************************/
			//$sort = array('_id'=>-1);
			$sort = array('fecemi'=>1);
			$comprobantes_firmados = $f->datastore->cj_ecomprobantes->find(array(
				'estado'=>'FI',
				'items.tipo'=>'agua_chapi'
			))->sort($sort);
			foreach ($comprobantes_firmados as $i=>$comp) {
				$comp['tipo_importacion']="ECOMPROBANTE";
				$comp['fecha_importacion']= new MongoDate($comp['fecreg']->sec);
				$f->model('ag/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
			$comprobantes_enviados = $f->datastore->cj_ecomprobantes->find(array(
				'estado'=>'ES',
				'items.tipo'=>'agua_chapi'
			))->sort($sort);
			foreach ($comprobantes_enviados as $i=>$comp) {
				$comp['tipo_importacion']="ECOMPROBANTE";
				$comp['fecha_importacion']= new MongoDate($comp['fecreg']->sec);
				$f->model('ag/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}

		/********************************************************************************
		*	PASO 5: IMPORTAR MOVIMIENTOS DE TEMP
		*********************************************************************************/
			#$sort = array('fecha_importacion'=>1); #En orden ascendente;
			#$sort = array('_id'=>1); #En orden ascendente;
				#$sort = array('fecha_importacion'=>-1); #En orden ascendente;
			$sort = array(
				'fecha_importacion'=>1, #En orden ascendente;
				'num'=>1, 				#En orden ascendente;
				//'numero'=>1, 			#En orden ascendente;
			); 
			$temporales = $f->datastore->ag_temp_logistica->find()->sort($sort);
			foreach ($temporales as $i=>$temporal) {
				//print_r($temporal);
				if($temporal['tipo_importacion']=='GUIA'){
					foreach ($temporal['items'] as $j=>$item){
						if(isset($temporal['estado'])){
							if($temporal['estado']=="A"){
								/***********************************************************************************
								*	ACTUALIZAR E INSERTAR LOS PRODUCTOS EN LA COLECCION DE LOGISTICA
								************************************************************************************/
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
								
								#SALIDA DE ALMACEN
								if($temporal['almacen_origen']!=null){
									if($temporal['almacen_origen']['_id']->{'$id'}==$alma_id->{'$id'}){
										$estadisticas['Guias_salida']++;
										$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$temporal["almacen_origen"]["_id"],"producto"=>$item['producto']['_id'])))->get('one_custom')->items;
										if($stock==null){
											$stock = array(
												'_id'=>new MongoId(),
												'producto'=>$item['producto']['_id'],
												'almacen'=>$temporal["almacen_origen"]["_id"],
												'stock'=>0,
												'costo'=>0
											);
											$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
										}
										$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>-1)))->get('custom')->items;

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

										#REDUCIR LOTE DE AGUA CHAPI
						                $lote = $f->model('ag/lote')->params(array(
						                    'producto'=>$item['producto']['_id']
						                ))->get('lote')->items;
						                $f->model('ag/lote')->params(array(
						                    '_id'=>$lote['_id'],
						                    'data'=>array('$inc'=>array(
						                        'cant'=>-$item['cant']
						                    ))
						                ))->save('custom');

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
											'glosa'=>'SALIDA DE PRODUCTOS CON DOCUMENTO '.$temporal['tipo']." ".$temporal['num'],
						                    'producto'=>array(
						                        "_id"=>$producto_get['_id'],
						                        "cod"=>$producto_get['cod'],
						                        "nomb"=>$producto_get['nomb']
						                    ),
						                    "almacen"=>$temporal['almacen_origen'],
						                    'organizacion'=>array(
						                        '_id'=>new MongoId('57b3250f8e73583808000038'),
						                        'nomb'=>utf8_encode('Actividades comerciales DGRE')   
						                 	),
						                    'clasif'=>array(
						                        "_id" => new MongoId("51f281b04d4a13c4040000a9"),
						                        "cod" => "2.3.1.8.1.99",
						                        "nomb" => "OTROS PRODUCTOS SIMILARES",
						                    ),
						                    'cuenta'=>array(
						                        "_id" => new MongoId("51a8ff654d4a13540a0000b7"),
						                        "cod" => "1201.0301",
						                        "nomb" => "Venta de Bienes",
						                    ),
						                    'stock'=>$stock['_id'],
						                    'fec'=>$temporal['fec'],
						                    'tipo'=>'S',
											'estado'=>'H',
						                    'modulo'=>'AG',
						                    'documento'=>array(
												'_id'=>$temporal['_id'],
												'cod'=>$temporal['num'],
												'tipo'=>$temporal['tipo']
											),
											'autor'=>$f->session->userDBMin,
						                    'trabajador'=>$f->session->userDBMin,
						                    'fecreg'=>$temporal['fecha_importacion'],
						                    'fecmod'=>$temporal['fecreg'],
						                    'lote'=>$lote['_id'],
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
											)
										))->save("update");
									}
								}
								#ENTRADA DE ALMACEN
								if($temporal['almacen_destino']!=null){
									if($temporal['almacen_destino']['_id']->{'$id'}==$alma_id->{'$id'}){
										$estadisticas['Guias_entrada']++;
										$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$temporal["almacen_destino"]["_id"],"producto"=>$item['producto']['_id'])))->get('one_custom')->items;
										if($stock==null){
											$stock = array(
												'_id'=>new MongoId(),
												'producto'=>$item['producto']['_id'],
												'almacen'=>$temporal["almacen_destino"]["_id"],
												'stock'=>0,
												'costo'=>0
											);
											$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
										}
										/*
											$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>-1)))->get('custom')->items;

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
										*/
										$saldo = $f->model("lg/movi")->params(array(
											'filter'=>array(
												'stock'=>$stock['_id'],
												'almacen._id'=>$alma_id,
												'producto._id'=>$producto_get['_id'],
												'modulo'=>'AG',
											),
											'sort'=>array(
												'fecreg'=>-1
											)
										))->get('custom')->items;
										$saldo_cant = 0;
										$saldo_monto = 0;
										if($saldo!=null){
											if(count($saldo)>0){
												$saldo_cant = $saldo[0]['saldo'];
												$saldo_monto = $saldo[0]['saldo_imp'];
											}
										}

										#DESAFORTUNADAMENTE,DADO LA NATURALEZA,ES MEJOR TOMAR LA DATA POR EL STOCK QUE POR EL MOVIMIENTO
										$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$alma_id,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
										#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
										$saldo_cant = $stock_temp['stock'];
										$saldo_monto = $stock_temp['costo'];

										$stock_actual = floatval($saldo_cant);
										$precio_unitario = $producto_get['precio'];

										$lote = array(
											'fecreg'=>$temporal['fecreg'],
											'autor'=>$f->session->userDBMin,
											'guia'=>array(
												'_id'=>$temporal['_id'],
												'num'=>$temporal['num']
											),
											'producto'=>$item['producto'],
											'proveedor'=>$item['proveedor'],
											'cant_ini'=>$item['cant'],
											'cant'=>floatval($item['cant'])
										);

										if($item['fec']!='')
											$lote['fecven'] = $item['fec'];
										$f->model("ag/lote")->params(array('data'=>$lote))->save("insert")->items;

										$lote = $f->model("ag/lote")->params(array('producto'=>$producto_get['_id']))->get("lote")->items;

										///************************************************
										//* REGISTRAR EL MOVIMIENTO -> KARDEX
										//**************************************************
										/*COMO FUNCIONA LOS MOVIMIENTOS*/
										//ENTRADA FISICO O SALIDA FISICO = cant
										//ENTRADA VALORADO O SALIDA VALORADO = total
										//PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
										//SALDO FISICO = saldo
										//SALDO VALORADO = saldo_imp
										$f->model("lg/movi")->params(array('data'=>array(
											'glosa'=>'INGRESO DE PRODUCTOS CON DOCUMENTO '.$temporal['tipo']." ".$temporal['num'],
											'organizacion'=>array(
						                        '_id'=>new MongoId('57b3250f8e73583808000038'),
						                        'nomb'=>utf8_encode('Actividades comerciales DGRE')   
						                 	),
						                    'producto'=>array(
						                        "_id"=>$producto_get['_id'],
						                        "cod"=>$producto_get['cod'],
						                        "nomb"=>$producto_get['nomb']
						                    ),
						                    'almacen'=>$temporal['almacen_destino'],
						                    'clasif'=>array(
						                        "_id" => new MongoId("51f281b04d4a13c4040000a9"),
						                        "cod" => "2.3.1.8.1.99",
						                        "nomb" => "OTROS PRODUCTOS SIMILARES",
						                    ),
						                    'cuenta'=>array(
						                        "_id" => new MongoId("51a8ff654d4a13540a0000b7"),
						                        "cod" => "1201.0301",
						                        "nomb" => "Venta de Bienes",
						                    ),
						                    'stock'=>$stock['_id'],
						                    'fec'=>$temporal['fec'],
						                    'tipo'=>'E',
						                    'estado'=>'H',
						                    'modulo'=>"AG",
						                    'documento'=>array(
												'_id'=>$temporal['_id'],
												'cod'=>$temporal['num'],
												'tipo'=>$temporal['tipo']
											),
						                    'autor'=>$f->session->userDBMin,
						                    'trabajador'=>$f->session->userDBMin,
						                    'fecreg'=>$temporal['fecha_importacion'],
						                    'fecmod'=>$temporal['fecreg'],
						                    'lote'=>$lote['_id'],
						                    //PRECIO UNITARIO
						                    'precio_unitario'=>floatval($precio_unitario),
						                    //ENTRADA FISICA
						                    'cant'=>floatval($item['cant']),
						                    //SALDO FISICO
											'saldo'=>$stock_actual+floatval($item['cant']),
						                    //SALIDA VALORADA
											'total'=>floatval($item['cant'])*floatval($precio_unitario),
						                    //SALDO VALORADO
						                    'saldo_imp'=>$saldo_monto+floatval($item['cant'])*floatval($precio_unitario),
						                )))->save("insert")->items;

										$f->model("lg/stck")->params(array(
											'_id'=>$stock['_id'],
											'data'=>array(
												'stock'=>$stock_actual+floatval($item['cant']),
											)
										))->save("update");

									}
								}
							}
						}
					}
				}
				#DE ESTAR SETEADO Y EL CAMPO CLIENTE ES UN COMPROBANTE MANUAL
				if($temporal['tipo_importacion']=='COMPROBANTE'){
					$estadisticas['Comprobantes_manuales']++;
					foreach ($temporal['items'] as $j=>$item){
						if($temporal['almacen']['_id']->{'$id'}==$alma_id->{'$id'}){
							/***********************************************************************************
							*	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
							************************************************************************************/
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
							if(is_null($producto_get)){error_log("COMPROBANTE MANUAL", 0);error_log($item['producto'], 0);}

							/*
										$saldo = $f->model("lg/movi")->params(array(
											'filter'=>array(
												'stock'=>$stock['_id']
											),
											'sort'=>array(
												'fecreg'=>-1
											)))->get('custom')->items;

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
							*/

							$stock = $f->model("lg/stck")->params(array(
								"filter"=>array(
									"almacen"=>$alma_id,
									"producto"=>$producto_get['_id']
								)
							))->get('one_custom')->items;		
							if($stock==null){
								$stock = array(
									'_id'=>new MongoId(),
									'producto'=>$producto_get['_id'],
									'almacen'=>$alma_id,
									'stock'=>0,
									'costo'=>0
								);
								$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
							}
							$saldo = $f->model("lg/movi")->params(array(
								'filter'=>array(
									'stock'=>$stock['_id'],
									'almacen._id'=>$alma_id,
									'producto._id'=>$producto_get['_id'],
									'modulo'=>'AG',
								),
								'sort'=>array(
									'fecreg'=>-1
								)
							))->get('custom')->items;
							$saldo_cant = 0;
							$saldo_monto = 0;
							if($saldo!=null){
								if(count($saldo)>0){
									$saldo_cant = $saldo[0]['saldo'];
									$saldo_monto = $saldo[0]['saldo_imp'];
								}
							}
							#DESAFORTUNADAMENTE,DADO LA NATURALEZA,ES MEJOR TOMAR LA DATA POR EL STOCK QUE POR EL MOVIMIENTO
							$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$alma_id,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
							#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
							$saldo_cant = $stock_temp['stock'];
							$saldo_monto = $stock_temp['costo'];


							$stock_actual = floatval($saldo_cant);
							$precio_unitario=$producto_get['precio'];
							#***********************************************************
							# REGISTRAR EL MOVIMIENTO DE SALIDA-> KARDEX				*
							#***********************************************************
							#COMO FUNCIONA LOS MOVIMIENTOS
							//ENTRADA FISICO O SALIDA FISICO = cant
							//ENTRADA VALORADO O SALIDA VALORADO = total
							//PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
							//SALDO FISICO = saldo
							//SALDO VALORADO = saldo_imp
							$f->model("lg/movi")->params(array('data'=>array(
								'glosa'=>'SALIDA DE PRODUCTOS CON COMPROBANTE MANUAL '.$temporal['tipo'].' '.$temporal['serie'].' '.$temporal['num'],
								'organizacion'=>array(
									'_id'=>new MongoId('57b3250f8e73583808000038'),
									'nomb'=>utf8_encode('Actividades comerciales DGRE')
								),
								'producto'=>array(
		                        	"_id"=>$producto_get['_id'],
		                        	"cod"=>$producto_get['cod'],
		                        	"nomb"=>$producto_get['nomb']
		                    	),
								"almacen"=>array(
									"_id"=>$alma_id,
									"nomb"=>$alma_nomb,
								),
				             	
								'clasif'=>array(
									"_id" => new MongoId("51f281b04d4a13c4040000a9"),
									"cod" => "2.3.1.8.1.99",
									"nomb" => "OTROS PRODUCTOS SIMILARES",
								),
								'cuenta'=>array(
									"_id" => new MongoId("51a8ff654d4a13540a0000b7"),
									"cod" => "1201.0301",
									"nomb" => "Venta de Bienes",
								),
								'stock'=>$stock['_id'],
								'fec'=>$temporal['fecreg'],
								'tipo'=>'S',
				                'estado'=>'H',
		                    	'modulo'=>"AG",
				                'documento'=>array(
									'_id'=>$temporal['_id'],
									'cod'=>$temporal['num'],
									'serie'=>$temporal['serie'],
									'tipo'=>$temporal['tipo'],
								),
				                'autor'=>$f->session->userDBMin,
								'trabajador'=>$f->session->userDBMin,
				                'fecreg'=>$temporal['fecreg'],
								'fecmod'=>new MongoDate(),
				                //'lote'=>$lote['_id'],
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
						}
					}
				}
				if($temporal['tipo_importacion']=='ECOMPROBANTE'){
					$estadisticas['Comprobantes_electronico']++;
					foreach ($temporal['items'] as $j=>$item){
						foreach ($item['conceptos'] as $k => $concepto) {
							if(isset($concepto['producto'])){
								#************************************************************************************
								#	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
								#************************************************************************************
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$concepto['producto']['_id']))->get("one")->items;
								$stock = $f->model("lg/stck")->params(array(
									"filter"=>array(
										"almacen"=>$alma_id,
										"producto"=>$producto_get['_id']
									)
								))->get('one_custom')->items;
								if($stock==null){
									$stock = array(
										'_id'=>new MongoId(),
										'producto'=>$producto_get['_id'],
										'almacen'=>$alma_id,
										'stock'=>0,
										'costo'=>0
									);
									$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
								}
								$saldo = $f->model("lg/movi")->params(array(
									'filter'=>array(
										'stock'=>$stock['_id'],
										'almacen._id'=>$alma_id,
										'producto._id'=>$producto_get['_id'],
										'modulo'=>'AG',
									),
									'sort'=>array(
										'fecreg'=>-1
									)
								))->get('custom')->items;
								//$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>1)))->get('custom')->items;
								$saldo_cant = 0;
								$saldo_monto = 0;
								if($saldo!=null){
									if(count($saldo)>0){
										$saldo_cant = $saldo[0]['saldo'];
										$saldo_monto = $saldo[0]['saldo_imp'];
									}
								}
								#DESAFORTUNADAMENTE,DADO LA NATURALEZA,ES MEJOR TOMAR LA DATA POR EL STOCK QUE POR EL MOVIMIENTO
								$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$alma_id,"producto"=>$producto_get['_id'])))->get('one_custom')->items;

								#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
								$saldo_cant = $stock_temp['stock'];
								$saldo_monto = $stock_temp['costo'];

								$stock_actual = floatval($saldo_cant);
								$precio_unitario=$producto_get['precio'];
								
								#***********************************************************
								# REGISTRAR EL MOVIMIENTO DE SALIDA-> KARDEX				*
								#***********************************************************
								#COMO FUNCIONA LOS MOVIMIENTOS
								//ENTRADA FISICO O SALIDA FISICO = cant
								//ENTRADA VALORADO O SALIDA VALORADO = total
								//PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
								//SALDO FISICO = saldo
								//SALDO VALORADO = saldo_imp
								$f->model("lg/movi")->params(array('data'=>array(
									'glosa'=>'SALIDA DE PRODUCTOS CON COMPROBANTE ELECTRONICO '.$temporal['tipo'].' '.$temporal['serie'].' '.$temporal['numero'],
									'organizacion'=>array(
					                    '_id'=>new MongoId('57b3250f8e73583808000038'),
					                    'nomb'=>utf8_encode('Actividades comerciales DGRE')
					             	),
					             	'producto'=>array(
			                        	"_id"=>$producto_get['_id'],
			                        	"cod"=>$producto_get['cod'],
			                        	"nomb"=>$producto_get['nomb']
			                    	),
			                    	"almacen"=>array(
										"_id"=>$alma_id,
										"nomb"=>$alma_nomb,
									),
									'clasif'=>array(
										"_id" => new MongoId("51f281b04d4a13c4040000a9"),
										"cod" => "2.3.1.8.1.99",
										"nomb" => "OTROS PRODUCTOS SIMILARES",
									),
									'cuenta'=>array(
										"_id" => new MongoId("51a8ff654d4a13540a0000b7"),
										"cod" => "1201.0301",
										"nomb" => "Venta de Bienes",
									),
									'stock'=>$stock['_id'],
					             	'documento'=>array(
										'_id'=>$temporal['_id'],
										'cod'=>$temporal['numero'],
										'serie'=>$temporal['serie'],
										'tipo'=>$temporal['tipo'],
									),
					               'organizacion'=>array(
										'_id'=>new MongoId('57b3250f8e73583808000038'),
										'nomb'=>utf8_encode('Actividades comerciales DGRE')
									),
					               'fec'=>$temporal['fecemi'],
					                'tipo'=>'S',
					                'estado'=>'H',
									'modulo'=>"AG",
					                'autor'=>$f->session->userDBMin,
									'trabajador'=>$f->session->userDBMin,
					                'fecreg'=>$temporal['fecreg'],
									'fecmod'=>new MongoDate(),
									//'lote'=>$lote['_id'],
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
							}
						}
					}
				}
			}
		/********************************************************************************
		*	PASO 6: REPORTAR ESTADISTICAS DE AGUA CHAPI
		*********************************************************************************/
			$f->response->json($estadisticas);
	}
}
?>