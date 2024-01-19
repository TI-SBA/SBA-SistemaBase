<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ct/aux_gast.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$debe = $saldo["debe_inicial"];
$haber = $saldo["haber_inicial"];
$saldo = $debe - $haber;
$baseRow = 10;
$index=0;
$monedas = array(
	"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
	"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
);
$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$clase = array(
	"RI"=>"RI",
	"RES"=>"RES",
	"PPTO"=>"PPTO",
	"NC"=>"NC",
	"CP"=>"CP."
);
$saldo_1 = 0;
$saldo_2 = 0;
$saldo_3 = 0;
$objPHPExcel->getActiveSheet()->insertNewRowBefore(9,1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "PROCESO PRESUPUESTARIO DE GASTOS ".$filtros["ano"])
							  /*->setCellValue('E2', $orga)
							  ->setCellValue('E3', $cuenmay_cod." - ".$cuenmay)
							  ->setCellValue('E4', $subcuen_cod." - ".$subcuen)
							  ->setCellValue('E5', $arrend." - ".$inmu)*/
							  ->setCellValue('H8', $saldo_1)
	                          ->setCellValue('K8', $saldo_2)
	                          ->setCellValue('N8', $saldo_3);
foreach($data as $r => $item) {
	$row = $baseRow + $r;	
	if($item["tipo"]=="O1"){	
		if($item["ejec_pres"]["tipo"]=="D"){
			$saldo_1 += $item["ejec_pres"]["monto"];
		}elseif($item["ejec_pres"]["tipo"]=="H"){
			$saldo_1 -= $item["ejec_pres"]["monto"];
		}
		if($item["asignaciones"]["tipo"]=="D"){
			$saldo_2 += $item["asignaciones"]["monto"];
		}elseif($item["asignaciones"]["tipo"]=="H"){
			$saldo_2 -= $item["asignaciones"]["monto"];
		}
		if($item["ejec_gasto"]["tipo"]=="D"){
			$saldo_3 += $item["ejec_gasto"]["monto"];
		}elseif($item["ejec_gasto"]["tipo"]=="H"){
			$saldo_3 -= $item["ejec_gasto"]["monto"];
		}
	}elseif($item["tipo_saldo"]=="O2"){
		if($item["ejec_pres"]["tipo"]=="D"){
			$saldo_1 -= $item["ejec_pres"]["monto"];
		}elseif($item["ejec_pres"]["tipo"]=="H"){
			$saldo_1 += $item["ejec_pres"]["monto"];
		}
		if($item["asignaciones"]["tipo"]=="D"){
			$saldo_2 -= $item["asignaciones"]["monto"];
		}elseif($item["asignaciones"]["tipo"]=="H"){
			$saldo_2 += $item["asignaciones"]["monto"];
		}
		if($item["ejec_gasto"]["tipo"]=="D"){
			$saldo_3 -= $item["ejec_gasto"]["monto"];
		}elseif($item["ejec_gasto"]["tipo"]=="H"){
			$saldo_3 += $item["ejec_gasto"]["monto"];
		}
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $meses[floatval(Date::format($item["fecini"]->sec, 'm'))].(($item["fecfin"]!="")?"/".$meses[floatval(Date::format($item["fecfin"]->sec, 'm'))]:""))
	                              ->setCellValue('B'.$row, Date::format($item["fecini"]->sec, 'm').(($item["fecfin"]!="")?"/".Date::format($item["fecfin"]->sec, 'm'):""))
	                              ->setCellValue('C'.$row, $clase[$item["clase"]])
	                              ->setCellValue('D'.$row, $item["num"])
	                              ->setCellValue('E'.$row, $item["detalle"])
	                              ->setCellValue('F'.$row, ($item["ejec_pres"]["tipo"]=="D")?$item["ejec_pres"]["monto"]:"")
	                              ->setCellValue('G'.$row, ($item["ejec_pres"]["tipo"]=="H")?$item["ejec_pres"]["monto"]:"")
	                              ->setCellValue('H'.$row, $saldo_1)
	                              ->setCellValue('I'.$row, ($item["asignaciones"]["tipo"]=="D")?$item["asignaciones"]["monto"]:"")
	                              ->setCellValue('J'.$row, ($item["asignaciones"]["tipo"]=="H")?$item["asignaciones"]["monto"]:"")
	                              ->setCellValue('K'.$row, $saldo_2)
								  ->setCellValue('L'.$row, ($item["ejec_gasto"]["tipo"]=="D")?$item["ejec_gasto"]["monto"]:"")
	                              ->setCellValue('M'.$row, ($item["ejec_gasto"]["tipo"]=="H")?$item["ejec_gasto"]["monto"]:"")
	                              ->setCellValue('N'.$row, $saldo_3);	                              
	$index++;
}
/*$objPHPExcel->getActiveSheet()->setCellValue('F'.(count($data)+$baseRow), $debe)
	                          ->setCellValue('G'.(count($data)+$baseRow), $haber)
	                          ->setCellValue('H'.(count($data)+$baseRow),$saldo);*/
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-auxiliares-standard-de-gastos.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>