<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/planilla.xlsx');
$data = $items;
$baseRow = 6;
$index=0;

//$meses = array("1","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
$meses = array(
	"1"=>"ENERO",
	"2"=>"FEBRERO",
	"3"=>"MARZO",
	"4"=>"ABRIL",
	"5"=>"MAYO",
	"6"=>"JUNIO",
	"7"=>"JULIO",
	"8"=>"AGOSTO",
	"9"=>"SEPTIEMBRE",
	"10"=>"OCTUBRE",
	"11"=>"NOVIEMBRE",
	"12"=>"DICIEMBRE",
	
);
//$objPHPExcel->getActiveSheet()->setCellValue('B2', $modulos[$filtros["modulo"]]);
$cols = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
	for($x=0;$x<count($items[0]["items"]);$x++){
			for($i=0;$i<count($items[0]["items"][$x]["conceptos"]);$i++){
			$objPHPExcel->getActiveSheet()->setCellValue($cols[$i]."4", $items[0]["items"][$x]["conceptos"][$i][concepto]["nomb"]);				
			}
			
	}	
$objPHPExcel->getActiveSheet()->setCellValue("E1","PLANILLA C.A.S."."  ".$meses[$items[0]["items"][0]["periodo"]["mes"]]."  ".$items[0]["items"][0]["periodo"]["ano"]);


foreach($data as $r => $dataRow) {
	if($r=0){
		$base=$baseRow;
	}else{
		$base=count($items[$r-1]["items"])+$baseRow;		
	}
	$row = $base + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $dataRow["orga"][0]["nomb"]);   
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
	$nro=1;	
	for($i=0;$i<count($dataRow["items"]);$i++){
		$row2 = $row + ($i+1);
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2,1);
		$objPHPExcel->getActiveSheet()->setCellValue("A".$row2, $nro);	
		$objPHPExcel->getActiveSheet()->getStyle("B".$row2)->getFont()->setBold(false);
		$objPHPExcel->getActiveSheet()->setCellValue("B".$row2, $dataRow["items"][$i]["trabajador"]["appat"]." ".$dataRow["items"][$i]["trabajador"]["apmat"].", ".$dataRow["items"][$i]["trabajador"]["nomb"]);	
		for($j=0;$j<count($dataRow["items"][$i]["conceptos"]);$j++){
			$objPHPExcel->getActiveSheet()->setCellValue($cols[$j].$row2, $dataRow["items"][$i]["conceptos"][$j]["subtotal"]);	
			$objPHPExcel->getActiveSheet()->getStyle($cols[$j].$row2)->getNumberFormat()->setFormatCode('0.00');
		}
		$nro++;
	}
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2+1,1);
	$objPHPExcel->getActiveSheet()->getStyle("B".($row2+1))->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($row2+1),"SUB TOTAL POR PROGRAMA");
	for($k=0;$k<count($dataRow["items"][0]["conceptos"]);$k++){
		$objPHPExcel->getActiveSheet()->setCellValue($cols[$k].($row2+1),"=SUM(".$cols[$k].($base+1).":".$cols[$k].$row2.")");
	}
$index++;
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reportePIA.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
$obwriter->setPreCalculateFormulas(false);
?>