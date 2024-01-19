<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/planilla.xlsx');
$monedas = array(
	"S"=>array("nomb"=>"NUEVO SOL","simb"=>"S/."),
	"D"=>array("nomb"=>"DOLAR","simb"=>"US$")
);
$data = $items;
$baseRow = 6;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('L2', $filtros["dia"]);
$objPHPExcel->getActiveSheet()->setCellValue('M2', $filtros["mes"]);
$objPHPExcel->getActiveSheet()->setCellValue('N2', $filtros["ano"]);
//$objPHPExcel->getActiveSheet()->setCellValue('A1', "REPORTE DE MOROSIDAD AL ".date("d/m/Y"));
//$objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
$r=0;
foreach($data["alquileres"] as $itemRow) {
	$row = $baseRow + $r;
	$arren = $itemRow["concepto"]["nomb"];
	if($itemRow["concepto"]["tipo_enti"]=="P"){
		$arren = $itemRow["concepto"]["appat"]." ".$itemRow["concepto"]["apmat"].", ".$itemRow["concepto"]["nomb"];
	}
	if($itemRow["moneda"]=="D"){
		$x_alq = "F";
		$x_mor = "H";
		$x_sub = "J";
		$x_igv = "L";
	}else{
		$x_alq = "G";
		$x_mor = "I";
		$x_sub = "K";
		$x_igv = "M";
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $itemRow["tipo"])
								  ->setCellValue('B'.$row, " ".$itemRow["serie"])
		                          ->setCellValue('C'.$row, " ".$itemRow["num"])
								  ->setCellValue('D'.$row, $arren)
								  ->setCellValue('E'.$row, $itemRow["tipo_inmueble"])
								  ->setCellValue($x_alq.$row, $itemRow["alquiler"])
								  ->setCellValue($x_mor.$row, $itemRow["mora"])
								  ->setCellValue($x_sub.$row, $itemRow["subtotal"])
								  ->setCellValue($x_igv.$row, $itemRow["igv"])
								  ->setCellValue('N'.$row, $itemRow["total"]);
	$r++;
}
$row = $baseRow + $r;
$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row.':C'.$row);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "1201.98")
							  ->setCellValue('D'.$row, "Otras Cuentas Por Cobrar");
$r++;
foreach($data["otros"] as $itemRow) {
	$row = $baseRow + $r;
	$arren = $itemRow["concepto"]["nomb"];
	if($itemRow["concepto"]["tipo_enti"]=="P"){
		$arren = $itemRow["concepto"]["appat"]." ".$itemRow["concepto"]["apmat"].", ".$itemRow["concepto"]["nomb"];
	}
	if($itemRow["moneda"]=="D"){
		$x_alq = "F";
		$x_mor = "H";
		$x_sub = "J";
		$x_igv = "L";
	}else{
		$x_alq = "G";
		$x_mor = "I";
		$x_sub = "K";
		$x_igv = "M";
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $itemRow["tipo"])
								  ->setCellValue('B'.$row, " ".$itemRow["serie"])
		                          ->setCellValue('C'.$row, " ".$itemRow["num"])
								  ->setCellValue('D'.$row, $arren)
								  ->setCellValue('E'.$row, $itemRow["tipo_inmueble"])
								  ->setCellValue($x_alq.$row, $itemRow["alquiler"])
								  ->setCellValue($x_mor.$row, $itemRow["mora"])
								  ->setCellValue($x_sub.$row, $itemRow["subtotal"])
								  ->setCellValue($x_igv.$row, $itemRow["igv"])
								  ->setCellValue('N'.$row, $itemRow["total"]);
	$r++;
}
$row = $baseRow + $r;
$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row.':C'.$row);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "1201.03.02")
							  ->setCellValue('D'.$row, "DERECHOS Y TASAS ADMINISTRATIVAS");
$r++;
foreach($data["tupa"] as $itemRow) {
	$row = $baseRow + $r;
	$arren = $itemRow["concepto"]["nomb"];
	if($itemRow["concepto"]["tipo_enti"]=="P"){
		$arren = $itemRow["concepto"]["appat"]." ".$itemRow["concepto"]["apmat"].", ".$itemRow["concepto"]["nomb"];
	}
	if($itemRow["moneda"]=="D"){
		$x_alq = "F";
		$x_mor = "H";
		$x_sub = "J";
		$x_igv = "L";
	}else{
		$x_alq = "G";
		$x_mor = "I";
		$x_sub = "K";
		$x_igv = "M";
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $itemRow["tipo"])
								  ->setCellValue('B'.$row, " ".$itemRow["serie"])
		                          ->setCellValue('C'.$row, " ".$itemRow["num"])
								  ->setCellValue('D'.$row, $arren)
								  ->setCellValue('E'.$row, $itemRow["tipo_inmueble"])
								  ->setCellValue($x_alq.$row, $itemRow["alquiler"])
								  ->setCellValue($x_mor.$row, $itemRow["mora"])
								  ->setCellValue($x_sub.$row, $itemRow["subtotal"])
								  ->setCellValue($x_igv.$row, $itemRow["igv"])
								  ->setCellValue('N'.$row, $itemRow["total"]);
	$r++;
}
$row = $baseRow + $r;
$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row.':C'.$row);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "2103")
							  ->setCellValue('D'.$row, "CUENTAS POR PAGAR");
$r++;
foreach($data["pago"] as $itemRow) {
	$row = $baseRow + $r;
	$arren = $itemRow["concepto"]["nomb"];
	if($itemRow["concepto"]["tipo_enti"]=="P"){
		$arren = $itemRow["concepto"]["appat"]." ".$itemRow["concepto"]["apmat"].", ".$itemRow["concepto"]["nomb"];
	}
	if($itemRow["moneda"]=="D"){
		$x_alq = "F";
		$x_mor = "H";
		$x_sub = "J";
		$x_igv = "L";
	}else{
		$x_alq = "G";
		$x_mor = "I";
		$x_sub = "K";
		$x_igv = "M";
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $itemRow["tipo"])
								  ->setCellValue('B'.$row, " ".$itemRow["serie"])
		                          ->setCellValue('C'.$row, " ".$itemRow["num"])
								  ->setCellValue('D'.$row, $arren)
								  ->setCellValue('E'.$row, $itemRow["tipo_inmueble"])
								  ->setCellValue($x_alq.$row, $itemRow["alquiler"])
								  ->setCellValue($x_mor.$row, $itemRow["mora"])
								  ->setCellValue($x_sub.$row, $itemRow["subtotal"])
								  ->setCellValue($x_igv.$row, $itemRow["igv"])
								  ->setCellValue('N'.$row, $itemRow["total"]);
	$r++;
}
$row = $baseRow + $r + 2;
$abc = array("E","F","G","H","I","j","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "FACTURAS ANULADAS");
foreach($data["facturas_anuladas"] as $i=>$fact){
	$objPHPExcel->getActiveSheet()->setCellValue($abc[$i].$row, $fact["serie"]."-".$fact["num"]);
}
$r++;
$row = $baseRow + $r + 2;
$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "BOLETAS ANULADAS");
foreach($data["boletas_anuladas"] as $i=>$fact){
	$objPHPExcel->getActiveSheet()->setCellValue($abc[$i].$row, $fact["serie"]."-".$fact["num"]);
}
$r++;
$row = $baseRow + $r + 2;
$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "RECIBOS ANULADOS");
foreach($data["facturas_anuladas"] as $i=>$fact){
	$objPHPExcel->getActiveSheet()->setCellValue($abc[$i].$row, $fact["serie"]."-".$fact["num"]);
}
$r++;
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-de-planilla-de-cobranza.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>