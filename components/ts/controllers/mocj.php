<?php
class Controller_ts_mocj extends Controller {
	function execute_index() {
		global $f;
		$f->response->print('<div>');
		$f->response->print('Organizaci&oacute;n: <b><label name="organomb"></label><input type="hidden" name="organizacion"></b> &nbsp; <button name="btnOrga">Organizaci&oacute;n</button>
		&nbsp;Caja Chica: <select name="caja"></select>&nbsp;Numero: <select name="numero"></select>');
		$f->response->print('<button name="btnRendicion">Ver Rendici&oacute;n</button>');
		$f->response->print('<button name="btnGenerar">Generar Rendici&oacute;n</button>');
		$f->response->print('<button name="btnAgregar">Nuevo Movimiento</button>');
		$f->response->print("</div>");
		$f->response->view('ts/mocj');
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ts/mocj")->params(array("caja"=>new MongoId($f->request->caja),"num"=>new MongoId($f->request->num),"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ts/mocj")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ts/mocj')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/mocj")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_saldos(){
		global $f;
		$model = $f->model("ts/sald")->params(array("caja"=>new MongoId($f->request->id)))->get("by_caja");
		$f->response->json( $model->items );
	}
	function execute_get_rendi(){
		global $f;
		$model = $f->model("ts/sald")->params(array("caja"=>new MongoId($f->request->id)))->get("rendi");
		$movs = $f->model("ts/mocj")->params(array(
			"caja"=>new MongoId($f->request->id),
			"num"=>$model->items[0]['_id']
		))->get("all");
		$f->response->json(array(
			'saldos'=>$model->items,
			'movs'=>$movs->items
		));
	}
	function execute_get_rendi_custom(){
		global $f;
		$model = $f->model("ts/sald")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$movs = $f->model("ts/mocj")->params(array(
			"saldo"=>new MongoId($f->request->id)
		))->get("all_saldo");
		$f->response->json(array(
			'saldos'=>array($model->items),
			'movs'=>$movs->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['monto'])) $data['monto'] = floatval($data['monto']);
		if(isset($data['caja_chica']['_id'])) $data['caja_chica']['_id'] = new MongoId($data['caja_chica']['_id']);
		if(isset($data['saldo'])) $data['saldo'] = new MongoId($data['saldo']);
		if(isset($data['beneficiario']['_id'])) $data['beneficiario']['_id'] = new MongoId($data['beneficiario']['_id']);
		if(isset($data['fecreg'])) $data['fecreg'] = new MongoDate(strtotime($data['fecreg']));
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['actividad']['_id'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente']['_id'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(isset($data['clasificador']['_id'])) $data['clasificador']['_id'] = new MongoId($data['clasificador']['_id']);
		if(isset($data['cuenta']['_id'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$cod = $f->model("ts/mocj")->params(array(
				//'caja'=>new MongoId($data['caja']),
				'saldo'=>new MongoId($data['saldo'])/*,
				'doc'=>$data['doc']*/
			))->get("cod");
			if($cod->items==null) $cod->items=0;
			$cod->items = floatval($cod->items);
			$data['item'] = $cod->items+1;
			if(!isset($data["fecreg"]))
				$data["fecreg"]=new MongoDate();
			$data['autor'] = $f->session->userDB;
			$movi = $f->model("ts/mocj")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ts/sald')->params(array(
				'_id'=>$data['saldo'],
				'data'=>array('$inc'=>array(
					'gasto'=>$data['monto'],
					'saldo'=>-$data['monto']
				))
			))->save('custom');
			$saldo = $f->model("ts/sald")->params(array('_id'=>$data['saldo']))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Movimientos de Caja Chica',
				'descr'=>'Se Cre&oacute; el Movimiento de Caja Chica con Item <b>'.$data["item"].
					'</b> del saldo <b>'.$saldo["cod"].'</b>'.
					' correspondiente a <b>'.$saldo['caja_chica']['nomb'].'</b> con un monto de <b>S/.'.$data['monto'].'</b>'
			))->save('insert');
			if(isset($saldo['afectacion'])){
				$ok = false;
				for($i=0; $i<sizeof($saldo['afectacion']); $i++){
					if($saldo['afectacion'][$i]['organizacion']['_id']==$data['organizacion']['_id']){
						for($j=0; $j<sizeof($saldo['afectacion'][$i]['gasto']); $j++){
							if($saldo['afectacion'][$i]['gasto'][$j]['clasificador']['_id']==$data['clasificador']['_id']){
								$f->model('ts/sald')->params(array(
									'_id'=>$data['saldo'],
									'data'=>array('$inc'=>array(
										'afectacion.'.$i.'.gasto.'.$j.'.monto'=>$data['monto'],
										'afectacion.'.$i.'.monto'=>$data['monto']
									))
								))->save('custom');
								$ok = true;
								$j = sizeof($saldo['afectacion'][$i]['gasto']);
							}
						}
						if($ok==false){
							$f->model('ts/sald')->params(array(
								'_id'=>$data['saldo'],
								'data'=>array('$inc'=>array(
									'afectacion.'.$i.'.monto'=>$data['monto']
								))
							))->save('custom');
							$ok = true;
							$f->model('ts/sald')->params(array(
								'_id'=>$data['saldo'],
								'data'=>array('$push'=>array(
									'afectacion.'.$i.'.gasto'=>array(
											'clasificador'=>$data['clasificador'],
											'monto'=>$data['monto']
										)
									)
								))
							)->save('custom');
						}
						$i = sizeof($saldo['afectacion']);
					}
				}
				if($ok==false){
					$f->model('ts/sald')->params(array(
						'_id'=>$data['saldo'],
						'data'=>array('$push'=>array(
							'afectacion'=>array(
								'organizacion'=>$data['organizacion'],
								'gasto'=>array(0=>array(
									'clasificador'=>$data['clasificador'],
									'monto'=>$data['monto']
								)),
								'monto'=>$data['monto']
							)
						))
					))->save('custom');
				}
			}else{
				$f->model('ts/sald')->params(array(
					'_id'=>$data['saldo'],
					'data'=>array('$push'=>array(
						'afectacion'=>array(
							'organizacion'=>$data['organizacion'],
							'gasto'=>array(0=>array(
								'clasificador'=>$data['clasificador'],
								'monto'=>$data['monto']
							)),
							'monto'=>$data['monto']
						)
					))
				))->save('custom');
			}
		}else{
			$movi_old = $f->model("ts/mocj")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model("ts/mocj")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$movi = $data;
			$diferencia = -(floatval($movi_old['monto'])-floatval($movi['monto']));
			$f->model('ts/sald')->params(array(
				'_id'=>$data['saldo'],
				'data'=>array('$inc'=>array(
					'gasto'=>$diferencia,
					'saldo'=>-$diferencia
				))
			))->save('custom');
			$saldo = $f->model("ts/sald")->params(array('_id'=>$data['saldo']))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Movimientos de Caja Chica',
				'descr'=>'Se Actualiz&oacute; el Movimiento de Caja Chica con Item <b>'.$movi_old["item"].
					'</b> del saldo <b>'.$saldo["cod"].'</b>'.
					' correspondiente a <b>'.$saldo['caja_chica']['nomb'].'</b> con un monto de <b>S/.'.$data['monto'].'</b>'
			))->save('insert');
			for($i=0; $i<sizeof($saldo['afectacion']); $i++){
				if($saldo['afectacion'][$i]['organizacion']['_id']==$movi_old['organizacion']['_id']){
					for($j=0; $j<sizeof($saldo['afectacion'][$i]['gasto']); $j++){
						if($saldo['afectacion'][$i]['gasto'][$j]['clasificador']['_id']==$movi_old['clasificador']['_id']){
							$f->model('ts/sald')->params(array(
								'_id'=>$data['saldo'],
								'data'=>array('$inc'=>array(
									'afectacion.'.$i.'.gasto.'.$j.'.monto'=>-floatval($movi_old['monto']),
									'afectacion.'.$i.'.monto'=>-floatval($movi_old['monto'])
								))
							))->save('custom');
							$j = sizeof($saldo['afectacion'][$i]['gasto']);
						}
					}
					$i = sizeof($saldo['afectacion']);
				}
			}
			if(isset($saldo['afectacion'])){
				$ok = false;
				for($i=0; $i<sizeof($saldo['afectacion']); $i++){
					if($saldo['afectacion'][$i]['organizacion']['_id']==$data['organizacion']['_id']){
						for($j=0; $j<sizeof($saldo['afectacion'][$i]['gasto']); $j++){
							if($saldo['afectacion'][$i]['gasto'][$j]['clasificador']['_id']==$data['clasificador']['_id']){
								$f->model('ts/sald')->params(array(
									'_id'=>$data['saldo'],
									'data'=>array('$inc'=>array(
										'afectacion.'.$i.'.gasto.'.$j.'.monto'=>$data['monto'],
										'afectacion.'.$i.'.monto'=>$data['monto']
									))
								))->save('custom');
								$ok = true;
								$j = sizeof($saldo['afectacion'][$i]['gasto']);
							}
						}
						if($ok==false){
							$f->model('ts/sald')->params(array(
								'_id'=>$data['saldo'],
								'data'=>array('$inc'=>array(
									'afectacion.'.$i.'.monto'=>$data['monto']
								))
							))->save('custom');
							$ok = true;
							$f->model('ts/sald')->params(array(
								'_id'=>$data['saldo'],
								'data'=>array('$push'=>array(
									'afectacion.'.$i.'.gasto'=>array(
											'clasificador'=>$data['clasificador'],
											'monto'=>$data['monto']
										)
									)
								))
							)->save('custom');
						}
						$i = sizeof($saldo['afectacion']);
					}
				}
				if($ok==false){
					$f->model('ts/sald')->params(array(
						'_id'=>$data['saldo'],
						'data'=>array('$push'=>array(
							'afectacion'=>array(
								'organizacion'=>$data['organizacion'],
								'gasto'=>array(0=>array(
									'clasificador'=>$data['clasificador'],
									'monto'=>$data['monto']
								)),
								'monto'=>$data['monto']
							)
						))
					))->save('custom');
				}
			}
		}
		$f->response->print("true");
	}
	function execute_save_rendi(){
		global $f;
		$data = $f->request->data;
		$fec = new MongoDate();
		$trabajador = $f->session->userDB;
		$caja = $f->model("ts/cjch")->params(array('_id'=>new MongoId($data['caja'])))->get("one")->items;
		$saldo = $f->model("ts/sald")->params(array('_id'=>new MongoId($data['saldo'])))->get("one")->items;
		$cuenta = array(
			'fecreg'=>$fec,
			'estado'=>'P',
			'autor'=>$trabajador,
			'beneficiario'=>$trabajador,
			'motivo'=>'Rendici&oacute;n del Fondo de '.$caja['nomb'],
			'conceptos'=>array(),
			'afectacion'=>$saldo['afectacion'],
			'total'=>floatval($saldo['gasto']),
			'total_desc'=>floatval(0),
			'total_pago'=>floatval($saldo['gasto']),
			'documentos'=>array(0=>$saldo['_id']),
			'origen'=>'P'
		);
		foreach ($saldo['afectacion'] as $afect){
			$cuenta['conceptos'][] = array(
				'tipo'=>'P',
				'observ'=>$afect['organizacion']['nomb'],
				'moneda'=>'S',
				'monto'=>floatval($afect['monto'])
			);
		}
		$ctpp = $f->model("ts/ctpp")->params(array('data'=>$cuenta))->save("insert")->items;
		$new_saldo = array(
			'fecreg'=>$fec,
			'estado'=>'A',
			'caja_chica'=>array(
				'_id'=>$caja['_id'],
				'nomb'=>$caja['nomb']
			),
			'monto'=>floatval($caja['monto']),
			'gasto'=>floatval(0),
			'saldo'=>floatval($caja['monto'])
		);
		$new_saldo['cod'] = floatval($f->model("ts/sald")->params(array('caja'=>$caja['_id']))->get("cod")->items)+1;
		$f->model("ts/sald")->params(array('data'=>$new_saldo))->save("insert");
		$upd = array(
			'estado'=>'F',
			'fecren'=>$fec,
			'autor_ren'=>$trabajador,
			'cont_patrimonial'=>$data['patri'],
			'cont_presupuestal'=>$data['presu']
		);
		foreach ($upd['cont_patrimonial'] as $i=>$patri){
			$upd['cont_patrimonial'][$i]['cuenta']['_id'] = new MongoId($patri['cuenta']['_id']);
			$upd['cont_patrimonial'][$i]['monto'] = floatval($patri['monto']);
		}
		foreach ($upd['cont_presupuestal'] as $i=>$presu){
			$upd['cont_presupuestal'][$i]['cuenta']['_id'] = new MongoId($presu['cuenta']['_id']);
			$upd['cont_presupuestal'][$i]['monto'] = floatval($presu['monto']);
		}
		$f->model("ts/sald")->params(array('_id'=>$saldo['_id'],'data'=>$upd))->save("update");
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/mocj.edit");
	}
	function execute_rendi(){
		global $f;
		$f->response->view("ts/mocj.rendi");
	}
}
?>