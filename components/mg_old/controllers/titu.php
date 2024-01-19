<?php
class Controller_mg_titu extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("mg/titu.edit");
	}
	function execute_titular(){
		global $f;
		$model = $f->model("mg/entidad")->get("titular");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$model = $f->model('mg/entidad')->save('titular');
		$f->model('ac/log')->params(array(
			'modulo'=>'MG',
			'bandeja'=>'Informaci&oacute;n de Titular',
			'descr'=>'Se actualiz&oacute; la informaci&oacute;n de <b>Sociedad de Beneficencia P&uacute;blica</b>.'
		))->save('insert');
		$f->response->json( $model->obj );
	}
	function execute_locales(){
		global $f;
		$model = $f->model('mg/entidad')->get('locales');
		$f->response->json( $model->items );
	}
	function execute_select() {
		global $f;
		$f->response->view("mg/titu.select");
	}
}
?>