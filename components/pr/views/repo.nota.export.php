<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pr/nota.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$data = $items;
$baseRow = 9;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "ANEXO C\nNOTA DE MODIFICACIÓN PRESUPUESTARIA Nº ".$filtros["num_nota"]."-".$filtros["ano"]);
$objPHPExcel->getActiveSheet()->setCellValue('I5',"MES DE LA MODIFICACIÓN:  - ".$filtros["ano"] );
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
	                              ->setCellValue('B'.$row, " ".$item["funcion"]["cod"])
	                              ->setCellValue('C'.$row, " ".$item["programa"]["cod"])
								  ->setCellValue('D'.$row, " ".$item["subprograma"]["cod"])
								  ->setCellValue('E'.$row, " ".$item["actividad"]["cod"])
								  ->setCellValue('F'.$row, " ".$item["componente"]["cod"])
								  ->setCellValue('G'.$row, " ".$item["meta"])
								  ->setCellValue('H'.$row, " ".$item["actividad"]["nomb"])
								  ->setCellValue('I'.$row, " ".$item["fuente"])
								  ->setCellValue('J'.$row, $item["tt"])
								  ->setCellValue('K'.$row, $item["gen"])
								  ->setCellValue('L'.$row, $item["sg1"])
								  ->setCellValue('M'.$row, $item["sg2"])
								  ->setCellValue('N'.$row, $item["e1"])
								  ->setCellValue('O'.$row, $item["e2"])
								  ->setCellValue('P'.$row, $item["hab"])
								  ->setCellValue('Q'.$row, $item["anu"]);	                              
	$r++;
}
$objPHPExcel->getActiveSheet()->setCellValue('P'.(count($data)+$baseRow), "=SUM(P8:P".(count($data)+$baseRow-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('Q'.(count($data)+$baseRow), "=SUM(Q8:Q".(count($data)+$baseRow-1).")");
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-credito-suplementario.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output'); 
?>