<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/mov_cue.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 7;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('B1', $periodo);
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['cod_operacion'])
	                              ->setCellValue('B'.$row, Date::format($dataRow["fecreg"]->sec, 'd'))
	                              ->setCellValue('C'.$row, $dataRow['medio_pago']['descr'])
	                              ->setCellValue('D'.$row, $dataRow['descr'])
	                              ->setCellValue('E'.$row, $dataRow['entidades']['tipo_doc'])
	                              ->setCellValue('F'.$row, $dataRow['entidades']['num_doc'])
	                              ->setCellValue('G'.$row, $dataRow['entidades']['nomb'])
	                              ->setCellValue('H'.$row, $dataRow['documentos']['num'])
	                              ->setCellValue('I'.$row, $dataRow['cuenta']['cod'])
	                              ->setCellValue('J'.$row, $dataRow['cuenta']['descr'])
	                              ->setCellValue('K'.$row, ($dataRow['tipo']=="D")?$dataRow['monto']:"")
	                              ->setCellValue('L'.$row, ($dataRow['tipo']=="H")?$dataRow['monto']:"")
	                              ->setCellValue('M'.$row, $dataRow['estado_sunat']);
	                              
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-mivimiento-cuenta-corriente-'.$periodo.'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>

