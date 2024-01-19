<?php
class Controller_ct_saldm extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres.auxi");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/saldm")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/saldm")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$filter = $f->request->data;
		$model = $f->model('ct/saldm')->params(array('fields'=>$fields,'filter'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_filter_one(){
		global $f;
		$fields = array();
		$filter = $f->request->data;
		$model = $f->model('ct/saldm')->params(
				array(
					'mes'=>floatval($filter["periodo"]["mes"]),
					'ano'=>$filter["periodo"]["ano"]
				))->get('filter_one');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/saldm")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			$f->model("ct/saldm")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/saldm")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_cerrar(){
		global $f;
		$data = $f->request->data;
		if(isset($f->request->data['_id'])){
			$f->model("ct/saldm")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");			
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/epres.auxi.edit");
	}
}
?>