<?php
class Controller_ch_cmmo extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/cmmo")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/cmmo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
/*
	function execute_log(){// En preparacion para usarse en un log
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
			if(isset($data['cama_old']['_id'])){
				$cama = $f->model("ch/cama")->params(array("_id"=>new MongoId($data['cama_old']['_id'])))->get("one")->items;
				if(is_null($cama))
					throw new Exception("Error: No existe el _id en las camas");
			}
			if(isset($data['hosp']['_id'])){
				$hosp = $f->model("ch/hosp")->params(array("_id"=>new MongoId($data['hosp']['_id'])))->get("one")->items;
				if(is_null($hosp))
					throw new Exception("Error: No existe la ficha hospitalaria");
				$data['paciente']=$hosp['paciente']['_id'];
				$data ['uso']= "U";
			}
			else{
				$data['paciente']="vacio";
				$data ['uso']= "V";
			}
			if(!is_set($data['salas']))
				throw new Exception("Error: no se recibio la ubicacion de la cama");
			if(!is_set($data['pabellon'])){
				$data['pabellon']='intensivo';
			}
			if(isset($data['fecreg']))
				$data['fecreg']=new MongoDate();																									

			$to_movimientos = array(
				'_id' => new MongoId(),
			 	'cama_old' => array(
			 		'_id' => new MongoId($data['_id']),
			 		'cama_old' => floatval($data['cama_old'])),
			  	'movimiento' => array(),
			  	'paciente' => array(),
			  	'pabellon' => $data['pabellon'],
			  	'sala' => $data['sala'],
			  	'estado' => $data['estado'],
			  	'fecreg' => new MongoDate(),
			  	'trabajador' => $data['trabajador']
			);

			if($cama['uso'] == "U" && $data['uso'] == "U")
			{
				if($cama['paciente']['_id']!=$data['paciente']['_id']) //Cambio de paciente
				{
					$to_movimientos = array(
						'_id' => new MongoId(),
					 	'cama_old' => array('_id' => new MongoId($data['_id']),'numero' => $data['numero']),
					  	'movimiento' => "cambio_paciente",
					  	'paciente_anterior' => $cama['paciente']['_id'],
					  	'paciente_posterior' => $data['paciente']['_id'],
					  	'pabellon_anterior' => $cama['pabellon'],
						'sala_anterior' => $cama['sala'],
						'pabellon_posterior' => $data['pabellon'],
						'sala_posterior' => $data['sala'],
					  	'uso' => "U",
					  	'fecreg' => new MongoDate(),
					  	'trabajador' => $data['trabajador']
					);
				}
			}
			if($cama['uso'] == "U" && $data['uso'] == 'V')
			{
				$to_movimientos = array(
					'_id' => new MongoId(),
				 	'cama_old' => array('_id' => new MongoId($data['_id']),'numero' => $data['numero']),
				  	'movimiento' => "desocupacion_cama",
					'paciente_anterior' => $cama['paciente']['_id'],
					'paciente_posterior' => "vacio",
					'pabellon_anterior' => $cama['pabellon'],
					'sala_anterior' => $cama['sala'],
					'pabellon_posterior' => $data['pabellon'],
					'sala_posterior' => $data['sala'],
				  	'uso' => "V",
				  	'fecreg' => new MongoDate(),
				  	'trabajador' => $data['trabajador']
				);
			}
			if($cama['uso'] == "V" && $data['uso'] == 'U')
			{
				$to_movimientos = array(
					'_id' => new MongoId(),
				 	'cama_old' => array('_id' => new MongoId($data['_id']),'numero' => $data['numero']),
				  	'movimiento' => "ocupacion_cama",
				  	'paciente_anterior' => "vacio",
					'paciente_posterior' => $data['paciente']['_id'],
					'pabellon_anterior' => $cama['pabellon'],
					'sala_anterior' => $cama['sala'],
					'pabellon_posterior' => $data['pabellon'],
					'sala_posterior' => $data['sala'],
				  	'uso' => "U",
				  	'fecreg' => new MongoDate(),
				  	'trabajador' => $data['trabajador']
				);
			}
			if($cama['uso'] == "V" && $data['uso'] == 'V')
			{
				if(($cama['pabellon']!=$data['pabellon']) || ($cama['sala']!=$data['sala']))
				{
					$to_movimientos = array(
						'_id' => new MongoId(),
				 		'cama_old' => array('_id' => new MongoId($data['_id']),'numero' => $data['numero']),
				  		'movimiento' => "traslado",
				  		'paciente_anterior' => "vacio",
						'paciente_posterior' => "vacio",
						'pabellon_anterior' => $cama['pabellon'],
						'sala_anterior' => $cama['sala'],
						'pabellon_posterior' => $data['pabellon'],
						'sala_posterior' => $data['sala'],
				  		'uso' => "V",
				  		'fecreg' => new MongoDate(),
				  		'trabajador' => $data['trabajador']
					);
				}
			}

				$cama_movimientos = $f->model("ch/cmmo")->params($to_movimientos)->save("insert")->items;
				//if(isset($data['numero']))
				//	throw new Exception("Error: no se debe recibir el campo numero");
				$model = $f->model("ch/cmmo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
				$response['status'] = 'success';
				$response['message'] = 'Fueron actualizacion los cambios correctamente.';
		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($model);
	}
	*/
	function execute_paciente(){
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
			#movimiento
			if(!isset($data['_id']))
				throw new Exception("Error: no se recibio el _id de movimientos");
			$cama_movimiento = $f->model("ch/cmmo")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			if(is_null($cama_movimiento))
				throw new Exception("Error: no existe el _id en los movimientos");
			if($cama_movimiento['estado']==1)
				throw new Exception("Error: existe un paciente en la cama");

			#camas
			$cama = $f->model("ch/cama")->params(array("_id"=>new MongoId($cama_movimiento['cama']['_id'])))->get("one")->items;
			if(is_null($cama))
				throw new Exception("Error: no se encuentra la cama");
			#paciente
			if(!isset($data['paciente']['_id']))
				throw new Exception("Error: no se recibio el _id de paciente ");

			if(!isset($data['paciente']['paciente']['_id']))
				throw new Exception("Error: no se recibio el _id de paciente ");
			else
				$data['paciente']['paciente']['_id'] = new MongoId($data['paciente']['paciente']['_id']);

			$hospi = $f->model("ch/hospi")->params(array("_id"=>new MongoId($data['paciente']['_id'])))->get("one")->items;
			if(is_null($hospi))
				throw new Exception("Error: no existe el _id en los paciente en hospitalizacion");
			$data['paciente']['_id']=$hospi['paciente']['_id'];
			#Operacion
			$data ['estado']= 1;
			if(!isset($data['paciente']['cie10']))
				throw new Exception("Error: No se recibio cie10nostico");
			$to_movimientos = array(
			 	'cama' => array(
			 		'_id' => new MongoId($cama_movimiento['cama']['_id']),
			 		'cama' => floatval($data['cama']['cama'])),
			  	'movimiento' => 'movimiento',
			  	'pabellon' => $cama['pabellon'],
			  	'sala' => $cama['sala'],
			  	'paciente' => $data['paciente'],
			  	'estado' => floatval($data ['estado']),
			  	'fecreg' => new MongoDate(),
			  	'trabajador' => $data['trabajador'],
			  	'cie10' => $data['paciente']['cie10']
			);

			$to_cama = array(
			  	'paciente' => $data['paciente'],
			  	'estado' => floatval($data ['estado']),
			  	'fecmod' => new MongoDate(),
			  	'trabajador' => $data['trabajador'],
			);

			//print_r($to_movimientos);
			$cama_movimientos = $f->model("ch/cmmo")->params(array('_id'=>new MongoId($data['_id']),'data'=>$to_movimientos))->save("update")->items;
			$cama = $f->model("ch/cama")->params(array('_id'=>$to_movimientos['cama']['_id'],'data'=>$to_cama))->save("update")->items;
			$response['status'] = 'success';
			$response['message'] = 'Fueron actualizacion los cambios correctamente.';
		}catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	/*function execute_movimiento(){
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
			#movimiento
			if(!isset($data['_id']))
				throw new Exception("Error: no se recibio el _id de movimientos");
			$cama_movimiento = $f->model("ch/cmmo")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			if(is_null($cama_movimiento))
				throw new Exception("Error: no existe el _id en los movimientos");
			if($cama_movimiento['estado']!=0)
				throw new Exception("Error: existe un paciente en la cama y no se puede mover");
			#camas
			$cama = $f->model("ch/cama")->params(array("_id"=>new MongoId($cama_movimiento['cama_old']['_id'])))->get("one")->items;
			if(is_null($cama))
				throw new Exception("Error: no se encuentra la cama");
			#movimiento
			if(!isset($data['fecmov']))
				throw new Exception("Error: no se recibio un movimiento");
			if(!isset($data['pabellon']) && !isset($data['sala']))
				throw new Exception("Error: no se encontro traslado");
			if(!isset($data['pabellon']))
				$data['pabellon']=$cama_movimiento['pabellon'];
			if(!isset($data['sala']))
				$data['sala']=$cama_movimiento['sala'];

			$data ['estado']= "0";

			$to_movimientos = array(
			  	'movimiento' => 'movmimiento',
			  	'pabellon' => $data['pabellon'],
			  	'sala' => $data['sala'],
			  	'paciente' => array(),
			  	'estado' => floatval($data['estado']),
			  	'fecmod' => new MongoDate(),
			  	'trabajador' => $data['trabajador'],
			  	'fecmov' => $data['fecmov']
			);

			//$cama_movimientos = $f->model("ch/cmmo")->params($to_movimientos)->save("insert")->items;
			$cama_movimientos = $f->model("ch/cmmo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$to_movimientos))->save("update")->items;
			$cama = $f->model("ch/cama")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$to_movimientos))->save("update")->items;
			$response['status'] = 'success';
			$response['message'] = 'Fueron actualizacion los cambios correctamente.';
		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($model);
	}*/
	function execute_estadisticas(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: no se obtuvieron las estadisticas',
			'data'=>array()
		);
		try{
			if(!is_set($data['pabellon'] && !is_set($data['sala']) && !is_set('estado'))){
			#Camas vacias
			$c_v_i_v = count($f->model("ch/cmmo")->get("vacias_intensivo_varones")->items);
			$c_v_i_d = count($f->model("ch/cmmo")->get("vacias_intensivo_damas")->items);
			$c_v_in_v = count($f->model("ch/cmmo")->get("vacias_intermedio_varones")->items);
			$c_v_in_d = count($f->model("ch/cmmo")->get("vacias_intermedio_damas")->items);
			/*
			#Camas usadas
			$c_u_i_v = $f->model("ch/cama")->get("usadas_intensivo_varones")->items;
			$c_u_i_d = $f->model("ch/cama")->get("usadas_intensivo_damas")->items;
			$c_u_in_v = $f->model("ch/cama")->get("usadas_intermedio_varones")->items;
			$c_u_in_d = $f->model("ch/cama")->get("usadas_intermedio_damas")->items;
			*/
			$response['data'] = array(
				'intensivo_varones' => $c_v_i_v,
				'intensivo_damas' => $c_v_i_d,
				'intermedio_varones' => $c_v_in_v,
				'intermedo_damas' => $c_v_in_d
			 );
			$response['status'] = 'success';
			$response['message'] = 'se obtuvieron las estadisticas correctamente.';
			}
			else
			{
				$to_custom;
				if(!is_set($data['pabellon']))
					$to_custom['pabellon'] = $data['pabellon'];
				if(!is_set($data['sala']))
					$to_custom['sala'] = $data['sala'];
				if(!is_set($data['estado']))
					$to_custom['estado'] = $data['estado'];

			$response['data'] = ($f->model("ch/cmmo")->params($to_custom)->get("custom")->items);
			$response['status'] = 'success';
			$response['message'] = 'se obtuvieron las estadisticas correctamente.';
			}

		}
		catch(Exception $e){
			$response['status']= 'error';
			$response['message']= $e->getMessage();
		}
		$f->response->json($model);
	}
	function execute_traslado(){
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
			if(!isset($data['fectra']))
				$data['fectra']=new MongoDate();
			if(!isset($data['pabellon']) && !isset($data['sala']))
				throw new Exception("Error: no se encontro traslado");
			if(!isset($data['pabellon']))
				$data['pabellon']=$cama_movimiento['pabellon'];
			if(!isset($data['sala']))
				$data['sala']=$cama_movimiento['sala'];
			#camas vacias
			if($data['pabellon']=="INTENSIVO")
			{
				if($data['sala']=="VARONES")
					$camas_vacias = ($f->model("ch/cmmo")->params(array())->get("vacias_intensivo_varones")->items);
				elseif($data['sala']=="DAMAS")
					$camas_vacias = ($f->model("ch/cmmo")->params(array())->get("vacias_intensivo_damas")->items);
				else
					throw new Exception("Error: no se reconoce la salas en intensivo");
			}
			elseif($data['pabellon']=="INTERMEDIO")
			{
				if($data['sala']=="VARONES")
					$camas_vacias = ($f->model("ch/cmmo")->params(array())->get("vacias_intermedio_varones")->items);
				elseif($data['sala']=="DAMAS")
					$camas_vacias = ($f->model("ch/cmmo")->params(array())->get("vacias_intermedio_damas")->items);
				else
					throw new Exception("Error: no se reconoce la salas en intermedio");
			}
			else
				throw new Exception("Error: no se reconoce el pabellon");
			if(is_null($camas_vacias))
				throw new Exception("Error: no hay camas vacias");

			#camas
			if(!isset($data['cama']['_id']))
				throw new Exception("Error: no se recibio el _id de la nueva cama para el traslado");
			$vieja_cama = $f->model("ch/cama")->params(array("_id"=>new MongoId($data['cama_old']['_id'])))->get("one")->items;
			$nueva_cama = $f->model("ch/cama")->params(array("_id"=>new MongoId($data['cama']['_id'])))->get("one")->items;
			if(is_null($vieja_cama))
				throw new Exception("Error: no se encuentra la cama inicial");
			if(is_null($nueva_cama))
				throw new Exception("Error: no se encuentra la cama a trasladar el paciente");

			#movimiento
			if(!isset($data['_id']))
				throw new Exception("Error: no se recibio el _id de movimientos");
			$cama_antiguo_movimiento = $f->model("ch/cmmo")->params(array("_id"=>new MongoId($data['cama_old']['_id']),"sala"=>$vieja_cama['sala'],"pabellon"=>$vieja_cama['pabellon']))->get("cama")->items;
			if(is_null($cama_antiguo_movimiento))
				throw new Exception("Error: no existe el _id en los movimientos");
			if(($cama_antiguo_movimiento['estado']==0))
				throw new Exception("Error: no existe un paciente en la cama");

			#Nuevo movimiento
			$cama_nuevo_movimiento = $f->model("ch/cmmo")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			//print_r($cama_nuevo_movimiento);
			if(!is_null($cama_nuevo_movimiento)){
				if(isset($cama_nuevo_movimiento['estado'])){
					if($cama_nuevo_movimiento['estado']==1)
						throw new Exception("Error: no se puede realizar el traslado ya que hay un paciente asignado a esa nueva cama");
				}
			}else
				throw new Exception("Error: no se puede realizar ya que no hay un movimiento asignado a la cama del traslado");
			#paciente
			if(!isset($vieja_cama['paciente']['paciente']['_id']))
				throw new Exception("Error: no se encontro _id de paciente en este movimiento de cama");
			$paciente = $f->model("ch/paci")->params(array("_id"=>new MongoId($data['paciente']['paciente']['_id'])))->get("one_entidad")->items;
			if(is_null($paciente))
				throw new Exception("Error: no existe el _id en los paciente");
			#traslado
			$antiguo_traslado = array(
			  	'movimiento' => "traslado",
			  	'paciente' => array(),
			  	'estado' => floatval(0),
			  	'fecmod' => new MongoDate(),
			  	'trabajador' => $data['trabajador'],
			  	'cie10' => array(),
			);
			$nuevo_traslado = array(
			  	'movimiento' => 'traslado',
			  	'paciente' => $paciente['paciente'],
			  	'estado' => floatval(1),
			  	'fecmod' => new MongoDate(),
			  	'trabajador' => $data['trabajador'],
			  	'cie10' => $cama_antiguo_movimiento['cie10'],
			  	'fectra' => $data['fectra']
			);
			#cama
			$antigua_cama = array(
				'paciente' => array(), 
				'estado' => floatval(0),
				'fecmod' => new MongoDate(),
				'trabajdor' => $data['trabajador'],
				'cie10' => array()
			);
			$nueva_cama = array(
				'paciente' => $paciente['paciente'],
				'estado' => floatval(1),
				'fecmod' => new MongoDate(),
				'trabajdor' => $data['trabajador'],
				'cie10' => $cama_antiguo_movimiento['cie10'],
			);
			//print_r($cama_nuevo_movimiento);
			//print_r($cama_movimiento);
			//throw new Exception("XDDDDD");
			$movimiento_antiguo = $f->model("ch/cmmo")->params(array('_id'=>new MongoId($cama_antiguo_movimiento['_id']),'data'=>$antiguo_traslado))->save("update")->items;
			$movimiento_nuevo = $f->model("ch/cmmo")->params(array('_id'=>($cama_nuevo_movimiento['_id']),'data'=>$nuevo_traslado))->save("update")->items;
			$cama_antigua = $f->model("ch/cama")->params(array('_id'=>$cama_antiguo_movimiento['cama']['_id'],'data'=>$antigua_cama))->save("update")->items;
			$cama_nueva = $f->model("ch/cama")->params(array('_id'=>$cama_nuevo_movimiento['cama']['_id'],'data'=>$nueva_cama))->save("update")->items;
			$response['status'] = 'success';
			$response['message'] = 'Fueron actualizacion los cambios correctamente.';
		}		
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ch/cmmo.edit");
	}
	function execute_tras(){
		global $f;
		$f->response->view("ch/cmmo.tras");
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
			$f->model('ts/cjse')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('cjse');
			$response['status'] = 'success';
			$response['message'] = ('Exito: la sesión se elimino correctamente');
		}
		catch (Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
}
?>