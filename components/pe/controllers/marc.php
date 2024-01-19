<?php
class Controller_pe_marc extends Controller {
	function execute_export_marcaciones(){
		global $f;
		$marcaciones = $f->datastore->pe_marcaciones->find();
		echo '<table>';
		foreach ($marcaciones as $ob){
		     echo '<tr>';
		     echo '<td>'.$ob['_id']->{'$id'}.'</td>';
		     echo '<td>'.$ob['tarjeta'].'</td>';
		     echo '<td>'.date('Y-m-d H:i:s',$ob['fecreg']->sec).'</td>';
		     echo '</tr>';
		}
		echo '</table>';
	}
	function execute_index() {
		global $f;
		//echo "script de insercion de marcaciones";
		$data = $f->request->data;
		$c = $f->datastore->marcaciones;
		$c->insert($data);
		//die();
		foreach($data['equipo'] as $i=>$cod){
			$this->save_marc(array(
				'empleado'=>$data['empleado'][$i],
				'fecha'=>$data['fecha'][$i]
			),$cod);
		}
		$f->response->print("true");
	}
	function save_marc($data,$cod){
		global $f;
		/*
		 * Primero obtenemos el equipo de marcado
		 */
		$equipo = $f->model('pe/equi')->params(array('cod'=>$cod))->get('cod')->items;
		/*
		 * Obtenemos el trabajador
		 */
		$trabajador = $f->model('mg/entidad')->params(array('cod'=>$data['empleado']))->get('cod_trabajador')->items;
		$f->library('helpers');
		$helper=new helper();
		$trabDB = $helper->getEntiDbRel($trabajador);
		/*
		 * Armamos la marcacion a guardar
		 */
		$marcacion = array(
			'fecreg'=>new MongoDate(strtotime($data['fecha'])),
			'tarjeta'=>$data['empleado'],
			'trabajador'=>$trabDB,
			'equipo'=>array(
				'_id'=>$equipo['_id'],
				'cod'=>$equipo['cod'],
				'nomb'=>$equipo['nomb'],
				'local'=>$equipo['local']
			)
		);
		/*
		 * Obtenemos las asistencias programadas del dia
		 */
		$asist_dia = $f->model("pe/asis")->params(array(
			'trab'=>$trabDB['_id'],
			'day'=>substr($data['fecha'], 0, 10)
		))->get("trab_day_all")->items;
		$asis = array();
		if($asist_dia==null){
			$marcacion['tipo'] = 'E';
			$asis = array(
				'trabajador'=>$trabDB,
				'fec'=>new MongoDate(),
				'ejecutado'=>array()
			);
		}else{
			$asis['trabajador'] = $trabDB;
			for($ii=0; $ii<sizeof($asist_dia); $ii++){
				if(isset($asist_dia[$ii]['ejecutado'])){
					if(!isset($asist_dia[$ii]['ejecutado']['salida'])){
						$marcacion['tipo'] = 'S';
						$asis = $asist_dia[$ii];
						$ii = sizeof($asist_dia);
					}
				}else{
					$marcacion['tipo'] = 'E';
					$asis = $asist_dia[$ii];
					$ii = sizeof($asist_dia);
				}
			}
		}
		if(!isset($asis['programado'])){
			if(isset($trabajador['roles']['trabajador']['turno'])){
				$programacion = $this->programar_dia($marcacion['trabajador']['_id'],$marcacion['fecreg'],$trabajador['roles']['trabajador']['turno']['_id']);
				if($programacion!=false){
					$asis['programado'] = $programacion;
				}
			}
		}
		$marcacion = $f->model('pe/marc')->params(array('data'=>$marcacion))->save('insert')->items;
		if(isset($asis['ejecutado']['entrada'])){
			$asis['ejecutado']['salida'] = array(
				'_id'=>$marcacion['_id'],
				'fecreg'=>$marcacion['fecreg'],
				'equipo'=>$marcacion['equipo']
			);
			$asis['ejecutado']['tiempo'] = ($asis['ejecutado']['salida']['fecreg']->sec - $asis['ejecutado']['entrada']['fecreg']->sec)/60;
		}else{
			$asis['ejecutado']['entrada'] = array(
				'_id'=>$marcacion['_id'],
				'fecreg'=>$marcacion['fecreg'],
				'equipo'=>$marcacion['equipo']
			);
		}
		if(isset($asis['_id']))
			$f->model('pe/asis')->params(array('_id'=>$asis['_id'],'data'=>$asis))->save('update');
		else
			$f->model('pe/asis')->params(array('data'=>$asis))->save('insert');
		if(isset($asis['ejecutado']['salida'])){
			$this->clasificar($asis);
		}
	}
	function chancar_equipo_marcaciones(){
		global $f;
		$equipo = array(
			"_id"=>new MongoId("52a0b027a4b5c3600900005d"),
			"cod"=>"192.168.3.30",
			"nomb"=>"Equipo de Administracion Central 01",
			"local"=>array(
				"_id"=>new MongoId("519d35d29c7684f0050000c2"),
				"descr"=>"Administracion Central",
				"direccion"=>"Municipalidad de Yarabamba, Yarabamba, Arequipa"
			)
		);
		$marcaciones = $f->datastore->pe_marcaciones->find( array('equipo.nomb'=>null) );
		foreach ($marcaciones as $ob){
		     $f->datastore->pe_marcaciones->update( array('_id'=>$ob['_id']) , array('$set'=>array('equipo'=>$equipo)) );
		}
	}
	function chancar_equipo_asistencia(){
		global $f;
		$equipo = array(
			"_id"=>new MongoId("52a0b027a4b5c3600900005d"),
			"cod"=>"192.168.3.30",
			"nomb"=>"Equipo de Administracion Central 01",
			"local"=>array(
				"_id"=>new MongoId("519d35d29c7684f0050000c2"),
				"descr"=>"Administracion Central",
				"direccion"=>"Municipalidad de Yarabamba, Yarabamba, Arequipa"
			)
		);
		$marcaciones = $f->datastore->pe_asistencia->find();
		foreach ($marcaciones as $ob){
			if(isset($ob['ejecutado']['entrada'])){
				if($ob['ejecutado']['entrada']['equipo']['nomb']==null){
					$f->datastore->pe_asistencia->update( array('_id'=>$ob['_id']) , array('$set'=>array('ejecutado.entrada.equipo'=>$equipo)) );
				}
			}
			if(isset($ob['ejecutado']['salida'])){
				if($ob['ejecutado']['salida']['equipo']['nomb']==null){
					$f->datastore->pe_asistencia->update( array('_id'=>$ob['_id']) , array('$set'=>array('ejecutado.salida.equipo'=>$equipo)) );
				}
			}
		}
	}
	function programar_dia($trab,$dia,$turno){
		global $f;
		$manana = 60*60*24;
		$feriado = $f->datastore->pe_feriados->findOne(array('fec'=>array(
				'$gte'=>new MongoDate(strtotime(date('Y-m-d',$dia->sec)." 00:00:00")),
				'$lt'=>new MongoDate(strtotime(date('Y-m-d',$dia->sec+$manana)." 00:00:00"))
			)
		));
		/*
		 * Si hoy dia es un feriado, no ejecutamos nada
		 */
		if($feriado==null){
			/*
			 * Si el trabajador tiene asistencia, no hay mas que hacer
			 */
			$asistencia = $f->datastore->pe_asistencia->findOne(array('trabajador._id'=>$trab,'fec'=>array(
					'$gte'=>new MongoDate(strtotime(date('Y-m-d',$dia->sec)." 00:00:00")),
					'$lt'=>new MongoDate(strtotime(date('Y-m-d',$dia->sec+$manana)." 00:00:00"))
				)
			));
			if($asistencia==null){
				/*
				 * Verificamos que el trabajador tenga un turno asignado
				 */
				$turno = $f->datastore->pe_turnos->findOne(array('_id'=>$turno));
				if($turno!=null){
					$dia_semana = date('w',$dia->sec);
					foreach ($turno['dias'] as $tmp){
						if($tmp['dia']==$dia_semana){
							return array(
								'inicio'=>new MongoDate(strtotime(date('Y-m-d',$dia->sec)." ".$tmp['horas']['ini'].":00")),
								'fin'=>new MongoDate(strtotime(date('Y-m-d',$dia->sec)." ".$tmp['horas']['fin'].":00"))
							);
						}
					}
				}
			}
		}
		return false;
	}
	function clasificar($asig){
		global $f;
		$manana = 60*60*24;
		$start = new MongoDate(strtotime(date('Y-m-d',$asig['ejecutado']['entrada']['fecreg']->sec)." 00:00:00"));
		$end = new MongoDate(strtotime(date('Y-m-d',$asig['ejecutado']['entrada']['fecreg']->sec+$manana)." 00:00:00"));
		$inci = $f->model("pe/inci")->params(array(
			"_id"=>$asig['trabajador']['_id'],
			'ini'=>$start,
			'fin'=>$end
		))->get("trab_periodo");
		$data = array('asig'=>array($asig),'inci'=>$inci->items);
		$total['asig'] = array();
		$total['inci'] = array();
		$total['dias'] = array();
		for($i=0; $i<sizeof($data['asig']); $i++){
			if(isset($data['asig'][$i]['programado'])){
				$daytmp = date("Y-m-d",$data['asig'][$i]['programado']['inicio']->sec);
			}else{
				$daytmp = date("Y-m-d",$data['asig'][$i]['ejecutado']['entrada']['fecreg']->sec);
			}
			if(!isset($total['dias'][$daytmp]))
				$total['dias'][$daytmp] = array(
					'prog'=>array(),
					'asis'=>array()
				);
			if(isset($data['asig'][$i]['programado'])){
				$total['dias'][$daytmp]['prog'][] = array(
					'inicio'=>$data['asig'][$i]['programado']['inicio'],
					'fin'=>$data['asig'][$i]['programado']['fin']
				);
			}
			if(isset($data['asig'][$i]['ejecutado'])){
				$tmp_event = array(
					'allDay'=>false,
					'start'=>$data['asig'][$i]['ejecutado']['entrada']['fecreg'],
					//'data'=>$data[$i]
				);
				$eje = array(
					'entrada'=>$data['asig'][$i]['ejecutado']['entrada']['fecreg']
				);
				if(isset($data['asig'][$i]['ejecutado']['salida']))
					$eje['salida'] = $data['asig'][$i]['ejecutado']['salida']['fecreg'];
				$total['dias'][$daytmp]['asis'][] = $eje;
				if(!isset($data['asig'][$i]['ejecutado']['salida'])) $color = 'gray';
				else $color = 'blue';
				if(isset($data['asig'][$i]['ejecutado']['salida'])){
					$fin = $data['asig'][$i]['ejecutado']['salida']['fecreg'];
					$title = $data['asig'][$i]['ejecutado']['entrada']['equipo']['local']['descr']+' ('+$data['asig'][$i]['ejecutado']['entrada']['equipo']['cod']+' - '+$data['asig'][$i]['ejecutado']['salida']['equipo']['cod']+')';
				}else if(date("Y-m-d",$data['asig'][$i]['ejecutado']['entrada']['fecreg'])!=date('Y-m-d')){
					$ini = $data['asig'][$i]['ejecutado']['entrada']['fecreg'];
					$fin = new MongoDate($ini->sec + (3600 * 1000));
					$title = 'Sin Cierre';
					$tmp_event['edit'] = true;
				}else{
					$fin = date('Y-m-d');
					$title = $data['asig'][$i]['ejecutado']['entrada']['equipo']['local']['descr']+' ('+$data['asig'][$i]['ejecutado']['entrada']['equipo']['cod']+')';
				}
				if(isset($data['asig'][$i]['manual'])){
					$tmp_event['edit'] = true;
					$tmp_event['remove'] = true;
				}else{
					$tmp_event['edit'] = false;
					$tmp_event['remove'] = false;
				}
				$tmp_event['title'] = $title;
				$tmp_event['end'] = $fin;
				$tmp_event['backgroundColor'] = $color;
				$total['asig'][] = $tmp_event;
			}
		}
		if(isset($data['inci'])){
			for($i=0; $i<sizeof($data['inci']); $i++){
				$incidencia = array(
					'title'=>$data['inci'][$i]['tipo']['nomb'],
					'allDay'=>false,
					'start'=>$data['inci'][$i]['fecini'],
					'end'=>$data['inci'][$i]['fecfin'],
					'backgroundColor'=>'green',
					'tipo'=>'R',
					'mid'=>$data['inci'][$i]['_id'],
					'data'=>$data['inci'][$i]
				);
				$total['inci'][] = $incidencia;
				$total['asig'][] = $incidencia;
			}
		}
		//print_r($total['dias']);die();
		$tmp_totdias = (floatval(($end->sec-$start->sec))/60/60/24);
		for($i=0; $i<$tmp_totdias; $i++){
			$dia = new MongoDate($start->sec + ($i * 24 * 3600));
			$diaForm = date("Y-m-d",$dia->sec);
			//echo 'DIA <=> '.$diaForm;die();
			if(isset($total['dias'][$diaForm])){
				$tmp = $total['dias'][$diaForm];
				if(sizeof($tmp['asis'])<1){
					for($j=0; $j<sizeof($tmp['prog']); $j++){
						$this->check(array(
							'title'=>'Sin clasificar - Inasistencia',
							'allDay'=>false,
							'start'=>$tmp['prog'][$j]['inicio'],
							'end'=>$tmp['prog'][$j]['fin'],
							'trabajador'=>$asig['trabajador'],
							'total_inci'=>$total['inci']
						));
					}
				}else{
					if(sizeof($tmp['prog'])<1){
						for($j=0; $j<sizeof($tmp['asis']); $j++){
	    					$this->check(array(
								'title'=>'Sin clasificar - Tiempo extra',
								'allDay'=>false,
								'start'=>$tmp['asis'][$j]['entrada'],
								'end'=>$tmp['asis'][$j]['salida'],
								'trabajador'=>$asig['trabajador'],
								'total_inci'=>$total['inci']
							));
	    				}
					}
					for($j=0; $j<sizeof($tmp['asis']); $j++){
						$asis = $tmp['asis'][$j];
						for($k=0; $k<sizeof($tmp['prog']); $k++){
							switch($this->compare($asis['entrada'],$tmp['prog'][$k]['inicio'])){
								case 0:
									if(isset($asis['salida'])){
										if($this->compare($asis['salida'],$tmp['prog'][$k]['fin'])==1){
			        						if($k<(sizeof($tmp['prog'])-1)){
	        									if($this->compare($tmp['prog'][$k+1]['inicio'],$asis['salida'])==1){
						        					$this->check(array(
														'title'=>'EXC',
														'allDay'=>false,
														'end'=>$tmp['prog'][$k+1]['inicio'],
														'start'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
						        					if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==1){
							        					$this->check(array(
															'title'=>'SAT',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['prog'][$k+1]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
						        					}else if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==-1){
						        						$this->check(array(
															'title'=>'EXC',
															'allDay'=>false,
															'end'=>$asis['salida'],
															'start'=>$tmp['prog'][$k+1]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
						        					}
						        					$tmp['prog'] = array_splice($tmp['prog'],$k,1);
	        									}else if($this->compare($tmp['prog'][$k+1]['inicio'],$asis['salida'])==-1){
	        										$this->check(array(
														'title'=>'EXC',
														'allDay'=>false,
														'end'=>$asis['salida'],
														'start'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
	        									}
			        						}else{
					        					$this->check(array(
													'title'=>'EXC',
													'allDay'=>false,
													'end'=>$asis['salida'],
													'start'=>$tmp['prog'][$k]['fin'],
													'trabajador'=>$asig['trabajador'],
													'total_inci'=>$total['inci']
												));
			        						}
				        					$k = sizeof($tmp['prog']);
										}else if($this->compare($asis['salida'],$tmp['prog'][$k]['fin'])==-1){
											if($j<(sizeof($tmp['asis'])-1)){
	        									if($this->compare($tmp['prog'][$k]['fin'],$tmp['asis'][$j+1]['entrada'])==1){
				        							$this->check(array(
														'title'=>'NOT',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['asis'][$j+1]['entrada'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
	        									}else{
				        							$this->check(array(
														'title'=>'SAT',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
	        									}
			        						}else{
			        							$this->check(array(
													'title'=>'SAT',
													'allDay'=>false,
													'start'=>$asis['salida'],
													'end'=>$tmp['prog'][$k]['fin'],
													'trabajador'=>$asig['trabajador'],
													'total_inci'=>$total['inci']
												));
			        						}
				        					$k = sizeof($tmp['prog']);
										}
									}else{
			        					$this->check(array(
											'title'=>'SIC',
											'allDay'=>false,
											'start'=>$asis['entrada'],
											'end'=>new MongoDate($asis['entrada']->sec + (3600 * 1000)),
											'trabajador'=>$asig['trabajador'],
											'total_inci'=>$total['inci']
										));
									}
									break;
								case 1:
									if($asis['salida']!=null){
										if($this->compare($asis['salida'],$tmp['prog'][$k]['inicio'])==1){
				        					$this->check(array(
												'title'=>'TAR',
												'allDay'=>false,
												'end'=>$asis['entrada'],
												'start'=>$tmp['prog'][$k]['inicio'],
												'trabajador'=>$asig['trabajador'],
												'total_inci'=>$total['inci']
											));
				        					if($this->inRange($asis['salida'],$tmp['prog'][$k]['inicio'],$tmp['prog'][$k]['fin'])==false){
				        						if($k<(sizeof($tmp['prog'])-1)){
				        							if($this->compare($asis['salida'],$tmp['prog'][$k+1]['inicio'])==1){
				        								$this->check(array(
															'title'=>'EXC',
															'allDay'=>false,
															'end'=>$tmp['prog'][$k+1]['inicio'],
															'start'=>$tmp['prog'][$k]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
							        					if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==1){
								        					$this->check(array(
																'title'=>'SAT',
																'allDay'=>false,
																'start'=>$asis['salida'],
																'end'=>$tmp['prog'][$k+1]['fin'],
																'trabajador'=>$asig['trabajador'],
																'total_inci'=>$total['inci']
															));
							        					}else if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==-1){
							        						$this->check(array(
																'title'=>'EXC',
																'allDay'=>false,
																'end'=>$asis['salida'],
																'start'=>$tmp['prog'][$k+1]['fin'],
																'trabajador'=>$asig['trabajador'],
																'total_inci'=>$total['inci']
															));
							        					}
							        					$tmp['prog'] = array_splice($tmp['prog'],$k,1);
		        									}else{
		        										$this->check(array(
															'title'=>'EXC',
															'allDay'=>false,
															'end'=>$asis['salida'],
															'start'=>$tmp['prog'][$k]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
		        									}
				        						}else{
						        					$this->check(array(
														'title'=>'EXC',
														'allDay'=>false,
														'end'=>$asis['salida'],
														'start'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
				        						}
					        					$tmp['prog'] = array_splice($tmp['prog'],$k,1);
				        					}else{
				        						if($j<(sizeof($tmp['asis'])-1)){
				        							if($this->compare($tmp['prog'][$k]['fin'],$tmp['asis'][$j+1]['entrada'])==1){
					        							$this->check(array(
															'title'=>'NOT',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['asis'][$j+1]['entrada'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
		        									}else{
					        							$this->check(array(
															'title'=>'SAT',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['prog'][$k]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
		        									}
				        						}else{
						        					$this->check(array(
														'title'=>'SAT',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
				        						}
				        					}
				        					$k = sizeof($tmp['prog']);
										}else{
				        					$this->check(array(
												'title'=>'HOE',
												'allDay'=>false,
												'end'=>$asis['salida'],
												'start'=>$asis['entrada'],
												'trabajador'=>$asig['trabajador'],
												'total_inci'=>$total['inci']
											));
				        					$k = sizeof($tmp['prog']);
										}
									}else{
			        					$this->check(array(
											'title'=>'SIC',
											'allDay'=>false,
											'start'=>$asis['entrada'],
											'end'=>new MongoDate($asis['entrada']->sec + (3600 * 1000)),
											'trabajador'=>$asig['trabajador'],
											'total_inci'=>$total['inci']
										));
									}
									break;
								case -1:
									if($asis['salida']!=null){
										if($this->compare($asis['salida'],$tmp['prog'][$k]['inicio'])==1){
				        					$this->check(array(
												'title'=>'ADE',
												'allDay'=>false,
												'start'=>$asis['entrada'],
												'end'=>$tmp['prog'][$k]['inicio'],
												'trabajador'=>$asig['trabajador'],
												'total_inci'=>$total['inci']
											));
				        					if($this->inRange($asis['salida'],$tmp['prog'][$k]['inicio'],$tmp['prog'][$k]['fin'])==false){
				        						if($k<(sizeof($tmp['prog'])-1)){
				        							if($this->compare($asis['salida'],$tmp['prog'][$k+1]['inicio'])==1){
				        								$this->check(array(
															'title'=>'EXC',
															'allDay'=>false,
															'end'=>$tmp['prog'][$k+1]['inicio'],
															'start'=>$tmp['prog'][$k]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
							        					$k++;
							        					if($this->compare($tmp['prog'][$k]['fin'],$asis['salida'])==1){
								        					$this->check(array(
																'title'=>'SAT',
																'allDay'=>false,
																'start'=>$asis['salida'],
																'end'=>$tmp['prog'][$k]['fin'],
																'trabajador'=>$asig['trabajador'],
																'total_inci'=>$total['inci']
															));
							        					}else if($this->compare($tmp['prog'][$k]['fin'],$asis['entrada'])==-1){
							        						$this->check(array(
																'title'=>'EXC',
																'allDay'=>false,
																'end'=>$asis['salida'],
																'start'=>$tmp['prog'][$k]['fin'],
																'trabajador'=>$asig['trabajador'],
																'total_inci'=>$total['inci']
															));
							        					}
							        					$k--;
							        					$tmp['prog'] = array_splice($tmp['prog'],$k,1);
		        									}else{
		        										$this->check(array(
															'title'=>'EXC',
															'allDay'=>false,
															'end'=>$asis['salida'],
															'start'=>$tmp['prog'][$k]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
		        									}
				        						}else{
						        					$this->check(array(
														'title'=>'EXC',
														'allDay'=>false,
														'end'=>$asis['salida'],
														'start'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
				        						}
				        					}else{
				        						if($j<(sizeof($tmp['asis'])-1)){
				        							if($this->compare($tmp['prog'][$k]['fin'],$tmp['asis'][$j+1]['entrada'])==1){
					        							$this->check(array(
															'title'=>'NOT',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['asis'][$j+1]['entrada'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
		        									}else{
					        							$this->check(array(
															'title'=>'SAT',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['prog'][$k]['fin'],
															'trabajador'=>$asig['trabajador'],
															'total_inci'=>$total['inci']
														));
		        									}
				        						}else{
						        					$this->check(array(
														'title'=>'SAT',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['prog'][$k]['fin'],
														'trabajador'=>$asig['trabajador'],
														'total_inci'=>$total['inci']
													));
				        						}
				        					}
				        					$k = sizeof($tmp['prog']);
										}else{
				        					$this->check(array(
												'title'=>'HOE',
												'allDay'=>false,
												'end'=>$asis['salida'],
												'start'=>$asis['entrada'],
												'trabajador'=>$asig['trabajador'],
												'total_inci'=>$total['inci']
											));
				        					$k = sizeof($tmp['prog']);
										}
									}else{
			        					$this->check(array(
											'title'=>'SIC',
											'allDay'=>false,
											'start'=>$asis['entrada'],
											'end'=>new MongoDate($asis['entrada']->sec + (3600 * 1000)),
											'trabajador'=>$asig['trabajador'],
											'total_inci'=>$total['inci']
										));
									}
									break;
							}
						}
					}
				}
			}
		}
	}
	function compare($a,$b){
		$result = $a->sec - $b->sec;
		if($result==0){
			return 0;
		}elseif($result>0){
			return 1;
		}else{
			return -1;
		}
	}
	function inRange($marc,$ini,$fin){
		global $f;
		if($marc->sec>$ini->sec && $marc->sec<$fin->sec){
			return true;
		}else{
			return false;
		}
	}
	function check($inci){
		global $f;
		$total_inci = $inci['total_inci'];
		if($inci['start']!=null&&$inci['end']!=null){
			if(date('Y-m-d H:i',$inci['start']->sec)!=date('Y-m-d H:i',$inci['end']->sec)){
				if(sizeof($total_inci)>0){
					$sectmp = true;
					for($ii=0; $ii<sizeof($total_inci); $ii++){
						if(date('Y-m-d H:i',$inci['start']->sec)==date('Y-m-d H:i',$total_inci[$ii]['start']->sec)&&date('Y-m-d H:i',$inci['end']->sec)==date('Y-m-d H:i',$total_inci[$ii]['end']->sec)){
							$sectmp = false;
							$ii = sizeof($total_inci);
						}
					}
					if($sectmp==true) $this->save_inci($inci);
				}else $this->save_inci($inci);
			}
		}
	}
	function save_inci($inci){
		global $f;
		$tipo = array();
		/*
		EXC exceso
		SAT salida temprana
		SIC sin cerrar
		HOE horas extra
		ADE adelanto

		TAR tardanza
		NOT no trabajado
		*/
		switch($inci['title']){
			case 'TAR':
				$tipo = array(
					'_id'=>new MongoId('52b33254a4b5c3d40c000030'),
					'nomb'=>'TARDANZA',
					'tipo'=>'TA',
					'goce_haber'=>false,
					'subsidiado'=>false
				);
				break;
			case 'NOT':
				$tipo = array(
					'_id'=>new MongoId('52b33213a4b5c3d40c00002f'),
					'nomb'=>'INASISTENCIAS',
					'tipo'=>'IN',
					'goce_haber'=>false,
					'subsidiado'=>false
				);
				break;
			case 'SAT':
				$tipo = array(
					'_id'=>new MongoId('52b33213a4b5c3d40c00002f'),
					'nomb'=>'INASISTENCIAS',
					'tipo'=>'IN',
					'goce_haber'=>false,
					'subsidiado'=>false
				);
				break;
		}
		if(sizeof($tipo)>0){
			$data = array(
				'fecini'=>$inci['start'],
				'fecfin'=>$inci['end'],
				'trabajador'=>$inci['trabajador'],
				'tipo'=>$tipo,
				'observ'=>'',
				'fecreg'=>new MongoDate(),
				'programada'=>false
			);
			$f->datastore->pe_incidencias->insert($data);
		}
	}
	function execute_ultimo_dia(){
		global $f;
		set_time_limit(0);
		$f->library('helpers');
		$helper = new helper();
		
		/*$content = file_get_contents('http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias');
		$content = $helper->get_string_between($content, "<table class=class=\"form-table\" border='1' cellpadding='0' cellspacing='0' width='81%' >", "</table>");
		$content = str_replace(' ', '', $content);
		$content = preg_replace('/\s+/', '', $content);
		$content = substr($content, strlen($content)-10,5);
		$f->model('mg/vari')->params(array(
			'filter'=>array('cod'=>'TC'),
			'data'=>array(
				'$set'=>array('valor'=>$content,'fecmod'=>new MongoDate()),
				'$push'=>array('historico'=>array('valor'=>$content,'fecreg'=>new MongoDate()))
			)
		))->save('update');*/
		$data = $f->datastore->pe_config->find()->sort(array('fec'=>1));
		foreach ($data as $key => $item) {
			$dia = $item;
		}
		$diferencia = -1;
		$dia = $dia['fec'];
		if($dia!=date('Y-m-d')){
			$seconds = strtotime(date('Y-m-d'))-strtotime($dia);
			$diferencia = $seconds/60/60/24;
			if($diferencia>0){
				$trabajadores_turno = $f->datastore->mg_entidades->find(array(
					'roles.trabajador.turno'=>array('$exists'=>true),
					'roles.trabajador.estado'=>'H'
				),array(
					'nomb'=>true,
					'appat'=>true,
					'apmat'=>true,
					'fullname'=>true,
					'tipo_enti'=>true,
					'docident'=>true,
					'roles.trabajador.cargo'=>true,
					'roles.trabajador.programa'=>true,
					'roles.trabajador.turno._id'=>true
				));
				$f->library('helpers');
				$helper=new helper();
				for($i=1; $i<=$diferencia; $i++){
					$tmp = strtotime($dia.' +'.$i.' days');
					foreach ($trabajadores_turno as $j=>$trab) {
						$asis = array(
							'trabajador'=>$helper->getEntiDbRel($trab),
							'fec'=>new MongoDate(),
							'programado'=>$this->programar_dia($trab,new MongoDate($tmp),$trab['roles']['trabajador']['turno']['_id'])
						);
						$f->datastore->pe_asistencia->insert($asis);
					}
				}
				$f->datastore->pe_config->insert(array('fec'=>date('Y-m-d')));
			}
		}
		$f->response->json(array('dias'=>$diferencia));
		
	}
	function execute_reset_marc(){
		global $f;
		set_time_limit(0);
		$f->datastore->pe_asistencia->update(array('ejecutado'=>array('$exists'=>true)),array('$unset'=>array('ejecutado'=>true)));
		$marcaciones = $f->datastore->pe_marcaciones->find(array('fecreg'=>array('$gte'=>new MongoDate(strtotime('2017-01-01'))),'reset'=>array('$exists'=>false)))->sort(array('fecreg'=>1));
		foreach($marcaciones as $i=>$marc){
			print_r($marc);
			echo "<br />";
			if($i==2){
				die();
			}
			/*
			 * Obtenemos las asistencias programadas del dia
			 */
			$asist_dia = $f->model("pe/asis")->params(array(
				'trab'=>$marc['trabajador']['_id'],
				'day'=>substr(date('Y-m-d',$marc['fecreg']->sec), 0, 10)
			))->get("trab_day_all")->items;
			$asis = array(
				'trabajador'=>$marc['trabajador'],
				'fec'=>new MongoDate(),
				'ejecutado'=>array()
			);
			if($asist_dia!=null){
				for($ii=0; $ii<sizeof($asist_dia); $ii++){
					if(isset($asist_dia[$ii]['ejecutado'])){
						if(!isset($asist_dia[$ii]['ejecutado']['salida'])){
							$asis = $asist_dia[$ii];
							$ii = sizeof($asist_dia);
						}
					}else{
						$asis = $asist_dia[$ii];
						$ii = sizeof($asist_dia);
					}
				}
			}
			if(isset($asis['ejecutado']['entrada'])){
				$asis['ejecutado']['salida'] = array(
					'_id'=>$marc['_id'],
					'fecreg'=>$marc['fecreg'],
					'equipo'=>$marc['equipo']
				);
				$asis['ejecutado']['tiempo'] = ($asis['ejecutado']['salida']['fecreg']->sec - $asis['ejecutado']['entrada']['fecreg']->sec)/60;
			}else{
				$asis['ejecutado']['entrada'] = array(
					'_id'=>$marc['_id'],
					'fecreg'=>$marc['fecreg'],
					'equipo'=>$marc['equipo']
				);
			}
			$tmp_lock = $f->datastore->pe_asistencia->findOne(array(
				'trabajador._id'=>$asis['trabajador']['_id'],
				'$or'=>array(
					array('ejecutado.entrada.fecreg'=>$marc['fecreg']),
					array('ejecutado.salida.fecreg'=>$marc['fecreg'])
				)
			));
			if($tmp_lock==null){
				if(isset($asis['_id']))
					$f->model('pe/asis')->params(array('_id'=>$asis['_id'],'data'=>$asis))->save('update');
				else
					$f->model('pe/asis')->params(array('data'=>$asis))->save('insert');
				if(isset($asis['ejecutado']['salida'])){
					$this->clasificar($asis);
				}
				$f->datastore->pe_marcaciones->update(array('_id'=>$marc['_id']),array('$set'=>array('reset'=>true)));
			}
		}
		$f->response->print('true');
	}
	function execute_nuevo(){
		global $f;
		set_time_limit(0);
		$data = array(
);  
		$plus = intval($f->request->data['plus']);
		$cant = 500;
		foreach($data['equipo'] as $i=>$cod){
			if($i>=($plus*$cant)&&$i<(($plus*$cant)+$cant)){
				echo $i.'<br />';
				$this->save_marc(array(
					'empleado'=>$data['empleado'][$i],
					'fecha'=>$data['fecha'][$i]
				),$cod);
			}else if($i==(($plus*$cant)+$cant)){
				$f->response->print('true');
				die();
			}
		}
		$f->response->print("true");
	}
}
?>