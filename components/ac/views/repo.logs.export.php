<?php
global $f;
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ac/logs.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 8;
$index=0;
$r = 0;
$modulos = array(
	"0"=>"Todos",
	"MG"=>"Maestros Generales",
	"TD"=>"Tramite Documentario",
	"CM"=>"Cementerio",
	"IN"=>"Inmuebles",
	"LG"=>"Logistica",
	"PR"=>"Planificacion y Presupuesto",
	"PE"=>"Personal",
	"AL"=>"Asesoria Legal",
	"CT"=>"Contabilidad",
	"CJ"=>"Caja",
	"TS"=>"Tesoreria",
	"AC"=>"Seguridad",
);
$objPHPExcel->getActiveSheet()->setCellValue('B2', $modulos[$filtros["modulo"]]);
$objPHPExcel->getActiveSheet()->setCellValue('B3', $filtros["desde"]);
$objPHPExcel->getActiveSheet()->setCellValue('B4', $filtros["hasta"]);
$objPHPExcel->getActiveSheet()->setCellValue('B5', $filtros["trabajador"]);
//$objPHPExcel->getActiveSheet()->setCellValue('A1', "CONCEPTOS PERSONAL PARA :".$data[0]["contrato"]["cod"]." - ".$data[0]["contrato"]["nomb"]);
foreach($data as $k => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $helper->replace_acc($item["bandeja"]))
	                              ->setCellValue('B'.$row, $helper->replace_acc($item["descr"]))
	                              ->setCellValue('C'.$row, $item["entidad"]["nomb"]." ".$item["entidad"]["appat"]." ".$item["entidad"]["apmat"])
								  ->setCellValue('D'.$row, Date::format($item["fecreg"]->sec, "d/m/Y H:i"));	                              
	$r++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-logs-usuarios-'.date("d-m-Y").'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>