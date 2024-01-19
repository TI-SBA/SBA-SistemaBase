<?php
class Controller_ha_cont extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ha/pame")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ha/pame")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['paciente']))
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		if(isset($data['medicinas'])){
			foreach ($data['medicinas'] as $i=>$med) {
				if(isset($med['stock'])) $data['medicinas'][$i]['stock'] = floatval($med['stock']);
				if(isset($med['hist'][0]['stock'])) $data['medicinas'][$i]['hist'][0]['stock'] = floatval($med['hist'][0]['stock']);
				if(isset($med['hist'][0]['cant'])) $data['medicinas'][$i]['hist'][0]['cant'] = floatval($med['hist'][0]['cant']);
				if(isset($med['hist'][0]['fec'])) $data['medicinas'][$i]['hist'][0]['fec'] = new MongoDate(strtotime($med['hist'][0]['fec']));
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'A';
			$model = $f->model("ha/pame")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'ha',
				'bandeja'=>'Control de Medicinas',
				'descr'=>'Se creó el Control de Medicinas para <b>'.$data['paciente']['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ha/pame")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'ha',
				'bandeja'=>'Control de Medicinas',
				'descr'=>'Se actualizó el Control de Medicinas para <b>'.$vari['paciente']['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ha/pame")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_save_dis(){
		global $f;
		$data = $f->request->data;
		$cont = $f->model('ha/pame')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		foreach($cont['medicinas'] as $i=>$medi){
			if(floatval($data['medicinas'][$i]['cant'])>0){
				$f->model('ha/pame')->params(array(
					'_id'=>$cont['_id'],
					'data'=>array(
						'$inc'=>array(
							'medicinas.'.$i.'.stock'=>-floatval($data['medicinas'][$i]['cant'])
						)
					)
				))->save('custom');
				$f->model('ha/pame')->params(array(
					'_id'=>$cont['_id'],
					'data'=>array(
						'$push'=>array(
							'medicinas.'.$i.'.hist'=>array(
								'fec'=>new MongoDate(strtotime($data['medicinas'][$i]['fec'])),
								'cant'=>-floatval($data['medicinas'][$i]['cant']),
								'tipo'=>$data['medicinas'][$i]['tipo'],
								'stock'=>($medi['stock']-floatval($data['medicinas'][$i]['cant'])),
								'trabajador'=>$f->session->userDBMin
							)
						)
					)
				))->save('custom');
			}
		}
		$f->response->print('true');
	}
	function execute_save_aum(){
		global $f;
		$data = $f->request->data;
		$cont = $f->model('ha/pame')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		foreach($data['medicinas'] as $i=>$medi){
			if(floatval($medi['cant'])>0){
				if(isset($medi['med'])){
					$f->model('ha/pame')->params(array(
						'_id'=>$cont['_id'],
						'data'=>array(
							'$push'=>array(
								'medicinas'=>array(
									'med'=>$medi['med'],
									'stock'=>floatval($medi['cant']),
									'hist'=>array(
										array(
											'fec'=>new MongoDate(strtotime($medi['fec'])),
											'cant'=>floatval($medi['cant']),
											'tipo'=>$medi['tipo'],
											'stock'=>floatval($medi['cant'])+$cont['medicinas'][$i]['stock'],
											'trabajador'=>$f->session->userDBMin
										)
									)
								)
							)
						)
					))->save('custom');
				}else{
					$f->model('ha/pame')->params(array(
						'_id'=>$cont['_id'],
						'data'=>array(
							'$inc'=>array(
								'medicinas.'.$i.'.stock'=>floatval($medi['cant'])
							)
						)
					))->save('custom');
					$f->model('ha/pame')->params(array(
						'_id'=>$cont['_id'],
						'data'=>array(
							'$push'=>array(
								'medicinas.'.$i.'.hist'=>array(
									'fec'=>new MongoDate(strtotime($medi['fec'])),
									'cant'=>floatval($medi['cant']),
									'tipo'=>$medi['tipo'],
									'stock'=>floatval($medi['cant'])+$cont['medicinas'][$i]['stock'],
									'trabajador'=>$f->session->userDBMin
								)
							)
						)
					))->save('custom');
				}
			}
		}
		$f->response->print('true');
	}
	function execute_save_devolver(){
		global $f;
		$control = $f->model('ha/pame')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		foreach ($control['medicinas'] as $i=>$medi) {
			if($medi['stock']!=0){
				$f->model('ha/pame')->params(array(
					'_id'=>$control['_id'],
					'data'=>array(
						'$push'=>array(
							'medicinas.'.$i.'.hist'=>array(
								'fec'=>new MongoDate(),
								'cant'=>-floatval($medi['stock']),
								'tipo'=>'D',
								'stock'=>0,
								'trabajador'=>$f->session->userDBMin
							)
						)
					)
				))->save('custom');
				$f->model('ha/pame')->params(array(
					'_id'=>$control['_id'],
					'data'=>array(
						'medicinas.'.$i.'.stock'=>0
					)
				))->save('update');
			}
		}
		$f->model('ha/pame')->params(array(
			'_id'=>$control['_id'],
			'data'=>array(
				'estado'=>'D'
			)
		))->save('update');
		$f->response->print('true');
	}
	function execute_save_deshacer(){
		global $f;
		$control = $f->model('ha/pame')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		foreach ($control['medicinas'] as $i=>$medi) {
			if($medi['med']==$f->request->data['med']){
				$ultimo = $medi['hist'][sizeof($medi['hist'])-1];
				$hist = array();
				for($j=0; $j<(sizeof($medi['hist'])-1); $j++){
					$hist[] = $medi['hist'][$j];
				}



				$f->model('ha/pame')->params(array(
					'_id'=>$control['_id'],
					'data'=>array(
						'$set'=>array(
							'medicinas.'.$i.'.hist'=>$hist
						)
					)
				))->save('custom');
				$f->model('ha/pame')->params(array(
					'_id'=>$control['_id'],
					'data'=>array(
						'$inc'=>array(
							'medicinas.'.$i.'.stock'=>-floatval($ultimo['cant'])
						)
					)
				))->save('custom');

				$f->model('ac/log')->params(array(
					'modulo'=>'ha',
					'bandeja'=>'Control de Medicinas',
					'id'=>$control['_id'],
					'medicina'=>$f->request->data['med'],
					'item'=>$ultimo,
					'descr'=>'Se deshace el movimiento de Control de Medicinas para <b>'.$control['paciente']['nomb'].' '.$control['paciente']['appat'].' '.$control['paciente']['apmat'].'</b> para la medicina '.$f->request->data['med'].'.'
				))->save('insert');
				break;
			}
		}
		$f->response->print('true');
	}
	function execute_edit(){
		global $f;
		$f->response->view("ha/cont.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ha/cont.details");
	}
	function execute_print(){
		global $f;
		$item = $f->model("ha/pame")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->view("ha/cont.print.xls",array('data'=>$item));
	}
}
?>