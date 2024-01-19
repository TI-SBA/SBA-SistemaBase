<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ho/control_medicinas.xlsx');
$objPHPExcel->getActiveSheet()->setCellValue('C2','CENTRO DE SALUD MENTAL' )
							->setCellValue('C3','CONTROL DE MEDICINAS' )
							->setCellValue('B5','PACIENTE' )
							->setCellValue('C5',$data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'] )
							->setCellValue('B6','NRO. H.C.' )
							->setCellValue('C6',$data['cod_hist'] )
							->setCellValue('B7','REPORTE DEL' )
							->setCellValue('C7',date('Y-m-d h:i'));
$row = 9;
foreach ($data['medicinas'] as $medi) {
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$medi['med'] );
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'FECHA')
								->setCellValue('C'.$row,'TIPO')
								->setCellValue('D'.$row,'ENTRADA')
								->setCellValue('E'.$row,'SALIDA')
								->setCellValue('F'.$row,'SALDO')
								->setCellValue('G'.$row,'TRABAJADOR');
	$row++;
	foreach ($medi['hist'] as $hist) {
		switch ($hist['tipo']) {
			case 'E': $tipo = 'Estado'; break;
			case 'R': $tipo = 'Receta'; break;
			case 'ER': $tipo = 'Estado y Receta'; break;
			case 'D': $tipo = 'Devolucion'; break;
		}
		if(floatval($hist['cant'])<0){
			$salida = -$hist['cant'];
			$entrada = 0;
		}else{
			$entrada = $hist['cant'];
			$salida = 0;
		}
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,date('Y-m-d',$hist['fec']->sec))
								->setCellValue('C'.$row,$tipo)
								->setCellValue('D'.$row,$entrada)
								->setCellValue('E'.$row,$salida)
								->setCellValue('F'.$row,$hist['stock'])
								->setCellValue('G'.$row,$hist['trabajador']['nomb'].' '.$hist['trabajador']['appat'].' '.$hist['trabajador']['apmat']);
		$row++;
	}
	$row++;
}
//$tmp_dia = date('Y-m-d', $data[0]['fecreg']->sec);
$styleArray = array(
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
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Control de Medicinas.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>