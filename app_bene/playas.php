<?php
//echo "<pre>";
$m = new MongoClient();
$db = $m->beneficencia;
//$pdb = $m->mydb;----------------------db de pruebas
$conexion=$db->as_petition;
//$citas = array();
$data=$_GET;
date_default_timezone_set('America/Lima');
/*********************************************************************************************************
Obitnene la ip del dispositivo consultante
*********************************************************************************************************/
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
/*********************************************************************************************************
Menu de funciones
*********************************************************************************************************/
    /*if(isset($data["get_dni"]))
        DNI();
    if(isset($data["get_histo_clinic"]))
        histoclini();
        */
        try
        {
        	if(isset($data["get_login"]))
        		get_login();
        	if(isset($data["get_playas"]))
        		get_playas();
        	if(isset($data["get_hora"]))
        		get_hora();
        	if(isset($data["get_help"]))
        		help();
        	if(empty($data))
        		throw new Exception("Error: no se entendio la peticion");
        }catch(Exception $e)
        {
        	$response['status'] = 'error';
        	$response['message'] = $e->getMessage();
        }
/*********************************************************************************************************
Realiza la funcion del logeo
*********************************************************************************************************/
function get_login()
{
	global $db;
	global $data;
	global $response;

	try
	{	
		if(!isset($data['playa']))
		{	
			if(!isset($data['userid'])||!isset($data['passwd']))
			{
				throw new Exception ("Error: ingresar usuario y contraseña");
			}
			$usuarios = $db->ac_users;
			$ulogin = $usuarios->findOne(array('userid'=>$data['userid'],'passwd'=>sha1($data['passwd'])));
			if(!isset($ulogin))
			{
				throw new Exception("Usuario o contraseña incorrecta");
			}			
		//return true;
		}
		$a_response = array(
				'estado_log' => true,
				'precios'	 => "pendiente",
				'tiempo' 	 => date('h:i'),
		);
		$response['data'] = $a_response;
		//$response['status']  = 'success';
		//$response['message'] = 'Fueron consultados los cambios correctamente.';
		/*
		if(isset($data['playa'])&&isset($data['rees']))
		{
			
		}
		*/
	//Verificacion de permisos de usuario por si es necesario
	/*************************************************************************
	$model = $db->ac_groups->find(array('members.userid'=>$ulogin['userid']));
	foreach ($model as $permisos)
	{
		$permi[] = $permisos['allowed'];
	}

	$enti = $db->mg_entidades->findOne(array('_id'=>new MongoId($ulogin['owner']['_id']))); 
	$a_response = array('Entidad'=>$enti,'usuario'=>$ulogin,'permisos'=>$permi);
	//$f->model("mg/entidad")->params(array("_id"=>new MongoId($user->items['owner']['_id'])))->get("one");
	$response['data']    = $a_response;
    $response['status']  = 'success';
    $response['message'] = 'Fueron consultados los cambios correctamente.';
    ***************************************************************************/
    }
    catch(Exception $e)
    {
    	$response['status']  = 'error';
    	$response['message'] = $e->getMessage();
    	$response['data'] = array(
    		'estado_log' => false);
    	//return false;
    }  
}
function get_hora()
{
	try
	{			
		global $response;
		$response['data'][0] =array(
			'hora' => date('H:i:s'));		
    }
    catch(Exception $e)
    {
    	$response['status']  = 'error';
    	$response['message'] = $e->getMessage();
    	//$response['data'] = array(
    	//	'estado_log' => false);
    	//return false;
    }  
}
function get_playas()
{
	global $db;
	global $data;
	global $response;
	try
	{				
			$playas_col = $db->in_playas;
			$playas = $playas_col->find(array('nomb'=>array('$exists'=>true)));
			
			foreach ($playas as $playas) 
			{
				$list_playa[] = array('ubicacion'=>$playas['nomb']);				
			}
			//array_push($list_playa,array('tiempo'=>1));
			$response['data'] = $list_playa;
			//$response['status']  = 'success';
			//$response['message'] = 'Fueron consultados los cambios correctamente.';
		//return true;
		
		/*
		if(isset($data['playa'])&&isset($data['rees']))
		{
			
		}
		*/
	//Verificacion de permisos de usuario por si es necesario
	/*************************************************************************
	$model = $db->ac_groups->find(array('members.userid'=>$ulogin['userid']));
	foreach ($model as $permisos)
	{
		$permi[] = $permisos['allowed'];
	}

	$enti = $db->mg_entidades->findOne(array('_id'=>new MongoId($ulogin['owner']['_id']))); 
	$a_response = array('Entidad'=>$enti,'usuario'=>$ulogin,'permisos'=>$permi);
	//$f->model("mg/entidad")->params(array("_id"=>new MongoId($user->items['owner']['_id'])))->get("one");
	$response['data']    = $a_response;
    $response['status']  = 'success';
    $response['message'] = 'Fueron consultados los cambios correctamente.';
    ***************************************************************************/
    }
    catch(Exception $e)
    {
    	$response['status']  = 'error';
    	$response['message'] = $e->getMessage();
    	$response['acceso'] = false;
    	//return false;
    }  
}



function get_playa_tarifa()
{

}
function get_resumen()
{

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
Respuesta(No midificar)
**********************************************************************************************************/
//$response['count']=$cuenta['conexiones'];
header('Content-Type: application/json');
//echo "</pre>";

echo json_encode($response);
?>