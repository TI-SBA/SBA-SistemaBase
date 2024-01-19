<?php
class Controller_ci_service extends Controller {
	function execute_index() {
		global $f;
		require('libraries/rpc/phprpc_server.php');
		$server = new PHPRPC_Server();
		$server->add('execute_td_tupa_one', $this);
		$server->add('execute_td_tupa_listar', $this);
		$server->add('execute_td_expd_one', $this);
		$server->add('execute_td_expd_listar', $this);
		$server->add('execute_in_espa_one', $this);
		$server->add('execute_in_espa_listar', $this);
		$server->add('execute_cm_espa_one', $this);
		$server->add('execute_cm_espa_listar', $this);
		$server->add('execute_po_visi_listar', $this);

		$server->setDebugMode(true);
		$server->start();
	}
	function execute_td_tupa_one() {
		global $f;
		$data = $f->model("td/tupa")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		return $data;
	}
	function execute_td_tupa_listar() {
		global $f;
		$data = $f->model("td/tupa")->params(array('texto'=>$f->request->data['texto']))->get("search_all");
		return $data;
	}
	function execute_td_expd_one() {
		global $f;
		$data = $f->model("td/expd")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		return $data;
	}
	function execute_td_expd_listar() {
		global $f;
		$params = array(
			'text'=>$f->request->data['texto'],
			'page'=>$f->request->data['page'],
			'page_rows'=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['num'])){
			if($f->request->data['num']!=''){
				$params['num'] = $f->request->data['num'];
				$params['text'] = '';
			}
		}
		$data = $f->model("td/expd")->params($params)->get("search_all");
		return $data;
	}
	function execute_in_espa_one(){
		global $f;
		$data = $f->model("in/espa")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		return $data;
	}
	function execute_in_espa_listar(){
		global $f;
		$params = array(
			'text'=>$f->request->data['texto'],
			'page'=>$f->request->data['page'],
			'page_rows'=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['tipo'])){
			if($f->request->data['tipo']!=''){
				$params['uso'] = $f->request->data['tipo'];
			}
		}
		$data = $f->model("in/espa")->params($params)->get("search_all");
		return $data;
	}
	function execute_cm_espa_one(){
		global $f;
		$data = $f->model("cm/espa")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		return $data;
	}
	function execute_cm_espa_listar(){
		global $f;
		$params = array(
			'texto'=>$f->request->data['texto'],
			'page'=>$f->request->data['page'],
			'page_rows'=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['tipo'])){
			if($f->request->data['tipo']!=''){
				$params['tipo'] = $f->request->data['tipo'];
			}
		}
		if(isset($f->request->data['sector'])){
			if($f->request->data['sector']!='')
				$params['sector'] = $f->request->data['sector'];
		}
		if(isset($f->request->data['estado'])){
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		}
		$data = $f->model("cm/espa")->params($params)->get("search");
		return $data;
	}
	function execute_po_visi_listar(){
		global $f;
		$params = array(
			'fecha'=>$f->request->data['fecha'],
			'page'=>$f->request->data['page'],
			'page_rows'=>$f->request->data['page_rows']
		);
		print_r($params);
		$data = $f->model("po/visi")->params($params)->get("search");
		//$data=array("hola mundo");
		
		return $data;
	}
}
?>
