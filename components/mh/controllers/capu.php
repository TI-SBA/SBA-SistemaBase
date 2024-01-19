<?php
class Controller_mh_capu extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mh/capu")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		if(isset($f->request->data['fields'])) $fields = array('nomb'=>true);
		else $fields = array();
		$model = $f->model('mh/capu')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$items = $f->model("mh/capu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['local']))
			$data['local']['_id'] = new MongoId($data['local']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = "H";
			$f->model("mh/capu")->params(array('data'=>$data))->save("insert");
		}else{
			$vari = $f->model("mh/capu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
			}
			$f->model("mh/capu")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_details(){
		global $f;
		$f->response->view("mh/capu.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mh/capu.edit");
	}
}
?>