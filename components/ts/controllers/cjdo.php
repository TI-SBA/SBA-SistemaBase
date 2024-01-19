<?php
class Controller_ts_cjdo extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ts/cjdo")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ts/cjdo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			$data = $f->request->data;
			$data['fecmod'] = new MongoDate();
			$data['trabajador'] = $f->session->userDBMin;																							//Autor del cambio
			if(isset($data['fecdoc']))	$data['fecdoc'] = new MongoDate(strtotime($data['fecdoc']));
			#$data['fecdoc'] = new MongoDate(strtotime($data['fecdoc']));
			#Formteo de Numeros
			if(isset($data['num']))	$data['num'] = floatval($data['num']);																			//convertir num de str a float
			if(!isset($data['sesion']))	throw new Exception("Error: No se tiene un campo de sesión");												//Verificar que exista un campo de sesion
			//if(!isset($data['sesion']))//Verificar que exista un campo de sesion
			//{
			//	throw new Exception("Error: No se tiene un campo de sesión");																		
			//}
			//else
			//{
			$l_sesion = $f->model("ts/cjse")->params(array("_id"=>new MongoId($data['sesion']['_id'])))->get("one")->items;							//Cargar el ultimo movimiento por _id de caja
			if (is_null($l_sesion)) 		throw new Exception("Error: dicha sesion no pudo ser encontrada");										//Verificar que exista ya una sesion
			if ($l_sesion['estado']!='A')	throw new Exception("Error: La sesion que selecciono no se encuentra APERTURADA");						//Verifocar que la sesion ya este aperturada
			
			if(!isset($data['mont']))		throw new Exception("Error: no se recibió un monto");
			$data['mont'] = floatval($data['mont']);																								//convertir monto de str a float
			if($data['mont']>$l_sesion['saldo_final'])	throw new Exception("Error: el monto es mayor que el saldo de la Caja");
			//}

			#Convertir en _id
			if(!isset($data['beneficiario']['_id'])) 	throw new Exception("Error: no se encuentra el _id de beneficiario");
			if(!isset($data['programa']['_id']))	 	throw new Exception("Error: no se encuentra el _id de programa");
			if(!isset($data['oficina']['_id']))		 	throw new Exception("Error: no se encuentra el _id de oficina");
			if(!isset($data['partida']['_id']))		 	throw new Exception("Error: no se encuentra el _id de partida");
			if(!isset($data['sesion']['_id'])) 		 	throw new Exception("Error: no se encuentra el _id de sesion");
			if(!isset($data['sesion']['caja']['_id'])) 	throw new Exception("Error: no se encuentra el _id de caja en sesion");
			$data['beneficiario']['_id'] = new MongoId($data['beneficiario']['_id']);																//Convertir beneficiario en mongoid
			$data['programa']['_id']=new MongoId($data['programa']['_id']);																			//Convertir programa en mongoid
			$data['oficina']['_id']=new MongoId($data['oficina']['_id']);																			//Convertir oficina en mongoid
			$data['partida']['_id']=new MongoId($data['partida']['_id']);																			//Convertir partida en mongoid
			$data['sesion']['_id']=new MongoId($data['sesion']['_id']);																				//Convertir sesion en mongoid
			$data['sesion']['caja']['_id']=new MongoId($data['sesion']['caja']['_id']);																//Convertir sesion caja en mongoid
			if(!isset($f->request->data['_id'])){																									//En caso de que el _id del registro no exista
				$data['fecreg'] = new MongoDate();
				$data['autor'] = $f->session->userDBMin;
				$data['estado'] = 'P';
				$model = $f->model("ts/cjdo")->params(array('data'=>$data))->save("insert")->items;
				$response['status'] = 'success';
				$response['message'] = 'Fueron realizados los cambios correctamente.';
			} else {																																//En caso que el registro de caja chica exista.	
				#movimientos
				//$vari = $f->model("ts/cjdo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
				$model = $f->model("ts/cjdo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
				//print_r($data['estado']);
				$response['status'] = 'success';
				$response['message'] = 'Fueron realizados los cambios correctamente.';
			}
		} catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
			$response['data'] = $data;
		}
		$f->response->json($response);
	}
	function execute_estado(){
		global $f;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try {
			$data = $f->request->data;
			if(!isset($data['_id']))	throw new Exception("Error: No recibo ID");
			$model = $f->model("ts/cjdo")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			if(is_null($model))	throw new Exception("Error: No se encontro el modelo");
			$l_sesio= $f->model("ts/cjse")->params(array("_id"=>new MongoId($model['sesion']['_id'])))->get("one")->items;		//Cargar sesion mediante el _id de sesion en el documento
			//$l_sesio= $f->model("ts/cjse")->params(array("_id"=>new MongoId($model['sesion']['caja']['_id'])))->get("lcaja")->items;
			if(is_null($l_sesio)) throw new Exception("Error: No se encontro una sesion con el id de sesion del documento");
			$l_movim= $f->model("ts/camo")->params(array("_id"=>new MongoId($l_sesio['caja']['_id'])))->get("lcaja")->items;	//Ultimo movimiento de esta caja
			if(is_null($l_movim))	$l_monto=$l_sesio['saldo_final'];	//Se usa el salda de la sesion
			else	$l_monto=$l_movim['saldo'];							//El monto sera el ultimo movimiento
			if(!isset($data['estado'])) throw new Exception("No se recibio ningun estado");
			if($data['estado']=='A'){									//Verificar si el estado es aprobado
				if($model['estado']=='C')	throw new Exception("Error: El documento ya fue anulado, imposible reaprobar");
				if($model['estado']=='A')	throw new Exception("El documento ya esta aprobado, imposible reaprobar");
				//echo ("Estado AvP:" . $data['estado'] . '<br>');
				//echo ("Monto ya en numero:" . $model['mont'] . '<br>');
				//$last_model = $f->model("ts/camo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("fecreg")->items;
				//$last_monto=$last_model['mont'];	
				$to_insert = array(
					'fecreg'=>new MongoDate(),
					'tipo'=>'S',
					//'documento'=>new MongoId($f->request->data['_id']),
					'sesion'=>new MongoId($l_sesio['_id']),
					'documento'=>new MongoId($model['_id']),
					'entrada'=>'0',
					'salida'=>$model['mont'],
					'saldo'=>($l_monto+0-$model['mont'])
				);
				$sesion_update = array(
					'saldo_final' => ($l_monto+0-$model['mont']),
					'fecmod' => new MongoDate()
				);
			} elseif($data['estado']=='C') {
				if($model['estado']=='P')	throw new Exception("Error: El documento esta en PENDIENTE, no se puede ANULAR un documento pendiente");
				if($model['estado']=='C')	throw new Exception("Error: El documento ya se encuentra ANULADO");
				$to_insert = array(
					'fecreg'=>new MongoDate(),
					'tipo'=>'E',
					//'documento'=>new MongoId($f->request->data['_id']),
					'sesion'=>new MongoId($l_sesio['_id']),
					'documento'=>new MongoId($model['_id']),
					'entrada'=>$model['mont'],
					'salida'=>'0',
					'saldo'=>($l_monto+$model['mont']-0)
				);
				$sesion_update = array(
					'saldo_faldo' => ($l_monto+$model['mont']-0),
					'fecmod' => new MongoDate()
				);
			} else throw new Exception("Error, el estado enviado no esta definido");
			$data['fecmod'] = new MongoDate();
			$data['trabajador'] = $f->session->userDBMin;
			$movim= $f->model("ts/camo")->params(array('data'=>$to_insert))->save("insert")->items;														//Insertar nuevo movimiento
			$model = $f->model("ts/cjdo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;			//Actualizar caja chicas documentos
			$sesion = $f->model("ts/cjse")->params(array('_id'=>new MongoId($model['sesion']['_id']),'data'=>$sesion_update))->save("update")->items;	//Actualizar la sesion actual
			$response['status'] = 'success';
			$response['message'] = 'Fueron realizados los cambios correctamente.';			
		} 
		catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/cjdo.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/cjdo.details");
	}
	function execute_delete()
	{
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			if (!isset($data['_id']))	throw new Exception("Error: no se recibio el ID de documento a eliminar");
			$docum = $f->model("ts/cjdo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;			//Cargar documento por ID
			if(is_null($docum))	throw new Exception("Error: no se puede eliminar este documento ya que su ID no existe");
			if (!isset($docum['estado']))	throw new Exception("Error: no se puede determinar el estado de esta documento");
			if($docum['estado']=='A')	throw new Exception("Error: no se puede eliminar esta sesion ya que esta se encuentra APROBADA");
			$f->model('ts/cjdo')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('cjdo');			//Eliminar los documentos pendientes
			$response['status'] = 'success';
			$response['message'] = utf8_encode('Exito: el documento se elimino correctamente');
		}
		catch (Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
}
?>