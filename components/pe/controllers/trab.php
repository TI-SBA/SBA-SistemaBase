<?php
class Controller_pe_trab extends Controller {
	function execute_lista(){
		global $f;
		if(isset($f->request->data['tipo'])){
			$filter = array(
				array('nomb'=>'roles.trabajador.contrato.cod','value'=>$f->request->data['tipo'])
			);
		}else{
			$filter = array(
				array('nomb'=>'roles.trabajador.contrato','value'=>array('$exists'=>true))
			);
		}
		$fields = array(
			'nomb'=>true,
			'appat'=>true,
			'apmat'=>true,
			'tipo_enti'=>true,
			'docident'=>true,
			'roles.trabajador.estado'=>true,
			'roles.trabajador.contrato'=>true,
			'roles.trabajador.organizacion'=>true,
			'roles.trabajador.cargo'=>true,
			'roles.trabajador.funcion'=>true
		);
		if(isset($f->request->data['type_fields'])){
			switch($f->request->data['type_fields']){
				case 'trab_horarios':
					$fields = array(
						'imagen'=>true,
						'tipo_enti'=>true,
			'nomb'=>true,
			'appat'=>true,
			'apmat'=>true,
						'fullname'=>true,
						'docident'=>true,
						'roles.trabajador.turno'=>true
					);
					break;
				case 'trab_lista':
					$fields = array(
						'tipo_enti'=>true,
						'fullname'=>true,
						'docident'=>true,
						'fecmod'=>true,
						'roles.trabajador.estado'=>true,
						'roles.trabajador.contrato'=>true,
						'roles.trabajador.programa'=>true,
						/*'roles.trabajador.historico'=>false,
						'roles.trabajador.bonos'=>false*/
					);
					break;
			}
		}
		$model = $f->model("mg/entidad")->params(array(
			"filter"=>$filter,
			'texto'=>$f->request->data['texto'],
			'fields'=>$fields,
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows'],
			'fields'=>$fields
		))->get("lista");
		if(isset($f->request->data['type_fields'])){
			if($f->request->data['type_fields']=='trab_horarios'){
				foreach ($model->items as $i=>$item) {
					$turno = null;
					if(isset($item['roles']['trabajador']['turno'])) $turno = $f->model('pe/turn')->params(array('_id'=>$item['roles']['trabajador']['turno']['_id']))->get('one')->items;
					else $model->items[$i]['roles']['trabajador']['turno'] = array('nomb'=>'NO LE ASIGNARON');
					if($turno==null) $model->items[$i]['roles']['trabajador']['turno']['nomb'] .= ' - NO EXISTE';
				}
			}
		}
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"page"=>$f->request->data['page'],
			"filter"=>array(
				array('nomb'=>'roles.trabajador.contrato.cod','value'=>$f->request->data['tipo'])
			),
			"page_rows"=>$f->request->data['page_rows'],
			"texto"=>$f->request->data['texto']
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_all_tipo(){
		global $f;
		$model = $f->model('mg/entidad')->params(array('tipo'=>$f->request->data['tipo']))->get('all_tipo');
		$f->response->json($model->items);
	}
	function execute_all(){
		global $f;
		$model = $f->model('mg/entidad')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->_id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_ficha(){
		global $f;
		$model = $f->model("pe/fich")->params(array("_id"=>new MongoId($f->request->id)))->get("enti");
		$f->response->json( $model->items );
	}
	function execute_edit_new(){
		global $f;
		$cont = $f->model("pe/cont")->get("all");
		$sist = $f->model("pe/sist")->get("all");
		$f->response->json( array('cont'=>$cont->items,'pension'=>$sist->items) );
	}
	function execute_pad(){
		global $f;
		$f->response->view("pe/pad.edit");
	}
	function execute_get_trab(){
		global $f;
		$ficha = $f->model("pe/fich")->params(array("_id"=>new MongoId($f->request->id)))->get("enti");
		$enti = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($ficha->items!=null){
			if(isset($ficha->items['familia'])){
				if(isset($ficha->items['familia']['padre']))
					$ficha->items['familia']['padre'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['padre']))->get("one")->items;
				if(isset($ficha->items['familia']['madre']))
					$ficha->items['familia']['madre'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['madre']))->get("one")->items;
				if(isset($ficha->items['familia']['hermanos'])){
					foreach ($ficha->items['familia']['hermanos'] as $i=>$her){
						$ficha->items['familia']['hermanos'][$i] = $f->model("mg/entidad")->params(array("_id"=>$her))->get("one")->items;
					}
				}
				if(isset($ficha->items['familia']['conyuge']))
					$ficha->items['familia']['conyuge'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['conyuge']))->get("one")->items;
				if(isset($ficha->items['familia']['hijos'])){
					foreach ($ficha->items['familia']['hijos'] as $i=>$hij){
						$ficha->items['familia']['hijos'][$i] = $f->model("mg/entidad")->params(array("_id"=>$hij))->get("one")->items;
					}
				}
			}
		}
		$f->response->json( array('ficha'=>$ficha->items,'enti'=>$enti->items) );
	}
	function execute_edit_ficha(){
		global $f;
		$ficha = $f->model("pe/fich")->params(array("_id"=>new MongoId($f->request->id)))->get("enti");
		$enti = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$sist = $f->model("pe/sist")->get("all");
		if($ficha->items!=null){
			if(isset($ficha->items['familia'])){
				if(isset($ficha->items['familia']['padre']))
					$ficha->items['familia']['padre'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['padre']))->get("one")->items;
				if(isset($ficha->items['familia']['madre']))
					$ficha->items['familia']['madre'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['madre']))->get("one")->items;
				if(isset($ficha->items['familia']['hermanos'])){
					foreach ($ficha->items['familia']['hermanos'] as $i=>$her){
						$ficha->items['familia']['hermanos'][$i] = $f->model("mg/entidad")->params(array("_id"=>$her))->get("one")->items;
					}
				}
				if(isset($ficha->items['familia']['conyuge']))
					$ficha->items['familia']['conyuge'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['conyuge']))->get("one")->items;
				if(isset($ficha->items['familia']['hijos'])){
					foreach ($ficha->items['familia']['hijos'] as $i=>$hij){
						$ficha->items['familia']['hijos'][$i] = $f->model("mg/entidad")->params(array("_id"=>$hij))->get("one")->items;
					}
				}
			}
		}
		$f->response->json( array('ficha'=>$ficha->items,'enti'=>$enti->items,'sist'=>$sist->items) );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		unset($data['_id']);
		$data['estado'] = 'H';
		if(isset($data['cargo']['_id'])) $data['cargo']['_id'] = new MongoId($data['cargo']['_id']);
		if(isset($data['cargo']['organizacion'])){
			$data['cargo']['organizacion']['_id'] = new MongoId($data['cargo']['organizacion']['_id']);
		}
		//$data['organizacion'] = $data['cargo']['organizacion'];
		if(isset($data['programa'])){
			$data['programa']['_id'] = new MongoId($data['programa']['_id']);
		}
		if(isset($data['oficina'])){
			if(isset($data['oficina']['_id'])) $data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
		}
		if(isset($data['actividad'])){
			$data['actividad']['_id'] = new MongoId($data['actividad']['_id']);
		}

		if(isset($data['organizacion']['actividad'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(isset($data['nivel']['_id'])) $data['nivel']['_id'] = new MongoId($data['nivel']['_id']);
		if(isset($data['nivel_carrera']['_id'])) $data['nivel_carrera']['_id'] = new MongoId($data['nivel_carrera']['_id']);
		$data['local']['_id'] = new MongoId($data['local']['_id']);
		if(isset($data['turno']['_id'])) $data['turno']['_id'] = new MongoId($data['turno']['_id']);
		$data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['cargo_clasif']['_id'])) $data['cargo_clasif']['_id'] = new MongoId($data['cargo_clasif']['_id']);
		if(isset($data['grupo_ocup']['_id'])) $data['grupo_ocup']['_id'] = new MongoId($data['grupo_ocup']['_id']);
		if($data['eps']=='0') $data['eps'] = false;
		else $data['eps'] = true;
		if(isset($data['ruc'])){
			unset($data['ruc']);
		}
		if(isset($data['pension']['_id'])){
			//$data['roles.trabajador.pension'] = $data['pension'];
			$data['pension']['_id'] = new MongoId($data['pension']['_id']);
		}
		$data_to_save = array(
			'roles.trabajador'=>$data,
			//'docident.1'=>array('tipo'=>'RUC','num'=>$f->request->data['ruc'])
		);

		if(isset($data['ruc'])){
			$data_to_save['docident.1'] = array('tipo'=>'RUC','num'=>$f->request->data['ruc']);
		}
		
		//if(isset($data['pension']['_id'])) $data['pension']['_id'] = new MongoId($data['pension']['_id']);
		$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data_to_save
		))->save("update");
		if(isset($data['cargo']['nomb']))
			$cargo = $data['cargo']['nomb'];
		else
			$cargo = $data['cargo']['funcion'];
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>cre&oacute;</b> al trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$data['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para el programa <b>'.$data['programa']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_edit(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->model('mg/entidad')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items['roles']['trabajador'];
		$trabajador['fec'] = new MongoDate();
		unset($trabajador['cese']);
		unset($trabajador['bonos']);
		unset($trabajador['ficha']);
		unset($trabajador['historico']);


		//$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('$push'=>array('roles.trabajador.historico'=>$trabajador))))->save("custom");
		$trab_hist = $trabajador;
		$trab_hist['trabajador'] = new MongoId($data['_id']);
		$f->model("pe/trah")->params(array('_id'=>$trab_hist['trabajador'],'data'=>$trab_hist))->save("insert");


		$upd = array('roles.trabajador.estado'=>'H');
		if(isset($data['oficina'])){
			if(isset($data['oficina']['_id'])) $data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
			$upd['roles.trabajador.oficina'] = $data['oficina'];
		}
		/*if(isset($data['actividad'])){
			if(isset($data['actividad']['_id'])) $data['oficina']['_id'] = new MongoId($data['actividad']['_id']);
			$upd['roles.trabajador.actividad'] = $data['actividad'];
		}*/
		if(isset($data['actividad']['_id'])){
			$data['actividad']['_id'] = new MongoId($data['actividad']['_id']);
			$upd['roles.trabajador.actividad'] = $data['actividad'];
		}
		if(isset($data['programa']['_id'])){
			$data['programa']['_id'] = new MongoId($data['programa']['_id']);
			$upd['roles.trabajador.programa'] = $data['programa'];
		}

		if(isset($data['cargo']['_id']))
			$data['cargo']['_id'] = new MongoId($data['cargo']['_id']);
		if(isset($data['cargo']['organizacion']['_id']))
			$data['cargo']['organizacion']['_id'] = new MongoId($data['cargo']['organizacion']['_id']);
		if(isset($data['cargo']['organizacion']))
			$data['organizacion'] = $data['cargo']['organizacion'];
		if(isset($data['cargo']))
			$upd['roles.trabajador.cargo'] = $data['cargo'];
		if(isset($data['organizacion']['actividad']))
			$data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente']))
			$data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(isset($data['organizacion'])){
			$upd['roles.trabajador.organizacion'] = $data['organizacion'];
		}
		
		if(isset($data['programa'])){
			$data['programa']['_id'] = new MongoId($data['programa']['_id']);
		}
		if(isset($data['nivel']['_id'])){
			$data['nivel']['_id'] = new MongoId($data['nivel']['_id']);
			$upd['roles.trabajador.nivel'] = $data['nivel'];
		}
		if(isset($data['nivel_carrera']['_id'])){
			$data['nivel_carrera']['_id'] = new MongoId($data['nivel_carrera']['_id']);
			$upd['roles.trabajador.nivel_carrera'] = $data['nivel_carrera'];
		}
		$data['local']['_id'] = new MongoId($data['local']['_id']);
		$upd['roles.trabajador.local'] = $data['local'];
		if(isset($data['turno']['_id'])){
			$data['turno']['_id'] = new MongoId($data['turno']['_id']);
			$upd['roles.trabajador.turno'] = $data['turno'];
		}
		$data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		$upd['roles.trabajador.contrato'] = $data['contrato'];
		if(isset($data['cargo_clasif']['_id'])){
			$data['cargo_clasif']['_id'] = new MongoId($data['cargo_clasif']['_id']);
			$upd['roles.trabajador.cargo_clasif'] = $data['cargo_clasif'];
		}
		if(isset($data['grupo_ocup']['_id'])){
			$data['grupo_ocup']['_id'] = new MongoId($data['grupo_ocup']['_id']);
			$upd['roles.trabajador.grupo_ocup'] = $data['grupo_ocup'];
		}
		if($data['eps']=='0') $data['eps'] = false;
		else $data['eps'] = true;
		$upd['roles.trabajador.eps'] = $data['eps'];
		$upd['roles.trabajador.tipo'] = $data['tipo'];

		/*if(isset($data['programa']))
			$upd['roles.trabajador.programa'] = $data['programa'];
*/
		if(isset($data['cod_tarjeta']))
			$upd['roles.trabajador.cod_tarjeta'] = $data['cod_tarjeta'];
		if(isset($data['essalud']))
			$upd['roles.trabajador.essalud'] = $data['essalud'];
		unset($data['ruc']);
		if(isset($data['pension']['_id'])){
			$data['pension']['_id'] = new MongoId($data['pension']['_id']);
			$upd['roles.trabajador.pension'] = $data['pension'];
		}
		if(isset($data['salario']))
			$upd['roles.trabajador.salario'] = $data['salario'];
		if(isset($data['modalidad']))
			$upd['roles.trabajador.modalidad'] = $data['modalidad'];
		if(isset($data['cod_aportante']))
			$upd['roles.trabajador.cod_aportante'] = $data['cod_aportante'];
		if(isset($data['observ']))
			$upd['roles.trabajador.observ'] = $data['observ'];
		if(isset($data['comision']))
			$upd['roles.trabajador.comision'] = $data['comision'];
		$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'$unset'=>array(
				'roles.trabajador.cargo'=>true,
				'roles.trabajador.funcion'=>true,
				'roles.trabajador.organizacion'=>true,
				'roles.trabajador.nivel'=>true,
				'roles.trabajador.local'=>true,
				'roles.trabajador.turno'=>true,
				'roles.trabajador.contrato'=>true,
				'roles.trabajador.cargo_clasif'=>true,
				'roles.trabajador.grupo_ocup'=>true,
				'roles.trabajador.pension'=>true,
				'roles.trabajador.cod_aportante'=>true,
				'roles.trabajador.essalud'=>true,
				'roles.trabajador.nivel_carrera'=>true,
				'roles.trabajador.tipo'=>true,
				'roles.trabajador.eps'=>true,
				'roles.trabajador.cod_tarjeta'=>true,
				'roles.trabajador.salario'=>true,
				'roles.trabajador.modalidad'=>true,
				'roles.trabajador.observ'=>true,
				'roles.trabajador.comision'=>true
			)
		)))->save("custom");
		//falta actualizar ruc
		//$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");
		if(isset($f->request->data['ruc']))
			$upd['docident.1'] = array('tipo'=>'RUC','num'=>$f->request->data['ruc']);

		$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");

		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($vari['roles']['trabajador']['cargo']['nomb']))
			$cargo = $vari['roles']['trabajador']['cargo']['nomb'];
		else
			$cargo = $vari['roles']['trabajador']['cargo']['funcion'];
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>actualiz&oacute;</b> la informaci&oacute;n del trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$vari['roles']['trabajador']['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para el programa <b>'.$vari['roles']['trabajador']['programa']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_upd(){
		global $f;
		$tmp = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items['roles']['trabajador'];
		$data = array_merge($tmp,$f->request->data);
		if(isset($f->request->data['cese'])){
			if($f->request->data['cese']!=''){
				$data['cese']['fec'] = new MongoDate(strtotime($data['cese']['fec']));	
			}else{
				$data['cese']['fec'] = new MongoDate();
			}
			
		}
		unset($data['_id']);
		$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('roles.trabajador'=>$data)))->save("update");
		$f->response->print("true");
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if($f->request->data['estado']=='H') $word = 'habilit&oacute;';
		else $word = 'deshabilit&oacute;';
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se '.$word.' al trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b>.'
		))->save('insert');
	}
	function execute_save_ficha(){
		global $f;
		$data = $f->request->data;
		if(isset($data['_id'])) $data['_id'] = new MongoId($data['_id']);
		$data['entidad']['_id'] = new MongoId($data['entidad']['_id']);
		if($data['estudios']['primaria']=='true') $data['estudios']['primaria'] = true;
		else $data['estudios']['primaria'] = false;
		if($data['estudios']['secundaria']=='true') $data['estudios']['secundaria'] = true;
		else $data['estudios']['secundaria'] = false;
		if(isset($data['pension'])) $data['pension']['_id'] = new MongoId($data['pension']['_id']);
		$ficha = array(
			'entidad'=>$data['entidad'],
			'estudios'=>array(
				'primaria'=>$data['estudios']['primaria'],
				'secundaria'=>$data['estudios']['secundaria']
			)
		);
		if(isset($data['fec_adm_pub'])) $ficha['fec_adm_pub'] = new MongoDate(strtotime($data['fec_adm_pub']));
		if(isset($data['fec_adm_sbpa'])) $ficha['fec_adm_sbpa'] = new MongoDate(strtotime($data['fec_adm_sbpa']));
		if(isset($data['fecnac'])) $ficha['fecnac'] = new MongoDate(strtotime($data['fecnac']));
		if(isset($data['ref'])) $ficha['ref'] = $data['ref'];
		if(isset($data['estudios']['superior'])){
			foreach ($data['estudios']['superior'] as $sup){
				if($sup['completa']=='true') $sup['completa'] = true;
				else $sup['completa'] = false;
				$sup['fecini'] = new MongoDate(strtotime($sup['fecini']));
				$sup['fecfin'] = new MongoDate(strtotime($sup['fecfin']));
				if(!isset($ficha['estudios']['superior'])) $ficha['estudios']['superior'] = array();
				$ficha['estudios']['superior'][] = $sup;
			}
		}
		if(isset($data['colegiatura'])){
			$ficha['colegiatura'] = array(
				'colegio'=>$data['colegiatura']['colegio'],
				'cod'=>$data['colegiatura']['cod'],
				'fec'=>new MongoDate(strtotime($data['colegiatura']['fec']))
			);
		}
		if(isset($data['certificaciones'])){
			foreach ($data['certificaciones'] as $sup){
				$sup['fecini'] = new MongoDate(strtotime($sup['fecini']));
				$sup['fecfin'] = new MongoDate(strtotime($sup['fecfin']));
				if(!isset($ficha['certificaciones'])) $ficha['certificaciones'] = array();
				$ficha['certificaciones'][] = $sup;
			}
		}
		if(isset($data['idiomas'])){
			foreach ($data['idiomas'] as $sup){
				if($sup['lee']=='true') $sup['lee'] = true;
				else $sup['lee'] = false;
				if($sup['escribe']=='true') $sup['escribe'] = true;
				else $sup['escribe'] = false;
				if($sup['habla']=='true') $sup['habla'] = true;
				else $sup['habla'] = false;
				if(!isset($ficha['idiomas'])) $ficha['idiomas'] = array();
				$ficha['idiomas'][] = $sup;
			}
		}
		if(isset($data['familia'])){
			if(isset($data['familia']['padre'])){
				$ficha['familia'] = array(
					'padre'=>new MongoId($data['familia']['padre']['_id'])
				);
				if($data['familia']['padre']['vivo']=='true') $data['familia']['padre']['vivo'] = true;
				else $data['familia']['padre']['vivo'] = false;
				$data['familia']['padre']['fecnac'] = new MongoDate(strtotime($data['familia']['padre']['fecnac']));
				/*echo "->".$data['familia']['padre']['_id']."<br />";
				print_r(array(
					'vivo'=>$data['familia']['padre']['vivo'],
					'fecnac'=>$data['familia']['padre']['fecnac']
				));*/
				$f->model("mg/entidad")->params(array('_id'=>new MongoId($data['familia']['padre']['_id']),'data'=>array(
					'vivo'=>$data['familia']['padre']['vivo'],
					'fecnac'=>$data['familia']['padre']['fecnac']
				)))->save("update");
			}
			if(isset($data['familia']['madre'])){
				if(!isset($ficha['familia'])) $ficha['familia'] = array();
				$ficha['familia']['madre'] = new MongoId($data['familia']['madre']['_id']);
				if($data['familia']['madre']['vivo']=='true') $data['familia']['madre']['vivo'] = true;
				else $data['familia']['madre']['vivo'] = false;
				$data['familia']['madre']['fecnac'] = new MongoDate(strtotime($data['familia']['madre']['fecnac']));
				$f->model("mg/entidad")->params(array('_id'=>new MongoId($data['familia']['madre']['_id']),'data'=>array(
					'vivo'=>$data['familia']['madre']['vivo'],
					'fecnac'=>$data['familia']['madre']['fecnac']
				)))->save("update");
			}
			if(isset($data['familia']['hermanos'])){
				if(!isset($ficha['familia'])) $ficha['familia'] = array();
				$ficha['familia']['hermanos'] = array();
				foreach ($data['familia']['hermanos'] as $i=>$her){
					$ficha['familia']['hermanos'][] = new MongoId($her['_id']);
					if($data['familia']['hermanos'][$i]['vivo']=='true') $data['familia']['hermanos'][$i]['vivo'] = true;
					else $data['familia']['hermanos'][$i]['vivo'] = false;
					$data['familia']['hermanos'][$i]['fecnac'] = new MongoDate(strtotime($data['familia']['hermanos'][$i]['fecnac']));
					$f->model("mg/entidad")->params(array('_id'=>new MongoId($data['familia']['hermanos'][$i]['_id']),'data'=>array(
						'vivo'=>$data['familia']['hermanos'][$i]['vivo'],
						'fecnac'=>$data['familia']['hermanos'][$i]['fecnac']
					)))->save("update");
				}
			}
			if(isset($data['familia']['conyuge'])){
				if(!isset($ficha['familia'])) $ficha['familia'] = array();
				$ficha['familia']['conyuge'] = new MongoId($data['familia']['conyuge']['_id']);
				$data['familia']['conyuge']['fecnac'] = new MongoDate(strtotime($data['familia']['conyuge']['fecnac']));
				$f->model("mg/entidad")->params(array('_id'=>new MongoId($data['familia']['conyuge']['_id']),'data'=>array(
					'fecnac'=>$data['familia']['conyuge']['fecnac']
				)))->save("update");
			}
			if(isset($data['familia']['hijos'])){
				if(!isset($ficha['familia'])) $ficha['familia'] = array();
				$ficha['familia']['hijos'] = array();
				foreach ($data['familia']['hijos'] as $i=>$hij){
					$ficha['familia']['hijos'][] = new MongoId($hij['_id']);
					$data['familia']['hijos'][$i]['fecnac'] = new MongoDate(strtotime($data['familia']['hijos'][$i]['fecnac']));
					$f->model("mg/entidad")->params(array('_id'=>new MongoId($data['familia']['hijos'][$i]['_id']),'data'=>array(
						'sexo'=>$data['familia']['hijos'][$i]['sexo'],
						'estado_civil'=>$data['familia']['hijos'][$i]['estado_civil'],
						'fecnac'=>$data['familia']['hijos'][$i]['fecnac']
					)))->save("update");
				}
			}
		}
		if(isset($data['experiencia'])){
			foreach ($data['experiencia'] as $sup){
				$sup['fecini'] = new MongoDate(strtotime($sup['fecini']));
				$sup['fecfin'] = new MongoDate(strtotime($sup['fecfin']));
				if(!isset($ficha['experiencia'])) $ficha['experiencia'] = array();
				$ficha['experiencia'][] = $sup;
			}
		}
		if(!isset($data['_id'])){
			$ficha['fecreg'] = new MongoDate();
			$fic = $f->model("pe/fich")->params(array('data'=>$ficha))->save("insert")->obj;
			$data = $fic;
		}else
			$f->model("pe/fich")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$ficha))->save("update");
		$updtrab = array(
			'roles.trabajador.ficha'=>$data['_id'],
			'sexo'=>$data['sexo'],
			'estado_civil'=>$data['estado_civil']
		);
		if(isset($data['pension'])) $updtrab['roles.trabajador.pension'] = $data['pension'];
		if(isset($data['sangre'])) $updtrab['sangre'] = $data['sangre'];
		if(isset($data['subsidio'])) $updtrab['subsidio'] = $data['subsidio'];
		if(isset($data['cod_aportante'])) $updtrab['roles.trabajador.cod_aportante'] = $data['cod_aportante'];
		if(isset($data['fec_adm_sbpa'])) $updtrab['roles.trabajador.fecing'] = $ficha['fec_adm_sbpa'];
		if(isset($data['fecnac'])) $updtrab['roles.trabajador.fecnac'] = $ficha['fecnac'];
		$f->model("mg/entidad")->params(array('_id'=>$data['entidad']['_id'],'data'=>$updtrab))->save("update");
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($data['entidad']['_id'])))->get("one")->items;
		if(isset($vari['roles']['trabajador']['cargo']['nomb']))
			$cargo = $vari['roles']['trabajador']['cargo']['nomb'];
		else
			$cargo = $vari['roles']['trabajador']['cargo']['funcion'];
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>actualiz&oacute;</b> la <b>Ficha de Trabajador</b> de <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$vari['roles']['trabajador']['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para la organizaci&oacute;n <b>'.$vari['roles']['trabajador']['organizacion']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_lega(){
		global $f;
		if(isset($f->request->data['_id'])){
			$data = array('$push'=>array(),'$unset'=>array(),'$pull'=>array());
			if(isset($f->request->data['vac'])){
				foreach ($f->request->data['vac'] as $item){
					$data['$push']['vacaciones'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['lic'])){
				foreach ($f->request->data['lic'] as $item){
					$data['$push']['licencias'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['mer'])){
				foreach ($f->request->data['mer'] as $item){
					$data['$push']['meritos'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['dem'])){
				foreach ($f->request->data['dem'] as $item){
					$data['$push']['demeritos'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['com'])){
				foreach ($f->request->data['com'] as $item){
					$data['$push']['comisiones'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['dec'])){
				foreach ($f->request->data['dec'] as $item){
					$data['$push']['declaraciones'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['delet'])){
				if(isset($f->request->data['delet']['vac'])){
					for($i=0; $i<sizeof($f->request->data['delet']['vac']); $i++){
						$data['$unset']['vacaciones.'.$f->request->data['delet']['vac'][$i]] = true;
					}
					$data['$pull']['vacaciones'] = null;
				}
				if(isset($f->request->data['delet']['lic'])){
					for($i=0; $i<sizeof($f->request->data['delet']['lic']); $i++){
						$data['$unset']['licencias.'.$f->request->data['delet']['lic'][$i]] = true;
					}
					$data['$pull']['licencias'] = null;
				}
				if(isset($f->request->data['delet']['mer'])){
					for($i=0; $i<sizeof($f->request->data['delet']['mer']); $i++){
						$data['$unset']['meritos.'.$f->request->data['delet']['mer'][$i]] = true;
					}
					$data['$pull']['meritos'] = null;
				}
				if(isset($f->request->data['delet']['dem'])){
					for($i=0; $i<sizeof($f->request->data['delet']['dem']); $i++){
						$data['$unset']['demeritos.'.$f->request->data['delet']['dem'][$i]] = true;
					}
					$data['$pull']['demeritos'] = null;
				}
				if(isset($f->request->data['delet']['com'])){
					for($i=0; $i<sizeof($f->request->data['delet']['com']); $i++){
						$data['$unset']['comisiones.'.$f->request->data['delet']['com'][$i]] = true;
					}
					$data['$pull']['comisiones'] = null;
				}
				if(isset($f->request->data['delet']['dec'])){
					for($i=0; $i<sizeof($f->request->data['delet']['dec']); $i++){
						$data['$unset']['declaraciones.'.$f->request->data['delet']['dec'][$i]] = true;
					}
					$data['$pull']['declaraciones'] = null;
				}
			}
			if(sizeof($data['$push'])==0) unset($data['$push']);
			if(sizeof($data['$unset'])==0){
				unset($data['$unset']);
				unset($data['$pull']);
			}
			if(isset($data['$push']) or isset($data['$unset'])){
				if(sizeof($data['$unset'])>0){
					$f->model("pe/fich")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('$unset'=>$data['$unset'])))->save("custom");
					$f->model("pe/fich")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('$pull'=>$data['$pull'])))->save("custom");
				}
				if(sizeof($data['$push'])>0)
					$f->model("pe/fich")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('$pushAll'=>$data['$push'])))->save("custom");
			}
		}else{
			$entidad = $f->request->data['enti'];
			$entidad['_id'] = new MongoId($entidad['_id']);
			$entidad['cargo']['organizacion']['_id'] = new MongoId($entidad['cargo']['organizacion']['_id']);
			$ficha = array(
				'entidad'=>$entidad,
				'estudios'=>array(
					'primaria'=>false,
					'secundaria'=>false
				),
				'fecreg'=>new MongoDate()
			);
			if(isset($f->request->data['vac'])){
				foreach ($f->request->data['vac'] as $item){
					$ficha['vacaciones'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['lic'])){
				foreach ($f->request->data['lic'] as $item){
					$ficha['licencias'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['mer'])){
				foreach ($f->request->data['mer'] as $item){
					$ficha['meritos'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['dem'])){
				foreach ($f->request->data['dem'] as $item){
					$ficha['demeritos'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['com'])){
				foreach ($f->request->data['com'] as $item){
					$ficha['comisiones'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			if(isset($f->request->data['dec'])){
				foreach ($f->request->data['dec'] as $item){
					$ficha['declaraciones'][] = array(
						'fec'=>new MongoDate(strtotime($item['fec'])),
						'descr'=>$item['descr']
					);
				}
			}
			$fic = $f->model("pe/fich")->params(array('data'=>$ficha))->save("insert")->obj;
			$updtrab = array(
				'roles.trabajador.ficha'=>$fic['_id'],
				'sexo'=>'M',
				'estado_civil'=>'S'
			);
			$f->model("mg/entidad")->params(array('_id'=>$entidad['_id'],'data'=>$updtrab))->save("update");
		}
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($vari['roles']['trabajador']['cargo']['nomb']))
			$cargo = $vari['roles']['trabajador']['cargo']['nomb'];
		else
			$cargo = $vari['roles']['trabajador']['cargo']['funcion'];
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>actualiz&oacute;</b> el <b>Legajo de Trabajador</b> de <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$vari['roles']['trabajador']['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para la organizaci&oacute;n <b>'.$vari['roles']['trabajador']['organizacion']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_bono(){
		global $f;
		/*
		* POR ALGUNA RAZON NO SE ESTA GUARDANDO CORRECTAMENTE EL BONO,
		* HABRA QUE FORZARLO
		*/
		$tmp = false;
		$enti = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($enti['roles']['trabajador']['bonos'])){
      if($enti['roles']['trabajador']['bonos']==null)
        $tmp = true;
		}else{
      $tmp = true;
		}
		if($tmp==true){
      $f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$set'=>array(
				'roles.trabajador.bonos'=>array()
			))
		))->save('custom');
		}
		/***********************************************************/
		$f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$push'=>array(
				'roles.trabajador.bonos'=>array(
					'tipo'=>$f->request->data['tipo'],
					'cod'=>$f->request->data['cod'],
					'cod_sunat'=>$f->request->data['cod_sunat'],
					'descr'=>$f->request->data['descr'],
					'formula'=>$f->request->data['formula']
				)
			))
		))->save('custom');
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($vari['roles']['trabajador']['cargo']['nomb']))
			$cargo = $vari['roles']['trabajador']['cargo']['nomb'];
		else
			$cargo = $vari['roles']['trabajador']['cargo']['funcion'];
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se agreg&oacute; el bono <b>'.$f->request->data['descr'].'</b> al  trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$vari['roles']['trabajador']['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para la organizaci&oacute;n <b>'.$vari['roles']['trabajador']['organizacion']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_update_bono(){
		global $f;
		$data = $f->request->data;
		/*$bonos = array();
		if(isset($data["bonos"])){
			foreach($data["bonos"] as $bono){
				array_push($bonos,$bono);
			}
		}*/
		$f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$set'=>array(
				'roles.trabajador.bonos'=>$f->request->data['bonos']
			))
		))->save('custom');
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($vari['roles']['trabajador']['cargo']['nomb']))
			$cargo = $vari['roles']['trabajador']['cargo']['nomb'];
		else
			$cargo = $vari['roles']['trabajador']['cargo']['funcion'];
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>actualizaron</b> los bonos del trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$vari['roles']['trabajador']['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para la organizaci&oacute;n <b>'.$vari['roles']['trabajador']['organizacion']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_retencion(){
		global $f;
		$retencion = array();
		foreach ($f->request->data['retencion'] as $item){
			$item['entidad']['_id'] = new MongoId($item['entidad']['_id']);
			$retencion[] = $item;
		}
		$f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$set'=>array(
				'roles.trabajador.retencion'=>$retencion
			))
		))->save('custom');
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($vari['roles']['trabajador']['cargo']['nomb']))
			$cargo = $vari['roles']['trabajador']['cargo']['nomb'];
		else
			$cargo = $vari['roles']['trabajador']['cargo']['funcion'];
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>actualiz&oacute;</b> la informaci&oacute;n de <b>Retenci&oacute;n Jur&iacute;dica</b> del trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> con contrato <b>'.$vari['roles']['trabajador']['contrato']['nomb'].'</b> y cargo <b>'.$cargo.'</b> para la organizaci&oacute;n <b>'.$vari['roles']['trabajador']['organizacion']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_delete(){
		global $f;
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Trabajadores',
			'descr'=>'Se <b>Elimin&oacute;</b> al trabajador <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b>.'
		))->save('insert');
		$f->datastore->mg_entidades->remove(array('_id'=>new MongoId($f->request->data['_id'])));
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/trab.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/trab.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("pe/trab.details");
	}
	function execute_ficha(){
		global $f;
		$f->response->view("pe/trab.ficha");
	}
	function execute_lega(){
		global $f;
		$f->response->view("pe/trab.lega");
	}
	function execute_details_lega(){
		global $f;
		$f->response->view("pe/trab.details.lega");
	}
	function execute_des(){
		global $f;
		$f->response->view("pe/trab.des");
	}
	function execute_bono(){
		global $f;
		$f->response->view("pe/trab.bono");
	}
	function execute_details_bono(){
		global $f;
		$f->response->view("pe/trab.details.bono");
	}
	function execute_histo(){
		global $f;
		$f->response->view("pe/trab.details.histo");
	}
	function execute_reten(){
		global $f;
		$f->response->view("pe/trab.reten");
	}
	function execute_print_ficha(){
		global $f;
		$ficha = $f->model("pe/fich")->params(array("_id"=>new MongoId($f->request->id)))->get("enti");
		$enti = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($ficha->items!=null){
			if(isset($ficha->items['familia'])){
				if(isset($ficha->items['familia']['padre']))
					$ficha->items['familia']['padre'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['padre']))->get("one")->items;
				if(isset($ficha->items['familia']['madre']))
					$ficha->items['familia']['madre'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['madre']))->get("one")->items;
				if(isset($ficha->items['familia']['hermanos'])){
					foreach ($ficha->items['familia']['hermanos'] as $i=>$her){
						$ficha->items['familia']['hermanos'][$i] = $f->model("mg/entidad")->params(array("_id"=>$her))->get("one")->items;
					}
				}
				if(isset($ficha->items['familia']['conyuge']))
					$ficha->items['familia']['conyuge'] = $f->model("mg/entidad")->params(array("_id"=>$ficha->items['familia']['conyuge']))->get("one")->items;
				if(isset($ficha->items['familia']['hijos'])){
					foreach ($ficha->items['familia']['hijos'] as $i=>$hij){
						$ficha->items['familia']['hijos'][$i] = $f->model("mg/entidad")->params(array("_id"=>$hij))->get("one")->items;
					}
				}
			}
		}
		$out = array('ficha'=>$ficha->items,'enti'=>$enti->items);
		$f->response->view("pe/ficha.print",$out);
	}
	function execute_print_legajo(){
		global $f;
		$ficha = $f->model("pe/fich")->params(array("_id"=>new MongoId($f->request->id)))->get("enti");
		$enti = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$out = array('ficha'=>$ficha->items,'enti'=>$enti->items);
		$f->response->view("pe/legajo.print",$out);
	}
}
?>