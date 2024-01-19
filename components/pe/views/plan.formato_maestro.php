<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/formato_maestro.xlsx');
$data = $items;
$row = 5;
//calculando el numero de dias en un periodo
$cols = array("E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");

if($items!=null){
	foreach($items as $item){
		$objPHPExcel->getActiveSheet()->setCellValue("A".$row, $item['_id']->{'$id'});
		$dni = '';
		if(isset($item['trabajador']['docident'])){
			foreach($item['trabajador']['docident'] as $doc){
				if($doc['tipo']=='DNI'){
					$dni = $doc['num'];
				}
			}
		}
		//if(isset($item['programa']['nomb'])){
		$objPHPExcel->getActiveSheet()->setCellValue("B".$row, $dni);
		//}
		$objPHPExcel->getActiveSheet()->setCellValue("C".$row, $item['trabajador']['appat'].' '.$item['trabajador']['apmat'].' '.$item['trabajador']['nomb']);
		if(isset($item['programa']['nomb'])){
			$objPHPExcel->getActiveSheet()->setCellValue("D".$row, $item['programa']['nomb']);
		}
		$out = 0;
		$col = 0;
		while ($out==0) {
			//echo $cols[$col]."<br />";
			$col_name = $objPHPExcel->getActiveSheet()->getCell($cols[$col]."3")->getValue();
			//echo $col_name."<br />";
			if(trim($col_name)!=""){
				//$col_value = $objPHPExcel->getActiveSheet()->getCell($cols[$col].$row)->getValue();
				//$maestros[$col_name]=$col_value;
				$objPHPExcel->getActiveSheet()->setCellValue($cols[$col].$row, $item['maestro'][$col_name]);
			}else{
				$out = 1;
			}
			$col++;
		}
		/*$objPHPExcel->getActiveSheet()->setCellValue("E".$row, $item['maestro']['TOTAL_DIAS_MES']);
		$objPHPExcel->getActiveSheet()->setCellValue("F".$row, $item['maestro']['MIN_PERMISO']);
		$objPHPExcel->getActiveSheet()->setCellValue("G".$row, $item['maestro']['MIN_EXT']);
		$objPHPExcel->getActiveSheet()->setCellValue("H".$row, $item['maestro']['MIN_TAR']);
		$objPHPExcel->getActiveSheet()->setCellValue("I".$row, $item['maestro']['DIAS_TRAB']);
		$objPHPExcel->getActiveSheet()->setCellValue("J".$row, $item['maestro']['DIAS_CESE']);
		$objPHPExcel->getActiveSheet()->setCellValue("K".$row, $item['maestro']['DIAS_INA']);
		$objPHPExcel->getActiveSheet()->setCellValue("L".$row, $item['maestro']['DIAS_LIC']);
		$objPHPExcel->getActiveSheet()->setCellValue("M".$row, $item['maestro']['DIAS_SUS']);
		$objPHPExcel->getActiveSheet()->setCellValue("N".$row, $item['maestro']['DIAS_PERMISO']);*/
		$row++;
	}
}
//die();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="download_formato_marcacion.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?> 