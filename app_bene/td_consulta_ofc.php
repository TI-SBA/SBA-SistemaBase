<?php
$m = new MongoClient();
$db = $m->beneficencia;
$conexion=$db->app_td_ofc_peticion;
if(isset($_GET)){
	if(isset($_GET['search_office']))	
	{
		if(isset($_GET['estado']))
		{
			$estado=$_GET['estado'];
			$find_array['estado']=array('$regex' =>$estado,'$options'=>'i');
		}			
		$Oficinas= $db->mg_oficinas;
		$cursor = $Oficinas->find($find_array,['nomb','_id']);
		$content_array=array();
		foreach ($cursor as $document) {
			$item['nomb']=$document['nomb'];
			$item['_id']=$document['_id'];
			array_push($content_array,$item);
		}
		$response=array('status' => 'success','ofc'=>$content_array,'content'=>'true');
	}
		
    else {
		$response=array('status' => 'failet request, bad url','content'=>'false');
    }
		header('Content-Type: application/json');
		echo json_encode($response);
}
else {
	{
		$response=array('status' => 'failet request','content'=>'false');
		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
?>
