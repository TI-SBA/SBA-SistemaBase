<?php
class Controller_ge_proy extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ge/proy")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ge/proy")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($f->request->data['all'])){
			$items['metas'] = $f->model("ge/meta")->params(array("proyecto"=>$items['_id']))->get("proyecto")->items;
		}
		$f->response->json( $items );
	}
	function execute_get_meta(){
		global $f;
		$items = $f->model("ge/meta")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['miembros'])){
			foreach ($data['miembros'] as $i=>$miembro) {
				$data['miembros'][$i]['_id'] = new MongoId($miembro['_id']);
				$data['miembros'][$i]['owner']['_id'] = new MongoId($miembro['owner']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("ge/proy")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'GE',
				'bandeja'=>'Gestion de Proyectos',
				'descr'=>'Se creó el Proyecto <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ge/proy")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'GE',
				'bandeja'=>'Gestion de Proyectos',
				'descr'=>'Se actualizó el Proyecto <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ge/proy")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_save_meta(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['proyecto'])) $data['proyecto'] = new MongoId($data['proyecto']);
		/*if(isset($data['miembros'])){
			foreach ($data['miembros'] as $i=>$miembro) {
				$data['miembros'][$i]['_id'] = new MongoId($miembro['_id']);
			}
		}*/
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$model = $f->model("ge/meta")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'GE',
				'bandeja'=>'Gestion de Proyectos',
				'descr'=>'Se creo la Meta <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ge/meta")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'GE',
				'bandeja'=>'Gestion de Proyectos',
				'descr'=>'Se actualizo la Meta <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ge/meta")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_save_tarea(){
		global $f;
		$data = $f->request->data;
		$meta['fecmod'] = new MongoDate();
		$meta['trabajador'] = $f->session->userDBMin;
		$f->model("ge/meta")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$meta))->save("update");
		if(isset($data['index'])){
			$f->model("ge/meta")->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>array(
					'$set'=>array('tareas.'.$data['index']=>array(
						'userid'=>$f->session->user['userid'],
						'tarea'=>$data['tarea'],
						'descr'=>$data['descr'],
						'fec'=>new MongoDate(strtotime($data['fec'])),
						'fecini'=>new MongoDate(strtotime($data['fecini'])),
						'prioridad'=>$data['prioridad'],
						'estado'=>$data['estado']
					))
				)
			))->save("custom");
			$f->model("ge/meta")->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>array(
					'$push'=>array('log'=>array(
						'tipo'=>'UPD',
						'fec'=>new MongoDate(),
						'tarea'=>array(
							'userid'=>$f->session->user['userid'],
							'tarea'=>$data['tarea'],
							'descr'=>$data['descr'],
							'fec'=>new MongoDate(strtotime($data['fec'])),
							'fecini'=>new MongoDate(strtotime($data['fecini'])),
							'prioridad'=>$data['prioridad'],
							'estado'=>$data['estado']
						)
					))
				)
			))->save("custom");
		}else{
			$f->model("ge/meta")->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>array(
					'$push'=>array('tareas'=>array(
						'userid'=>$f->session->user['userid'],
						'tarea'=>$data['tarea'],
						'descr'=>$data['descr'],
						'fec'=>new MongoDate(strtotime($data['fec'])),
						'fecini'=>new MongoDate(strtotime($data['fecini'])),
						'prioridad'=>$data['prioridad'],
						'estado'=>'P'
					))
				)
			))->save("custom");
			$f->model("ge/meta")->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>array(
					'$push'=>array('log'=>array(
						'tipo'=>'CRE',
						'fec'=>new MongoDate(),
						'tarea'=>array(
							'userid'=>$f->session->user['userid'],
							'tarea'=>$data['tarea'],
							'descr'=>$data['descr'],
							'fec'=>new MongoDate(strtotime($data['fec'])),
							'fecini'=>new MongoDate(strtotime($data['fecini'])),
							'prioridad'=>$data['prioridad'],
							'estado'=>'P'
						)
					))
				)
			))->save("custom");
		}
		/*******************************************************************************
		* CALCULAR PORCENTAJE
		*******************************************************************************/
		$meta = $f->model("ge/meta")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$metas = $f->model("ge/meta")->params(array("proyecto"=>$meta['proyecto']))->get("proyecto")->items;
		$tareas = 0;
		$tareas_comp = 0;
		foreach ($metas as $i=>$item) {
			if(isset($item['tareas'])){
				foreach ($item['tareas'] as $k=>$tarea) {
					$tareas++;
					if($tarea['estado']=='C') $tareas_comp++;
				}
			}
		}
		if($tareas==0){
			$porc = 0;
		}else{
			$porc = ($tareas_comp*100)/$tareas;
		}
		$f->model("ge/proy")->params(array('_id'=>$meta['proyecto'],'data'=>array(
			'porcentaje'=>$porc,
			'fecmod'=>new MongoDate(),
			'trabajador'=>$f->session->userDBMin
		)))->save("update");
		$f->response->print('true');
	}
	function execute_update_tarea(){
		global $f;
		$data = $f->request->data;
		$meta['fecmod'] = new MongoDate();
		$meta['trabajador'] = $f->session->userDBMin;
		$f->model("ge/meta")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$meta))->save("update");
		$f->model("ge/meta")->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array(
				'$set'=>array(
					'tareas.'.$data['index'].'.estado'=>$f->request->data['estado'],
					'tareas.'.$data['index'].'.feccon'=> new MongoDate()
				)
			)
		))->save("custom");
		if($f->request->data['estado']=='C'){
			$f->model("ge/meta")->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>array(
					'$push'=>array('log'=>array(
						'tipo'=>'COM',
						'fec'=>new MongoDate(),
						'tarea'=>array(
							'userid'=>$f->session->user['userid'],
							'tarea'=>$data['tarea'],
							'fec'=>new MongoDate(),
							'estado'=>$data['estado']
						)
					))
				)
			))->save("custom");
		}else{
			$f->model("ge/meta")->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>array(
					'$push'=>array('log'=>array(
						'tipo'=>'DES',
						'fec'=>new MongoDate(),
						'tarea'=>array(
							'userid'=>$f->session->user['userid'],
							'tarea'=>$data['tarea'],
							'fec'=>new MongoDate(),
							'estado'=>$data['estado']
						)
					))
				)
			))->save("custom");
		}
		/*******************************************************************************
		* CALCULAR PORCENTAJE
		*******************************************************************************/
		$meta = $f->model("ge/meta")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$metas = $f->model("ge/meta")->params(array("proyecto"=>$meta['proyecto']))->get("proyecto")->items;
		$tareas = 0;
		$tareas_comp = 0;
		foreach ($metas as $i=>$item) {
			if(isset($item['tareas'])){
				foreach ($item['tareas'] as $k=>$tarea) {
					$tareas++;
					if($tarea['estado']=='C') $tareas_comp++;
				}
			}
		}
		if($tareas==0){
			$porc = 0;
		}else{
			$porc = ($tareas_comp*100)/$tareas;
		}
		$f->model("ge/proy")->params(array('_id'=>$meta['proyecto'],'data'=>array(
			'porcentaje'=>$porc,
			'fecmod'=>new MongoDate(),
			'trabajador'=>$f->session->userDBMin
		)))->save("update");
		$f->response->print('true');
	}
	function execute_delete_meta(){
		global $f;
		$item = $f->model('ge/meta')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'ge_metas';
		$item['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($item);
		$f->datastore->ge_metas->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ge/proy.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ge/proy.details");
	}
	function execute_avance(){
		global $f;
		$f->response->view("ge/proy.avance");
	}
	function execute_fomato_jsgantt(){
		global $f;
		$items = $f->model("ge/proy")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		{
			$items['metas'] = $f->model("ge/meta")->params(array("proyecto"=>$items['_id']))->get("proyecto")->items;
		}
		$jsgantt=[];
		$colors=array('gtaskred','gtaskyellow','gtaskgreen','gtaskblue','gtaskpink');
		$proyecto = 1;
		$proyecto_pname = (strlen($items['nomb']) > 30) ? substr($items['nomb'],0,30).'…' : $items['nomb'];
		$jsgantt[]=array(
			'pid' => $proyecto,
			'pname' => $proyecto_pname,
			'pstart' => '',
			'pend' => '',
			'pclass' => 'ggroupblack',
			'plink' => '',
			'pmile' => 0,
			'pres' => $items['autor']['appat'],
			'pcomp' => $items['porcentaje'],
			'pgroup' => 1,
			'pparent' => 0,
			'popen' =>  1,
			'pdepend' => '',
			'pcaption' => '',
			'pnotes' => $items['descr'],
			'pgantt' => 'g',
		);
		if(isset($items['metas'])){
			foreach ($items['metas'] as $i =>  $meta) {
					$meta_pname = (strlen($meta['nomb']) > 27) ? substr($meta['nomb'],0,27).'…' : $meta['nomb'];
    				$jsgantt[]=array(
					'pid' => intval($proyecto.($i+1)),
					'pname' => $meta_pname,
					'pstart' => '',
					'pend' => '',
					'pclass' => 'ggroupblack',
					'plink' => '',
					'pmile' => 0,
					'pres' => $meta['autor']['appat'],
					'pcomp' => 0,
					'pgroup' => 1,
					'pparent' => intval($proyecto),
					'popen' =>  1,
					'pdepend' => '',
					'pcaption' => '',
					'pnotes' => $meta['descr'],
					'pgantt' => 'g',
				);
				if(isset($meta['tareas'])){
					foreach ($meta['tareas'] as $j => $tarea) {
						$tarea_pname = (strlen($tarea['tarea']) > 25) ? substr($tarea['tarea'],0,25).'…' : $tarea['tarea'];
						if(isset($tarea['fecini']))	$pstart = date('Y-m-d', $tarea['fecini']->sec);
						else $pstart = date('Y-m-d', $items['fecreg']->sec);
						$tarea_pcomp = ($tarea['estado'] == "C") ? 100 : 0;
						$jsgantt[]=array(
							'pid' => intval($proyecto.($i+1).($j+1)),
							'pname' => $tarea_pname,
							'pstart' => $pstart,
							'pend' => date('Y-m-d', $tarea['fec']->sec),
							'pclass' => $colors[array_rand($colors, 1)],
							'plink' => '',
							'pmile' => 0,
							'pres' => $tarea['userid'],
							'pcomp' => $tarea_pcomp,
							'pgroup' => 0,
							'pparent' => intval($proyecto.($i+1)),
							'popen' =>  1,
							'pdepend' => '',
							'pcaption' => '',
							'pnotes' => $tarea['descr'],
							'pgantt' => 'g',
						);
					}
				}
			}
		}
		$items['jsgantt'] = $jsgantt;
		header("Content-type:application/json");
		echo json_encode( $items ,JSON_UNESCAPED_UNICODE);
		//$f->response->json( $items );
	}
	function execute_gantt(){
		global $f;
		$f->response->view("ge/proy.gantt");
	}
	function execute_edit_meta(){
		global $f;
		$f->response->view("ge/proy.meta");
	}
	function execute_edit_tarea(){
		global $f;
		$f->response->view("ge/proy.tarea");
	}
}
?>
