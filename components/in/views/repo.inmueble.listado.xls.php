<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/listado_inmuebles.xlsx');
$baseRow = 2;
$row = $baseRow;
foreach($data as $r => $item) {
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item['tipo']['nomb'])
									->setCellValue('B'.$row, $item['sublocal']['nomb'])
									->setCellValue('C'.$row, $item['direccion']);
	$row++;
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado de Inmuebles al '.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>