<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
//$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/comp_boleta.xlsx');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/record_pago.xlsx');
//$data = $items;
$monedas = array(
	"S"=>"S/.",
	"D"=>"$"
);
$docs = array(
	"B"=>"B.V.",
	"F"=>"FACT."
);
$meses = array("--","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","S/.","US$");
$meses_2 = array("--","ENE-FEB","FEB-MAR","MAR-ABR","ABR-MAY","MAY-JUN","JUN-JUL","JUL-AGO","AGO-SET","SET-OCT","OCT-NOV","NOV-DIC","DIC-ENE","S/.","US$");
$condicion_arrendamiento = array(
	"CT"=>"Nuevo",
	"RE"=>"Renovación",
	"CV"=>"Convenio",
	"CU"=>"Cesión en Uso",
	"CM"=>"Comodato",
	"AC"=>"Acta de Conciliación",
	"RS"=>'Por Ocupación',
	"AU"=>'Autorización',
	"PE"=>'Penalidades',
	"TR"=>'Traspaso',
	"AD"=>'Audiencias'
);
$row = 12;

$objPHPExcel->getActiveSheet()->setCellValue('A5', $items['inmueble']['tipo']['nomb']);
$objPHPExcel->getActiveSheet()->setCellValue('B5', $items['inmueble']['sublocal']['nomb']);
$objPHPExcel->getActiveSheet()->setCellValue('D5', $items['inmueble']['direccion']);

$objPHPExcel->getActiveSheet()->setCellValue('A8', date("d/m/Y",$items['fecini']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('B8', date("d/m/Y",$items['fecfin']->sec));

$objPHPExcel->getActiveSheet()->setCellValue('D8', $items['motivo']['nomb']);

$docu = '['.$items['titular']['docident'][0]['tipo'].' '.$items['titular']['docident'][0]['num'].']';
$titular = $items['titular']['nomb'];
if($items['titular']['tipo_enti']=='P'){
	$titular.=' '.$items['titular']['appat'].' '.$items['titular']['apmat'];
}

$objPHPExcel->getActiveSheet()->setCellValue('A10', $docu.' '.$titular);
if(isset($items['pagos'])){
	foreach($items['pagos'] as $pago){
		if(isset($pago['estado'])){
			
			$dia_contrato = date('d', $items['fecini']->sec);
			$time_ini = $pago['ano'].'-'.$pago['mes'].'-'.$dia_contrato;
			if(floatval($dia_contrato)>=15){
				$fecha_ini = date('d/m/Y',strtotime('-1 months',strtotime($time_ini)));
				$dia_fin = '15';
				$time_fin = $pago['ano'].'-'.$pago['mes'].'-'.$dia_fin;
				$fecha_fin = date('d/m/Y',strtotime($time_fin));
			}else{
				$fecha_ini = date('d/m/Y',strtotime($time_ini));
				$dia_fin = date('t', $time_ini);
				$fecha_fin = date('t/m/Y',strtotime($time_ini));
			}
			//
			

			if(isset($pago['historico'])){
				foreach($pago['historico'] as $hist){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $fecha_ini.' - '.$fecha_fin);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $hist['total']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, date('d/m/Y',$hist['fec']->sec));
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $docs[$hist['tipo']]);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $hist['num']);
					$row++;
				}
			}
			if(isset($pago['comprobantes'])){
				foreach($pago['comprobantes'] as $comps){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $fecha_ini.' - '.$fecha_fin);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $comps['detalle']['alquiler']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, date('d/m/Y',$pago['comprobante']['fecreg']->sec));
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $docs[$comps['comprobante']['tipo']]);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $comps['comprobante']['num']);
					$row++;
				}
			}
			if(isset($pago['comprobante'])){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $fecha_ini.' - '.$fecha_fin);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $pago['detalle']['alquiler']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, date('d/m/Y',$pago['comprobante']['fecreg']->sec));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $docs[$pago['comprobante']['tipo']]);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $pago['comprobante']['num']);
				$row++;
			}
		}
	}
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="boleta.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>