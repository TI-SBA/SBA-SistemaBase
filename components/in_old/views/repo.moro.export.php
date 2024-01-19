<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/moro.xlsx');
$monedas = array(
	"S"=>array("nomb"=>"NUEVO SOL","simb"=>"S/."),
	"D"=>array("nomb"=>"DOLAR","simb"=>"US$")
);
$data = $items;
$baseRow = 4;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "REPORTE DE MOROSIDAD AL ".date("d/m/Y"));
$objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
foreach($data as $r => $itemRow) {
	$row = $baseRow + $r;
	$arren = $itemRow["arrendatario"]["nomb"];
	if($itemRow["arrendatario"]["tipo_enti"]=="P"){
		$arren = $itemRow["arrendatario"]["appat"]." ".$itemRow["arrendatario"]["apmat"].", ".$itemRow["arrendatario"]["nomb"];
	}	
	foreach($itemRow["arrendamiento"]["rentas"] as $item){
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $arren)
									  ->setCellValue('B'.$row, $itemRow["espacio"]["ubic"]["local"]["direc"]." - ".$itemRow["espacio"]["descr"])
		                              ->setCellValue('C'.$row, $item["num"])
									  ->setCellValue('D'.$row, $item["letra"])
									  ->setCellValue('E'.$row, $monedas[$item["moneda"]]["nomb"])
									  ->setCellValue('F'.$row, $item["importe"])
									  ->setCellValue('G'.$row, Date::format($item["fecpago"]->sec, 'd/m/Y'));
	}
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-de-morosidad.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>