<?php
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pr/poi.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 12;
$row=11;
$objPHPExcel->getActiveSheet()->setCellValue('A2', "EVALUACIÓN ".$filtros["title"]." DE LAS ACTIVIDADES Y/O PROYECTOS DE LA SOCIEDAD DE BENEFICENCIA DE AREQUIPA");
$objPHPExcel->getActiveSheet()->setCellValue('A3', "PERIODO DE EJECUCIÓN: ".$filtros["filtro"]." DEL AÑO FISCAL ".$filtros["periodo"]);
$total_gen_p = 0;
$total_gen_e = 0;

foreach($data as $r => $act) {
	//$row = $baseRow + $index;
	//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$total_p = 0;
	$total_e = 0;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "")
	                              //->setCellValue('C'.$row, $act["actividad"]["nomb"])
								  ->setCellValue('C'.$row, $act["actividad"]["cod"])
								  ->setCellValue('I'.$row, '=A'.$row.'*H'.$row);
								 //->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
	$row_acti = $row;							  
	$row++;
	foreach($act["componentes"] as $comp){
		//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "")
	                              	  //->setCellValue('C'.$row, $comp["componente"]["nomb"]);
									  	->setCellValue('C'.$row, $comp["componente"]["cod"]);
		$row++;
		//foreach($comp["organizaciones"] as $orga){
		foreach($comp["programas"] as $prog){
			$prog_orga = 0;
			$ejec_orga = 0;
			//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
			$row_orga = $row;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "")
	                              	  	  //->setCellValue('C'.$row, $orga["organizacion"]["nomb"]);
										  ->setCellValue('C'.$row, $prog["programa"]["nomb"]);
			$row++;
			//foreach($orga["items"] as $item){
			foreach($prog["items"] as $item){
				//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "")
		                              	  	  ->setCellValue('C'.$row, $item["actividad"])
											  ->setCellValue('D'.$row, $item["unidad"]["nomb"])
											  ->setCellValue('E'.$row, $item["programacion"])
											  ->setCellValue('F'.$row, $item["ejecucion"])
											  ->setCellValue('H'.$row, '=F'.$row.'/E'.$row);
											  //->setCellValue('I'.$row, '=A'.$row.'*H'.$row)
											  //->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				//$objPHPExcel->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				//$objPHPExcel->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$row++;
				$total_p+=$item["programacion"];
				$total_e+=$item["ejecucion"];
				$prog_orga+=$item["programacion"]; 
				$ejec_orga+=$item["ejecucion"];
				$total_gen_p+=$item["programacion"];
				$total_gen_e+=$item["ejecucion"];
			}
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_orga, $prog_orga)
										  ->setCellValue('F'.$row_orga, $ejec_orga);
		}
	}
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_acti, $total_p)
	                              ->setCellValue('F'.$row_acti, $total_e)
								  ->setCellValue('H'.$row_acti, '=F'.$row_acti.'/E'.$row_acti)
								  ->setCellValue('I'.$row_acti, '=A'.$row_acti.'*H'.$row_acti);
								  //->getStyle('H'.$row_acti)->getNumberFormat()->setFormatCode('0.00');
	//$objPHPExcel->getActiveSheet()->getStyle('I'.$row_acti)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$row_acti.':A'.($row-1));
}
$r++;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "TOTAL")
							  ->setCellValue('E'.$row, $total_gen_p)
						      ->setCellValue('F'.$row, $total_gen_e);
//$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-pia-poe.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>