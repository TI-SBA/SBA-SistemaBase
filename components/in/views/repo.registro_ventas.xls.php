<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cj/registro_ventas_2018.xlsx');
//$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cj/registro_ventas.xlsx');

//echo date('H:i:s') , " Add new data to the template" , EOL;
$baseRow = 11;
$index=0;
$rr = 0;
$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
$objPHPExcel->getActiveSheet()->setCellValue('I4',$meses[intval($params["mes"])].", ".intval($params["ano"]) );
$total_dia = 0;
$total_subtotal = 0;
$total_igv = 0;
$total_inafecta = 0;
$total_all = 0;
if(!isset($data[0]['fecreg'])) $data[0]['fecreg']=$data[0]['fecemi'];
$tmp_dia = date('Y-m-d', $data[0]['fecreg']->sec);
if(isset($data)){
	foreach($data as $r => $item) {
		//$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);

		#VARIABLES INICIALES
		$periodo="POR DEFECTO";
		$tipo = 'NO SE DETECTA TIPO';
		$nomb = "NO SE DETECTA NOMBRE";
		$serie = "NO SE DETECTA SERIE";
		$numero = "NO SE DETECTA NUMERO";
		$fecha = "NO SE DETECTA FECHA";
		$tipo_doc = "NO SE DETECTA TIPO_DOCUMENTO";
		$doc = "NO SE DETECTA DOCUMENTO";
		$subtotal = '';
		$igv = '';
		$inafecta = '';
		$total = 0;

		if(isset($params['mes']) && isset($params['ano'])){
			$periodo=$params['ano'].$params['mes']."00";
		}


		if (isset($item['numero'])) {
			/**
			*	PROCESAMIENTO DE LA INFORMACION DE COMPROBANTES ELECTRONICOS
			*/
			if(!isset($item['fecreg'])) $item['fecreg']=$item['fecemi'];

			if(isset($item['fecreg'])){
				$fecha = date('d/m/Y', $item['fecreg']->sec);
			}

			if($tmp_dia!=date('Y-m-d', $item['fecreg']->sec)){
				$objPHPExcel->getActiveSheet()->setCellValue('AJ'.($row), $total_dia);
				$total_dia = 0;
				$tmp_dia = date('Y-m-d', $item['fecreg']->sec);
			}

			if($item['total']=='0')
				$item['total'] = 0;

			/** 
			*	A PARTIR DE AQUI SE APLICA TODO LO REALACIONADO A FACTURACION ELECTRONICA
			*/
			#REALIZAR NOMBRE DE COMPROBANTES ELECTRONICOS
			

			if(isset($item['tipo_doc'])){
				switch($item['tipo_doc']){
					case 'RUC':
						$tipo_doc = '06';
						break;
					case 'DNI':
						$tipo_doc = '01';
						break;
					case '0':
						$tipo_doc = '00';
						break;
				}

				$doc = $item['cliente_doc'];
				if(isset($item['cliente_nomb'])){
					if($tipo_doc == '01'){
						$nombre_array = explode(' ',$item['cliente_nomb']);
						$apellido_array = array_slice($nombre_array, -2, 2);
						$nombre_array = array_slice($nombre_array, 0, -2);
						$apellido = implode(' ', $apellido_array);
						$nombre = implode(' ', $nombre_array);
						$nomb = $apellido.", ".$nombre;
					} else {
						$nomb = $item['cliente_nomb'];
					}
				}
			}

			if(isset($item['tipo'])){
				//$tipo = $item['tipo'];
				$doc = $item['cliente_doc'];
				switch($item['tipo']){
					case 'R':
						$tipo = '00';
						break;
					case 'F':
						$tipo = '01';
						break;
					case 'B':
						$tipo = '03';
						break;
					case 'RA':
						$tipo = '00';
						break;
					case 'NC':
						$tipo = '07';
						break;
					case 'NB':
						$tipo = '08';
						break;	
				}
			}

			if(isset($item['total_ope_gravadas'])){
				$subtotal = $item['total_ope_gravadas'];
				$igv = $item['total_igv'];
				if($item['total_ope_inafectas'] > 0) $inafecta = $item['total_ope_inafectas'];
				$total = $item['total'];
			}

			if(isset($item['serie'])){
				$serie = $item['serie'];
			}

			if(isset($item['numero'])){
				$numero = $item['numero'];
			}
			
			if($item['estado']=='X'){
				$item['total'] = 0;
				$nomb = 'Anulado';
				$tipo_doc = '00';
				$doc = 0;
				$subtotal = 0;
				$inafecta = 0;
				$subtotal = 0;
				$igv = 0;
				$total = 0;
			}
			if(!isset($item['num'])){
				$item['num'] = floatval($item['numero']);
			}

		} elseif(isset($item['num'])) {
			/**
			*	PROCESAMIENTO DE LA INFORMACION DE COMPROBANTES MANUALES
			*/

			/*if(substr($item['total'],strlen($item['total'])-3,3)=='.00'){
				$item['total'] = substr($item['total'],0,strlen($item['total'])-3);
			}*/

			if(isset($item['tipo'])){
				switch($item['tipo']){
					case 'R':
						$tipo = '00';
						break;
					case 'F':
						$tipo = '01';
						break;
					case 'B':
						$tipo = '03';
						break;
				}
			}

			if(isset($item['fecreg'])){
				$fecha = date('d/m/Y', $item['fecreg']->sec);
			}

			# FINAL DEL DIA
			if($tmp_dia!=date('Y-m-d', $item['fecreg']->sec)){
				//NO FUNCIONA
				if(isset($rede)){
					foreach($rede as $re => $itemr) {
						if($tmp_dia==$itemr['fec']){
							$row = $baseRow + $r + $rr;
							$nomb = $itemr['entidad']['nomb'];
							if(isset($itemr['entidad']['appat']))
								$nomb = $itemr['entidad']['appat'].' '.$itemr['entidad']['apmat'].', '.$nomb;
							/*if(substr($itemr['total'],strlen($itemr['total'])-3,3)=='.00'){
								$itemr['total'] = substr($itemr['total'],0,strlen($itemr['total'])-3);
							}*/
							if($itemr['total']=='0'){
								$itemr['total'] = 0;
							}
							$itemr['fec'] = date("d/m/Y", strtotime($itemr['fec']));
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $periodo)
														  ->setCellValue('B'.$row, $r+$rr+1)
							                              ->setCellValue('D'.$row, $itemr['fec'])
														  ->setCellValue('F'.$row, $tipo)
														  ->setCellValue('G'.$row, 'Rec.')
														  ->setCellValue('H'.$row, $itemr['num'])
														  ->setCellValue('L'.$row, $nomb)
														  ->setCellValue('P'.$row, $itemr['total'])
														  ->setCellValue('V'.$row, $itemr['total']);
							$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING);
							$total_dia += floatval($itemr['total']);
							$total_all += floatval($itemr['total']);
							$rr++;
						}
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('AJ'.($row), $total_dia);
				$total_dia = 0;
				$tmp_dia = date('Y-m-d', $item['fecreg']->sec);
			}

			if(isset($item['serie'])){
				$serie = $item['serie'];
			}

			if(isset($item['doc_cliente']) && !is_null($item['doc_cliente'])) {
				$doc = $item['doc_cliente'];
			}elseif(isset($item['cliente']['docident'][0]['num'])){
				$doc = $item['cliente']['docident'][0]['num'];
			}else{
				$doc = "0";
			}
			
			if(isset($doc)) {
				if($tipo_doc=="NO SE DETECTA TIPO_DOCUMENTO")
				if(strlen($doc)==8){
					$tipo_doc = '01';
				}elseif(strlen($doc)==11){
					$tipo_doc = "06";
				}else{
					$tipo_doc = "00";
				}
			}else{
				$tipo_doc = "00";
			}
			
			if(isset($item['num'])){
				$numero = $item['num'];
			}

			if(isset($item['cliente']['nomb']))
				$nomb = $item['cliente']['nomb'];
			elseif (is_string($item['cliente'])) {
				$nomb = $item['cliente'];
			}

			if(isset($item['cliente']['appat']))
				$nomb = $item['cliente']['appat'].' '.$item['cliente']['apmat'].', '.$nomb;

			if($tipo=='00'){
				$inafecta = $item['total'];
				$total = $item['total'];
			}elseif($tipo=='01' || $tipo=='03'){
				$subtotal = $item['subtotal'];
				$igv = $item['igv'];
				$total = $item['total'];
			}

			if($item['estado']=='X'){
				$item['total'] = 0;
				$nomb = 'Anulado';
			}

			if($item['total']=='0')
				$item['total'] = 0;

			# HAY VECES QUE EL ARRAY DE SUNAT EXISTE, PARA MORAS U OTROS, ESTE TIENE MAS PRIORIDAD
			if(isset($item['sunat'])){
				foreach ($item['sunat'] as $s => $sunat) {
					if(isset($sunat['ope_gravadas']))  $subtotal += $sunat['ope_gravadas'];
					if(isset($sunat['ope_inafectas'])) if($sunat['ope_inafectas'] > 0) $inafecta += $sunat['ope_inafectas'];
					if(isset($sunat['igv'])) 		   $igv += round(($sunat['igv']*$sunat['ope_gravadas']),2);
					if(isset($sunat['importe_total'])) $total += $sunat['importe_total'];
				}
			}
		} else {
			print_r($item);
			die();
		}

		$row = $baseRow + $r + $rr;

		//$item['total'] = "'".$item['total'];
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $periodo)
									  ->setCellValue('B'.$row, $r+$rr+1)
		                              ->setCellValue('D'.$row, $fecha)
									  ->setCellValue('F'.$row, $tipo)
									  ->setCellValue('G'.$row, $serie)
									  ->setCellValue('H'.$row, $numero)
									  ->setCellValue('J'.$row, $tipo_doc)
									  ->setCellValue('K'.$row, $doc)
									  ->setCellValue('L'.$row, $nomb)
									  ->setCellValue('N'.$row, $subtotal)
									  ->setCellValue('P'.$row, $igv)
									  ->setCellValue('S'.$row, $inafecta)
									  ->setCellValue('X'.$row, $total);
									  #->setCellValue('V'.$row, $item['total']);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING);
		$total_dia += floatval($total);
		$total_subtotal += floatval($subtotal);
		$total_igv += floatval($igv);
		$total_inafecta += floatval($inafecta);
		$total_all += floatval($total);
	}
}
# HE REVISADO QUE NO FUNCIONA ESTA PORCION DE CODIGO POR AHORA
if(isset($rede)){
	foreach($rede as $re => $itemr) {
		if($tmp_dia==$itemr['fec']){
			$row = $baseRow + $r + 1 + $rr;
			$nomb = $itemr['entidad']['nomb'];
			if(isset($itemr['entidad']['appat']))
				$nomb = $itemr['entidad']['appat'].' '.$itemr['entidad']['apmat'].', '.$nomb;
			/*if(substr($itemr['total'],strlen($itemr['total'])-3,3)=='.00'){
				$itemr['total'] = substr($itemr['total'],0,strlen($itemr['total'])-3);
			}*/
			if($itemr['total']=='0'){
				$itemr['total'] = 0;
			}
			$itemr['fec'] = date("d/m/Y", strtotime($itemr['fec']));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $r+$rr+2)
			                              ->setCellValue('D'.$row, $itemr['fec'])
										  ->setCellValue('F'.$row, $tipo)
										  ->setCellValue('G'.$row, 'Rec.')
										  ->setCellValue('H'.$row, $itemr['num'])
										  ->setCellValue('L'.$row, $nomb)
										  ->setCellValue('P'.$row, $itemr['total'])
										  ->setCellValue('V'.$row, $itemr['total']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$row, $tipo, PHPExcel_Cell_DataType::TYPE_STRING);
			$total_dia += floatval($itemr['total']);
			$total_all += floatval($itemr['total']);
			$rr++;
		}
	}
}

# TOTALES
$objPHPExcel->getActiveSheet()->setCellValue('AJ'.($row), $total_dia);

$row++;
$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, 'TOTALES')
                              ->setCellValue('N'.$row, number_format($total_subtotal,2))
							  ->setCellValue('P'.$row, number_format($total_igv,2))
							  ->setCellValue('S'.$row, number_format($total_inafecta,2))
							  ->setCellValue('X'.$row, number_format($total_all,2))
							  ->setCellValue('AJ'.$row, number_format($total_all,2));
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
$objPHPExcel->getActiveSheet()->getStyle('L'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('N'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('P'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('S'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('X'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AJ'.$row)->applyFromArray($styleArray);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Registro de Ventas '.$params['ano'].'-'.$params['mes'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>