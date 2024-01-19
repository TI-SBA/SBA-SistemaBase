<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/al/expd.xlsx');
$data = $items;
$baseRow = 8;
$index=0;
$tipos = array(
	"0"=>"Todos",
	"C"=>"Civil",
	"P"=>"Penal",
	"A"=>"Administrativo",
	"L"=>"Laboral",
	"T"=>"Contensioso Administrativo",
	"S"=>"Sucesion Intestada"
);
$enc = array(
	"B"=>"Sociedad de Beneficencia Publica de Arequipa",
	"C"=>"Contraloria",
	"M"=>"Mimdes"
);
$objPHPExcel->getActiveSheet()->setCellValue('B3', $tipos[$filtros["tipo"]]);
$objPHPExcel->getActiveSheet()->setCellValue('B4', $enc[$filtros["encargado"]]);
$objPHPExcel->getActiveSheet()->setCellValue('B5', $filtros["materia"]);
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	$demandante = $dataRow['demandante'];
	if(is_array($demandante)){
		$demandante = $dataRow['demandante']["nomb"];
	}
	$demandado = $dataRow['demandado'];
	if(is_array($demandado)){
		$demandado = $dataRow['demandado']["nomb"];
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['numero'])
	                              ->setCellValue('B'.$row, $dataRow['ubicacion'])
	                              ->setCellValue('C'.$row, $demandante)
	                              ->setCellValue('D'.$row, $demandado)
	                              ->setCellValue('E'.$row, $dataRow['juzgado'])
	                              ->setCellValue('F'.$row, $dataRow['materia'])
	                              ->setCellValue('G'.$row, $dataRow['estado'])
								  ->setCellValue('H'.$row, $tipos[$dataRow['tipo']])
								  ->setCellValue('I'.$row, $dataRow['trabajador_autor']["nomb"]." ".$dataRow['trabajador_autor']["appat"]." ".$dataRow['trabajador_autor']["apmat"]);                            
	$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-expedientes.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>

