<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/servicios.xlsx');
$monedas = array(
	"S"=>array("nomb"=>"NUEVO SOL","simb"=>"S/."),
	"D"=>array("nomb"=>"DOLAR","simb"=>"US$")
);
$estado = array(
	"P"=>"Pendiente",
	"C"=>"Cancelado",
	"X"=>"Anulado"
);
$data = $items;
$baseRow = 6;
$index=0;
$arren = $filtros["arren"]["nomb"];
if($filtros["arren"]["tipo_enti"]=="P"){
	$arren .= " ".$filtros["arren"]["appat"]." ".$filtros["arren"]["apmat"];
}
$objPHPExcel->getActiveSheet()->setCellValue('A2', "ARRENDATARIO: ".$arren);
$objPHPExcel->getActiveSheet()->setCellValue('A3', "SERVICIO: ".$filtros["serv"]["nomb"]);
$objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
foreach($data as $r => $itemRow) {
	$cliente = $itemRow["cliente"]["nomb"];
	if($itemRow["cliente"]["tipo_enti"]=="P"){
		$cliente .= $itemRow["cliente"]["appat"]." ".$itemRow["cliente"]["apmat"];
	}
	$comprobante = "";
	if(!isset($item["comprobantes"])){
		foreach($itemRow["comprobantes"] as $k=>$comp){
			$comprobante .= $comp["serie"]." - ".$comp["num"];
			if($k>0)$comprobante.="|";
		}
	}
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $cliente)
								  ->setCellValue('B'.$row, Date::format($itemRow["fecven"]->sec,"d/m/Y"))
								  ->setCellValue('C'.$row, number_format($itemRow["importe"]),2)
								  ->setCellValue('D'.$row, $estado[$itemRow["estado"]],2)
								  ->setCellValue('D'.$row, $comprobante ,2);
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-de-morosidad.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>