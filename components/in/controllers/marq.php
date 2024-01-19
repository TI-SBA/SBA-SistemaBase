<?php
class Controller_in_marq extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("in/marq")->params($params)->get("lista") );
	}
	function execute_edit_marq(){
		global $f;
		$f->response->view("in/marq.edit");
	}
	function execute_edit_marq_tienda(){
		global $f;
		$f->response->view("in/marq.tienda.edit");
	}
	function execute_edit_marq_oficina(){
		global $f;
		$f->response->view("in/marq.oficina.edit");
	}
	function execute_edit_marq_stand(){
		global $f;
		$f->response->view("in/marq.stand.edit");
	}
}
?>