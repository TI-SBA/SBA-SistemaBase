<?php
class Controller_ct_pcon extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('&nbsp;Tipo: <select name="filTipo">
		<option value="A">Activo</option>
		<option value="P">Pasivo</option>
		<option value="PT">Patrimonio</option>
		<option value="I">Ingresos</option>
		<option value="G">Gastos</option>
		<option value="R">Resultados</option>
		<option value="PR">Presupuestos</option>
		<option value="O">Orden</option>
		</select>&nbsp;&nbsp;');
		$f->response->print('<button name="btnAgregar">Nueva Cuenta</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"Cuenta Contable","w"=>200 ),
			3=>array( "nomb"=>"Descripci&oacute;n","w"=>300 ),
			4=>array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array(
			"id"=>$f->request->id
		);
		if(isset($f->request->data["tipo"])){
			$params['tipo']= $f->request->tipo;
		}
		if(isset($f->request->data["texto"])){
			$params['texto'] = $f->request->texto;
		}
		$model = $f->model("ct/pcon")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_lista2(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		$model = $f->model("ct/pcon")->params($params)->get("lista2");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['logistica']))
			$params['logistica'] = true;
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ct/pcon")->params($params)->get("search") );
	}
	function execute_all(){
		global $f;
		$params = array();
		if(isset($f->request->data['fields']))
			$params['fields'] = $f->request->data['fields'];
		$model = $f->model('ct/pcon')->params($params)->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/pcon")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if(isset($f->request->data["datah"])){
			if(isset($model->items["cuentas"]["hijos"])){
				foreach($model->items["cuentas"]["hijos"] as $i=>$hijo){			
					$model->items["cuentas"]["hijos"][$i] = $f->model("ct/pcon")->params(array("_id"=>$hijo["id"]))->get("one")->items;
				}
			}
		}
		$f->response->json( $model->items );
	}
	function execute_validar(){
		global $f;
		$model = $f->model("ct/pcon")->params(array("cod"=>$f->request->cod))->get("code");
		if($model->items!=null){
			$f->response->json("true");
		}else{
			$f->response->json("false");
		}
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		//$data['partida']['_id'] = new MongoId($data['partida']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			if($data['cuentas']['padre']==null){
				$data['cod']=(int)$data['cod'];
			}else{
				$data['cod']=$data['cod'];
			}
			$f->model("ct/pcon")->params(array('data'=>$data))->save("insert");
			$clas['id'] = $data['_id'];
			$parent = $data['cuentas']['padre'];
			$f->model("ct/pcon")->params(array('_id'=>new MongoId($parent),'data'=>$clas))->save("push");
		}else{
			$f->model("ct/pcon")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_order(){
		global $f;
		$data = $f->request->data;
		if(isset($data["cuentas"])){
			$edit = array();
			foreach($data["cuentas"] as $i=>$cuen){
				$edit["cuentas.hijos"][$i]["id"] = new MongoId($cuen);
			}
			$f->model("ct/pcon")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$edit))->save("update");
		}
		
		if(isset($data["eliminados"])){
			foreach($data["eliminados"] as $eli){
				$f->model("ct/pcon")->params(array('_id'=>$eli))->delete("clas");
			}
		}
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/cuen.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ct/pcon.details");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/pcon.select");
	}
	function execute_order(){
		global $f;
		$f->response->view("ct/cuen.order");
	}
}
?>