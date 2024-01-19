<?php
setlocale(LC_ALL, 'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/situ.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "SITUACION DE LOS INMUEBLES DE LA SBPA AL ".strftime("%d de %B del %Y"));
foreach($data as $r => $item) {
	$row = $baseRow + $r;
	$arren = $item["arrendatario"]["nomb"];
	if($item["arrendatario"]["tipo_enti"]=="P"){
		$arren = $item["arrendatario"]["appat"]." ".$item["arrendatario"]["apmat"].", ".$item["arrendatario"]["nomb"];
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, ($r+1))
								  ->setCellValue('B'.$row, $item["registro"]["ficha"])
								  ->setCellValue('C'.$row, $item["registro"]["partida"]["cod"])
	                              ->setCellValue('D'.$row, $item['area_construida'])
								  ->setCellValue('E'.$row, $item['area_terreno'])
								  ->setCellValue('F'.$row, ($item['conserv']=="B")?"X":"")
								  ->setCellValue('G'.$row, ($item['conserv']=="R")?"X":"")
								  ->setCellValue('H'.$row, ($item['conserv']=="M")?"X":"")
								  ->setCellValue('I'.$row, $item['data.ubic.local.direc'])
								  ->setCellValue('J'.$row, $item['tipo_local'])
								  ->setCellValue('k'.$row, ($item['ocupado']==true)?"X":"")
								  ->setCellValue('L'.$row, ($item['ocupado']!=true)?"X":"")
								  ->setCellValue('M'.$row, $arren)
								  ->setCellValue('N'.$row, "falta")
								  ->setCellValue('O'.$row, "falta")
								  ->setCellValue('P'.$row, $item["uso"])
								  ->setCellValue('Q'.$row, number_format($item["valor"]["renta"],2))
								  ->setCellValue('R'.$row, "falta")
								  ->setCellValue('S'.$row, "falta")
								  ->setCellValue('T'.$row, "falta")
								  ->setCellValue('U'.$row, "falta")
								  ->setCellValue('V'.$row, "falta")
								  ->setCellValue('W'.$row, "falta");
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="situacion-de-inmuebles.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>