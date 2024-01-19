<?php
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pr/metas.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 12;
$row=6;
$etapa = "";
if($filtros["etapa"]=="P"){
	$etapa = "programado";
	$title = "PROGRAMACION";
	$anexo = "Anexo A";
}elseif($filtros["etapa"]=="E"){
	$etapa = "ejecutado";
	$title = "EJECUCION";
	$anexo = "Anexo B";
}
$objPHPExcel->getActiveSheet()->setCellValue('A1', $anexo);
$objPHPExcel->getActiveSheet()->setCellValue('A2', $title." DE METAS FISICAS DEL AÑO ".$filtros["periodo"]);
//$objPHPExcel->getActiveSheet()->setCellValue('A3', "PERIODO DE EJECUCIÓN: ".$filtros["filtro"]." DEL AÑO FISCAL ".$filtros["periodo"]);
foreach($data as $r => $act) {
	//$row = $baseRow + $index;
	//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $act["actividad"]["cod"]." ".$act["actividad"]["nomb"]);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
	$row++;
	foreach($act["componentes"] as $comp){
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "    ".$comp["componente"]["cod"]." ".$comp["componente"]["nomb"]);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
		$row++;
		foreach($comp["metas"] as $meta){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "        ".$meta["cod"]." ".$meta["nomb"])
	                              		  ->setCellValue('B'.$row, $meta["unidad"]["nomb"])
	                              		  ->setCellValue('C'.$row, $meta[$etapa][0])
										  ->setCellValue('D'.$row, $meta[$etapa][1])
										  ->setCellValue('E'.$row, $meta[$etapa][2])
										  ->setCellValue('F'.$row, $meta[$etapa][3])
										  ->setCellValue('G'.$row, $meta[$etapa][4])
										  ->setCellValue('H'.$row, $meta[$etapa][5])
										  ->setCellValue('I'.$row, $meta[$etapa][6])
										  ->setCellValue('J'.$row, $meta[$etapa][7])
										  ->setCellValue('K'.$row, $meta[$etapa][8])
										  ->setCellValue('L'.$row, $meta[$etapa][9])
										  ->setCellValue('M'.$row, $meta[$etapa][10])
										  ->setCellValue('N'.$row, $meta[$etapa][11]);		
			$row++;
		}
	}
}
//$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-metas-fisicas.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>