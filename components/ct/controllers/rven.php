<?php
class Controller_ct_rven extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('&nbsp; Periodo <input type="text" name="periodo">');
		$f->response->print('<button name="btnAgregar">Agregar Registro</button>');
		$f->response->print('<button name="btnCerrar">Cerrar Periodo</button>');
		$f->response->print("</div>");
		$f->response->view("ct/rven");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/rven")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/rven")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ct/rven')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/rven")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_edit(){
		global $f;
		$cod = $f->model("ct/rven")->get("cod")->items;
		if($cod==null) $cod=1;
		else $cod = floatval($cod)+1;
		$f->response->json(array(
			'tico'=>$f->model("ct/tico")->get("all")->items,
			'periodo'=>(isset($f->request->data['mes']))?date('Ym00',strtotime($f->request->data['ano'].'-'.((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'-01')):date('Ym00'),
			'cod'=>$cod
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fecemi'])) $data['fecemi'] = new MongoDate(strtotime($data['fecemi']));
		if(isset($data['fecven'])) $data['fecven'] = new MongoDate(strtotime($data['fecven']));
		if(isset($data['tipo_comprobante']['_id'])) $data['tipo_comprobante']['_id'] = new MongoId($data['tipo_comprobante']['_id']);
		if(isset($data['proveedor']['_id'])) $data['proveedor']['_id'] = new MongoId($data['proveedor']['_id']);
		if(isset($data['fecemi_mod'])) $data['fecemi_mod'] = new MongoDate(strtotime($data['fecemi_mod']));
		if(isset($data['tipo_doc_mod']['_id'])) $data['tipo_doc_mod']['_id'] = new MongoId($data['tipo_doc_mod']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado_registro'] = "A";
			$data['autor'] = $f->session->userDB;
			$f->model("ct/rven")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/rven")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_cerrar(){
		global $f;
		$f->model("ct/rven")->params(array('filter'=>array(
			'periodo'=>(isset($f->request->data['mes']))?date('Ym00',strtotime($f->request->data['ano'].'-'.((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'-01')):date('Ym00')
		),'data'=>array('$set'=>array(
			'estado_registro'=>'C'
		))))->save("custom");
		$f->response->print("true");
	}
	function execute_details(){
		global $f;
		$f->response->view("ct/rven.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/rven.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/rven.select");
	}
}
?>