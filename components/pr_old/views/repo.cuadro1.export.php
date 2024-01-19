<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objReader->setIncludeCharts(TRUE);
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pr/cuadro1.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$data = $items;
$baseRow = 7;
$index=0;
//$objPHPExcel->getActiveSheet()->setCellValue('A1', "ANEXO C\nNOTA DE MODIFICACIÓN PRESUPUESTARIA Nº ".$filtros["num_nota"]."-".$filtros["ano"]);
//$objPHPExcel->getActiveSheet()->setCellValue('I5',"MES DE LA MODIFICACIÓN:  - ".$filtros["ano"] );
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item["actividad"]["nomb"])
	                              ->setCellValue('B'.$row, $item["pia"])
	                              ->setCellValue('C'.$row, $item["pim"])
								  ->setCellValue('D'.$row, $item["eje"]);	                              
	$r++;
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-cuadro1.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->setIncludeCharts(true);
$objWriter->save('php://output'); 
?>