<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cm/espa_vend.xlsx');
$baseRow = 5;
$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('B2','NICHOS EN VIDA '.strtoupper($meses[$params["mes"]]).", ".$params["ano"] );
$total = 0;
$row = $baseRow;
foreach($data as $r => $item) {
	if(isset($item['espacio']['nicho'])){
		if(isset($item['recibos'])){
			$row++;
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $item['espacio']['nomb'])
										  /*->setCellValue('D'.$row, $item['espacio']['nicho']['pabellon']['nomb'].
										  	' '.$item['espacio']['nicho']['pabellon']['num'].
											', Piso '.$item['espacio']['nicho']['piso'])*/
			                              ->setCellValue('C'.$row,  $item['recibos'][0]['serie'].'-'.$item['recibos'][0]['num'])
			                              ->setCellValue('D'.$row, date('d/m/Y', $item['recibos'][0]['fecreg']->sec))
										  ->setCellValue('E'.$row, number_format($item['recibos'][0]['total'],2));
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setWrapText(true);
			$total += floatval($item['recibos'][0]['total']);
		}
	}
}
$row++;
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, 'TOTAL');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, number_format($total,2));
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Espacios Vendidos '.$params['ano'].'-'.$params['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>