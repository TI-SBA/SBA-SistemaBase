<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/garantias.xlsx');

$objPHPExcel->getActiveSheet()->setCellValue('B1', $titular['nomb'].' '.$titular['appat'].' '.$titular['apmat'])
							->setCellValue('B2', $inmueble['direccion'])
							->setCellValue('B3', date('Y-m-d H:i'))
							->setCellValue('A4', 'Tipo de Cambio')
							->setCellValue('B4', $tc['valor']);
$row = 6;
foreach($data as $rq => $items) {
	foreach ($items['garantias'] as $r => $item) {
		switch($item['tipo']){
			case 'RI': $tipo = 'Recibo de Ingresos'; break;
			case 'RC': $tipo = 'Recibo de Caja'; break;
			case 'NC': $tipo = 'Nota Contable'; break;
			case 'FACT': $tipo = 'Factura'; break;
			case 'CP': $tipo = 'Comprobante de Pago'; break;
			case 'BV': $tipo = 'Boleta de Venta'; break;
		}
		switch($item['moneda']){
			case 'D': $item['moneda'] = 'Dolares'; break;
			case 'S': $item['moneda'] = 'Soles'; break;
		}
		$item['importe'] = str_replace(',','.',$item['importe']);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, date('Y-m-d',$item['fec']->sec))
									  ->setCellValue('B'.$row, $tipo)
									  ->setCellValue('C'.$row, $item['num'])
		                              ->setCellValue('D'.$row, $item['moneda'])
		                              ->setCellValue('E'.$row, number_format($item['importe'], 2, '.', ''));
		if($item['dev_fec']!=null){
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, date('Y-m-d',$recibo['dev_fec']->sec))
									 	  ->setCellValue('G'.$row, number_format($item['dev_importe'], 2, '.', ''));
		}
		$row++;
	}
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Garantias de '.$titular['nomb'].' '.$titular['appat'].' '.$titular['apmat'].' ('.$inmueble['direccion'].') al '.date('Y-m-d H:i').'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>