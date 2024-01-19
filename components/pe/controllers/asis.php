<?php
class Controller_pe_asis extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("pe/asis.main");
	}
	function execute_get_horario(){
		global $f;
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json(array('turno'=>$turno['dias']));
	}
	function execute_get_asistencia(){
		global $f;
		$asistencia = $f->model("pe/asis")->params(array(
			'enti'=>new MongoId($f->request->data['trabajador']),
			'start'=>new MongoDate(strtotime($f->request->data['start'])),
			'end'=>new MongoDate(strtotime($f->request->data['end']))
		))->get("trab_hor")->items;
		if($asistencia!=null){
			foreach ($asistencia as $i=>$item){
				if(isset($item['nomb'])) $asistencia[$i]['title'] = $item['nomb'];
				else $asistencia[$i]['title'] = '';
				$asistencia[$i]['start'] = date('Y-m-d H:i:s',$item['programado']['inicio']->sec);
				$asistencia[$i]['end'] = date('Y-m-d H:i:s',$item['programado']['fin']->sec);
			}
		}
		$f->response->json($asistencia);
	}
	function execute_get_hora(){
		global $f;
		/* ASISTENCIA */
		$asistencia = $f->model("pe/asis")->params(array(
			'enti'=>new MongoId($f->request->data['trabajador']),
			'start'=>new MongoDate(strtotime($f->request->data['start'])),
			'end'=>new MongoDate(strtotime($f->request->data['end']))
		))->get("trab_hor")->items;
		if($asistencia!=null){
			foreach ($asistencia as $i=>$item){
				if(isset($item['nomb'])) $asistencia[$i]['title'] = $item['nomb'];
				else $asistencia[$i]['title'] = '';
				$asistencia[$i]['start'] = date('Y-m-d H:i:s',$item['programado']['inicio']->sec);
				$asistencia[$i]['end'] = date('Y-m-d H:i:s',$item['programado']['fin']->sec);
			}
		}
		/* FERIADOS */
		if(isset($f->request->data['start'])){
			$params['start'] = new MongoDate(strtotime($f->request->data['start']));
			$params['end'] = new MongoDate(strtotime($f->request->data['end']));
		}
		$feriados = $f->model('pe/feri')->params($params)->get('all')->items;
		if(isset($f->request->data['calendario'])){
			if($feriados!=null){
				foreach ($feriados as $i=>$item) {
					$feriados[$i]['title'] = $item['nomb'];
					$feriados[$i]['start'] = date('Y-m-d',$item['fec']->sec);
				}
			}
		}
		/* HORARIO */
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->data['turno'])))->get("one")->items['dias'];
		/* JSON */
		$f->response->json(array(
			'asistencia'=>$asistencia,
			'feriados'=>$feriados,
			'turno'=>$turno
		));
	}
	function execute_save_hora(){
		global $f;
		$data = $f->request->data;
		$data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['del'])){
			foreach ($data['del'] as $blo){
				$f->model("pe/asis")->params(array('_id'=>new MongoId($blo)))->delete("data");
			}
		}
		if(isset($data['data'])){
			foreach ($data['data'] as $blo){
				$blo['trabajador'] = $data['trabajador'];
				$blo['programado']['inicio'] = new MongoDate(strtotime($blo['programado']['inicio']));
				$blo['programado']['fin'] = new MongoDate(strtotime($blo['programado']['fin']));
				if(!isset($blo['_id'])){
					$f->model("pe/asis")->params(array('data'=>$blo))->save("insert");
				}else{
					$f->model("pe/asis")->params(array('_id'=>new MongoId($blo['_id']),'data'=>$blo))->save("update");
				}
			}
		}
		$model = $f->model("pe/asis")->params(array("enti"=>$data['trabajador']['_id']))->get("trab_horario");
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($data['turno'])))->get("one");
		$f->response->json( array('asig'=>$model->items,'turno'=>$turno->items['dias']) );
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Control de Asistencia: Horarios',
			'descr'=>'Se actualiz&oacute; el <b>Turno Programado</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b>.'
		))->save('insert');
	}
	function execute_save_asis(){
		global $f;
		$data = $f->request->data;
		if(isset($data['_id'])) $data['_id'] = new MongoId($data['_id']);
		if(isset($data['enti']['_id'])) $data['enti']['_id'] = new MongoId($data['enti']['_id']);
		if(isset($data['enti']['cargo']['_id'])) $data['enti']['cargo']['_id'] = new MongoId($data['enti']['cargo']['_id']);
		if(isset($data['enti']['cargo']['organizacion']['_id'])) $data['enti']['cargo']['organizacion']['_id'] = new MongoId($data['enti']['cargo']['organizacion']['_id']);
		if(isset($data['equipo']['_id'])) $data['equipo']['_id'] = new MongoId($data['equipo']['_id']);
		if(isset($data['equipo']['local']['_id'])) $data['equipo']['local']['_id'] = new MongoId($data['equipo']['local']['_id']);
		$mar1 = array(
			'fecreg'=>new MongoDate(strtotime($data['inicio'])),
			'tarjeta'=>$data['tarjeta'],
			'trabajador'=>$data['enti'],
			'tipo'=>'E',
			'equipo'=>$data['equipo']
		);
		$mar1_db = $f->model("pe/marc")->params(array('data'=>$mar1))->save("insert")->obj;
		$mar2 = array(
			'fecreg'=>new MongoDate(strtotime($data['fin'])),
			'tarjeta'=>$data['tarjeta'],
			'trabajador'=>$data['enti'],
			'tipo'=>'S',
			'equipo'=>$data['equipo']
		);
		$mar2_db = $f->model("pe/marc")->params(array('data'=>$mar2))->save("insert")->obj;
		if(isset($f->request->data['_id'])){
			$asis = $f->model("pe/asis")->params(array('_id'=>$data['_id']))->get("one")->items;
			if(isset($asis['ejecutado'])){
				$f->model("pe/marc")->params(array('_id'=>$asis['ejecutado']['entrada']['_id']))->delete("id");
				if(isset($asis['ejecutado']['salida']))
					$f->model("pe/marc")->params(array('_id'=>$asis['ejecutado']['salida']['_id']))->delete("id");
			}
		}else{
			$tmp = $f->model("pe/asis")->params(array(
				'trab'=>$data['enti']['_id'],
				'day'=>substr($data['fin'], 0, 10)
			))->get("trab_day")->items;
			if($tmp==null){
				$asis = array(
					'trabajador'=>$data['enti']
				);
			}else{
				$asis = $tmp[0];
			}
		}
		$dif = ($mar2_db['fecreg']->sec - $mar1_db['fecreg']->sec)/60;
		$asis['ejecutado'] = array(
			'entrada'=>array(
				'_id'=>$mar1_db['_id'],
				'fecreg'=>$mar1_db['fecreg'],
				'equipo'=>$mar1_db['equipo']
			),
			'salida'=>array(
				'_id'=>$mar2_db['_id'],
				'fecreg'=>$mar2_db['fecreg'],
				'equipo'=>$mar2_db['equipo']
			),
			'tiempo'=>$dif
		);
		//print_r($asis);die();
		$asis['manual'] = true;
		if(!isset($asis['_id'])){
			//if(isset($data['manual'])) $asis['manual'] = true;
			$asis = $f->model("pe/asis")->params(array('data'=>$asis))->save("insert")->obj;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Control de Asistencia: Asistencia',
				'descr'=>'Se cre&oacute; la <b>Asistencia Manual</b> del trabajador <b>'.$asis['trabajador']['nomb'].' '.$asis['trabajador']['appat'].' '.$asis['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$asis['ejecutado']['entrada']['fecreg']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$asis['ejecutado']['salida']['fecreg']->sec).'</b>'
			))->save('insert');
		}else{
			$f->model("pe/asis")->params(array('_id'=>$asis['_id'],'data'=>$asis))->save("update")->obj;
			$data = $f->model("pe/asis")->params(array('_id'=>$asis['_id']))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Control de Asistencia: Asistencia',
				'descr'=>'Se actualiz&oacute; la <b>Asistencia Manual</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$data['ejecutado']['entrada']['fecreg']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$data['ejecutado']['salida']['fecreg']->sec).'</b>'
			))->save('insert');
		}
		$f->response->json($asis);
	}
	function execute_save_inci(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fecini'])) $data['fecini'] = new MongoDate(strtotime($data['fecini']));
		if(isset($data['fecfin'])) $data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
		if(isset($data['tipo']['_id'])) $data['tipo']['_id'] = new MongoId($data['tipo']['_id']);
		if(isset($data['tipo']['goce_haber'])){
			if($data['tipo']['goce_haber']=='true') $data['tipo']['goce_haber'] = true;
			else $data['tipo']['goce_haber'] = false;
		}
		if(isset($data['tipo']['subsidiado'])){
			if($data['tipo']['subsidiado']=='true') $data['tipo']['subsidiado'] = true;
			else $data['tipo']['subsidiado'] = false;
		}
		if(isset($data['tipo']['todo'])){
			if($data['tipo']['todo']=='true') $data['tipo']['todo'] = true;
			else $data['tipo']['todo'] = false;
		}
		if(isset($data['trabajador']['_id'])) $data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(!isset($data['_id'])){
			$data['fecreg'] = new MongoDate();
			$inci = $f->model("pe/inci")->params(array('data'=>$data))->save("insert")->obj;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Control de Asistencia: Incidencias',
				'descr'=>'Se clasific&oacute; la <b>Incidencia</b> como <b>'.$data['tipo']['nomb'].'</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$data['fecini']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$data['fecfin']->sec).'</b>'
			))->save('insert');
		}else{
			unset($data['_id']);
			$inci = $f->model("pe/inci")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->obj;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Control de Asistencia: Incidencias',
				'descr'=>'Se actualiz&oacute; la <b>Incidencia</b> como <b>'.$data['tipo']['nomb'].'</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$data['fecini']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$data['fecfin']->sec).'</b>'
			))->save('insert');
		}
		$f->response->json( $inci );
	}
	function execute_delete_asis(){
		global $f;
		$data = $f->model("pe/asis")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model("pe/asis")->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete("data");
		$data['feceli'] = new MongoDate();
		$data['coleccion'] = 'pe_asistencia';
		$data['trabajador_delete'] = $f->session->userDBMin;
		$f->datastore->temp_del->insert($data);
		$f->datastore->pe_asistencia->remove(array('_id'=>$data['_id']));
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Control de Asistencia: Asistencia',
			'descr'=>'Se elimin&oacute; la <b>Asistencia Manual</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$data['ejecutado']['entrada']['fecreg']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$data['ejecutado']['salida']['fecreg']->sec).'</b>'
		))->save('insert');
		$f->response->print(true);
	}
	function execute_delete_inci(){
		global $f;
		$data = $f->model("pe/inci")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$data['feceli'] = new MongoDate();
		$data['coleccion'] = 'pe_incidencias';
		$data['trabajador_delete'] = $f->session->userDBMin;
		$f->datastore->temp_del->insert($data);
		$f->datastore->pe_incidencias->remove(array('_id'=>$data['_id']));
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Control de Asistencia: Incidencias',
			'descr'=>'Se elimin&oacute; la <b>Incidencia</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$data['ejecutado']['entrada']['fecreg']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$data['ejecutado']['salida']['fecreg']->sec).'</b>'
		))->save('insert');
		$f->response->print(true);
	}
	function calcular_minutos_libres(){
		global $f;
		//
	}
	function execute_clasificar(){
		global $f;
		$start = new MongoDate(strtotime($f->request->data['ini']));
		$end = new MongoDate(strtotime($f->request->data['fin']));
		$model = $f->model("pe/asis")->params(array(
			"enti"=>new MongoId($f->request->data['enti']),
			'ini'=>$start,
			'fin'=>$end
		))->get("marc_periodo");
		$inci = $f->model("pe/inci")->params(array(
			"_id"=>new MongoId($f->request->data['enti']),
			'ini'=>$start,
			'fin'=>$end
		))->get("trab_periodo");
		$data = array('asig'=>$model->items,'inci'=>$inci->items);
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
					'data'=>$data[$i]
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
			$diaForm = date("Y-m-d",$dia);
			//echo 'DIA <=> '.$diaForm;die();
			if(isset($total['dias'][$diaForm])){
				$tmp = $total['dias'][$diaForm];
				if(sizeof($tmp['asis'])<1){
					for($j=0; $j<sizeof($tmp['prog']); $j++){
						/*$this->check({
							'title'=>'Sin clasificar - Inasistencia',
							'allDay'=>false,
							'start'=>$tmp['prog'][$j]['inicio'],
							'end'=>$tmp['prog'][$j]['fin'],
							'tipo'=>'I',
							'backgroundColor'=>'red'
						});*/
					}
				}/*else{
					if(sizeof($tmp['prog'])<1){
						for($j=0; $j<sizeof($tmp['asis']); $j++){
	    					$this->check({
								'title'=>'Sin clasificar - Tiempo extra',
								'allDay'=>false,
								'start'=>$tmp['asis'][$j]['entrada'],
								'end'=>$tmp['asis'][$j]['salida'],
								'tipo'=>'I',
								'backgroundColor'=>'red'
							});
	    				}
					}
					for($j=0; $j<sizeof($tmp['asis']); $j++){
						$asis = $tmp['asis'][$j];
						for($k=0; $k<sizeof($tmp['prog']); $k++){
							switch($this->compare($asis['entrada'],$tmp['prog'][$k]['inicio'])){
								case 0:
									if(isset($asis['salida'])){
										if($this->compare($asis['salida'],$tmp['prog'][$k]['fin'])==1){
			        						if(k<(sizeof($tmp['prog'])-1)){
	        									if($this->compare($tmp['prog'][$k+1]['inicio'],$asis['salida'])==1){
						        					$this->check({
														'title'=>'Sin clasificar - Exceso',
														'allDay'=>false,
														'end'=>$tmp['prog'][$k+1]['inicio'],
														'start'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
						        					if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==1){
							        					$this->check({
															'title'=>'Sin clasificar - Salida temprana',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['prog'][$k+1]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
						        					}else if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==-1){
						        						$this->check({
															'title'=>'Sin clasificar - Exceso',
															'allDay'=>false,
															'end'=>$asis['salida'],
															'start'=>$tmp['prog'][$k+1]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
						        					}
						        					$tmp['prog'] = array_splice($tmp['prog'],$k,1);
	        									}else if($this->compare($tmp['prog'][$k+1]['inicio'],$asis['salida'])==-1){
	        										$this->check({
														'title'=>'Sin clasificar - Exceso',
														'allDay'=>false,
														'end'=>$asis['salida'],
														'start'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
	        									}
			        						}else{
					        					$this->check({
													'title'=>'Sin clasificar - Exceso',
													'allDay'=>false,
													'end'=>$asis['salida'],
													'start'=>$tmp['prog'][$k]['fin'],
													'tipo'=>'I',
													'backgroundColor'=>'red'
												});
			        						}
				        					$k = sizeof($tmp['prog']);
										}else if($this->compare($asis['salida'],$tmp['prog'][$k]['fin'])==-1){
											if($j<(sizeof($tmp['asis'])-1)){
	        									if($this->compare($tmp['prog'][$k]['fin'],$tmp['asis'][$j+1]['entrada'])==1){
				        							$this->check({
														'title'=>'Sin clasificar - No trabajado',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['asis'][$j+1]['entrada'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
	        									}else{
				        							$this->check({
														'title'=>'Sin clasificar - Salida temprana',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
	        									}
			        						}else{
			        							$this->check({
													'title'=>'Sin clasificar - Salida temprana',
													'allDay'=>false,
													'start'=>$asis['salida'],
													'end'=>$tmp['prog'][$k]['fin'],
													'tipo'=>'I',
													'backgroundColor'=>'red'
												});
			        						}
				        					k = sizeof($tmp['prog']);
										}
									}else{
			        					$this->check({
											'title'=>'Sin clasificar - Sin cerrar',
											'allDay'=>false,
											'start'=>$asis['entrada'],
											'end'=>new MongoDate($asis['entrada']->sec + (3600 * 1000)),
											'tipo'=>'I',
											'backgroundColor'=>'red'
										});
									}
									break;
								case 1:
									if($asis['salida']!=null){
										if($this->compare($asis['salida'],$tmp['prog'][$k]['inicio'])==1){
				        					$this->check({
												'title'=>'Sin clasificar - Tardanza',
												'allDay'=>false,
												'end'=>$asis['entrada'],
												'start'=>$tmp['prog'][$k]['inicio'],
												'tipo'=>'I',
												'backgroundColor'=>'red'
											});
				        					if(ciHelper.date.inRange($asis['salida'],$tmp['prog'][$k]['inicio'],$tmp['prog'][$k]['fin'])==false){
				        						if(k<($tmp['prog'].length-1)){
				        							if($this->compare($asis['salida'],$tmp['prog'][$k+1]['inicio'])==1){
				        								$this->check({
															'title'=>'Sin clasificar - Exceso',
															'allDay'=>false,
															'end'=>$tmp['prog'][$k+1]['inicio'],
															'start'=>$tmp['prog'][$k]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
							        					if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==1){
								        					$this->check({
																'title'=>'Sin clasificar - Salida temprana',
																'allDay'=>false,
																'start'=>$asis['salida'],
																'end'=>$tmp['prog'][$k+1]['fin'],
																'tipo'=>'I',
																'backgroundColor'=>'red'
															});
							        					}else if($this->compare($tmp['prog'][$k+1]['fin'],$asis['salida'])==-1){
							        						$this->check({
																'title'=>'Sin clasificar - Exceso',
																'allDay'=>false,
																'end'=>$asis['salida'],
																'start'=>$tmp['prog'][$k+1]['fin'],
																'tipo'=>'I',
																'backgroundColor'=>'red'
															});
							        					}
							        					$tmp['prog'].splice(k,1);
		        									}else{
		        										$this->check({
															'title'=>'Sin clasificar - Exceso',
															'allDay'=>false,
															'end'=>$asis['salida'],
															'start'=>$tmp['prog'][$k]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
		        									}
				        						}else{
						        					$this->check({
														'title'=>'Sin clasificar - Exceso',
														'allDay'=>false,
														'end'=>$asis['salida'],
														'start'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
				        						}
					        					$tmp['prog'].splice(k,1);
				        					}else{
				        						if(j<($tmp['asis'].length-1)){
				        							if($this->compare($tmp['prog'][$k]['fin'],$tmp['asis'][$j+1]['entrada'])==1){
					        							$this->check({
															'title'=>'Sin clasificar - No trabajado',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['asis'][$j+1]['entrada'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
		        									}else{
					        							$this->check({
															'title'=>'Sin clasificar - Salida temprana',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['prog'][$k]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
		        									}
				        						}else{
						        					$this->check({
														'title'=>'Sin clasificar - Salida temprana',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
				        						}
				        					}
				        					k = $tmp['prog'].length;
										}else{
				        					$this->check({
												'title'=>'Sin clasificar - Hora extra',
												'allDay'=>false,
												'end'=>$asis['salida'],
												'start'=>$asis['entrada'],
												'tipo'=>'I',
												'backgroundColor'=>'red'
											});
				        					k = $tmp['prog'].length;
										}
									}else{
			        					$this->check({
											'title'=>'Sin clasificar - Sin cerrar',
											'allDay'=>false,
											'start'=>$asis['entrada'],
											'end'=>new Date($asis['entrada'].getTime() + (3600 * 1000)),
											'tipo'=>'I',
											'backgroundColor'=>'red'
										});
									}
									break;
								case -1:
									if($asis['salida']!=null){
										if($this->compare($asis['salida'],$tmp['prog'][$k]['inicio'])==1){
				        					$this->check({
												'title'=>'Sin clasificar - Adelanto',
												'allDay'=>false,
												'start'=>$asis['entrada'],
												'end'=>$tmp['prog'][$k]['inicio'],
												'tipo'=>'I',
												'backgroundColor'=>'red'
											});
				        					if(ciHelper.date.inRange($asis['salida'],$tmp['prog'][$k]['inicio'],$tmp['prog'][$k]['fin'])==false){
				        						if(k<($tmp['prog'].length-1)){
				        							if($this->compare($asis['salida'],$tmp['prog'][$k+1]['inicio'])==1){
				        								$this->check({
															'title'=>'Sin clasificar - Exceso',
															'allDay'=>false,
															'end'=>$tmp['prog'][$k+1]['inicio'],
															'start'=>$tmp['prog'][$k]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
							        					k++;
							        					if($this->compare($tmp['prog'][$k]['fin'],$asis['salida'])==1){
								        					$this->check({
																'title'=>'Sin clasificar - Salida temprana',
																'allDay'=>false,
																'start'=>$asis['salida'],
																'end'=>$tmp['prog'][$k]['fin'],
																'tipo'=>'I',
																'backgroundColor'=>'red'
															});
							        					}else if($this->compare($tmp['prog'][$k]['fin'],$asis['entrada'])==-1){
							        						$this->check({
																'title'=>'Sin clasificar - Exceso',
																'allDay'=>false,
																'end'=>$asis['salida'],
																'start'=>$tmp['prog'][$k]['fin'],
																'tipo'=>'I',
																'backgroundColor'=>'red'
															});
							        					}
							        					k--;
							        					$tmp['prog'].splice(k,1);
		        									}else{
		        										$this->check({
															'title'=>'Sin clasificar - Exceso',
															'allDay'=>false,
															'end'=>$asis['salida'],
															'start'=>$tmp['prog'][$k]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
		        									}
				        						}else{
						        					$this->check({
														'title'=>'Sin clasificar - Exceso',
														'allDay'=>false,
														'end'=>$asis['salida'],
														'start'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
				        						}
				        					}else{
				        						if(j<($tmp['asis'].length-1)){
				        							if($this->compare($tmp['prog'][$k]['fin'],$tmp['asis'][$j+1]['entrada'])==1){
					        							$this->check({
															'title'=>'Sin clasificar - No trabajado',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['asis'][$j+1]['entrada'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
		        									}else{
					        							$this->check({
															'title'=>'Sin clasificar - Salida temprana',
															'allDay'=>false,
															'start'=>$asis['salida'],
															'end'=>$tmp['prog'][$k]['fin'],
															'tipo'=>'I',
															'backgroundColor'=>'red'
														});
		        									}
				        						}else{
						        					$this->check({
														'title'=>'Sin clasificar - Salida temprana',
														'allDay'=>false,
														'start'=>$asis['salida'],
														'end'=>$tmp['prog'][$k]['fin'],
														'tipo'=>'I',
														'backgroundColor'=>'red'
													});
				        						}
				        					}
				        					k = $tmp['prog'].length;
										}else{
				        					$this->check({
												'title'=>'Sin clasificar - Hora extra',
												'allDay'=>false,
												'end'=>$asis['salida'],
												'start'=>$asis['entrada'],
												'tipo'=>'I',
												'backgroundColor'=>'red'
											});
				        					k = $tmp['prog'].length;
										}
									}else{
			        					$this->check({
											'title'=>'Sin clasificar - Sin cerrar',
											'allDay'=>false,
											'start'=>$asis['entrada'],
											'end'=>new Date($asis['entrada'].getTime() + (3600 * 1000)),
											'tipo'=>'I',
											'backgroundColor'=>'red'
										});
									}
									break;
							}
						}
					}
				}*/
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
	function check($inci){
		global $f;
		if($inci['start']!=null&&$inci['end']!=null){
			/*if(ciHelper.date.format.ymdhi($inci['start'])!=ciHelper.date.format.ymdhi($inci['end'])){
				if(p.inci.length>0){
					sectmp = true;
					for(var ii=0; ii<p.inci.length; ii++){
						if(ciHelper.date.format.ymdhi(inci.start)==ciHelper.date.format.ymdhi(p.inci[ii].start)&&ciHelper.date.format.ymdhi(inci.end)==ciHelper.date.format.ymdhi(p.inci[ii].end)){
							sectmp = false;
							ii = p.inci.length;
						}
					}
					if(sectmp==true) p.asig.push(inci);
				}else p.asig.push(inci);
			}*/
		}
	}
	function execute_importar(){
		global $f;
		set_time_limit(0);
		$conexión = new MongoClient("mongodb://200.10.77.56:27017");
	        $bd = $conexión->db_skm_mh;
			$page = 1;
			$page_rows = 100;
	        $col = $bd->pacientes;
	        $items = $col->find(array('check1'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
	        foreach($items as $key => $item){
	        	if(!isset($item['Telefono'])) $item['Telefono'] = '';
	            $data = array(
	            	'old_id'=>$item['_id'],
	            	'procedencia'=>array(
	            		'departamento'=>$item['IdDpto'],
	            		'provincia'=>$item['IdProv'],
	            		'distrito'=>$item['IdDist'],
	            	),
	            	'lugar_nacimiento'=>array(
	            		'departamento'=>$item['IdDptoNac'],
	            		'provincia'=>$item['IdProvNac'],
	            		'distrito'=>$item['IdDistNac'],
	            	),
	                'his_cli'=>$item['HistoriaClinica'],
	                'fe_regi'=>$item['Fecha'],
	                'sexo'=>$item['Sexo'],
	                'domi'=>$item['Domicilio'],
	                'fecha_na'=>$item['FechaNacimiento'],
	                'es_civil'=>$item['IDEciv'],
	                'reli'=>$item['IDRelg'],
	                'idio'=>$item['IdIdma'],
	                'instr'=>$item['IDGins'],
	                'refe'=>$item['ReferidoPor'],
	                'tele'=>$item['Telefono'],
	                'ocupa'=>$item['IdOcup'],
	                't_deso'=>$item['TiempoDesocupacion'],
	                'm_resi'=>$item['TiempoResidencia'],
	                'd_ini'=>$item['IdDiag'],
	                'ti_doc'=>$item['IdDocu'],
	                'm_consu'=>$item['MotivoConsulta']
	            );
	            $f->datastore->mh_pacientes->insert($data);
	            $bd->pacientes->update(array('_id'=>$item['_id']),array('$set'=>array('check1'=>true)));
	        }
	}
}
?>