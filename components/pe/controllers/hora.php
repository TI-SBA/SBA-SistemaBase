<?php
class Controller_pe_hora extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pe/hora.main");
	}
	function execute_grid_asis() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('<label>Periodo</label>');
		$f->response->print('<input type="text" name="periodo">');
		$f->response->print('&nbsp;<div name="rbtn">');
		/*$f->response->print('<input type="radio" id="rbtnTrab1" name="rbtnTrab" value="276" checked="checked" /><label for="rbtnTrab1">276</label>');
		$f->response->print('<input type="radio" id="rbtnTrab2" name="rbtnTrab" value="559" /><label for="rbtnTrab2">CAS</label>');*/
		$f->response->print('<select name="tipo_cont"></select>');
		$f->response->view("ci/ci.search");
		$f->response->print("</div>");
		$f->response->print('</div>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Trabajador","w"=>350 ),
			array( "nomb"=>"Horas Trabajadas (min)","w"=>100 ),
			array( "nomb"=>"Bloques sin cerrar","w"=>100 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_asis_hor(){
		global $f;
		$model = $f->model("pe/asis")->params(array("enti"=>new MongoId($f->request->enti)))->get("trab_hor");
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->turno)))->get("one");
		$f->response->json( array('asig'=>$model->items,'turno'=>$turno->items['dias']) );
	}
	function execute_asis_prog(){
		global $f;
		$model = $f->model("pe/prog")->params(array("enti"=>new MongoId($f->request->enti)))->get("trab_prog");
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->turno)))->get("one");
		$f->response->json( array('asig'=>$model->items,'turno'=>$turno->items['dias']) );
	}
	function execute_asis(){
		global $f;
		$model = $f->model("pe/asis")->params(array("enti"=>new MongoId($f->request->enti)))->get("trab");
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->turno)))->get("one");
		$f->response->json( array('asig'=>$model->items,'turno'=>$turno->items['dias']) );
	}
	function execute_marc(){
		global $f;
		$params = array('enti'=>new MongoId($f->request->enti));
		if(isset($f->request->data['ini'])){
			$params['ini'] = new MongoDate(strtotime($f->request->data['ini']));
			$params['fin'] = new MongoDate(strtotime($f->request->data['fin']));
		}
		$model = $f->model("pe/asis")->params($params)->get("marc");
		$f->response->json( $model->items );
	}
	function execute_inci(){
		global $f;
		$model = $f->model("pe/asis")->params(array("enti"=>new MongoId($f->request->enti)))->get("marc");
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->turno)))->get("one");
		$f->response->json( array('asig'=>$model->items,'turno'=>$turno->items['dias']) );
	}
	function execute_all(){
		global $f;
		$model = $f->model("pe/asis")->params(array("enti"=>new MongoId($f->request->enti)))->get("marc");
		$inci = $f->model("pe/inci")->params(array("_id"=>new MongoId($f->request->enti)))->get("trab");
		$f->response->json( array('asig'=>$model->items,'inci'=>$inci->items) );
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/nive")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_lista(){
		global $f;
		if(isset($f->request->data['texto'])){
			if($f->request->data['texto']==''){
				$model = $f->model("mg/entidad")->params(array(
					"data"=>array(
						//"roles.trabajador.contrato.cod"=>$f->request->data['tipo'],
						"roles.trabajador.estado"=>'H'
					),
					"roles"=>'trabajador',
					"page"=>$f->request->data['page'],
					"page_rows"=>$f->request->data['page_rows']
				))->get("lista");
			}else{
				$model = $f->model("mg/entidad")->params(array(
					"filter"=>array(
						//array('nomb'=>"roles.trabajador.contrato.cod",'value'=>$f->request->data['tipo']),
						array('nomb'=>"roles.trabajador.estado",'value'=>'H')
					),
					"rol"=>array(
						'nomb'=>'roles.trabajador',
						'value'=>array('$exists'=>true)
					),
					"page"=>$f->request->data['page'],
					"page_rows"=>$f->request->data['page_rows'],
					"texto"=>$f->request->data['texto']
				))->get("lista");
			}
		}else{
			$model = $f->model("mg/entidad")->params(array(
				"data"=>array(
					//"roles.trabajador.contrato.cod"=>$f->request->data['tipo'],
					"roles.trabajador.estado"=>'H'
				),
				"roles"=>'trabajador',
				"page"=>$f->request->data['page'],
				"page_rows"=>$f->request->data['page_rows']
			))->get("lista");
		}
		foreach ($model->items as $i=>$enti){
			$model->items[$i]['hora_trab'] = 0;
			$model->items[$i]['blo'] = 0;
			$asis = $f->model('pe/asis')->params(array(
				'enti'=>$enti['_id'],
				'fecini'=>new MongoDate(strtotime($f->request->data['fecini'].' 00:00:00')),
				'fecfin'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59'))
			))->get('bole')->items;
			if($asis!=null){
				foreach ($asis as $as){
					if(isset($as['ejecutado'])){
						if(isset($as['ejecutado']['salida'])){
							$model->items[$i]['hora_trab'] += abs(($as['ejecutado']['salida']['fecreg']->sec)-($as['ejecutado']['entrada']['fecreg']->sec));
						}else
							$model->items[$i]['blo']++;
					}
				}
			}
			$model->items[$i]['hora_trab'] = $model->items[$i]['hora_trab']/60;
		}
		$f->response->json( $model );
	}
	function execute_all_periodo(){
		global $f;
		$model = $f->model("pe/asis")->params(array(
			"enti"=>new MongoId($f->request->data['enti']),
			'ini'=>new MongoDate(strtotime($f->request->data['ini'])),
			'fin'=>new MongoDate(strtotime($f->request->data['fin']))
		))->get("marc_periodo");
		$inci = $f->model("pe/inci")->params(array(
			"_id"=>new MongoId($f->request->data['enti']),
			'ini'=>new MongoDate(strtotime($f->request->data['ini'])),
			'fin'=>new MongoDate(strtotime($f->request->data['fin']))
		))->get("trab_periodo");
		$f->response->json( array('asig'=>$model->items,'inci'=>$inci->items) );
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
	function execute_save_progra(){
		global $f;
		$data = $f->request->data;
		$data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['del'])){
			foreach ($data['del'] as $blo){
				$f->model("pe/prog")->params(array('_id'=>new MongoId($blo)))->delete("data");
			}
		}
		if(isset($data['data'])){
			foreach ($data['data'] as $blo){
				$blo['trabajador'] = $data['trabajador'];
				$blo['fecini'] = new MongoDate(strtotime($blo['fecini']));
				$blo['fecfin'] = new MongoDate(strtotime($blo['fecfin']));
				$blo['tipo']['_id'] = new MongoId($blo['tipo']['_id']);
				if(isset($blo['dias'])) $blo['dias'] = (((($blo['fecfin']-$blo['fecini'])/60)/60)/24);
				else $blo['minutos'] = ($blo['fecfin']-$blo['fecini'])/60;
				if(!isset($blo['_id'])){
					$f->model("pe/prog")->params(array('data'=>$blo))->save("insert");
				}else{
					$f->model("pe/prog")->params(array('_id'=>new MongoId($blo['_id']),'data'=>$blo))->save("update");
				}
			}
		}
		$model = $f->model("pe/prog")->params(array("enti"=>$data['trabajador']['_id']))->get("trab_prog");
		$turno = $f->model("pe/turn")->params(array("_id"=>new MongoId($data['turno'])))->get("one");
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Control de Asistencia: Programaci&oacute;n de Incidencias',
			'descr'=>'Se actualizar&oacute; las <b>Incidencias Programadas</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b>.'
		))->save('insert');
		$f->response->json( array('asig'=>$model->items,'turno'=>$turno->items['dias']) );
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
	function execute_delete_asis(){
		global $f;
		$data = $f->model("pe/asis")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model("pe/asis")->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete("data");
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Control de Asistencia: Asistencia',
			'descr'=>'Se elimin&oacute; la <b>Asistencia Manual</b> del trabajador <b>'.$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> para el periodo de <b>'.date('Y-M-d h:i:s',$data['ejecutado']['entrada']['fecreg']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s',$data['ejecutado']['salida']['fecreg']->sec).'</b>'
		))->save('insert');
		$f->response->print(true);
	}
	function execute_sinclas(){
		global $f;
		$f->response->view("pe/hora.sinclas");
	}
	function execute_calen(){
		global $f;
		$f->response->view("pe/hora.calen");
	}
	function execute_regi(){
		global $f;
		$f->response->view("pe/hora.regi");
	}
	function execute_modal(){
		global $f;
		$f->response->view("pe/hora.modal");
	}
}
?>