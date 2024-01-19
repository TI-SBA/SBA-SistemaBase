<?php
class Controller_pe_bole extends Controller {
	function execute_lista(){
		global $f,$helper;
		$model = $f->model("pe/docs")->params(array(
			'filter'=>array(
				'boletas'=>array('$exists'=>true),
				'contrato.cod'=>$f->request->tipo,
				'periodo.ano'=>$f->request->ano,
				'periodo.mes'=>$f->request->mes
			),
			'fields'=>array(
				'estado'=>true,
				'trabajador'=>true,
				'total_pago'=>true,
				'total_desc'=>true,
				'total'=>true,
				'ult_autor'=>true,
				'fecmod'=>true
			),
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_export(){
		global $f;
		$model = $f->model("pe/docs")->params(array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->periodo,
			"tipo"=>$f->request->tipo
		))->get("lista_bole_export");
		$model->mes=$f->request->mes;
		$model->ano=$f->request->periodo;
		$model->tipo=$f->request->tipo;
		$f->response->view("pe/plan.export",$model );
	}
	function execute_print(){
		global $f;
		$model = $f->model("pe/docs")->params(array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->periodo,
			"tipo"=>$f->request->tipo
		))->get("lista_bole_export");
		$model->mes=$f->request->mes;
		$model->ano=$f->request->periodo;
		$model->tipo=$f->request->tipo;
		$f->response->view("pe/plan.print",$model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"boletas",
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/docs')->get('all');
		$f->response->json($model->items);
	}
	function execute_all_planilla(){
		global $f;
		$model = $f->model('pe/docs')->params(array('planilla._id'=>new MongoId($f->request->data['_id'])))->get('all');
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model('mg/entidad')->params(array('_id'=>$item['trabajador']['_id']))->get('one')->items;
			}
		}
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$model->items['trabajador'] = $f->model("mg/entidad")->params(array("_id"=>$model->items['trabajador']['_id']))->get("one")->items;
		$model->items['trabajador']['orga'] = $f->model("mg/orga")->params(array("_id"=>$model->items['trabajador']['roles']['trabajador']['organizacion']['_id']))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_conso(){
		global $f;
		$model = $f->model("pe/docs")->params(array(
			'filter'=>array(
				'contrato.cod'=>$f->request->data['tipo'],
				'estado'=>array('$ne'=>'X'),
				'periodo.mes'=>$f->request->data['mes'],
				'periodo.ano'=>$f->request->data['ano']
			)
		))->get("lista_all");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model->items );
	}
	function execute_trab_per(){
		global $f;
		$vars = array();
		$model = $f->model("mg/vari")->params(array("fields"=>array(
			'cod'=>true,
			'nomb'=>true,
			'valor'=>true
		)))->get("all");
		foreach ($model->items as $item){
			$vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
		}
		$pago = array();
		$desc = array();
		$apor = array();
		$bono = array();
		$params = array(
			'doc'=>$f->request->data['doc'],
			'fields'=>array(
				'cod'=>true,
				'nomb'=>true,
				'formula'=>true,
				'tipo'=>true,
				'clasif'=>true,
				'cuenta'=>true,
				'filtro'=>true,
				'imprimir'=>true,
				'planilla'=>true
			)
		);
		if(isset($f->request->data['tipo'])) $params['tipo'] = $f->request->data['tipo'];
		$model = $f->model("pe/conc")->params($params)->get("all");
		if($model->items!=null){
			foreach ($model->items as $item){
				$conc_tmp = array(
					'_id'=>$item['_id'],
					'cod'=>$item['cod'],
					'descr'=>$item['nomb'],
					'formula'=>$item['formula'],
					'imprimir'=>$item['imprimir'],
					'planilla'=>$item['planilla'],
					'tipo'=>$item['tipo']
				);
				if(isset($item['filtro'])) $conc_tmp['filtro'] = $item['filtro'];
				if(isset($item['clasif'])) $conc_tmp['clasif'] = $item['clasif'];
				if(isset($item['cuenta'])) $conc_tmp['cuenta'] = $item['cuenta'];
				switch ($item['tipo']){
					case 'P': $pago[] = $conc_tmp; break;
					case 'D': $desc[] = $conc_tmp; break;
					case 'A': $apor[] = $conc_tmp; break;
				}
			}
		}
		/*
		 * obtener si el trabajador tiene bonos
		 */
		$trab = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['enti'])))->get("one")->items;
		$fich = $f->model("pe/fich")->params(array("_id"=>$trab['_id']))->get("enti")->items;
		if(isset($trab['roles']['trabajador']['bonos'])){
			foreach ($trab['roles']['trabajador']['bonos'] as $bon){
				$bono[] = $bon;
			}
		}
		$f->response->json(array(
			'pago'=>$pago,
			'descuento'=>$desc,
			'aporte'=>$apor,
			'vars'=>$vars,
			'bono'=>$bono
		));
	}
	function dias_agui_fi($ini,$perm){
		$total = 90;
		if($ini>(new MongoDate(strtotime(date('Y').'-04-01')))){
			//date('Y-M-d h:i:s', $ini->sec);
			$mes_ini = intval(date('m',$ini->sec));
			
			$total = 30 * (6-$mes_ini);
		}
		$total -= $perm;
		return $total;
	}
	function dias_agui_na($ini,$perm){
		$total = 90;
		if($ini<(new MongoDate(strtotime(date('Y').'-09-01')))){
			//date('Y-M-d h:i:s', $ini->sec);
			$mes_ini = intval(date('m',$ini->sec));
			$total = 30 * (6-$mes_ini);
		}
		$total -= $perm;
		return $total;
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['ult_autor'] = $f->session->userDB;
		if(isset($data['contrato']['_id'])) $data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['trabajador']['_id'])) $data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['trabajador']['cargo']['_id'])) $data['trabajador']['cargo']['_id'] = new MongoId($data['trabajador']['cargo']['_id']);
		if(isset($data['trabajador']['cargo']['organizacion']['_id'])) $data['trabajador']['cargo']['organizacion']['_id'] = new MongoId($data['trabajador']['cargo']['organizacion']['_id']);
		if(isset($data['periodo']['inicio'])) $data['periodo']['inicio'] = new MongoDate(strtotime($data['periodo']['inicio']));
		if(isset($data['periodo']['fin'])) $data['periodo']['fin'] = new MongoDate(strtotime($data['periodo']['fin']));
		if(isset($data['conceptos'])){
			foreach ($data['conceptos'] as $i=>$con){
				if(isset($con['concepto']['_id'])){
					if($con['concepto']['_id']!='')
						$data['conceptos'][$i]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
				}
				if(isset($con['concepto']['clasificador']['_id']))
					$data['conceptos'][$i]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
				if(isset($con['concepto']['cuenta']['_id']))
					$data['conceptos'][$i]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);

				if(isset($con['concepto']['imprimir'])){
					$data['conceptos'][$i]['concepto']['imprimir'] = floatval($con['concepto']['imprimir']);
				}else{
					$data['conceptos'][$i]['concepto']['imprimir'] = 0;
				}
				if(isset($con['concepto']['planilla'])){
					$data['conceptos'][$i]['concepto']['planilla'] = floatval($con['concepto']['planilla']);
				}else{
					$data['conceptos'][$i]['concepto']['planilla'] = 0;
				}
			}
		}
		if(!isset($f->request->data['_id'])){
			//CODIGO DE BOLETA
			$cod = $f->model("pe/docs")->params(array('filter'=>array('boletas'=>array('$exists'=>true))))->get("cod");
			if($cod->items==null) $cod->items="001000";
			else{
				$tmp = intval($cod->items);
				$tmp++;
				$tmp = (string)$tmp;
				for($i=strlen($tmp); $i<6; $i++){
					$tmp = '0'.$tmp;
				}
				$cod->items = $tmp;
			}
			$data['cod'] = $cod->items;
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'R';
			$data['boletas'] = true;
			$data['autor'] = $f->session->userDB;
			$f->model("pe/docs")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Planillas',
				'descr'=>'Se cre&oacute; una <b>Boleta de Pago</b> para el trabajador <b>'.
					$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> '.
					'correspondiente al periodo '.$data['periodo']['ano'].'-'.$data['periodo']['mes']
			))->save('insert');
		}else{
			$f->model("pe/docs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_generar_pago(){
		global $f;
		$data = $f->request->data;
		$fec = new MongoDate();
		$autor = $f->session->userDB;
		$boletas = $f->model("pe/docs")->params(array(
			'filter'=>array(
				'contrato.cod'=>$data['tipo'],
				'estado'=>'R',
				'periodo.mes'=>$data['mes'],
				'periodo.ano'=>$data['ano'],
				'boletas'=>array('$exists'=>true)
			)
		))->get("lista_all")->items;
		if($boletas!=null){
			$orga = array();
			$orgs = array();
			foreach ($boletas as $bole){
				$f->model('pe/docs')->params(array(
					'_id'=>$bole['_id'],
					'data'=>array(
						'estado'=>'P'
					)
				))->save('update');
				$index = array_search($bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['actividad']['_id']->{'$id'}, $orgs);
				if($index===false){
					$orgs[] = $bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['actividad']['_id']->{'$id'};
					$orga[] = array($bole);
				}else{
					$orga[$index][] = $bole;
				}
			}
			foreach ($orga as $org){
				$org[0]['trabajador'] = $f->model('mg/entidad')->params(array('_id'=>$org[0]['trabajador']['_id']))->get('one')->items;
				$cpp = array(
					'fecreg'=>$fec,
					'modulo'=>'PE',
					'estado'=>'P',
					'origen'=>'P',
					'autor'=>$autor,
					'motivo'=>'Planillas '.$data['tipo'].' de '.$org[0]['trabajador']['roles']['trabajador']['cargo']['organizacion']['actividad']['nomb'].' - Periodo: '.$data['ano'].'-'.$data['mes'],
					'documentos'=>array(),
					'conceptos'=>array(),
					'afectacion'=>array(),
					'total'=>0,
					'total_pago'=>0,
					'total_desc'=>0
				);
				$cpp_desc = array();
				$desc = array();
				$cpp_apor = array();
				$apor = array();
				$concs = array();
				$clas = array();
				$orgs = array();
				foreach ($org as $bole){
					$cpp['documentos'][] = $bole['_id'];
					foreach ($bole['conceptos'] as $conc){
						//print_r($conc);die();
						//$f->response->print($conc['concepto']['tipo']);
						if(floatval($conc['subtotal'])!=0){
							$index = array_search($conc['concepto']['nomb'],$concs);
							if($index===false){
								$concs[] = $conc['concepto']['nomb'];
								$tipo_tmp = $conc['concepto']['tipo'];
								if($tipo_tmp=='A') $tipo_tmp = 'D';
								$cpp['conceptos'][] = array(
									'tipo'=>$tipo_tmp,
									'observ'=>$conc['concepto']['nomb'],
									'monto'=>floatval($conc['subtotal']),
									'moneda'=>'S',
									'concepto'=>$conc['concepto'],
									'modulo'=>'PE'
								);
								if($conc['concepto']['tipo']=='P'){
									$index_org = array_search($bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']->{'$id'},$orgs);
									if($index_org===false){
										$orgs[] = $bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']->{'$id'};
										$tmp = $f->model('mg/orga')->params(array('_id'=>$bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']))->get('one')->items;
										$cpp['afectacion'][] = array(
											'organizacion'=>array(
												'_id'=>$tmp['_id'],
												'nomb'=>$tmp['nomb'],
												'actividad'=>array(
													'_id'=>$tmp['actividad']['_id'],
													'nomb'=>$tmp['actividad']['nomb'],
													'cod'=>$tmp['actividad']['cod']
												),
												'componente'=>array(
													'_id'=>$tmp['componente']['_id'],
													'nomb'=>$tmp['componente']['nomb'],
													'cod'=>$tmp['componente']['cod']
												)
											),
											'monto'=>floatval($conc['subtotal'])
										);
									}
								}
							}else{
								$cpp['conceptos'][$index]['monto'] += floatval($conc['subtotal']);
							}
							if($conc['concepto']['tipo']=='P'){
								$index_org = array_search($bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']->{'$id'},$orgs);
								if(isset($conc['concepto']['clasificador'])){
									$index = array_search($conc['concepto']['clasificador']['_id']->{'$id'}, $clas);
									if($index===false){
										$clas[] = $conc['concepto']['clasificador']['_id']->{'$id'};
										$cpp['afectacion'][$index_org]['gasto'][] = array(
											'clasificador'=>$conc['concepto']['clasificador'],
											'monto'=>floatval($conc['subtotal'])
										);
										$cpp['afectacion'][$index_org]['monto'] += floatval($conc['subtotal']);
									}else{
										$cpp['afectacion'][$index_org]['gasto'][$index]['monto'] += floatval($conc['subtotal']);
										$cpp['afectacion'][$index_org]['monto'] += floatval($conc['subtotal']);
									}
								}
							}
							if($conc['concepto']['tipo']=='P'){
								$cpp['total_pago'] += floatval($conc['subtotal']);
								$cpp['total'] += floatval($conc['subtotal']);
							}elseif($conc['concepto']['tipo']=='D'){
								$cpp['total'] -= floatval($conc['subtotal']);
								$cpp['total_desc'] += floatval($conc['subtotal']);
							}elseif($conc['concepto']['tipo']=='A'){
								$cpp['total'] -= floatval($conc['subtotal']);
								$cpp['total_desc'] += floatval($conc['subtotal']);
							}
							/*
							 * Si es un concepto de tipo descuento
							 * generar una cuenta por pagar
							 * o actualizar la ya creada
							 * */
							if($conc['concepto']['tipo']=='D'){
								$index = array_search($conc['concepto']['_id']->{'$id'}, $desc);
								if($index===false){
									$desc[] = $conc['concepto']['_id']->{'$id'};
									$tmp = $f->model('pe/conc')->params(array('_id'=>$conc['concepto']['_id']))->get('one')->items;
									if(is_array($tmp['beneficiario'])){
										$cpp_desc[] = array(
											'fecreg'=>$fec,
											'estado'=>'P',
											'modulo'=>'PE',
											'origen'=>'D',
											'autor'=>$autor,
											'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
											'beneficiario'=>$tmp['beneficiario'],
											'conceptos'=>array(array(
												'tipo'=>'P',
												'observ'=>$conc['concepto']['nomb'],
												'monto'=>floatval($conc['subtotal']),
												'moneda'=>'S',
												'concepto'=>$conc['concepto'],
												'modulo'=>'PE'
											)),
											'total'=>floatval($conc['subtotal']),
											'total_pago'=>floatval($conc['subtotal']),
											'total_desc'=>0
										);
									}else{
										$trab = $f->model('mg/entidad')->params(array('_id'=>$bole['trabajador']['_id']))->get('one')->items;
										if($tmp['beneficiario']=='RJ'){
											foreach ($trab['roles']['trabajador']['retencion'] as $reten){
												$cpp_desc[] = array(
													'fecreg'=>$fec,
													'estado'=>'P',
													'modulo'=>'PE',
													'origen'=>'D',
													'autor'=>$autor,
													'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
													'beneficiario'=>$reten['entidad'],
													'conceptos'=>array(array(
														'tipo'=>'P',
														'observ'=>$conc['concepto']['nomb'],
														'monto'=>floatval($conc['subtotal']),
														'moneda'=>'S',
														'concepto'=>$conc['concepto'],
														'modulo'=>'PE'
													)),
													'total'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_pago'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_desc'=>0
												);
											}
										}elseif($tmp['beneficiario']=='AFP'){
											$afp = $f->model('pe/sist')->params(array('_id'=>$trab['roles']['trabajador']['pension']['_id']))->get('one')->items;
											$cpp_desc[] = array(
												'fecreg'=>$fec,
												'estado'=>'P',
												'origen'=>'D',
												'modulo'=>'PE',
												'autor'=>$autor,
												'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
												'beneficiario'=>$afp['entidad'],
												'conceptos'=>array(array(
													'tipo'=>'P',
													'observ'=>$conc['concepto']['nomb'],
													'monto'=>floatval($conc['subtotal']),
													'moneda'=>'S',
													'concepto'=>$conc['concepto'],
													'modulo'=>'PE'
												)),
												'total'=>floatval($conc['subtotal']),
												'total_pago'=>floatval($conc['subtotal']),
												'total_desc'=>0
											);
										}
									}
								}else{
									$cpp_desc[$index_org]['total'] += floatval($conc['subtotal']);
									$cpp_desc[$index_org]['total_pago'] += floatval($conc['subtotal']);
								}
							}
							/*
							 * Si es un concepto de tipo aporte
							 * generar una cuenta por pagar
							 * o actualizar la ya creada
							 * */
							if($conc['concepto']['tipo']=='A'){
								$index = array_search($conc['concepto']['_id']->{'$id'}, $apor);
								if($index===false){
									$apor[] = $conc['concepto']['_id']->{'$id'};
									$tmp = $f->model('pe/conc')->params(array('_id'=>$conc['concepto']['_id']))->get('one')->items;
									if(is_array($tmp['beneficiario'])){
										$cpp_apor[] = array(
											'fecreg'=>$fec,
											'estado'=>'P',
											'origen'=>'D',
											'modulo'=>'PE',
											'autor'=>$autor,
											'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
											'beneficiario'=>$tmp['beneficiario'],
											'conceptos'=>array(array(
												'tipo'=>'P',
												'observ'=>$conc['concepto']['nomb'],
												'monto'=>floatval($conc['subtotal']),
												'moneda'=>'S',
												'concepto'=>$conc['concepto'],
												'modulo'=>'PE'
											)),
											'total'=>floatval($conc['subtotal']),
											'total_pago'=>floatval($conc['subtotal']),
											'total_desc'=>0
										);
									}else{
										$trab = $f->model('mg/entidad')->params(array('_id'=>$bole['trabajador']['_id']))->get('one')->items;
										if($tmp['beneficiario']=='RJ'){
											foreach ($trab['roles']['trabajador']['retencion'] as $reten){
												$cpp_apor[] = array(
													'fecreg'=>$fec,
													'estado'=>'P',
													'modulo'=>'PE',
													'origen'=>'D',
													'autor'=>$autor,
													'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
													'beneficiario'=>$reten['entidad'],
													'conceptos'=>array(array(
														'tipo'=>'P',
														'observ'=>$conc['concepto']['nomb'],
														'monto'=>floatval($conc['subtotal']),
														'moneda'=>'S',
														'concepto'=>$conc['concepto'],
														'modulo'=>'PE'
													)),
													'total'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_pago'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_desc'=>0
												);
											}
										}elseif($tmp['beneficiario']=='AFP'){
											$afp = $f->model('pe/sist')->params(array('_id'=>$trab['roles']['trabajador']['pension']['_id']))->get('one')->items;
											$cpp_apor[] = array(
												'fecreg'=>$fec,
												'estado'=>'P',
												'modulo'=>'PE',
												'origen'=>'D',
												'autor'=>$autor,
												'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
												'beneficiario'=>$afp['entidad'],
												'conceptos'=>array(array(
													'tipo'=>'P',
													'observ'=>$conc['concepto']['nomb'],
													'monto'=>floatval($conc['subtotal']),
													'moneda'=>'S',
													'concepto'=>$conc['concepto'],
													'modulo'=>'PE'
												)),
												'total'=>floatval($conc['subtotal']),
												'total_pago'=>floatval($conc['subtotal']),
												'total_desc'=>0
											);
										}
									}
								}else{
									$cpp_apor[$index_org]['total'] += floatval($conc['subtotal']);
									$cpp_apor[$index_org]['total_pago'] += floatval($conc['subtotal']);
								}
							}
						}
					}
				}
				//$f->response->json($cpp_desc);
				
				
				
				
				
				
				
				
				
				/*$f->response->json($cpp);
				die();*/
				
				
				
				
				
				
				
				
				foreach ($cpp_desc as $cta){
					$f->model('ts/ctpp')->params(array('data'=>$cta))->save('insert');
				}
				foreach ($cpp_apor as $cta){
					$f->model('ts/ctpp')->params(array('data'=>$cta))->save('insert');
				}
				$f->model('ts/ctpp')->params(array('data'=>$cpp))->save('insert');
			}
			$f->response->print("true");
		}else $f->response->json(array());
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Planillas',
			'descr'=>'Se realiza el pago de las Boletas de Trabajador del periodo <b>'.$data['ano'].'-'.$data['mes'].'</b>.'
		))->save('insert');
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/bole.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("pe/bole.details");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/bole.select");
	}
	function execute_conso(){
		global $f;
		$f->response->view("pe/bole.conso");
	}
	function execute_print_bole(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items['trabajador'] = $f->model("mg/entidad")->params(array("_id"=>$model->items['trabajador']['_id']))->get("one")->items;
		$model->items['trabajador']['orga'] = $f->model("mg/orga")->params(array("_id"=>$model->items['trabajador']['roles']['trabajador']['organizacion']['_id']))->get("one")->items;
		if(isset($model->items['trabajador']['roles']['trabajador']['ficha'])){
			$model->items['trabajador']['roles']['trabajador']['ficha'] = $f->model("pe/fich")->params(array("_id"=>new MongoId($model->items['trabajador']['_id'])))->get("enti")->items;
		}
		if($model->items['contrato']['cod']=="276"){
			$f->response->view("pe/bole.print",$model);
		}else{
			$f->response->view("pe/bole.cas.print",$model);
		}
	}
	function execute_print_bole_lote(){
		global $f;
		$data = $f->request->data;
		$params = array(
			'planilla._id'=>new MongoId($data['_id'])
		);
		$model = $f->model('pe/docs')->params($params)->get('all');
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				//$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array("_id"=>$item['trabajador']['_id']))->get("one")->items;
				/*$model->items[$i]['trabajador']['orga'] = $f->model("mg/orga")->params(array("_id"=>$item['trabajador']['roles']['trabajador']['organizacion']['_id']))->get("one")->items;*/
				if(isset($item['trabajador']['roles']['trabajador']['ficha'])){
					$model->items[$i]['trabajador']['roles']['trabajador']['ficha'] = $f->model("pe/fich")->params(array("_id"=>new MongoId($item['trabajador']['_id'])))->get("enti")->items;
				}
			}
		}
		/*print_r($model->items);
		die();*/
		$f->response->view('pe/bole.lote.print', $model);
	}
	function execute_upload_planillas(){
		global $f;
		global $f;
		set_time_limit(0);
		echo 'RECONOCIENDO CSV<br />';
		$fp = fopen ( "mov_hsp.csv", "r" );
		$cod = 0;
		$conf = $f->model('cj/conf')->params(array('cod'=>'HO'))->get('cod')->items;
		$modalidades = array(
			'mes'=>'M',
			'dia'=>'D',
			'día'=>'D'
		);
		echo 'INICIANDO PROCESO DE IMPORTACION! <br />';
		while ($data = fgetcsv ($fp, 1000, ";")){
			if($cod!=0){
				$codpla = utf8_encode(trim($data[0]));
				$codpro = utf8_encode(trim($data[1]));
				$sittra = utf8_encode(trim($data[2]));
				$vacaci = utf8_encode(trim($data[3]));
				$boladi = utf8_encode(trim($data[4]));
				$codact = utf8_encode(trim($data[5]));
				$codcom = utf8_encode(trim($data[6]));
				$coduni = utf8_encode(trim($data[7]));
				$jefmed = utf8_encode(trim($data[8]));
				$cartra = utf8_encode(trim($data[9]));
				$carips = utf8_encode(trim($data[10]));
				$cattra = utf8_encode(trim($data[11]));
				$carafp = utf8_encode(trim($data[12]));
				$fecing = utf8_encode(trim($data[13]));
				$fecces = utf8_encode(trim($data[14]));
				$libele = utf8_encode(trim($data[15]));
				$apenom = utf8_encode(trim($data[16]));
				$diatra = utf8_encode(trim($data[17]));
				$dt_ess = utf8_encode(trim($data[18]));
				$suebas = utf8_encode(trim($data[19]));
				$suebas_a = utf8_encode(trim($data[20]));
				$remreu = utf8_encode(trim($data[21]));
				$remesp = utf8_encode(trim($data[22]));
				$trahom = utf8_encode(trim($data[23]));
				$dl2567 = utf8_encode(trim($data[24]));
				$subfam = utf8_encode(trim($data[25]));
				$remper = utf8_encode(trim($data[26]));
				$rd1rm8 = utf8_encode(trim($data[27]));
				$refmov = utf8_encode(trim($data[28]));
				$dl2569 = utf8_encode(trim($data[29]));
				$nivsal = utf8_encode(trim($data[30]));
				$dp0659 = utf8_encode(trim($data[31]));
				$ds2598 = utf8_encode(trim($data[32]));
				$guaord = utf8_encode(trim($data[33]));
				$guaext = utf8_encode(trim($data[34]));
				$reinte = utf8_encode(trim($data[35]));
				$ncurei = utf8_encode(trim($data[36]));
				$encarg = utf8_encode(trim($data[37]));
				$cfirei = utf8_encode(trim($data[38]));
				$reint1 = utf8_encode(trim($data[39]));
				$dl2510 = utf8_encode(trim($data[40]));
				$dl2503 = utf8_encode(trim($data[41]));
				$ds1994 = utf8_encode(trim($data[42]));
				$ds0288 = utf8_encode(trim($data[43]));
				$du0809 = utf8_encode(trim($data[44]));
				$du1189 = utf8_encode(trim($data[45]));
				$du0909 = utf8_encode(trim($data[46]));
				$du0989 = utf8_encode(trim($data[47]));
				$dl5599 = utf8_encode(trim($data[48]));
				$l26504 = utf8_encode(trim($data[49]));
				$du0739 = utf8_encode(trim($data[50]));
				$du0119 = utf8_encode(trim($data[51]));
				$totase = utf8_encode(trim($data[52]));
				$totapa = utf8_encode(trim($data[53]));
				$ds276 = utf8_encode(trim($data[54]));
				$rds276 = utf8_encode(trim($data[55]));
				$ssp276 = utf8_encode(trim($data[56]));
				$ncur27 = utf8_encode(trim($data[57]));
				$cfir27 = utf8_encode(trim($data[58]));
				$totpag = utf8_encode(trim($data[59]));
				$codafp = utf8_encode(trim($data[60]));
				$snptra = utf8_encode(trim($data[61]));
				$jcmtra = utf8_encode(trim($data[62]));
				$ipsvid = utf8_encode(trim($data[63]));
				$impren = utf8_encode(trim($data[64]));
				$retjud = utf8_encode(trim($data[65]));
				$resfis = utf8_encode(trim($data[66]));
				$ncufis = utf8_encode(trim($data[67]));
				$cfifis = utf8_encode(trim($data[68]));
				$descon = utf8_encode(trim($data[69]));
				$otrdes = utf8_encode(trim($data[70]));
				$pdeafp = utf8_encode(trim($data[71]));
				$ncuode = utf8_encode(trim($data[72]));
				$cfiode = utf8_encode(trim($data[73]));
				$cuocaf = utf8_encode(trim($data[74]));
				$bcocon = utf8_encode(trim($data[75]));
				$ncucon = utf8_encode(trim($data[76]));
				$cficon = utf8_encode(trim($data[77]));
				$bcoco1 = utf8_encode(trim($data[78]));
				$bcosur = utf8_encode(trim($data[79]));
				$ncusur = utf8_encode(trim($data[80]));
				$cfisur = utf8_encode(trim($data[81]));
				$bcocre = utf8_encode(trim($data[82]));
				$ncucre = utf8_encode(trim($data[83]));
				$cficre = utf8_encode(trim($data[84]));
				$bcosol = utf8_encode(trim($data[85]));
				$ncusol = utf8_encode(trim($data[86]));
				$cfisol = utf8_encode(trim($data[87]));
				$ememed = utf8_encode(trim($data[88]));
				$creoje = utf8_encode(trim($data[89]));
				$ncuoje = utf8_encode(trim($data[90]));
				$cfioje = utf8_encode(trim($data[91]));
				$desodo = utf8_encode(trim($data[92]));
				$ncuodo = utf8_encode(trim($data[93]));
				$cfiodo = utf8_encode(trim($data[94]));
				$preadm = utf8_encode(trim($data[95]));
				$ncuadm = utf8_encode(trim($data[96]));
				$cfiadm = utf8_encode(trim($data[97]));
				$apspp = utf8_encode(trim($data[98]));
				$priseg = utf8_encode(trim($data[99]));
				$compor = utf8_encode(trim($data[100]));
				$glosa0 = utf8_encode(trim($data[101]));
				$glosa1 = utf8_encode(trim($data[102]));
				$glosa2 = utf8_encode(trim($data[103]));
				$glosa3 = utf8_encode(trim($data[104]));
				$totdes = utf8_encode(trim($data[105]));
				$ssopat = utf8_encode(trim($data[106]));
				$fonpat = utf8_encode(trim($data[107]));
				$totpat = utf8_encode(trim($data[108]));
				$totnet = utf8_encode(trim($data[109]));
				$segsoc = utf8_encode(trim($data[110]));
				$nroimp = utf8_encode(trim($data[111]));
				$ttr = utf8_encode(trim($data[112]));
				$sa = utf8_encode(trim($data[113]));
				$sc = utf8_encode(trim($data[114]));
				$ac = utf8_encode(trim($data[115]));
				$st = utf8_encode(trim($data[116]));
				$precaf = utf8_encode(trim($data[117]));
				$ncupca = utf8_encode(trim($data[118]));
				$cfipca = utf8_encode(trim($data[119]));
				$deszap = utf8_encode(trim($data[120]));
				$ncuzap = utf8_encode(trim($data[121]));
				$cfizap = utf8_encode(trim($data[122]));
				$ctazap = utf8_encode(trim($data[123]));
				$itfzap = utf8_encode(trim($data[124]));
				$bcowie = utf8_encode(trim($data[125]));
				$ncuwie = utf8_encode(trim($data[126]));
				$cfiwie = utf8_encode(trim($data[127]));
				$itfwie = utf8_encode(trim($data[128]));
				$ctawie = utf8_encode(trim($data[129]));
				$bcoint = utf8_encode(trim($data[130]));
				$ncuint = utf8_encode(trim($data[131]));
				$cfiint = utf8_encode(trim($data[132]));
				$ncrint = utf8_encode(trim($data[133]));
				$glosa4 = utf8_encode(trim($data[134]));
				$kelsho = utf8_encode(trim($data[135]));
				$ncukel = utf8_encode(trim($data[136]));
				$cfikel = utf8_encode(trim($data[137]));
				$segrim = utf8_encode(trim($data[138]));
				$bondif = utf8_encode(trim($data[139]));
				$escola = utf8_encode(trim($data[140]));
				$codofi = utf8_encode(trim($data[141]));
				$codufi = utf8_encode(trim($data[142]));
				$susnom = utf8_encode(trim($data[143]));
				$glosa5 = utf8_encode(trim($data[144]));
				$boncre = utf8_encode(trim($data[145]));
				$retjub = utf8_encode(trim($data[146]));
				$dev073 = utf8_encode(trim($data[147]));
				$faltas = utf8_encode(trim($data[148]));
				$tardan = utf8_encode(trim($data[149]));
				$perpar = utf8_encode(trim($data[150]));
				$subess = utf8_encode(trim($data[151]));
				$ncusub = utf8_encode(trim($data[152]));
				$cfisub = utf8_encode(trim($data[153]));
				$coling = utf8_encode(trim($data[154]));
				$tarda2 = utf8_encode(trim($data[155]));
				$multas = utf8_encode(trim($data[156]));
				$perpa2 = utf8_encode(trim($data[157]));
				$falta2 = utf8_encode(trim($data[158]));
				$dev011 = utf8_encode(trim($data[159]));
				$glosa6 = utf8_encode(trim($data[160]));
				$glosa7 = utf8_encode(trim($data[161]));
				$itfint = utf8_encode(trim($data[162]));
				$oralas = utf8_encode(trim($data[163]));
				$ncuora = utf8_encode(trim($data[164]));
				$cfiora = utf8_encode(trim($data[165]));
				$du0379 = utf8_encode(trim($data[166]));
				$ncu037 = utf8_encode(trim($data[167]));
				$cfi037 = utf8_encode(trim($data[168]));
				$ddu037 = utf8_encode(trim($data[169]));
				$rednet = utf8_encode(trim($data[170]));
				$pagnet = utf8_encode(trim($data[171]));
				$glosa8 = utf8_encode(trim($data[172]));
				$glosa9 = utf8_encode(trim($data[173]));
				$glosa10 = utf8_encode(trim($data[174]));
				$glosa11 = utf8_encode(trim($data[175]));
				$glosa12 = utf8_encode(trim($data[176]));
				$glosa13 = utf8_encode(trim($data[177]));
				$glosa14 = utf8_encode(trim($data[178]));
				$bonesc = utf8_encode(trim($data[179]));
				$agufpa = utf8_encode(trim($data[180]));
				$agunav = utf8_encode(trim($data[181]));
				$fecnac = utf8_encode(trim($data[182]));
				$reiina = utf8_encode(trim($data[183]));
				$commix = utf8_encode(trim($data[184]));
				$afpmix = utf8_encode(trim($data[185]));
				$contra = utf8_encode(trim($data[186]));
				$curcaf = utf8_encode(trim($data[187]));
				$intd37 = utf8_encode(trim($data[188]));
				$trabajador = $f->datastore->mg_entidades->findOne(array( 'docident.num'=>$libele));

				if($trabajador!=null){
					$boleta = array(
						"_id"=>new MongoId(),
						"fecreg"=>new MongoDate(),
						"fecmod"=>new MongoDate(),
						"nomb"=>""
					);
				}

				if($trabajador==null){
					$trabajador = array(
						'_id'=>new MongoId(),
						'fecreg'=>new MongoDate(),
						'nomb'=>$nomb,
						'appat'=>$appat,
						'apmat'=>$apmat,
						'fullname'=>$nomb.' '.$appat.' '.$apmat,
						'docident'=>array(
							array('tipo'=>'DNI','num'=>'')
						),
						'roles'=>array(
							'paciente'=>array(
								'centro'=>'MH',
								'hist_cli'=>$hcl_pac,
								'categoria'=>$cat_tar
							)
						),
						'tipo_enti'=>'P',
						'import_date'=>date('Y-m-d'),
						'import_script'=>'ho/repo/import_hopi'
					);
					$f->datastore->mg_entidades->insert($trabajador);
					echo 'SE CREO CORRECTAMETRE UN NUEVO TRABAJADOR <BR />';
				}
			}
			$cod++;
		}
		fclose($fp);
		echo 'LA IMPORTACIÓN SE HA REALIZADO CON EXITO! <br />';
	}
}
?>