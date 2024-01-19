<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/comp_factura.xlsx');

$monedas = array(
	"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
	"D"=>array("nomb"=>"DOLARES","simb"=>"US$")
);

$objPHPExcel->getActiveSheet()->setCellValue('J6',date('d',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('K6',date('m',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('L6',date('Y',$data['fecreg']->sec));

$titular = $data['cliente']['nomb'];
if(isset($data['cliente']['appat']))
	$titular = $data['cliente']['appat'].' '.$data['cliente']['apmat'].' '.$data['cliente']['nomb'];
$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($titular) );

if(isset($data['cliente']['docident'])){
	if(sizeof($data['cliente']['docident'])>0){
		foreach ($data['cliente']['docident'] as $doc) {
			if($doc['tipo']=='RUC'){
				$objPHPExcel->getActiveSheet()->setCellValue('C4',strtoupper($doc['num']) );
				break;
			}
		}
	}
}

if(isset($data['cliente']['domicilios'])){
	if(sizeof($data['cliente']['domicilios'])>0){
		$objPHPExcel->getActiveSheet()->setCellValue('B3',strtoupper($data['cliente']['domicilios'][0]['direccion']) );
	}
}



foreach ($data['items'] as $pago) {
	# code...
}



$objPHPExcel->getActiveSheet()->setCellValue('K14',$monedas[$data["moneda"]]["simb"].number_format($data["total"],2));

$decimal = round((($data["total"]-((int)$data["total"]))*100),0);
if($decimal==0) $decimal = '0'.$decimal;
$objPHPExcel->getActiveSheet()->setCellValue('B15',strtoupper(Number::lit($data["total"]).' Y '.$decimal.'/100 '.$monedas[$data["moneda"]]["nomb"]) );

$objPHPExcel->getActiveSheet()->setCellValue('E17',date('d',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('F17',date('m',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('G17',date('Y',$data['fecreg']->sec));

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="factura.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>