<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ct/movi.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 6;
$index=0;
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item['cuenta']['cod'])
	                              ->setCellValue('B'.$row, $item['cuenta']['descr'])
	                              ->setCellValue('C'.$row, $item['debe_anterior'])
	                              ->setCellValue('D'.$row, $item['haber_anterior'])
	                              ->setCellValue('E'.$row, "")
	                              ->setCellValue('F'.$row, "")
	                              ->setCellValue('G'.$row, "")
	                              ->setCellValue('H'.$row, "")
	                              ->setCellValue('I'.$row, "")
	                              ->setCellValue('J'.$row, "");
	                              
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-movimiento-cuentas-presupuesto.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>