<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/planilla_inm.xlsx');

$baseRow = 6;
$row = $baseRow;
foreach($data as $m => $modulo) {
	foreach ($modulo as $d => $documento) {
		$autor_ultimo = (isset($documento[0]['autor'])) ? $documento[0]['autor'] : "--" ;
		$autor_primero = (isset($documento[1]['autor'])) ? $documento[1]['autor'] : "--" ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $m)
									  ->setCellValue('C'.$row, $d)
									  ->setCellValue('D'.$row, $autor_ultimo)
								 	  ->setCellValue('E'.$row, $documento[0]['fecha'])
								 	  ->setCellValue('F'.$row, $autor_primero)
								 	  ->setCellValue('G'.$row, $documento[1]['fecha'])
								 	  ->setCellValue('H'.$row, $documento['total']);
        $row++;
	}
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_coleccion');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>