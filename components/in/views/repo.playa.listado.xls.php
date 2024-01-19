<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/listado_playas.xlsx');
$baseRow = 2;
$row = $baseRow;
foreach($data as $r => $item) {
	if($item['tipo']=='F'){
		$tipo = '01';
	}elseif ($item['tipo']=='B') {
		$tipo = '03';
	}
	if(''.intval($item['serie'])=='22'&&$item['tipo']=='F'){
		$item['serie'] = '0022';
	}else{
		$item['serie'] = '0'.intval($item['serie']);
	}
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item['tipo'])
									->setCellValue('B'.$row, $item['serie'])
									->setCellValue('C'.$row, $item['num'])
									->setCellValue('D'.$row, date('d/m/Y',$item['fecreg']->sec))
									->setCellValue('E'.$row, $item['cliente'])
									->setCellValue('F'.$row, $item['playa']['nomb'])
									->setCellValue('G'.$row, number_format($item['subtotal'], 2, '.', ''))
									->setCellValue('H'.$row, number_format($item['igv'], 2, '.', ''))
									->setCellValue('I'.$row, number_format($item['total'], 2, '.', ''))
									->setCellValueExplicit('A'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('B'.$row, $item['serie'], PHPExcel_Cell_DataType::TYPE_STRING);
	$row++;
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado de Comprobantes de '.date('M Y',$data[0]['fecreg']->sec).'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>