<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
//$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/comp_boleta.xlsx');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/boleta_final.xlsx');
$monedas = array(
	"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
	"D"=>array("nomb"=>"DOLARES","simb"=>"US$")
);
$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
if(!isset($data['moneda']))
	$data['moneda'] = 'S';

$objPHPExcel->getActiveSheet()->setCellValue('J6',date('d',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('K6',date('m',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('L6',date('Y',$data['fecreg']->sec));

$objPHPExcel->getActiveSheet()->setCellValue('E17',date('d',$data['fecreg']->sec));
$objPHPExcel->getActiveSheet()->setCellValue('F17',$meses[intval(date('m',$data['fecreg']->sec))-1]);
$anio = Date::format($data["fecreg"]->sec, 'Y');
$anio = substr($anio, 3);
$objPHPExcel->getActiveSheet()->setCellValue('H17',$anio);

$titular = $data['cliente']['nomb'];
if(isset($data['cliente']['appat']))
	$titular = $data['cliente']['appat'].' '.$data['cliente']['apmat'].' '.$data['cliente']['nomb'];
$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($titular) );

$domic = "";
foreach ($data['cliente']['domicilios'] as $domi) {
	if(isset($domi['tipo']))
		if($domi['tipo']=='PERSONAL')
			$domic = $domi['direccion'];
}
if($domic=='')
	$domic = $data["cliente"]["domicilios"][0]["direccion"];
/*if(isset($data['cliente']['domicilios'])){
	if(sizeof($data['cliente']['domicilios'])>0){
		$objPHPExcel->getActiveSheet()->setCellValue('C3',strtoupper($data['cliente']['domicilios'][0]['direccion']) );
	}
}*/
$objPHPExcel->getActiveSheet()->setCellValue('C3',strtoupper($domic) );





















$tmp_vou = false;
$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
//$y = 68;
if(isset($data['alquiler'])){
	$objPHPExcel->getActiveSheet()->setCellValue('E6',strtoupper($data['contrato']['inmueble']['direccion'].' ('.$data['contrato']['inmueble']['tipo']['nomb'].')') );
	$total_alq = 0;
	$total_igv = 0;
	$alquileres = array();
	foreach ($data['items'] as $key => $row) {
		$total_alq += floatval($row['conceptos'][0]['monto']);
		$total_igv += floatval($row['conceptos'][1]['monto']);
		if(isset($row['conceptos'][2]))
			$total_alq += floatval($row['conceptos'][2]['monto']);
		$alquileres[] = $row;
	}
	if(($total_alq+$total_igv)!=floatval($data['total'])){
		$total_igv += floatval($data['total']) - ($total_alq+$total_igv);
	}
	$texto_alq = "";
	switch($data['contrato']['motivo']['_id']->{'$id'}){
		case '55316577bc795ba80100003b':
			//SIN CONTRATO
			$texto_alq = 'POR OCUPACION';
			break;
		case '55316565bc795ba801000037':
			//RENOVACION SIN CONTRATO
			$texto_alq = 'POR OCUPACION';
			break;
		case '5531652fbc795ba80100002d':
			//ACTA DE CONCILIACION
			$texto_alq = 'POR OCUPACION';
			break;
		case '5531656fbc795ba801000039':
			//RENOVACION
			$texto_alq = 'ALQUILER';
			break;
		case '5531654cbc795ba801000033':
			//NUEVO
			$texto_alq = 'ALQUILER';
			break;
		case '5540f3c3bc795b7801000029':
			//convenios
			$texto_alq = 'ALQUILER';
			break;
		case '55316543bc795ba801000031':
			//convenios
			$texto_alq = 'POR AUTORIZACION';
			break;
		default: $texto_alq = 'asdasdasdasdsadasdas';
	}
	if(isset($data['compensacion'])){
		$texto_alq = 'POR COMPENSACION';
	}
	$dia_ini = intval(date('d', $data['contrato']['fecini']->sec));
	$size_alq = sizeof($alquileres);
	if($size_alq>4){
		if($dia_ini==1){
			$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].'-'.$alquileres[0]['pago']['ano'].' AL '.$meses[$alquileres[$size_alq-1]['pago']['mes']-1].'-'.$alquileres[$size_alq-1]['pago']['ano'];
		}else{
			$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
		}
		$texto_alq_ .= ' - cada mes '.$monedas[$data["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto'],2);
		$objPHPExcel->getActiveSheet()->setCellValue('E8',$texto_alq_);
		$total_alq = 0;
		foreach($alquileres as $k => $alq) {
			$total_alq += floatval($alq['conceptos'][0]['monto']);
			$total_alq += floatval($alq['conceptos'][1]['monto']);
			if(isset($alq['conceptos'][2]))
				$total_mor += floatval($alq['conceptos'][2]['monto']);
		}
		$objPHPExcel->getActiveSheet()->setCellValue('K8',$monedas[$data["moneda"]]["simb"].number_format($total_alq,2));
	}else{
		if(!isset($data['parcial'])){
			foreach($alquileres as $k => $alq) {
				if($dia_ini==1){
					$texto_alq_ = $texto_alq.' '.$meses[$alq['pago']['mes']-1].' DEL '.$alq['pago']['ano'];
				}else{
					$mes_ante = $alq['pago']['mes']-1;
					$ano_ante = $alq['pago']['ano'];
					if($mes_ante<1){
						$mes_ante = 12;
						$ano_ante--;
					}
					$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alq['pago']['mes'].'-'.$alq['pago']['ano'];
				}
				if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($data['contrato']['importe'])){
					//
				}else{
					$tmp_vou = true;
					$porc = (floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($data['contrato']['importe']);
					$texto_alq_ .= ' ('.$porc.'%)';
				}
				$total_alq = 0;
				$total_alq += floatval($alq['conceptos'][0]['monto']);
				$total_alq += floatval($alq['conceptos'][1]['monto']);
				if(isset($alq['conceptos'][2]))
					$total_mor += floatval($alq['conceptos'][2]['monto']);
				$objPHPExcel->getActiveSheet()->setCellValue('E8',$texto_alq_);
				$objPHPExcel->getActiveSheet()->setCellValue('K8',$monedas[$data["moneda"]]["simb"].number_format($total_alq,2));
			}
		}else{
			$texto_alq .= ' - PAGO PARCIAL';
			if($dia_ini==1){
				$texto_alq_ = $texto_alq.' DE '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
			}else{
				$mes_ante = $alquileres[0]['pago']['mes']-1;
				$ano_ante = $alquileres[0]['pago']['ano'];
				if($mes_ante<1){
					$mes_ante = 12;
					$ano_ante--;
				}
				$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[0]['pago']['mes'].'-'.$alquileres[0]['pago']['ano'];
			}
			if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($data['contrato']['importe'])){
				//
			}else{
				$tmp_vou = true;
				$porc = (floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($data['contrato']['importe']);
				$texto_alq_ .= ' ('.$porc.'%)';
			}
			$total_alq = 0;
			$total_alq += floatval($alq['conceptos'][0]['monto']);
			$total_alq += floatval($alq['conceptos'][1]['monto']);
			if(isset($alq['conceptos'][2]))
				$total_mor += floatval($alq['conceptos'][2]['monto']);
			$objPHPExcel->getActiveSheet()->setCellValue('E8',$texto_alq_);
			$objPHPExcel->getActiveSheet()->setCellValue('K8',$monedas[$data["moneda"]]["simb"].number_format($total_alq,2));
		}
	}
}elseif(isset($data['acta_conciliacion'])){
	$objPHPExcel->getActiveSheet()->setCellValue('E6',strtoupper($data['acta_conciliacion']['inmueble']['direccion'].' ('.$data['acta_conciliacion']['inmueble']['tipo']['nomb'].')') );
	$total_alq = 0;
	$total_mor = 0;
	$alquileres = array();
	foreach ($data['items'] as $key => $row) {
		foreach ($row['conceptos'] as $conc) {
			if($conc['cuenta']['cod']=='1202.0901.47'){
				$total_mor += $conc['monto'];
			}elseif($conc['cuenta']['cod']=='2101.010503.47'){
				$total_alq += $conc['monto'];
			}else{
				$total_alq += $conc['monto'];
				$alquileres[] = $conc;
			}
		}
	}
	$texto_alq = "";
	$size_alq = sizeof($alquileres);
	if($size_alq>4){
		//
	}else{
		foreach($alquileres as $k => $alq) {
			$texto_alq_ = 'ACTA DE CONCILIACION - '.$alq['concepto'];
			$objPHPExcel->getActiveSheet()->setCellValue('E6',$texto_alq_);
			$objPHPExcel->getActiveSheet()->setCellValue('E8',$monedas[$data["moneda"]]["simb"].number_format($alq['monto'],2));
		}
	}
}elseif(isset($data['combinar_alq'])){
	$total_mor = 0;
	foreach ($data['items'] as $comp) {
		if(isset($comp['alquiler'])){
			/***************************************************************************************************
			* COBRO DE ALQUILERES
			***************************************************************************************************/
			$alquileres = array();
			foreach ($comp['items'] as $row) {
				$total_alq += floatval($row['conceptos'][0]['monto']);
				$total_alq += floatval($row['conceptos'][1]['monto']);
				if(isset($row['conceptos'][2]))
					$total_mor += floatval($row['conceptos'][2]['monto']);
				$alquileres[] = $row;
			}
			$texto_alq = "";
			$comp['contrato'] = $f->model('in/cont')->params(array('_id'=>$comp['contrato']))->get('one')->items;
			switch($comp['contrato']['motivo']['_id']->{'$id'}){
				case '55316577bc795ba80100003b':
					//SIN CONTRATO
					$texto_alq = 'POR OCUPACION';
					break;
				case '55316565bc795ba801000037':
					//RENOVACION SIN CONTRATO
					$texto_alq = 'POR OCUPACION';
					break;
				case '5531652fbc795ba80100002d':
					//ACTA DE CONCILIACION
					$texto_alq = 'POR OCUPACION';
					break;
				case '5531656fbc795ba801000039':
					//RENOVACION
					$texto_alq = 'ALQUILER';
					break;
				case '5531654cbc795ba801000033':
					//NUEVO
					$texto_alq = 'ALQUILER';
					break;
				case '5540f3c3bc795b7801000029':
					//convenios
					$texto_alq = 'ALQUILER';
					break;
				case '55316543bc795ba801000031':
					//convenios
					$texto_alq = 'POR AUTORIZACION';
					break;
				default: $texto_alq = 'asdasdasdasdsadasdas';
			}
			if(isset($comp['compensacion'])){
				$texto_alq = 'POR COMPENSACION';
			}
			$dia_ini = intval(date('d', $comp['contrato']['fecini']->sec));
			$size_alq = sizeof($alquileres);
			if($size_alq>4){
				if($dia_ini==1){
					$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].'-'.$alquileres[0]['pago']['ano'].' AL '.$meses[$alquileres[$size_alq-1]['pago']['mes']-1].'-'.$alquileres[$size_alq-1]['pago']['ano'];
				}else{
					$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
				}
				$texto_alq_ .= ' - cada mes '.$monedas[$data["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto'],2);
				//$this->setXY(20,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
				$tot_alq = 0;
				foreach($alquileres as $k => $alq) {
					$tot_alq += floatval($alq['conceptos'][0]['monto']);
				}
				//$this->setXY(165,$y);$this->Cell(25,5,number_format($tot_alq,2),0,0,'R');
			}else{
				if(!isset($comp['parcial'])){
					foreach($alquileres as $k => $alq) {
						if($dia_ini==1){
							$texto_alq_ = $texto_alq.' '.$meses[$alq['pago']['mes']-1].' DEL '.$alq['pago']['ano'];
						}else{
							$mes_ante = $alq['pago']['mes']-1;
							$ano_ante = $alq['pago']['ano'];
							if($mes_ante==-1){
								$mes_ante = 12;
								$ano_ante--;
							}
							$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alq['pago']['mes'].'-'.$alq['pago']['ano'];
						}
						if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($comp['contrato']['importe'])){
							//
						}else{
							$tmp_vou = true;
							$porc = (floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($comp['contrato']['importe']);
							$texto_alq_ .= ' ('.$porc.'%)';
						}
						//$this->setXY(20,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
						//$this->setXY(165,$y);$this->Cell(25,5,number_format($alq['conceptos'][0]['monto'],2),0,0,'R');
					}
				}else{
					$texto_alq .= ' - PAGO PARCIAL';
					if($dia_ini==1){
						$texto_alq_ = $texto_alq.' DE '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
					}else{
						$mes_ante = $alquileres[0]['pago']['mes']-1;
						$ano_ante = $alquileres[0]['pago']['ano'];
						if($mes_ante==-1){
							$mes_ante = 12;
							$ano_ante--;
						}
						$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[0]['pago']['mes'].'-'.$alquileres[0]['pago']['ano'];
					}
					if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($comp['contrato']['importe'])){
						//
					}else{
						$tmp_vou = true;
						$porc = (floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($comp['contrato']['importe']);
						$texto_alq_ .= ' ('.$porc.'%)';
					}
					//$this->setXY(20,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
					//$this->setXY(165,$y);$this->Cell(25,5,number_format($alquileres[0]['conceptos'][0]['monto'],2),0,0,'R');
				}
			}
		}elseif(isset($comp['acta_conciliacion'])){
			/***************************************************************************************************
			* COBRO DE ACTAS
			***************************************************************************************************/
			$alquileres = array();
			foreach ($comp['items'] as $row) {
				foreach ($row['conceptos'] as $conc) {
					if($conc['cuenta']['cod']=='1202.0901.47'){
						$total_mor += $conc['monto'];
					}elseif($conc['cuenta']['cod']=='2101.010503.47'){
						$total_alq += $conc['monto'];
					}else{
						$total_alq += $conc['monto'];
						$alquileres[] = $conc;
					}
				}
			}
			$texto_alq = "";
			$size_alq = sizeof($alquileres);
			if($size_alq>4){
				/* EN CASO QUE SEAN MAS DE 4 */
			}else{
				foreach($alquileres as $k => $alq) {
					$texto_alq_ = 'ACTA DE CONCILIACION - '.$alq['concepto'];
					//$this->setXY(20,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
					//$this->setXY(165,$y);$this->Cell(25,5,number_format($alq['monto'],2),0,0,'R');
				}
			}
		}else{
			/***************************************************************************************************
			* COBRO DE SERVICIOS
			***************************************************************************************************/
			print_r($comp);die();
			$total_alq = 0;
			$total_mor = 0;
			$texto_alq_ = '';
			foreach ($comp['items'] as $row) {
				$texto_alq_ = $row['cuenta_cobrar']['servicio']['nomb'];
				foreach ($row['conceptos'] as $conc) {
					if($conc['cuenta']['cod']=='1202.0901.47'){
						$total_mor += $conc['monto'];
					}elseif($conc['cuenta']['cod']=='2101.010503.47'){
						$total_alq += $conc['monto'];
					}else{
						$total_alq += $conc['monto'];
					}
				}
			}
			if(isset($comp['observ'])){
				if($comp['observ']!='')
					$texto_alq_ = $comp['observ'];
			}
			$objPHPExcel->getActiveSheet()->setCellValue('E8',$texto_alq_);
			$objPHPExcel->getActiveSheet()->setCellValue('K8',number_format($total_alq,2));
		}
	}
}elseif(isset($data['conceptos'])){
	$total_alq = 0;
	$total_mor = 0;
	$texto_alq_ = '';
	foreach ($data['conceptos'] as $key => $row) {
		$texto_alq_ = $row['concepto']['nomb'];
		if($row['concepto']['cuenta']['cod']=='1202.0901.47'){
			$total_mor += $row['monto'];
		}elseif($conc['concepto']['cuenta']['cod']=='2101.010503.47'){
			$total_alq += $row['monto'];
		}else{
			$total_alq += $row['monto'];
		}
	}
	if(isset($data['observ'])){
		if($data['observ']!='')
			$texto_alq_ = $data['observ'];
	}
	$objPHPExcel->getActiveSheet()->setCellValue('D11',$texto_alq_);
	$objPHPExcel->getActiveSheet()->setCellValue('K11',number_format($total_alq,2));
}else{
	//$this->setXY(20,59);$this->Cell(130,5,$data['inmueble']['direccion'].' ('.$data['inmueble']['tipo']['nomb'].')',0,0,'C');
	$total_alq = 0;
	$total_mor = 0;
	$texto_alq_ = '';
	foreach ($data['items'] as $key => $row) {
		$texto_alq_ = $row['cuenta_cobrar']['servicio']['nomb'];
		foreach ($row['conceptos'] as $conc) {
			if($conc['cuenta']['cod']=='1202.0901.47'){
				$total_mor += $conc['monto'];
			}elseif($conc['cuenta']['cod']=='2101.010503.47'){
				$total_alq += $conc['monto'];
			}else{
				$total_alq += $conc['monto'];
			}
		}
	}
	if(isset($data['observ'])){
		if($data['observ']!='')
			$texto_alq_ = $data['observ'];
	}
	$objPHPExcel->getActiveSheet()->setCellValue('D11',$texto_alq_);
	if(isset($data['inmueble'])){
		$objPHPExcel->getActiveSheet()->setCellValue('D12',$data['inmueble']['direccion']);
	}
	$objPHPExcel->getActiveSheet()->setCellValue('K11',$monedas[$data["moneda"]]["simb"].number_format($total_alq,2));
	//$this->setXY(20,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
	//$this->setXY(165,$y);$this->Cell(25,5,$monedas[$data["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
}
if($total_mor!=0){
	$objPHPExcel->getActiveSheet()->setCellValue('F10','Moras');
	$objPHPExcel->getActiveSheet()->setCellValue('K10',number_format($total_mor,2));
}






























$objPHPExcel->getActiveSheet()->setCellValue('K14',$monedas[$data["moneda"]]["simb"].number_format($data["total"],2));

$decimal = round((($data["total"]-((int)$data["total"]))*100),0);
if($decimal==0) $decimal = '0'.$decimal;
$objPHPExcel->getActiveSheet()->setCellValue('B15',strtoupper(Number::lit($data["total"]).' Y '.$decimal.'/100 '.$monedas[$data["moneda"]]["nomb"]) );

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="boleta.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>