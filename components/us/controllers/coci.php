<?php
class Controller_us_coci extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("us/coci")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("us/coci")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("us/coci")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'US',
				'bandeja'=>'Cocinas',
				'descr'=>'Se creó la Cocina <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("us/coci")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'US',
				'bandeja'=>'Cocinas',
				'descr'=>'Se actualizó la Cocina <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("us/coci")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("us/coci.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("us/coci.details");
	}
}
?>