<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/deudores.xlsx');
$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Lista de Deudores al '.date('Y-m-d'))
								->setCellValue('B4', date('Y-m-d'));
$row = 7;
foreach($titulares as $r => $item) {
	if($item['total']!=0 || $item['total_d']!=0){
		$cliente = $item['titular']['nomb'];
		if(isset($item['titular']['appat']))
			$cliente .= ' '.$item['titular']['appat'].' '.$item['titular']['apmat'];
		$doc = '';
		$tipo = '';
		if(isset($item['titular']['docident'])){
			if(isset($item['titular']['docident'][0])){
				$doc = $item['titular']['docident'][0]['num'];
				$tipo = $item['titular']['docident'][0]['tipo'];
			}
		}
		$direc = '';
		if(isset($item['titular']['domicilios'])){
			if(isset($item['titular']['domicilios'][0])){
				$direc = $item['titular']['domicilios'][0]['direccion'];
			}
		}
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $tipo)
									  ->setCellValue('C'.$row, $doc)
									  ->setCellValue('D'.$row, $cliente)
									  ->setCellValue('E'.$row, $direc)
									  ->setCellValue('F'.$row, $item['total'])
									  ->setCellValue('G'.$row, $item['total_d'])
									  ->setCellValue('H'.$row, $item['inmuebles'])
									  ->setCellValue('I'.$row, $item['ids'])
									  ->setCellValue('J'.$row, date('Y-m-d',$item['ult']->sec))
									  ->setCellValue('K'.$row, $item['aval']);
		$row++;
	}
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Lista de Deudores al '.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>