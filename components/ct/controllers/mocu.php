<?php
class Controller_ct_mocu extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view('ct/mocu');
		$f->response->print("</div>");
		$f->response->view('ct/mocu.grid');
	}
	function execute_lista(){
		global $f;
		$f->response->json(array(
			/*'auxs'=>$f->model("ct/auxs")->params(array(
				"ano"=>$f->request->data['ano'],
				"mes"=>$f->request->data['mes'],
				"tipo"=>$f->request->data['tipo']
			))->get("lista_periodo")->items,*/
			'sald'=>$f->model("ct/saux")->params(array(
				"ano"=>$f->request->data['ano'],
				"mes"=>$f->request->data['mes'],
				"tipo"=>$f->request->data['tipo']
			))->get("lista_periodo")->items
		));
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ct/mocu")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ct/mocu')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/mocu")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
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
			$f->model("ct/mocu")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/mocu")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/mocu.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/mocu.select");
	}
	function execute_gen(){
		global $f;
		$f->response->view("ct/mocu.gen");
	}
}
?>