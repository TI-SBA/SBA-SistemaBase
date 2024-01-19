<?php
class Controller_mh_padi extends Controller {
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
		$f->response->json( $f->model("mh/padi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("mh/padi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($f->request->data['all'])){
			if(isset($items['consulta'])){
				foreach ($items['consulta'] as $i=>$cons) {
					$items['consulta'][$i]['_paciente'] = $f->model("mh/paci")->params(array("_id"=>$cons['paciente']['_id']))->get("one")->items;
				}
			}
			
		}
		$f->response->json( $items );
	}
	function execute_get_parte(){
		global $f;
		$items = $f->model("mh/padi")->params()->get("parte")->items;
		$f->response->json( $items );
	}
	function execute_get_dash(){
		global $f;
		ini_set( 'memory_limit', '500M' );
		$ini = date('Y-m-01');
		$fin = date('Y-m-d');
		$params = array();
		$procedencia = [];
		$arbol = [];
		
		$items = $f->model('mh/padi')->params(array(
			'filter'=>array(
				'fech'=>array(
					'$gte'=> new MongoDate(strtotime($ini."- 2 year")),
					//'$gte'=> new MongoDate(strtotime($ini."- 8 month")),
					'$lte'=> new MongoDate()
				)
			),'fields'=>array('consulta.paciente._id'=>1)
		))->get('dash')->items;
		for($i;$i<count($items);$i++){
			if(isset($items[$i]['consulta']) && count($items[$i]['consulta'])>0){
				for($j = 0;$j<count($items[$i]['consulta']);$j++){
					$paciente = $f->model('mh/paci')->params(array('_id'=>$items[$i]['consulta'][$j]['paciente']['_id']))->get('one')->items;
					$procedencia_depa = intval($paciente['procedencia']['departamento']);
					$procedencia_prov = intval($paciente['procedencia']['provincia']);
					$procedencia_dist = intval($paciente['procedencia']['distrito']);

					if(isset($arbol[intval($paciente['procedencia']['departamento'])][intval($paciente['procedencia']['provincia'])][intval($paciente['procedencia']['distrito'])])){
						$arbol[intval($paciente['procedencia']['departamento'])][intval($paciente['procedencia']['provincia'])][intval($paciente['procedencia']['distrito'])]  +=1;
					}else{
						$arbol[intval($paciente['procedencia']['departamento'])][intval($paciente['procedencia']['provincia'])][intval($paciente['procedencia']['distrito'])]  = 1;
					}
					$items[$i]['consulta'][$j]['paciente'] = $paciente;
					
					print_r($paciente);
					
				}
				
			}
		}
		//print_r($arbol[4][1][1]);
		$f->response->json($arbol);
	}

	function execute_get_reporte(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin))),
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("reporte")->items;
		if($items !=null){
			/*if(isset($items['consulta'])){
				foreach($items['consulta'] as $consulta){
					print_r($consulta['paciente']);
				}
			}*/
		}
		

		$f->response->view("mh/quince.report.php",array('diario'=>$items));
	}
	/*-------------------------------------------------------------*/
	function execute_get_ubicacion(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin))),
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("mensual")->items;

		$f->response->view("mh/ubicacion.report.php",array('diario'=>$items));
	/*-------------------------------------------------------------*/
}
	function execute_get_reporte_cubanas(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("cubanas")->items;
		$f->response->view("mh/cubanas.report.php",array('reporte'=>$items,));
	}

	function execute_get_reporte_diagnostico(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("mensual")->items;
		$f->response->view("mh/diagnostico.report.php",array('diario'=>$items));
	}
	function execute_get_reporte_pacientes(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("reporte")->items;
		$f->response->view("mh/pacientes.report.php",array('diario'=>$items));
	}
	function execute_get_mensual(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['mes']) && isset($data['ano'])) {
			$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
			$day_fin = date('t',$fecini);
			$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("mensual")->items;
		$f->response->view("mh/mes.report.php",array('diario'=>$items));
	}
	function execute_get_psicologico(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['mes']) && isset($data['ano'])) {
			$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
			$day_fin = date('t',$fecini);
			$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("mensual")->items;
		$f->response->view("mh/psico.report.php",array('diario'=>$items));
	}
	function execute_get_doctor_mensual(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['mes']) && isset($data['ano'])) {
			$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
			$day_fin = date('t',$fecini);
			$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$items = $f->model("mh/padi")->params($params)->get("mensual")->items;
		$params = array(
				'ano'=>$data['ano'],
				'mes'=>$data['mes'],
				'inyectables_s'=>$data['inyectables_s'],
				'haldol_dosis_s'=>$data['haldol_dosis_s'],
				'haldol_aplic_s'=>$data['haldol_aplic_s'],
				'inyectables_a'=>$data['inyectables_a'],
				'haldol_dosis_a'=>$data['haldol_dosis_a'],
				'haldol_aplic_a'=>$data['haldol_aplic_a']
				);

		$f->response->view("mh/doct.mensual.report.php",array('diario'=>$items,'params'=>$params));
	}
function execute_get_doctor_MH(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['mes']) && isset($data['ano'])) {
			$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
			$day_fin = date('t',$fecini);
			$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
			$params['$and'] = array(
				array('fech'=>array('$gte'=>new MongoDate($fecini))),
				array('fech'=>array('$lte'=>new MongoDate($fecfin)))
			);
		}
		$model = $f->model("mh/padi")->params($params)->get("doctor")->items;
		$items = array();
		if($model!=null){
			foreach($model as $item){
				if(!isset($items[$item['medico']['_id']->{'$id'}])){
					$items[$item['medico']['_id']->{'$id'}] = array(
						'medico'=>$item['medico'],
						'nuevos'=>0,
						'contin'=>0,
						'reingr'=>0,
						'sin_edad'=>0,
						'ninos'=>0,
						'jovenes'=>0,
						'adultos'=>0,
						'ancianos'=>0,
						'varones'=>0,
						'mujeres'=>0
					);
				}
				$fechaarte = $item['fech']->sec;


				foreach($item['consulta'] as $diario){



					if(isset($diario['paciente']['fecha_na'])){


					if(isset($diario['paciente']['fecha_na']['sec'])){

						$fecha_nacimiento = $diario['paciente']['fecha_na']['sec'];
					}else{
						$fecha_nacimiento = $diario['paciente']['fecha_na']->sec;
					}

					$edad = $fechaarte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));

				}else{

					$edad = -1;

				}
					if($diario['paciente']['sexo']==0 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['mujeres']++;
					}

					if($diario['paciente']['sexo']==1 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['varones']++;
					}

					if($diario['esta'] == 2  &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['nuevos']++;
					}

					if($diario['esta'] == 3 &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['contin']++;
					}

					if($diario['esta'] == 4 &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['reingr']++;
					}

					if($diario['esta'] == 1 &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
					}

					if($edad<11  && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['ninos']++;
					}

					if($edad>=11 && $edad<18 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['jovenes']++;
					}

					if($edad>=18 && $edad<60 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['adultos']++;
					}

					if($edad>=60 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568' && $item['modulo'] == 'MH'){
						$items[$item['medico']['_id']->{'$id'}]['ancianos']++;
					}

				}
			}
		}

		$params = array(
			'ano'=>$data['ano'],
			'mes'=>$data['mes'],
			'modulo'=>'MH',
			'inyectables_s'=>$data['inyectables_s'],
			'haldol_dosis_s'=>$data['haldol_dosis_s'],
			'haldol_aplic_s'=>$data['haldol_aplic_s']
		);
		$f->response->view("mh/doct.report.php",array('diario'=>$items,'params'=>$params));
	}
function execute_get_doctor_AD(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['mes']) && isset($data['ano'])) {
			$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
			$day_fin = date('t',$fecini);
			$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
			$params['$and'] = array(
					array('fech'=>array('$gte'=>new MongoDate($fecini))),
					array('fech'=>array('$lte'=>new MongoDate($fecfin)))
					);
		}
		$model = $f->model("mh/padi")->params($params)->get("doctor")->items; /// TODOS LOS PARTES DIARIOS
		if($model!=null){
			foreach($model as $item){
					if($item['modulo']=='AD'){
						if(!isset($items[$item['medico']['_id']->{'$id'}])){
					$items[$item['medico']['_id']->{'$id'}] = array(
							'medico'=>$item['medico'],
							'nuevos'=>0,
							'contin'=>0,
							'reingr'=>0,
							'sin_edad'=>0,
							'ninos'=>0,
							'jovenes'=>0,
							'adultos'=>0,
							'ancianos'=>0,
							'varones'=>0,
							'mujeres'=>0


							);
				}
				$fechaarte = $item['fech']->sec;

				foreach($item['consulta'] as $diario){
					if(isset($diario['paciente']['fecha_na'])){
						if(isset($diario['paciente']['fecha_na']['sec'])){
							$fecha_nacimiento = $diario['paciente']['fecha_na']['sec'];
						}else{
							$fecha_nacimiento = $diario['paciente']['fecha_na']->sec;
						}
						$edad = $fechaarte-$fecha_nacimiento;
						$edad = floor($edad/(60*60*24*365));

					}else{
						$edad = -1;

					}

					if($diario['paciente']['sexo']== '0' && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){

						$items[$item['medico']['_id']->{'$id'}]['mujeres']++;
					}

					if($diario['paciente']['sexo']== '1' && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['varones']++;

					}
					if($diario['esta'] == '2'  &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['nuevos']++;

					}
					if($diario['esta'] == '3' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['contin']++;
					}

					if($diario['esta'] == '4' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['reingr']++;
					}
					if($edad<11  && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['ninos']++;
					}

					if($edad>=11 && $edad<18 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['jovenes']++;
					}

					if($edad>=18 && $edad<60 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['adultos']++;
					}

					if($edad>=60 && $diario['esta'] != '5' &&  $item['medico']['_id'] != '5977ae6c3e603746248b4568'  && $item['modulo'] == 'AD'){
						$items[$item['medico']['_id']->{'$id'}]['ancianos']++;

					}

					}
				}

			}

		}

							$params = array(
							'ano'=>$data['ano'],
							'mes'=>$data['mes'],
							'modulo'=>'AD',
							'inyectables_a'=>$data['inyectables_a'],
							'haldol_dosis_a'=>$data['haldol_dosis_a'],
							'haldol_aplic_a'=>$data['haldol_aplic_a']
							);

					$f->response->view("mh/doct.report.php",array('diario'=>$items,'params'=>$params));
}
function execute_save(){
					global $f;
					$data = $f->request->data;
					$data['fecmod'] = new MongoDate();
					$data['trabajador'] = $f->session->userDBMin;

					if(isset($data['fech'])){
						$data['fech']=new MongoDate(strtotime($data['fech']));
					}
					if(isset($data['medico'])){
						$data['medico']['_id'] = new MongoId($data['medico']['_id']);
					}
					if(!isset($f->request->data['_id'])){
						$data['num']= floatval($data['num']);
						$data['fecreg'] = new MongoDate();
						$data['autor'] = $f->session->userDBMin;
						$data['estado'] = 'H';
						$model = $f->model("mh/padi")->params(array('data'=>$data))->save("insert")->items;
						$f->model('ac/log')->params(array(
									'modulo'=>'MH',
									'bandeja'=>'Parte Diario',
									'descr'=>'Se creó el parte diario para <b>'.$data['medico']['nomb'].' '.$data['medico']['appat'].' '.$data['medico']['apmat'].'</b>.'
									))->save('insert');
					}else{
						$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($data['medico']['_id'])))->get("one")->items;
						$f->model('ac/log')->params(array(
									'modulo'=>'MH',
									'bandeja'=>'Parte Diario',
									'descr'=>'Se modificó el parte diario para <b>'.$vari['medico']['nomb'].' '.$vari['medico']['appat'].' '.$vari['medico']['apmat'].'</b>.'
									))->save('insert');
						$model = $f->model("mh/padi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
					}
					$f->response->json($model);
				}
				function execute_save_diario(){
					global $f;
					$data = $f->request->data;
					if(isset($data['paciente'])){
						$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
						$data['paciente']['paciente']['_id'] = new MongoId($data['paciente']['paciente']['_id']);
					}
					if(isset($f->request->data['_id'])){
						$diario = array(
								'his_cli'=>floatval($data['paciente']['his_cli']),
								'paciente'=>$data['paciente'],
								'esta'=>$data['esta'],
								'cie10'=>$data['cie10'],
								'cate'=>$data['cate']
								);
						if(!isset($data['index'])){
							$model = $f->model("mh/padi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('$push'=>array('consulta'=>$diario))))->save("custom")->items;
						}else{
							$model = $f->model("mh/padi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('$set'=>array('consulta.'.$data['index']=>$diario))))->save("custom")->items;
						}
						$f->model('ac/log')->params(array(
									'modulo'=>'MH',
									'bandeja'=>'Parte Diario',
									'descr'=>'Se creó el parte diario para <b>'.$model['medico']['nomb'].' '.$model['medico']['appat'].' '.$model['medico']['apmat'].'</b>.'
									))->save('insert');
						$f->response->json($diario);
					}
				}
				function execute_edit(){
					global $f;
					$f->response->view("mh/padi.edit");
				}
				function execute_details(){
					global $f;
					$f->response->view("mh/padi.details");
				}
				function execute_delete(){
					global $f;
					$f->model('mh/padi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('padi');
					$f->response->print("true");
				}
				function execute_delete_consulta(){
					global $f;
					$index = $f->request->data['index'];
					$parte = $f->model('mh/padi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
					if($parte!=null){
						if(isset($parte['consulta'])){
							$consultas = array();
							foreach($parte['consulta'] as $i=>$consulta){
								if($i!=$index){
									array_push($consultas, $consulta);
								}
							}
							$model = $f->model("mh/padi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('consulta'=>$consultas)))->save("update")->items;
						}
					}
					$f->response->print("true");
				}
				function execute_print(){
					global $f;
					$diario = $f->model('mh/padi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
					$ubigeo_file = file_get_contents(IndexPath.DS."scripts/ubigeo-peru.min.json");
					$ubigeo = json_decode($ubigeo_file, true);
					if(isset($diario['consulta']) && count($diario['consulta'])>0){
						foreach($diario['consulta'] as $i=>$consulta){
							$paciente = $f->model('mh/paci')->params(array('_id'=>$consulta['paciente']['_id']))->get('one')->items;
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
							$diario['consulta'][$i]['paciente'] = $paciente;
						}
					}
					//print_r($diario);
					$f->response->view("mh/padi.print",array('diario'=>$diario));


				}
				function execute_string_number(){
					global $f;
					$model = $f->model('mh/padi')->get('num_string')->items;
					if($model!=null){
						foreach($model as $i=>$item){
							$set = array(
									'num'=>floatval($item['num'])
									);
							$f->model('mh/padi')->params(array('_id'=>$item['_id'],'data'=>$set))->save('update');
							echo 'NUMERO DE PARTE '.$item['num'].' REPARADA';
						}
					}
					print_r('ENTRO');
				}
				}
				?>
