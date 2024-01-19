<?php
class Controller_mh_psiq extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mh/psiq")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("mh/psiq")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$items['paciente'] = $f->model('mh/paci')->params(array('filter'=>array('paciente.nomb'=>$items['paciente']['paciente']['nomb'])))->get('all')->items[0];
		$f->response->json( $items );
	}
	function execute_get_evol(){
		global $f;
		$items = $f->model("mh/psiq")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo(){
		global $f;
		$items = $f->model("mh/psiq")->params()->get("codigo")->items;
		$f->response->json( $items );
	}
	function execute_get_reporte(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['mes']) && isset($data['ano'])) {
			$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
			$day_fin = date('t',$fecini);
			$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
			$params['$and'] = array(
				array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
				array('fecreg'=>array('$lte'=>new MongoDate($fecfin)))
			);
		}
		$items = $f->model("mh/psiq")->params($params)->get("reporte")->items;
	
		$f->response->view("mh/psiq.report.php",array('psiquiatrica'=>$items));
	}
	
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		

		if(isset($data['paciente'])){
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
			$data['paciente']['paciente']['_id'] = new MongoId($data['paciente']['paciente']['_id']);
		}
		if(isset($data['paciente']["his_cli"])){
			$data['paciente']["his_cli"]= floatval($data['paciente']["his_cli"]);
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("mh/psiq")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("mh/psiq")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("mh/psiq")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_evolu(){
		global $f;
		$f->response->view("mh/evolu.edit");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mh/psiq.edit");
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
			$ficha= $f->model("mh/psiq")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
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
			$f->response->view("mh/paci.edit");
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

			$ficha= $f->model("mh/psiq")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
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
		$f->response->view("mh/psiq.details");
	}
	function execute_delete(){
		global $f;
		$f->model('mh/psiq')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('psiq');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$psiquiatrica = $f->model('mh/psiq')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("mh/psiq.print",array('psiquiatrica'=>$psiquiatrica));

	}
}
?>