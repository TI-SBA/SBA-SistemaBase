<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/daot.xlsx');
$baseRow = 2;
$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$row = 0;
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$base = 0;
	$igv = 0;
	foreach ($item['conceptos'] as $key=>$conc) {
		if($conc['cuenta']['cod']=='2101.010501'||$conc['cuenta']['cod']=='2101.010503.47'){
			$igv += floatval($conc['monto']);
		}else{
			$base += floatval($conc['monto']);
		}
	}
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item['_id']['cliente_id']->{'$id'})
								  ->setCellValue('E'.$row, $item['cliente']['nomb'])
	                              ->setCellValue('G'.$row, number_format($base, 2, '.', ''))
								  ->setCellValue('H'.$row, number_format($igv, 2, '.', ''))
								  ->setCellValue('I'.$row, number_format($item['total'], 2, '.', ''))
								  ->setCellValue('J'.$row, '=SUM(G'.$row.':H'.$row.')');
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="DAOT.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>