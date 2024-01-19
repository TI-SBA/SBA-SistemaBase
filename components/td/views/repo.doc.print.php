<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/td/docs.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$data = $items;
$baseRow = 5;
$index=0;
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('B2', "DOCUMENTOS TRAMITADOS DEL MES ".strtoupper($meses[$params['mes']])." ".$params['ano']);
//$objPHPExcel->getActiveSheet()->setCellValue('I5',"MES DE LA MODIFICACIÓN: ".strtoupper($meses[$filtros["mes_modif"]])." - ".$filtros["ano"] );
$total_array = 0;
$total_expd = 0;
$total_ini = 0;
foreach($orga as $r => $org) {
	$row = $baseRow + $r;
	//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $org)
	                              ->setCellValue('C'.$row, $orga_array[$r])
								  ->setCellValue('D'.$row, $orga_expd[$r])
								  ->setCellValue('E'.$row, $orga_ini[$r])
								  ->setCellValue('F'.$row, $orga_descr[$r]);
	$total_array += $orga_array[$r];
	$total_expd += $orga_expd[$r];
	$total_ini += $orga_ini[$r];
	$r++;
}
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, 'TOTALES')
                              ->setCellValue('C'.$row, $total_array)
							  ->setCellValue('D'.$row, $total_expd)
							  ->setCellValue('E'.$row, $total_ini)
							  ->setCellValue('F'.$row, $total_all);
$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 12,
        'name'  => 'Verdana'
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArray);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$params['ano'].' - '.strtoupper($meses[$params['mes']]).' Reporte Documentos Adjuntados.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output'); 
?>