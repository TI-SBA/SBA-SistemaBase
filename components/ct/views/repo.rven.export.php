<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ct/reg_ventas.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 12;
$index=0;
$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('I4', $meses[$filtros["mes"]]." ".$filtros["ano"]);
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	$proveedor = $dataRow['proveedor']['nomb'];
	if($dataRow['proveedor']['tipo_enti'])$proveedor = $dataRow['proveedor']['nomb']." ".$dataRow['proveedor']['appat']." ".$dataRow['proveedor']['apmat'];
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "dfgdf")
								  ->setCellValue('B'.$row, $dataRow['num_correlativo'])
	                              ->setCellValue('C'.$row, Date::format($dataRow["fecemi"]->sec, 'd/m/Y'))
	                              ->setCellValue('D'.$row, Date::format($dataRow["fecven"]->sec, 'd/m/Y'))
	                              ->setCellValue('E'.$row, $dataRow['tipo_comprobante']['cod'])
	                              ->setCellValue('F'.$row, $dataRow['serie_comprobante'])
	                              ->setCellValue('G'.$row, $dataRow['num_comprobante'])
	                              ->setCellValue('H'.$row, $dataRow['ticket'])
	                              ->setCellValue('I'.$row, $dataRow['tipo_doc'])
	                              ->setCellValue('J'.$row, $dataRow['num_doc'])
	                              ->setCellValue('K'.$row, $proveedor)	                              
	                              ->setCellValue('L'.$row, $dataRow['valor_facturado'])
	                              ->setCellValue('M'.$row, $dataRow['bi'])
	                              ->setCellValue('N'.$row, $dataRow['importe_exonerada'])
	                              ->setCellValue('O'.$row, $dataRow['importe_inafecta'])
	                              ->setCellValue('P'.$row, $dataRow['isc'])
	                              ->setCellValue('Q'.$row, $dataRow['igv'])
								  ->setCellValue('R'.$row, $dataRow['bi_arroz'])
	                              ->setCellValue('S'.$row, $dataRow['impuesto_arroz'])
	                              ->setCellValue('T'.$row, $dataRow['otros_tributos'])
	                              ->setCellValue('U'.$row, $dataRow['importe_total'])
	                              ->setCellValue('V'.$row, $dataRow['tc'])
	                              ->setCellValue('W'.$row, Date::format($dataRow["fecemi_mod"]->sec, 'd/m/Y'))
	                              ->setCellValue('X'.$row, $dataRow['tipo_doc_mod']['cod'])
	                              ->setCellValue('Y'.$row, $dataRow['ser_doc_mod'])
	                              ->setCellValue('Z'.$row, $dataRow['num_doc_mod']);
	                              
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-registro-de-ventas.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>