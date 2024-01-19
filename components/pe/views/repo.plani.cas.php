<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/planilla.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$baseRow = 6;
$index=0;
$items=$boletas;
$cols = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
	for($i=0;$i<count($boletas[0]["conceptos"]);$i++){
			$objPHPExcel->getActiveSheet()->setCellValue($cols[$i]."4", $boletas[0]["conceptos"][$i][concepto]["nomb"]);	
	}	
$meses = array("1","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
$objPHPExcel->getActiveSheet()->setCellValue("E1","PLANILLA "); 


foreach($items as $r => $dataRow) {
	if($r=0){
		$base=$baseRow;
	}else{
		$base=count($boletas[$r-1]["trabajador"])+$baseRow;		
	}
	$row = $base + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue("B5", $datarow["trabajador"]["cargo"]["organizacion"]["nomb"]); 
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
	$nro=1;	
	for($i=0;$i<count($dataRow["boletas"]);$i++){
		$row2 = $row + ($i+1);
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2,1);
		$objPHPExcel->getActiveSheet()->setCellValue("A".$row2, $nro);	
		$objPHPExcel->getActiveSheet()->getStyle("B".$row2)->getFont()->setBold(false);
		$objPHPExcel->getActiveSheet()->setCellValue("B".$row2, $dataRow["trabajador"]["appat"]." ".$dataRow["trabajador"]["apmat"].", ".$dataRow["trabajador"]["nomb"]);	
		for($j=0;$j<count($dataRow["conceptos"]);$j++){
			$objPHPExcel->getActiveSheet()->setCellValue($cols[$j].$row2, $dataRow["conceptos"][$j]["subtotal"]);	
			$objPHPExcel->getActiveSheet()->getStyle($cols[$j].$row2)->getNumberFormat()->setFormatCode('0.00');
		}
		$nro++;
	}
	
	$index++;
}

$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2+1,1);
$objPHPExcel->getActiveSheet()->getStyle("B".($row2+1))->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('B'.($row2+1),"SUB TOTAL POR PROGRAMA");
for($k=0;$k<count($dataRow["conceptos"]);$k++){
	$objPHPExcel->getActiveSheet()->setCellValue($cols[$k].($row2+1),"=SUM(".$cols[$k].($base+1).":".$cols[$k].$row2.")");
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

$objPHPExcel->getActiveSheet()->setCellValue("B5", $boletas[0]["trabajador"]["cargo"]["organizacion"]["nomb"]);  

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporteCAS.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>