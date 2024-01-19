<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cm/espacios.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$baseRow = 4;
$index=0;
$rr = 0;
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$ocup = "";
	if(isset($item['ocupantes'])){
		foreach ($item['ocupantes'] as $oc) {
			$ocup .= ' - '.$oc['nomb'];
			if(isset($oc['appat']))
				$ocup .= ' '.$oc['appat'].' '.$oc['apmat'];
		}
	}
	$prop = "";
	if(isset($item['propietario'])){
		$prop .= $item['propietario']['nomb'];
		if(isset($item['propietario']['appat']))
			$prop .= ' '.$item['propietario']['appat'].' '.$item['propietario']['apmat'];
	}
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $r+1)
	                              ->setCellValue('C'.$row, $item['nomb'])
								  ->setCellValue('D'.$row, $prop)
								  ->setCellValue('E'.$row, $ocup)
								  ->setCellValue('F'.$row, $item['total']);	
}
/*$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 12,
        'name'  => 'Verdana'
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
    )
);
$objPHPExcel->getActiveSheet()->getStyle('L'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('P'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('V'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AD'.$row)->applyFromArray($styleArray);*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Registro de Espacios.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>