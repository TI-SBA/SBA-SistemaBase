<?php
class Controller_lg_unid extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/unid")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		$model = $f->model('lg/unid')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("lg/unid")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$f->model("lg/unid")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Unidades',
				'descr'=>'Se cre&oacute; la unidad <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Unidades',
				'descr'=>'Se actualiz&oacute; la unidad <b>'.$data['nomb'].'</b>.'
			))->save('insert');
			$f->model("lg/unid")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/unid.edit");
	}
}
?>