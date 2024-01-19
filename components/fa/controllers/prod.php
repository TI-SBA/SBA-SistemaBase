<?php
class Controller_fa_prod extends Controller {
	/*function execute_lista_legacy(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("fa/prod")->params($params)->get("lista") );
	}*/
	/*function execute_get_legacy(){
		global $f;
		$items = $f->model("fa/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}*/
	/*function execute_save_legacy(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['unidad']))
			$data['unidad']['_id'] = new MongoId($data['unidad']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['stock'] = 0;
			$model = $f->model("fa/prod")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'FA',
				'bandeja'=>'Inventario',
				'descr'=>'Se creó el Producto <b>'.$data['nomb'].'</b> con precio '.$data['precio'].'.'
			))->save('insert');
		}else{
			$model = $f->model("fa/prod")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			$vari = $f->model("fa/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'FA',
				'bandeja'=>'Inventario',
				'descr'=>'Se actualizó el Producto <b>'.$vari['nomb'].'</b> con precio '.$vari['precio'].'.'
			))->save('insert');
		}
		$f->response->json($model);
	}*/
	/*function execute_save_lote_legacy(){
		global $f;
		$data = $f->request->data;
		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDBMin;
		foreach ($data['items'] as $i=>$item) {
			$data['items'][$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
			$data['items'][$i]['producto']['unidad']['_id'] = new MongoId($item['producto']['unidad']['_id']);
			$data['items'][$i]['cant'] = floatval($item['cant']);
			if($item['fec']!='')
				$data['items'][$i]['fec'] = new MongoDate(strtotime($item['fec']));
		}
		$model = $f->model("fa/guia")->params(array('data'=>$data))->save("insert")->items;
		foreach ($data['items'] as $i=>$item){
			$lote = array(
				'fecreg'=>new MongoDate(),
				'autor'=>$f->session->userDBMin,
				'guia'=>array(
					'_id'=>$model['_id'],
					'num'=>$model['num']
				),
				'producto'=>$item['producto'],
				'proveedor'=>$item['proveedor'],
				'laboratorio'=>$item['laboratorio'],
				'cant_ini'=>$item['cant'],
				'cant'=>$item['cant']
			);
			if($item['fec']!='')
				$lote['fecven'] = $item['fec'];
			$lote = $f->model("fa/lote")->params(array('data'=>$lote))->save("insert")->items;
			$f->model("fa/movi")->params(array('data'=>array(
				'fecreg'=>new MongoDate(),
				'autor'=>$f->session->userDBMin,
				'producto'=>$item['producto'],
				'cant'=>$item['cant'],
				'lote'=>$lote['_id'],
				'estado'=>'E',
				'guia'=>array(
					'_id'=>$model['_id'],
					'num'=>$model['num']
				)
			)))->save("insert")->items;
			$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
				'$inc'=>array('stock'=>$item['cant'])
			)))->save("custom");
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'FA',
			'bandeja'=>'Inventario',
			'descr'=>'Se ingreso un Lote segun Guia de Remision <b>'.$data['num'].'</b>.'
		))->save('insert');
		$f->response->json($model);
	}*/
	/*function execute_save_lote_no_terminado(){
		global $f;
		$data = $f->request->data;
		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDBMin;
		$response= array(
			'status' => 'error', 
			'message' => 'Error: se desconoce un error',
			'data' => array(),
		);		
		try {
			$hoy=mktime(0);
			if (!isset($data['items'])) throw new Exception("Error: no se recibio ningun lote de medicamentos");
			if (!isset($data['num'])) throw new Exception("Error: no se recibio un numero de guia de remisión");
			if (!isset($item['colec'])) $item['colec']='LG';
			$alma=$f->model("lg/alma")->get("farmacia")->items;
			if(isset($data['alamacen'])){
				$data['alamacen']['_id'] = new MongoId($data['alamacen']['_id']);
				$items = $f->model("lg/alma")->params(array("_id"=>$data['alamacen']['_id']))->get("one")->items;
				if(!isset($items)) throw new Exception("Error: El _id de almacen enviado no es correcto");
			}
			else
				$data['almacen'] = new MongoId($alma['_id']);

			foreach ($data['items'] as $i=>$item) {
				if (!isset($item['proveedor'])) throw new Exception("Error: no se recibio un proveedor");
				if (!isset($item['laboratorio'])) throw new Exception("Error: no se recibio un laboratorio");
				if (isset($data['items'][$i]['producto'])){
					if(!isset($data['items'][$i]['producto']['_id'])) throw new Exception("Error: no se recibio el _id de producto en el item  ".$i);
					if(!isset($data['items'][$i]['producto']['unidad']['_id'])) throw new Exception("Error: no se recibio el _id de unidad en el item ".$i);
					if(!isset($data['items'][$i]['cant'])) throw new Exception("Error: no se recibio la cantidad especificada en el item ".$i);

					$data['items'][$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
					$data['items'][$i]['producto']['unidad']['_id'] = new MongoId($item['producto']['unidad']['_id']);
					$data['items'][$i]['cant'] = floatval($item['cant']);
					//if($item['fec']!='')
					if(!empty($item['fec']))
						$data['items'][$i]['fec'] = new MongoDate(strtotime($item['fec']));
					else throw new Exception("Error: se recibio una fecha vacia de vencimiento para el item ".$i);
					}
					else throw new Exception("Error: el item ".$i." no presenta un producto.");	
				}
			$guia = $f->model("fa/guia")->params(array('data'=>$data))->save("insert")->items;
			if(is_null($guia))
				throw new Exception("Error: la guia de remision no se inserto correctamente");

			foreach ($data['items'] as $i=>$item){

				$stck=$f->model("lg/stck")->params(
					array(
						array(
							'producto'=>$item['producto']['_id'],
							'almacen'=>$data['almacen'],
							),
						'data'=>array(
							'$inc'=>array(
								'stock'=>$item['cant']
								)
				)))->save("custom_al_pr");
				
				if(is_null($stck))
					throw new Exception("Error: el stock no se inserto correctamente");

				$prod = $f->model("lg/lote")->params(array('data'=>$lote))->get("one")->items;
				if(is_null($prod))
					throw new Exception("Error: el producto no existe");

				$lote = array(
					'fecreg'=>new MongoDate(),
					'autor'=>$f->session->userDBMin,
					'guia'=>array(
						'_id'=>$guia['_id'],
						'num'=>$guia['num']
					),
					'almacen'=>$data['almacen'],
					'producto'=>$item['producto'],
					'stock'=> $stck['_id'],
					'proveedor'=>$item['proveedor'],
					'laboratorio'=>$item['laboratorio'],
					'cant_ini'=>$item['cant'],
					'cant'=>$item['cant'],
					'colec'=>'LG',
				);
				if(!empty($item['fec']))
					$lote['fecven'] = $item['fec'];

				if(is_null($stck))
					throw new Exception("Error: el stock no se inserto correctamente");

				$lote = $f->model("fa/lote")->params(array('data'=>$lote))->save("insert")->items;

				if(is_null($lote))
					throw new Exception("Error: el lote no se inserto correctamente");

				$f->model("lg/movi")->params(array('data'=>array(
					'fecreg'=>new MongoDate(),
					'autor'=>$f->session->userDBMin,
					'documento' => array(
						'_id' => NULL,
						'cod' => NULL,
						'tipo' => NULL,
					),
					//'glosa' => NULL,
					'almacen' => $data['almacen'],
					'producto'=> $item['producto'],
					'cant'=>$item['cant'],
					'lote'=>$lote['_id'],
					'tipo'=>'E',
					'colec'=>'LG',
					'guia'=>array(
						'_id'=>$guia['_id'],
						'num'=>$guia['num']
					),
					//'entrada_cant' => $item['cant'],
   					//'entrada_monto' => NULL,
					//'salida_cant'=> NULL,
  					//'salida_monto'=> NULL,
  					'precio_unitario' => $prod['precio'],
  					//'saldo_cant' => NULL,
  					//'saldo_monto' => NULL,
  					//'periodo' => (date('Y',$test).'-'.date('m',$test)),
				)))->save("insert")->items;

				if(is_null($lote))
					throw new Exception("Error: el movimiento no se inserto correctamente");
			}
				$response['data'] =  array('guia' => $guia, );
				$response['status'] = 'success';
				$response['message'] = 'Exito: la información se actualizó correctamente';
			$f->model('ac/log')->params(array(
				'modulo'=>'FA',
				'bandeja'=>'Inventario',
				'descr'=>'Se ingreso un Lote segun Guia de Remision <b>'.$data['num'].'</b>.'
			))->save('insert');
		}
		catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}*/
	/*function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			$alma=$f->model("lg/alma")->get("farmacia")->items;
			if(isset($data['alamacen'])){
				$data['alamacen']['_id'] = new MongoId($data['alamacen']['_id']);
				$items = $f->model("lg/alma")->params(array("_id"=>$data['alamacen']['_id']))->get("one")->items;
				if(!isset($items)) throw new Exception("Error: El _id de almacen enviado no es correcto");
			}
			else
				$data['almacen'] = new MongoId($alma['_id']);
			if(isset($data['unidad'])){
				if(!isset($data['unidad']['_id'])) throw new Exception("Error: no se recibio un _id de unidad");
				$items = $f->model("lg/unid")->params(array('_id'=>$data['unidad']['_id']))->get("one")->items;
				if(!isset($items)){
					if(!isset($data['unidad']['nomb'])) throw new Exception("Error: el _id ni nombre de unidad");
					$items = $f->model("lg/unid")->params(array('nomb'=>$data['unidad']['nomb']))->get("por_nomb")->items;
					if(!isset($items)) throw new Exception("Error: el _id de unidad eno es correcta, no se encontro por nombre en unidad de logistica");
					unset($data['unidad']);
					//$data['unidad']['_id']=new MongoId($items['_id']);
					//$data['unidad']['nomb']=new MongoId($items['nomb']);
					$data['unidad']['_id']=$items['_id'];
					$data['unidad']['nomb']=$items['nomb'];
				}
			}
			else throw new Exception("Error: no se recibio una unidad de medida");
			//if(!isset($data['cuenta'])) throw new Exception("Error: no se recibio la cuenta contable");
			if(!isset($data['cuenta'])){
				$data['cuenta']=array(
					'_id' => new MongoId("51a908a54d4a1328070000d3"), 
					'cod' => "1301.080102",
					'descr' => "Medicamentos",
				);
			}
			//if(!isset($data['clasif'])) throw new Exception("Error: no se recibio el clasificador");
			if(!isset($data['clasif'])){
				$data['clasif']=array(
					'_id' => new MongoId("51f281944d4a13440a0000e9"),
					'cod' => "2.3.1.8.1.2",
					'descr' => "GASTOS POR LA ADQUISICIÓN DE MEDICAMENTOS PARA PACIENTES DE LOS HOSPITALES, CLÍNICAS,\nPOLICLÍNICOS, ENTRE OTRAS ENTIDADES PÚBLICAS.",
				);
			}

			if(!isset($data['nomb'])) throw new Exception("Error: no se recibio un nombre");
			if(!isset($data['descr'])) throw new Exception("Error: no se recibio una descripcion");
			if(!isset($data['precio'])) throw new Exception("Error: no se recibio un precio");

			if(!isset($data['tipo_producto'])) $data['tipo_producto']='P';
			if(!isset($data['estado'])) $data['estado']='H';
			if(!isset($data['cant'])) $data['cant']=intval(0);
			if(!isset($data['valor_total'])) $data['valor_total']=intval(0);
			if(!isset($data['modulo']))	$data['modulo']='FA';
			if(!isset($item['colec'])) $item['colec']='LG';

			if(!isset($f->request->data['_id'])){
				$data['fecreg'] = new MongoDate();
				$data['autor'] = $f->session->userDBMin;
				$model = $f->model("lg/prod")->params(array('data'=>$data))->save("insert")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'FA',
					'bandeja'=>'Inventario',
					'descr'=>'Se creó el Producto <b>'.$data['nomb'].'</b> con precio '.$data['precio'].'.'
				))->save('insert');
				$data['stock'] = intval(0);
				$a_stocks= array(
					'producto' => $model['_id'],
					'almacen' => $data['almacen'],
					'stock' => intval($data['stock']),
					'precio' => floatval($data['precio']),
				);
				$f->model("lg/stck")->params(array('data'=>$a_stocks))->save("insert")->items;
				$response['data'] = $model;
				$response['status'] = 'success';
				$response['message'] = 'Exito: la información se guardo correctamente';
			}else{
				$model = $f->model("lg/prod")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
				$vari = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
				$stck=$f->model("lg/stck")->params(array('producto'=>$model['_id'],'almacen' => $data['almacen']))->get("prod")->items;
				$a_stocks= array(
					'producto' => $model['_id'],
					'almacen' => $data['almacen'],
					'stock' => intval($data['stock']),
					'precio' => floatval($data['precio']),
				);
				$f->model("lg/stck")->params(array('_id' => $stck['_id'],'data'=>$a_stocks))->save("update")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'FA',
					'bandeja'=>'Inventario',
					'descr'=>'Se actualizó el Producto <b>'.$vari['nomb'].'</b> con precio '.$vari['precio'].'.'
				))->save('insert');
				$response['data'] = $model;
				$response['status'] = 'success';
				$response['message'] = 'Exito: la información se actualizó correctamente';
			}
		}
		catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}*/
	/*function execute_details(){
		global $f;
		$f->response->view("fa/prod.details");
	}*/
	/*function execute_edit(){
		global $f;
		$f->response->view("fa/prod.edit");
	}*/
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		if(isset($f->request->data['almacen']))
			if($f->request->data['almacen']!='')
				$params['almacen'] = new MongoId($f->request->data['almacen']);
		$rpta = $f->model("lg/stck")->params($params)->get("lista");
		if($rpta->items!=null){
			foreach ($rpta->items as $key => $value) {
				$rpta->items[$key]['producto'] = $f->model("lg/prod")->params(array('_id'=>new MongoId($value['producto'])))->get("one")->items;
			}
		}
		$f->response->json( $rpta );
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save_lote(){
		global $f;
		$data = $f->request->data;
		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDBMin;
		foreach ($data['items'] as $i=>$item) {
			$data['items'][$i]['producto']['_id'] = new MongoId($item['producto']['_id']);
			$data['items'][$i]['producto']['unidad']['_id'] = new MongoId($item['producto']['unidad']['_id']);
			$data['items'][$i]['cant'] = floatval($item['cant']);
			if($item['fec']!='')
				$data['items'][$i]['fec'] = new MongoDate(strtotime($item['fec']));
		}
		$model = $f->model("fa/guia")->params(array('data'=>$data))->save("insert")->items;
		foreach ($data['items'] as $i=>$item){
			$lote = array(
				'fecreg'=>new MongoDate(),
				'autor'=>$f->session->userDBMin,
				'guia'=>array(
					'_id'=>$model['_id'],
					'num'=>$model['num']
				),
				'producto'=>$item['producto'],
				'proveedor'=>$item['proveedor'],
				'laboratorio'=>$item['laboratorio'],
				'cant_ini'=>$item['cant'],
				'cant'=>$item['cant']
			);
			if($item['fec']!='')
				$lote['fecven'] = $item['fec'];
			$lote = $f->model("fa/lote")->params(array('data'=>$lote))->save("insert")->items;
			#INSERTAR UN MOVIMIENTO EN LA COLECCION DE FARMACIA POR MOTIVOS DE LEGADO
			$f->model("fa/movi")->params(array('data'=>array(
				'fecreg'=>new MongoDate(),
				'autor'=>$f->session->userDBMin,
				'producto'=>$item['producto'],
				'cant'=>$item['cant'],
				'lote'=>$lote['_id'],
				'estado'=>'E',
				'guia'=>array(
					'_id'=>$model['_id'],
					'num'=>$model['num']
				)
			)))->save("insert")->items;
			#ACTUALIZAR EL STOCK EN FA PROD
			$f->model("fa/prod")->params(array('_id'=>$item['producto']['_id'],'data'=>array(
				'$inc'=>array('stock'=>$item['cant'])
			)))->save("custom");

			/************************************************************************************
			*	AÑADIR Y CREAR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
			************************************************************************************/
			$alma=$f->model("lg/alma")->get("farmacia")->items;
			$id_almacen = new MongoId($alma['_id']);
			$nomb_almacen = $alma['nomb'];
			$fecreg = new MongoDate();

			$producto_get = $f->model("lg/prod")->params(array('_id'=>$item['producto']['_id']))->get("one")->items;
			$producto_fa = $f->model('fa/prod')->params(array('_id'=>$item['producto']['_id']))->get('one')->items;

			if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('oldid'=>$producto_fa['cod']))->get('oldid')->items;
			if(is_null($producto_get)) $producto_get = $f->model('lg/prod')->params(array('nomb'=>$item['producto']['nomb']))->get('nomb')->items;

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
				'glosa'=>'INGRESO DE PRODUCTOS CON GUIA '.$model['num'],
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
             	'documento'=>array(
					'_id'=>$model['_id'],
					'cod'=>$model['num'],
					'tipo'=>'GR'
				),
				'guia'=>array(
					'_id'=>$model['_id'],
					'num'=>$model['num']
				),
                'producto'=>array(
                    "_id"=>$producto_get['_id'],
                    "cod"=>$producto_get['cod'],
                    "nomb"=>$producto_get['nomb']
                ),
                'modulo'=>"FA",
                'fecreg'=>$model['fecreg'],
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
                'guia'=>$model['_id'],
                'lote'=>$lote['_id'],
                'fec'=>$model['fecreg'],
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
		$f->model('ac/log')->params(array(
			'modulo'=>'FA',
			'bandeja'=>'Inventario',
			'descr'=>'Se ingreso un Lote segun Guia de Remision <b>'.$data['num'].'</b>.'
		))->save('insert');
		$f->response->json($model);
	}
	function execute_lote(){
		global $f;
		$f->response->view("fa/guia.edit");
	}
}
?>