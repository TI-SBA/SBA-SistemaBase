<?php
class Controller_ag_prod extends Controller {
	// function execute_lista_legacy(){
	// 	global $f;
	// 	$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
	// 	if(isset($f->request->data['texto']))
	// 		if($f->request->data['texto']!='')
	// 			$params['texto'] = $f->request->data['texto'];
	// 	if(isset($f->request->data['sort']))
	// 		$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
	// 	if(isset($f->request->data['almacen']))
	// 		if($f->request->data['almacen']!='')
	// 			$params['almacen'] = new MongoId($f->request->data['almacen']);
	// 	$rpta = $f->model("lg/stck")->params($params)->get("lista");
	// 	if($rpta->items!=null){
	// 		foreach ($rpta->items as $key => $value) {
	// 			$rpta->items[$key]['producto'] = $f->model("lg/prod")->params(array('_id'=>new MongoId($value['producto'])))->get("one")->items;
	// 		}
	// 	}
	// 	$f->response->json( $rpta );
	// }
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if ($f->request->data['almacen']!="51a79e6a4d4a13280700003e") {# Si no es logistica
			if(isset($f->request->data['texto']))
				if($f->request->data['texto']!='')
					$params['texto'] = $f->request->data['texto'];
			if(isset($f->request->data['sort']))
				$params['sort'] = array(
					$f->request->data['sort']=>floatval($f->request->data['sort_i'])
				);
			if(isset($f->request->data['almacen']))
				if($f->request->data['almacen']!='')
					$params['almacen'] = new MongoId($f->request->data['almacen']);
			$rpta = $f->model("lg/stck")->params($params)->get("lista");
			$lista = (object)['items'=>[]];
			if($rpta->items!=null){
				foreach ($rpta->items as $key => $value) {
					$model=$f->model("lg/prod")->params(array('_id'=>new MongoId($value['producto'])))->get("one")->items;
					if (isset($model['modulo'])) {
						if ($model['modulo'] == $f->request->data['modulo']) {
							$rpta->items[$key]['producto'] = $model;
							$lista->items[]=$rpta->items[$key];
						}
					}
				}
			}
		} else {#ALMACEN CENTRAL DE LOGISTICA OAS
			$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
			$params['modulo'] = 'AG';
			if(isset($f->request->data['texto']))
				if($f->request->data['texto']!='')
					$params['texto'] = $f->request->data['texto'];
			if(isset($f->request->data['sort']))
				$params['sort'] = array(
					$f->request->data['sort']=>floatval($f->request->data['sort_i'])
				);
			$prods = $f->model("lg/prod")->params($params)->get("lista")->items;
			unset($params);
			$params = array("page"=>$f->request->data['page'],"page_rows"=>9999);
			if(isset($f->request->data['texto']))
				if($f->request->data['texto']!='')
					$params['texto'] = $f->request->data['texto'];
			if(isset($f->request->data['sort']))
				$params['sort'] = array(
					$f->request->data['sort']=>floatval($f->request->data['sort_i'])
				);
			if(isset($f->request->data['almacen']))
				if($f->request->data['almacen']!='')
					$params['almacen'] = new MongoId($f->request->data['almacen']);
			$stck = $f->model("lg/stck")->params($params)->get("lista");
			$lista = (object)['items'=>[]];
			if($stck->items!=null){
				foreach ($stck->items as $key => $value) {
					foreach ($prods as $prod) {
						if($value['producto']->{'$id'}==$prod['_id']->{'$id'}){
							$model=$f->model("lg/prod")->params(array('_id'=>new MongoId($value['producto'])))->get("one")->items;
							$stck->items[$key]['producto'] = $model;
							$lista->items[]=$stck->items[$key];
						}
					}
				}
			}
		}
		$f->response->json( $lista );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ag/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$model = $f->model("ag/prod")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AG',
				'bandeja'=>'Inventario',
				'descr'=>'Se creó el Producto <b>'.$data['nomb'].'</b> con precio '.$data['precio'].'.'
			))->save('insert');
		}else{
			$model = $f->model("ag/prod")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			$vari = $f->model("ag/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AG',
				'bandeja'=>'Inventario',
				'descr'=>'Se actualizó el Producto <b>'.$vari['nomb'].'</b> con precio '.$vari['precio'].'.'
			))->save('insert');
		}
		$f->response->json($model);
	}
	/*function execute_save_lote(){
		global $f;
		$data = $f->request->data;
		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDBMin;
		$data['estado'] = 'A';
		if(isset($data['almacen_origen']['_id'])){
			if($data['almacen_origen']['_id']!='0'){
				$data['almacen_origen']['_id'] = new MongoId($data['almacen_origen']['_id']);
			}else{
				$data['almacen_origen'] = null;
			}
		}
		if(isset($data['almacen_destino']['_id'])){
			if($data['almacen_destino']['_id']!='0'){
				$data['almacen_destino']['_id'] = new MongoId($data['almacen_destino']['_id']);
			}else{
				$data['almacen_destino'] = null;	
			}
		}
		foreach ($data['items'] as $i=>$item) {
			$data['items'][$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
			$data['items'][$i]['cant'] = floatval($item['cant']);
			if($item['fec']!='')
				$data['items'][$i]['fec'] = new MongoDate(strtotime($item['fec']));
		}
		$model = $f->model("ag/guia")->params(array('data'=>$data))->save("insert")->items;
		$fecreg = new MongoDate();
		foreach ($data['items'] as $i=>$item) {
			$producto_get = $f->model('lg/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
			if($data['almacen_origen']!=null){
				$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$data["almacen_origen"]["_id"],"producto"=>$item['producto']['_id'])))->get('one_custom')->items;
				if($stock==null){
					$stock = array(
						'_id'=>new MongoId(),
						'producto'=>$item['producto']['_id'],
						'almacen'=>$data["almacen_origen"]["_id"],
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
						$saldo_cant = $saldo[0]['saldo_cant'];
						$saldo_monto = $saldo[0]['saldo_monto'];
					}
				}

				$stock_actual = floatval($saldo_cant);

				//
				//* REGISTRAR EL MOVIMIENTO -> KARDEX
				//
				$mov = array(
					'fecreg'=>$fecreg,
					'documento'=>array(
						'_id'=>$data['_id'],
						'cod'=>$data['num'],
						'tipo'=>'GR'
					),
					'glosa'=>'SALIDA DE PRODUCTOS CON GUIA '.$data['num'],
					//'tipo'=>'E',
					'almacen'=>$data['almacen_origen'],
					'stock'=>$stock['_id'],
					'cant'=>floatval($item['cant']),
					'entrada_cant'=>0,
					'entrada_monto'=>floatval(0),
					'salida_cant'=>floatval($item['cant']),
					'salida_monto'=>0,
					'precio_unitario'=>floatval(0),
					'saldo_cant'=>$stock_actual-floatval($item['cant']),
					'saldo_monto'=>0,
					'periodo'=>date("Y-m",$fecreg->sec)
					//'total'=>floatval($prod['subtotal'])
				);
				$mov['saldo_cant'] = $saldo_cant-$mov['salida_cant'];
				$mov['saldo_monto'] = $saldo_monto-$mov['salida_monto'];
				$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
				$f->model("lg/stck")->params(array(
					'_id'=>$stock['_id'],
					'data'=>array(
						'stock'=>floatval($mov['saldo_cant'])
					)
				))->save("update");
			}
			if($data['almacen_destino']!=null){
				$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$data["almacen_destino"]["_id"],"producto"=>$item['producto']['_id'])))->get('one_custom')->items;
				if($stock==null){
					$stock = array(
						'_id'=>new MongoId(),
						'producto'=>$item['producto']['_id'],
						'almacen'=>$data["almacen_destino"]["_id"],
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
						$saldo_cant = $saldo[0]['saldo_cant'];
						$saldo_monto = $saldo[0]['saldo_monto'];
					}
				}

				$stock_actual = floatval($saldo_cant);

				///************************************************
				//* REGISTRAR EL MOVIMIENTO -> KARDEX
				//**************************************************
				$mov = array(
					'fecreg'=>$fecreg,
					'documento'=>array(
						'_id'=>$data['_id'],
						'cod'=>$data['num'],
						'tipo'=>'GR'
					),
					'glosa'=>'INGRESO DE PRODUCTOS CON GUIA '.$data['num'],
					//'tipo'=>'E',
					'almacen'=>$data['almacen_destino'],
					'stock'=>$stock['_id'],
					'cant'=>floatval($item['cant']),
					'entrada_cant'=>floatval($item['cant']),
					'entrada_monto'=>floatval(0),
					'salida_cant'=>0,
					'salida_monto'=>0,
					'precio_unitario'=>floatval(0),
					'saldo_cant'=>$stock_actual+floatval($item['cant']),
					'saldo_monto'=>0,
					'periodo'=>date("Y-m",$fecreg->sec)
					//'total'=>floatval($prod['subtotal'])
				);
				$mov['saldo_cant'] = $saldo_cant+$mov['entrada_cant'];
				$mov['saldo_monto'] = $saldo_monto+$mov['entrada_monto'];
				$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
				$f->model("lg/stck")->params(array(
					'_id'=>$stock['_id'],
					'data'=>array(
						'stock'=>floatval($mov['saldo_cant'])
					)
				))->save("update");
			}
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'AG',
			'bandeja'=>'Inventario',
			'descr'=>'Se ingreso un Lote segun Guia de Remision <b>'.$data['num'].'</b>.'
		))->save('insert');
		$f->response->json($model);
	}
	*/
	function execute_save_lote(){
		/**
		*	INGRESO Y SALIDA POR GUIAS DE REMISION Y FACTURAS
		*/
		global $f;
		$data = $f->request->data;
		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDBMin;
		$data['estado'] = 'A';
		if(isset($data['almacen_origen']['_id'])){
			if($data['almacen_origen']['_id']!='0'){
				$data['almacen_origen']['_id'] = new MongoId($data['almacen_origen']['_id']);
			}else{
				$data['almacen_origen'] = null;
			}
		}
		if(isset($data['almacen_destino']['_id'])){
			if($data['almacen_destino']['_id']!='0'){
				$data['almacen_destino']['_id'] = new MongoId($data['almacen_destino']['_id']);
			}else{
				$data['almacen_destino'] = null;	
			}
		}
		foreach ($data['items'] as $i=>$item) {
			$data['items'][$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
			$data['items'][$i]['cant'] = floatval($item['cant']);
			if($item['fec']!='')
				$data['items'][$i]['fec'] = new MongoDate(strtotime($item['fec']));
		}
		$model = $f->model("ag/guia")->params(array('data'=>$data))->save("insert")->items;
		$fecreg = new MongoDate();
		foreach ($data['items'] as $i=>$item) {
			$producto_get = $f->model('lg/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;
			#SALIDA DE ALMACEN
			if($data['almacen_origen']!=null){
				$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$data["almacen_origen"]["_id"],"producto"=>$item['producto']['_id'])))->get('one_custom')->items;
				if($stock==null){
					$stock = array(
						'_id'=>new MongoId(),
						'producto'=>$item['producto']['_id'],
						'almacen'=>$data["almacen_origen"]["_id"],
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
				$precio_unitario = $producto_get['precio'];

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
					'glosa'=>'SALIDA DE PRODUCTOS CON GUIA '.$data['num'],
					'organizacion'=>array(
                        '_id'=>new MongoId('57b3250f8e73583808000038'),
                        'nomb'=>utf8_encode('Actividades comerciales DGRE')   
                 	),
                 	'documento'=>array(
						'_id'=>$data['_id'],
						'cod'=>$data['num'],
						'tipo'=>'GR'
					),
                    'producto'=>array(
                        "_id"=>$producto_get['_id'],
                        "cod"=>$producto_get['cod'],
                        "nomb"=>$producto_get['nomb']
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
                    'lote'=>$lote['_id'],
                    'tipo'=>'S',
                    "almacen"=>$data['almacen_origen'],
                    'autor'=>$f->session->userDBMin,
                    'trabajador'=>$f->session->userDBMin,
                    'fecreg'=>$fecreg,
                    'fecmod'=>$fecreg,
                    'guia'=>$model['_id'],
                    'fec'=>date("Y-m-d",$fecreg->sec),
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
			if($data['almacen_destino']!=null){
				$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$data["almacen_destino"]["_id"],"producto"=>$item['producto']['_id'])))->get('one_custom')->items;
				if($stock==null){
					$stock = array(
						'_id'=>new MongoId(),
						'producto'=>$item['producto']['_id'],
						'almacen'=>$data["almacen_destino"]["_id"],
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

				$lote = array(
					'fecreg'=>$model['fecreg'],
					'autor'=>$f->session->userDBMin,
					'guia'=>array(
						'_id'=>$model['_id'],
						'num'=>$model['num']
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
					'glosa'=>'INGRESO DE PRODUCTOS CON GUIA '.$data['num'],
					'organizacion'=>array(
                        '_id'=>new MongoId('57b3250f8e73583808000038'),
                        'nomb'=>utf8_encode('Actividades comerciales DGRE')   
                 	),
                 	'documento'=>array(
						'_id'=>$data['_id'],
						'cod'=>$data['num'],
						'tipo'=>'GR'
					),
                    'producto'=>array(
                        "_id"=>$producto_get['_id'],
                        "cod"=>$producto_get['cod'],
                        "nomb"=>$producto_get['nomb']
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
                    'tipo'=>'E',
                    'almacen'=>$data['almacen_destino'],
                    'autor'=>$f->session->userDBMin,
                    'trabajador'=>$f->session->userDBMin,
                    'fecreg'=>$fecreg,
                    'fecmod'=>$fecreg,
                    'guia'=>$model['_id'],
                    'lote'=>$lote['_id'],
                    'fec'=>date("Y-m-d",$fecreg->sec),
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
		$f->model('ac/log')->params(array(
			'modulo'=>'AG',
			'bandeja'=>'Inventario',
			'descr'=>'Se ingreso un Lote segun Guia de Remision <b>'.$data['num'].'</b>.'
		))->save('insert');
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ag/prod.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ag/prod.details");
	}
	function execute_lote(){
		global $f;
		$f->response->view("ag/guia.edit");
	}
}
?>