<?php
class Controller_ts_cheq extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ts/cheq")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ts/cheq")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['entidad']['_id']))
			$data['entidad']['_id'] = new MongoId($data['entidad']['_id']);
		if(isset($data['fec']))
			$data['fec'] = new MongoDate(strtotime($data['fec']));
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'R';
			$model = $f->model("ts/cheq")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cheques',
				'descr'=>'Se creó el Cheque <b>'.$data['entidad']['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ts/cheq")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cheques',
				'descr'=>'Se actualizó el Cheque <b>'.$vari['entidad']['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ts/cheq")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/cheq.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/cheq.details");
	}
	function execute_print(){
		global $f;
		$cheque = $f->model("ts/cheq")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->view("ts/cheq.print",array("cheque"=>$cheque));
	}
}
?>