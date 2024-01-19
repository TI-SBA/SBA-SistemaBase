<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cj/nicho_vida.xlsx');
$baseRow = 5;
$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('B2','NICHOS EN VIDA '.strtoupper($meses[$params["mes"]]).", ".$params["ano"] );
$total = 0;
foreach($data as $r => $item) {
	if(isset($item['espacio']['nicho'])){
		$row = $baseRow + $r;
		$nomb = $item['operacion']['ocupante']['nomb'];
		if(isset($item['operacion']['ocupante']['appat']))
			$nomb = $nomb." ".$item['operacion']['ocupante']['appat']." ".$item['operacion']['ocupante']['apmat'];
		$cliente = $item['cliente']['nomb'];
		if(isset($item['cliente']['appat']))
			$cliente = $cliente." ".$item['cliente']['appat']." ".$item['cliente']['apmat'];
		//$item['total'] = "'".$item['total'];
		$text = "Paga: ".$cliente." \n Para: ".$nomb;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $item['espacio']['nicho']['num'])
									  ->setCellValue('C'.$row, $item['espacio']['nicho']['fila'])
									  ->setCellValue('D'.$row, $item['espacio']['nicho']['pabellon']['nomb'].
									  	' '.$item['espacio']['nicho']['pabellon']['num'].
										', Piso '.$item['espacio']['nicho']['piso'])
									  ->setCellValue('E'.$row, "".$text)
		                              ->setCellValue('F'.$row, date('d', $item['fecreg']->sec))
		                              ->setCellValue('G'.$row, date('m', $item['fecreg']->sec))
		                              ->setCellValue('H'.$row, date('Y', $item['fecreg']->sec))
									  ->setCellValue('I'.$row, $item['serie'].'-'.$item['num'])
									  ->setCellValue('J'.$row, number_format($item['total'],2));
		$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setWrapText(true);
		$total += floatval($item['total']);
	}else{
		//print_r($item);die();
	}
}
/*$row++;
$row++;
$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($item['total'],2));*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Nichos en Vida '.$params['ano'].'-'.$params['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>