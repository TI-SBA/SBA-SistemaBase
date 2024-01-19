<?php
class Controller_ct_mayc extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view('ct/mayc');
		$f->response->print("</div>");
		$f->response->view('ct/mayc.grid');
	}
	function execute_mayor(){
		global $f;
		$f->response->json(
			$f->model("ct/saux")->params(array(
				"ano"=>$f->request->data['ano'],
				"mes"=>$f->request->data['mes'],
				"cuenta_mayor"=>new MongoId($f->request->data['_id'])
			))->get("cuenta_mayor")->items
		);
	}
	function execute_auxs(){
		global $f;
		$f->response->json(
			$f->model("ct/auxs")->params(array(
				"saldo"=>new MongoId($f->request->data['saldo'])
			))->get("periodo")->items
		);
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/mayc")->params(array(
			"tipo"=>$f->request->data['tipo'],
			"cuenta"=>new MongoId($f->request->data['cuenta'])
		))->get("lista");
		$f->response->json( $model->items );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ct/mayc")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ct/mayc')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/mayc")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fecha'])) $data['fecha'] = new MongoDate(strtotime($data['fecha']));
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['cuenta_mayor']['_id'])) $data['cuenta_mayor']['_id'] = new MongoId($data['cuenta_mayor']['_id']);
		if(isset($data['subcuenta']['_id'])) $data['subcuenta']['_id'] = new MongoId($data['subcuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$f->model("ct/mayc")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/mayc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/mayc.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/mayc.select");
	}
	function execute_gen(){
		global $f;
		$f->response->view("ct/mayc.gen");
	}
}
?>