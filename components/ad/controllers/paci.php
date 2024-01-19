<?php
class Controller_ad_paci extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ad/paci")->params($params)->get("lista") );
	}

	function execute_listac(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		$registros = array();
		try{
			$hoy=date('Y-m-d');
			$params = array(
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($hoy.' 00:00:00')),
					'$lte'=>new MongoDate(strtotime($hoy.' 23:59:59'))
				),
				'modulo'=>'AD',
			);
			$comp_hoy = $f->model('cj/comp')->params($params)->get('hoy');
			//var_dump($comp_hoy);
			if(is_null($comp_hoy->items)){
				throw new Exception("Error: No se encontro pacientes que cancelaron su recibo de pago el dia de hoy");
			}
			//var_dump($comp_hoy);
			//print_r(iterator_to_array($comp_hoy));
			//print_r($comp_hoy->items);
			foreach ($comp_hoy->items as $recibo){
				//print_r($num);
				$cliente_id = $num['cliente']['_id']->{'$id'};
				if(!isset($registros[$cliente_id])){
					$temp=$f->model("ad/paci")->params(array("_id"=>$num['cliente']['_id']))->get("one_entidad")->items;
					if (!is_null($temp))
						$registros[$cliente_id] = $temp;
				}
			}
			$response['status'] = 'success';
			$response['message'] = 'Consulta realizada con exito';
			$registros = array_values($registros);
			$response['data'] = $registros;
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}

	function execute_get(){
		global $f;
		//print_r($f->request->data);
		$items = $f->model("ad/paci")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_fichasalud(){
		global $f;
		$paciente = $f->model('ad/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/salud.report.php",array('paciente'=>$paciente));
		
	}
	function execute_get_adulto(){
		global $f;
		$paciente = $f->model('ad/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/adulto.report.php",array('paciente'=>$paciente));
	}
	function execute_get_carnet(){
		global $f;
		$paciente = $f->model('ad/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/carnet.report.php",array('paciente'=>$paciente));
	}
	function execute_get_tarjeta(){
		global $f;
		$paciente = $f->model('ad/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/tarjeta.report.php",array('paciente'=>$paciente));
	}
	function execute_get_historial(){
		global $f;
		$items = $f->model("ad/paci")->params(array())->get("historial")->items;
		$f->response->json( $items );
	}
	function execute_get_historia(){
		global $f;
		$cod = $f->request->data['cod'];
		$entidad = $f->model('mg/entidad')->params(array(
			'filter'=>array('roles.paciente.hist_cli'=>intval($cod)),
			'fields'=>array(),
			'sort'=>array('_id'=>1)
		))->get('custom_data')->items;
		if($entidad!=null){
			$entidad = $entidad[0];
		}
		$f->response->json( $entidad );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['fe_regi'])){
			$data['fe_regi']=new MongoDate(strtotime($data['fe_regi']));
		}
		if(isset($data['fecha_na'])){
			$data['fecha_na']=new MongoDate(strtotime($data['fecha_na']));
		}
		if(isset($data['paciente'])){
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		}
		if(isset($data['apoderado'])){
			$data['apoderado']['_id'] = new MongoId($data['apoderado']['_id']);
		}
		if(isset($data['reli'])){
			$data['reli'] = intval($data['reli']);
		}
		if(isset($data['idio'])){
			$data['idio'] = intval($data['idio']);
		}
		if(isset($data['instr'])){
			$data['instr'] = intval($data['instr']);
		}
		if(isset($data['ocupa'])){
			$data['ocupa'] = intval($data['ocupa']);
		}
		if(isset($data['his_cli'])){
			$data['his_cli'] = floatval($data['his_cli']);
		}
		if(!isset($f->request->data['_id'])){
			$data['his_cli']= floatval($data['his_cli']);
			$data['procedencia']['departamento']= intval($data['procedencia']['departamento']);
			$data['procedencia']['provincia']= intval($data['procedencia']['provincia']);
			$data['procedencia']['distrito']= intval($data['procedencia']['distrito']);

			$data['lugar_nacimiento']['departamento']= intval($data['lugar_nacimiento']['departamento']);
			$data['lugar_nacimiento']['provincia']= intval($data['lugar_nacimiento']['provincia']);
			$data['lugar_nacimiento']['distrito']= intval($data['lugar_nacimiento']['distrito']);

			$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
			$f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id'],'data'=>array('roles.paciente.centro'=>'AD','roles.paciente.hist_cli'=>$data['his_cli'])))->save('update');

			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			if(isset($data['paciente'])){
				$data['paciente']['fullname'] = $data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'];
			}
			$model = $f->model("ad/paci")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AD',
				'bandeja'=>'Ficha frontal',
				'descr'=>'Se creó el paciente con ficha frontal <b>'.$data['his_cli'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ad/paci")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AD',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['paciente']['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ad/paci")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}

		$ficha_social = $f->model('ad/social')->params(array('filter'=>array('paciente._id'=>$data['paciente']['_id'])))->get('all')->items;
		$historias = $f->model('ad/hist')->params(array('filter'=>array('paciente._id'=>$data['paciente']['_id'])))->get('all')->items;
		$psicologia = $f->model('ad/psic')->params(array('filter'=>array('paciente._id'=>$data['paciente']['_id'])))->get('all')->items;
		$psiquiatrica = $f->model('ad/psiq')->params(array('filter'=>array('paciente._id'=>$data['paciente']['_id'])))->get('all')->items;
		if($ficha_social==null){
			$ficha_social = array(
				'paciente'=>$data['paciente'],
				'edad'=>$data["edad"],
				'grad'=>$data["instr"],
				'his'=>$data["his_cli"],
				'sexo'=>$data['sexo'],
				//'domi'=>$data["domicilios"][0]["direccion"],
				'nmie'=>'',
				'tria'=>'',
				'cfami'=>'',
				'ingr'=>'',
				'nhab'=>'',
				'tfam'=>'',
				'pres'=>'',
				'dina'=>'',
				'tipo'=>'',
				'vivi'=>'',
				'cons'=>'',
				'tsoc'=>'',
				'dsoc'=>'',
				'fecreg'=>new MongoDate(),
				'fecmod'=>new MongoDate(),
				'trabajador'=>$f->session->userDBMin,
				'autor'=>$f->session->userDBMin,
				'estado'=>'H'
			);
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
			if(isset($ficha_social['paciente']['roles'])){
				$ficha_social['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
			}else{
				$ficha_social['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
			//print_r($ficha_social);
			$f->model('ad/social')->params(array('data'=>$ficha_social))->save('insert');
		}
		if($historias==null){
			$historias = array(
				'paciente'=>$data['paciente'],
				'evoluciones'=>"",
				'clin'=>$data["his_cli"],
				'fecreg'=>new MongoDate(),
				'fecmod'=>new MongoDate(),
				'trabajador'=>$f->session->userDBMin,
				'autor'=>$f->session->userDBMin,
				'estado'=>'H'
			);
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
			if(isset($historias['paciente']['roles'])){
				$historias['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
			}else{
				$historias['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
			//print_r($historias);
			$f->model('ad/hist')->params(array('data'=>$historias))->save('insert');
		}
		if($psicologia==null){
			$psicologia = array(
				'paciente'=>$data['paciente'],
				'edad'=>$data['edad'],
				'moti'=>"",
				'refe'=>"",
				'repa'=>"",
				'orga'=>"",
				'inte'=>"",
				'perso'=>"",
				'concu'=>"",
				'his_cli'=>floatval($data["his_cli"]),
				'fecreg'=>new MongoDate(),
				'fecmod'=>new MongoDate(),
				'trabajador'=>$f->session->userDBMin,
				'autor'=>$f->session->userDBMin,
				'estado'=>'H'
			);
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
			if(isset($psicologia['paciente']['roles'])){
				$psicologia['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
			}else{
				$psicologia['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
			//print_r($historias);
			$f->model('ad/psic')->params(array('data'=>$psicologia))->save('insert');
		}
		if($psiquiatrica==null){
			$psiquiatrica = array(
				'paciente'=>$data['paciente'],
				'his_cli'=>floatval($data["his_cli"]),
				'fecreg'=>new MongoDate(),
				'fecmod'=>new MongoDate(),
				'trabajador'=>$f->session->userDBMin,
				'autor'=>$f->session->userDBMin,
				'estado'=>'H'
			);
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
			if(isset($psiquiatrica['paciente']['roles'])){
				$psiquiatrica['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
			}else{
				$psiquiatrica['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
			//print_r($historias);
			$f->model('ad/psiq')->params(array('data'=>$psiquiatrica))->save('insert');
		}
		$f->response->json($model);
	}

	function execute_edit(){
		global $f;
		$f->response->view("ad/paci.edit");
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
			$ficha= $f->model("ad/paci")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
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
			$ficha= $f->model("ad/paci")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
			if(is_null($ficha))
				throw new Exception("Error: no se encontro el paciente");
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
		//print_r($response);
		$f->response->json($response);
	}

	function execute_details(){
		global $f;
		$f->response->view("ad/paci.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ad/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('paci');
		$f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$unset'=>array('roles.paciente'=>true))
		))->save('custom');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$paciente = $f->model('ad/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$ubigeo_file = file_get_contents(IndexPath.DS."scripts/ubigeo-peru.min.json");
		//var_dump($ubigeo_file);
		$ubigeo = json_decode($ubigeo_file, true);
		//echo "asds";
		//var_dump($ubigeo);die();
		$procedencia_depa = intval($paciente['procedencia']['departamento']);
		$procedencia_prov = intval($paciente['procedencia']['provincia']);
		$procedencia_dist = intval($paciente['procedencia']['distrito']);
		foreach($ubigeo as $ubi){
			if (intval($ubi['departamento']) == $procedencia_depa && intval($ubi['provincia']) == intval('00') && intval($ubi['distrito']) == intval('00') ) {
				$paciente['procedencia']['departamento'] = $ubi['nombre'];
			}
			if (intval($ubi['departamento']) == $procedencia_depa && intval($ubi['provincia']) == $procedencia_prov && intval($ubi['distrito']) == intval('00') ) {
				$paciente['procedencia']['provincia'] = $ubi['nombre'];
			}
			if (intval($ubi['departamento']) == $procedencia_depa && intval($ubi['provincia']) == $procedencia_prov && intval($ubi['distrito']) == $procedencia_dist ) {
				$paciente['procedencia']['distrito'] = $ubi['nombre'];
			}
		}
		//print_r($paciente);die();
		/*$paciente['procedencia']['departamento'] = $procedencia_depa;
		$paciente['procedencia']['provincia'] = $procedencia_prov;
		$paciente['procedencia']['distrito'] = $procedencia_dist;*/
		$f->response->view("ad/paci.print",array('paciente'=>$paciente));
	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ad/paci')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ad/paci")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
	function execute_string_number(){
		global $f;
		$model = $f->model('ad/paci')->get('hist_cli_string')->items;
		if($model!=null){
			foreach($model as $i=>$item){
				$set = array(
					'his_cli'=>floatval($item['his_cli'])
				);
				$f->model('ad/paci')->params(array('_id'=>$item['_id'],'data'=>$set))->save('update');
				echo 'HISTORIA CLINICA '.$item['his_cli'].' REPARADA';
			}
		}
		//echo count($model);
	}
	/*function execute_migrar_categorias(){
		global $f;
		$social = $f->model('ad/social')->params(array('filter'=>array('categoria'=>'8'),'fields'=>array('paciente'=>true,'categoria'=>true)))->get('all')->items;
		if($social!=null){
			foreach($social as $soc){
				$paciente_id = $soc['paciente']['_id'];
				$paciente = $f->model('ad/paci')->params(array('_id'=>$paciente_id))->get('one_entidad')->items;
				//echo $paciente['_id']->{'$id'}.'<br />';
				//echo $paciente.'<br />';
				echo $soc['categoria'].'<br />';
				$f->model('ad/paci')->params(array('_id'=>$paciente['_id'],'data'=>array('categoria'=>'8')))->save('update');
			}
		}
	}*/
}
?>