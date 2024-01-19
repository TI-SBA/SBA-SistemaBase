<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/reporte_CajaChica.xlsx');//RUTA DE PLANTILLA
$doc = array("B.V","FACT","REC","TICK","R.H","REC.C","F.E","B.E");

$row = 9;
$total = 0;
/*$objPHPExcel->getActiveSheet()->setCellValue('H5', "".date('d', $sesion['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('I5', "".date('m', $sesion['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('J5', "".date('Y', $sesion['fecreg']->sec));
*/
//print_r($sesion);

for($i=0;$i<count($sesion);$i++){
if ($sesion[$i]['beneficiario']['tipo_enti']==='P') {$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, "".$sesion[$i]['beneficiario']['nomb']." ".$sesion[$i]['beneficiario']['appat']." ".$sesion[$i]['beneficiario']['apmat']);} 
	else if ($sesion[$i]['beneficiario']['tipo_enti']==='E'){ $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, "".$sesion[$i]['beneficiario']['nomb']); }
	else{echo "error de la busqueda";}

	$objPHPExcel->getActiveSheet()->setCellValue('H5',"".date('d'));
	$objPHPExcel->getActiveSheet()->setCellValue('I5',"".date('m'));
	$objPHPExcel->getActiveSheet()->setCellValue('J5',"".date('o'));
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$i."");
   $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "".date('Y-m-d', $sesion[$i]['fecdoc']->sec));	
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "".$doc[$sesion[$i]['tidoc']]);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "".$sesion[$i]['num']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, "".$sesion[$i]['conce']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, "".$sesion[$i]['oficina']['nomb']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$sesion[$i]['mont']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "".$sesion[$i]['partida']['cod']);
	$row++;
	$total+= $sesion[$i]['mont'];
}

$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "".$total);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Registro de Ventas '.$metadata['ano'].'-'.$metadata['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>