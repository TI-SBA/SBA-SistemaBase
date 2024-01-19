<?php
class Controller_ct_cban extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('&nbsp;Periodo: <select name="mes"><option value="1">Enero</option>
		<option value="2">Febrero</option>
		<option value="3">Marzo</option>
		<option value="4">Abril</option>
		<option value="5">Mayo</option>
		<option value="6">Junio</option>
		<option value="7">Julio</option>
		<option value="8">Agosto</option>
		<option value="9">Setiembre</option>
		<option value="10">Octubre</option>
		<option value="11">Noviembre</option>
		<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6">');
		$f->response->print('<button name="btnAgregar">Nueva Conciliaci&oacute;n</button>');
		$f->response->print("</div>");
		$f->response->view("ct/cban");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/cban")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/cban")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/cban')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/cban")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_fin(){
		global $f;
		$data = $f->request->data;
		if(isset($f->request->data['_id'])){
			$f->model("ct/cban")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			$data["activo"]["autor"]["_id"]=new MongoId($data["activo"]["autor"]["_id"]);
			$data["activo"]["autor"]["cargo"]["_id"]=new MongoId($data["activo"]["autor"]["cargo"]["_id"]);
			$data["activo"]["autor"]["cargo"]["organizacion"]["_id"]=new MongoId($data["activo"]["autor"]["cargo"]["organizacion"]["_id"]);
			$data["activo"]["fec"] = $data["fecreg"];
			$data["cuenta_banco"]["_id"] = new MongoId($data["cuenta_banco"]["_id"]);
			$data["cuenta_banco"]["banco"]["_id"] = new MongoId($data["cuenta_banco"]["banco"]["_id"]);
			if(isset($data["cheques"])){
				for($i=0;$i<count($data["cheques"]);$i++){
					$data["cheques"][$i]["detalle"]["_id"]=new MongoId($data["cheques"][$i]["detalle"]["_id"]['$id']);
					$data["cheques"][$i]["fecha"]=new MongoDate(strtotime(str_replace("/", "-", $data["cheques"][$i]["fecha"])));
				}
			}
			if(isset($data["depositos"])){
				for($i=0;$i<count($data["depositos"]);$i++){
					$data["depositos"][$i]["fecha"]=new MongoDate(strtotime($data["depositos"][$i]["fecha"]));
				}
			}
			if(isset($data["gastos"])){
				for($i=0;$i<count($data["gastos"]);$i++){
					$data["gastos"][$i]["fecha"]=new MongoDate(strtotime($data["gastos"][$i]["fecha"]));
				}
			}
			$data["total_cheques"]=floatval($data["total_cheques"]);
			$data["total_depositos"]=floatval($data["total_depositos"]);
			$data["total_gastos"]=floatval($data["total_gastos"]);
			$data["total_rectificaciones"]=floatval($data["total_rectificaciones"]);
			$data["saldo_libro_bancos"]=floatval($data["saldo_libro_bancos"]);
			$data["saldo_bancos"]=floatval($data["saldo_bancos"]);
			$data["saldo_extracto"]=floatval($data["saldo_extracto"]);
			$data["diferencia"]=floatval($data["diferencia"]);
			$f->model("ct/cban")->params(array('data'=>$data))->save("insert");
		}else{
			$data["activo"]["fec"] = new MongoId();
			$data["activo"]["autor"]["_id"]=new MongoId($data["activo"]["autor"]["_id"]);
			$data["activo"]["autor"]["cargo"]["_id"]=new MongoId($data["activo"]["autor"]["cargo"]["_id"]);
			$data["activo"]["autor"]["cargo"]["organizacion"]["_id"]=new MongoId($data["activo"]["autor"]["cargo"]["organizacion"]["_id"]);
			if(isset($data["cheques"])){
				for($i=0;$i<count($data["cheques"]);$i++){
					$data["cheques"][$i]["detalle"]["_id"]=new MongoId($data["cheques"][$i]["detalle"]["_id"]['$id']);
					$data["cheques"][$i]["fecha"]=new MongoDate(strtotime(str_replace("/", "-", $data["cheques"][$i]["fecha"])));
				}
			}
			if(isset($data["depositos"])){
				for($i=0;$i<count($data["depositos"]);$i++){
					$data["depositos"][$i]["fecha"]=new MongoDate(strtotime($data["depositos"][$i]["fecha"]));
				}
			}
			if(isset($data["gastos"])){
				for($i=0;$i<count($data["gastos"]);$i++){
					$data["gastos"][$i]["fecha"]=new MongoDate(strtotime($data["gastos"][$i]["fecha"]));
				}
			}
			$data["total_cheques"]=floatval($data["total_cheques"]);
			$data["total_depositos"]=floatval($data["total_depositos"]);
			$data["total_gastos"]=floatval($data["total_gastos"]);
			$data["total_rectificaciones"]=floatval($data["total_rectificaciones"]);
			$data["saldo_libro_bancos"]=floatval($data["saldo_libro_bancos"]);
			$data["saldo_bancos"]=floatval($data["saldo_bancos"]);
			$data["saldo_extracto"]=floatval($data["saldo_extracto"]);
			$data["diferencia"]=floatval($data["diferencia"]);
			$f->model("ct/cban")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/cban.edit");
	}
}
?>