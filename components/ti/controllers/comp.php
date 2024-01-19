<?php
class Controller_ti_comp extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ti/comp")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ti/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['local']))
			$data['local']['_id'] = new MongoId($data['local']['_id']);
		if(isset($data['ip'])){
			$first = substr($data['ip'],0,3);
			$last = substr($data['ip'],strrpos($data['ip'],'.',-1)+1);
			$data['order'] = (intval($first)*1000)+intval($last);
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("ti/comp")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TI',
				'bandeja'=>'Computadora',
				'descr'=>'Se creó la Computadora <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ti/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TI',
				'bandeja'=>'Computadora',
				'descr'=>'Se actualizó la Computadora <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ti/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}

	function execute_import_computadoras_excel(){
		global $f;

		$DATOS_FINAL_1=$this->execute_excel_a_array("DATOS_FINAL_1");
		echo "<pre>";
		print_r($DATOS_FINAL_1);
		echo "</pre>";
		die();
		$insert_base = array(
			'main_data' => array(
				'id_unico' => '--',
				'nombre_equipo' => '--',
				'local' => array(
					'local' => '--',
					'edificio' => '--',
					'piso' => 0,
				),
				'IP' => '--',
			),
			'personal' => array(
				'responsable' => array(),
				'usuario' => array(),
				'usuario_AD' => '--',
				'oficina' => array(),
				'tipo_contratacion'=>array(),
			),
			'hardware' => array(
				'basico' => array(
					'Procesador'=>'--',
					'RAM' => '--',
					'Placa' => '--',
					'CASE' => '--',
					'disco_duro' => '--',
				),
				'extras'=> array(),
			),
			'software' => array(
				'basico' => array(
					'sistema_operativo'=>array(),
					'antivirus' => array(),
					'suite_ofimatica' => array(),
				),
				'extras'=> array(),
			),
			'log' => array(),
			'metadata' => array(
				'fecreg' => new MongoDate(),
				'fecmod' => new MongoDate(),
				'autor' => $f->session->userDB,
				'trabajador' => $f->session->userDB,
			),
		);



		foreach ($DATOS_FINAL_1 as $i => $pc) {
			$computadora = $f->datastore->lg_almacenes->findOne(array('main_data.nombre_equipo'=> $pc["NOMBRE PC"]));
			if(is_null($computadora)){
				$to_insert = $insert_base;
				if(!empty($pc["NOMBRE PC"])) $to_insert['main_data']['nombre_equipo'] = $pc["NOMBRE PC"];
				$to_insert['main_data']['id_unico'] = uniqid('', true);
				if(!empty($pc["LOCAL"])) $to_insert['main_data']['local']['local'] = $pc["LOCAL"];
				if(!empty($pc["EDIFICIO"])) $to_insert['main_data']['local']['edificio'] = $pc["EDIFICIO"];
				if(!empty($pc["PISO"])) $to_insert['main_data']['local']['piso'] = floatval($pc["PISO"]);
				if(!empty($pc["IP"])) $to_insert['main_data']['IP'] = $pc["IP"];
				if(!empty($pc["A CARGO DE "])) $to_insert['personal']['responsable']['simple'] = $pc["A CARGO DE "];
				if(!empty($pc["USUARIO SISTEMA"])) $to_insert['personal']['usuario']['simple'] = $pc["USUARIO SISTEMA"];
				if(!empty($pc["AD"])) $to_insert['personal']['usuario_AD'] = $pc["AD"];
				if(!empty($pc["OFICINA"])) $to_insert['personal']['oficina']['simple'] = $pc["OFICINA"];
				if(!empty($pc["TIPO DE CONTRATO"])) $to_insert['personal']['tipo_contratacion']['simple'] = $pc["TIPO DE CONTRATO"];
				if(!empty($pc["PROCESADOR"])) $to_insert['hardware']['basico']['Procesador'] = $pc["PROCESADOR"];
				if(!empty($pc["RAM"])) $to_insert['hardware']['basico']['RAM'] = $pc["RAM"];
				if(!empty($pc["PLACA"])) $to_insert['hardware']['basico']['Placa'] = $pc["PLACA"];
				if(!empty($pc["CASE"])) $to_insert['hardware']['basico']['CASE'] = $pc["CASE"];
				if(!empty($pc["DISCO DURO"])) $to_insert['hardware']['basico']['disco_duro'] = $pc["DISCO DURO"];
				if(!empty($pc["SISTEMA OPERATIVO"])) $to_insert['software']['basico']['sistema_operativo']['nombre'] = $pc["SISTEMA OPERATIVO"];
				if(!empty($pc["LICENCIA WINDOWS"])) $to_insert['software']['basico']['sistema_operativo']['licencia_clave'] = $pc["LICENCIA WINDOWS"];
				$to_insert['software']['basico']['sistema_operativo']['tiene_licencia'] = "--";
				if(!empty($pc["ANTIVIRUS"])) $to_insert['software']['basico']['antivirus']['nombre'] = $pc["ANTIVIRUS"];
				if(!empty($pc["LICENCIA ANTIVIRUS"])) $to_insert['software']['basico']['antivirus']['licencia_clave'] = $pc["LICENCIA ANTIVIRUS"];
				$to_insert['software']['basico']['antivirus']['tiene_licencia'] = "--";
				if(!empty($pc["OFFICE"])) $to_insert['software']['basico']['suite_ofimatica']['nombre'] = $pc["OFFICE"];
				if(!empty($pc["LICENCIA OFFICE"])) $to_insert['software']['basico']['suite_ofimatica']['licencia_clave'] = $pc["LICENCIA OFFICE"];
				$to_insert['software']['basico']['suite_ofimatica']['tiene_licencia'] = "--";
				print_r($to_insert);
				$model = $f->model("ti/comp")->params(array('data'=>$to_insert))->save("insert")->items;
				//print_r($to_insert);
			}
			else{
				print_r("YA FUE AGREGADO");
			}
		}

	}
	function execute_excel_a_array($fileName){
		global $f;
		date_default_timezone_set('America/Lima');
		set_time_limit(60);

		/* PASO 1: EXTRAER INFORMACION DEL EXCEL A UN ARRAY NUMERICO */
		ini_set('display_errors', 0);
		$f->library('excel');
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load(IndexPath.DS.'zorrito_contratos/'.$fileName.'.xlsx');
		$highestColumnas = $objPHPExcel->getActiveSheet()->getHighestColumn();
		$highestFilas = $objPHPExcel->getActiveSheet()->getHighestRow();
		$highestColumnasNumber = PHPExcel_Cell::columnIndexFromString($highestColumnas);
		$baseFilas = 1;
		$datasets=[];
		for ($row = 1; $row < $highestFilas + 1; $row++) {
		    $dataset = array();
		    for ($column = 0; $column < $highestColumnasNumber; $column++) {
		        $dataset[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($column, $row)->getValue();
		    }
		    $datasets[] = $dataset;
		}
		ini_set('display_errors', 1);

		/* PASO 2: ORDENAR LA INFORMACION DEL EXCEL EN UN ARRAY ASOCIATIVO Y RETORNAR */
		$asociativo = [];
		for ($row = 1; $row < $highestFilas; $row++) {
		    for ($column = 0; $column < $highestColumnasNumber; $column++) {
		        $asociativo[$row][$datasets[0][$column]] = $datasets[$row][$column];
		    }
		}
		return $asociativo; 
		
	}

	function execute_lista_computadoras_json(){
		global $f;

		$compus = $f->model("ti/comp")->params()->get("all")->items;
		$computadoras=[];

		foreach ($compus as $i => $pc) {
			$compu_unica=array();
			$compu_unica['_id']=$pc['_id'];
			$compu_unica['id_unico']=$pc['main_data']['id_unico'];
			$compu_unica['nombre_equipo']=$pc['main_data']['nombre_equipo'];
			$computadoras[]=$compu_unica;
		}



		header("Content-type:application/json");
		echo json_encode($computadoras);		
	}

	function execute_edit(){
		global $f;
		$f->response->view("ti/comp.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ti/comp.details");
	}
}
?>