<?php
class Controller_fa_conf extends Controller {
	/*function execute_reset(){
		global $f;
		$comprobantes = $f->datastore->cj_comprobantes->find(array(
			'estado'=>'R',
			'modulo'=>'FA'
		));
		foreach ($comprobantes as $i=>$comp) {
			foreach ($comp['items'] as $j=>$item){
				if(!isset($item['cant'])){
					$item['producto']['_id'] = new MongoId($item['producto']['_id']);
					$producto = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
					$item['cant'] = floatval($item['monto']) / floatval($producto['precio']);
					$f->model('cj/comp')->params(array(
						'_id'=>$comp['_id'],
						'data'=>array(
							'items.'.$j.'.producto._id'=>$item['producto']['_id'],
							'items.'.$j.'.cant'=>$item['cant']
						)
					))->save('update');
				}
			}
		}

		$f->model('fa/prod')->params(array(
			'filter'=>array('stock'=>array('$exists'=>true)),
			'data'=>array('$set'=>array('stock'=>0))
		))->save('custom_all');
		$f->model('fa/lote')->save('clear_all');
		$f->model('fa/movi')->save('clear_all');
		$guias = $f->model('fa/guia')->get('all')->items;
    	foreach ($guias as $guia){
      		foreach ($guia['items'] as $i=>$item) {
        		$lote = array(
					'fecreg'=>$guia['fecreg'],
					'autor'=>$f->session->userDBMin,
					'guia'=>array(
						'_id'=>$guia['_id'],
						'num'=>$guia['num']
					),
					'producto'=>$item['producto'],
					'proveedor'=>$item['proveedor'],
					'cant_ini'=>$item['cant'],
					'cant'=>floatval($item['cant'])
				);
				if($item['fec']!='')
					$lote['fecven'] = $item['fec'];
				$lote = $f->model("fa/lote")->params(array('data'=>$lote))->save("insert")->items;
				$f->model("fa/movi")->params(array('data'=>array(
					'fecreg'=>$guia['fecreg'],
					'fec'=>$guia['fec'],
					'autor'=>$f->session->userDBMin,
					'producto'=>$item['producto'],
					'cant'=>$item['cant'],
					'lote'=>$lote['_id'],
					'estado'=>'E',
					'guia'=>array(
						'_id'=>$guia['_id'],
						'num'=>$guia['num']
					)
				)))->save("insert")->items;
				$producto = $f->model("fa/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
				if(isset($producto['stock'])){
					$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
						'$inc'=>array('stock'=>floatval($item['cant']))
					)))->save("custom");
				}else{
					$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
						'$set'=>array('stock'=>$item['cant'])
					)))->save("custom");
				}
			}
		}
		$comprobantes = $f->model('cj/comp')->params(array('filter'=>array(
			'estado'=>'R',
			'modulo'=>'FA'
		)))->get('all')->items;
		foreach ($comprobantes as $i=>$comp) {
			foreach ($comp['items'] as $j=>$item){
				$f->model('fa/prod')->params(array(
					'_id'=>$item['producto']['_id'],
					'data'=>array('$inc'=>array(
						'stock'=>-floatval($item['cant'])
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
					'fecreg'=>$comp['fecreal'],
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
			}
		}
	}
	*/
	function execute_index(){
		global $f;
		$f->response->view('fa/conf');
	}
	function execute_get(){
		global $f;
		$conf = $f->model('cj/conf')->params(array('cod'=>'FA'))->get('cod')->items;
		$f->response->json($conf);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
  		$data['fecmod'] = new MongoDate();
  		$data['trabajador'] = $f->session->userDB;
		$conf = $f->model('cj/conf')->params(array('cod'=>'FA'))->get('cod')->items;
		if(isset($data['IGV']))
			$data['IGV']['_id'] = new MongoId($data['IGV']['_id']);
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($conf)){
			$f->model("cj/conf")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("cj/conf")->params(array('_id'=>$conf['_id'],'data'=>$data))->save("update");
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Configuracion',
			'descr'=>'Se modifico la <b>Configuracion de Farmacia</b> (Farmacia Mois&eacute;s Heresi)'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_reset_legacy(){
		global $f;
		$comprobantes = $f->datastore->cj_comprobantes->find(array(
			'estado'=>'R',
			'modulo'=>'FA'
		));
		$alma=$f->model("lg/alma")->get("farmacia")->items;
		$id_almacen = new MongoId($alma['_id']);
		$nomb_almacen = $alma['nomb'];
		$fecreg = new MongoDate();
		$stoks = $f->datastore->lg_stocks->remove(array('almacen'=>$alma['_id']));
		#*********************************************************************************
		#	PASO 1: LISTAR CADA COMPROBANTE Y VERIFICAR QUE MANTENGAN SU CAMPO PRECIO
		#********************************************************************************
			foreach ($comprobantes as $i=>$comp) {
				foreach ($comp['items'] as $j=>$item){
					#SI NO ESTA SETEADO EL ITEM CANT
					if(!isset($item['cant'])){
						$item['producto']['_id'] = new MongoId($item['producto']['_id']);
						/*Producto de Farmacia*/
						$producto_get = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['generico']))->get('nomb')->items;
						#if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldid'=>$item['producto']['cod']))->get('oldid')->items;
						$item['cant'] = floatval($item['monto']) / floatval($producto_get['precio']);
						$f->model('cj/comp')->params(array(
							'_id'=>$comp['_id'],
							'data'=>array(
								'items.'.$j.'.producto._id'=>$item['producto']['_id'],
								'items.'.$j.'.cant'=>$item['cant']
							)
						))->save('update');
					}
				}
			}
		#*********************************************************************************
		#	PASO 2: VERIFICACION QUE LOS PRODUCTOS SE ENCUENTREN EN LOGISTICA
		#*********************************************************************************
			#*****************************************************************************
			#	A: OBTENER PRODUCTOS DE FARMACIA
			#*****************************************************************************
			$productos = $f->model('fa/prod')->get('all')->items;
			#*******************************************************************************
			#	B: VERIFICACION E INSERCION PRODUCTOS DE FARMACIA SI NO EXISTE EN LOGISTICA  
			#*******************************************************************************
			foreach ($productos as $faprod){
				$producto_get = $f->model('lg/prod')->params(array(
					'oldcod'=>$faprod['cod'],
					'unidad'=>$faprod['unidad']['_id'],
				))->get('oldcod')->items;
				//$producto_get = $f->model('lg/prod')->params(array('nomb'=>$faprod['nomb']))->get('nomb')->items;
				//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$faprod['generico']))->get('nomb')->items;
				if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$faprod['nomb']))->get('nomb')->items;
				//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$faprod['generico']))->get('generico')->items;
				if(is_null($producto_get)){
					#GENERAR CODIGO LOGISTICA
					$cod = $f->model('lg/prod')->get('cod');
					if($cod->items==null) $cod->items="000001";
					else{
						$tmp = intval($cod->items);
						$tmp++;
						$tmp = (string)$tmp;
						for($i=strlen($tmp); $i<6; $i++){
							$tmp = '0'.$tmp;
						}
						$cod->items = $tmp;
					}
					#INSERTAR PRODUCTO
					$a_prod=array(
						'oldcod'		=>$faprod['cod'],
						'old_id'		=>$faprod['_id'],
						'autor'			=>$faprod['autor'],
						'trabajador'	=>$faprod['autor'],
						'fecreg' 		=>$faprod['fecreg'],
						'generico' 		=>$faprod['generico'],
						'fecmod'		=>new MongoDate(),
						'cod'			=>$cod->items,
						'modulo'		=>'FA',
						'nomb'			=>$faprod['nomb'],
						'descr'			=>$faprod['generico'],
						'estado'		=>"H",
						'precio'		=>floatval($faprod['precio']),
						'descr'			=>$faprod['generico'],
						'precio_venta'	=>floatval($faprod['precio']),
						'tipo_producto' =>"P",
						'unidad' 		=>$faprod['unidad'],
						'clasif'		=>array(
							"_id" => new MongoId("51f281b04d4a13c4040000a9"),
	 						"cod" => "2.3.1.8.1.99",
							"nomb" => "OTROS PRODUCTOS SIMILARES",
						),
						'cuenta'		=>array(
							"_id" 	=> new MongoId("51a9088f4d4a1328070000d1"),
	 						"cod" 	=> "1301.0801",
							"nomb" 	=> "Productos Farmaceuticos",
						),
					);
					$producto_get = $f->model("lg/prod")->params(array('data'=>$a_prod))->save("insert")->items;
					$producto_get = $f->model('lg/prod')->params(array('oldcod'=>$faprod['cod']))->get('oldcod')->items;
				}
				else{
					#SI NO ESTA SETEADO EL MODULO
					if(!isset($producto_get['modulo'])){
							$a_prod=array(
								'modulo'=>"FA",
							);
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI EL MODULO ESTA SETEADO CON UN CAMPO DIFERENTE A FARMACIA
					if($producto_get['modulo']!='FA'){
						$a_prod=array(
							'modulo'=>"FA",
						);
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI EL CAMPO PRECIO NO ES CORRECTO
					if($producto_get['precio']==0){
						$a_prod=array(
							'precio'=>$faprod['precio'],
							'precio_unitario'=>$faprod['precio'],
							'precio_venta'=>$faprod['precio'],
						);
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI NO ESTA SETEADO EL OLDCOD
					if(!isset($producto_get['oldcod'])){
							$a_prod=array(
								'oldcod'=>$faprod['cod'],
							);
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI NO ESTA SETEADO EL OLD_id
					if(!isset($producto_get['old_id'])){
							$a_prod=array(
								'old_id'=>$faprod['_id'],
							);
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}

				}
				#***************************************************************************
				#	C: ANADIR Y CREAR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
				#***************************************************************************
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
			}		
		#*********************************************************************************
		#	PASO 3: VACIAR STOCKS Y MOVIMIENTOS TANTO EN FARMACIA COMO EN LOGISTICA (MANUAL)
		#*********************************************************************************
			#LIMPIAR LOS STOCKS EN LA COLECCION DE FARMACIA
			$f->model('fa/prod')->params(array(
				'filter'=>array('stock'=>array('$exists'=>true)),
				'data'=>array('$set'=>array('stock'=>0))
			))->save('custom_all');
			#LIMPIAR LOTES Y MOVIMIENTOS EN FARMACIA
			$f->model('fa/lote')->save('clear_all');
			$f->model('fa/movi')->save('clear_all');
			#LIMPIAR LOS STOCKS EN LA COLECCION DE LOGISTICA
			$f->model('lg/stck')->params(array(
				'filter'=>array('almacen'=>$id_almacen),
				'data'=>array('$set'=>array(
					'stock'=>0,
					'costo'=>0,
				))
			))->save('custom_all');
			#-------------ADVERTENCIA----------------
			#Si desbloqueas estas lineas, borraras todos los lotes y movimientos de logistica
			#desbloquear solo si sabes lo que haces
			#LIMPIAR LOTES Y MOVIMIENTOS EN LOGISTICA
			#$f->model('fa/lote')->save('clear_all');
			#$f->model('fa/movi')->save('clear_all');
			$movimien = $f->datastore->lg_movimientos->remove(array('modulo'=>'FA'));

		#*********************************************************************************
		#	PASO 4: IMPORTAR GUIAS DE TEMP
		#*********************************************************************************
		$temporales = $f->datastore->fa_temp_logistica->drop();
			$guias = $f->model('fa/guia')->get('all')->items;
	    	foreach ($guias as $guia){
	      		$f->model('fa/temp')->params(array(
					'data'=>$guia,
				))->save('insert')->items;
			}
		#*********************************************************************************
		#	PASO 5: EXPORTAR COMPROBANTES A TEMP  
		#*********************************************************************************
			$sort = array('_id'=>-1);
			$comprobantes = $f->datastore->cj_comprobantes->find(array(
				'estado'=>'R',
				'modulo'=>'FA'
			))->sort($sort);
			foreach ($comprobantes as $i=>$comp) {
				$f->model('fa/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
		#*********************************************************************************
		#	PASO 6: EXPORTAR ECOMPROBANTES A TEMP  
		#*********************************************************************************
			$sort = array('_id'=>-1);
			$comprobantes_firmados = $f->datastore->cj_ecomprobantes->find(array(
				'estado'=>'FI',
				'items.tipo'=>'farmacia'
			))->sort($sort);
			foreach ($comprobantes_firmados as $i=>$comp) {
				$f->model('fa/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
			$comprobantes_enviados = $f->datastore->cj_ecomprobantes->find(array(
				'estado'=>'ES',
				'items.tipo'=>'farmacia'
			))->sort($sort);
			foreach ($comprobantes_enviados as $i=>$comp) {
				$f->model('fa/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
		#*********************************************************************************
		#	PASO 7: IMPORTAR MOVIMIENTOS DE TEMP
		#*********************************************************************************
			$sort = array('fecreg'=>1); #En orden ascendente;
			#$sort = array('_id'=>1); #En orden ascendente;
			$temporales = $f->datastore->fa_temp_logistica->find()->sort($sort);
			foreach ($temporales as $i=>$temporal) {
				#SI NO ESTA SETEADO EL CAMPO MODULO, NI EL CAMPO CLIENTE ES UNA GUIA DE REMISION
				if(!isset($temporal['modulo']) && !isset($temporal['cliente']) && !isset($temporal['cliente_nomb'])){
					foreach ($temporal['items'] as $j=>$item){
						#ACTUALIZAR EL PRODUCTO EN LA COLECCION DE FARMACIA
						$producto = $f->model("fa/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
						if(isset($producto['stock'])){
							$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
								'$inc'=>array('stock'=>floatval($item['cant']))
							)))->save("custom");
						}else{
							$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
								'$set'=>array('stock'=>$item['cant'])
							)))->save("custom");
						}
						#***********************************************************************************
						#	ACTUALIZAR E INSERTAR LOS PRODUCTOS EN LA COLECCION DE LOGISTICA
						#***********************************************************************************
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
						$producto_fa = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
						if(is_null($producto_get)) $producto_get = $f->model("lg/prod")->params(array('old_id'=>$producto_fa['_id']))->get("old_id")->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldcod'=>$item['producto']['cod']))->get('oldcod')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array(
							'oldcod'=>$producto_fa['cod'],
							'unidad'=>$faprod['unidad']['_id'],
						))->get('oldcod')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;

						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$producto_fa['nomb']))->get('nomb')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$item['producto']['generico']))->get('generico')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$producto_fa['generico']))->get('generico')->items;
						/*if(is_null($producto_get)){
							#GENERAR COD
							$cod = $f->model('lg/prod')->get('cod');
							if($cod->items==null) $cod->items="000001";
							else{
								$tmp = intval($cod->items);
								$tmp++;
								$tmp = (string)$tmp;
								for($i=strlen($tmp); $i<6; $i++){
									$tmp = '0'.$tmp;
								}
								$cod->items = $tmp;
							}
							#INSERTAR PRODUCTO
							$a_prod=array(
								'oldid'			=>$item['producto']['cod'],
								'autor'			=>$producto_fa['autor'],
								'trabajador'	=>$producto_fa['autor'],
								'fecreg' 		=>$producto_fa['fecreg'],
								'generico' 		=>$producto_fa['generico'],
								'fecmod'		=>new MongoDate(),
								'cod'			=>$cod->items,
								'modulo'		=>'FA',
								'nomb'			=>$item['producto']['nomb'],
								'descr'			=>$item['producto']['generico'],
								'estado'		=>"H",
								'precio'		=>floatval($producto_fa['precio']),
								'descr'			=>$item['producto']['generico'],
								'precio_venta'	=>floatval($producto_fa['precio']),
								'tipo_producto' =>"P",
								'unidad' 		=>$item['producto']['unidad'],
								'clasif'		=>array(
									"_id" => new MongoId("51f281b04d4a13c4040000a9"),
			 						"cod" => "2.3.1.8.1.99",
									"nomb" => "OTROS PRODUCTOS SIMILARES",
								),
								'cuenta'		=>array(
									"_id" 	=> new MongoId("51a9088f4d4a1328070000d1"),
			 						"cod" 	=> "1301.0801",
									"nomb" 	=> "Productos Farmaceuticos",
								),
							);
							$producto_get = $f->model("lg/prod")->params(array('data'=>$a_prod))->save("insert")->items;
							$producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						}*/
						if(is_null($producto_get)){error_log("GUIA", 0);error_log($item['producto'], 0);}

						/*else{
							if(!isset($producto_get['modulo'])){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['modulo']!='FA'){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['precio']==0){
								$a_prod=array(
									'precio'=>$producto_get['precio'],
									'precio_unitario'=>$producto_get['precio'],
									'precio_venta'=>$producto_get['precio'],
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
						}*/

						if(!isset($producto_get['autor'])); $producto_get['autor']=$f->session->userDBMin;

						#INSERTAR UN LOTE DE FARMACIA
						$lote = array(
							'fecreg'=>$temporal['fecreg'],
							'autor'=>$producto_get['autor'],
							'guia'=>array(
								'_id'=>$temporal['_id'],
								'num'=>$temporal['num']
							),
							'producto'=>array(
								'_id'=>$producto_get['_id'],
								'nomb'=>$producto_get['nomb']
							),
							'proveedor'=>$item['proveedor'],
							'cant_ini'=>$item['cant'],
							'cant'=>floatval($item['cant'])
						);
						$lote['producto']['_id']=$producto_get['_id'];

						if($item['fec']!='')
							$lote['fecven'] = $item['fec'];
						$f->model("fa/lote")->params(array('data'=>$lote))->save("insert")->items;
						$lote = $f->model("fa/lote")->params(array('producto'=>$producto_get['_id']))->get("lote")->items;

						#INSERTAR LOS MOVIMIENTOS EN LA COLECCION DE FARMACIA
						$f->model("fa/movi")->params(array('data'=>array(
							'fecreg'=>$temporal['fecreg'],
							'fec'=>$temporal['fecreg'],
							'autor'=>$f->session->userDBMin,
							'producto'=>$item['producto'],
							'cant'=>$item['cant'],
							'lote'=>$lote['_id'],
							'estado'=>'E',
							'guia'=>array(
								'_id'=>$temporal['_id'],
								'num'=>$temporal['num']
							)
						)))->save("insert")->items;

						#***********************************************************************************
						#	ANADIR Y CREAR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
						#************************************************************************************
						$stock = $f->model("lg/stck")->params(array(
							"filter"=>array(
								"almacen"=>$id_almacen,
								"producto"=>$producto_get['_id']
							)
						))->get('one_custom')->items;		
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
						$saldo = $f->model("lg/movi")->params(array(
							'filter'=>array(
								'stock'=>$stock['_id'],
								'almacen._id'=>$id_almacen,
								'producto._id'=>$producto_get['_id'],
								'modulo'=>'FA',
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
						$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
						#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
						$saldo_cant = $stock_temp['stock'];
						$saldo_monto = $stock_temp['costo'];
						$stock_actual = floatval($saldo_cant);
						$precio_unitario=$producto_get['precio'];

						#************************************************
						#* REGISTRAR EL MOVIMIENTO -> KARDEX
						#************************************************
						#COMO FUNCIONA LOS MOVIMIENTOS
						//ENTRADA FISICO O SALIDA FISICO = cant
						//ENTRADA VALORADO O SALIDA VALORADO = total
						//PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
						//SALDO FISICO = saldo
						//SALDO VALORADO = saldo_imp
						$f->model("lg/movi")->params(array('data'=>array(
							'glosa'=>'INGRESO DE PRODUCTOS CON GUIA '.$temporal['num'],
							'organizacion'=>array(
		                        '_id'=>new MongoId('57b325908e73582808000032'),
		                    	'nomb'=>utf8_encode('Botica Moises Heresi')   
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
		                 	'documento'=>array(
								'_id'=>$temporal['_id'],
								'cod'=>$temporal['num'],
								'tipo'=>'GR'
							),
							'guia'=>array(
								'_id'=>$temporal['_id'],
								'num'=>$temporal['num']
							),
		                    'producto'=>array(
		                        "_id"=>$producto_get['_id'],
		                        "cod"=>$producto_get['cod'],
		                        "nomb"=>$producto_get['nomb']
		                    ),
		                    'modulo'=>"FA",
		                    'fecreg'=>$temporal['fecreg'],
							'fecmod'=>new MongoDate(),
		                    'lote'=>$lote['_id'],
		                    'stock'=>$stock['_id'],
		                    'tipo'=>'E',
		                    "almacen"=>array(
									"_id"=>$id_almacen,
									"nomb"=>$nomb_almacen,
							),
		                    'autor'=>$f->session->userDBMin,
							'trabajador'=>$f->session->userDBMin,
		                    'guia'=>$temporal['_id'],
		                    'lote'=>$lote['_id'],
		                    'fec'=>$temporal['fecreg'],
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
								'costo'=>$saldo_monto+floatval($item['cant'])*floatval($precio_unitario),
							)
						))->save("update");
					}
				}
				#DE ESTAR SETEADO Y EL CAMPO CLIENTE ES UN COMPROBANTE MANUAL
				if(isset($temporal['modulo']) && isset($temporal['cliente']) && !isset($temporal['cliente_nomb'])){
					foreach ($temporal['items'] as $j=>$item){
						#Reducir stock de FARMACIA
						$producto_fa=$f->model('fa/prod')->params(array(
							'_id'=>$item['producto']['_id'],
							'data'=>array('$inc'=>array(
								'stock'=>-floatval($item['cant'])
							))
						))->save('custom')->items;
						#***********************************************************************************
						#	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
						#***********************************************************************************
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
						$producto_fa = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
						if(is_null($producto_get)) $producto_get = $f->model("lg/prod")->params(array('old_id'=>$producto_fa['_id']))->get("old_id")->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldcod'=>$item['producto']['cod']))->get('oldcod')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array(
							'oldcod'=>$producto_fa['cod'],
							'unidad'=>$faprod['unidad']['_id'],
						))->get('oldcod')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$producto_fa['nomb']))->get('nomb')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$item['producto']['generico']))->get('generico')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$producto_fa['generico']))->get('generico')->items;
						//if(is_null($producto_get)){print_r("COMPROBANTE MANUAL <br>");print_r($item['producto']);}
						if(is_null($producto_get)){error_log("COMPROBANTE MANUAL", 0);error_log($item['producto'], 0);}
						/*if(is_null($producto_get)){
							#GENERAR COD
							$cod = $f->model('lg/prod')->get('cod');
							if($cod->items==null) $cod->items="000001";
							else{
								$tmp = intval($cod->items);
								$tmp++;
								$tmp = (string)$tmp;
								for($i=strlen($tmp); $i<6; $i++){
									$tmp = '0'.$tmp;
								}
								$cod->items = $tmp;
							}
							#INSERTAR PRODUCTO
							$a_prod=array(
								'oldid'			=>$item['producto']['cod'],
								'autor'			=>$producto_fa['autor'],
								'trabajador'	=>$producto_fa['autor'],
								'fecreg' 		=>$producto_fa['fecreg'],
								'generico' 		=>$producto_fa['generico'],
								'fecmod'		=>new MongoDate(),
								'cod'			=>$cod->items,
								'modulo'		=>'FA',
								'nomb'			=>$item['producto']['nomb'],
								'descr'			=>$item['producto']['generico'],
								'estado'		=>"H",
								'precio'		=>floatval($producto_fa['precio']),
								'descr'			=>$item['producto']['generico'],
								'precio_venta'	=>floatval($producto_fa['precio']),
								'tipo_producto' =>"P",
								'unidad' 		=>$item['producto']['unidad'],
								'clasif'		=>array(
									"_id" => new MongoId("51f281b04d4a13c4040000a9"),
			 						"cod" => "2.3.1.8.1.99",
									"nomb" => "OTROS PRODUCTOS SIMILARES",
								),
								'cuenta'		=>array(
									"_id" 	=> new MongoId("51a9088f4d4a1328070000d1"),
			 						"cod" 	=> "1301.0801",
									"nomb" 	=> "Productos Farmaceuticos",
								),
							);
							$producto_get = $f->model("lg/prod")->params(array('data'=>$a_prod))->save("insert")->items;
							$producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						}
						else{
							if(!isset($producto_get['modulo'])){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['modulo']!='FA'){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['precio']==0){
								$a_prod=array(
									'precio'=>$producto_get['precio'],
									'precio_unitario'=>$producto_get['precio'],
									'precio_venta'=>$producto_get['precio'],
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
						}*/
						$stock = $f->model("lg/stck")->params(array(
							"filter"=>array(
								"almacen"=>$id_almacen,
								"producto"=>$producto_get['_id']
							)
						))->get('one_custom')->items;		
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
						$saldo = $f->model("lg/movi")->params(array(
							'filter'=>array(
								'stock'=>$stock['_id'],
								'almacen._id'=>$id_almacen,
								'producto._id'=>$producto_get['_id'],
								'modulo'=>'FA',
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
						$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
						#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
						$saldo_cant = $stock_temp['stock'];
						$saldo_monto = $stock_temp['costo'];


						$stock_actual = floatval($saldo_cant);
						$precio_unitario=$producto_get['precio'];
						#Reducir stock de LOTES
						$lote = $f->model('fa/lote')->params(array(
							'producto'=>$producto_get['_id']
						))->get('lote')->items;
						$f->model('fa/lote')->params(array(
							'_id'=>$lote['_id'],
							'data'=>array('$inc'=>array(
								'cant'=>-$item['cant']
							))
						))->save('custom');
						#Generar reducciOn del movimiento
						$f->model("fa/movi")->params(array('data'=>array(
							'fecreg'=>$temporal['fecreg'],
							'fec'=>$temporal['fecreg'],
							'autor'=>$f->session->userDBMin,
							'producto'=>$item['producto'],
							'cant'=>-$item['cant'],
							'lote'=>$lote['_id'],
							'estado'=>'E',
							'comprobante'=>array(
								'_id'=>$temporal['_id'],
								'serie'=>$temporal['serie'],
								'num'=>$temporal['num']
							)
						)))->save("insert")->items;
						//if(isset($producto_get)){
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
			             	'documento'=>array(
								'_id'=>$temporal['_id'],
								'cod'=>$temporal['num'],
								'serie'=>$temporal['serie'],
								'tipo'=>$temporal['tipo'],
							),
			                'organizacion'=>array(
			                    '_id'=>new MongoId('57b325908e73582808000032'),
			                	'nomb'=>utf8_encode('Botica Moises Heresi')   
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
			                'fecreg'=>$temporal['fecreg'],
							'fecmod'=>new MongoDate(),
			                'comprobante'=>array(
	                        	'_id'=>$temporal['_id'],
	                            'serie'=>$temporal['serie'],
	                            'num'=>$temporal['num'],
							),
			                'fec'=>$temporal['fecreg'],
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
				#DE ESTAR SETEADO, CAMPO CLIENTE_NOMB ES UN COMPROBANTE ELECTRONICO
				#OJO, LOS ELECTRONICOS DEPENDE DE LOGISTICA SI O SI
				if(!isset($temporal['modulo']) && !isset($temporal['cliente']) && isset($temporal['cliente_nomb'])){
					foreach ($temporal['items'] as $j=>$item){
						foreach ($item['conceptos'] as $k => $concepto) {
							if(isset($concepto['producto'])){
								#************************************************************************************
								#	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
								#************************************************************************************
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$concepto['producto']['_id']))->get("one")->items;
								if(is_null($producto_get)){print_r("COMPROBANTE ELECTRONICO");print_r($concepto['producto']);}

								$stock = $f->model("lg/stck")->params(array(
									"filter"=>array(
										"almacen"=>$id_almacen,
										"producto"=>$producto_get['_id']
									)
								))->get('one_custom')->items;
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
								$saldo = $f->model("lg/movi")->params(array(
									'filter'=>array(
										'stock'=>$stock['_id'],
										'almacen._id'=>$id_almacen,
										'producto._id'=>$producto_get['_id'],
										'modulo'=>'FA',
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
								$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
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
					             	'documento'=>array(
										'_id'=>$temporal['_id'],
										'cod'=>$temporal['numero'],
										'serie'=>$temporal['serie'],
										'tipo'=>$temporal['tipo'],
									),
					                'organizacion'=>array(
					                    '_id'=>new MongoId('57b325908e73582808000032'),
					                	'nomb'=>utf8_encode('Botica Moises Heresi')   
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
					                'fecreg'=>$temporal['fecreg'],
									'fecmod'=>new MongoDate(),
					                'comprobante'=>array(
			                        	'_id'=>$temporal['_id'],
			                            'serie'=>$temporal['serie'],
			                            'num'=>$temporal['numero'],
									),
					                'fec'=>$temporal['fecreg'],
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
	}
	function execute_reset(){
		global $f;
		$comprobantes = $f->datastore->cj_comprobantes->find(array(
			'estado'=>'R',
			'modulo'=>'FA'
		));
		$alma=$f->model("lg/alma")->get("farmacia")->items;
		$id_almacen = new MongoId($alma['_id']);
		$nomb_almacen = $alma['nomb'];
		$fecreg = new MongoDate();
		$stoks = $f->datastore->lg_stocks->remove(array('almacen'=>$alma['_id']));
		#*********************************************************************************
		#	PASO 1: LISTAR CADA COMPROBANTE Y VERIFICAR QUE MANTENGAN SU CAMPO PRECIO
		#********************************************************************************
			foreach ($comprobantes as $i=>$comp) {
				foreach ($comp['items'] as $j=>$item){
					#SI NO ESTA SETEADO EL ITEM CANT
					if(!isset($item['cant'])){
						$item['producto']['_id'] = new MongoId($item['producto']['_id']);
						/*Producto de Farmacia*/
						$producto_get = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['generico']))->get('nomb')->items;
						#if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldid'=>$item['producto']['cod']))->get('oldid')->items;
						$item['cant'] = floatval($item['monto']) / floatval($producto_get['precio']);
						$f->model('cj/comp')->params(array(
							'_id'=>$comp['_id'],
							'data'=>array(
								'items.'.$j.'.producto._id'=>$item['producto']['_id'],
								'items.'.$j.'.cant'=>$item['cant']
							)
						))->save('update');
					}
				}
			}
		#*********************************************************************************
		#	PASO 2: VERIFICACION QUE LOS PRODUCTOS SE ENCUENTREN EN LOGISTICA
		#*********************************************************************************
			#*****************************************************************************
			#	A: OBTENER PRODUCTOS DE FARMACIA
			#*****************************************************************************
			$productos = $f->model('fa/prod')->get('all')->items;
			#*******************************************************************************
			#	B: VERIFICACION E INSERCION PRODUCTOS DE FARMACIA SI NO EXISTE EN LOGISTICA  
			#*******************************************************************************
			foreach ($productos as $faprod){
				$producto_get = $f->model('lg/prod')->params(array(
					'oldcod'=>$faprod['cod'],
					'unidad'=>$faprod['unidad']['_id'],
				))->get('oldcod')->items;
				//$producto_get = $f->model('lg/prod')->params(array('nomb'=>$faprod['nomb']))->get('nomb')->items;
				//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$faprod['generico']))->get('nomb')->items;
				if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$faprod['nomb']))->get('nomb')->items;
				//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$faprod['generico']))->get('generico')->items;
				if(is_null($producto_get)){
					#GENERAR CODIGO LOGISTICA
					$cod = $f->model('lg/prod')->get('cod');
					if($cod->items==null) $cod->items="000001";
					else{
						$tmp = intval($cod->items);
						$tmp++;
						$tmp = (string)$tmp;
						for($i=strlen($tmp); $i<6; $i++){
							$tmp = '0'.$tmp;
						}
						$cod->items = $tmp;
					}
					#INSERTAR PRODUCTO
					$a_prod=array(
						'oldcod'		=>$faprod['cod'],
						'old_id'		=>$faprod['_id'],
						'autor'			=>$faprod['autor'],
						'trabajador'	=>$faprod['autor'],
						'fecreg' 		=>$faprod['fecreg'],
						'generico' 		=>$faprod['generico'],
						'fecmod'		=>new MongoDate(),
						'cod'			=>$cod->items,
						'modulo'		=>'FA',
						'nomb'			=>$faprod['nomb'],
						'descr'			=>$faprod['generico'],
						'estado'		=>"H",
						'precio'		=>floatval($faprod['precio']),
						'descr'			=>$faprod['generico'],
						'precio_venta'	=>floatval($faprod['precio']),
						'tipo_producto' =>"P",
						'unidad' 		=>$faprod['unidad'],
						'clasif'		=>array(
							"_id" => new MongoId("51f281b04d4a13c4040000a9"),
	 						"cod" => "2.3.1.8.1.99",
							"nomb" => "OTROS PRODUCTOS SIMILARES",
						),
						'cuenta'		=>array(
							"_id" 	=> new MongoId("51a9088f4d4a1328070000d1"),
	 						"cod" 	=> "1301.0801",
							"nomb" 	=> "Productos Farmaceuticos",
						),
					);
					$producto_get = $f->model("lg/prod")->params(array('data'=>$a_prod))->save("insert")->items;
					$producto_get = $f->model('lg/prod')->params(array('oldcod'=>$faprod['cod']))->get('oldcod')->items;
				}
				else{
					#SI NO ESTA SETEADO EL MODULO
					if(!isset($producto_get['modulo'])){
							$a_prod=array(
								'modulo'=>"FA",
							);
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI EL MODULO ESTA SETEADO CON UN CAMPO DIFERENTE A FARMACIA
					if($producto_get['modulo']!='FA'){
						$a_prod=array(
							'modulo'=>"FA",
						);
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI EL CAMPO PRECIO NO ES CORRECTO
					if($producto_get['precio']==0){
						$a_prod=array(
							'precio'=>$faprod['precio'],
							'precio_unitario'=>$faprod['precio'],
							'precio_venta'=>$faprod['precio'],
						);
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI NO ESTA SETEADO EL OLDCOD
					if(!isset($producto_get['oldcod'])){
							$a_prod=array(
								'oldcod'=>$faprod['cod'],
							);
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}
					#SI NO ESTA SETEADO EL OLD_id
					if(!isset($producto_get['old_id'])){
							$a_prod=array(
								'old_id'=>$faprod['_id'],
							);
							$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
					}

				}
				#***************************************************************************
				#	C: ANADIR Y CREAR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
				#***************************************************************************
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
			}		
		#*********************************************************************************
		#	PASO 3: VACIAR STOCKS Y MOVIMIENTOS TANTO EN FARMACIA COMO EN LOGISTICA (MANUAL)
		#*********************************************************************************
			#LIMPIAR LOS STOCKS EN LA COLECCION DE FARMACIA
			$f->model('fa/prod')->params(array(
				'filter'=>array('stock'=>array('$exists'=>true)),
				'data'=>array('$set'=>array('stock'=>0))
			))->save('custom_all');
			#LIMPIAR LOTES Y MOVIMIENTOS EN FARMACIA
			$f->model('fa/lote')->save('clear_all');
			$f->model('fa/movi')->save('clear_all');
			#LIMPIAR LOS STOCKS EN LA COLECCION DE LOGISTICA
			$f->model('lg/stck')->params(array(
				'filter'=>array('almacen'=>$id_almacen),
				'data'=>array('$set'=>array(
					'stock'=>0,
					'costo'=>0,
				))
			))->save('custom_all');
			#-------------ADVERTENCIA----------------
			#Si desbloqueas estas lineas, borraras todos los lotes y movimientos de logistica
			#desbloquear solo si sabes lo que haces
			#LIMPIAR LOTES Y MOVIMIENTOS EN LOGISTICA
			#$f->model('fa/lote')->save('clear_all');
			#$f->model('fa/movi')->save('clear_all');
			$movimien = $f->datastore->lg_movimientos->remove(array('modulo'=>'FA'));

		#*********************************************************************************
		#	PASO 4: IMPORTAR GUIAS DE TEMP
		#*********************************************************************************
		$temporales = $f->datastore->fa_temp_logistica->drop();
			$guias = $f->model('fa/guia')->get('all')->items;
	    	foreach ($guias as $guia){
	      		$f->model('fa/temp')->params(array(
					'data'=>$guia,
				))->save('insert')->items;
			}
		#*********************************************************************************
		#	PASO 5: EXPORTAR COMPROBANTES A TEMP  
		#*********************************************************************************
			$sort = array('_id'=>-1);
			$comprobantes = $f->datastore->cj_comprobantes->find(array(
				'estado'=>'R',
				'modulo'=>'FA'
			),array(
				'estado'=>1,
				'modulo'=>1,
				'fecreg'=>1
			))->sort($sort);
			foreach ($comprobantes as $i=>$comp) {
				$f->model('fa/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
		#*********************************************************************************
		#	PASO 6: EXPORTAR ECOMPROBANTES A TEMP  
		#*********************************************************************************
			$sort = array('_id'=>-1);
			$comprobantes_firmados = $f->datastore->cj_ecomprobantes->find(array(
				'estado'=>'FI',
				'items.tipo'=>'farmacia'
			),array(
				'estado'=>1,
				'items.tipo'=>1,
				'fecreg'=>1
			))->sort($sort);
			foreach ($comprobantes_firmados as $i=>$comp) {
				$f->model('fa/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
			$comprobantes_enviados = $f->datastore->cj_ecomprobantes->find(array(
				'estado'=>'ES',
				'items.tipo'=>'farmacia'
			),array(
				'estado'=>1,
				'items.tipo'=>1,
				'fecreg'=>1
			))->sort($sort);
			foreach ($comprobantes_enviados as $i=>$comp) {
				$f->model('fa/temp')->params(array(
					'data'=>$comp,
				))->save('insert')->items;
			}
		#*********************************************************************************
		#	PASO 7: IMPORTAR MOVIMIENTOS DE TEMP
		#*********************************************************************************
			#Esta nueva version arregla los problemas de $sort solo colocando los siguientes
			#_id
			#'modulo'
			#'cliente'
			#'cliente_nomb'
			#'fecreg'
			//die();
			$sort = array('fecreg'=>1); #En orden ascendente;
			#$sort = array('_id'=>1); #En orden ascendente;
			$temporales = $f->datastore->fa_temp_logistica->find()->sort($sort);
			foreach ($temporales as $i=>$temporal_s) {
				$temporal = $f->model("fa/guia")->params(array('_id'=>$temporal_s['_id']))->get("one")->items;
				if(is_null($temporal)) $temporal = $f->model("cj/comp")->params(array('_id'=>$temporal_s['_id']))->get("one")->items;
				if(is_null($temporal)) $temporal = $f->model("cj/ecom")->params(array('_id'=>$temporal_s['_id']))->get("one")->items;
				if(is_null($temporal)) {
					print_r($temporal);
					die();
				}
				#SI NO ESTA SETEADO EL CAMPO MODULO, NI EL CAMPO CLIENTE ES UNA GUIA DE REMISION
				if(!isset($temporal['modulo']) && !isset($temporal['cliente']) && !isset($temporal['cliente_nomb'])){
					foreach ($temporal['items'] as $j=>$item){
						#ACTUALIZAR EL PRODUCTO EN LA COLECCION DE FARMACIA
						$producto = $f->model("fa/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
						if(isset($producto['stock'])){
							$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
								'$inc'=>array('stock'=>floatval($item['cant']))
							)))->save("custom");
						}else{
							$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
								'$set'=>array('stock'=>$item['cant'])
							)))->save("custom");
						}
						#***********************************************************************************
						#	ACTUALIZAR E INSERTAR LOS PRODUCTOS EN LA COLECCION DE LOGISTICA
						#***********************************************************************************
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
						$producto_fa = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
						if(is_null($producto_get)) $producto_get = $f->model("lg/prod")->params(array('old_id'=>$producto_fa['_id']))->get("old_id")->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldcod'=>$item['producto']['cod']))->get('oldcod')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array(
							'oldcod'=>$producto_fa['cod'],
							'unidad'=>$faprod['unidad']['_id'],
						))->get('oldcod')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;

						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$producto_fa['nomb']))->get('nomb')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$item['producto']['generico']))->get('generico')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$producto_fa['generico']))->get('generico')->items;
						/*if(is_null($producto_get)){
							#GENERAR COD
							$cod = $f->model('lg/prod')->get('cod');
							if($cod->items==null) $cod->items="000001";
							else{
								$tmp = intval($cod->items);
								$tmp++;
								$tmp = (string)$tmp;
								for($i=strlen($tmp); $i<6; $i++){
									$tmp = '0'.$tmp;
								}
								$cod->items = $tmp;
							}
							#INSERTAR PRODUCTO
							$a_prod=array(
								'oldid'			=>$item['producto']['cod'],
								'autor'			=>$producto_fa['autor'],
								'trabajador'	=>$producto_fa['autor'],
								'fecreg' 		=>$producto_fa['fecreg'],
								'generico' 		=>$producto_fa['generico'],
								'fecmod'		=>new MongoDate(),
								'cod'			=>$cod->items,
								'modulo'		=>'FA',
								'nomb'			=>$item['producto']['nomb'],
								'descr'			=>$item['producto']['generico'],
								'estado'		=>"H",
								'precio'		=>floatval($producto_fa['precio']),
								'descr'			=>$item['producto']['generico'],
								'precio_venta'	=>floatval($producto_fa['precio']),
								'tipo_producto' =>"P",
								'unidad' 		=>$item['producto']['unidad'],
								'clasif'		=>array(
									"_id" => new MongoId("51f281b04d4a13c4040000a9"),
			 						"cod" => "2.3.1.8.1.99",
									"nomb" => "OTROS PRODUCTOS SIMILARES",
								),
								'cuenta'		=>array(
									"_id" 	=> new MongoId("51a9088f4d4a1328070000d1"),
			 						"cod" 	=> "1301.0801",
									"nomb" 	=> "Productos Farmaceuticos",
								),
							);
							$producto_get = $f->model("lg/prod")->params(array('data'=>$a_prod))->save("insert")->items;
							$producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						}*/
						if(is_null($producto_get)){error_log("GUIA", 0);error_log($item['producto'], 0);}

						/*else{
							if(!isset($producto_get['modulo'])){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['modulo']!='FA'){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['precio']==0){
								$a_prod=array(
									'precio'=>$producto_get['precio'],
									'precio_unitario'=>$producto_get['precio'],
									'precio_venta'=>$producto_get['precio'],
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
						}*/

						if(!isset($producto_get['autor'])); $producto_get['autor']=$f->session->userDBMin;

						#INSERTAR UN LOTE DE FARMACIA
						$lote = array(
							'fecreg'=>$temporal['fecreg'],
							'autor'=>$producto_get['autor'],
							'guia'=>array(
								'_id'=>$temporal['_id'],
								'num'=>$temporal['num']
							),
							'producto'=>array(
								'_id'=>$producto_get['_id'],
								'nomb'=>$producto_get['nomb']
							),
							'proveedor'=>$item['proveedor'],
							'cant_ini'=>$item['cant'],
							'cant'=>floatval($item['cant'])
						);
						$lote['producto']['_id']=$producto_get['_id'];

						if($item['fec']!='')
							$lote['fecven'] = $item['fec'];
						$f->model("fa/lote")->params(array('data'=>$lote))->save("insert")->items;
						$lote = $f->model("fa/lote")->params(array('producto'=>$producto_get['_id']))->get("lote")->items;

						#INSERTAR LOS MOVIMIENTOS EN LA COLECCION DE FARMACIA
						$f->model("fa/movi")->params(array('data'=>array(
							'fecreg'=>$temporal['fecreg'],
							'fec'=>$temporal['fecreg'],
							'autor'=>$f->session->userDBMin,
							'producto'=>$item['producto'],
							'cant'=>$item['cant'],
							'lote'=>$lote['_id'],
							'estado'=>'E',
							'guia'=>array(
								'_id'=>$temporal['_id'],
								'num'=>$temporal['num']
							)
						)))->save("insert")->items;

						#***********************************************************************************
						#	ANADIR Y CREAR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
						#************************************************************************************
						$stock = $f->model("lg/stck")->params(array(
							"filter"=>array(
								"almacen"=>$id_almacen,
								"producto"=>$producto_get['_id']
							)
						))->get('one_custom')->items;		
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
						$saldo = $f->model("lg/movi")->params(array(
							'filter'=>array(
								'stock'=>$stock['_id'],
								'almacen._id'=>$id_almacen,
								'producto._id'=>$producto_get['_id'],
								'modulo'=>'FA',
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
						$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
						#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
						$saldo_cant = $stock_temp['stock'];
						$saldo_monto = $stock_temp['costo'];
						$stock_actual = floatval($saldo_cant);
						$precio_unitario=$producto_get['precio'];

						#************************************************
						#* REGISTRAR EL MOVIMIENTO -> KARDEX
						#************************************************
						#COMO FUNCIONA LOS MOVIMIENTOS
						//ENTRADA FISICO O SALIDA FISICO = cant
						//ENTRADA VALORADO O SALIDA VALORADO = total
						//PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
						//SALDO FISICO = saldo
						//SALDO VALORADO = saldo_imp
						$f->model("lg/movi")->params(array('data'=>array(
							'glosa'=>'INGRESO DE PRODUCTOS CON GUIA '.$temporal['num'],
							'organizacion'=>array(
		                        '_id'=>new MongoId('57b325908e73582808000032'),
		                    	'nomb'=>utf8_encode('Botica Moises Heresi')   
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
		                 	'documento'=>array(
								'_id'=>$temporal['_id'],
								'cod'=>$temporal['num'],
								'tipo'=>'GR'
							),
							'guia'=>array(
								'_id'=>$temporal['_id'],
								'num'=>$temporal['num']
							),
		                    'producto'=>array(
		                        "_id"=>$producto_get['_id'],
		                        "cod"=>$producto_get['cod'],
		                        "nomb"=>$producto_get['nomb']
		                    ),
		                    'modulo'=>"FA",
		                    'fecreg'=>$temporal['fecreg'],
							'fecmod'=>new MongoDate(),
		                    'lote'=>$lote['_id'],
		                    'stock'=>$stock['_id'],
		                    'tipo'=>'E',
		                    "almacen"=>array(
									"_id"=>$id_almacen,
									"nomb"=>$nomb_almacen,
							),
		                    'autor'=>$f->session->userDBMin,
							'trabajador'=>$f->session->userDBMin,
		                    'guia'=>$temporal['_id'],
		                    'lote'=>$lote['_id'],
		                    'fec'=>$temporal['fecreg'],
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
								'costo'=>$saldo_monto+floatval($item['cant'])*floatval($precio_unitario),
							)
						))->save("update");
					}
				}
				#DE ESTAR SETEADO Y EL CAMPO CLIENTE ES UN COMPROBANTE MANUAL
				if(isset($temporal['modulo']) && isset($temporal['cliente']) && !isset($temporal['cliente_nomb'])){
					foreach ($temporal['items'] as $j=>$item){
						#Reducir stock de FARMACIA
						$producto_fa=$f->model('fa/prod')->params(array(
							'_id'=>$item['producto']['_id'],
							'data'=>array('$inc'=>array(
								'stock'=>-floatval($item['cant'])
							))
						))->save('custom')->items;
						#***********************************************************************************
						#	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
						#***********************************************************************************
						$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
						$producto_fa = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
						if(is_null($producto_get)) $producto_get = $f->model("lg/prod")->params(array('old_id'=>$producto_fa['_id']))->get("old_id")->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldcod'=>$item['producto']['cod']))->get('oldcod')->items;
						if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array(
							'oldcod'=>$producto_fa['cod'],
							'unidad'=>$faprod['unidad']['_id'],
						))->get('oldcod')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$producto_fa['nomb']))->get('nomb')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$item['producto']['generico']))->get('generico')->items;
						//if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('generico'=>$producto_fa['generico']))->get('generico')->items;
						//if(is_null($producto_get)){print_r("COMPROBANTE MANUAL <br>");print_r($item['producto']);}
						if(is_null($producto_get)){error_log("COMPROBANTE MANUAL", 0);error_log($item['producto'], 0);}
						/*if(is_null($producto_get)){
							#GENERAR COD
							$cod = $f->model('lg/prod')->get('cod');
							if($cod->items==null) $cod->items="000001";
							else{
								$tmp = intval($cod->items);
								$tmp++;
								$tmp = (string)$tmp;
								for($i=strlen($tmp); $i<6; $i++){
									$tmp = '0'.$tmp;
								}
								$cod->items = $tmp;
							}
							#INSERTAR PRODUCTO
							$a_prod=array(
								'oldid'			=>$item['producto']['cod'],
								'autor'			=>$producto_fa['autor'],
								'trabajador'	=>$producto_fa['autor'],
								'fecreg' 		=>$producto_fa['fecreg'],
								'generico' 		=>$producto_fa['generico'],
								'fecmod'		=>new MongoDate(),
								'cod'			=>$cod->items,
								'modulo'		=>'FA',
								'nomb'			=>$item['producto']['nomb'],
								'descr'			=>$item['producto']['generico'],
								'estado'		=>"H",
								'precio'		=>floatval($producto_fa['precio']),
								'descr'			=>$item['producto']['generico'],
								'precio_venta'	=>floatval($producto_fa['precio']),
								'tipo_producto' =>"P",
								'unidad' 		=>$item['producto']['unidad'],
								'clasif'		=>array(
									"_id" => new MongoId("51f281b04d4a13c4040000a9"),
			 						"cod" => "2.3.1.8.1.99",
									"nomb" => "OTROS PRODUCTOS SIMILARES",
								),
								'cuenta'		=>array(
									"_id" 	=> new MongoId("51a9088f4d4a1328070000d1"),
			 						"cod" 	=> "1301.0801",
									"nomb" 	=> "Productos Farmaceuticos",
								),
							);
							$producto_get = $f->model("lg/prod")->params(array('data'=>$a_prod))->save("insert")->items;
							$producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;
						}
						else{
							if(!isset($producto_get['modulo'])){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['modulo']!='FA'){
								$a_prod=array(
									'modulo'=>"FA",
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
							if($producto_get['precio']==0){
								$a_prod=array(
									'precio'=>$producto_get['precio'],
									'precio_unitario'=>$producto_get['precio'],
									'precio_venta'=>$producto_get['precio'],
								);
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$producto_get['_id'],'data'=>$a_prod))->save("update")->items;
							}
						}*/
						$stock = $f->model("lg/stck")->params(array(
							"filter"=>array(
								"almacen"=>$id_almacen,
								"producto"=>$producto_get['_id']
							)
						))->get('one_custom')->items;		
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
						$saldo = $f->model("lg/movi")->params(array(
							'filter'=>array(
								'stock'=>$stock['_id'],
								'almacen._id'=>$id_almacen,
								'producto._id'=>$producto_get['_id'],
								'modulo'=>'FA',
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
						$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
						#SE CHANCARA EL VALOR DEL MOVIMIENTO POR EL DEL STOCK
						$saldo_cant = $stock_temp['stock'];
						$saldo_monto = $stock_temp['costo'];


						$stock_actual = floatval($saldo_cant);
						$precio_unitario=$producto_get['precio'];
						#Reducir stock de LOTES
						$lote = $f->model('fa/lote')->params(array(
							'producto'=>$producto_get['_id']
						))->get('lote')->items;
						$f->model('fa/lote')->params(array(
							'_id'=>$lote['_id'],
							'data'=>array('$inc'=>array(
								'cant'=>-$item['cant']
							))
						))->save('custom');
						#Generar reducciOn del movimiento
						$f->model("fa/movi")->params(array('data'=>array(
							'fecreg'=>$temporal['fecreg'],
							'fec'=>$temporal['fecreg'],
							'autor'=>$f->session->userDBMin,
							'producto'=>$item['producto'],
							'cant'=>-$item['cant'],
							'lote'=>$lote['_id'],
							'estado'=>'E',
							'comprobante'=>array(
								'_id'=>$temporal['_id'],
								'serie'=>$temporal['serie'],
								'num'=>$temporal['num']
							)
						)))->save("insert")->items;
						//if(isset($producto_get)){
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
			             	'documento'=>array(
								'_id'=>$temporal['_id'],
								'cod'=>$temporal['num'],
								'serie'=>$temporal['serie'],
								'tipo'=>$temporal['tipo'],
							),
			                'organizacion'=>array(
			                    '_id'=>new MongoId('57b325908e73582808000032'),
			                	'nomb'=>utf8_encode('Botica Moises Heresi')   
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
			                'fecreg'=>$temporal['fecreg'],
							'fecmod'=>new MongoDate(),
			                'comprobante'=>array(
	                        	'_id'=>$temporal['_id'],
	                            'serie'=>$temporal['serie'],
	                            'num'=>$temporal['num'],
							),
			                'fec'=>$temporal['fecreg'],
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
				#DE ESTAR SETEADO, CAMPO CLIENTE_NOMB ES UN COMPROBANTE ELECTRONICO
				#OJO, LOS ELECTRONICOS DEPENDE DE LOGISTICA SI O SI
				if(!isset($temporal['modulo']) && !isset($temporal['cliente']) && isset($temporal['cliente_nomb'])){
					foreach ($temporal['items'] as $j=>$item){
						foreach ($item['conceptos'] as $k => $concepto) {
							if(isset($concepto['producto'])){
								#************************************************************************************
								#	DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
								#************************************************************************************
								$producto_get = $f->model("lg/prod")->params(array('_id'=>$concepto['producto']['_id']))->get("one")->items;
								if(is_null($producto_get)){print_r("COMPROBANTE ELECTRONICO");print_r($concepto['producto']);}

								$stock = $f->model("lg/stck")->params(array(
									"filter"=>array(
										"almacen"=>$id_almacen,
										"producto"=>$producto_get['_id']
									)
								))->get('one_custom')->items;
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
								$saldo = $f->model("lg/movi")->params(array(
									'filter'=>array(
										'stock'=>$stock['_id'],
										'almacen._id'=>$id_almacen,
										'producto._id'=>$producto_get['_id'],
										'modulo'=>'FA',
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
								$stock_temp = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$producto_get['_id'])))->get('one_custom')->items;
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
					             	'documento'=>array(
										'_id'=>$temporal['_id'],
										'cod'=>$temporal['numero'],
										'serie'=>$temporal['serie'],
										'tipo'=>$temporal['tipo'],
									),
					                'organizacion'=>array(
					                    '_id'=>new MongoId('57b325908e73582808000032'),
					                	'nomb'=>utf8_encode('Botica Moises Heresi')   
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
					                'fecreg'=>$temporal['fecreg'],
									'fecmod'=>new MongoDate(),
					                'comprobante'=>array(
			                        	'_id'=>$temporal['_id'],
			                            'serie'=>$temporal['serie'],
			                            'num'=>$temporal['numero'],
									),
					                'fec'=>$temporal['fecreg'],
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
				unset($temp);
			}
	}
	function execute_comparar_stocks(){
		global $f;
		$productos = $f->datastore->fa_productos->find(array());
		$alma=$f->model("lg/alma")->get("farmacia")->items;
		$id_almacen = new MongoId($alma['_id']);
		$nomb_almacen = $alma['nomb'];
		$fecreg = new MongoDate();
		$c=0;
		$p=0;
		$n=0;
		foreach ($productos as $i=>$prod_fa){
			$prod_lg = $f->model("lg/prod")->params(array('old_id'=>$prod_fa['_id']))->get("old_id")->items;
			$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$prod_lg['_id'])))->get('one_custom')->items;
			if(is_null($prod_lg)){
				echo "<pre>";
				echo "PRODUCTO QUE NO SE PRODUCTO:";
				echo "<br>";
				print_r($prod_fa['nomb']);
				echo "<br>";
				print_r($prod_fa['stock']);
				echo "<br>";
				echo "</pre>";
				$p++;
			}
			elseif(is_null($stock['stock'])){
				echo "<pre>";
				echo "PRODUCTO QUE NO SE ENCUENTRA STOCK:";
				echo "<br>";
				print_r($prod_fa['nomb']);
				echo "<br>";
				print_r($prod_fa['stock']);
				echo "<br>";
				print_r($prod_lg['nomb']);
				echo "<br>";
				echo "</pre>";
				$n++;
			}
			elseif($prod_fa['stock']!=$stock['stock']){
				echo "<pre>";
				echo "PRODUCTO QUE DISCREPA MOVIMIENTO:";
				echo "<br>";
				print_r($prod_fa['nomb']);
				echo "<br>";
				print_r($prod_fa['stock']);
				echo "<br>";
				print_r($prod_lg['nomb']);
				echo "<br>";
				print_r($stock['stock']);
				echo "<br>";
				echo "</pre>";
				$c++;
			}
		}
		echo "<pre>";
		echo "no lgprod:";
		print_r($p);
		echo "<br>";
		echo "no estock:";
		print_r($n);
		echo "<br>";
		echo "TOTAL:";
		print_r($c);
		echo "<br>";
		echo "</pre>";
	}
}
?>