<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/mov_cjb.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$data = $items;
$baseRow = 5;
$baseHead = 4;
$index=0;
$saldo = 0;
/** Headers */
$objPHPExcel->getActiveSheet()->setCellValue('E1','MOVIMIENTOS DEL LIBRO CAJA Y BANCOS '.$meses[$params["mes"]].'-'.$params["ano"]);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,"Items"); 
$objPHPExcel->getActiveSheet()->insertNewColumnBeforeByIndex(1,count($cuentas_debe));
foreach($cuentas_debe as $c_d){	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c_d["col"],$baseHead,$c_d["cod"]); 
}
$baseCol_h = 0;
$baseCol_h = count($cuentas_debe)+7;
$objPHPExcel->getActiveSheet()->insertNewColumnBeforeByIndex($baseCol_h+1,count($cuentas_haber));
foreach($cuentas_haber as $c_h){	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($baseCol_h+$c_h["col"],$baseHead,$c_h["cod"]); 
}
/** Body */
$base_d = 1;
$base_c = count($cuentas_debe)+1;
$base_h = count($cuentas_debe)+8;
$base_s = count($cuentas_debe)+8+count($cuentas_debe);
foreach($items as $r=>$item){
	$row = $baseRow + $r;	
	foreach($item["cuentas_debe"] as $cuen_d){
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cuentas_debe[$cuen_d["cuenta"]["_id"]->{'$id'}]["col"]+$base_d,$row,$cuen_d["monto"]);
	}	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$row,$r+1)
								  ->setCellValueByColumnAndRow($base_c,$row,Date::format($item["fecreg"]->sec, 'd'))
								  ->setCellValueByColumnAndRow($base_c+1,$row,$item["doc"])
								  ->setCellValueByColumnAndRow($base_c+2,$row," ".$item["num_doc"])
								  ->setCellValueByColumnAndRow($base_c+3,$row,$item["concepto"])
								  ->setCellValueByColumnAndRow($base_c+4,$row,"Falta")
								  ->setCellValueByColumnAndRow($base_c+5,$row,$item["cuenta_banco"]["cod"])
								  ->setCellValueByColumnAndRow($base_c+6,$row," ".$item["cheque"])
								  ->setCellValueByColumnAndRow($base_s,$row,$item["debe"])
								  ->setCellValueByColumnAndRow($base_s+1,$row,$item["haber"]);
	foreach($item["cuentas_haber"] as $cuen_h){
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($cuentas_haber[$cuen_h["cuenta"]["_id"]->{'$id'}]["col"]+$base_h,$row,$cuen_h["monto"]);
	}                              
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-movimiento-caja-bancos-'.$periodo.'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
