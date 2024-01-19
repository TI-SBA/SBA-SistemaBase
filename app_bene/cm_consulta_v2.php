<?php
$m = new MongoClient();
$db = $m->beneficencia;
$conexion=$db->app_cm_peticion;
if(isset($_GET)){
		if(isset($_GET['search_deceased']))	
		{
			$find_array['estado']='C';
			if(isset($_GET['appat']))
			{
				$appat=$_GET['appat'];
				$find_array['ocupantes.appat']=array('$regex' =>$appat,'$options'=>'i');
			}
			if(isset($_GET['apmat']))
			{
				$apmat=$_GET['apmat'];
				$find_array['ocupantes.apmat']=array('$regex' =>$apmat,'$options'=>'i');
			}
			if(isset($_GET['nomb']))
			{
				$nomb=$_GET['nomb'];
				$find_array['ocupantes.nomb']=array('$regex' =>$nomb,'$options'=>'i');
			}
			$espacios= $db->cm_espacios;
			$cursor = $espacios->find($find_array,['sector','estado','nomb','tipo','ocupantes','nicho','tumba','mausoleo']);
			$content_array=array();
			foreach ($cursor as $document) {
				$item['sector']=$document['sector'];
				$item['estado']=$document['estado'];
				$item['ubicacion']=ucwords(strtolower($document['nomb']));
				$ocupantes=array();
				if(array_key_exists('ocupantes',$document))
				{
					foreach ($document['ocupantes'] as $ocupante) {
						$subItem['nomb']=ucwords(strtolower($ocupante['nomb']));
						$subItem['appat']=ucwords(strtolower($ocupante['appat']));
						$subItem['apmat']=ucwords(strtolower($ocupante['apmat']));
						array_push($ocupantes,$subItem);
					}
					$item['ocupantes']=$ocupantes;
				}
				if(array_key_exists('nicho',$document)) $item['tipo']='Nicho';
				if(array_key_exists('tumba',$document)) $item['tipo']='Tumba';
				if(array_key_exists('mausoleo',$document)) $item['tipo']='Mausoleo';
				array_push($content_array,$item);
			}
			$response=array('status' => 'success consult','lista'=>$content_array,'content'=>'true');
		}
		else if(isset($_GET['search_available']))
		{
			$find_array['estado']='D';
			if(isset($_GET['sector']))
			{
				$sector=$_GET['sector'];
				$find_array['sector']=$sector;
			}
			if(isset($_GET['tipo']))
			{
				$tipo=$_GET['tipo'];
				switch ($tipo) {
				  case 'N':
					$find_array['nicho']=array('$exists' =>'true');
					break;
				  case 'T':
					$find_array['tumba']=array('$exists' =>'true');
					break;
				  case 'M':
					$find_array['mausoleo']=array('$exists' =>'true');
					break;
				}
			}
			$espacios= $db->cm_espacios;
			$cursor = $espacios->find($find_array,['sector','estado','nomb','tipo','ocupantes','nicho','tumba','mausoleo']);
			$content_array=array();
			foreach ($cursor as $document) {
				$item['id']=$document['_id'];
				$item['sector']=$document['sector'];
				$item['estado']=$document['estado'];
				$item['ubicacion']=$document['nomb'];
				if(array_key_exists('nicho',$document)) $item['tipo']='Nicho';
				if(array_key_exists('tumba',$document)) $item['tipo']='Tumba';
				if(array_key_exists('mausoleo',$document)) $item['tipo']='Mausoleo';
				array_push($content_array,$item);
			}
			$response=array('status' => 'success consult','lista'=>$content_array,'content'=>'true');
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
