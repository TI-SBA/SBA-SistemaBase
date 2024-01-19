<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/mov_efe.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('B1', $periodo);
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['tipo_doc']." - ".$dataRow['num_doc'])
	                              ->setCellValue('B'.$row, Date::format($dataRow["fecreg"]->sec, 'd'))
	                              ->setCellValue('C'.$row, $dataRow['descr'])
	                              ->setCellValue('D'.$row, $dataRow['cuenta']['cod'])
	                              ->setCellValue('E'.$row, $dataRow['cuenta']['descr'])
	                              ->setCellValue('F'.$row, ($dataRow['tipo']=="D")?$dataRow['monto']:"")
	                              ->setCellValue('G'.$row, ($dataRow['tipo']=="H")?$dataRow['monto']:"")
	                              ->setCellValue('H'.$row, $dataRow['estado_sunat']);
	                              
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-mivimiento-efectivo-'.$periodo.'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
