<?php
class Controller_ad_social extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ad/social")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ad/social")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		//print_r($f);
		if($items!=null){
			$items['paciente'] = $f->model('ad/paci')->params(array('filter'=>array('paciente._id'=>$items['paciente']['_id'])))->get('all')->items[0];
		}
		#Revisar si el paciente tiene historial
		$histo = $f->model("ad/hist")->params(array('_id'=>new MongoId($items['paciente']['paciente']['_id'])))->get("por_entidad")->items;
		if(!is_null($histo)){
			if(isset($histo['evoluciones']))
			{
				#En caso de que el paciente tenga historial clinico
				$items['evoluciones']=$histo['evoluciones'];
			}
		} 
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['paciente'])){
		 	$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		 	$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
		 	if(isset($data['paciente']['roles'])){
		 		$data['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
		 	}else{
		 		$data['paciente']['roles']=array(
		 			'paciente'=>$paciente['roles']['paciente']
				);
			}
		}
		// if(isset($data['paciente'])){
		// 	$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		// 	$data['paciente']['paciente']['_id'] = new MongoId($data['paciente']['paciente']['_id']);
		// }
		if(isset($data['evoluciones'])){
			for($i = 0;$i<count($data["evoluciones"]);$i++){
				if(isset($data['evoluciones'][$i]['fec'])){
					$data['evoluciones'][$i]['fec']=new MongoDate(strtotime($data['evoluciones'][$i]['fec']));
				}
				if(isset($data['evoluciones'][$i]['user']["_id"])){
					$data['evoluciones'][$i]['user']["_id"]=new MongoId($data['evoluciones'][$i]['user']["_id"]);
				}
			}
		}
		if(isset($data['apoderado']))
			$data['apoderado']['_id'] = new MongoId($data['apoderado']['_id']);
		if(isset($data['fena'])){
			$data['fena']=new MongoDate(strtotime($data['fena']));
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			if(isset($data['paciente'])){
				$data['paciente']['fullname'] = $data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'];
			}
			#Obtener el historial clinico del paciente y actualizar asi no especifique paciente
			$histo = $f->model("ad/hist")->params(array('_id'=>new MongoId($f->request->data['paciente']['_id'])))->get("por_entidad")->items;
			if(!isset($data['evoluciones'])){
				#En caso de que no se haya introducido una evolución
				if(!is_null($histo)){
					#Pero existe historial clinico
					$de_histo=$histo['evoluciones'];
					$data['evoluciones']=$de_histo;
				}
				#Si no existe historial clinico, no se hace nada
			}
			else{
				#En caso de que haya una evolución, se asume que existe un historial clinico
				$union_evol['evoluciones']=array_merge($histo['evoluciones'],$data['evoluciones']);
				$data['evoluciones']=$union_evol['evoluciones'];
				$f->model('ad/hist')->params(array('_id'=>new MongoId($histo["_id"]),'data' => $union_evol))->save('update');
			}
			$model = $f->model("ad/social")->params(array('data'=>$data))->save("insert")->items;
			#Log
			$f->model('ac/log')->params(array(
				'modulo'=>'ad',
				'bandeja'=>'Ficha Social',
				'descr'=>'Se creó la Ficha Social <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}
		else{
			$vari = $f->model("ad/social")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("ad/social")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			#Obtener el historial clinico del paciente
			$histo = $f->model("ad/hist")->params(array('_id'=>new MongoId($f->request->data['paciente']['_id'])))->get("por_entidad")->items;
			if(!isset($data['evoluciones'])){
				#En caso de que no se haya introducido una evolución
				if(!is_null($histo)){
					#Pero existe historial clinico
					$de_histo=$histo['evoluciones'];
					$data['evoluciones']=$de_histo;
				}
				#Se asume que existe un historial clinico
				#De no existir, no se hara absolutamente nada 
			}
			else{
				#En caso de que haya una evolución
				#Se asume que existe un historial clinico
				$union_evol['evoluciones']=$data['evoluciones'];
				$data['evoluciones']=$union_evol['evoluciones'];
				$f->model('ad/hist')->params(array('_id'=>new MongoId($histo["_id"]),'data' => $union_evol))->save('update');
			}
			$model = $f->model("ad/social")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;

		}
		$f->response->json($model);


	}
	
	function execute_edit(){
		global $f;
		$f->response->view("ad/social.edit");
	}

	function execute_editc(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			$ficha= $f->model("ad/social")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			if(is_null($ficha))
			{
				throw new Exception("Error: no se encontro el paciente");
			}
			$hoy=date('Y-m-d');
			$params = array(
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($hoy.' 00:00:00')),
					'$lte'=>new MongoDate(strtotime($hoy.' 23:59:59'))
				),
				'_id'=>new MongoId($ficha['paciente']['_id'])
			);
			$compr = $f->model("cj/comp")->params($params)->get("one")->items;
			if(is_null($compr))	
			{
				throw new Exception("Error: el paciente no realizo el pago previo");
			}
			$f->response->view("ad/paci.edit");
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
			$f->response->json($response);
		}
	}

	function execute_permiso(){	
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: El paciente no esta habilitado para su modificacion', 
			'data'=>array(
				'permiso'=>false
			)
		);
		try{
			$ficha= $f->model("ad/social")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			if(is_null($ficha))
			{
				throw new Exception("Error: no se encontro el paciente");
			}
			$hoy=date('Y-m-d');
			$params = array(
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($hoy.' 00:00:00')),
					'$lte'=>new MongoDate(strtotime($hoy.' 23:59:59'))
				),
				'_id'=>new MongoId($ficha['paciente']['_id'])
			);
			$compr = $f->model("cj/comp")->params($params)->get("por_entidad")->items;
			if(is_null($compr))	
			{
				throw new Exception("Error: el paciente no realizo el pago previo");
			}
			$response['data']['permiso']= true;
			$response['status'] = 'success';
			$response['message'] = 'Fueron realizados los cambios correctamente.';

		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}

	function execute_details(){
		global $f;
		$f->response->view("ad/social.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ad/social')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('social');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$social = $f->model('ad/social')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$social['paciente'] = $f->model('ad/paci')->params(array('_id'=>$social['paciente']['_id']))->get('one_entidad')->items;
		//$social['paciente'] = $f->model('mg/entidad')->params(array('_id'=>$social['paciente']['_id']))->get('one')->items;
		//print_r($social);die();
		$f->response->view("ad/social.print",array('social'=>$social));

	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ad/social')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ad/social")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
}
?>