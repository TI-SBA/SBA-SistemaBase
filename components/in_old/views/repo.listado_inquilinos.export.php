<?php
setlocale(LC_ALL,'es-ES');
global $f;
$f->library('excel');
//echo date('H:i:s') , " Load from Excel5 template" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/listado_inquilinos.xlsx');
$estado = array(
	"P"=>"Pendiente",
	"C"=>"Cancelado",
	"X"=>"Anulado"
);
$data = $items;
$baseRow = 6;
$index=0;
$condic = "";
switch($f->request->data['condic']){
	case "CT": $condic = "Nuevo"; break;
	case "RE": $condic = "Renovaci&oacute;n"; break;
	case "CV": $condic = "Convenio"; break;
	case "CU": $condic = "Cesi&oacute;n en Uso"; break;
	case "CM": $condic = "Comodato"; break;
	case "AC": $condic = "Acta de Conciliaci&oacute;n"; break;
	case "RS": $condic = "Por Ocupaci&oacute;n"; break;
	case "AU": $condic = "Autorizaci&oacute;n"; break;
	case "PE": $condic = "Penalidades"; break;
	case "TR": $condic = "Traspaso"; break;
	case "AD": $condic = "Audiencias"; break;
}
$titulo = "LISTADO DE INQUILINOS";
if($f->request->data['condic']!='')
	$titulo .= ' POR '.$condic;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$titulo);
$objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
if($data!=null){
	foreach($data as $r => $itemRow) {
		switch($itemRow['arrendamiento']['condicion']){
			case "CT": $condic = "Nuevo"; break;
			case "RE": $condic = "Renovaci&oacute;n"; break;
			case "CV": $condic = "Convenio"; break;
			case "CU": $condic = "Cesi&oacute;n en Uso"; break;
			case "CM": $condic = "Comodato"; break;
			case "AC": $condic = "Acta de Conciliaci&oacute;n"; break;
			case "RS": $condic = "Por Ocupaci&oacute;n"; break;
			case "AU": $condic = "Autorizaci&oacute;n"; break;
			case "PE": $condic = "Penalidades"; break;
			case "TR": $condic = "Traspaso"; break;
			case "AD": $condic = "Audiencias"; break;
		}
		$cliente = $itemRow["arrendatario"]["nomb"];
		if($itemRow["arrendatario"]["tipo_enti"]=="P"){
			$cliente .= $itemRow["arrendatario"]["appat"]." ".$itemRow["arrendatario"]["apmat"];
		}
		$direc = $itemRow["espacio"]['descr'].' - '.$itemRow["espacio"]['ubic']['local']['direc'];
		$row = $baseRow + $r;
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $cliente)
									  ->setCellValue('B'.$row, Date::format($itemRow['arrendamiento']["fecven"]->sec,"d/m/Y"))
									  ->setCellValue('C'.$row, number_format($itemRow['espacio']['valor']["renta"]))
									  ->setCellValue('D'.$row, $direc)
									  ->setCellValue('E'.$row, $condic);
	}
	$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado-de-inquilinos.xlsx"');
header('Cache-Control: max-age=0');
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>