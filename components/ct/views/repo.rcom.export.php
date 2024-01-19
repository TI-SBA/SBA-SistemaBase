<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ct/reg_compras.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 12;
$index=0;
$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('I3', $meses[$filtros["mes"]]." ".$filtros["ano"]);
foreach($data as $r => $dataRow) {
	$proveedor = $dataRow['proveedor']['nomb'];
	if($dataRow['proveedor']['tipo_enti'])$proveedor = $dataRow['proveedor']['nomb']." ".$dataRow['proveedor']['appat']." ".$dataRow['proveedor']['apmat'];
	$bi_cfog = $dataRow['bi_cfog'];
	if($bi_cfog==null)$bi_cfog="0.00";
	$igv_cfog = $dataRow['igv_cfog'];
	if($igv_cfog==null)$igv_cfog="0.00";
	$bi_cfong = $dataRow['bi_cfong'];
	if($bi_cfong==null)$bi_cfong="0.00";
	$igv_cfong = $dataRow['igv_cfong'];
	if($igv_cfong==null)$igv_cfong="0.00";
	$bi_scf = $dataRow['bi_scf'];
	if($bi_scf==null)$bi_scf="0.00";
	$igv_scf = $dataRow['igv_scf'];
	if($igv_scf==null)$igv_scf="0.00";
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['num_correlativo'])
	                              ->setCellValue('B'.$row, Date::format($dataRow["fecemi"]->sec, 'd/m/Y'))
	                              ->setCellValue('C'.$row, Date::format($dataRow["fecven"]->sec, 'd/m/Y'))
	                              ->setCellValue('D'.$row, $dataRow['tipo_comprobante']['cod'])
	                              ->setCellValue('E'.$row, $dataRow['serie_comprobante'])
	                              ->setCellValue('F'.$row, $dataRow['anio_DUA_DSI'])
	                              ->setCellValue('G'.$row, $dataRow['num_comprobante'])
	                              ->setCellValue('H'.$row, $dataRow['tipo_doc'])
	                              ->setCellValue('I'.$row, $dataRow['num_doc'])
	                              ->setCellValue('J'.$row, $proveedor)
	                              ->setCellValue('K'.$row, $dataRow['ref'])
	                              ->setCellValue('L'.$row, $bi_cfog)
	                              ->setCellValue('M'.$row, $igv_cfog)
	                              ->setCellValue('N'.$row, $bi_cfong)
	                              ->setCellValue('O'.$row, $igv_cfong)
	                              ->setCellValue('P'.$row, $bi_scf)
	                              ->setCellValue('Q'.$row, $igv_scf)
	                              ->setCellValue('R'.$row, $dataRow['valor_no_grav'])/** fALTA s*/
	                              ->setCellValue('T'.$row, $dataRow['isc'])
	                              ->setCellValue('U'.$row, $dataRow['otros_tributos'])
	                              ->setCellValue('V'.$row, $dataRow['importe_cp'])
	                              ->setCellValue('W'.$row, $dataRow['num_cp'])
	                              ->setCellValue('X'.$row, $dataRow['num_detrac'])
	                              ->setCellValue('Y'.$row, $dataRow['fecemi_detrac'])
	                              ->setCellValue('Z'.$row, $dataRow['tc'])
	                              ->setCellValue('AA'.$row, Date::format($dataRow["fecemi_mod"]->sec, 'd/m/Y'))
	                              ->setCellValue('AB'.$row, $dataRow['tipo_doc_mod']['cod'])
	                              ->setCellValue('AC'.$row, $dataRow['ser_doc_mod'])
	                              ->setCellValue('AD'.$row, $dataRow['num_doc_mod'])
	                              ->setCellValue('AE'.$row, $dataRow['programa']['nomb']);
	                              
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-registro-de-compras.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>