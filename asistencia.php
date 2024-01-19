<?php
/*
 * Author: fnaquira
 * Last Update: 2013-12-05
 * 
 * Observaciones
 * -Este script tiene que programarse para el mismo dia, es decir, iniciar despues de las 0:00am
 */
$con = new Mongo("localhost"); // Connect to Mongo Server
$db = $con->selectDB('beneficencia');
/*
 * Bases de datos
 */
$db_asistencia = $db->pe_asistencia;
$db_turnos = $db->pe_turnos;
$db_feriados = $db->pe_feriados;
$db_trabajadores = $db->mg_entidades;

$feriado = $db_feriados->findOne(array('fec'=>
	array(
		'$gte'=>new MongoDate(strtotime(date('Y-m-d')." 00:00:00")),
		'$lt'=>new MongoDate(strtotime(date('Y-m-d',strtotime(date("Y-m-d")." +1 day"))." 00:00:00"))
	)
));
/*
 * Si hoy dia es un feriado, no ejecutamos nada
 */
if($feriado==null){
	/*
	 * Se obtiene todos los trabajadores y todos los feriados y comenzamos a comparar
	 */
	$trabajadores = $db_trabajadores->find(array('roles.trabajador.estado'=>'H'));
	foreach($trabajadores as $trabajador){
		/*
		 * Si el trabajador tiene asistencia, no hay mas que hacer
		 */
		$asistencia = $db_asistencia->findOne(array('trabajador._id'=>$trabajador['_id'],'fec'=>
			array(
				'$gte'=>new MongoDate(strtotime(date('Y-m-d')." 00:00:00")),
				'$lt'=>new MongoDate(strtotime(date('Y-m-d',strtotime(date("Y-m-d")." +1 day"))." 00:00:00"))
			)
		));
		if($asistencia==null){
			/*
			 * Verificamos que el trabajador tenga un turno asignado
			 */
			if(isset($trabajador['roles']['trabajador']['turno'])){
				$turno = $db_turnos->findOne(array('_id'=>$trabajador['roles']['trabajador']['turno']['_id']));
				$trab_rel = array(
					'_id'=>$trabajador['_id'],
					'nomb'=>$trabajador['nomb'],
					'tipo_enti'=>$trabajador['tipo_enti']
				);
				if($trab_rel['tipo_enti']=='P'){
					$trab_rel['appat'] = $trabajador['appat'];
					$trab_rel['apmat'] = $trabajador['apmat'];
				}
				if($turno!=null){
					$dia_semana = date('w');
					foreach ($turno['dias'] as $tmp){
						if($tmp['dia']==$dia_semana){
							$asis_save = array(
								'fec'=>new MongoDate(),
								'trabajador'=>$trab_rel,
								'programado'=>array(
									'inicio'=>new MongoDate(strtotime(date('Y-m-d')." ".$tmp['horas']['ini'].":00")),
									'fin'=>new MongoDate(strtotime(date('Y-m-d')." ".$tmp['horas']['fin'].":00"))
								)
							);
							$db_asistencia->insert($asis_save);
						}
					}
				}
			}
		}
	}	
}
?>