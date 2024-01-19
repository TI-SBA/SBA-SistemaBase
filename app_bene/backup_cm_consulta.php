<?php
$m = new MongoClient();
$db = $m->beneficencia;
$conexion=$db->app_cm_peticion;
$data=$_GET;
$ip = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');
/*
$response=array(
		'status'=>'error',
		'message'=>'Error: A ocurrido un error al ingresar algun parametro',
		'data'=>array()
);
*/

$response=array(
		//'data'=>array()
);
/*********************************************************************************************************
Habilitación de las funciones
*********************************************************************************************************/
try{
	if(isset($data["restart"])){
		if(empty($data["restart"]))
			throw new Exception("Error: el campo de reinicio esta vacio");
		if (!is_numeric($data["restart"]))
			throw new Exception("Error: el campo de reinicio no es un numero reconocible");
		if ($data["restart"]<=0)
			throw new Exception("Error: el numero de reinicio es menor que 1");
		$restart=floatval($data["restart"]);
	}
	if(isset($data["lista"]))
		lista();
/*	if(isset($data["get_histo_clinic"]))
		histoclini();
	if(isset($data["get_consulta"]))
		consulta();
	if(isset($data["get_help"]))
		help();
*/	if(empty($data))
		throw new Exception("Error: no se entendio la peticion");
}	catch(Exception $e){
		$response['status'] = 'error';
		$response['message'] = $e->getMessage();
	}
/**********************************************************************************************************
Obtener datos del paciente mediante una LISTA
**********************************************************************************************************/
function lista(){
	global $db;
	global $data;
	global $response;
	$espacios = $db->cm_espacios;

	$to_response = array();

	try{

		$find_espacios=array();

		/*Verificacion Descipción del espacio*/
		//if(!isset($data['descrip']))	throw new Exception("Error: no se recibio el campo descripción");
		if(isset($data['appat']))	{
			if(is_null($data['appat']))	throw new Exception("Error: el campo appat esta recibiendo un nulo");
			if(empty($data['appat']))	throw new Exception("Error: el campo appat esta vacio");
			$find_espacios['appat']=$data['appat'];
		}
		if(isset($data['apmat']))	{
			if(is_null($data['apmat']))	throw new Exception("Error: el campo apmat esta recibiendo un nulo");
			if(empty($data['apmat']))	throw new Exception("Error: el campo apmat esta vacio");
			$find_espacios['apmat']=$data['apmat'];
		}
		if(isset($data['nomb']))	{
			if(is_null($data['nomb']))	throw new Exception("Error: el campo nomb esta recibiendo un nulo");
			if(empty($data['nomb']))	throw new Exception("Error: el campo nomb esta vacio");
			$find_espacios['nomb']=$data['nomb'];
		}
		//if(empty($data['descrip']))	throw new Exception("Error: el campo descripción esta vacio");

		/*Verificacion sector del espacio*/
		//if(!isset($data['sesion']))	throw new Exception("Error: no se recibio el campo sector");
		if(isset($data['sector'])){
			if(is_null($data['sector']))	throw new Exception("Error: el sector se recibio nulo");
			if(empty($data['sector']))	throw new Exception("Error: el sector se recibio vacio");
			if(!is_string($data['sector'])) throw new Exception("Error: el sector es de tipo incorrecto");
			if(strlen($data['sector'])!=1) throw new Exception("Error: el sector es de longitud incorrecta");
			//if($data['sector']!="A" || $data['sector']!="B" || $data["sector"]!='C' || $data['sector']!="D" || $data['sector']!="E" || $data['sector']!="F" || $data['sector']!="G") throw new Exception("Error: el contenido de el sector es incorrecto");
			$find_espacios['sector']=$data['sector'];
		}

		/*Verificacion estado del espacio*/
		//if(!isset($data['estado']))	throw new Exception("Error: no se recibio el campo estado");
		if(isset($data['estado'])){
			if(is_null($data['estado']))	throw new Exception("Error: el campo estado esta recibiendo un nulo");
			if(empty($data['estado']))	throw new Exception("Error: el campo estado esta vacio");
			if(!is_string($data['estado'])) throw new Exception("Error: el estado es de tipo incorrecto");
			if(strlen($data['estado'])!=1) throw new Exception("Error: el estado es de longitud incorrecta");
			//if($data['estado']!="C" ||  $data['estado']!="D") throw new Exception("Error: el contenidoido de el estado es incorrecto");
			$find_espacios["estado"]=$data['estado'];
		}
		
		/*Verificacion tipo del espacio*/
		//if(!isset($data['tipo']))	throw new Exception("Error: no se recibio el campo tipo");
		if(isset($data['tipo'])){
			if(is_null($data['tipo']))	throw new Exception("Error: el campo tipo esta recibiendo un nulo");
			if(empty($data['tipo']))	throw new Exception("Error: el campo tipo esta vacio");
			if(!is_string($data['tipo'])) throw new Exception("Error: el tipo es de tipo incorrecto");
			if(strlen($data['tipo'])!=1) throw new Exception("Error: el tipo es de longitud incorrecta");
			if($data['tipo']=="M") $find_espacios['mausoleo']=array('$exists'=>1);
			elseif($data['tipo']=="N") $find_espacios['nicho']=array('$exists'=>1);
			elseif($data['tipo']=="T") $find_espacios['tumba']=array('$exists'=>1);
			else	throw new Exception("Error: No se encontro otro tipo en cementerio");
			//if($data['tipo']!="T" ||  $data['tipo']!="N" || $data['tipo']!="M") throw new Exception("Error: el contenido de tipo es incorrecto");
				//print_r($data['tipo']!="T");
			//$find_espacios['tipo']=$data['tipo'];

		}

		$lista_espacio=array();
		if(empty($find_espacios))	throw new Exception("Error: no se recibio ningun parametro");
		else {
			if(isset($find_espacios['nomb']) || isset($find_espacios['appat']) || isset($find_espacios['apmat'])){
				if(isset($find_espacios['nomb'])) {
					$persona['nomb']=$find_espacios['nomb'];
					unset($find_espacios['nomb']);
				}
				if(isset($find_espacios['appat'])) {
					$persona['appat']=$find_espacios['appat'];
					unset($find_espacios['appat']);
				}
				if(isset($find_espacios['apmat'])) {
					$persona['apmat']=$find_espacios['apmat'];
					unset($find_espacios['apmat']);
				}

				/*Obtención de la lista por propietario*/
		/*		$find_espacios['propietario'] = $persona;
				$lista = $espacios->find($find_espacios)->limit(31);

				foreach ($lista as $espa) {
					$temp_espa = array(
						'sector' => $espa['sector'],
						'estado' => $espa['estado'], 
						'ubicacion' => $espa['nomb'],						
					);
					if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
						$ocup=array();
						foreach ($espa['ocupantes'] as $ocupante) {
							$ocupante=array(
								'nomb'=>$ocupante['nomb'],
							);
							array_push($ocup,$ocupante);
						}
						$temp_espa=array_merge($temp_espa,array('ocupantes'=>$ocup));
					}
					array_push($lista_espacio, $temp_espa);
				}
		*/
				/*Obtención de la lista por ocupantes*/
				$find_espacios['ocupantes'] = $persona;
				$lista = $espacios->find($find_espacios)->limit(31);
//print_r($persona);
				foreach ($lista as $espa) {
					$temp_espa = array(
						'sector' => $espa['sector'],
						'estado' => $espa['estado'], 
						'ubicacion' => $espa['nomb'],
					);
					if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
						$ocup=array();
						foreach ($espa['ocupantes'] as $ocupante) {
							$ocupante=array(
								'nomb'=>$ocupante['nomb'],
							);
							array_push($ocup,$ocupante);
						}
						$temp_espa=array_merge($temp_espa,array('ocupantes'=>$ocup));
					}
					array_push($lista_espacio, $temp_espa);
				}
			}
			else{
				$lista = $espacios->find($find_espacios)->limit(31);
			
			//print_r($find_espacios);
					/*Obtención de la lista*/
					foreach ($lista as $espa) {
						$temp_espa = array(
							'sector' => $espa['sector'],
							'estado' => $espa['estado'], 
							'ubicacion' => $espa['nomb'],
						);
						if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
							$ocup=array();
							foreach ($espa['ocupantes'] as $ocupante) {
								$ocupante=array(
									'nomb'=>$ocupante['nomb'],
								);
								array_push($ocup,$ocupante);
							}
							$temp_espa=array_merge($temp_espa,array('ocupantes'=>$ocup));
						}
						array_push($lista_espacio, $temp_espa);
					}
			}
		}
		
		if (empty($lista_espacio)) throw new Exception("Error: no se encontro nada segun los parametros");
/*
		if(is_null($paciente))
			throw new Exception("Error: no se s al paciente con el DNI proporcionado");
		if(!isset($paciente['paciente']['appat']))
			throw new Exception("Error: no se encontro el apellido paterno");
		if(!isset($paciente['paciente']['apmat']))
			throw new Exception("Error: no se encontro el apellido materno");
		if(!isset($paciente['paciente']['nomb']))
			throw new Exception("Error: no se encontro el nombre");
*/

/*		$temp_espa = array(
				'estado' => $espa['estado'], 
				'capacidad' => $espa['capacidad'], 
				'nicho' => array(
					'fila'=> $espa['fila'],
					'numero'=> $espa['numero'],
					'pabellon' => $espa[''],
				),
		);
		*/
		//$to_response =array('lista'=>$lista_espacio);
		$to_response =$lista_espacio;

		/*Respuesta de la función*/
		//$response['data'] = $to_response;
		$response['lista'] = $to_response;
		//$response['status'] = 'success';
		//$response['message'] = 'La consulta fue exitosa';
	}
	catch(Exception $e){
		$response['status'] = 'error';
		$response['message'] = $e->getMessage();
	}
}
/**********************************************************
Obtener ayuda de las funciones implementadas de la app
***********************************************************/
function help(){
	global $data;
	global $response;
	//try {
		$response['get_lista']='Obtiene el una lista de nichos segun la información presentada';
		#Finalizar consulta
		$response['message'] = array();
		$response['status'] = 'success';
	//}
	//catch (Exception $e){
	//	$response['status'] = 'error';
	//	$response['message'] = $e->getMessage();
	//}
}
/**********************************************************************************************************
Cuenta de conexiones a la base de datos (Evitar modificar)
**********************************************************************************************************/
$obj=array('ipe'=>$ip);
$peticion = $conexion->findOne($obj);
if(is_null($peticion)){
	$cuenta=array('conexiones'=>floatval(1));
	$new_peticion = $conexion->insert(array('ipe'=>$ip,'conexiones'=>floatval(1)));
}
else{
	$numero=floatval($peticion['conexiones']);
	$numero++;
	if(isset($restart))
		$cuenta=array('conexiones'=>$data["restart"]);
	else
		$cuenta=array('conexiones'=>floatval($numero));
	$new_peticion = $conexion->update(array('_id'=>new MongoId($peticion['_id'])),array('$set'=>$cuenta));
}
/**********************************************************************************************************
Respuesta(No modificar)
**********************************************************************************************************/
//$response['count']=$cuenta['conexiones'];
header('Content-Type: application/json');
echo json_encode($response);
session_unset();
?>