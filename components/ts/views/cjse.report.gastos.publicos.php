<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/reporte_GastosPublicos.xlsx'); //RUTA DE GASTOS PUBLICOS
$objPHPExcel->setActiveSheetIndexByName('Gastos');
$alphabet = range('A', 'Z');
$row = 7;
$column = 5; //F
$total = 0;
$column_clas=3; //D
$column_clas_val=[];
$row_area=6;
$row_area_val=[];

//LEER COLUMNAS DE CLASIFICADOR DE GASTOS PUBLICOS DEL TEMPLATE
for($i=$row;$i<100;$i++) {
	$temp=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($column_clas, $i)->getValue();
	if($temp!=''){
		if(strpos($temp, '.')){
			$column_clas_val[$temp]=$i;
		}
	}
	
}
//LEER FILAS DE AREA DE GASTOS PUBLICOS DEL TEMPLATE
for($i=$column;$i<100;$i++) {
	$temp=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($i, $row_area)->getValue();
	if($temp!=''){
			//$temp=explode('.',$temp);
			$row_area_val[(string)$temp]=$alphabet[$i];
	}
	
}
/*
$areas=array(
	'Gerencia Administrativa'=>'GA', ('OCT','ORH','OAS','OEI')
	'Alta Direccion'=>'AD' ,('PRESIDENCIA','GG','GA','TD','DGRE','DGBS')
	'Asesoria Legal'=>'AL', ('AL')
	'Inmuebles'=>'47', ('IN')
	'Organo Control Institucional'=>'OCI', ('OCI')
	'Planificacion y Presupuesto'=>'PP', ('PP')
	//'Comercialisacion e servicio colaterales'=>'CSC', ('AG')
	'Agua Chapi'=>'C.H.', ('AG')
	//'DONACIONES'=>'ICSA',
);
*/
for($i=0;$i<count($documentos);$i++){
	if(explode(".",$documentos[$i]['partida']['cod'])>6){
		$temp=explode(".",$documentos[$i]['partida']['cod']);
		$documentos[$i]['partida']['cod']=$temp[0].".".$temp[1].".".$temp[2].".".$temp[3].".".$temp[4].".".$temp[5];
	}
	if(isset($column_clas_val[$documentos[$i]['partida']['cod']])){
		$temp = $objPHPExcel->getActiveSheet()->getCell($row_area_val[$documentos[$i]['area']].$column_clas_val[$documentos[$i]['partida']['cod']])->getValue();
		$temp = $temp + $documentos[$i]['mont'];
		$objPHPExcel->getActiveSheet()->setCellValue($row_area_val[$documentos[$i]['area']].$column_clas_val[$documentos[$i]['partida']['cod']], "".$temp);
	}else{
		echo "<pre>";
		print_r("No se encontro en el template el valor del clasificador ".$documentos[$i]['partida']['cod']);
		echo "<br>";
		print_r($column_clas_val);
		echo "</pre>";
		die();
	}
	//$row++;
	//$total+= $sesion[$i]['saldo'];
}

//$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$total);
//$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, "".$sesion_extra['deber_final']);
//$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$sesion_extra['haber_final']);
//$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "".$sesion_extra['saldo_final']);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Estadisticas del objeto de gasto '.$metadata['ano'].'-'.$metadata['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>