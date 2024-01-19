<?php
class Controller_ch_paci extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/paci")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/paci")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$social = $f->model('ch/social')->params(array('filter'=>array('paciente._id'=>$data['paciente']['_id'])))->get('one')->items;
		
		if($social !=null){
			$modulo = $social['modulo'];
			$f->$social("ch/social")->params(array('paciente._id'=>$social['paciente']['_id'],'data'=>array('modulo'=>$modulo)))->save('update')->items;
		}
		
		$f->response->json( $items );
	}
	function execute_get_fichasalud(){
		global $f;
		$paciente = $f->model('ch/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/salud.report.php",array('paciente'=>$paciente));
		
	}
	function execute_get_adulto(){
		global $f;
		$paciente = $f->model('ch/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/adulto.report.php",array('paciente'=>$paciente));
		
	}
	function execute_get_carnet(){
		global $f;
		$paciente = $f->model('ch/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/carnet.report.php",array('paciente'=>$paciente));
		
	}
	function execute_get_tarjeta(){
		global $f;
		$paciente = $f->model('ch/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/tarjeta.report.php",array('paciente'=>$paciente));
		
	}
	function execute_get_historial(){
		global $f;
		$items = $f->model("ch/paci")->params(array())->get("historial")->items;
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
		if(isset($data['fecha_mod'])){
			$data['fecha_mod']=new MongoDate(strtotime($data['fecha_mod']));
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
			$f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id'],'data'=>array('roles.paciente.centro'=>$data['modulo'],'roles.paciente.hist_cli'=>$data['his_cli'])))->save('update');
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			if(isset($data['paciente'])){
				$data['paciente']['fullname'] = $data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'];
			}
			$model = $f->model("ch/paci")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				//'modulo'=>'MH',
				'bandeja'=>'Ficha frontal',
				'descr'=>'Se creó el paciente con ficha frontal <b>'.$data['his_cli'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ch/paci")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ch/paci")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		//QUE ES A PARTIR DE ESTA PARTE, LO QUE QUERIA ES QUE DESDE LA FICHA FRONTAL SE ACTUALICE EL CAMBIO DEL CAMPO MODULO EN LA FICHA SOCIAL
		$ficha_social = $f->model('ch/social')->params(array('filter'=>array('paciente._id'=>$model['paciente']['_id'])))->get('all')->items;
		//var_dump($ficha_social)	;
		//die();
		//if($ficha_social==null){
		if(is_null($ficha_social)){
			$ficha_social = array(
				'paciente'=>$data['paciente'],
				'edad'=>$data["edad"],
				'grad'=>$data["instr"],
				'his'=>$data["his_cli"],
				'sexo'=>$data['sexo'],
				'domi'=>$data["domicilios"][0]["direccion"],
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
				'estado'=>'H',
				'modulo'=>$data['modulo']
			);
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$model['_id']))->get('one')->items;
			if(isset($ficha_social['paciente']['roles'])){
				$ficha_social['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
				$ficha_social['modulo'] = $model['modulo'];
			}else{
				$ficha_social['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
			//print_r($ficha_social);
			$f->model('ch/social')->params(array('data'=>$ficha_social))->save('insert');
			
		}else{
			//var_dump($ficha_social[0]);
		//	die();
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$model['_id']))->get('one')->items;
			if(isset($ficha_social[0]['paciente']['roles'])){
				$ficha_social[0]['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
				$ficha_social[0]['modulo'] = $model['modulo'];
			}else{
				$ficha_social[0]['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
			$f->model('ch/social')->params(array('_id'=>$ficha_social[0]['_id'],'data'=>$ficha_social[0]))->save('update');
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ch/paci.edit");
	}
	function execute_modulo(){
		global $f;
		$f->response->view("ch/paci.modulo");
	}
	function execute_details(){
		global $f;
		$f->response->view("ch/paci.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ch/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('paci');
		$f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$unset'=>array('roles.paciente'=>true))
		))->save('custom');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$paciente = $f->model('ch/paci')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
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
		$f->response->view("ch/paci.print",array('paciente'=>$paciente));
	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ch/paci')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ch/paci")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
	function execute_string_number(){
		global $f;
		$model = $f->model('ch/paci')->get('hist_cli_string')->items;
		if($model!=null){
			foreach($model as $i=>$item){
				$set = array(
					'his_cli'=>floatval($item['his_cli'])
				);
				$f->model('ch/paci')->params(array('_id'=>$item['_id'],'data'=>$set))->save('update');
				echo 'HISTORIA CLINICA '.$item['his_cli'].' REPARADA';
			}
		}
		//echo count($model);
		print_r($model);
	}
	/*function execute_migrar_categorias(){
		global $f;
		$social = $f->model('ch/social')->params(array('filter'=>array('categoria'=>'8'),'fields'=>array('paciente'=>true,'categoria'=>true)))->get('all')->items;
		if($social!=null){
			foreach($social as $soc){
				$paciente_id = $soc['paciente']['_id'];
				$paciente = $f->model('ch/paci')->params(array('_id'=>$paciente_id))->get('one_entidad')->items;
				//echo $paciente['_id']->{'$id'}.'<br />';
				//echo $paciente.'<br />';
				echo $soc['categoria'].'<br />';
				$f->model('ch/paci')->params(array('_id'=>$paciente['_id'],'data'=>array('categoria'=>'8')))->save('update');
			}
		}
	}*/
}
?>