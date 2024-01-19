<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/cj/continuidad_comprobantes.xlsx');

//$meses = array('',"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");

/**
*	INFORMACION INICIAL
*/

# ESTRUCTURA DEL INICIAL DEL DOCUMENTO[HOJA][CAMPO1][subcampos]
$documento_inicial=array(
	'RESUMEN'=>array(
		'CABECERA'=>array(
			'cord_inicial'=>'C2',
			'cord_final'=>'C5',
			'fecini'=>array(
				'cord_fila'=>'C',
				'cord_columna'=>'2',
				'valor'=>'ERROR: NO SE SABE LA FECHA INICIAL',
			),
			'fecfin'=>array(
				'cord_fila'=>'C',
				'cord_columna'=>'3',
				'valor'=>'ERROR: NO SE SABE LA FECHA FINAL',
			),
			'modulo'=>array(
				'cord_fila'=>'C',
				'cord_columna'=>'4',
				'valor'=>'ERROR: NO SE SABE EL MODULO',
			),
			'autor'=>array(
				'cord_fila'=>'C',
				'cord_columna'=>'5',
				'valor'=>'ERROR: NO SE SABE QUE AUTOR LO REALIZO',
			),
		),
		'TABLA'=>array(
			'cord_inicial'=>'B8',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
			'faltantes'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'E',
				'valor'=>'DESCONOCIDO',
			),
			'observaciones'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'F',
				'valor'=>'DESCONOCIDO',
			),
			'duplicados'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'G',
				'valor'=>'DESCONOCIDO',
			),
		),
	),
	'Faltantes'=>array(
		'manuales'=>array(
			'cord_inicial'=>'A1',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'A',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'numero'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
		),
		'electronicas'=>array(
			'cord_inicial'=>'A5',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'A',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'numero'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
		),
	),
	'Observaciones'=>array(
		'manuales'=>array(
			'cord_inicial'=>'B1',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
			'numero'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'E',
				'valor'=>'DESCONOCIDO',
			),
			'fecha_emi'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'F',
				'valor'=>'DESCONOCIDO',
			),
		),
		'electronicas'=>array(
			'cord_inicial'=>'B5',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
			'numero'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'E',
				'valor'=>'DESCONOCIDO',
			),
			'fecha_emi'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'F',
				'valor'=>'DESCONOCIDO',
			),
		),
	),
	'Repeticiones'=>array(
		'manuales'=>array(
			'cord_inicial'=>'B2',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
			'numero'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'E',
				'valor'=>'DESCONOCIDO',
			),
			'fecha_emi'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'F',
				'valor'=>'DESCONOCIDO',
			),
		),
		'electronicas'=>array(
			'cord_inicial'=>'B5',
			'cord_final'=>'--',
			'iterable'=>true,
			'modalidad'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'B',
				'valor'=>'DESCONOCIDO',
			),
			'serie'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'C',
				'valor'=>'DESCONOCIDO',
			),
			'tipo'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'D',
				'valor'=>'DESCONOCIDO',
			),
			'numero'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'E',
				'valor'=>'DESCONOCIDO',
			),
			'fecha_emi'=>array(
				'cord_fila'=>'--',
				'cord_columna'=>'F',
				'valor'=>'DESCONOCIDO',
			),
		),
	),
);
$documento=$documento_inicial;
/**
*	PROCESAMIENTO Y MODFICACIÓN DE CAMPOS
*/

# Hoja de RESUMEN
	// CABECERA
	$cabecera=$documento['RESUMEN']['CABECERA'];
	if(isset($params['fecini'])) 	$cabecera['fecini']['valor']=$params['fecini'];
	if(isset($params['fecfin'])) 	$cabecera['fecfin']['valor']=$params['fecfin'];
	$cabecera['modulo']['valor']=$data['modulo'];
	$cabecera['autor']['valor']=$data['autor']['nomb']." ".$data['autor']['appat']." ".$data['autor']['apmat'];
	$documento['RESUMEN']['CABECERA']=$cabecera;

	// TABLA
	$tabla_inicial=$documento_inicial['RESUMEN']['TABLA'];
	$documento['RESUMEN']['TABLA']=[];
	$tabla=[];
	$fila=substr($tabla_inicial['cord_inicial'],-1);
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$tabla_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;
	
	$data_clasif=[];
	foreach ($data as $c => $campo) {
		if($c==="observaciones" || $c==="faltantes" || $c==="duplicados"){
			foreach ($campo as $m => $motivo) {
				if($m==="manual"){
					foreach ($motivo as $t => $tipo) {
						foreach ($tipo as $s => $serie) {
							$data_clasif[$m][$t][$s][$c]=$serie;
						}
					}
				}
				if($m==="electronica"){
					foreach ($motivo as $s => $serie) {
						foreach ($serie as $t => $tipo) {
							$data_clasif[$m][$t][$s][$c]=$serie[$t];
						}
					}
				}
			}
		}
	}

	if($tabla_inicial['iterable']){
		foreach ($data_clasif as $m => $motivo){
			foreach ($motivo as $t => $tipo) {
				foreach ($tipo as $s => $serie) {
					// VALOR
					$tabla=$tabla_inicial;
					$tabla['modalidad']['valor']=$m;
					$tabla['serie']['valor']=(string)$s;
					$tabla['tipo']['valor']=(string)$t;

					if(isset($serie['faltantes'])) {
						if(!empty($serie['faltantes'])){
							$tabla['faltantes']['valor']="SI";	
						}else{
							$tabla['faltantes']['valor']="NO";
						}
					}
					else{
						$tabla['faltantes']['valor']="NO";
					}

					if(isset($serie['observaciones'])) {
						if(!empty($serie['observaciones'])){
							$tabla['observaciones']['valor']="SI";	
						}else{
							$tabla['observaciones']['valor']="NO";
						}
					}
					else{
						$tabla['observaciones']['valor']="NO";
					}

					if(isset($serie['duplicados'])) {
						if(!empty($serie['duplicados'])){
							$tabla['duplicados']['valor']="SI";	
						}else{
							$tabla['duplicados']['valor']="NO";
						}
					}
					else{
						$tabla['duplicados']['valor']="NO";
					}

					// POSICION
					$tabla['modalidad']['cord_fila']=$fila;
					$tabla['serie']['cord_fila']=$fila;
					$tabla['tipo']['cord_fila']=$fila;
					$tabla['faltantes']['cord_fila']=$fila;
					$tabla['observaciones']['cord_fila']=$fila;
					$tabla['duplicados']['cord_fila']=$fila;
					$tabla['duplicados']['cord_fila']=$fila;
					$colum_final=$tabla['duplicados']['cord_columna'];
					$fila_final=$fila;
					$tabla['cord_inicial']=$colum_inicial.$fila_inicial;
					$tabla['cord_final']=$colum_final.$fila_final;
					array_push($documento['RESUMEN']['TABLA'],$tabla);
					$fila++;
				}
			}
		}
	}

# Hoja de Faltantes

	// FALTANTES MANUALES
	$falt_manual_inicial=$documento_inicial['Faltantes']['manuales'];
	$documento['Faltantes']['manuales']=[];
	$falt_manual=[];
	$fila=substr($falt_manual_inicial['cord_inicial'],-1);
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$falt_manual_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;

		// TITULO DE LAS FALTANTES MANUALES
		$falt_manual=$falt_manual_inicial;
		$falt_manual['modalidad']['valor']="MODALIDAD";
		$falt_manual['serie']['valor']="SERIE";
		$falt_manual['tipo']['valor']="TIPO";
		$falt_manual['numero']['valor']="NUMERO";
		// POSICION DEL TITULO
		$falt_manual['modalidad']['cord_fila']=$fila;
		$falt_manual['serie']['cord_fila']=$fila;
		$falt_manual['tipo']['cord_fila']=$fila;
		$falt_manual['numero']['cord_fila']=$fila;
		$colum_final=$falt_manual['numero']['cord_columna'];
		$fila_final=$fila;
		$falt_manual['cord_inicial']=$colum_inicial.$fila_inicial;
		$falt_manual['cord_final']=$colum_final.$fila_final;
		array_push($documento['Faltantes']['manuales'],$falt_manual);
		$fila++;

		foreach ($data as $c => $campo) {
			if($c==="faltantes"){
				foreach ($campo as $m => $motivo) {
					if($m==="manual"){
						foreach ($motivo as $t => $tipo) {
							foreach ($tipo as $s => $serie) {
								foreach($serie as $n => $numero){
									// VALOR
									$falt_manual=$falt_manual_inicial;
									$falt_manual['modalidad']['valor']=$m;
									$falt_manual['serie']['valor']=(string)$s;
									$falt_manual['tipo']['valor']=(string)$t;
									$falt_manual['numero']['valor']=(string)$numero;
									// POSICION
									$falt_manual['modalidad']['cord_fila']=$fila;
									$falt_manual['serie']['cord_fila']=$fila;
									$falt_manual['tipo']['cord_fila']=$fila;
									$falt_manual['numero']['cord_fila']=$fila;
									$colum_final=$falt_manual['numero']['cord_columna'];
									$fila_final=$fila;
									$falt_manual['cord_inicial']=$colum_inicial.$fila_inicial;
									$falt_manual['cord_final']=$colum_final.$fila_final;
									array_push($documento['Faltantes']['manuales'],$falt_manual);
									$fila++;
								}
							}
						}
					}
				}
			}
		}

	// FALTANTES ELECTRONICAS
	$falt_elect_inicial=$documento_inicial['Faltantes']['electronicas'];
	$documento['Faltantes']['electronicas']=[];
	$falt_elect=[];
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$falt_elect_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;

		// TITULO DE LAS FALTANTES ELECTRONICAS
		$falt_elect=$falt_elect_inicial;
		$falt_elect['modalidad']['valor']="MODALIDAD";
		$falt_elect['serie']['valor']="SERIE";
		$falt_elect['tipo']['valor']="TIPO";
		$falt_elect['numero']['valor']="NUMERO";
		// POSICION DEL TITULO
		$falt_elect['modalidad']['cord_fila']=$fila;
		$falt_elect['serie']['cord_fila']=$fila;
		$falt_elect['tipo']['cord_fila']=$fila;
		$falt_elect['numero']['cord_fila']=$fila;
		$colum_final=$falt_elect['numero']['cord_columna'];
		$fila_final=$fila;
		$falt_elect['cord_inicial']=$colum_inicial.$fila_inicial;
		$falt_elect['cord_final']=$colum_final.$fila_final;
		array_push($documento['Faltantes']['electronicas'],$falt_elect);
		$fila++;

		foreach ($data as $c => $campo) {
			if($c==="faltantes"){
				foreach ($campo as $m => $motivo) {
					if($m==="electronica"){
						foreach ($motivo as $s => $serie) {
							foreach ($serie as $t => $tipo) {
								foreach($tipo as $n => $numero){
									// VALOR
									$falt_elect=$falt_elect_inicial;
									$falt_elect['modalidad']['valor']=$m;
									$falt_elect['serie']['valor']=(string)$s;
									$falt_elect['tipo']['valor']=(string)$t;
									$falt_elect['numero']['valor']=(string)$numero;

									// POSICION
									$falt_elect['modalidad']['cord_fila']=$fila;
									$falt_elect['serie']['cord_fila']=$fila;
									$falt_elect['tipo']['cord_fila']=$fila;
									$falt_elect['numero']['cord_fila']=$fila;
									$colum_final=$falt_elect['numero']['cord_columna'];
									$fila_final=$fila;
									$falt_elect['cord_inicial']=$colum_inicial.$fila_inicial;
									$falt_elect['cord_final']=$colum_final.$fila_final;
									array_push($documento['Faltantes']['electronicas'],$falt_elect);
									$fila++;
								}
							}
						}
					}
				}
			}
		}

# Hoja de Observaciones


	// OBSERVACIONES MANUALES
	$obsv_manual_inicial=$documento_inicial['Observaciones']['manuales'];
	$documento['Observaciones']['manuales']=[];
	$obsrv_manual=[];
	$fila=substr($obsv_manual_inicial['cord_inicial'],-1);
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$obsv_manual_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;

		// TITULO DE LAS OBSERVACIONES MANUALES
		$obsrv_manual=$obsv_manual_inicial;
		$obsrv_manual['modalidad']['valor']="MODALIDAD";
		$obsrv_manual['serie']['valor']="SERIE";
		$obsrv_manual['tipo']['valor']="TIPO";
		$obsrv_manual['numero']['valor']="NUMERO";
		$obsrv_manual['fecha_emi']['valor']="FECHA DE EMISION";
		// POSICION DEL TITULO
		$obsrv_manual['modalidad']['cord_fila']=$fila;
		$obsrv_manual['serie']['cord_fila']=$fila;
		$obsrv_manual['tipo']['cord_fila']=$fila;
		$obsrv_manual['numero']['cord_fila']=$fila;
		$obsrv_manual['fecha_emi']['cord_fila']=$fila;
		$colum_final=$obsrv_manual['fecha_emi']['cord_columna'];
		$fila_final=$fila;
		$obsrv_manual['cord_inicial']=$colum_inicial.$fila_inicial;
		$obsrv_manual['cord_final']=$colum_final.$fila_final;
		array_push($documento['Observaciones']['manuales'],$obsrv_manual);
		$fila++;

	foreach ($data as $c => $campo) {
		if($c==="observaciones"){
			foreach ($campo as $m => $motivo) {
				if($m==="manual"){
					foreach ($motivo as $t => $tipo) {
						foreach ($tipo as $s => $serie) {
							foreach($serie as $n => $numero){
								// VALOR
								$obsrv_manual=$obsv_manual_inicial;
								$obsrv_manual['modalidad']['valor']=$m;
								$obsrv_manual['serie']['valor']=(string)$s;
								$obsrv_manual['tipo']['valor']=(string)$t;
								$obsrv_manual['numero']['valor']=(string)$n;
								//$obsrv_manual['fecha_emi']['valor']=(string)(date($numero['fecreg'],"Y-m-d H:i:s"));
								$obsrv_manual['fecha_emi']['valor']=(date("Y-m-d",$numero['fecreg']->sec));

								// POSICION
								$obsrv_manual['modalidad']['cord_fila']=$fila;
								$obsrv_manual['serie']['cord_fila']=$fila;
								$obsrv_manual['tipo']['cord_fila']=$fila;
								$obsrv_manual['numero']['cord_fila']=$fila;
								$obsrv_manual['fecha_emi']['cord_fila']=$fila;
								$colum_final=$obsrv_manual['fecha_emi']['cord_columna'];
								$fila_final=$fila;
								$obsrv_manual['cord_inicial']=$colum_inicial.$fila_inicial;
								$obsrv_manual['cord_final']=$colum_final.$fila_final;
								array_push($documento['Observaciones']['manuales'],$obsrv_manual);
								$fila++;
							}
						}
					}
				}
			}
		}
	}

	// OBSERVACIONES ELECTRONICAS
	$obsv_elect_inicial=$documento_inicial['Observaciones']['electronicas'];
	$documento['Observaciones']['electronicas']=[];
	$obsv_elect=[];
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$obsv_elect_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;

		// TITULO DE LAS OBSERVACIONES MANUALES
		$obsv_elect=$obsv_elect_inicial;
		$obsv_elect['modalidad']['valor']="MODALIDAD";
		$obsv_elect['serie']['valor']="SERIE";
		$obsv_elect['tipo']['valor']="TIPO";
		$obsv_elect['numero']['valor']="NUMERO";
		$obsv_elect['fecha_emi']['valor']="FECHA DE REGISTRO";
		// POSICION DEL TITULO
		$obsv_elect['modalidad']['cord_fila']=$fila;
		$obsv_elect['serie']['cord_fila']=$fila;
		$obsv_elect['tipo']['cord_fila']=$fila;
		$obsv_elect['numero']['cord_fila']=$fila;
		$obsv_elect['fecha_emi']['cord_fila']=$fila;
		$colum_final=$obsv_elect['fecha_emi']['cord_columna'];
		$fila_final=$fila;
		$obsv_elect['cord_inicial']=$colum_inicial.$fila_inicial;
		$obsv_elect['cord_final']=$colum_final.$fila_final;
		array_push($documento['Observaciones']['electronicas'],$obsv_elect);
		$fila++;

	foreach ($data as $c => $campo) {
		if($c==="observaciones"){
			foreach ($campo as $m => $motivo) {
				if($m==="electronica"){
					foreach ($motivo as $s => $serie) {
						foreach ($serie as $t => $tipo) {
							foreach($tipo as $n => $numero){
								// VALOR
								$obsv_elect=$obsv_elect_inicial;
								$obsv_elect['modalidad']['valor']=$m;
								$obsv_elect['serie']['valor']=(string)$s;
								$obsv_elect['tipo']['valor']=(string)$t;
								$obsv_elect['numero']['valor']=(string)$n;
								//$obsrv_manual['fecha_emi']['valor']=(string)(date($numero['fecreg'],"Y-m-d H:i:s"));
								$obsv_elect['fecha_emi']['valor']=(date("Y-m-d",$numero['fecreg']->sec));

								// POSICION
								$obsv_elect['modalidad']['cord_fila']=$fila;
								$obsv_elect['serie']['cord_fila']=$fila;
								$obsv_elect['tipo']['cord_fila']=$fila;
								$obsv_elect['numero']['cord_fila']=$fila;
								$obsv_elect['fecha_emi']['cord_fila']=$fila;
								$colum_final=$obsv_elect['fecha_emi']['cord_columna'];
								$fila_final=$fila;
								$obsv_elect['cord_inicial']=$colum_inicial.$fila_inicial;
								$obsv_elect['cord_final']=$colum_final.$fila_final;
								array_push($documento['Observaciones']['electronicas'],$obsv_elect);
								$fila++;
							}
						}
					}
				}
			}
		}
	}

# Hoja de Duplicados

	// DUPLICACIONES MANUALES
	$dupli_manual_inicial=$documento_inicial['Repeticiones']['manuales'];
	$documento['Repeticiones']['electronicas']=[];
	$dupli_manual=[];
	$fila=substr($dupli_manual_inicial['cord_inicial'],-1);
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$dupli_manual_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;

		// TITULO DE LAS OBSERVACIONES MANUALES
		$dupli_manual=$dupli_manual_inicial;
		$dupli_manual['modalidad']['valor']="MODALIDAD";
		$dupli_manual['serie']['valor']="SERIE";
		$dupli_manual['tipo']['valor']="TIPO";
		$dupli_manual['numero']['valor']="NUMERO";
		$dupli_manual['fecha_emi']['valor']="FECHA DE EMISION";
		// POSICION DEL TITULO
		$dupli_manual['modalidad']['cord_fila']=$fila;
		$dupli_manual['serie']['cord_fila']=$fila;
		$dupli_manual['tipo']['cord_fila']=$fila;
		$dupli_manual['numero']['cord_fila']=$fila;
		$dupli_manual['fecha_emi']['cord_fila']=$fila;
		$colum_final=$dupli_manual['fecha_emi']['cord_columna'];
		$fila_final=$fila;
		$dupli_manual['cord_inicial']=$colum_inicial.$fila_inicial;
		$dupli_manual['cord_final']=$colum_final.$fila_final;
		array_push($documento['Repeticiones']['manuales'],$dupli_manual);
		$fila++;

	foreach ($data as $c => $campo) {
		if($c==="duplicados"){
			foreach ($campo as $m => $motivo) {
				if($m==="manual"){
					foreach ($motivo as $t => $tipo) {
						foreach ($tipo as $s => $serie) {
							foreach($serie as $n => $numero){
								// VALOR
								$dupli_manual=$dupli_manual_inicial;
								$dupli_manual['modalidad']['valor']=$m;
								$dupli_manual['serie']['valor']=(string)$s;
								$dupli_manual['tipo']['valor']=(string)$t;
								$dupli_manual['numero']['valor']=(string)$n;
								$dupli_manual['fecha_emi']['valor']=(date("Y-m-d",$numero['fecreg']->sec));

								// POSICION
								$dupli_manual['modalidad']['cord_fila']=$fila;
								$dupli_manual['serie']['cord_fila']=$fila;
								$dupli_manual['tipo']['cord_fila']=$fila;
								$dupli_manual['numero']['cord_fila']=$fila;
								$dupli_manual['fecha_emi']['cord_fila']=$fila;
								$colum_final=$dupli_manual['fecha_emi']['cord_columna'];
								$fila_final=$fila;
								$dupli_manual['cord_inicial']=$colum_inicial.$fila_inicial;
								$dupli_manual['cord_final']=$colum_final.$fila_final;
								array_push($documento['Repeticiones']['manuales'],$dupli_manual);
								$fila++;
							}
						}
					}
				}
			}
		}
	}

	// OBSERVACIONES ELECTRONICAS
	$dupli_elect_inicial=$documento_inicial['Repeticiones']['electronicas'];
	$documento['Repeticiones']['electronicas']=[];
	$dupli_elect=[];
	$fila_inicial=$fila;
	$fila_final=$fila;
	$colum=$dupli_elect_inicial['cord_inicial'][0];
	$colum_inicial=$colum;
	$colum_final=$colum;

		// TITULO DE LAS OBSERVACIONES MANUALES
		$dupli_elect=$dupli_elect_inicial;
		$dupli_elect['modalidad']['valor']="MODALIDAD";
		$dupli_elect['serie']['valor']="SERIE";
		$dupli_elect['tipo']['valor']="TIPO";
		$dupli_elect['numero']['valor']="NUMERO";
		$dupli_elect['fecha_emi']['valor']="FECHA DE REGISTRO";
		// POSICION DEL TITULO
		$dupli_elect['modalidad']['cord_fila']=$fila;
		$dupli_elect['serie']['cord_fila']=$fila;
		$dupli_elect['tipo']['cord_fila']=$fila;
		$dupli_elect['numero']['cord_fila']=$fila;
		$dupli_elect['fecha_emi']['cord_fila']=$fila;
		$colum_final=$dupli_elect['fecha_emi']['cord_columna'];
		$fila_final=$fila;
		$dupli_elect['cord_inicial']=$colum_inicial.$fila_inicial;
		$dupli_elect['cord_final']=$colum_final.$fila_final;
		array_push($documento['Repeticiones']['electronicas'],$dupli_elect);
		$fila++;

	foreach ($data as $c => $campo) {
		if($c==="duplicados"){
			foreach ($campo as $m => $motivo) {
				if($m==="electronica"){
					foreach ($motivo as $s => $serie) {
						foreach ($serie as $t => $tipo) {
							foreach($tipo as $n => $numero){
								// VALOR
								$dupli_elect=$dupli_elect_inicial;
								$dupli_elect['modalidad']['valor']=$m;
								$dupli_elect['serie']['valor']=(string)$s;
								$dupli_elect['tipo']['valor']=(string)$t;
								$dupli_elect['numero']['valor']=(string)$n;
								$dupli_elect['fecha_emi']['valor']=(date("Y-m-d",$numero['fecreg']->sec));

								// POSICION
								$dupli_elect['modalidad']['cord_fila']=$fila;
								$dupli_elect['serie']['cord_fila']=$fila;
								$dupli_elect['tipo']['cord_fila']=$fila;
								$dupli_elect['numero']['cord_fila']=$fila;
								$dupli_elect['fecha_emi']['cord_fila']=$fila;
								$colum_final=$dupli_elect['fecha_emi']['cord_columna'];
								$fila_final=$fila;
								$dupli_elect['cord_inicial']=$colum_inicial.$fila_inicial;
								$dupli_elect['cord_final']=$colum_final.$fila_final;
								array_push($documento['Repeticiones']['electronicas'],$dupli_elect);
								$fila++;
							}
						}
					}
				}
			}
		}
	}

/**
* 	ARMADO DEL ESQUEMA DOCUMENTO, (MODIFICAR SOLO SI EL ESQUEMA INICIAL FUE MODIFICADO)
*/

# Hoja de RESUMEN
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue($cabecera['fecini']['cord_fila'].$cabecera['fecini']['cord_columna'],$cabecera['fecini']['valor'])
                              ->setCellValue($cabecera['fecfin']['cord_fila'].$cabecera['fecfin']['cord_columna'],$cabecera['fecfin']['valor'])
                              ->setCellValue($cabecera['modulo']['cord_fila'].$cabecera['modulo']['cord_columna'],$cabecera['modulo']['valor'])
                              ->setCellValue($cabecera['autor']['cord_fila'].$cabecera['autor']['cord_columna'],$cabecera['autor']['valor']);

foreach ($documento['RESUMEN']['TABLA'] as $ft => $fila_tabla) {
	foreach ($fila_tabla as $ct => $columna_tabla) {
		if(is_array($columna_tabla)){
			$objPHPExcel->getActiveSheet()->setCellValue($columna_tabla['cord_columna'].$columna_tabla['cord_fila'],$columna_tabla['valor']);
		}
	}
}

# Hoja de FALTANTES
$objPHPExcel->setActiveSheetIndex(1);
foreach ($documento['Faltantes'] as $m => $motivo) {
	if(is_array($motivo)){
		foreach ($motivo as $ft => $fila_tabla) {
			if(is_array($fila_tabla)){
				foreach ($fila_tabla as $ct => $columna_tabla) {
					if(is_array($columna_tabla)){
						$objPHPExcel->getActiveSheet()->setCellValueExplicit($columna_tabla['cord_columna'].$columna_tabla['cord_fila'],$columna_tabla['valor'],PHPExcel_Cell_DataType::TYPE_STRING);
						//$objPHPExcel->getActiveSheet()->setCellValue($columna_tabla['cord_columna'].$columna_tabla['cord_fila'],$columna_tabla['valor']);
					}
				}
			}		
		}
	}
}

# Hoja de OBSERVACIONES
$objPHPExcel->setActiveSheetIndex(2);
foreach ($documento['Observaciones'] as $m => $motivo) {
	if(is_array($motivo)){
		foreach ($motivo as $ft => $fila_tabla) {
			if(is_array($fila_tabla)){
				foreach ($fila_tabla as $ct => $columna_tabla) {
					if(is_array($columna_tabla)){
						$objPHPExcel->getActiveSheet()->setCellValue($columna_tabla['cord_columna'].$columna_tabla['cord_fila'],$columna_tabla['valor']);
					}
				}
			}		
		}
	}
}

# Hoja de Duplicaciones
$objPHPExcel->setActiveSheetIndex(3);
foreach ($documento['Repeticiones'] as $m => $motivo) {
	if(is_array($motivo)){
		foreach ($motivo as $ft => $fila_tabla) {
			if(is_array($fila_tabla)){
				foreach ($fila_tabla as $ct => $columna_tabla) {
					if(is_array($columna_tabla)){
						$objPHPExcel->getActiveSheet()->setCellValue($columna_tabla['cord_columna'].$columna_tabla['cord_fila'],$columna_tabla['valor']);
					}
				}
			}
			
		}
	}
}

/**
* AL FINALIZAR, SETEAR LA HOJA INICIAL EN LA HOJA RESUMEN
*/
$objPHPExcel->setActiveSheetIndex(0);

/*

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
$objPHPExcel->getActiveSheet()->getStyle('P'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('V'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AD'.$row)->applyFromArray($styleArray);
*/

# PORCION DE DIAGNOSTICO DE INFORMATICA, ESCRIBIR EN LA PETICIÓN debug_documento O debug_documento=json(FIREFOX)
	if (isset($f->request->data['debug_documento'])) {
		$debug=array(
			'documento_inicial'=>$documento_inicial,
			'documento_final'=>$documento,
		);
		if($f->request->data['debug_documento']==="json"){
			header("Content-type:application/json");	
			echo json_encode($debug);
			die();
		}else{
			echo "<pre>";
			print_r($debug);
			echo "</pre>";
			die();
		}
	}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte de Continuidad de comprobantes '.$params['fecini'].'-'.$params['fecfin'].'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
$objWriter->save('php://output');
?>