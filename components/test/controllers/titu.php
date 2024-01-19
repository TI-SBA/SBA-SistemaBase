<?php
class Controller_test_titu extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("test/titu.edit");
	}
	function execute_titular(){
		global $f;
		$model = $f->model("mg/entidad")->get("titular");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$model = $f->model('mg/entidad')->save('titular');
		$f->response->json( $model->obj );
	}
	function execute_locales(){
		global $f;
		$model = $f->model('mg/entidad')->get('locales');
		$f->response->json( $model->items );
	}
	function execute_select() {
		global $f;
		$f->response->view("test/titu.select");
	}
}
?>