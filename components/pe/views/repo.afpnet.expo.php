<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/afpnet.xls');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$items=$data;
$baseRow = 4;
$index=0;

//$cols = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
$final=$baseRow;
$arrayrows = array('c','d','e');
$nada = false;
$count = 1;
foreach($items as $r => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $count);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $item["trabajador"]["cod_aportante"]);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "0");
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $item["trabajador"]['docident'][0]["num"]);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, strtoupper($item["trabajador"]["appat"]));
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, strtoupper($item["trabajador"]["apmat"]));
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, strtoupper($item["trabajador"]["nomb"]));
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, "S");
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, "N");
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, "N");
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, "");
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $item["remuneracion_asegurable"]);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, "0");
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, "0");
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$row, "0");
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, "N");

	$count++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="afpnet.xls"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>