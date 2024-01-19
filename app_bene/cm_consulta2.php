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
	if(empty($data))
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
	$limite=50;
	$espacios = $db->cm_espacios;

	$to_response = array();

	try{

		$find_espacios=array();
		/*Verificacion sector del sector*/
		if(isset($data['sector'])){
			if(is_null($data['sector']))	throw new Exception("Error: el sector se recibio nulo");
			if(empty($data['sector']))	throw new Exception("Error: el sector se recibio vacio");
			if(!is_string($data['sector'])) throw new Exception("Error: el sector es de tipo incorrecto");
			if(strlen($data['sector'])!=1) throw new Exception("Error: el sector es de longitud incorrecta");
			$find_espacios['sector']=strtoupper($data['sector']);
		}

		/*Verificacion estado del estado*/
		if(isset($data['estado'])){
			if(is_null($data['estado']))	throw new Exception("Error: el campo estado esta recibiendo un nulo");
			if(empty($data['estado']))	throw new Exception("Error: el campo estado esta vacio");
			if(!is_string($data['estado'])) throw new Exception("Error: el estado es de tipo incorrecto");
			if(strlen($data['estado'])!=1) throw new Exception("Error: el estado es de longitud incorrecta");
			$find_espacios["estado"]=strtoupper($data['estado']);
		}
		
		/*Verificacion tipo del tipo*/
		if(isset($data['tipo'])){
			if(is_null($data['tipo']))	throw new Exception("Error: el campo tipo esta recibiendo un nulo");
			if(empty($data['tipo']))	throw new Exception("Error: el campo tipo esta vacio");
			if(!is_string($data['tipo'])) throw new Exception("Error: el tipo es de tipo incorrecto");
			if(strlen($data['tipo'])!=1) throw new Exception("Error: el tipo es de longitud incorrecta");
			if(strtoupper($data['tipo'])=="M") $find_espacios['mausoleo']=array('$exists'=>1);
			elseif(strtoupper($data['tipo'])=="N") $find_espacios['nicho']=array('$exists'=>1);
			elseif(strtoupper($data['tipo'])=="T"){
				$find_espacios['tumba']=array('$exists'=>1);
				$find_espacios['nicho']=array('$exists'=>0);
			} 
			else	throw new Exception("Error: No se encontro otro tipo en cementerio");
		}
		if(isset($find_espacios["estado"]) && $find_espacios["estado"]=="D"){
			$limite=null;
			/******************************************************************************************
			Recepcion de datos en mayusculas
			*******************************************************************************************/
			if(isset($data['appat']))	{
				if(is_null($data['appat']))	throw new Exception("Error: el campo appat esta recibiendo un nulo");
				if(empty($data['appat']))	throw new Exception("Error: el campo appat esta vacio");
				$find_espacios['ocupantes.appat']=strtoupper($data['appat']);
			}
			if(isset($data['apmat']))	{
				if(is_null($data['apmat']))	throw new Exception("Error: el campo apmat esta recibiendo un nulo");
				if(empty($data['apmat']))	throw new Exception("Error: el campo apmat esta vacio");
				$find_espacios['ocupantes.apmat']=strtoupper($data['apmat']);
			}
			if(isset($data['nomb']))	{
				if(is_null($data['nomb']))	throw new Exception("Error: el campo nomb esta recibiendo un nulo");
				if(empty($data['nomb']))	throw new Exception("Error: el campo nomb esta vacio");
				$find_espacios['ocupantes.nomb']=strtoupper($data['nomb']);
			}

			$lista_espacio=array();

			if(empty($find_espacios))	throw new Exception("Error: no se recibio ningun parametro");
			else {
				$lista = $espacios->find($find_espacios)->limit($limite);
				if(!empty($lista)){
					foreach ($lista as $espa) {
						$tipo="";
						if(array_key_exists ('nicho', $espa ))$tipo="Nicho";
						elseif(array_key_exists ('tumba', $espa ))$tipo="Tumba";
						elseif(array_key_exists ('mausoleo', $espa ))$tipo="Mausoleo";
						//***********************************************************//
						$temp_espa = array(							
							'sector' => $espa['sector'],
							'estado' => $espa['estado'], 
							'ubicacion' => $espa['nomb'],
							'tipo'=>$tipo,
						);
						if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
							$ocup=array();
							foreach ($espa['ocupantes'] as $ocupante) {
								$ocupante=array(
									'appat' => $ocupante['appat'],
									'apmat' => $ocupante['apmat'],
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
		}

		else{
			$lista_espacio=array();
			/******************************************************************************************
			Recepcion de datos en mayusculas
			*******************************************************************************************/
			if(isset($data['appat'])) {
				if(is_null($data['appat']))	throw new Exception("Error: el campo appat esta recibiendo un nulo");
				if(empty($data['appat']))	throw new Exception("Error: el campo appat esta vacio");
				$find_espacios['ocupantes.appat']=strtoupper($data['appat']);
			}
			if(isset($data['apmat'])) {
				if(is_null($data['apmat']))	throw new Exception("Error: el campo apmat esta recibiendo un nulo");
				if(empty($data['apmat']))	throw new Exception("Error: el campo apmat esta vacio");
				$find_espacios['ocupantes.apmat']=strtoupper($data['apmat']);
			}
			if(isset($data['nomb'])) {
				if(is_null($data['nomb']))	throw new Exception("Error: el campo nomb esta recibiendo un nulo");
				if(empty($data['nomb']))	throw new Exception("Error: el campo nomb esta vacio");
				$find_espacios['ocupantes.nomb']=strtoupper($data['nomb']);
			}

			if(empty($find_espacios))	throw new Exception("Error: no se recibio ningun parametro");
			else {
				if(!isset($find_espacios['ocupantes.appat']) && !isset($find_espacios['ocupantes.apmat']) && !isset($find_espacios['ocupantes.nomb'])){
						$lista = $espacios->find($find_espacios)->limit($limite);
					if(!empty($lista)){
						foreach ($lista as $espa) {
							$tipo="";
							if(array_key_exists ('nicho', $espa ))$tipo="Nicho";
							if(array_key_exists ('tumba', $espa ))$tipo="Tumba";
							if(array_key_exists ('mausoleo', $espa ))$tipo="Mausoleo";
							//***********************************************************//
							$temp_espa = array(							
								'sector' => $espa['sector'],
								'estado' => $espa['estado'], 
								'ubicacion' => $espa['nomb'],
								'tipo'=>$tipo,
							);
							if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
								$ocup=array();
								foreach ($espa['ocupantes'] as $ocupante) {
									$ocupante=array(
										'appat' => $ocupante['appat'],
										'apmat' => $ocupante['apmat'],
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
				else{
					$lista = $espacios->find($find_espacios)->limit($limite);
					if(!empty($lista)){
						foreach ($lista as $espa) {
							$tipo="";
							if(array_key_exists ('nicho', $espa ))$tipo="Nicho";
							if(array_key_exists ('tumba', $espa ))$tipo="Tumba";
							if(array_key_exists ('mausoleo', $espa ))$tipo="Mausoleo";
							//***********************************************************//
							$temp_espa = array(							
								'sector' => $espa['sector'],
								'estado' => $espa['estado'], 
								'ubicacion' => $espa['nomb'],
								'tipo'=>$tipo,
							);
								if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
									$ocup=array();
									foreach ($espa['ocupantes'] as $ocupante) {
										$ocupante=array(
											'appat' => $ocupante['appat'],
											'apmat' => $ocupante['apmat'],
											'nomb'=>$ocupante['nomb'],
										);
										array_push($ocup,$ocupante);
									}
									$temp_espa=array_merge($temp_espa,array('ocupantes'=>$ocup));
								}
								array_push($lista_espacio, $temp_espa);
						}
					}
					/****************************************************************************
					Busqueda por mayusculas y minusculas
					****************************************************************************/
					if(isset($find_espacios['ocupantes.appat']))	{
						$find_espacios['ocupantes.appat']=ucwords(strtolower(($find_espacios['ocupantes.appat'])));

					}
					if(isset($find_espacios['ocupantes.apmat']))	{
						$find_espacios['ocupantes.apmat']=ucwords(strtolower(($find_espacios['ocupantes.apmat'])));
					}
					if(isset($find_espacios['ocupantes.nomb']))	{
						$find_espacios['ocupantes.nomb']=ucwords(strtolower(($find_espacios['ocupantes.nomb'])));
					}

					$lista = $espacios->find($find_espacios)->limit($limite);
					if(!empty($lista)){
						foreach ($lista as $espa) {
							$tipo="";
							if(array_key_exists ('nicho', $espa ))$tipo="Nicho";
							if(array_key_exists ('tumba', $espa ))$tipo="Tumba";
							if(array_key_exists ('mausoleo', $espa ))$tipo="Mausoleo";
							//***********************************************************//
							$temp_espa = array(							
								'sector' => $espa['sector'],
								'estado' => $espa['estado'], 
								'ubicacion' => $espa['nomb'],
								'tipo'=>$tipo,
							);
							if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
								$ocup=array();
								foreach ($espa['ocupantes'] as $ocupante) {
									$ocupante=array(
										'appat' => $ocupante['appat'],
										'apmat' => $ocupante['apmat'],
										'nomb'=>$ocupante['nomb'],
									);
									array_push($ocup,$ocupante);
								}
								$temp_espa=array_merge($temp_espa,array('ocupantes'=>$ocup));
							}
							array_push($lista_espacio, $temp_espa);
						}
					}
					/****************************************************************************
					Busqueda por minusculas
					****************************************************************************/
					if(isset($find_espacios['ocupantes.appat']))	{
						$find_espacios['ocupantes.appat']=strtolower($find_espacios['ocupantes.appat']);
					}
					if(isset($find_espacios['ocupantes.apmat']))	{
						$find_espacios['ocupantes.apmat']=strtolower($find_espacios['ocupantes.apmat']);
					}
					if(isset($find_espacios['ocupantes.nomb']))	{
						$find_espacios['ocupantes.nomb']=strtolower($find_espacios['ocupantes.nomb']);
					}

					$lista = $espacios->find($find_espacios)->limit($limite);
					if(!empty($lista)){
						foreach ($lista as $espa) {
							$tipo="";
							if(array_key_exists ('nicho', $espa ))$tipo="Nicho";
							if(array_key_exists ('tumba', $espa ))$tipo="Tumba";
							if(array_key_exists ('mausoleo', $espa ))$tipo="Mausoleo";
							//***********************************************************//
							$temp_espa = array(							
								'sector' => $espa['sector'],
								'estado' => $espa['estado'], 
								'ubicacion' => $espa['nomb'],
								'tipo'=>$tipo,
							);
							if(isset($espa['ocupantes']) && !empty($espa['ocupantes']) ){
								$ocup=array();
								foreach ($espa['ocupantes'] as $ocupante) {
									$ocupante=array(
										'appat' => $ocupante['appat'],
										'apmat' => $ocupante['apmat'],
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
			}
		}
		if (empty($lista_espacio)) throw new Exception("Error: no se encontro nada segun los parametros");

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
	$new_peticion = $conexion->update(array('_id'=>new MongoId($peticion['_id'])),array('$set'=>array($cuenta,'data'=>$data)));
}
/**********************************************************************************************************
Respuesta(No modificar)
**********************************************************************************************************/
//$response['count']=$cuenta['conexiones'];
header('Content-Type: application/json');
echo json_encode($response);
session_unset();
?>