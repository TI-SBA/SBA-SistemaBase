<?php
class Controller_ch_conpa extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/conpa")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/conpa")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
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
			$model = $f->model("ch/conpa")->params(array('data'=>$data))->save("insert")->items;
			
		}else{
			$vari = $f->model("ch/conpa")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("ch/conpa")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ch/conpa.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ch/conpa.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ch/conpa')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('conpa');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$conpaulta = $f->model('ch/conpa')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/conpa.print",array('conpaulta'=>$conpaulta));

	}
}
?>
