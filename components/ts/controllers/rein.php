<?php
class Controller_ts_rein extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Recibo de Ingreso","w"=>200 ),
			array( "nomb"=>"Organizaci&oacute;n","w"=>250 ),
			array( "nomb"=>"Total","w"=>150 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['organizacion']))
			if($f->request->data['organizacion']!='')
				$params['organizacion'] = new MongoId($f->request->data['organizacion']);
		if(isset($f->request->data['por']))
			if($f->request->data['por']!='')
				$params['por'] = $f->request->data['por'];
		if(isset($f->request->data['modulo'])){
			if($f->request->data['modulo']!=''){
				if($f->request->data['modulo']=='IN'){
					$params['tipo_inm'] = array('$exists'=>true);
				}else{
					$params['modulo'] = $f->request->data['modulo'];
				}
			}
		}
		if(isset($f->request->data['short'])){
			$params['fields'] = array(
				'cod'=>true,
				'estado'=>true,
				'organizacion'=>true,
				'tipo_inm'=>true,
				'total'=>true,
				'moneda'=>true,
				'fec'=>true,
				'fecfin'=>true,
				'modulo'=>true
			);
		}
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/rein")->params($params)->get("lista") );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("cj/rein")->params(array(
			"estado"=>$estado,
			"por"=>$f->request->por,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('cj/rein')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$data = $f->model("cj/rein")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		if(isset($data['detalle'])){
			foreach ($data['detalle'] as $key => $value) {
				$data['detalle'][$key]['cuenta'] = $f->model('ct/pcon')->params(array('cod'=>$value['cuenta']['cod']))->get('cod')->items;
			}
		}
		if(isset($data['cont_patrimonial'])){
			foreach ($data['cont_patrimonial'] as $key => $value) {
				$data['cont_patrimonial'][$key]['cuenta'] = $f->model('ct/pcon')->params(array('cod'=>$value['cuenta']['cod']))->get('cod')->items;
			}
		}
		$f->response->json( $data );
	}
	function execute_get_cod(){
		global $f;
		$cod = $f->model("cj/rein")->get("cod");
		if($cod->items==null) $cod->items=1;
		$f->response->json(array('cod'=>$cod->items,'fuen'=>$f->model("pr/fuen")->get("all")->items));
	}
	function execute_get_rec(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'items.0.cuenta_cobrar.servicio.organizacion._id'=>new MongoId($f->request->data['orga']),
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lt'=>new MongoDate(strtotime($f->request->data['fec'].' +1 day'))
			)
		)))->get("all")->items;
		if($comp!=null){
			foreach($comp as $i=>$co){
				if($co['estado']!='X')
				foreach($co['items'] as $j=>$item){
					foreach($item['conceptos'] as $k=>$conc){
						$comp[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$conc['concepto']['_id']))->get("one")->items;
						if(isset($comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']))
							$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']['cuenta']['_id'];
						else
							$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['cuenta']['_id'];
						$comp[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
					}
				}
			}
		}
		$f->response->json(array(
			'prog'=>$prog,
			'comp'=>$comp
		));
	}
	function execute_get_cta(){
		global $f;
		$f->response->json(array(
			'tc'=>$f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items,
			'rein'=>$f->model("cj/rein")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items,
			'ctban'=>$f->model("ts/ctban")->get("all")->items,
			'tmed'=>$f->model("ts/tipo")->get("all")->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("cj/rein")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_anular(){
		global $f;
		$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'X',
			'anulado'=>array(
				'fec'=>new MongoDate(),
				'autor'=>array(
					'_id'=>$f->session->enti['_id'],
					'tipo_enti'=>$f->session->enti['tipo_enti'],
					'nomb'=>$f->session->enti['nomb'],
					'appat'=>$f->session->enti['appat'],
					'apmat'=>$f->session->enti['apmat'],
					'cargo'=>array(
						'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
						'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
						'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
					)
				)
			)
		)))->save("update");
		$f->response->print("true");
	}
	function execute_aprobar(){
		global $f;
		$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'RB',
			'recibido'=>array(
				'fec'=>new MongoDate(),
				'autor'=>array(
					'_id'=>$f->session->enti['_id'],
					'tipo_enti'=>$f->session->enti['tipo_enti'],
					'nomb'=>$f->session->enti['nomb'],
					'appat'=>$f->session->enti['appat'],
					'apmat'=>$f->session->enti['apmat'],
					'cargo'=>array(
						'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
						'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
						'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
					)
				)
			)
		)))->save("update");
		$f->response->print("true");
	}
	function execute_save_mov(){
		global $f;
		$data = $f->request->data;
		$autor = array(
			'_id'=>$f->session->enti['_id'],
			'tipo_enti'=>$f->session->enti['tipo_enti'],
			'nomb'=>$f->session->enti['nomb'],
			'appat'=>$f->session->enti['appat'],
			'apmat'=>$f->session->enti['apmat'],
			'cargo'=>array(
				'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
				'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
				'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
			)
		);
		/*
		 * Se debe verificar q existe, sino se crea uno nuevo
		 */
		$saldo = $f->model('ts/saldlibr')->params(array('periodo'=>date('Ym00'),'tipo'=>'E'))->get('rein')->items;
		if($saldo==null){
			$saldo_last = $f->model('ts/saldlibr')->params(array('tipo'=>'E'))->get('last_rein')->items;
			if($saldo_last==null){
				$f->response->json(array('error'=>true));
				die();
			}
			$saldo = array(
				'tipo'=>'E',
				'periodo'=>date('Ym00'),
				'estado'=>'A',
				'apertura'=>array(
					'fec'=>new MongoDate(),
					'autor'=>$autor
				),
				'saldo_deudor_inicial'=>$saldo_last['saldo_deudor_final'],
				'saldo_acreedor_inicial'=>$saldo_last['saldo_acreedor_final'],
				'saldo_deudor_final'=>$saldo_last['saldo_deudor_final']
			);
			$saldo = $f->model('ts/saldlibr')->params(array('data'=>$saldo))->save('insert')->items;
		}
		$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'RE',
			'registro_efectivo'=>array(
				'fec'=>new MongoDate(),
				'autor'=>$autor
			)
		)))->save("update");
		$total = 0;
		foreach ($data['items'] as $item){
			$moef = array(
				'fecreg'=>new MongoDate(),
				'estado'=>'P',
				'estado_sunat'=>floatval(1),
				'periodo'=>date('Ym00'),
				'id_doc'=>new MongoId($f->request->data['_id']),
				'tipo_doc'=>'RI',
				'num_doc'=>$data['cod'],
				'tipo'=>'D',
				'descr'=>$item['descr'],
				'monto'=>floatval($item['monto']),
				'cuenta'=>array(
					'_id'=>new MongoId($item['cuenta']['_id']),
					'cod'=>$item['cuenta']['cod'],
					'descr'=>$item['cuenta']['descr']
				),
				'saldo'=>array(
					'_id'=>$saldo['_id'],
					'periodo'=>$saldo['periodo'],
					'tipo'=>$saldo['tipo']
				)
			);
			$f->model("ts/moef")->params(array('data'=>$moef))->save("insert");
			$total += floatval($item['monto']);
		}
		$f->model("ts/saldlibr")->params(array('_id'=>$saldo['_id'],'data'=>array(
			'$inc'=>array(
				'saldo_deudor_final'=>$total
			)
		)))->save("editar");
		$f->response->print("true");
	}
	function execute_save_cta(){
		global $f;
		$data = $f->request->data;
		$data['_id'] = new MongoId($data['_id']);
		$fec = new MongoDate();
		$autor = array(
			'_id'=>$f->session->enti['_id'],
			'tipo_enti'=>$f->session->enti['tipo_enti'],
			'nomb'=>$f->session->enti['nomb'],
			'appat'=>$f->session->enti['appat'],
			'apmat'=>$f->session->enti['apmat'],
			'cargo'=>array(
				'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
				'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
				'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
			)
		);
		$rein = $f->model('cj/rein')->params(array('_id'=>$data['_id']))->get('one')->items;
		$periodo = gmdate("Ym00", $rein['fec']->sec);
		$peri_mes = intval(gmdate("m", $rein['fec']->sec));
		$peri_ano = gmdate("Y", $rein['fec']->sec);
		/*
		 * EFECTIVO EN SOLES
		 * */
		foreach ($data['efectivo'] as $efec){
			foreach ($rein['cont_patrimonial'] as $item){
				if($item['tipo']=='H'){
					//saldo de tipo c
					$saldo = $f->model('ts/saldlibr')->params(array('periodo'=>$periodo,'tipo'=>'C','ctban'=>$item['cuenta_banco']['_id']))->get('rein')->items;
					if($saldo==null){
						$saldo_last = $f->model('ts/saldlibr')->params(array('tipo'=>'C'))->get('last_rein')->items;
						if($saldo_last==null){
							$f->response->json(array('error'=>1));
							die();
						}
						$saldo = array(
							'tipo'=>'C',
							'periodo'=>$periodo,
							'estado'=>'A',
							'apertura'=>array(
								'fec'=>$fec,
								'autor'=>$autor
							),
							'cuenta_banco'=>array(
								'_id'=>new MongoId($efec['cuenta_banco']['_id']),
								'cod_banco'=>$efec['cuenta_banco']['cod_banco'],
								'nomb'=>$efec['cuenta_banco']['nomb'],
								'cod'=>$efec['cuenta_banco']['cod'],
								'moneda'=>$efec['cuenta_banco']['moneda']
							),
							'saldo_deudor_inicial'=>$saldo_last['saldo_deudor_final'],
							'saldo_acreedor_inicial'=>$saldo_last['saldo_acreedor_final'],
							'saldo_deudor_final'=>$saldo_last['saldo_deudor_final'],
							'saldo_acreedor_final'=>0
						);
						$saldo = $f->model('ts/saldlibr')->params(array('data'=>$saldo))->save('insert')->items;
					}
					/*
					 * Se debe verificar q existe el saldo de banco por cuenta contable, sino se crea uno nuevo
					 */
					$ctban = $f->model('ts/ctban')->params(array('_id'=>new MongoId($efec['cuenta_banco']['_id'])))->get('one')->items;
					$saldo_ctban = $f->model('ts/saldlibr')->params(array(
						'periodo'=>$periodo,
						'tipo'=>'B',
						'ctban'=>$ctban['_id']
					))->get('rein')->items;
					if($saldo_ctban==null){
						$saldo_last = $f->model('ts/saldlibr')->params(array('tipo'=>'B','ctban'=>$ctban['_id']))->get('last_rein')->items;
						if($saldo_last==null){
							$f->response->json(array('error'=>2));
							die();
						}
						$saldo_ctban = array(
							'tipo'=>'B',
							'periodo'=>$periodo,
							'estado'=>'A',
							'apertura'=>array(
								'fec'=>$fec,
								'autor'=>$autor
							),
							'cuenta_banco'=>array(
								'_id'=>$ctban['_id'],
								'cod_banco'=>$ctban['cod_banco'],
								'nomb'=>$ctban['nomb'],
								'cod'=>$ctban['cod'],
								'moneda'=>$ctban['moneda']
							),
							'saldo_deudor_inicial'=>$saldo_last['saldo_deudor_final'],
							'saldo_acreedor_inicial'=>$saldo_last['saldo_acreedor_final'],
							'saldo_deudor_final'=>$saldo_last['saldo_deudor_final'],
							'saldo_acreedor_final'=>0
						);
						$saldo_ctban = $f->model('ts/saldlibr')->params(array('data'=>$saldo_ctban))->save('insert')->items;
					}
				}
			}
			/*
			 * Se debe verificar q existe, sino se crea uno nuevo
			 */
			$saldo = $f->model('ts/saldlibr')->params(array('periodo'=>$periodo,'tipo'=>'E'))->get('rein')->items;
			if($saldo==null){
				$saldo_last = $f->model('ts/saldlibr')->params(array('tipo'=>'E'))->get('last_rein')->items;
				if($saldo_last==null){
					$f->response->json(array('error'=>1));
					die();
				}
				$saldo = array(
					'tipo'=>'E',
					'periodo'=>$periodo,
					'estado'=>'A',
					'apertura'=>array(
						'fec'=>$fec,
						'autor'=>$autor
					),
					'saldo_deudor_inicial'=>$saldo_last['saldo_deudor_final'],
					'saldo_acreedor_inicial'=>$saldo_last['saldo_acreedor_final'],
					'saldo_deudor_final'=>$saldo_last['saldo_deudor_final'],
					'saldo_acreedor_final'=>0
				);
				$saldo = $f->model('ts/saldlibr')->params(array('data'=>$saldo))->save('insert')->items;
			}
			/*
			 * Primer proceso donde se guarda data en la bd
			 */
			$mov_efe = array(
				'fecreg'=>new MongoDate(strtotime($data['fecdep'])),
				'estado'=>'P',
				'estado_sunat'=>'1',
				'cuenta_banco'=>$efec['cuenta_banco'],
				'periodo'=>$saldo['periodo'],
				'tipo'=>'H',
				'saldo'=>array(
					'_id'=>$saldo['_id'],
					'periodo'=>$saldo['periodo'],
					'tipo'=>$saldo['tipo']
				),
				'tipo_doc'=>'V',
				'num_doc'=>$efec['voucher'],
				'monto'=>floatval($efec['monto']),
				'descr'=>'Dep&oacute;sito Banco - '.$rein['organizacion']['nomb']
			);
			$mov_efe['cuenta_banco']['_id'] = new MongoId($mov_efe['cuenta_banco']['_id']);
			$ctban = $f->model('ts/ctban')->params(array('_id'=>$mov_efe['cuenta_banco']['_id']))->get('one')->items;
			$mov_efe['cuenta'] = $ctban['cuenta'];
			$f->model('ts/moef')->params(array('data'=>$mov_efe))->save('insert');
			$f->model('ts/saldlibr')->params(array(
				'filter'=>array('_id'=>$saldo['_id']),
				'data'=>array('$inc'=>array('saldo_acreedor_final'=>$mov_efe['monto']))
			))->save('custom');
			/*
			 * P09_S05 Paso 11
			 * */
			$saldo_ctban = $f->model('ts/saldlibr')->params(array(
				'periodo'=>$periodo,
				'tipo'=>'B',
				'ctban'=>$ctban['_id']
			))->get('rein')->items;
			$mov_ban = array(
				'fec'=>new MongoDate(strtotime($data['fecdep'])),
				'estado'=>'P',
				'cuenta_banco'=>$efec['cuenta_banco'],
				'periodo'=>$saldo_ctban['periodo'],
				'tipo'=>'D',
				'saldo'=>array(
					'_id'=>$saldo_ctban['_id'],
					'periodo'=>$saldo_ctban['periodo'],
					'tipo'=>$saldo_ctban['tipo']
				),
				'tipo_doc'=>'V',
				'num_doc'=>$efec['voucher'],
				'tipo_origen'=>'RI',
				'num_origen'=>$rein['cod'],
				'monto'=>floatval($efec['monto']),
				'monto_original'=>floatval($efec['monto_original']),
				//'moneda'=>'S',
				'detalle'=>$efec['descr']
			);
			$mov_ban['cuenta_banco']['_id'] = new MongoId($mov_ban['cuenta_banco']['_id']);
			if($efec['tipo']=='S') $mov_ban['moneda'] = 'S';
			else $mov_ban['moneda'] = 'D';
			$f->model('ts/moba')->params(array('data'=>$mov_ban))->save('insert');
			$f->model('ts/saldlibr')->params(array(
				'filter'=>array('_id'=>$saldo_ctban['_id']),
				'data'=>array('$inc'=>array('saldo_deudor_final'=>$mov_ban['monto']))
			))->save('custom');
			/*
			 * Se inicia con la contabilidad patrimonial
			 * P09_S05 Paso 15
			 */
			foreach ($rein['cont_patrimonial'] as $item){
				if($item['tipo']=='H'){
					/*
					 * Ya no se necesita verificar el saldo, se hizo al inicio
					 */
					$saldo = $f->model('ts/saldlibr')->params(array('periodo'=>$periodo,'tipo'=>'C','ctban'=>$item['cuenta_banco']['_id']))->get('rein')->items;
					$cta = $item['cuenta'];
					/*
					 * Creamos la primera parte del movimiento de cuenta
					 * */
					$mov = array(
						'fecreg'=>new MongoDate(strtotime($data['fecdep'])),
						'estado'=>'P',
						'estado_sunat'=>'1',
						'saldo'=>array(
							'_id'=>$saldo['_id'],
							'periodo'=>$saldo['periodo'],
							'tipo'=>$saldo['tipo']
						),
						'periodo'=>$saldo['periodo'],
						'id_operacion'=>$rein['_id'],
						'cod_operacion'=>'RI '.$rein['cod'],
						'cuenta_banco'=>array(
							'_id'=>$ctban['_id'],
							'cod_banco'=>$ctban['cod_banco'],
							'nomb'=>$ctban['nomb'],
							'cod'=>$ctban['cod'],
							'moneda'=>$ctban['moneda']
						),
						'medio_pago'=>$efec['medio'],
						'documentos'=>array(
							'tipo'=>'V',
							'num'=>$efec['voucher']
						),
						'documentos_libro'=>$efec['voucher'],
						'comprobantes'=>array(),
						'entidades'=>array(
							'entidad'
						),
						'tipo'=>'D',
						'cuenta'=>$cta,
						'monto'=>floatval($item['monto'])
					);
					$mov['medio_pago']['_id'] = new MongoId($mov['medio_pago']['_id']);
					foreach ($rein['detalle'] as $ij=>$item_det){
						/*
						 * Se jalan los datos del comprobante para info del cliente
						 * */
						$rein['detalle'][$ij]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$item_det['comprobante']['_id']))->get('one')->items;
						if(substr($item_det['cuenta']['cod'],0,strlen($cta['cod']))==$cta['cod']){
							/*
							 * agregar los comprobantes
							 * */
							$mov['comprobantes'][] = $rein['detalle'][$ij]['comprobante']['_id'];
							$mov['entidades']['entidad'][] = $rein['detalle'][$ij]['comprobante']['cliente'];
						}
					}
					if(sizeof($mov['entidades']['entidad'])==1){
						$mov['entidades']['entidad'] = $mov['entidades']['entidad'][0];
						$enti_mov = $f->model('mg/entidad')->params(array('_id'=>$mov['entidades']['entidad']['_id']))->get('enti')->items;
						$mov['entidades']['tipo_doc'] = $enti_mov['docident'][0]['tipo'];
						$mov['entidades']['num_doc'] = $enti_mov['docident'][0]['num'];
						$mov['entidades']['nomb'] = $enti_mov['nomb'];
						if($enti_mov['tipo_enti']=='P'){
							$mov['entidades']['nomb'] .= ' '.$enti_mov['appat'].' '.$enti_mov['apmat'];;
						}
					}else{
						$mov['entidades']['tipo_doc'] = "-";
						$mov['entidades']['num_doc'] = "-";
						$mov['entidades']['nomb'] = "Varios";
					}
					/*
					 * Se procede a ingresar la descripcion de acuerdo a la cuenta contable
					 */
					for($ii = 0; $ii<sizeof($data['cont_patrimonial']); $ii++){
						if($data['cont_patrimonial'][$ii]['cuenta']['cod']==$cta['cod']){
							$mov['descr'] = $data['cont_patrimonial'][$ii]['descr'];
							$ii = sizeof($data['cont_patrimonial']);
						}
					}
					$f->model('ts/movcue')->params(array('data'=>$mov))->save('insert');
					/*
					 * Se incrementa el saldo deudor final
					 * P09_S05 Paso 17
					 */
					$f->model('ts/saldlibr')->params(array(
						'filter'=>array('_id'=>$saldo['_id']),
						'data'=>array('$inc'=>array('saldo_deudor_final'=>$mov['monto']))
					))->save('custom');
				}
			}
		}
		/*
		 * Si es que existieran vouchers, se realizan el P09_S05 proceso 31 
		 */
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $item){
				$mov_efe = array(
					'fecreg'=>new MongoDate(strtotime($item['fec'])),
					'estado'=>'P',
					'estado_sunat'=>'1',
					'cuenta_banco'=>$item['cuenta_banco'],
					'tipo'=>'H',
					'cuenta'=>$item['cuenta'],
					'monto'=>0
				);
				$mov_efe['cuenta_banco']['_id'] = new MongoId($mov_efe['cuenta_banco']['_id']);
				$mov_efe['cuenta']['_id'] = new MongoId($mov_efe['cuenta']['_id']);
				$saldo = $f->model('ts/saldlibr')->params(array('periodo'=>$periodo,'tipo'=>'E'))->get('rein')->items;
				$mov_efe['saldo'] = array(
					'_id'=>$saldo['_id'],
					'periodo'=>$saldo['periodo']
				);
				$mov_efe['periodo'] = $saldo['periodo'];
				$mov_efe['descr'] = 'Dep&oacute;sito '.$mov_efe['cuenta_banco']['nomb'].' Detracci&oacute;n IGV';
				/* Si solamente hay un voucher relacionado o varios */
				if(sizeof($item['docs'])==1){
					$mov_efe['tipo_doc'] = 'V';
					$mov_efe['num_doc'] = $item['docs'][0];
					$mov_efe['monto'] += floatval($item['monto'][0]);
				}else{
					$mov_efe['varios'] = 'Varios';
					foreach ($item['docs'] as $i=>$doc){
						$mov_efe['documentos'][] = array('documento'=>array(
							'tipo'=>'V',
							'num_doc'=>$doc
						));
						$mov_efe['monto'] += floatval($item['monto'][$i]);
					}
				}
				$f->model('ts/moef')->params(array('data'=>$mov_efe))->save('insert');
				$f->model('ts/saldlibr')->params(array(
					'filter'=>array('_id'=>$saldo['_id']),
					'data'=>array('$inc'=>array('saldo_acreedor_final'=>$mov_efe['monto']))
				))->save('custom');
				/*
				 * P09_S05 proceso 35
				 */
				$saldo = $f->model('ts/saldlibr')->params(array(
					'periodo'=>$periodo,
					'tipo'=>'C',
					'ctban'=>new MongoId($item['cuenta_banco']['_id'])
				))->get('rein')->items;
				$mov_cue = array(
					'fecreg'=>new MongoDate(strtotime($item['fec'])),
					'estado'=>'P',
					'estado_sunat'=>'1',
					'saldo'=>array(
						'_id'=>$saldo['_id'],
						'periodo'=>$saldo['periodo']
					),
					'periodo'=>$saldo['periodo'],
					'cuenta_banco'=>$item['cuenta_banco'],
					'id_operacion'=>$rein['_id'],
					'cod_operacion'=>'RI '.$rein['cod'],
					'medio_pago'=>$item['medio'],
					'documentos'=>array(
						'tipo'=>'V',
						'num'=>$item['docs'][0]
					),
					'documentos_libro'=>$item['docs'][0],
					'comprobantes'=>array(),
					'entidades'=>array(
						'entidad'
					),
					'tipo'=>'D',
					'cuenta'=>$item['cuenta'],
					'monto'=>floatval($mov_efe['monto']),
					'descr'=>$item['descr']
				);
				$mov_cue['cuenta_banco']['_id'] = new MongoId($mov_cue['cuenta_banco']['_id']);
				$mov_cue['cuenta']['_id'] = new MongoId($mov_cue['cuenta']['_id']);
				$mov_cue['medio_pago']['_id'] = new MongoId($mov_cue['medio_pago']['_id']);
				$mov_cue['entidades']['entidad'] = $item['entidad'];
				$enti_mov = $f->model('mg/entidad')->params(array('_id'=>$item['entidad']['_id']))->get('enti')->items;
				$mov_cue['entidades']['tipo_doc'] = $enti_mov['docident'][0]['tipo'];
				$mov_cue['entidades']['num_doc'] = $enti_mov['docident'][0]['num'];
				$mov_cue['entidades']['nomb'] = $enti_mov['nomb'];
				if($enti_mov['tipo_enti']=='P'){
					$mov_cue['entidades']['nomb'] .= ' '.$enti_mov['appat'].' '.$enti_mov['apmat'];;
				}
				$f->model('ts/movcue')->params(array('data'=>$mov_cue))->save('insert');
				/*
				 * Se incrementa el saldo deudor final
				 * P09_S05 Paso 37
				 */
				$f->model('ts/saldlibr')->params(array(
					'filter'=>array('_id'=>$saldo['_id']),
					'data'=>array('$inc'=>array('saldo_deudor_final'=>$mov_cue['monto']))
				))->save('custom');
			}
		}
		/*
		 * Se hace al final para comprobar que exista el saldo, este es el paso 1 del back P09_S05
		 * */
		$f->model('cj/rein')->params(array('_id'=>$data['_id'],'data'=>array(
			'estado'=>'RC',
			'registro_ctacte'=>array(
				'fec'=>$fec,
				'autor'=>$autor
			)
		)))->save('update');
		/*
		 * P09_S05 proceso 39
		 */
		$cjban = array(
			'fecreg'=>$rein['fecreg'],
			'periodo'=>array(
				'mes'=>$peri_mes,
				'ano'=>$peri_ano
			),
			'doc'=>'RI',
			'num_doc'=>$rein['cod'],
			'concepto'=>'RI de la Organizaci&oacute;n '.$rein['organizacion']['nomb'],
			'organizacion'=>$rein['organizacion'],
			'cuentas'=>array(),
			'debe'=>0,
			'haber'=>0
		);
		foreach ($rein['cont_patrimonial'] as $cont){
			if($cont['tipo']=='D') $cjban['debe'] += floatval($cont['monto']);
			else $cjban['haber'] += floatval($cont['monto']);
			$cjban['cuentas'][] = array(
				'tipo'=>$cont['tipo'],
				'cuenta'=>$cont['cuenta'],
				'monto'=>$cont['monto']
			);
		}
		$f->model('cj/cjban')->params(array('data'=>$cjban))->save('insert');
		/*
		 * P09_S05 proceso 41
		 */
		$cjban = array(
			'fecreg'=>$rein['fecreg'],
			'periodo'=>array(
				'mes'=>$peri_mes,
				'ano'=>$peri_ano
			),
			'doc'=>'Voucher',
			'organizacion'=>$rein['organizacion'],
			'cuentas'=>array(),
			'debe'=>0,
			'haber'=>0
		);
		foreach ($data['efectivo'] as $efec) {
			if($efec['tipo']=='S'){
				$cjban['num_doc'] = $efec['voucher'];
				$cjban['concepto'] = $efec['descr'];
			}
			$ctban = $f->model('ts/ctban')->params(array('_id'=>$efec['cuenta_banco']['_id']))->get('one')->items;
			$cjban['cuentas'][] = array(
				'tipo'=>'D',
				'cuenta'=>$ctban['cuenta'],
				'monto'=>floatval($efec['monto'])
			);
			$cjban['debe'] += floatval($efec['monto']);
		}
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $item){
				$item['cuenta']['_id'] = new MongoId($item['cuenta']['_id']);
				$cjban['cuentas'][] = array(
					'tipo'=>'D',
					'cuenta'=>$item['cuenta'],
					'monto'=>floatval($item['monto'][0])
				);
				$cjban['debe'] += floatval($item['monto'][0]);
			}
		}
		foreach ($rein['cont_patrimonial'] as $cont){
			if($cont['tipo']=='D'){
				$cjban['cuentas'][] = array(
					'tipo'=>'H',
					'cuenta'=>$cont['cuenta'],
					'monto'=>floatval($cont['monto'])
				);
				$cjban['haber'] += floatval($cont['monto']);
			}
		}
		$f->model('cj/cjban')->params(array('data'=>$cjban))->save('insert');
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/rein.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ts/rein.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/rein.details");
	}
	function execute_mov(){
		global $f;
		$f->response->view("ts/rein.mov");
	}
	function execute_cta(){
		global $f;
		$f->response->view("ts/rein.cta");
	}
}
?>