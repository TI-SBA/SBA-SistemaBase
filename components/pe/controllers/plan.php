<?php
class Controller_pe_plan extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("pe/plan")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("pe/plan")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_all(){
		global $f;
		$items = $f->model("pe/plan")->get("all")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$rpta = array(
			'status'=>'error',
			'message'=>'Han ocurrido algunos errores al procesar su solicitud',
			'rpta'=>null
		);
		$error = $this->save_validate($data);
		if($error==null){
			$data['fecmod'] = new MongoDate();
			$data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
			$data['trabajador'] = $f->session->userDBMin;
			$data['fecini'] = new MongoDate(strtotime($data['fecini']));
			$data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
			$data['periodo']['sort'] = floatval($data['periodo']['sort']);
			$data['conceptos'] = array();
			$params = array(
				'contrato'=>$data['contrato']['_id'],
				'fields'=>array(
					'cod'=>true,
					'nomb'=>true,
					'tipo'=>true,
					'imprimir'=>true,
					'planilla'=>true
				)
			);
			$conceptos = $f->model("pe/conc")->params($params)->get("all");
			if($conceptos->items!=null){
				foreach($conceptos->items as $concepto){
					$data['conceptos'][] = $concepto;
				}
			}

			if(!isset($f->request->data['_id'])){
				$data['fecreg'] = new MongoDate();
				$data['autor'] = $f->session->userDBMin;
				$data['estado'] = 'C';
				$model = $f->model("pe/plan")->params(array('data'=>$data))->save("insert")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Planilla',
					'descr'=>'Se creó la planilla <b>'.$data['nomb'].'</b>.'
				))->save('insert');

				$rpta['status'] = 'success';
				$rpta['message'] = 'El registro fue creado correctamente';
				$rpta['rpta'] = $data;
			}else{
				$vari = $f->model("pe/plan")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Planilla',
					'descr'=>'Se actualizó la planilla <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
				$model = $f->model("pe/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
				$rpta['status'] = 'success';
				$rpta['message'] = 'El registro fue modificado correctamente';
				$rpta['rpta'] = $data;
			}
		}else{
			$rpta['status'] = 'error';
			$rpta['message'] = $error;
			$rpta['rpta'] = null;
		}
		$f->response->json($rpta);
	}
	function save_validate($data){
		$error = null;
		if(!isset($data['contrato']['_id'])){
			$error = "Es necesario ingresar el contrato de la planilla";
			return $error;
		}
		if(!isset($data['periodo']['ano'])){
			$error = "Es necesario ingresar el anho del periodo";
			return $error;
		}else{
			if(strlen($data['periodo']['ano'])!=4){
				$error = "El formato del anho del periodo es incorrecto";
				return $error;
			}
		}
		if(!isset($data['fecini'])){
			$error = "Es necesario ingresar la fecha de inicio de periodo";
			return $error;
		}else{
			if(strtotime($data['fecini'])==0){
				$error = "El formato de la fecha de inicio de periodo es incorrecto";
				return $error;
			}
		}
		if(!isset($data['fecfin'])){
			$error = 'Es necesario ingresar la fecha de fin de periodo';
			return $error;
		}else{
			if(strtotime($data['fecfin'])==0){
				$error = "El formato de la fecha de fin de periodo es incorrecto";
				return $error;
			}
		}
		return $error;
	}

	function execute_save_trabajadores(){
		global $f;
		$data = $f->request->data;
		$rpta = array(
			'status'=>'error',
			'message'=>'No he podido procesar su solicitud',
			'rpta'=>null
		);
		try {
			if(!isset($data['trabajadores'])){
				throw new Exception("No he recibido la lista de trabajadores");
			}else{
				if(count($data['trabajadores'])==0){
					throw new Exception("El numero de trabajadores que me enviaste debe ser mayor a cero");
				}else{
					foreach($data['trabajadores'] as $i=>$trab){
						$data['trabajadores'][$i] = $f->model('mg/entidad')->params(array('_id'=>new MongoId($trab)))->get('one')->items;
						if($data['trabajadores'][$i]==null){
							throw new Exception("He detectado que al menos un trabajador no existe en mi base de datos");
						}
					}
				}
			}
			if(!isset($data['_id'])){
				throw new Exception("No he recibido un identificador de planilla");
			}else{
				$planilla = $f->model("pe/plan")->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
				if($planilla==null){
					throw new Exception("No he encotrado la planilla entregada en mi base de datos");
				}
			}
			$f->model('pe/docs')->params(array('planilla._id'=>new MongoId($data['_id'])))->delete('custom');
			foreach($data['trabajadores'] as $i=>$trab){
				$fich = $f->model("pe/fich")->params(array("_id"=>$trab['_id']))->get("enti")->items;
				if(isset($trab['roles']['trabajador']['bonos'])){
					foreach ($trab['roles']['trabajador']['bonos'] as $bon){
						$bono[] = $bon;
					}
				}
				$fecha_0 = new MongoDate(0);
				$documento = array(
					"fecreg"=>new MongoDate(),
					"fecmod"=>new MongoDate(),
					"planilla"=>array(
						"_id"=>$planilla['_id'],
						"periodo"=>$planilla['periodo']
					),
					"trabajador"=>array(
						"_id"=>$trab['_id'],
						"nomb"=>$trab['nomb'],
						"appat"=>$trab['appat'],
						"apmat"=>$trab['apmat'],
						"tipo_enti"=>$trab['tipo_enti'],
						"fullname"=>$trab['fullname'],
						"docident"=>$trab['docident'],
						"cod_tarjeta"=>$trab['roles']['trabajador']['cod_tarjeta'],
						"cod_aportante"=>$trab['roles']['trabajador']['cod_aportante'],
						"essalud"=>$trab['roles']['trabajador']["essalud"],
						"cargo"=>$trab['roles']['trabajador']['cargo'],
						"fecing"=>(isset($fich['fec_adm_sbpa'])?$fich['fec_adm_sbpa']:$fecha_0),
						"fecnac"=>(isset($fich['fecnac'])?$fich['fecnac']:$fecha_0)
					),
					"autor"=>$f->session->userDBMin,
					"maestro"=>array(
					)
				);
				if(isset($trab['roles']['trabajador']['pension'])){
					$documento['pension'] = $trab['roles']['trabajador']['pension'];
				}
				if(isset($trab['roles']['trabajador']['programa'])){
					$documento['programa'] = $trab['roles']['trabajador']['programa'];
				}
				if(isset($trab['roles']['trabajador']['comision'])){
					$documento['comision'] = $trab['roles']['trabajador']['comision'];
				}
				if(isset($trab['roles']['trabajador']['cargo'])){
					$documento['cargo'] = $trab['roles']['trabajador']['cargo'];
				}
				if(isset($trab['roles']['trabajador']['contrato'])){
					$documento['contrato'] = $trab['roles']['trabajador']['contrato'];
				}
				if(isset($trab['roles']['trabajador']['nivel_carrera'])){
					$documento['contrato'] = $trab['roles']['trabajador']['contrato'];
				}

				/*
				 * Calculo de variables personalizadas por entidad
				 */
				$tmp = array(
					'dia_ina'=>array(),
					'dia_lic'=>array(),
					'dia_sub'=>array(),
					'dia_per'=>array(),
					'dia_vac'=>array(),
					'dia_tra'=>array(),
					'dia_sus'=>array()
				);
				$enti = array(
					'TOTAL_DIAS_MES'=>intval(date('t')),
					'MES_ACTUAL'=>date('m'),
					'EDAD'=>0
				);
				if(isset($fich['fec_ing_sbpa'])){
					$enti['FEC_ING'] = date('Y-M-d', $fich['fec_ing_sbpa']->sec);
				}else{
					$enti['FEC_ING'] = false;
				}
				if(isset($fich['fecnac'])){
					list($Y,$m,$d) = explode("-",date("Y-m-d",$fich['fecnac']->sec));
		    		$enti['EDAD'] = ( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
					//$enti['EDAD'] = date('Y-M-d', $fich['fec_ing_sbpa']->sec);
				}
				$enti['TIPO_CONT'] = $trab['roles']['trabajador']['contrato']['cod'];
				$enti['TIPO_TRAB'] = "\"".$trab['roles']['trabajador']['tipo']."\"";
				$enti['TIPO_COMI'] = "\"".$documento['comision']."\"";
				$enti['SALARIO_NIVEL'] = false;
				$enti['REM_BAS'] = false;
				$enti['REM_REU'] = false;
				$enti['INCENTIVO'] = false;
				if(isset($trab['roles']['trabajador']['nivel_carrera'])){
					$trab['roles']['trabajador']['nivel_carrera'] = $f->model("pe/nive")->params(array("_id"=>$trab['roles']['trabajador']['nivel_carrera']['_id']))->get("one")->items;
					$enti['NIVEL_REMUNERATIVO'] = "\"".$trab['roles']['trabajador']['nivel_carrera']['abrev']."\"";
					$enti['SALARIO_NIVEL'] = floatval($trab['roles']['trabajador']['nivel_carrera']['salario']);
					$enti['REM_BAS'] = floatval($trab['roles']['trabajador']['nivel_carrera']['basica']);
					$enti['REM_REU'] = floatval($trab['roles']['trabajador']['nivel_carrera']['reunificada']);
					$enti['INCENTIVO'] = floatval($trab['roles']['trabajador']['nivel_carrera']['incentivo']);
				}
				if(isset($trab['roles']['trabajador']['nivel'])){
					$trab['roles']['trabajador']['nivel'] = $f->model("pe/nive")->params(array("_id"=>$trab['roles']['trabajador']['nivel']['_id']))->get("one")->items;
					$enti['NIVEL_REMUNERATIVO'] = "\"".$trab['roles']['trabajador']['nivel']['abrev']."\"";
					$enti['SALARIO_NIVEL'] = floatval($trab['roles']['trabajador']['nivel']['salario']);
					$enti['REM_BAS'] = floatval($trab['roles']['trabajador']['nivel']['basica']);
					$enti['REM_REU'] = floatval($trab['roles']['trabajador']['nivel']['reunificada']);
					$enti['INCENTIVO'] = floatval($trab['roles']['trabajador']['nivel']['incentivo']);
				}
				if(isset($trab['roles']['trabajador']['salario'])) $enti['SALARIO_CAS'] = floatval($trab['roles']['trabajador']['salario']);
				else $enti['SALARIO_CAS'] = false;
				$enti['PENS'] = false;
				$enti['PORC_PEN_1'] = false;
				$enti['PORC_PEN_2'] = false;
				$enti['PORC_PEN_3'] = false;
				$enti['PORC_PEN_4'] = false;
				$enti['PORC_PEN_5'] = false;
				$enti['PORC_PEN_6'] = false;
				$enti['PORC_PEN_7'] = false;
				$enti['PORC_PEN_8'] = false;
				$enti['PORC_PEN_9'] = false;
				$enti['PORC_PEN_10'] = false;
				if(isset($trab['roles']['trabajador']['pension'])){
					$enti['PENS'] = true;
					/*if(isset($trab['roles']['trabajador']['pension']['porcentajes']['pension']))
						$enti['PORC_PEN'] = floatval($trab['roles']['trabajador']['pension']['porcentajes']['pension']);
					if(isset($trab['roles']['trabajador']['pension']['porcentajes']['seguro']))
						$enti['PORC_SEG'] = floatval($trab['roles']['trabajador']['pension']['porcentajes']['seguro']);
					if(isset($trab['roles']['trabajador']['pension']['porcentajes']['comision']))
						$enti['POR_COM'] = floatval($trab['roles']['trabajador']['pension']['porcentajes']['comision']);*/
					$trab['roles']['trabajador']['pension'] = $f->model("pe/sist")->params(array("_id"=>$trab['roles']['trabajador']['pension']['_id']))->get("one")->items;
					if(isset($trab['roles']['trabajador']['pension']['porcentajes'])){
						foreach ($trab['roles']['trabajador']['pension']['porcentajes'] as $i=>$por){
							switch ($i){
								case 0:
									$enti['PORC_PEN_1'] = floatval($por['val'])/100;
									break;
								case 1:
									$enti['PORC_PEN_2'] = floatval($por['val'])/100;
									break;
								case 2:
									$enti['PORC_PEN_3'] = floatval($por['val'])/100;
									break;
								case 3:
									$enti['PORC_PEN_4'] = floatval($por['val'])/100;
									break;
								case 4:
									$enti['PORC_PEN_5'] = floatval($por['val'])/100;
									break;
								case 5:
									$enti['PORC_PEN_6'] = floatval($por['val'])/100;
									break;
								case 6:
									$enti['PORC_PEN_7'] = floatval($por['val'])/100;
									break;
								case 7:
									$enti['PORC_PEN_8'] = floatval($por['val'])/100;
									break;
								case 8:
									$enti['PORC_PEN_9'] = floatval($por['val'])/100;
									break;
								case 9:
									$enti['PORC_PEN_10'] = floatval($por['val'])/100;
									break;
							}
						}
					}
				}else{
					/*$enti['PORC_PEN'] = false;
					$enti['PORC_SEG'] = false;
					$enti['POR_COM'] = false;*/
				}
				if(isset($trab['roles']['trabajador']['eps'])) $enti['EPS'] = true;
				else $enti['EPS'] = false;
				$enti['SUBSIDIO_FAMILIAR'] = false;
				if(isset($trab['subsidio'])){
					if($trab['subsidio']!='')
						$enti['SUBSIDIO_FAMILIAR'] = true;
				}
				if(isset($fich['familia'])){
					if(isset($fich['familia']['hijos'])){
						//$enti['HIJOS'] = true;
						$enti['HIJOS'] = sizeof($fich['familia']['hijos']);
					}else $enti['HIJOS'] = false;
				}else $enti['HIJOS'] = false;
				$enti['MIN_EFE'] = 0;
				$enti['MIN_PRO'] = 0;
				$enti['MIN_TAR'] = 0;
				$enti['MIN_EXT'] = 0;
				$enti['MIN_PERMISO'] = 0;
				$enti['VACACIONES'] = false;
				$enti['DIAS_INA'] = 0;
				$enti['DIAS_INA_JUST'] = 0;
				$enti['DIAS_INA_INJU'] = 0;
				$enti['DIAS_LIC'] = 0;
				$enti['DIAS_SUS'] = 0;
				$enti['SUB_DIA'] = 0;
				$enti['DIAS_PERMISO'] = 0;
				$enti['NUM_GUARDIAS_ORD'] = 0;
				$enti['NUM_GUARDIAS_EXT'] = 0;
				if(isset($planilla['fecini'])&&isset($planilla['fecfin'])){
					/* Asistencia */
					$asis = $f->model('pe/asis')->params(array(
						'enti'=>$trab['_id'],
						'fecini'=>new MongoDate(strtotime(date('Y-m-d H:i:s', $planilla['fecini']->sec).' -1 minute')),
						'fecfin'=>new MongoDate(strtotime(date('Y-m-d H:i:s', $planilla['fecfin']->sec).' +1 day -1 minute'))
					))->get('bole')->items;
					if($asis!=null){
						foreach ($asis as $as){
							if(isset($as['programado'])){
								$enti['MIN_PRO'] += abs(($as['programado']['fin']->sec)-($as['programado']['inicio']->sec));
							}
							if(isset($as['ejecutado'])){
								if(isset($as['ejecutado']['salida'])){
									$enti['MIN_EFE'] += abs(($as['ejecutado']['salida']['fecreg']->sec)-($as['ejecutado']['entrada']['fecreg']->sec));
								}
							}
							/*
							 * iba a ser el calculo de dias trabajados
							 */
							if(isset($as['ejecutado']))
								$tmp['dia_tra'][date('Y-M-d', $as['ejecutado']['entrada']['fecreg']->sec)] = true;	
						}
					}
					$enti['MIN_EFE'] = $enti['MIN_EFE']/60;
					$enti['MIN_PRO'] = $enti['MIN_PRO']/60;
					$inci = $f->model('pe/inci')->params(array(
						'enti'=>$trab['_id'],
						'fecini'=>new MongoDate(strtotime(date('Y-m-d H:i:s', $planilla['fecini']->sec))),
						'fecfin'=>new MongoDate(strtotime(date('Y-m-d', $planilla['fecfin']->sec).' 23:59:59'))
					))->get('bole')->items;
					if($inci!=null){
						foreach ($inci as $in){
							if($in['tipo']['goce_haber']==false){
								if($in['tipo']['tipo']=='IN'){
									if($in['tipo']['nomb']!='SUSPENSIÓN'&&$in['tipo']['nomb']!='Suspension')
										$tmp['dia_ina'][date('Y-M-d', $in['fecfin']->sec)] = true;
									else{
										$tmp['dia_sus'][date('Y-M-d', $in['fecfin']->sec)] = true;
									}
								}elseif($in['tipo']['tipo']=='LI') $tmp['dia_lic'][date('Y-M-d', $in['fecfin']->sec)] = true;
								elseif($in['tipo']['tipo']=='TA'){
									$enti['MIN_TAR'] += abs(($in['fecfin']->sec)-($in['fecini']->sec));
								}
							}else{
								if($in['tipo']['tipo']=='TE') $enti['MIN_EXT'] += abs(($in['fecfin']->sec)-($in['fecini']->sec));
							}
							
							
							
							
							if($in['tipo']['tipo']=='PE'){
								if($in['tipo']['goce_haber']==false){
									if($in['tipo']['todo']==true){
										$tmp['dia_per'][date('Y-M-d', $in['fecfin']->sec)] = true;
									}else{
										$enti['MIN_PERMISO'] += abs(($in['fecfin']->sec)-($in['fecini']->sec));
									}
								}
							}
							
							//echo $in['tipo']['subsidiado'];
							
							
							if($in['tipo']['tipo']=='VA'){
								$tmp['dia_vac'][date('Y-M-d', $in['fecfin']->sec)] = true;
							}
							if($in['tipo']['tipo']=='SU'){
								$tmp['dia_sus'][date('Y-M-d', $in['fecfin']->sec)] = true;
							}
							
							
							
							if($in['tipo']['subsidiado']==true){
								$tmp['dia_sub'][date('Y-M-d', $in['fecfin']->sec)] = true;
							}
							
							
							
							if($in['tipo']['tipo']=='JO'){
								/*
								 * iba a ser el calculo de dias trabajados
								 */
								$tmp['dia_tra'][date('Y-M-d', $in['fecfin']->sec)] = true;
							}
						}
					}
					$enti['DIAS_TRAB_TOTAL'] = sizeof($tmp['dia_tra']);
					//$enti['DIAS_TRAB'] = 30-sizeof($tmp['dia_ina'])-sizeof($tmp['dia_sub']);

					$dias_cese = 0;
					if(isset($trab['roles']['trabajador']['cese'])){
						//print_r($trab['roles']['trabajador']['cese']['fec']->sec);
						//print_r(floatval(date('m',$trab['roles']['trabajador']['cese']['fec']->sec)));
						if(floatval(date('m',$trab['roles']['trabajador']['cese']['fec']->sec))==floatval($planilla['periodo']['mes'])){
							$dias_cese = 30-floatval(date('d',$trab['roles']['trabajador']['cese']['fec']->sec));
							//print_r($dias_cese);
						}
					}
					$enti['DIAS_CESE'] = $dias_cese;
					$enti['DIAS_TRAB'] = 30-sizeof($tmp['dia_ina'])-sizeof($tmp['dia_sus']);
					////////////////////// VERIFICAR QUE INASISTENCIA NO COINCIDA CON DIA SUBSIDIADO
					$enti['MIN_TAR'] = $enti['MIN_TAR']/60;
					$enti['MIN_EXT'] = $enti['MIN_EXT']/60;
					$enti['MIN_PERMISO'] = $enti['MIN_PERMISO']/60;
					$enti['DIAS_INA'] = sizeof($tmp['dia_ina']);
					$enti['DIAS_LIC'] = sizeof($tmp['dia_lic']);
					$enti['DIAS_SUS'] = sizeof($tmp['dia_sus']);
					$enti['SUB_DIA'] = sizeof($tmp['dia_sub']);
					$enti['DIAS_PERMISO'] = sizeof($tmp['dia_per']);
					$enti['VACACIONES'] = sizeof($tmp['dia_vac']);
					if($enti['VACACIONES']!=0)
						/*
						 * SE DEBE AÑADIR UNA OPCION PARA VERIFICAR LAS VACACIONES
						 */
						//$enti['VACACIONES'] = true;
						$enti['VACACIONES'] = false;
					else
						$enti['VACACIONES'] = false;
				}

				/*********************************************************************************
				 * 
				 * CALCULO DE SUBSIDIOS AL MES DE LA BOLETA
				 * 
				 ********************************************************************************/
				if(isset($planilla['periodo']['mes'])){
					/* Asistencia */
					$inci = $f->model('pe/inci')->params(array(
						'enti'=>$trab['_id'],
						'fecini'=>new MongoDate(strtotime($planilla['periodo']['ano'].'-'.$planilla['periodo']['mes'].'-01')),
						'fecfin'=>new MongoDate(strtotime($planilla['periodo']['ano'].'-'.$planilla['periodo']['mes'].'-01 +1 month'))
					))->get('bole')->items;
					if($inci!=null){
						foreach ($inci as $in){
							if($in['tipo']['subsidiado']==true){
								$tmp['dia_sub'][date('Y-M-d', $in['fecfin']->sec)] = true;
							}
						}
					}
					$enti['DIAS_TRAB'] = $enti['DIAS_TRAB']-sizeof($tmp['dia_sub']);
					$enti['SUB_DIA'] = sizeof($tmp['dia_sub']);
				}
				/*
				 * Calculo de dias correspondientes a Aguinaldo FIESTAS PATRIAS
				 */
				$enti['DIAS_AGUI_FP'] = 0;
				if($planilla['periodo']['mes']=='07'){
					if(isset($trab['roles']['trabajador']['fecing'])){
						if(!isset($trab['roles']['trabajador']['cese'])){
							$enti['DIAS_AGUI_FP'] = $this->dias_agui_fi($trab['roles']['trabajador']['fecing'],$enti['DIAS_PERMISO']);
						}elseif($trab['roles']['trabajador']['cese']['fec']>(new MongoDate(strtotime(date('Y').'-06-31')))){
							$enti['DIAS_AGUI_FP'] = $this->dias_agui_fi($trab['roles']['trabajador']['fecing'],$enti['DIAS_PERMISO']);
						}
					}
				}
				/*
				 * Calculo de dias correspondientes a Aguinaldo NAVIDAD
				 */
				$enti['DIAS_AGUI_NA'] = 0;
				if($planilla['periodo']['mes']=='12'){
					if(isset($trab['roles']['trabajador']['fecing'])){
						if(!isset($trab['roles']['trabajador']['cese'])){
							$enti['DIAS_AGUI_NA'] = $this->dias_agui_na($trab['roles']['trabajador']['fecing'],$enti['DIAS_PERMISO']);
						}elseif($trab['roles']['trabajador']['cese']['fec']>(new MongoDate(strtotime(date('Y').'-11-30')))){
							$enti['DIAS_AGUI_NA'] = $this->dias_agui_na($trab['roles']['trabajador']['fecing'],$enti['DIAS_PERMISO']);
						}
					}
				}
				/*
				 * Fin de Calculo de dias correspondientes a Aguinaldo
				 */
				$tmp_bole = $f->model('pe/docs')->params(array(
					'filter'=>array('boletas'=>true,'trabajador._id'=>$trab['_id']),
					'fields'=>array('neto'=>true)
				))->get('custom')->items;
				if($tmp_bole!=null){
					if(isset($tmp_bole[0]['neto']))
						$enti['BOLETA'] = floatval($tmp_bole[0]['neto']);
				}
				else $enti['BOLETA'] = 0;

				$documento['maestro'] = $enti;
				$f->model("pe/docs")->params(array('data'=>$documento))->save("insert")->items;
			}
			$rpta = array(
				'status'=>'success',
				'message'=>"He registrado correctamente los trabajadores en mi base de datos de planillas",
				'rpta'=>null
			);
		} catch (Exception $e) {
			$rpta = array(
				'status'=>'error',
				'message'=>$e->getMessage(),
				'rpta'=>$e
			);
		}
		$f->response->json($rpta);
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
	function execute_edit(){
		global $f;
		$f->response->view("pe/plan.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("pe/plan.details");
	}
	function execute_download_formato_maestro(){
		global $f;
		$data = $f->request->data;
		$params = array(
			'planilla._id'=>new MongoId($data['_id'])
		);
		$model = $f->model('pe/docs')->params($params)->get('all');
		/*if($model->items!=null){
		}*/
		$f->response->view('pe/plan.formato_maestro',$model);
	}
	function execute_import_maestro_main(){
		global $f;
		$f->response->view('pe/plan.import_maestro_main');
	}
	function execute_import_maestro(){
		global $f;
		//$equipo = $f->request->data['equipo'];
		set_time_limit(0);
		$inputFileName = IndexPath.DS.'tmp/'.$f->request->data['file'];
		$f->library('excel');
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load($inputFileName);
		$row = 5;
		//calculando el numero de dias en un periodo
		$cols = array(/*"E","F","G",*/"H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
		$num_rows = $objPHPExcel->getActiveSheet()->getHighestRow();

		for($row=5;$row<=$num_rows;$row++){
			$id_doc = new MongoId($objPHPExcel->getActiveSheet()->getCell('A'.$row)->getValue());
			$documento = $f->model('pe/docs')->params(array('_id'=>$id_doc))->get('one')->items;

			if($documento!=null){
				$trabajador = $f->model('mg/entidad')->params(array('_id'=>$documento['trabajador']['_id']))->get('one')->items;
				if($trabajador!=null){
					$programa_excel = trim($objPHPExcel->getActiveSheet()->getCell('D'.$row)->getValue());
					$programa = $f->model('mg/prog')->params(array('filter'=>array('nomb'=>$programa_excel)))->get('all')->items;
					if($programa==null) echo 'NO PROG '.$programa_excel.'<BR />';
					else $programa = $programa[0];

					$sispen_excel = trim($objPHPExcel->getActiveSheet()->getCell('F'.$row)->getValue());
					$sispen = $f->model('pe/sist')->params(array('filter'=>array('nomb'=>$sispen_excel)))->get('all')->items;
					if($sispen==null) echo 'NO SIST '.$sispen_excel.'<BR />';
					else $sispen = $sispen[0];

					$comision_excel = trim($objPHPExcel->getActiveSheet()->getCell('G'.$row)->getValue());
					$salario_excel = trim($objPHPExcel->getActiveSheet()->getCell('E'.$row)->getValue());
					if($programa!=null && $sispen!=null){
						$trab_upd = array(
							'roles.trabajador.programa'=>array(
								'_id'=>$programa['_id'],
								'nomb'=>$programa['nomb']
							),
							'roles.trabajador.pension'=>array(
								'_id'=>$sispen['_id'],
								'nomb'=>$sispen['nomb'],
								'tipo'=>$sispen['tipo'],
							),
							'roles.trabajador.comision'=>$comision_excel,
							'roles.trabajador.salario'=>$salario_excel
						);
						//echo "SE HA MODIFICADO EL TRABAJADOR";
						$f->model("mg/entidad")->params(array('_id'=>$trabajador['_id'],'data'=>$trab_upd))->save("update");
						/*$documento_upd = array(
							'programa'=>array(
								'_id'=>$programa['_id'],
								'nomb'=>$programa['nomb']
							),
							'pension'=>array(
								'_id'=>$pension['_id'],
								'nomb'=>$pension['nomb'],
								'tipo'=>$pension['tipo'],
							),
						);*/
					}
				}else{
					echo "NO SE HA ENCONTRATO EL TRABAJADOR: ".$documento['_id']->{'$id'};
				}
				$maestros = $documento['maestro'];
				$out = 0;
				$col = 0;
				while ($out==0) {
					$col_name = $objPHPExcel->getActiveSheet()->getCell($cols[$col]."3")->getValue();
					if(trim($col_name)!=""){
						$col_value = $objPHPExcel->getActiveSheet()->getCell($cols[$col].$row)->getValue();
						$maestros[$col_name]=$col_value;
					}else{
						$out = 1;
					}
					$col++;
				}
				/*$maestros['TOTAL_DIAS_MES']=$objPHPExcel->getActiveSheet()->getCell('E'.$row)->getValue();
				$maestros['MIN_PERMISO']=$objPHPExcel->getActiveSheet()->getCell('F'.$row)->getCalculatedValue();
				$maestros['MIN_EXT']=$objPHPExcel->getActiveSheet()->getCell('G'.$row)->getValue();
				$maestros['MIN_TAR']=$objPHPExcel->getActiveSheet()->getCell('H'.$row)->getValue();
				$maestros['DIAS_TRAB']=$objPHPExcel->getActiveSheet()->getCell('I'.$row)->getValue();
				$maestros['DIAS_CESE']=$objPHPExcel->getActiveSheet()->getCell('J'.$row)->getValue();
				$maestros['DIAS_INA']=$objPHPExcel->getActiveSheet()->getCell('K'.$row)->getValue();
				$maestros['DIAS_LIC']=$objPHPExcel->getActiveSheet()->getCell('L'.$row)->getValue();
				$maestros['DIAS_SUS']=$objPHPExcel->getActiveSheet()->getCell('M'.$row)->getValue();
				$maestros['DIAS_PERMISO']=$objPHPExcel->getActiveSheet()->getCell('N'.$row)->getValue();
				$maestros['NUM_GUARDIAS_ORD']=$objPHPExcel->getActiveSheet()->getCell('O'.$row)->getValue();
				$maestros['NUM_GUARDIAS_EXT']=$objPHPExcel->getActiveSheet()->getCell('P'.$row)->getValue();*/
				//print_r($id_doc);
				$f->model('pe/docs')->params(array('_id'=>$id_doc,'data'=>array('maestro'=>$maestros)))->save('update');
			}
		}
		$rpta = array(
			'status'=>'success',
			'message'=>"He procesado correctamente el formato subido",
			'rpta'=>null
		);
	}
	function execute_repo_elec_export(){
		global $f;
	        $data = $f->request->data;
	        $params = array(
	            'planilla._id'=>new MongoId($data['_id'])
	        );
	        $model = $f->model('pe/docs')->params($params)->get('all');
	        $planilla = $f->model('pe/plan')->params(array('_id'=>new MongoId($data['_id'])))->get('one');
	        /*if($model->items!=null){
	        }*/
	        $header = array();
	        $trabajadores = array();
	        foreach ($model->items as $i => $item) {
	            if(isset($item['conceptos'])){
	                foreach($item['conceptos'] as $conc){
	                    if(!isset($header[$item['programa']['_id']->{'$id'}][$conc['concepto']['cod']]) && floatval($conc['concepto']['planilla'])>0){
	                        $header[$item['programa']['_id']->{'$id'}][$conc['concepto']['cod']]['concepto'] = $conc['concepto'];
	                        $header[$item['programa']['_id']->{'$id'}][$conc['concepto']['cod']]['importe'] = 0;
	                    }
	                }
	            }
			}
	        $header_all = $header;
	        foreach ($model->items as $i => $item) {
	            if(!isset($trabajadores[$item['programa']['_id']->{'$id'}])){
	                $trabajadores[$item['programa']['_id']->{'$id'}]['programa'] = $item['programa'];
	                $trabajadores[$item['programa']['_id']->{'$id'}]['totales'] = array();
	                $trabajadores[$item['programa']['_id']->{'$id'}]['trabajadores'] = array();
	            }
	            $model->items[$i]['planilla'] = $f->model('pe/plan')->params(array('_id'=>$item['planilla']['_id']))->get('one')->items;
	            $model->items[$i]['totales'] = $header[$item['programa']['_id']->{'$id'}];
	            if(isset($item['conceptos'])){
	                foreach($item['conceptos'] as $conc){
	                    if(floatval($conc['concepto']['planilla'])>0){
	                        $model->items[$i]['totales'][$conc['concepto']['cod']]['importe']+=floatval($conc['subtotal']);
	                        $header_all[$item['programa']['_id']->{'$id'}][$conc['concepto']['cod']]['importe']+=floatval($conc['subtotal']);
	                    }
	                }
	                unset($model->items[$i]['conceptos']);
	            }
	            $trabajadores[$item['programa']['_id']->{'$id'}]['trabajadores'][$item['_id']->{'$id'}] = $model->items[$i];
	        }
	        /*print_r($model);
	        die();*/
			if(isset($f->request->data['pdf'])){
	            $f->response->view('pe/plan.repo_elec.print',array('items'=>$trabajadores,'header'=>$header_all,'planilla'=>$planilla->items));    
	        }else{
	            $f->response->view('pe/plan.repo_elec.export',array('items'=>$trabajadores,'header'=>$header_all,'planilla'=>$planilla->items));
	        }
    }
}
?>