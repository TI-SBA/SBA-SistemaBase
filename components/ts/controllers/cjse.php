<?php
class Controller_ts_cjse extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		if(isset($f->request->data['estado']))
			$params['estado'] = $f->request->data['estado'];
		$f->response->json( $f->model("ts/cjse")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ts/cjse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_last(){
		global $f;
		$items = $f->model("ts/cjse")->params(array("_id"=>new MongoId($f->request->data['caja'])))->get("lcaja")->items;
		if (is_null($items)) $items=array();
		$f->response->json( $items );
	}
	/*function execute_save_old(){
		global $f;															
		$data = $f->request->data;																//Recepcion del formulario en $data
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try
		{
			#backend
			$data['fecmod'] = new MongoDate();																										//Fecha de la modificacion
			$data['estado'] = 'A';
			$data['trabajador'] = $f->session->userDBMin;																							//Autor del cambio
			//$data['_id'] = new MongoId();																											//Generar _id
			//$last_movim = $f->model("ts/camo")->params(array())->get("fecreg")->items;															//Cargar el ultimo movimiento
			//$last_caja= $f->model("ts/cjdo")->params(array())->get("fecreg")->items;																//Cargar la ultima caja chica
			#Frontend
			if(!isset($data['caja']))	throw new Exception("Error: No se especifico una caja");
			//print_r($data);
			$last_sesio= $f->model("ts/cjse")->params(array("_id"=>new MongoId($data['caja']['_id'])))->get("lcaja")->items;
			//var_dump($last_sesio);//Cargar la ultima sesion con esa caja
			//$model = $f->model("ts/cjse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;//Cargar el usuario por _id
			#
			if(isset($data['estado']))
			{																//Verificar si se recibe estado
				if ($data['estado'] == 'A')
				{															//Apertura de sesion
					if($last_sesio!=null){
						if($last_sesio['estado']=='A')	throw new Exception("Error: Esta sesion ya se aperturo");
					}
					#Inicializar Backend
					$data['fecreg'] = new MongoDate();																										//Fecha de registro
					$data['fecape'] = new MongoDate();																										//Fecha de Apertura

					if(!isset($data['deber_anterior']))			throw new Exception("Error: No se deber deber_anterior");
					if(!isset($data['rendicion']))				throw new Exception("Error: No se encontro informacion sobre la rendicion");
					if(!isset($data['rendicion']['fecren']))	throw new Exception("Error: No se encontro informacion sobre la fecha rendicion");
					if(!isset($data['rendicion']['tipo']))		throw new Exception("Error: No se encontro informacion sobre el tipo de rendicion");
					if(!isset($data['rendicion']['numero']))	throw new Exception("Error: No se encontro informacion sobre el numero de rendicion");
					if(!isset($data['rendicion']['monto']))	throw new Exception("Error: No se encontro informacion sobre el monto de rendicion");

					$data['rendicion']['fecren'] = new MongoDate(strtotime($data['rendicion']['fecren']));
					$data['deber_anterior'] = floatval($data['rendicion']['monto']);
					$data['rendicion']['monto'] = floatval($data['rendicion']['monto']);

					#Verificar Frontend
					if(!isset($data['responsable']))	throw new Exception("Error: No se tiene un responsable");
					//if(!isset($data['caja']))
					//{																																		//Verificar si hay una caja chica
					//	throw new Exception("Error: No se asigno una caja chica");
					//}
					//else
					//{
					//$last_sesio= $f->model("ts/cjse")->params(array("_id"=>new MongoId($data['caja']['_id'])))->get("get_lcaja")->items;				//Cargar la ultima sesion con esa caja
					//}
					if($last_sesio==null)																												//Verificar si hubo una ultima sesion
					{
						if(!isset($data['saldo_inicial']))	throw new Exception("Error: No se recibio un saldo inicial ni existe una ultima sesion");
						else
						{
							$data['saldo_inicial']=floatval($data['saldo_inicial']);																		//Saldo inicial sera flotante
							$sal_ini=$data['saldo_inicial'];																					//El saldo inicial recibira un nuevo saldo
						}
					}
					else
					{
						$last_sesio['saldo_inicial']=floatval($last_sesio['saldo_inicial']);																//El saldo inicial sera el de la ultima sesion
						$sal_ini=$last_sesio['saldo_inicial'];																					
					}
					#Operaciones
					$data['caja']['_id']=new MongoId($data['caja']['_id']);																					//Arreglar el _id de caja
					$data['responsable']['_id']=new MongoId($data['responsable']['_id']);																	//Arreglar el _id de responsable
					$data['saldo_inicial'] = $sal_ini;
					//print_r($data);
					//die();																										//Arreglar sal_ini
					if(!isset($data['_id'])) $model = $f -> model("ts/cjse")->params(array('data'=>$data))->save("insert")->items;
					else $model = $f->model("ts/cjse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
					#$model = $f -> model("ts/cjse")->params(array('data'=>$data))->save("insert")->items;														//Enviar a la base de datos
					$response['status'] = 'success';
					$response['message'] = utf8_encode('Exito: la sesión se aperturo correctamente.');
					$response['data'] = $model;
				}
				else 																																		//Caso contrario
				{ 																																
					//
					//if($model['estado']=='C')
					//{																																		//Verificar que la sesion ya fue cerrada
					//	throw new Exception("Error: Esta sesion ya fue cerrada");
					//}
					//#Inicializar Backend
					//$data['saldo_final']=$last_movim['saldo'];																								//Cargar el ultimo movimiento
					//#Verificar frontend
					//if(!isset($data['feccie']))																												//Veificar si se envia una fecha de cierre de sesion
					//{															
					//	throw new Exception("Error: No se registro fecha de cierre");
					//}
					//else
					//{
					//	$data['feccie']=new MongoDate(strtotime($data['feccie']));																			//Convertir en formato fecha
					//}
					//$model = $f->model("ts/cjse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;		//Enviar a la base de datos
					//$response['status'] = 'success';
					//$response['message'] = 'la sesión fue cerrada correctamente.';
					throw new Exception("Error: Se recibio otro estado excepto Apertura");
				}
			}
			else	throw new Exception("No se recibio su Apertura o Cierre de la sesion");
		}
		catch (Exception $e) 
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}*/
	function execute_save(){
		global $f;															
		$data = $f->request->data;																													//Recepcion del formulario en $data
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try {
			# Backend
			$data['fecreg'] = new MongoDate();
			$data['fecmod'] = new MongoDate();																										
			$data['estado'] = 'A';
			$data['trabajador'] = $f->session->userDBMin;																							
			# Frontend
			if(!isset($data['caja']))	throw new Exception("Error: No se especifico una caja");
			if(!isset($data['estado'])) throw new Exception("No se recibio su Apertura o Cierre de la sesion");
			if($data['estado'] != 'A') 	throw new Exception("Error: Se recibio otro estado excepto Apertura");
			# Inicializar Backend
			$data['fecreg'] = new MongoDate();																										//Fecha de registro
			$data['fecape'] = new MongoDate();																										//Fecha de Apertura

			if(!isset($data['rendicion']))				throw new Exception("Error: No se encontro informacion sobre la rendicion");
			if(!isset($data['rendicion']['fecren']))	throw new Exception("Error: No se encontro informacion sobre la fecha rendicion");
			if(!isset($data['rendicion']['tipo']))		throw new Exception("Error: No se encontro informacion sobre el tipo de rendicion");
			if(!isset($data['rendicion']['numero']))	throw new Exception("Error: No se encontro informacion sobre el numero de rendicion");
			if(!isset($data['rendicion']['monto']))		throw new Exception("Error: No se encontro informacion sobre el monto de rendicion");

			$data['rendicion']['fecren'] = 	new MongoDate(strtotime($data['rendicion']['fecren']));
			$data['rendicion']['monto']  = 	floatval($data['rendicion']['monto']);

			$last_sesio = $f->model("ts/cjse")->params(array("_id"=>new MongoId($data['caja']['_id'])))->get("lcaja")->items;
			if(!isset($data['saldo_anterior']) || !isset($data['saldo_inicial']) || !isset($data['saldo_final'])){
				# CALCULO DE SALDO INICIAL Y FINAL SEGUN EL BACKEND EN CASO DE QUE NO SE ENVIE DICHA INFORMACIONGFM
				if(!is_null($last_sesio)){
					if($last_sesio['estado']=='A')		throw new Exception("Error: La ultima sesion se mantiene aperturada");
					$hab_ant						=	floatval($last_sesio['haber_final']);
					$deb_ant 						=	floatval($last_sesio['deber_final']);
					$sal_ant 						= 	floatval($last_sesio['saldo_final']);
					$sal_ini 						=   floatval($last_sesio['saldo_final']) + floatval($data['rendicion']['monto']);
					$hab_fin						=	floatval($last_sesio['haber_final']);
					$deb_fin 						=	floatval($last_sesio['deber_final']);
					$sal_fin 						=   floatval($last_sesio['saldo_final']) + floatval($data['rendicion']['monto']);
				} else {
					$hab_ant						=	0;
					$deb_ant 						=	0;
					$sal_ant 						= 	0;
					$sal_ini 						=	0 + floatval($data['rendicion']['monto']);													//El saldo inicial recibira un nuevo saldo   
					$hab_fin						=	0;
					$deb_fin 						=	0;
					$sal_fin 						=   0 + floatval($data['rendicion']['monto']);													//El saldo final recibira un nuevo saldo
				}
			} else 	{
				$hab_ant	=	floatval($data['haber_anterior']);
				$deb_ant 	=	floatval($data['deber_anterior']);
				$sal_ant	=	floatval($data['saldo_anterior']);
				$sal_ini 	=	floatval($data['saldo_inicial']);
				$hab_fin	=	floatval($data['haber_final']);
				$deb_fin 	=	floatval($data['deber_final']);
				$sal_fin 	=   floatval($data['saldo_final']);
			}

			if(!isset($data['responsable']))	throw new Exception("Error: No se tiene un responsable");
			$data['caja']['_id']		=	new MongoId($data['caja']['_id']);																	//Arreglar el _id de caja
			$data['responsable']['_id']	=	new MongoId($data['responsable']['_id']);															//Arreglar el _id de responsable

			$data['haber_anterior']		=	$hab_ant;
			$data['deber_anterior']		=	$deb_ant;
			$data['saldo_anterior'] 	=	$sal_ant;
			$data['saldo_inicial']		=	$sal_ini;
			$data['haber_final']		=	$hab_fin;
			$data['deber_final'] 		=	$deb_fin;
			$data['saldo_final']		=	$sal_fin;


			if(!isset($data['_id'])) 	$model = $f -> model("ts/cjse")->params(array('data'=>$data))->save("insert")->items;
			else 						$model = $f->model("ts/cjse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;	

			$response['status'] = 'success';
			$response['message'] = 'Exito: la sesion se aperturo correctamente.';
			$response['data'] = $model;
		}
		catch (Exception $e) 
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
			$response['data'] = $data;
		}
		$f->response->json($response);
	}
	function execute_cierre(){
		global $f;															
		$data = $f->request->data;																									//Recepcion del formulario en $data
		try{
			if(!isset($data['_id']))	throw new Exception("Error: No se recibio ningun ID");
			if(!isset($data['estado']))	throw new Exception("Error: No se recibio ningun estado");
			if($data['estado']!='C')	throw new Exception("Error: No se entiende el estado");									//Salir si no se entienda el estado
			if(!isset($data['feccie']))	throw new Exception("Error: no se registro fecha de cierre");								//Verificar si se envia una fecha de cierre de sesion

			$last_model = $f->model("ts/cjse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;		//Cargar el usuario sesion por _id
			if(is_null($last_model))	throw new Exception("Error: No hay sesiones existentes, por lo que no se puede cerrar ninguna");
			$last_movim = $f->model("ts/camo")->params(array("_id"=>new MongoId($last_model['caja']['_id'])))->get("lcaja")->items;	//Cargar el ultimo movimiento por _id de caja

			if($last_model['estado']=='C')	throw new Exception("Error: Esta sesion ya fue cerrada");								//Verificar que la sesion ya fue cerrada

			$adata=array();																											//Variable nueva que se usara para actualizar
			#Verificar frontend
			$adata['estado']=$data['estado'];																						//Se cierra el estado

			#Inicializar Backend
			if(is_null($last_movim))	$adata['saldo_final']=floatval($last_model['saldo_inicial']);								//Copiar el saldo inicial si no hay ningun movimiento	
			else						$adata['saldo_final']=floatval($last_movim['saldo']);										//Cargar el ultimo movimiento
			
			$adata['feccie']=new MongoDate(strtotime($data['feccie']));																//Convertir en formato fecha
			
			$model = $f->model("ts/cjse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$adata))->save("update")->items;	//Enviar a la base de datos
			$response['status'] = 'success';
			$response['message'] = utf8_encode('Exito: la sesion fue cerrada correctamente.');
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/cjse.edit");
	}
	function execute_sesion(){
		global $f;
		$f->response->view("ts/cjse.sesion");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/cjse.details");
	}
	function execute_delete(){
		//global $f;
		//$f->model('ts/cjse')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('cjse');
		//$f->response->print("true");
		/*
		*	Borrar sesion
		*	Recibira el ID de la sesion a borrar, y si esa sesion aun tiene documentos, se reusara a borrarlas
		*/
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try{
			if (!isset($data['_id']))		throw new Exception("Error: no se recibio el ID de sesion a eliminar");								//Verificar si se recibio un ID
			$sesion = $f->model("ts/cjse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;					//Cargar sesion por ID
			if(is_null($sesion))			throw new Exception("Error: no se puede eliminar esta sesion ya que su ID no existe");
			if (!isset($sesion['estado']))	throw new Exception("Error: no se puede determinar el estado de esta sesion");
			if($sesion['estado']=='C')		throw new Exception("Error: no se puede eliminar esta sesion ya que esta se encuentra CERRADA");

			//$data['fecmod'] = new MongoDate();																									//Generar fecha de la modificacion
			//$data['trabajador'] = $f->session->userDBMin;																							//Gemerar autor del cambio

			$l_docum= $f->model("ts/cjdo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("lsesion")->items;						//Ultimo documento de esta sesion
			if (!is_null($l_docum))			throw new Exception("Error: No se puede borrar la sesion ya que tiene documentos asignados, Eliminar los documentos");	//Eliminar la sesion
			$f->model('ts/cjse')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('cjse');
			$response['status'] = 'success';
			$response['message'] = utf8_encode('Exito: la sesión se elimino correctamente');
		} catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_reporte(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try{
			$items = $f->model("ts/cjdo")->params(array(
				'filter' => array(
					"sesion._id" => new MongoId($f->request->data['_id']),
				),'sort' => array(
					'fecdoc' => 1, 
				)
			))->get("all")->items;

			//$f->response->view("ts/cjse.report.php",array('sesion'=>$items));			
			$f->response->view("ts/cjse.report.php",array(
				'sesion'=>$items,
				'metadata'=> array(
					'mes' => '06',
					'ano' => '2018'
				)
			));
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_reporte_auxiliar_estandar(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try{
			$items = $f->model("ts/cjdo")->params(array(
				'filter' => array(
					"sesion._id" => new MongoId($f->request->data['_id']),
					"estado" => array('$in' => array('A')),
				),'fields' => array(
					"fecdoc" => 1,
					"tidoc" => 1,
					"num" => 1,
					"beneficiario.nomb" => 1,
					"conce" => 1,
				),'sort' => array(
					'fecdoc' => 1, 
				)
			))->get("all")->items;
			if(is_null($items)) throw new Exception("Error: no se encontraron documentos en esta sesion");
			foreach ($items as $i => $docum) {
				$movim = NULL;
				$movim = $f->model("ts/camo")->params(array(
					'filter' => array(
						"documento" => $docum['_id'],
					),'fields' => array(
						"entrada" => 1,
						"salida" => 1,
						"saldo" => 1,
					)
				))->get("all")->items;
				if(is_null($movim)) throw new Exception("Error: Existe un documento sin movimiento registrado");
				$items[$i]['movimiento'] = $movim;
			}
			$sesion_actual = $f->model("ts/cjse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(is_null($sesion_actual)) throw new Exception("Error: No se encontro la sesion de los documentos");
			if(isset($f->request->data['debug'])){
				echo "<pre>";
				print_r($items); 
				echo "</pre>";
				die();

			}
			//$f->response->view("ts/cjse.report.php",array('sesion'=>$items));			
			$f->response->view("ts/cjse.report.auxiliar.estandar.php",array(
				'sesion_extra'=>$sesion_actual,
				'sesion'=>$items,
				'metadata'=> array(
					'mes' => '06',
					'ano' => '2018'
				)
			));
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_reporte_gastos_publicos(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try{
			$areas=array(
				'5b50d5593e6037db798b4569'	=>	'GA',//OEI
				'5b50d5473e6037dd798b4567'	=>	'GA',//OCT
				'5b50d5343e6037db798b4567'	=>	'GA',//ORH
				'5b50d51b3e6037d9798b4567'	=>	'GA',//OAS

				'5b50d5053e6037d6798b4567'	=>	'AD',//GA
				'5b68aa453e60378c058b4569'	=>	'AD',//DGBS
				'58beaed63e6037312d8b456b'	=>	'AD',//GG
				'578d006b8e7358e40f000045'	=>	'AD',//PRESIDENCIA
				'5b68aa1c3e60378c058b4567'	=>	'AD',//DGRE

				'58beaee73e6037312d8b456d'	=>	'AL',//AL

				'578d05198e73587807000039'	=>	'47',//IN

				'58beaef53e6037452d8b4567'	=>	'OCI',//OCI

				'58beaf023e6037492d8b4567'	=>	'PP',//PP

				'58beaf2d3e6037512d8b4567'	=>	'C.H.',//AG
			);

			$items = $f->model("ts/cjdo")->params(array(
				'filter' => array(
					"sesion._id" => new MongoId($f->request->data['_id']),
					"estado" => array('$in' => array('A')),
				),'fields' => array(
					"partida" => 1,
					"mont" => 1,
					"programa" => 1,
				),'sort' => array(
					'fecdoc' => 1, 
				)
			))->get("all")->items;

			if(is_null($items)) throw new Exception("Error: no se encontraron documentos en esta sesion");
			foreach ($items as $i => $docum) {
				if(!isset($areas[$docum['programa']['_id']->{'$id'}])) {
					$data=array(
						'documento' => $docum,
						'areas' => $areas,
					);
					throw new Exception("Error: No existe una sigla de clasificador de gasto publico para el programa => ".$docum['programa']['nomb']);
				}
				$items[$i]['area'] = $areas[$docum['programa']['_id']->{'$id'}];
			}
			if(isset($f->request->data['debug'])){
				echo "<pre>";
				print_r($items); 
				echo "</pre>";
				die();

			}		
			$f->response->view("ts/cjse.report.gastos.publicos.php",array(
					'documentos'=>$items,
					'metadata'=> array(
						'mes' => '06',
						'ano' => '2018'
					)
			));
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['data'] = $data;
			$response['message'] = $e->getMessage();
		}
		//$f->response->json($response);
		echo json_encode($response);
		die();
	}
	function execute_reset(){
		global $f;
		$response=array
		(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			$data=$f->request->data;
			$estadisticas=array(
				'salidas'=>0,
				'entradas'=>0,
			);
			/********************************************************************************
			*	PASO 1: OBTENER SESION
			********************************************************************************/
			if(!isset($data['sesion'])) throw new Exception("Error: no se envio el id en el campo sesion");
			$sesion=$f->model("ts/cjse")->params(array('_id'=>new MongoId($data['sesion'])))->get("one")->items;
			if(is_null($sesion)) throw new Exception("Error: no se encontro esta sesion");
			/********************************************************************************
			*	PASO 2: OBTENER DOCUMENTOS APROBADOS DE CAJA SESION
			********************************************************************************/
				$docums = $f->model("ts/cjdo")->params(array(
					'filter' => array(
						"sesion._id" => $sesion["_id"],
						"estado" => array('$in' => array('A','C')),
					),
					'sort' => array(
						'fecdoc' => 1, 
					)
				))->get("all")->items;
				if(is_null($docums)) throw new Exception("Error: no se encontro documentos en esta sesion");
			/********************************************************************************
			*	PASO 3: OBTENER MOVIMIENTOS DE SESION DE CAJA CHICA
			*********************************************************************************/
				foreach ($docums as $i => $docum) {
					$movim = $f->model("ts/camo")->params(array(
						'filter' => array(
							"documento" => $docum['_id'],
						),
						'sort' => array(
							'fecdoc' => 1, 
						)
					))->get("all")->items;
					if(is_null($movim)) throw new Exception("Error: Existe un documento sin movimiento registrado");
					$docums[$i]['movimiento'] = $movim;
					
				}
			/********************************************************************************
			*	PASO 4: PROCESAR MOVIMIENTOS PARA QUE RECALCULEN LOS MONTOS
			*********************************************************************************/
				//if(!isset($sesion['deber_anterior'])) 		throw new Exception("Error: no se encontro campo deber_anterior en la sesion");
				//if(!isset($sesion['rendicion'])) 			throw new Exception("Error: no se detecta el campo de rendicion en la sesion");
				//if(!isset($sesion['rendicion']['monto'])) 	throw new Exception("Error: no se detecta el monto de rendicion en la sesion");
				//if(!isset($sesion['rendicion']['fecren'])) 	throw new Exception("Error: no se detecta la fecha de rendicion en la sesion");
				
				/* $saldo = $sesion['saldo_inicial'];
				$movim_update = array(
					'fecreg' => $sesion['rendicion']['fecren'],
					'tipo' => 'E',
					'sesion' => new MongoId($sesion['_id']),
					'documento' => new MongoId($sesion['_id']),
					'entrada' => floatval($sesion['rendicion']['monto']),
					'salida' => 0,
					'saldo' => floatval($sesion['deber_anterior'])+floatval($sesion['rendicion']['monto']),
				);
				$sesion_update = array(
					'saldo_inicial' => $saldo,
					'saldo_final' => $saldo,
					'fecmod' => new MongoDate(),
					'trabajador'=> $f->session->userDBMin,
				);
				*/

			/********************************************************************************
			*	PASO 5: PROCESAR MOVIMIENTOS PARA QUE RECALCULEN LOS MONTOS
			*********************************************************************************/
				if(!isset($sesion['haber_anterior'])) 	throw new Exception("Error: no se encontro campo haber_anterior en la caja chica");
				if(!isset($sesion['deber_anterior'])) 	throw new Exception("Error: no se encontro campo deber_anterior en la caja chica");
				if(!isset($sesion['saldo_anterior'])) 	throw new Exception("Error: no se encontro campo saldo_anterior en la caja chica");
				if(!isset($sesion['saldo_inicial'])) 	throw new Exception("Error: no se encontro campo saldo_inicial en la caja chica");
				$haber = $sesion['haber_anterior'];
				$saldo = $sesion['saldo_inicial'];
				foreach ($docums as $i => $docum) {
					if(!isset($docum['mont'])){
						$data = $docum;
						throw new Exception("Error: no se encontro unn monto en el documento");
					} 
					$movim_update = array(
						'fecreg' => new MongoDate(),
						'tipo'=>'--',
						'sesion'=>new MongoId($sesion['_id']),
						'documento'=>new MongoId($docum['_id']),
						'entrada'=>0,
						'salida'=>0,
						'saldo'=>0
					);
					$sesion_update = array(
						'deber_anterior' => $haber,
						'saldo_final' => $saldo,
						'fecmod' => new MongoDate(),
						'trabajador'=> $f->session->userDBMin,
					);
					$monto  = $docum['mont'];
					if($docum['estado'] == 'A'){
						if(count($docum['movimiento']) !== 1) throw new Exception("Error: se detecto mas de 1 movimiento en un documento aprobado");
						foreach ($docum['movimiento'] as $m => $movim) {
							if(!isset($movim['tipo'])) {
								$data = $movim;
								throw new Exception("Error: No se detecta el campo estado en el movimiento");
							}
							//if($movim['tipo'] !== "S") throw new Exception("Error: El movimiento detectado es un ingreso para un documento aprobado");
							if(!isset($movim['fecreg'])) throw new Exception("Error: no se encontro el campo fecreg en el movimiento");
							$movim_update['tipo']='S';
							$movim_update['salida']=$monto;
							$movim_update['saldo']=$saldo-$monto;
							$movim_update['fecreg'] = $docum['fecreg'];
							$sesion_update['saldo_final']= $movim_update['saldo'];
							$sesion_update['haber_final']= $haber+$monto;
							$movim = $f->model("ts/camo")->params(array('_id' => $movim['_id'],'data'=> $movim_update))->save("update")->items;
							$sesion = $f->model("ts/cjse")->params(array('_id' => $sesion['_id'],'data'=>$sesion_update))->save("update")->items;
							$movim_update = array(
								'fecreg' => new MongoDate(),
								'tipo'=>'--',
								'sesion'=>new MongoId($sesion['_id']),
								'documento'=>new MongoId($docum['_id']),
								'entrada'=>0,
								'salida'=>0,
								'saldo'=>0
							);
							$docums[$i]['movimiento'] = $movim;
							$saldo = $sesion['saldo_final'];
							$haber = $sesion['haber_final'];
						}

					} elseif($docum['estado'] == 'C') {
						$monto  = 0;
						if(count($docum['movimiento']) !== 2) throw new Exception("Error: se detecto mas de 2 movimiento en un documento anulado");
						if($docum['movimiento'][0]['tipo'] !== "S") {
							$data = $docum['movimiento'];
							throw new Exception("Error: El movimiento detectado es un ingreso para un documento aprobado luego cerrado");
						}
						if(!isset($docum['movimiento'][0]['fecreg'])) throw new Exception("Error: no se encontro el campo fecreg");
						$movim_update['tipo']='S';
						$movim_update['salida']=$monto;
						$movim_update['saldo']=$saldo-$monto;
						$movim_update['fecreg'] = $docum['fecreg'];
						$sesion_update['saldo_final']= $movim_update['saldo'];
						$sesion_update['haber_final']= $haber+$monto;
						$movim = $f->model("ts/camo")->params(array('_id' => $docum['movimiento'][0]['_id'] ,'data'=>$movim_update))->save("update")->items;
						$sesion = $f->model("ts/cjse")->params(array('_id' => $sesion['_id'],'data'=>$sesion_update))->save("update")->items;
						$movim_update = array(
							'fecreg' => new MongoDate(),
							'tipo'=>'--',
							'sesion'=>new MongoId($sesion['_id']),
							'documento'=>new MongoId($docum['_id']),
							'entrada'=>0,
							'salida'=>0,
							'saldo'=>0
						);
						//$docums[$i]['movimiento'][0] = $movim;
						$saldo = $sesion['saldo_final'];
						$haber = $sesion['haber_final'];

						if($docum['movimiento'][1]['tipo'] !== "E") throw new Exception("Error: El movimiento detectado es una salida para un documento cerrado");
						if(!isset($docum['movimiento'][1]['fecreg'])) throw new Exception("Error: no se encontro el campo fecreg");
						$movim_update['tipo']='E';
						$movim_update['entrada']=$monto;
						$movim_update['saldo']=$saldo+$monto;
						$movim_update['fecreg'] = $docum['fecreg'];
						$sesion_update['saldo_final']= $movim_update['saldo'];
						$sesion_update['haber_final']= $haber+$monto;
						$movim = $f->model("ts/camo")->params(array('_id' => $docum['movimiento'][1]['_id'] ,'data'=>$movim_update))->save("update")->items;
						$sesion = $f->model("ts/cjse")->params(array('_id' => $sesion['_id'],'data'=>$sesion_update))->save("update")->items;
						$docums[$i]['movimiento'][1] = $movim;
						$saldo = $sesion['saldo_final'];
						$haber = $sesion['haber_final'];
					}
				}
				$response['status'] = 'success';
				$response['message'] = "Se realizo un recalculo correcto de la informacion";
				$response['data'] = $docums;
				
		} catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
			$response['data'] = $data;
		}
		$f->response->json($response);
		
	}
}
?>