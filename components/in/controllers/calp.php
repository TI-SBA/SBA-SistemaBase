<?php
class Controller_in_calp extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("in/calp")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("in/calp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("in/calp")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Calendario de Pagos',
				'descr'=>'Se creó la Fecha de Pago <b>'.$data['dia'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("in/calp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Calendario de Pagos',
				'descr'=>'Se actualizó la Fecha de Pago <b>'.$vari['dia'].'</b>.'
			))->save('insert');
			$model = $f->model("in/calp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("in/calp.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("in/calp.details");
	}
}
?>