<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/lg/daot.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "DECLARACION ANUAL DE OPERACIONES CON TERCEROS - ".$filtros["ano"]);
foreach($data as $r => $item) {
	if(floatval($item["importe_cp"])<3800){
		$row = $baseRow + $r;
		$proveedor = $item["proveedor"]["nomb"];
		if($item["proveedor"]["tipo_enti"]=="P"){
			$tipo_per = "NATURAL";
			$proveedor .=" ".$item["proveedor"]["appat"]." ".$item["proveedor"]["apmat"];
		}else{
			$tipo_per = "JURIDICA";
		}
		$index++;
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $index)
									  ->setCellValue('B'.$row, $tipo_per)
		                              ->setCellValue('C'.$row, $item["tipo_doc"])
									  ->setCellValue('D'.$row, $item["num_doc"])
									  ->setCellValue('E'.$row, $proveedor)
									  ->setCellValue('F'.$row, "0.00")
									  ->setCellValue('G'.$row, "0.00")
									  ->setCellValue('H'.$row, $item["valor_no_grav"])
									  ->setCellValue('I'.$row, $item["otros_tributos"])
									  ->setCellValue('J'.$row, $item["importe_cp"]);
	}
}
$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="daot.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>