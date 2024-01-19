<?php
class Controller_mh_cama extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mh/cama")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("mh/cama")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['trabajador'] = $f->session->userDBMin;
		$data['fecmod']=new MongoDate();
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			$data['paciente']="vacio";
			if(!isset($data['sala']))
				throw new Exception("Error: no se recibio la sala de la cama");
			if(!isset($data['pabellon']))
				throw new Exception("Error: no se recibio el pabellon de la cama");
			if(isset($data['fecreg']))
				$data['fecreg']=new MongoDate();
			if(!isset($data['cama']))
				throw new Exception("Error: no se recibio el numero de cama");
			$cama_generada = $f->model("mh/cama")->params(array('cama'=>floatval($data['cama']),'pabellon'=>$data['pabellon'],'sala'=>$data['sala']))->get("numero")->items;
			if(!is_null($cama_generada))
				throw new Exception("Error: ese numero de cama ya esta siendo usado");
			$data['cama']=floatval($data['cama']);
			$data['paciente']=array();
			$data ['estado']=floatval($data ['estado']);
			if(!isset($f->request->data['_id'])){
				#Guardar camas
				$data['_id']=new MongoId();
				$to_movimientos = array(
					'_id' => new MongoId(),
				 	'cama' => array(
				 		'_id' => new MongoId($data['_id']),
				 		'cama' => floatval($data['cama'])),
				  	'movimiento' => array(),
				  	'paciente' => array(),
				  	'pabellon' => $data['pabellon'],
				  	'sala' => $data['sala'],
				  	'estado' => floatval($data['estado']),
				  	'fecreg' => new MongoDate(),
				  	'fectra' => array(),
				  	'fecmod' => new MongoDate(),
				  	'trabajador' => $data['trabajador'],
				  	'cie10' => array()
				);
				$movimiento = $f->model("mh/cmmo")->params(array('data' => $to_movimientos))->save("insert")->items;
				$cama = $f->model("mh/cama")->params(array('data'=>$data))->save("insert")->items;
				$response['status'] = 'success';
				$response['message'] = 'Fueron guardados los cambios correctamente.';
			}
			else
			{
				#añadido
				throw new Exception("Error: no se encontro el _id");
				#Omite: Actualizar camas
				/*$cama = $f->model("mh/cama")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
				if(is_null($cama))
				{
					throw new Exception("Error: no se encontro la cama con ese _id");
				}
				$data['_id'] = new MongoId($data['_id']);	//Caso en el que se pone en formato _id
				//$cama_movimientos = $f->model("mh/cmmo")->params($to_movimientos)->save("insert")->items;
				$model = $f->model("mh/cama")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
				$response['status'] = 'success';
				$response['message'] = 'Fueron actualizacion los cambios correctamente.';
				*/
			}
		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_save_temporal(){
		global $f;
		$data = $f->request->data;
		$data['trabajador'] = $f->session->userDBMin;
		$data['fecmod']=new MongoDate();
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			#traslado
			if(!isset($data['pabellon']))
				throw new Exception("Error: no se especifico la sala");
			if(!isset($data['sala']))
				throw new Exception("Error: no se especifico el pabellon");
			#camas vacias
			if($data['pabellon']=="INTENSIVO")
			{
				if($data['sala']=="VARONES")
					$camas_vacias = ($f->model("mh/cmmo")->params(array())->get("vacias_intensivo_varones")->items);
				elseif($data['sala']=="DAMAS")
					$camas_vacias = ($f->model("mh/cmmo")->params(array())->get("vacias_intensivo_damas")->items);
				else
					throw new Exception("Error: no se reconoce la salas en intensivo");
			}
			elseif($data['pabellon']=="INTERMEDIO")
			{
				if($data['sala']=="VARONES")
					$camas_vacias = ($f->model("mh/cmmo")->params(array())->get("vacias_intermedio_varones")->items);
				elseif($data['sala']=="DAMAS")
					$camas_vacias = ($f->model("mh/cmmo")->params(array())->get("vacias_intermedio_damas")->items);
				else
					throw new Exception("Error: no se reconoce la salas en intermedio");
			}
			else
				throw new Exception("Error: no se reconoce el pabellon");
			if(!is_null($camas_vacias))
				throw new Exception("Error: Aun hay camas vacias");

			$data['paciente']="vacio";
			if(!isset($data['sala']))
				throw new Exception("Error: no se recibio la sala de la cama");
			if(!isset($data['pabellon']))
				throw new Exception("Error: no se recibio el pabellon de la cama");
			if(isset($data['fecreg']))
				$data['fecreg']=new MongoDate();
			if(!isset($data['cama']))
				throw new Exception("Error: no se recibio el numero de cama");
			$cama_generada = $f->model("mh/cama")->params(array('cama'=>floatval($data['cama']),'pabellon'=>$data['pabellon'],'sala'=>$data['sala']))->get("numero")->items;
			if(!is_null($cama_generada))
				throw new Exception("Error: ese numero de cama ya esta siendo usado");
			$data['cama']=floatval($data['cama']);
			$data['paciente']=array();
			$data ['estado']=floatval($data ['estado']);
			$data ['temporal']="si";
			if(!isset($f->request->data['_id'])){
				#Guardar camas
				$data['_id']=new MongoId();
				$to_movimientos = array(
					'_id' => new MongoId(),
				 	'cama' => array(
				 		'_id' => new MongoId($data['_id']),
				 		'cama' => floatval($data['cama'])),
				  	'movimiento' => array(),
				  	'paciente' => array(),
				  	'pabellon' => $data['pabellon'],
				  	'sala' => $data['sala'],
				  	'estado' => floatval($data['estado']),
				  	'fecreg' => new MongoDate(),
				  	'fectra' => array(),
				  	'fecmod' => new MongoDate(),
				  	'trabajador' => $data['trabajador'],
				  	'cie10' => array(),
				  	'temporal' => "Si",
				);
				$movimiento = $f->model("mh/cmmo")->params(array('data' => $to_movimientos))->save("insert")->items;
				$cama = $f->model("mh/cama")->params(array('data'=>$data))->save("insert")->items;
				$response['status'] = 'success';
				$response['message'] = 'Fueron guardados los cambios correctamente.';
			}else{
				#añadido
				throw new Exception("Error: no se encontro el _id");
				#Omite: Actualizar camas
				/*$cama = $f->model("mh/cama")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
				if(is_null($cama))
				{
					throw new Exception("Error: no se encontro la cama con ese _id");
				}
				$data['_id'] = new MongoId($data['_id']);	//Caso en el que se pone en formato _id
				//$cama_movimientos = $f->model("mh/cmmo")->params($to_movimientos)->save("insert")->items;
				$model = $f->model("mh/cama")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
				$response['status'] = 'success';
				$response['message'] = 'Fueron actualizacion los cambios correctamente.';
				*/
			}
		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}

	function execute_edit(){
		global $f;
		$f->response->view("mh/cama.edit");
	}
	function execute_delete(){
		/*
		*	Borrar sesion
		*	Recibira el ID de la sesion a borrar
		*/
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try
		{
			if (!isset($data['_id']))
				throw new Exception("Error: no se recibio el _id a borrar");	
			$cama_borrar=$f->model('mh/cama')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one');
			if (is_null($cama))
				throw new Exception("Error: no se encontro la cama a eliminar");		
			if (!isset($cama['estado']))
				throw new Exception("Error: inconsistencia al no encontrar el estado de la cama");
			if ($cama['estado']=='1')
				throw new Exception("Error: no se puede elminar esta cama ya que esta ocupada");
			$f->model('mh/cama')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('cama');
			$response['status'] = 'success';
			$response['message'] = utf8_encode('Exito: la sesión se elimino correctamente');
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
}
?>