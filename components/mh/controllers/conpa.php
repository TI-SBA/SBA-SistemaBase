<?php
class Controller_mh_conpa extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mh/conpa")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("mh/conpa")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;



		if(!isset($f->request->data['_id'])){
			$data['his_cli']= floatval($data['his_cli']);
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("mh/conpa")->params(array('data'=>$data))->save("insert")->items;
			
		}else{
			$vari = $f->model("mh/conpa")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("mh/conpa")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("mh/conpa.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("mh/conpa.details");
	}
	function execute_delete(){
		global $f;
		$f->model('mh/conpa')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('conpa');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$conpaulta = $f->model('mh/conpa')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("mh/conpa.print",array('conpaulta'=>$conpaulta));

	}
	function execute_get_alta(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['ini']) && isset($data['fin'])){
			$ini = strtotime($data['ini'].' 00:00:00');
			$fin = strtotime($data['fin'].' 23:59:59');
			$params['$and'] = array(
				array('fec_alta' =>array('$gte'=>new MongoDate($ini))),
				array('fec_alta' =>array('$lte'=>new MongoDate($fin)))
			);
		}
		$items = $f->model("mh/conpa")->params($params)->get('all')->items;
		
	}
}
?>
