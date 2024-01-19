<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/trabs.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$r = 0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "LISTA DE TRABAJADORES AL ".date("d/m/Y"));
foreach($data as $k => $item) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
								  ->setCellValue('B'.$row, $item["nomb"])
	                              ->setCellValue('C'.$row, $item["appat"])
	                              ->setCellValue('D'.$row, $item["apmat"])
								  ->setCellValue('E'.$row, $item["roles"]["trabajador"]["oficina"]["nomb"])
								  ->setCellValue('F'.$row, $item["roles"]["trabajador"]["contrato"]["nomb"])
								  ->setCellValue('G'.$row, $item["roles"]["trabajador"]["cod_tarjeta"])
								  ->setCellValue('H'.$row, $item["roles"]["trabajador"]["cargo"]["nomb"])
								  ->setCellValue('I'.$row, $item["roles"]["trabajador"]["nivel"]["nomb"])
								  ->setCellValue('J'.$row, $item["roles"]["trabajador"]["nivel"]["abrev"])
								  ->setCellValue('K'.$row, $item["roles"]["trabajador"]["nivel"]["basica"])
								  ->setCellValue('L'.$row, $item["roles"]["trabajador"]["nivel"]["reunificada"])
								  ->setCellValue('M'.$row, $item["roles"]["trabajador"]["turno"]["tipo"])
								  ->setCellValue('N'.$row, $item["roles"]["trabajador"]["cargo_clasif"]["nomb"])
								  ->setCellValue('O'.$row, $item["roles"]["trabajador"]["cargo_clasif"]["cod"])
								  ->setCellValue('P'.$row, $item["roles"]["trabajador"]["grupo_ocup"]["nomb"])
								  ->setCellValue('Q'.$row, $item["roles"]["trabajador"]["essalud"])
								  ->setCellValue('R'.$row, $item["roles"]["trabajador"]["pension"]["nomb"])
								  ->setCellValue('S'.$row, $item["roles"]["trabajador"]["pension"]["tipo"])
								  ->setCellValue('T'.$row, $item["roles"]["trabajador"]["cod_aportante"])
								  ->setCellValue('U'.$row, $item["roles"]["trabajador"]["bonos"][0]["cod"])
								  ->setCellValue('V'.$row, $item["roles"]["trabajador"]["bonos"][0]["formula"])
								  ->setCellValue('W'.$row, $item["roles"]["trabajador"]["eps"])
								  ->setCellValue('X'.$row, $item["roles"]["trabajador"]["salario"])
								  ->setCellValue('Y'.$row, $item["roles"]["trabajador"]["comision"]);
								  

	$r++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-lista-trabajadores-'.date("Y").'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>