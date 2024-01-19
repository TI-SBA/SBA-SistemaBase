<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/reporte_LibroBancos.xlsx');//RUTA DE PLANTILLA
$doc = array("B.V","FACT","REC","TICK","R.H");

$row = 9;
$total = 0;
$objPHPExcel->getActiveSheet()->setCellValue('F4', "".$libro['fecreg']);
/*$objPHPExcel->getActiveSheet()->setCellValue('H5', "".date('d', $libro['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('I5', "".date('m', $libro['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('J5', "".date('Y', $libro['fecreg']->sec));
*/
//print_r($libro);
/*

for($i=0;$i<count($libro);$i++){
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, "".$libro[$i]['beneficiario']['nomb']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "".date('Y-m-d', $libro[$i]['fecreg']->sec));
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "".$doc[$libro[$i]['tidoc']]);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "".$libro[$i]['num']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, "".$libro[$i]['conce']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, "".$libro[$i]['programa']['nomb']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$libro[$i]['mont']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "".$libro[$i]['partida']['cod']);
	$row++;
	$total+= $libro[$i]['mont'];
}

$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$total);
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Libro Bancos '.$libro['ano'].'-'.$libro['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>