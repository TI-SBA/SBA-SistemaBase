<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/auxs_caja_chica.xlsx');//RUTA DE AUXILIAR ESTANDAR
$doc = array("B.V","FACT","REC","TICK","R.H","REC.C","F.E","B.E");

$row = 11;
$total = 0;
/*$objPHPExcel->getActiveSheet()->setCellValue('H5', "".date('d', $sesion['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('I5', "".date('m', $sesion['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('J5', "".date('Y', $sesion['fecreg']->sec));
*/
//print_r($sesion);

$objPHPExcel->getActiveSheet()->setCellValue('E9', "vienen...");
$objPHPExcel->getActiveSheet()->setCellValue('G9', "".$sesion_extra['deber_anterior']);
$objPHPExcel->getActiveSheet()->setCellValue('H9', "".$sesion_extra['haber_anterior']);
$objPHPExcel->getActiveSheet()->setCellValue('I9', "".$sesion_extra['saldo_anterior']);

$objPHPExcel->getActiveSheet()->setCellValue('B10', "".date('Y-m-d', $sesion_extra['rendicion']['fecren']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('C10', "".$doc[$sesion_extra['rendicion']['tipo']]);
$objPHPExcel->getActiveSheet()->setCellValue('D10', "".$sesion_extra['rendicion']['numero']);
$objPHPExcel->getActiveSheet()->setCellValue('G10', "".$sesion_extra['rendicion']['monto']);
$objPHPExcel->getActiveSheet()->setCellValue('I10', "".$sesion_extra['saldo_inicial']);



for($i=0;$i<count($sesion);$i++){
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "".date('Y-m-d', $sesion[$i]['fecdoc']->sec));
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "".$doc[$sesion[$i]['tidoc']]);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "".$sesion[$i]['num']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, "".$sesion[$i]['beneficiario']['nomb']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, "".$sesion[$i]['conce']);
	foreach ($sesion[$i]['movimiento'] as $j => $movim) {
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, "".$movim['entrada']);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$movim['salida']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "".$movim['saldo']);
		$row++;
		$total= $movim['saldo'];
	}
	//$row++;
	//$total+= $sesion[$i]['saldo'];
}

//$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$total);
$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, "".$sesion_extra['deber_final']);
$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$sesion_extra['haber_final']);
$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "".$sesion_extra['saldo_final']);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Auxiliar Estandar'.$metadata['ano'].'-'.$metadata['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>