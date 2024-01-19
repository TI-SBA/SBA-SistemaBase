<?php
global $f;
set_time_limit(0);
ini_set('memory_limit', '-1');
date_default_timezone_set('America/Lima');
$f->library('excel');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objReader->setIncludeCharts(TRUE);
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ag/reporte_movimientos.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$row = 2;
$temp_stock = 0;
foreach($data as $r => $item) {
	//print_r($item);
	//die();
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $item['_id']->{'$id'});
	if(isset($item['comprobante']['numero'])) $item['comprobante']['num']=$item['comprobante']['numero'];
	if(isset($item['comprobante'])){
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, date("d/m/Y", $item['fec']->sec))
		                              ->setCellValue('C'.$row, $item['comprobante']['serie'])
									  ->setCellValue('D'.$row, $item['comprobante']['num'])
									  ->setCellValue('E'.$row, $item['producto']['nomb'])
									  ->setCellValue('G'.$row, floatval($item['cant']))
									  ->setCellValue('H'.$row, $temp_stock-floatval($item['cant']));
		$temp_stock -= floatval($item['cant']);
		/*foreach ($item['comprobante']['items'] as $prod) {
			if($prod['producto']['_id']->{'$id'}==$item['producto']['_id']->{'$id'}){
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, floatval($prod['monto']) )
											->setCellValue('H'.$row, floatval($prod['monto'])*floatval($prod['cant']) );
				break;
			}
		}*/
	//}else{
	}if(isset($item['guia'])){
		//$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, date("d/m/Y", $item['fecreg']->sec))
		//							  ->setCellValue('D'.$row, $item['guia']['num'])
		//							  ->setCellValue('E'.$row, $item['producto']['nomb'])
		//							  ->setCellValue('F'.$row, $item['cant'])
		//							  ->setCellValue('H'.$row, $temp_stock+floatval($item['cant']));
		//$temp_stock += floatval($item['cant']);
		if(isset($item['guia']['entrada'])){
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, date("d/m/Y", $item['fecreg']->sec))
									  ->setCellValue('D'.$row, $item['guia']['num'])
									  ->setCellValue('E'.$row, $item['producto']['nomb'])
									  ->setCellValue('F'.$row, $item['cant'])
									  ->setCellValue('H'.$row, $temp_stock+floatval($item['cant']));
			$temp_stock += floatval($item['cant']);
		}
		if(isset($item['guia']['salida'])){
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, date("d/m/Y", $item['fecreg']->sec))
									  ->setCellValue('D'.$row, $item['guia']['num'])
									  ->setCellValue('E'.$row, $item['producto']['nomb'])
									  ->setCellValue('G'.$row, $item['cant'])
									  ->setCellValue('H'.$row, $temp_stock-floatval($item['cant']));
			$temp_stock -= floatval($item['cant']);
		}
	}
	$row++;
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte de Movimientos.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
?>