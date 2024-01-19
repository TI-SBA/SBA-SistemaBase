<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
$f->library('helpers');
$helper=new helper();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
if($recibo['tipo_inm']=='P'){
	//$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/planilla.xlsx');
	$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/planilla_inm.xlsx');
}else{
	$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/planilla_inm.xlsx');
}
$baseRow = 6;
$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");

switch($recibo['tipo_inm']){
	case 'P':
		$tipo_rein = 'Playas';
		break;
	case 'A':
		$tipo_rein = 'Alquileres';
		break;
}
$tot_saciones = '';
$objPHPExcel->getActiveSheet()->setCellValue('M2', $recibo['planilla'])
							->setCellValue('N2', date('d',$recibo['fec']->sec))
							->setCellValue('O2', date('m',$recibo['fec']->sec))
							->setCellValue('P2', date('Y',$recibo['fec']->sec));
$row = 0;
foreach($comp as $r => $item) {
	$tot_saciones = '';
	if(isset($item['items'])){
		foreach($item['items'] as $sb => $sub){
			if(isset($sub['subitems'])){
				foreach($sub['subitems'] as $san => $sanciones){
					if(isset($sanciones['precio_total'])){
						$tot_saciones += floatval($sanciones['precio_total']);
					}else{
						$tot_saciones = '';
					}
					
				}
			}
		}
	}
	//print_r($tot_saciones."-");
	$row = $baseRow + $r;
	if($recibo['tipo_inm']=='P'){
		
		# RECIBO DE PLAYAS
		# PARCHE DE FACTURACION ELECTRONICA
		if(isset($item['cliente_nomb'])) {
			$item['doc_cliente']=$item['cliente_doc'];
			$item['cliente']=$item['cliente_nomb'];
		}
		if(isset($item['cliente_doc'])) {
			$item['doc_cliente']=$item['cliente_doc'];
		}
		if(isset($item['fecemi'])) {
			$item['fecreg']=$item['fecemi'];
			//$item['fecreg']=$item['fecemi'];
		}
		if (isset($item['total_ope_gravadas'])) {
			$item['subtotal']=$item['total_ope_gravadas'];
		}
		if (isset($item['total_igv'])) {
			$item['igv']=$item['total_igv'];
		}
		if (isset($item['numero'])) {
			$item['num']=$item['numero'];
		}
		if (!isset($item['playa']['nomb'])) {
			$item['playa']['nomb']="DESCONOCIDO";
		}

		#CONTINUAR CON EL LEGADO
		$cliente = $item['cliente'];
		$serie = $item['serie'];
		$subtotal = $item['subtotal'];
		$igv = $item['igv'];

		if($item['tipo']=='F'){
			$tipo = '01';
		}elseif($item['tipo']=='B') {
			$tipo = '03';
		}elseif($item['tipo']=='R'){
			$tipo = '00';
			$serie = '0000';
			$subtotal = '';
			$igv = 0;
			$mora = $item['total'];
			
		}elseif($item['tipo']=='NC'){
			$tipo = '07';
		}
		if(''.intval($item['serie'])=='22'&&$item['tipo']=='F'){
			$item['serie'] = '0022';
		}else{
			$item['serie'] = '0'.intval($item['serie']);
		}


		# FACTURAS ANULADAS
		if($item['estado']=="X"){	
				$item['subtotal']=0;
				$item['igv']=0;
				$item['total']=0;
				$subtotal = $item['subtotal'];
				$igv = $item['igv'];
				$item['playa']['nomb']="ANULADO";
				$item['cliente']="ANULADO";
				$item['doc_cliente']='0';
				$tipo_doc = '0';
		}
		if(isset($item['doc_cliente'])){
			if(strlen($item['doc_cliente']) == 8 && $item['estado']!="X"){
				$tipo_doc = '1';
			}else if(strlen($item['doc_cliente']) > 8 && $item['estado']!="X"){
				$tipo_doc = '6';
			}
		}else{
			foreach($item['cliente']['docident'] as $doc => $docu){
				if(strlen($docu['num']) == 8 && $item['estado']!="X"){
					$tipo_doc = '1';
				}else if(strlen($docu['num']) > 8 && $item['estado']!="X"){
					$tipo_doc = '6';
				}
			}
		}
		
		if($item['tipo']=='R'){
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $item['num'])
									  ->setCellValue('E'.$row, $tipo_doc)							  
									  ->setCellValue('F'.$row, $item['doc_cliente'])
									  ->setCellValue('G'.$row, $item['cliente'])
									  ->setCellValue('H'.$row, $helper->format_word($item['playa']['nomb']))
		                              ->setCellValue('P'.$row, number_format($subtotal, 2, '.', ''))
		                              ->setCellValue('N'.$row, number_format($item['total'], 2, '.', ''))
									  ->setCellValue('L'.$row, number_format($igv, 2, '.', ''))
									  ->setCellValue('Q'.$row, number_format($item['total'], 2, '.', ''))
									  ->setCellValue('A'.$row, date('d/m/Y',$item['fecreg']->sec))
									  ->setCellValueExplicit('B'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING)
									  ->setCellValueExplicit('C'.$row, $serie, PHPExcel_Cell_DataType::TYPE_STRING);	
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $item['num'])
									  ->setCellValue('E'.$row, $tipo_doc)							  
									  ->setCellValue('F'.$row, $item['doc_cliente'])
									  ->setCellValue('G'.$row, $item['cliente'])
									  ->setCellValue('H'.$row, $helper->format_word($item['playa']['nomb']))
		                              ->setCellValue('J'.$row, number_format($subtotal, 2, '.', ''))
		                              ->setCellValue('N'.$row, number_format($item['total'], 2, '.', ''))
									  ->setCellValue('L'.$row, number_format($igv, 2, '.', ''))
									  ->setCellValue('Q'.$row, number_format($item['total'], 2, '.', ''))
									  ->setCellValue('A'.$row, date('d/m/Y',$item['fecreg']->sec))
									  ->setCellValueExplicit('B'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING)
									  ->setCellValueExplicit('C'.$row, $serie, PHPExcel_Cell_DataType::TYPE_STRING);

		}

		
	}else{
		# PARCHE DE FACTURACION ELECTRONICA
		if(isset($item['cliente_nomb'])) {
			$item['doc_cliente']=$item['cliente_doc'];
			$item['cliente']['nomb']=$item['cliente_nomb'];
		}
		if(isset($item['cliente_doc'])) {
			$item['cliente']['docident'][0]['num']=$item['cliente_doc'];
		}
		if(isset($item['fecemi'])) {
			$item['fecreg']=$item['fecemi'];
			//$item['fecreg']=$item['fecemi'];
		}
		if (isset($item['total_ope_gravadas'])) {
			$item['subtotal']=$item['total_ope_gravadas'];
		}
		if (isset($item['total_igv'])) {
			$item['igv']=$item['total_igv'];
		}
		if (isset($item['numero'])) {
			$item['num']=$item['numero'];
		}
		if (!isset($item['playa']['nomb'])) {
			$item['playa']['nomb']="DESCONOCIDO";
		}
		if(isset($item['tc'])){
			$tc = floatval($item['tc']);
		}elseif(isset($item['tipo_cambio'])) {
			$tc = floatval($item['tipo_cambio']);
		}

		#CONTINUAR CON EL LEGADO
		$cliente = $item['cliente']['nomb'];
		if(isset($item['cliente']['appat'])) $cliente = $item['cliente']['appat'].' '.$item['cliente']['apmat'].', '.$cliente;
		$serie = $item['serie'];
		$subtotal = $item['subtotal'];
		$igv = $item['igv'];
		if($item['serie']=="B001" || $item['serie']=="F001"){
			$serie = $item['serie'];
		}elseif(''.intval($item['serie'])=='22'&&$item['tipo']=='F'){
			$serie = '0022';
		}elseif(''.intval($item['serie'])=='1'){
			$serie = '00'.intval($item['serie']);
		}else{
			$serie = '0'.intval($item['serie']);
		}
		if($item['tipo']=='F'){
			$tipo = '01';
		}elseif($item['tipo']=='B') {
			$tipo = '03';
		}elseif($item['tipo']=='R'){
			$tipo = '00';
			$serie = '0000';
			$subtotal = '';
			$igv = 0;
			$mora = $item['total'];
			
		}elseif($item['tipo']=='NC'){
			$tipo = '07';
		}
		if($item['estado']=='X'){
			$igv = 0;
			$moras = 0;
			$subtotal = 0;
			$item['total'] = 0;
			$cliente = 'Dado de Baja';
			$item['cliente']['docident'][0]['num'] = '0';
			$tipo_doc = '0';
		}else{
			$moras = 0;
			$subtotal = 0;
			$igv = 0;
			if(isset($item['combinar_alq'])){
				foreach ($item['items'] as $ite) {
					foreach ($ite['items'] as $it) {
						if(isset($it['conceptos'])){
							foreach ($it['conceptos'] as $conc) {
								if($conc['cuenta']['cod']=='1202.0901.47'){
									$moras += floatval($conc['monto']);
								}elseif($conc['cuenta']['cod']=='2101.010501'){
									$igv += floatval($conc['monto']);
								}else{
									$subtotal += floatval($conc['monto']);
								}
							}
						}
					}
				}
			#LA FACTURACION FUNCIONA TAMBIEN AQUI
			}elseif(isset($item['items'])){
				foreach ($item['items'] as $it) {
					if(isset($it['conceptos'])){
						foreach ($it['conceptos'] as $conc) {
							if($conc['cuenta']['cod']=='1202.0901.47' || $conc['cuenta']['cod']=='1202.0902'){
								$moras += floatval($conc['monto']);
							}elseif($conc['cuenta']['cod']=='2101.010501'){
								$igv += floatval($conc['monto']);
								//Para el caso de las bases, el IGV sale en una cuenta diferente
							}elseif($conc['cuenta']['cod']=='2101.010503'){
								$igv += floatval($conc['monto']);
							}else{
								$subtotal += floatval($conc['monto']);
							}
						}
					}
				}
			}elseif(isset($item['conceptos'])){
				foreach ($item['conceptos'] as $conc) {
					if($conc['concepto']['cuenta']['cod']=='2101.010501'){
						$igv += floatval($conc['monto']);
					}else{
						$subtotal += floatval($conc['monto']);
					}
				}
				if($igv==0 && $serie!="0000"){
					$subtotal = $subtotal / 1.18;
					$igv = $subtotal * 0.18;
				}
			}
			if($tipo == '07'){
				$igv = $igv * -1;
				$subtotal = $subtotal * -1;
				$item['total'] = $item['total'] * -1;
			}
		}
		//$subtotal -= $moras;
		if(!isset($item['cliente']['docident'])){
			$item['cliente']['docident'] = array(array('num'=>'','tipo'=>''));
		
		}
		if(isset($item['doc_cliente'])){
			if(strlen($item['doc_cliente']) == 8 && $item['estado']!="X"){
				$tipo_doc = '1';
			}else if(strlen($item['doc_cliente']) > 8 && $item['estado']!="X"){
				$tipo_doc = '6';
			}
		}else{
			foreach($item['cliente']['docident'] as $doc => $docu){
				if(strlen($docu['num']) == 8 && $item['estado']!="X"){
					$tipo_doc = '1';
				}else if(strlen($docu['num']) > 8 && $item['estado']!="X"){
					$tipo_doc = '6';
				}
			}
		}
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $item['num'])
									  ->setCellValue('E'.$row, $tipo_doc)
									  ->setCellValueExplicit('F'.$row, $item['cliente']['docident'][0]['num'],PHPExcel_Cell_DataType::TYPE_STRING)
									  ->setCellValue('G'.$row, $helper->format_word($cliente) )
									  ->setCellValue('Q'.$row, number_format($item['total'], 2, '.', ''))
									  ->setCellValue('A'.$row, date('d/m/Y',$item['fecreg']->sec))
									  ->setCellValueExplicit('B'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING)
									  ->setCellValueExplicit('C'.$row, $serie, PHPExcel_Cell_DataType::TYPE_STRING);

	if(isset($item['items'])){
		//print_r($item);
		if($item['tipo'] == 'R'){
			if($item['moneda']=='S' || $item['moneda']=='PEN'){
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, '', 2, '.', '');
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, '', 2, '.', '');
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, (''), 2, '.', '');
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($subtotal, 2, '.', ''));
			}else if($item['moneda']=='D' || $item['moneda']=='USD'){
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, number_format($subtotal, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv), 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$row, number_format($moras, 2, '.', ''));
	
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv)*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($moras*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, number_format($item['total']*$tc, 2, '.', ''));
			}
		}else{
			if($item['moneda']=='S' || $item['moneda']=='PEN'){
				if(isset($moras)){
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, number_format($igv, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, number_format(($subtotal+$igv), 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($tot_saciones, 2, '.', ''));
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, number_format($igv, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, number_format(($subtotal+$igv), 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, '', 2, '.', '');
				}
				
			}else if($item['moneda']=='D' || $item['moneda']=='USD'){
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, number_format($subtotal, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv), 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$row, number_format($moras, 2, '.', ''));
	
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv)*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($moras*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, number_format($item['total']*$tc, 2, '.', ''));
			}
		}
	}else{
		if($item['tipo'] == 'R'){
			if($item['moneda']=='S' || $item['moneda']=='PEN'){
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, '', 2, '.', '');
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, '', 2, '.', '');
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, (''), 2, '.', '');
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($subtotal, 2, '.', ''));
			}else if($item['moneda']=='D' || $item['moneda']=='USD'){
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, number_format($subtotal, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv), 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$row, number_format($moras, 2, '.', ''));
	
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv)*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($moras*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, number_format($item['total']*$tc, 2, '.', ''));
			}
		}else{
			if($item['moneda']=='S' || $item['moneda']=='PEN'){
				if(isset($moras)){
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, number_format($igv, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, number_format(($subtotal+$igv), 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, '', 2, '.', '');
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, number_format($igv, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal, 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$row, number_format(($subtotal+$igv), 2, '.', ''));
					$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, '', 2, '.', '');
				}
				
			}else if($item['moneda']=='D' || $item['moneda']=='USD'){
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, number_format($subtotal, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv), 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$row, number_format($moras, 2, '.', ''));
	
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, number_format($igv*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, number_format($subtotal*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, number_format(($subtotal+$igv)*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$row, number_format($moras*$tc, 2, '.', ''));
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, number_format($item['total']*$tc, 2, '.', ''));
			}
		}
	}
	
		if(isset($item['inmueble'])&&$item['tipo']!='R'){
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $helper->format_word($item['inmueble']['tipo']['nomb']));
		}
	}
}
//die();
if($recibo['tipo_inm']=='P'){
	
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, 'TOTAL')
	                              ->setCellValue('I'.$row, number_format(0, 2, '.', ''))
	                              ->setCellValue('J'.$row, '=SUM(J'.$baseRow.':J'.($row-1).')')
	                              ->setCellValue('L'.$row, '=SUM(L'.$baseRow.':L'.($row-1).')')
				      //->setCellValue('K'.$row, number_format(0, 2, '.', ''))
	                              ->setCellValue('N'.$row, '=SUM(N'.$baseRow.':N'.($row-1).')')
								  //->setCellValue('N'.$row, number_format(0, 2, '.', ''))
								  //->setCellValue('O'.$row, '=SUM(M'.$baseRow.':N'.($row-1).')')
								  ->setCellValue('Q'.$row, '=SUM(Q'.$baseRow.':Q'.($row-1).')');
}else{
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, 'TOTAL')
								  //->setCellValue('I'.$row, number_format(0, 2, '.', ''))
	                              ->setCellValue('J'.$row, '=SUM(J'.$baseRow.':J'.($row-1).')')
	                              ->setCellValue('L'.$row, '=SUM(L'.$baseRow.':L'.($row-1).')')
				      ->setCellValue('N'.$row, '=SUM(N'.$baseRow.':N'.($row-1).')')
	                              //->setCellValue('N'.$row, '=SUM(N'.$baseRow.':N'.($row-1).')')
				      ->setCellValue('P'.$row, '=SUM(P'.$baseRow.':P'.($row-1).')')

								  //->setCellValue('O'.$row, number_format(0, 2, '.', ''))
								  ->setCellValue('Q'.$row, '=SUM(Q'.$baseRow.':Q'.($row-1).')');
								  //->setCellValue('Q'.$row, '=SUM(P'.$baseRow.':P'.($row-1).')');
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Planilla de Inmuebles '.$tipo_rein.' - '.date('Y-m-d',$recibo['fec']->sec).'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>