<?php
class Controller_in_acta extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("in/acta")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("in/acta")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($f->request->data['_id'])){
			$item = $f->model("in/acta")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			foreach ($data['items'] as $k=>$itemq) {
				$data['items.'.$k.'.num'] = intval($itemq['num']);
				$data['items.'.$k.'.total'] = floatval($itemq['total']);
				$data['items.'.$k.'.fecven'] = new MongoDate(strtotime($itemq['fecven']));
				foreach ($itemq['conceptos'] as $j=>$conc) {
					$data['items.'.$k.'.conceptos.'.$j.'.monto'] = floatval($conc['monto']);
					$data['items.'.$k.'.conceptos.'.$j.'.tipo'] = $conc['tipo'];
					$data['items.'.$k.'.conceptos.'.$j.'.descr'] = $conc['descr'];
				}
			}
			unset($data['items']);
		}else{			
			foreach ($data['items'] as $k=>$item) {
				$data['items'][$k]['num'] = intval($item['num']);
				$data['items'][$k]['total'] = floatval($item['total']);
				$data['items'][$k]['fecven'] = new MongoDate(strtotime($item['fecven']));
				foreach ($item['conceptos'] as $j=>$conc) {
					$data['items'][$k]['conceptos'][$j]['monto'] = floatval($conc['monto']);
					$data['items'][$k]['conceptos'][$j]['descr'] = $conc['descr'];
					$data['items'][$k]['conceptos'][$j]['tipo'] = $conc['tipo'];
				}
			}
		}
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['arrendatario']))
			$data['arrendatario']['_id'] = new MongoId($data['arrendatario']['_id']);
		if(isset($data['inmueble']))
			$data['inmueble']['_id'] = new MongoId($data['inmueble']['_id']);
		if(isset($data['inmueble']['sublocal']))
			$data['inmueble']['sublocal']['_id'] = new MongoId($data['inmueble']['sublocal']['_id']);
		if(isset($data['inmueble']['tipo']))
			$data['inmueble']['tipo']['_id'] = new MongoId($data['inmueble']['tipo']['_id']);
		if(isset($data['cuotas']))
			$data['cuotas'] = intval($data['cuotas']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("in/acta")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Acta de Conciliacion',
				'descr'=>'Se creó el Acta de Conciliacion <b>'.$data['num'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("in/acta")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Acta de Conciliacion',
				'descr'=>'Se actualizo el Acta de Conciliacion <b>'.$data['num'].'</b>.'
			))->save('insert');
			$model = $f->model("in/acta")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_get_by_titu(){
		global $f;
		$acta = $f->model('in/acta')->params(array('filter'=>array(
			'arrendatario._id'=>new MongoId($f->request->data['_id'])
		)))->get('all')->items;
		$f->response->json($acta);
	}
	function execute_delete(){
		global $f;
		$acta = $f->model('in/acta')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$acta['feceli'] = new MongoDate();
		$acta['coleccion'] = 'in_actas_conciliacion';
		$acta['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($acta);
		$f->datastore->in_actas_conciliacion->remove(array('_id'=>$acta['_id']));
		
		$f->response->print(true);
	}
	function execute_edit(){
		global $f;
		$f->response->view("in/acta.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("in/acta.details");
	}
}
?>