<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/nuev_contr.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "CONTRATOS CON FECHA DE INICIO MAYOR A ".Date::format(strtotime($f->request->data["feccon"]), 'd/m/Y'));
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$arren = $item["arrendatario"]["nomb"];
	if($item["arrendatario"]["tipo_enti"]=="P"){
		$arren = $item["arrendatario"]["appat"]." ".$item["arrendatario"]["apmat"].", ".$item["arrendatario"]["nomb"];
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $arren)
								  ->setCellValue('B'.$row, $item["espacio"]["ubic"]["local"]["direc"]." - ".$item["espacio"]["descr"])
	                              ->setCellValue('C'.$row, $item["espacio"]["valor"]["renta"])
								  ->setCellValue('D'.$row,Date::format($item["arrendamiento"]["feccon"]->sec, 'd/m/Y'))
								  ->setCellValue('E'.$row,Date::format($item["arrendamiento"]["fecven"]->sec, 'd/m/Y'));
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="nuevos-contratos.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>