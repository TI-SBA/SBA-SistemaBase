<?php
$m = new MongoClient();
$db = $m->beneficencia;
$conexion=$db->app_td_peticion;
if(isset($_GET)){
		if(isset($_GET['search_td']))	
		{
			if(isset($_GET['ofc']))
			{
				$tdofc=$_GET['ofc'];
				$find_array['ubicacion.nomb']=array('$regex' =>$tdofc,'$options'=>'i');
			}
			$expedientes= $db->td_expedientes;
			$cursor = $expedientes->find($find_array,['estado','num','fecreg','traslados']);
			$content_array=array();			
			foreach ($cursor as $document) {	
				if(array_key_exists('traslados',$document))
				{	
					$traslados=array();		
					foreach ($document['traslados'] as $traslado) {	
						if($traslado['destino']['estado']=="A" || $traslado['destino']['estado']=="A"){							
							$subItem['estado']=ucwords(strtolower($traslado['destino']['estado']));								
							$item['estado']=ucwords(strtolower($document['estado']));
							$item['num']=$document['num'];				
							$item['fecreg']=$document['fecreg']->toDateTime();
							array_push($traslados,$subItem);
							$item['traslados']=$traslados;
							array_push($content_array,$item);
						}	
					}
				}		
			}
			$response=array('status' => 'success','expedientes'=>$content_array,'content'=>'true');
		}		
    else {
		$response=array('status' => 'failet, bad url','content'=>'false');
    }
		header('Content-Type: application/json');
		echo json_encode($response);
}
else {
	{
		$response=array('status' => 'failet','content'=>'false');
		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
?>
