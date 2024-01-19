<?php
class Controller_ar_docu extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		$params['modulo'] = 'AR';
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mg/docu")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("mg/docu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_verify(){
		global $f;
		$items = $f->model("mg/docu")->params(array('data'=>array(
			"num"=>$f->request->data['num']
		)))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_all(){
		global $f;
		$items = $f->model("mg/docu")->get("all")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['oficina']))
			$data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$data['modulo'] = 'AR';
			$model = $f->model("mg/docu")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AR',
				'bandeja'=>'Documentos',
				'descr'=>'Se creó el Documento <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("mg/docu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AR',
				'bandeja'=>'Documentos',
				'descr'=>'Se actualizó el Documento <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("mg/docu")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ar/docu.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ar/docu.details");
	}
}
?>