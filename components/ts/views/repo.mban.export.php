<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/mov_ban.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('B1', $periodo);
$saldo = 0;
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	if($dataRow['tipo']=='D'){
		$saldo += $dataRow['monto'];
	}else{
		$saldo -= $dataRow['monto'];
	}
	//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, Date::format($dataRow["fec"]->sec, 'd'))
	                              ->setCellValue('B'.$row, $dataRow['tipo_doc']." ".$dataRow['num_doc'])
	                              ->setCellValue('C'.$row, $dataRow['tipo_origen']." ".$dataRow['num_origen'])
	                              ->setCellValue('D'.$row, $dataRow['detalle'].(($dataRow['moneda']=="D")?" - $".$dataRow['monto']." - TC S/.".$dataRow['tc']:""))
	                              ->setCellValue('E'.$row, ($dataRow['tipo']=="D")?$dataRow['monto']:"")
	                              ->setCellValue('F'.$row, ($dataRow['tipo']=="H")?$dataRow['monto']:"")
	                              ->setCellValue('G'.$row, $saldo);                            
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-mivimiento-bancos-'.$periodo.'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>

