<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/turnos.xlsx');
$data = $items;
$row = 3;
//calculando el numero de dias en un periodo
$cols = array("D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
$nofweek = array("D","L",'M','MI','J','V','s');
$datetime1 = new DateTime($ano.'-'.$mes.'-01');
$datetime2 = new DateTime($ano.'-'.$mes.'-01');
$datetime2->modify('last day of this month');
$num_days = $datetime1->diff($datetime2)->days+1;
$lisDay = array();
$datetime1->modify('-1 day');
$objPHPExcel->getActiveSheet()->setCellValue('C1', $mes.'-'.$ano);
for($i=0;$i<$num_days;$i++){
	$datetime1->modify('+1 day');
	//$lisDay[$datetime1->format('d')] = 0;
	$objPHPExcel->getActiveSheet()->setCellValue($cols[$i].'1', $datetime1->format('d'));
	$objPHPExcel->getActiveSheet()->setCellValue($cols[$i].'2', $nofweek[$datetime1->format('w')]);
}
foreach($items as $r => $item) {
	$nomb = $item['nomb'];
	$appat = '';
	$apmat = '';
	if(isset($item['appat'])){
		$appat = $item['appat'];
	}
	if(isset($item['apmat'])){
		$apmat = $item['apmat'];
	}
	$nomb = utf8_encode($appat.' '.$apmat.' '.$nomb);
	$cod_tarjeta = '';
	if(isset($item['roles']['trabajador']['cod_tarjeta'])){
		$cod_tarjeta = ' '.$item['roles']['trabajador']['cod_tarjeta'];
	}
	//$objPHPExcel->getActiveSheet()->mergeCells('C'.($row).':C'.($row+1));
	$objPHPExcel->getActiveSheet()->setCellValue("A".$row, $item['_id']->{'$id'});
	$objPHPExcel->getActiveSheet()->setCellValue("B".$row, $cod_tarjeta);
	$objPHPExcel->getActiveSheet()->setCellValue("C".$row, $nomb);
	$row+=1;
	//$objPHPExcel->getActiveSheet()->mergeCells('C'.($row-1).':C'.$row);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="download_formato_marcacion.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?> 