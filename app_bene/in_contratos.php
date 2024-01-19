<?php
$m = new MongoClient();
$db = $m->beneficencia;
$conexion=$db->app_in_peticion;
if(isset($_GET))
{
	if(isset($_GET['search_in']))	
	{
		if(isset($_GET['si']))
		{
			$situacion=$_GET['si'];
			$find_array['situacion']=array('$regex' =>$situacion,'$options'=>'i');
		}
		$contratos= $db->in_contratos;
		$cursor = $contratos->find($find_array,['inmueble','titular','situacion','moneda','importe','fecdes','fecini','fecfin','contrato_dias','desalojo','infocorp','nro_contrato','sedapar','seal','arbitrios','con_mora','compensacion','fecreg','porcentaje','pagos']);
		$content_array=array();			
		foreach ($cursor as $document) {	
				
			$item['inmueble']=$document['inmueble']['direccion'];
			//$item['titular']=$document['titular']['nomb'];
			
			//$item['t_nomb']=$document['titular']['appat'];
			
			$item['situacion']=$document['situacion'];
			$item['moneda']=$document['moneda'];
			//$item['importe']=$document['importe'];
			//$item['fecdes']=$document['fecdes']->toDateTime();
			//$item['fecini']=$document['fecini']->toDateTime();
			//$item['fecfin']=$document['fecfin']->toDateTime();			
			//$item['contrato_dias']=$document['contrato_dias'];			
			//$item['desalojo']=$document['desalojo'];
			//$item['infocorp']=$document['infocorp'];
			//$item['nro_contrato']=$document['nro_contrato'];
			//$item['sedapar']=$document['sedapar'];
			//$item['seal']=$document['seal'];			
			//$item['arbitrios']=$document['arbitrios'];
			//$item['con_mora']=$document['con_mora'];
			//$item['compensacion']=$document['compensacion'];
			//$item['fecreg']=$document['fecreg']->toDateTime();	
			//$item['porcentaje']=$document['porcentaje'];
			
			if(array_key_exists('pagos',$document))
			{
				$pago=array();
				foreach ($document['pagos'] as $pagos) {
					$subItem['p_item']=$pagos['item'];
					$subItem['p_mes']=$pagos['mes'];
					$subItem['p_ano']=$pagos['ano'];
					$subItem['p_estado']=$pagos['estado'];					
					//$subItem['c_tipo']=$pagos['comprobante']['tipo'];
					//$subItem['c_serie']=$pagos['comprobante']['serie'];
					//$subItem['c_num']=$pagos['comprobante']['num'];			
					$subItem['d_alquiler']=$pagos['detalle']['alquiler'];
					$subItem['d_igv']=$pagos['detalle']['igv'];
					$subItem['d_moras']=$pagos['detalle']['moras'];				
					array_push($pago,$subItem);
				}
				$item['pagos']=$pago;
			}
			array_push($content_array,$item);		
		}
		$response=array('status' => 'success','contratos'=>$content_array,'content'=>'true');
	}		
    else 
	{
		$response=array('status' => 'failet, bad url','content'=>'false');
    }
		header('Content-Type: application/json');
		echo json_encode($response);
}
else 
{
	{
		$response=array('status' => 'failet','content'=>'false');
		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
?>
