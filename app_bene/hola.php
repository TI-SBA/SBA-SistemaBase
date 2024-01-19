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

		$to_response =array(
			'hola' => 'mundo',
			"mongoid" => new MongoId()
		);

		/*Respuesta de la función*/
		//$response['data'] = $to_response;
		$response['lista'] = $to_response;
		//$response['status'] = 'success';
		//$response['message'] = 'La consulta fue exitosa';
	

/**********************************************************
Obtener ayuda de las funciones implementadas de la app
***********************************************************/


/**********************************************************************************************************
Respuesta(No modificar)
**********************************************************************************************************/
//$response['count']=$cuenta['conexiones'];
header('Content-Type: application/json');
echo json_encode($response);
session_unset();
?>