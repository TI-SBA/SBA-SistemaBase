<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/situacion_actual.xlsx');
$monedas = array(
	"S"=>"S/.",
	"D"=>"$"
);
$meses = array("--","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","S/.","US$");
$meses_2 = array("--","ENE-FEB","FEB-MAR","MAR-ABR","ABR-MAY","MAY-JUN","JUN-JUL","JUL-AGO","AGO-SET","SET-OCT","OCT-NOV","NOV-DIC","DIC-ENE","S/.","US$");
$row = 6;
$objPHPExcel->getActiveSheet()->setCellValue('C3', date("d/m/Y h:i") );
foreach ($inmuebles as $i=>$inmu) {
	if(!isset($inmu['desocupado'])){
		foreach ($inmu['contratos'] as $j=>$cont) {
			$titular = $cont['titular']['nomb'];
			if(isset($cont['titular']['appat']))
				$titular = $cont['titular']['appat'].' '.$cont['titular']['apmat'].', '.$cont['titular']['nomb'];
			$dia_contrato = intval(date('d', $cont['fecini']->sec));
			$total = 0;
			$ultimo = 'NO SE PAGO NINGUNA CUOTA';
			foreach ($cont['pagos'] as $pago) {
				$ult = false;
				if(!isset($pago['estado']))
					$total += floatval($cont['importe']);
				elseif($pago['estado']=='P'){
					$total += (floatval($cont['importe'])-floatval($pago['total']));
					$ult = true;
				}else{
					$ult = true;
				}
				if($ult==true){
					if($dia_contrato==16){
						$pago['mes'] = $pago['mes']-1;
						if($pago['mes']==0) $pago['mes'] = 12;
						$ultimo = $meses_2[$pago['mes']].' - '.$pago['ano'];
					}else{
						$ultimo = $meses[$pago['mes']].' - '.$pago['ano'];
					}
				}
			}
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $inmu['direccion'])
										->setCellValue('C'.$row, $titular)
										->setCellValue('D'.$row, $cont['motivo']['nomb'])
										->setCellValue('E'.$row, date("d/m/Y",$cont['fecini']->sec).' <-> '.date("d/m/Y",$cont['fecfin']->sec))
										->setCellValue('F'.$row, $total)
										->setCellValue('G'.$row, $ultimo);
			$row++;
			if(($j+1)==sizeof($inmu['contratos'])){
				if($cont['fecfin']->sec<strtotime(date('Y-m-d'))){
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $inmu['direccion'])
												->setCellValue('C'.$row, $titular)
												->setCellValue('D'.$row, 'INMUEBLE DESOCUPADO')
												->setCellValue('G'.$row, $ultimo);
					$row++;
				}
			}
		}
	}else{
		$titular = $inmu['contrato']['titular']['nomb'];
		if(isset($inmu['contrato']['titular']['appat']))
			$titular = $inmu['contrato']['titular']['appat'].' '.$inmu['contrato']['titular']['apmat'].', '.$inmu['contrato']['titular']['nomb'];
		$ultimo = 'NO SE PAGO NINGUNA CUOTA';
		foreach ($inmu['contrato']['pagos'] as $pago) {
			$ult = false;
			if(!isset($pago['estado'])){
					//
			}elseif($pago['estado']=='P'){
				$ult = true;
			}else{
				$ult = true;
			}
			if($ult==true){
				if($dia_contrato==16){
					$ultimo = $meses_2[$pago['mes']].' - '.$pago['ano'];
				}else{
					$ultimo = $meses[$pago['mes']].' - '.$pago['ano'];
				}
			}
		}
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $inmu['direccion'])
									->setCellValue('C'.$row, $titular)
									->setCellValue('D'.$row, 'INMUEBLE DESOCUPADO')
									->setCellValue('F'.$row, 'CANCELADO')
									->setCellValue('G'.$row, $ultimo);
		$row++;
	}
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Situacion Actual de Inmuebles al '.date('Y-m-d h:i').'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>