<?php
class Controller_in_inmu extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("in/inmu")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("in/inmu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_all_sub(){
		global $f;
		$items = $f->model("in/inmu")->params(array("sublocal"=>new MongoId($f->request->data['_id'])))->get("all")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['tipo']))
			$data['tipo']['_id'] = new MongoId($data['tipo']['_id']);
		if(isset($data['tipo']['cuenta']))
			$data['tipo']['cuenta']['_id'] = new MongoId($data['tipo']['cuenta']['_id']);
		if(isset($data['sublocal']))
			$data['sublocal']['_id'] = new MongoId($data['sublocal']['_id']);
		if(isset($data['sublocal']['tipo']))
			$data['sublocal']['tipo']['_id'] = new MongoId($data['sublocal']['tipo']['_id']);
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("in/inmu")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'SubLocal',
				'descr'=>'Se creó el Inmueble <b>'.$data['direccion'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("in/inmu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'SubLocal',
				'descr'=>'Se actualizó el Inmueble <b>'.$vari['direccion'].'</b>.'
			))->save('insert');
			$model = $f->model("in/inmu")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("in/inmu.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("in/inmu.details");
	}
}
?>