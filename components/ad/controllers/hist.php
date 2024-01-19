<?php
class Controller_ad_hist extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ad/hist")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ad/hist")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$items['paciente'] = $f->model('ad/paci')->params(array('filter'=>array('paciente.nomb'=>$items['paciente']['nomb'])))->get('all')->items[0];
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		//try
		{
			$data['fecmod'] = new MongoDate();																										//Asignar fecha de modificacion
			$data['trabajador'] = $f->session->userDBMin;																							//Asighnar trabajador 

			if(isset($data['paciente'])){																											//Importar paciente del sys_tio
				$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
				$data['paciente']['paciente']['_id'] = new MongoId($data['paciente']['paciente']['_id']);
			}
			//$l_compr= $f->model("cj/comp")->params(array("_id"=>new MongoId($data['paciente']['paciente']['_id'])))->get("lcaja")->items;
			/*
			if(is_null($l_compr))
			{
				//Throw
			}
			if($l_compr['fecreg'] == $data['fecmod'])
			{
				//throw	
			}
			*/
			for($i = 0;$i<count($data["evoluciones"]);$i++){
				if(isset($data['evoluciones'][$i]['fec'])){
					$data['evoluciones'][$i]['fec']=new MongoDate(strtotime($data['evoluciones'][$i]['fec']));
				}
				if(isset($data['evoluciones'][$i]['user']["_id"])){
					$data['evoluciones'][$i]['user']["_id"]=new MongoId($data['evoluciones'][$i]['user']["_id"]);
				}
			}
			if(isset($data['clin'])){
				$data['clin']= floatval($data['clin']);
			}
			if(isset($data['paciente']['his_cli'])){
				$data['paciente']['his_cli'] = floatval($data['paciente']['his_cli']);
			}
			if(!isset($f->request->data['_id'])){
				$data['fecreg'] = new MongoDate();
				$data['autor'] = $f->session->userDBMin;
				$data['estado'] = 'H';
				$model = $f->model("ad/hist")->params(array('data'=>$data))->save("insert")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'IN',
					'bandeja'=>'Tipo de Local',
					'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
				))->save('insert');
			}else{
				$vari = $f->model("ad/hist")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'IN',
					'bandeja'=>'Tipo de Local',
					'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
				$model = $f->model("ad/hist")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			}
			$f->response->json($model);
		}
		//catch (Exception $e)
		{
			//
		}
	}
	function execute_edit(){
		global $f;
		$f->response->view("ad/hist.edit");
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
			$ficha= $f->model("ad/hist")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
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
			if(is_null($compr))																											//Verificar si comprobante existe
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

	function execute_permiso(){																											//Permiso que retorna comprobante si existe
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
			$ficha= $f->model("ad/hist")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
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
			if(is_null($compr))																											//Verificar si comprobante existe
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
		$f->response->view("ad/hist.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ad/hist')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('hist');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$hist = $f->model('ad/hist')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/hist.print",array('hist'=>$hist));

	}
	function execute_importar(){
		global $f;
		set_time_limit(1);
		$conexión = new MongoClient("mongodb://localhost:27017/");
		$bd = $conexión->beneficencia;
		$page = 1;
		$page_rows = 100;
		$col = $bd->ad_pacientes;
		$items = $col->find(array('check1'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
		foreach($items as $key => $item){
			print_r($item);
			$data = array(
		    	'paciente'=>$item['paciente'],
		    	'clin'=>$item['his_cli'],
				"evoluciones"=>"",
				"fecreg"=>$item['fe_regi']
		
		    );
		    $f->datastore->ad_historias_clinicas->insert($data);
		    $bd->ad_pacientes->update(array('_id'=>$item['_id']),array('$set'=>array('check1'=>true)));
		}
	}
}
?>