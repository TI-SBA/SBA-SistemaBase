<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/lg/cuadro_necesidades.xlsx');
$objPHPExcel->getActiveSheet()->setCellValue('D4','Dependencia')
							->setCellValue('F4',$data['organizacion']['nomb'])
							->setCellValue('D5','Periodo')
							->setCellValue('F5',$data['periodo']);
$row = 10;
foreach ($data['items'] as $item) {
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$item['item'])
							->setCellValue('C'.$row,$item['clasif']['cod'])
							->setCellValue('D'.$row,$item['clasif']['descr'])
							->setCellValue('G'.$row,$item['unidad']['nomb'])
							->setCellValue('H'.$row,$item['entrega'][0])
							->setCellValue('I'.$row,$item['entrega'][1])
							->setCellValue('J'.$row,$item['entrega'][2])
							->setCellValue('K'.$row,$item['entrega'][3])
							->setCellValue('L'.$row,$item['entrega'][4])
							->setCellValue('M'.$row,$item['entrega'][5])
							->setCellValue('N'.$row,$item['entrega'][6])
							->setCellValue('O'.$row,$item['entrega'][7])
							->setCellValue('P'.$row,$item['entrega'][8])
							->setCellValue('Q'.$row,$item['entrega'][9])
							->setCellValue('R'.$row,$item['entrega'][10])
							->setCellValue('S'.$row,$item['entrega'][11]);
	if($item['tipo']=='P'){
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$item['producto']['cod'])
							->setCellValue('F'.$row,$item['producto']['nomb']);
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'')
							->setCellValue('F'.$row,$item['servicio']);
	}
	$row++;
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Cuadro de Necesidades '.$data['periodo'].' del '.$data['organizacion']['nomb'].' al '.date('Y-m-d h:i').'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>