<?php
class Controller_lg_alte extends Controller {
	
	function execute_get(){
		global $f;
		$items = $f->model("lg_alte")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	
	//acomodar a la estructura del excel
	function execute_import_items_excel(){
		global $f;
		$DATOS_FINAL=$this->execute_excel_a_array("Cod_QR_Log_Patri");	
		$insert_base = array(
			'main_data' => array(
				//'id_unico' => '--',
				'N' => '--',
				'programa' => 'Sin programa',
				'oficina' => 'Sin oficina',
				'responsable' => 'Sin responsable',
				'cod_patri' => 'Sin codigo',
				'descripcion' => 'Sin descripcion',
				'estado' => 'Sin estado',
				'observaciones' => 'Sin observaciones',
			),
			'log' => array(),
			'metadata' => array(
				'fecreg' => new MongoDate(),
				'fecmod' => new MongoDate(),
				'autor' => $f->session->userDB,
				'trabajador' => $f->session->userDB,
			),
		);

		foreach ($DATOS_FINAL as $i => $itm) {
			$to_insert = $insert_base;
		    //$to_insert['main_data']['id_unico'] = uniqid('', true);
			if(!empty($itm["N"])) $to_insert['main_data']['N'] = $itm["N"];
			if(!empty($itm["PROGRAMA"])) $to_insert['main_data']['programa'] = $itm["PROGRAMA"];
			if(!empty($itm["OFICINA"])) $to_insert['main_data']['oficina'] = $itm["OFICINA"];
			if(!empty($itm["RESPONSABLE"])) $to_insert['main_data']['responsable'] = $itm["RESPONSABLE"];
			if(!empty($itm["CODIGO PATRIMONIAL"])) $to_insert['main_data']['cod_patri'] = $itm["CODIGO PATRIMONIAL"];
			if(!empty($itm["DESCRIPCION"])) $to_insert['main_data']['descripcion'] = $itm["DESCRIPCION"];
			if(!empty($itm["ESTADO"])) $to_insert['main_data']['estado'] = $itm["ESTADO"];
			if(!empty($itm["OBSERVACIONES"])) $to_insert['main_data']['observaciones'] = $itm["OBSERVACIONES"];
			//
			$model = $f->model("lg/alte")->params(array('data'=>$to_insert))->save("insert")->items;	
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
}
?>