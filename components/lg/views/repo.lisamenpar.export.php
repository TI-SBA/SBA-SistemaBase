<?php
global $f;
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/lg/lisamenpar.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data=$items;
$baseRow = 8;
$index=0;
$r = 0;

/*$tipos = array(
	"0"=>"Fisico",
	"1"=>"Valorizado",
	"2"=>"Ambos"
	);
	*/
$objPHPExcel->getActiveSheet()->setCellValue('B3', $items[0][producto][cuenta]["cod"]);
$objPHPExcel->getActiveSheet()->setCellValue('B4', $items[0][producto][clasif]["cod"]);
$objPHPExcel->getActiveSheet()->setCellValue('B5', $items[0][producto][clasif]["nomb"]);
foreach($data as $k => $item) {
$tipos=$params["tipo"];
	$row = $baseRow + $r;
	if($tipos=="2"){
		$Fisico=$items[0][producto]["cant"];
		$Valorado=$items[0][producto]["valor_total"];
		
	}elseif($tipos=="1"){
			$Valorado=$items[0][producto]["cant"];
			
	}elseif($tipos=="0"){
			$Fisico=$items[0][producto]["valor_total"];
			
	}
	
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $items[0][producto]["cod"])
	                              ->setCellValue('B'.$row, $items[0][producto]["nomb"])
	                              ->setCellValue('C'.$row, $items[0][producto][unidad]["nomb"])
								  ->setCellValue('D'.$row, $items[0][producto][stock][0][almacen]["nomb"])
								  ->setCellValue('E'.$row, $Fisico)
	                 			  ->setCellValue('F'.$row, $Valorado);
								  
	$r++;
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lisamenpar.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>