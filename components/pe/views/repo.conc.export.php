<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/conceptos.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 6;
$index=0;
$r = 0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "CONCEPTOS PERSONAL PARA :".$data[0]["contrato"]["cod"]." - ".$data[0]["contrato"]["nomb"]);
foreach($data as $k => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item["nomb"])
	                              ->setCellValue('B'.$row, $item["cod"])
	                              ->setCellValue('C'.$row, $item["tipo"])
								  ->setCellValue('D'.$row, $item["descr"])
								  ->setCellValue('E'.$row, $item["formula"]);	                              
	$r++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-conceptos-personal-'.date("Y").'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>