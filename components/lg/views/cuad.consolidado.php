<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/lg/cuadro_consolidado.xlsx');
$objPHPExcel->getActiveSheet()->setCellValue('C2','CONSOLIDADO DE CUADRO DE NECESIDADES DEL PERIODO '.$f->request->data['periodo']);
$row = 6;
foreach ($clasifs as $clasif) {
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$clasif['cod'])
								->setCellValue('C'.$row,$clasif['nomb']);
	$row++;
}
$col = 'D';
if($data!=null){
	foreach ($data as $key=>$orga){
		$row_t = 6;
		foreach ($clasifs as $clasif) {
			$objPHPExcel->getActiveSheet()->setCellValue($col.$row_t,0);
			$row_t++;
		}
		$col++;
	}
	$col = 'D';
	$prev_col = 0;
	foreach ($data as $key=>$orga){
		$prev_col = $col;
		$objPHPExcel->getActiveSheet()->setCellValue($col.'5',$orga['organizacion']['nomb']);
		$objPHPExcel->getActiveSheet()->getStyle($col.'5')->getAlignment()->setTextRotation(90);
		foreach ($orga['totales_clasif'] as $gasto) {
			$i = array_search($gasto['clasif']['_id']->{'$id'}, $clasifs_i);
			$objPHPExcel->getActiveSheet()->setCellValue($col.(6+$i),$gasto['precio_total']);
		}
		$objPHPExcel->getActiveSheet()->setCellValue($col.$row, '=SUM('.$col.'6:'.$col.($row-1).')');
		$col++;
	}
	foreach ($clasifs as $i=>$clasif) {
		if(sizeof($data)==1)
			$objPHPExcel->getActiveSheet()->setCellValue($col.(6+$i), '=SUM(D'.(6+$i).')');
		else
			$objPHPExcel->getActiveSheet()->setCellValue($col.(6+$i), '=SUM(D'.(6+$i).':'.$prev_col.(6+$i).')');
	}
	if(sizeof($data)!=1)
		$objPHPExcel->getActiveSheet()->mergeCells('D4:'.$prev_col.'4');
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, 'TOTALES');
	$objPHPExcel->getActiveSheet()->mergeCells($col.'4:'.$col.'5');
	$objPHPExcel->getActiveSheet()->setCellValue($col.'4', 'TOTALES');
	if(sizeof($data)==1)
		$objPHPExcel->getActiveSheet()->setCellValue($col.$row, '=SUM(D'.$row.')');
	else
	$objPHPExcel->getActiveSheet()->setCellValue($col.$row, '=SUM(D'.$row.':'.$prev_col.$row.')');
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Consolidado '.$f->request->data['periodo'].' - Cuadro de Necesidades.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>