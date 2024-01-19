<?php
global $f;
$f->library('excel');
$estilo = array(
	'borders' => array(
	    'allborders' => array(
	        'style' => PHPExcel_Style_Border::BORDER_THIN,
	        'color' => array('rgb' => '000000')
	    )
	)
);
$estilo2 = array(
	'font'  => array(
	        'bold'  => true
	),
	'borders' => array(
	    'allborders' => array(
	        'style' => PHPExcel_Style_Border::BORDER_THIN,
	        'color' => array('rgb' => '000000')
	    )
	)
);
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/planilla.xlsx');
$data = $items;
$row = 5;
//calculando el numero de dias en un periodo
$cols = array("F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ","CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ","DA","DB","DC","DD","DE","DF","DG","DH","DI","DJ","DK","DL","DM","DN","DO","DP","DQ","DR","DS","DT","DU","DV","DW","DX","DY","DZ","EA","EB","EC","ED","EE","EF","EG","EH","EI","EJ","EK","EL","EM","EN","EO","EP","EQ","ER","ES","ET","EU","EV","EW","EX","EY","EZ");
if($items!=null){
	$meses = array(
		"--",
		"ENERO",
		"FEBRERO",
		"MARZO",
		"ABRIL",
		"MAYO",
		"JUNIO",
		"JULIO",
		"AGOSTO",
		"SETIEMBRE",
		"OCTUBRE",
		"NOVEMBRE",
		"DICIEMBRE"
	);
	foreach($items as $prog){
		$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":".$cols[count($header)-1].$row);
		$objPHPExcel->getActiveSheet()->setCellValue("A".$row, $planilla["nomb"]." / ".$prog['programa']['nomb']);
		$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("A1"), "A".$row.":A".$row);
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(48);
		$row++;
		$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":".$cols[count($header)-1].$row);
		$objPHPExcel->getActiveSheet()->setCellValue("A".$row, "CORRESPONDIENTE AL MES DE ".$meses[floatval($planilla["periodo"]["mes"])]." ".$planilla["periodo"]["ano"]);
		$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("A3"), "A".$row.":A".$row);
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(24);
		$row++;
		$count = 1;
		$aux = 0;
		$objPHPExcel->getActiveSheet()->setCellValue("A".$row, "Nro.");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$row, "APELLIDOS Y NOMBRES");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$row, "DNI");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$row, "CARGO");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$row, "AFP");
		foreach($header as $head){
			$objPHPExcel->getActiveSheet()->setCellValue($cols[$aux].$row, $head['concepto']['nomb'])->getStyle($cols[$aux].$row.":".$cols[$aux].$row)->applyFromArray($estilo);
			$aux++;
		}
		$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("B3"), "A".$row.":".$cols[($aux-1)].$row);
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(45.75);
		$row++;
		$row_prog = $row;
		foreach($prog['trabajadores'] as $item){
			$cargo = "--";
			if(isset($item['trabajador']['cargo']['funcion'])){
				$cargo = $item['trabajador']['cargo']['funcion'];
			}elseif(isset($item['trabajador']['cargo']['nomb'])){
				$cargo = $item['trabajador']['cargo']['nomb'];
			}
			$objPHPExcel->getActiveSheet()->setCellValue("A".$row, $count)->getStyle("A".$row.":A".$row)->applyFromArray($estilo);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$row, $item['trabajador']['appat'].' '.$item['trabajador']['apmat'].' '.$item['trabajador']['nomb'])->getStyle("B".$row.":B".$row)->applyFromArray($estilo);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$row, $item['trabajador']['docident'][0]['num'])->getStyle("C".$row.":C".$row)->applyFromArray($estilo);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$row, $cargo)->getStyle("D".$row.":D".$row)->applyFromArray($estilo);		
			$objPHPExcel->getActiveSheet()->setCellValue("E".$row, $item['pension']['nomb'])->getStyle("E".$row.":E".$row)->applyFromArray($estilo);
			$aux = 0;
			foreach($item['totales'] as $tota){
				$objPHPExcel->getActiveSheet()->setCellValue($cols[$aux].$row, $tota['importe'])->getStyle($cols[$aux].$row.":".$cols[$aux].$row)->applyFromArray($estilo);
				$aux++;
			}
			$row++;
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, 'TOTALES')->getStyle('E'.$row.":E".$row)->applyFromArray($estilo2);
			$aux = 0;
			foreach($item['totales'] as $tota){
				$objPHPExcel->getActiveSheet()->setCellValue($cols[$aux].$row, '=SUM('.$cols[$aux].$row_prog.':'.$cols[$aux].($row-1).')')->getStyle($cols[$aux].$row.":".$cols[$aux].$row)->applyFromArray($estilo2);
				$aux++;
			}
			$count++;
		}
		
		$row+=2;
	}
	$objPHPExcel->getActiveSheet()->removeRow(1,4);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="planilla_electronica-'.date('YmdHi').'.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?> 
