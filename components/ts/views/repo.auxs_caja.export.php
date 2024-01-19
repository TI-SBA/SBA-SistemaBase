<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/ts/cajachica.xlsx');
$sheet = $objPHPExcel->getActiveSheet();

    //Start adding next sheets
    
    //while ($i < 10) {

    // Add new sheet
    $objWorkSheet = $objPHPExcel->setActiveSheetIndex(0); //Setting index when creating

    //Write cells
    $data = $items;
	$baseRow = 10;
	$index=0;
	//$objPHPExcel->getActiveSheet()->setCellValue('A1', "DECLARACION ANUAL DE OPERACIONES CON TERCEROS - ".$filtros["ano"]);
	//$objPHPExcel->getActiveSheet()->setCellValue('E8', "SALDO ANTERIOR");
	//$objPHPExcel->getActiveSheet()->setCellValue('H8', $saldo_old['saldo']);
	//$objPHPExcel->getActiveSheet()->setCellValue('E9', "REEMBOLSO DE CAJA");
	//$objPHPExcel->getActiveSheet()->setCellValue('H9', $saldo_old['saldo']);
	$total = 0;
	if($movs!=null){
		foreach($movs as $r => $item) {
			$row = $baseRow + $r;
			$index++;
			$proveedor = $item["beneficiario"]["nomb"];
			if($item["beneficiario"]["tipo_enti"]=="P"){
				$proveedor .=" ".$item["beneficiario"]["appat"]." ".$item["beneficiario"]["apmat"];
			}
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $index)
										  ->setCellValue('B'.$row, date('y.m.d',$item['fecreg']->sec))
										  ->setCellValue('C'.$row, $item["documento"])
										  ->setCellValue('D'.$row, $item["num_doc"])
										  ->setCellValue('E'.$row, $proveedor)
										  ->setCellValue('F'.$row, $item["concepto"])
										  ->setCellValue('G'.$row, $item["organizacion"]["nomb"])
										  ->setCellValue('H'.$row, $item["monto"])
										  ->setCellValue('I'.$row, $item["clasificador"]["cod"]);
			$total += floatval($item['monto']);
		}
	}
	$objPHPExcel->getActiveSheet()->setCellValue('G'.($row+1), '********');
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($row+1), $total);

    // Rename sheet
    $objWorkSheet->setTitle("CajaChica");
    $objWorkSheet = $objPHPExcel->setActiveSheetIndex(2);
    $data = $items;
	$baseRow = 10;
	$index=0;
	//$objPHPExcel->getActiveSheet()->setCellValue('A1', "DECLARACION ANUAL DE OPERACIONES CON TERCEROS - ".$filtros["ano"]);
	$objPHPExcel->getActiveSheet()->setCellValue('E8', "SALDO ANTERIOR");
	$objPHPExcel->getActiveSheet()->setCellValue('H8', $saldo_old['saldo']);
	$objPHPExcel->getActiveSheet()->setCellValue('E9', "REEMBOLSO DE CAJA");
	$objPHPExcel->getActiveSheet()->setCellValue('H9', $saldo_old['saldo']);
	$total = 0;
	if($movs!=null){
		foreach($movs as $r => $item) {
			$row = $baseRow + $r;
			$index++;
			$proveedor = $item["beneficiario"]["nomb"];
			if($item["beneficiario"]["tipo_enti"]=="P"){
				$proveedor .=" ".$item["beneficiario"]["appat"]." ".$item["beneficiario"]["apmat"];
			}
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $index)
										  ->setCellValue('B'.$row, date('y.m.d',$item['fecreg']->sec))
			                              ->setCellValue('C'.$row, $item["documento"])
										  ->setCellValue('D'.$row, $item["num_doc"])
										  ->setCellValue('E'.$row, $proveedor)
										  ->setCellValue('F'.$row, $item["concepto"])
										  ->setCellValue('H'.$row, $item["monto"]);
			$total += floatval($item['monto']);
		}
	}
$objPHPExcel->getActiveSheet()->setCellValue('G'.($row+1), '********');
$objPHPExcel->getActiveSheet()->setCellValue('H'.($row+1), $total);
$objWorkSheet->setTitle("AuxCajaChica");
    //$i++;
    //}
//echo date('H:i:s') , " Add new data to the template" , EOL;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="cajachica.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>