<?php
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pr/presupuestos.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 9;
$index=0;
$cols = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W");
	for($i=0;$i<=count($p_fuentes);$i++){
		if($i!=count($p_fuentes)){
		$objPHPExcel->getActiveSheet()->setCellValue($cols[$i]."6", $p_fuentes[$i]["cod"]);	
		}else{
		$objPHPExcel->getActiveSheet()->setCellValue($cols[$i]."6", "TOTAL");	
		}
	}
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, " ".$dataRow['cod'])
	                              ->setCellValue('B'.$row, $dataRow['nomb']);
	$total=0;                              
	for($i=0;$i<=count($p_fuentes);$i++){
		if($i!=count($p_fuentes)){
		$objPHPExcel->getActiveSheet()->setCellValue($cols[$i].$row, array_sum($importes[$index]["importe"][$i]));	
		$total = array_sum($importes[$index]["importe"][$i]) + $total;
		}else{
		$objPHPExcel->getActiveSheet()->setCellValue($cols[$i].$row, $total);	
		}
		//$suma = array_sum($importes[$index]["importe"][$i]) + array_sum($importes[$index]["importe"][$i]);
		//$objPHPExcel->getActiveSheet()->setCellValue($cols[$i].$row, $suma);
	}
	//$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, array_sum($importes[$index]["importe"][0]));
	//$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, array_sum($importes[$index]["importe"][1]));
	//$suma = array_sum($importes[$index]["importe"][0]) + array_sum($importes[$index]["importe"][1]);
	//$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $suma);
$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reportePIM.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>