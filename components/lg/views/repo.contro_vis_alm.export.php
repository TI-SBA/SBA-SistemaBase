<?php
global $f;
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/lg/contr_vi_al.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 12;
$index=0;
$r = 0;

$objPHPExcel->getActiveSheet()->setCellValue('B5', $items[0][producto]["nomb"]);
$objPHPExcel->getActiveSheet()->setCellValue('B6', $items[0][producto]["cod"]);
$objPHPExcel->getActiveSheet()->setCellValue('B7', $items[0][producto][unidad]["nomb"]);
$objPHPExcel->getActiveSheet()->setCellValue('B8', $items[0][almacen]["nomb"]);
$objPHPExcel->getActiveSheet()->setCellValue('C8', " al periodo ".$params["ano"]."-".$params["mes"]);
//$objPHPExcel->getActiveSheet()->setCellValue('A1', "CONCEPTOS PERSONAL PARA :".$data[0]["contrato"]["cod"]." - ".$data[0]["contrato"]["nomb"]);
foreach($data as $k => $item) {
	$row = $baseRow + $r;
	
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, Date::format($items[$k]["fecreg"]->sec, "d/m/Y"))
	                              ->setCellValue('B'.$row, $items[$k][documento]["tipo"])
	                              ->setCellValue('C'.$row, $items[$k][documento]["cod"]);
								  if($items[$k]["tipo"]=="E"){
								  	
								  	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $items[$k]["cant"]);
								  }
									  
								  if($items[$k]["tipo"]=="S") {
								  		
								  	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $items[$k]["cant"]);
								  }	                              
	$r++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-control-visible-almacen-'.date("d-m-Y").'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>