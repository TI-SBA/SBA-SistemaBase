<?php
class Controller_ts_liba extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ts/saldlibr")->params($params)->get("lista") );
	}
	function execute_lista_mov(){
		global $f;
		$model = $f->model("ts/moba")->params(array('periodo'=>$f->request->data['periodo'],'ctban'=>new MongoId($f->request->data['ctban'])))->get("all_periodo")->items;
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ts/saldlibr")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("in/tipo")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("in/tipo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("in/tipo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("in/tipo.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("in/tipo.details");
	}
}
?>