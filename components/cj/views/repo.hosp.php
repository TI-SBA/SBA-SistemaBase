<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/mh/reporte_estadistico.xlsx');//RUTA DE PLANTILLA
//$doc = array("B.V","FACT","REC","TICK","R.H");

//$row = 9;
//$total = 0;
/*$objPHPExcel->getActiveSheet()->setCellValue('H5', "".date('d', $conceptos['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('I5', "".date('m', $conceptos['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('J5', "".date('Y', $conceptos['fecreg']->sec));
*/
//print_r($conceptos);
/*
for($i=0;$i<count($conceptos);$i++){
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, "".$conceptos[$i]['beneficiario']['nomb']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "".date('Y-m-d', $conceptos[$i]['fecreg']->sec));
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "".$doc[$conceptos[$i]['tidoc']]);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "".$conceptos[$i]['num']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, "".$conceptos[$i]['conce']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, "".$conceptos[$i]['programa']['nomb']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$conceptos[$i]['mont']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "".$conceptos[$i]['partida']['cod']);
	$row++;
	$total+= $conceptos[$i]['mont'];
}
*/
$cons_medi_nuevo = 0;
$cons_medi_conti = 0;
$cons_medi_indi = 0;
$con_es_psic_cont = 0;

for ($i=0; $i <count($conceptos); $i++) { 
	for($j = 0;$j<count($conceptos[$i]['items']);$j++){
		if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Médica - Nuevo'){
			$cons_medi_nuevo++;								
		}
		if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Médica Continuador'){
			$cons_medi_conti++;								
		}
		if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Médica Indigente'){
			$cons_medi_indi++;								
		}
		if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psicología - Continuador' || $conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psicología - Indigente' || $conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psicología - Nuevo') {
			$con_es_psic_cont++;								
		}
		
	}

}

$objPHPExcel->getActiveSheet()->setCellValue('B4', "".$cons_medi_nuevo);
$objPHPExcel->getActiveSheet()->setCellValue('B5', "".$cons_medi_conti);
$objPHPExcel->getActiveSheet()->setCellValue('B6', "".$con_es_psic_cont);
$objPHPExcel->getActiveSheet()->setCellValue('B7', "".$cons_medi_indi);
//$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$total);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Registro de Ventas '.$conceptos['ano'].'-'.$conceptos['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>