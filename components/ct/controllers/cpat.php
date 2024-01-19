<?php
class Controller_ct_cpat extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('<table style="display: inline;">
			<tbody>
				<tr>
					<td>Periodo: <input type="text" name="periodo"></td>
					<td>Pabellon: <select name="pabellon"></select></td>
				</tr>
			</tbody>
		</table>');
		//$f->response->print('<button name="btnIni" style="display: inline;">Guardar Periodo</button>');
		$f->response->print('<button name="btnAgregar" style="display: inline;">Agregar Registro</button>');
		$f->response->print('<button name="btnCerrar" style="display: inline;">Cerrar Periodo</button>');
		$f->response->print("</div>");
		$f->response->view("ct/cpat");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/cpat")->params(array(
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano,
			"pabellon"=>new MongoId($f->request->pabe)
		))->get("lista");
		$f->response->json($model->items);
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/cpat")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/cpat')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get_pabe(){
		global $f;
		$pabe = $f->model('cm/pabe')->params(array('fields'=>array(
			'nomb'=>true,
			'num'=>true,
			'pisos'=>true
		)))->get('all')->items;
		$f->response->json(array(
			'pabe'=>$pabe,
			'peri'=>null
		));
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/cpat")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['pabellon']['_id'])) $data['pabellon']['_id'] = new MongoId($data['pabellon']['_id']);
		if(isset($data['espacio']['_id'])) $data['espacio']['_id'] = new MongoId($data['espacio']['_id']);
		if(isset($data['cliente']['_id'])) $data['cliente']['_id'] = new MongoId($data['cliente']['_id']);
		if(isset($data['fecha'])) $data['fecha'] = new MongoDate(strtotime($data['fecha']));
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			$data['estado'] = 'A';
			$data['autor'] = $f->session->userDB;
			$f->model("ct/cpat")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/cpat")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/cpat.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ct/cpat')->params(array("_id"=>$f->request->id))->delete('tipo');
    	$f->response->print( "true" );
	}
}
?>