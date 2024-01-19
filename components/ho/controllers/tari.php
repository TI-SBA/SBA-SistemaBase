<?php
class Controller_ho_tari extends Controller {
	function execute_lista(){
		global $f;
		$params = array();
		if(isset($f->request->data['page']))
			$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ho/tari")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ho/tari")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
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
			$model = $f->model("ho/tari")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'HO',
				'bandeja'=>'Tarifa de Hospitalizaciones',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ho/tari")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			
			$model = $f->model("ho/tari")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ho/tari.edit");
	}
	function execute_edit_agri(){
		global $f;
		$f->response->view("ho/tari.edit.agri");
	}
	function execute_edit_gana(){
		global $f;
		$f->response->view("ho/tari.edit.gana");
	}
	function execute_details(){
		global $f;
		$f->response->view("ho/tari.details");
	}
}
?>