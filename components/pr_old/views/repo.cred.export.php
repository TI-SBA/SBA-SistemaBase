<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pr/cred.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 11;
$index=0;
$r = 0;
$objPHPExcel->getActiveSheet()->setCellValue('B1', "PRESUPUESTO APLIATORIO DE INGRESOS AÑO FISCAL ".$filtros["periodo"]." CREDITO SUPLEMENTARIO NUMERO ".$filtros["num_credito"]." EN NUEVOS SOLES");
$objPHPExcel->getActiveSheet()->setCellValue('B8',$filtros["fuente"]["cod"]." ".$filtros["fuente"]["rubro"] );
foreach($data as $k => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "")
	                              ->setCellValue('B'.$row, $item["orga"]["componente"]["nomb"])
	                              ->setCellValue('C'.$row, "");	                              
	$r++;
	foreach($item["items"] as $part){
		$row = $baseRow + $r;
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $part["cod"])
		                              ->setCellValue('B'.$row, $part["nomb"])
		                              ->setCellValue('C'.$row, array_sum($part["importes"]));	                              
		$r++;
	}
}
//$objPHPExcel->getActiveSheet()->setCellValue('C'.(count($data)+$baseRow), "0.00");
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-credito-suplementario.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>