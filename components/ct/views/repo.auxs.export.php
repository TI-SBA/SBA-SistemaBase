<?php
global $f;
$f->library('excel');
$orga = $saldo["organizacion"]["nomb"];
$cuenmay = $saldo["cuenta_mayor"]["descr"];
$cuenmay_cod = $saldo["cuenta_mayor"]["cod"];
$subcuen = $saldo["sub_cuenta"]["descr"];
$subcuen_cod = $saldo["sub_cuenta"]["cod"];
$arren = $saldo["arrendatario"];
$inmu = $saldo["inmueble"]["nomb"];
if($arren!=null){
	$arrend = $arren["nomb"];
	if($arren["tipo_enti"]=="P"){
		$arrend = $arren["nomb"]." ".$arren["appat"]." ".$arren["apmat"];
	}
}
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ct/aux_est.xlsx');
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
	"CP"=>"C. P.",
	"NC"=>"N. C.",
	"OS"=>"O. S.",
	"OC"=>"O. C.",
	"RI"=>"R. I.",
	"PC"=>"P. C."
);
$objPHPExcel->getActiveSheet()->insertNewRowBefore(9,1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "AUXILIAR ESTANDAR ".$periodo)
							  ->setCellValue('E2', $orga)
							  ->setCellValue('E3', $cuenmay_cod." - ".$cuenmay)
							  ->setCellValue('E4', $subcuen_cod." - ".$subcuen)
							  ->setCellValue('E5', $arrend." - ".$inmu)
							  ->setCellValue('F8', $debe)
	                          ->setCellValue('G8', $haber)
	                          ->setCellValue('H8', $saldo);
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	if($item["tipo"]=="D"){
		$debe = $debe + $item["monto"];
		$saldo = $saldo + $item["monto"];
		//$this->SetXY(215,$y);$this->MultiCell(25,5,number_format($item["monto"],2,".", ","),'0','R');
	}else{
		$haber = $haber + $item["monto"];
		$saldo = $saldo - $item["monto"];
		//$this->SetXY(240,$y);$this->MultiCell(25,5,number_format($item["monto"],2,".", ","),'0','R');
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $meses[floatval(Date::format($item["fec"]->sec, 'm'))])
	                              ->setCellValue('B'.$row, Date::format($item["fec"]->sec, 'd'))
	                              ->setCellValue('C'.$row, $clase[$item["clase"]])
	                              ->setCellValue('D'.$row, $item["num"])
	                              ->setCellValue('E'.$row, $item["detalle"])
	                              ->setCellValue('F'.$row, ($item["tipo"]=="D")?$item["monto"]:"")
	                              ->setCellValue('G'.$row, ($item["tipo"]=="H")?$item["monto"]:"")
	                              ->setCellValue('H'.$row, $saldo);
	                              
	$index++;
}
$objPHPExcel->getActiveSheet()->setCellValue('F'.(count($data)+$baseRow), $debe)
	                          ->setCellValue('G'.(count($data)+$baseRow), $haber)
	                          ->setCellValue('H'.(count($data)+$baseRow),$saldo);
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-registro-de-compras.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>