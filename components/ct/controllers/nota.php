<?php
class Controller_ct_nota extends Controller {
	function execute_index_lit() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");		
		$f->response->print('<button name="btnAgregar">Agregar Nota</button>');
		$f->response->print("</div>");
		$f->response->print('
		<table>
			<tbody>
				<tr>
				<td>Periodo: <input type="text" size="6" name="periodo"></td>
				<td>Mes: <select name="mes">
					<option value="1">Enero</option>
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
					<option value="12">Diciembre</option>
				</select></td>
				</tr>
			</tbody>
		</table>
		');
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"N&uacute;mero","w"=>50 ),
			2=>array( "nomb"=>"Nombre","w"=>200 ),
			3=>array( "nomb"=>"Descripci&oacute;n","w"=>450)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index_num() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");		
		$f->response->print('<button name="btnAgregar">Agregar Nota Num&eacute;rica</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>30 ),
			array( "nomb"=>"Periodo","w"=>150 ),
			array( "nomb"=>"Nota Literal","w"=>250 ),
			array( "nomb"=>"Nombre","w"=>250),
			array( "nomb"=>"Registrado por","w"=>250),
			array( "nomb"=>"Registrado","w"=>150)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/nota")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("lista");
		$f->response->json( $model );
	}
	function execute_lista_num(){
		global $f;
		$model = $f->model("ct/nota")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_num");
		$f->response->json( $model );
	}
	function execute_search_num(){
		global $f;
		$model = $f->model("ct/nota")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search_num");
		$f->response->json( $model );
	}
	function execute_search_lit(){
		global $f;
		$model = $f->model("ct/nota")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto,"mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("search_lit");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/nota')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/nota")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			if($model->items["tipo"]=="A"){
				foreach($model->items["activos"] as $i=>$item){
					$clas = $f->model("ct/pcon")->params(array("_id"=>$item["cuenta"]["_id"]))->get("one")->items;
					$model->items["activos"][$i]["cuenta"] = $clas;
				}
			}elseif($model->items["tipo"]=="O"){
				foreach($model->items["otros"] as $i=>$item){
					$clas = $f->model("ct/pcon")->params(array("_id"=>$item["cuenta"]["_id"]))->get("one")->items;
					$model->items["otros"][$i]["cuenta"] = $clas;
				}
			}
		
		}
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"]= new MongoDate();
			$data['autor'] = $f->session->userDB;
			$f->model("ct/nota")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_num(){
		global $f;
		$data = $f->request->data;
		$data['fec_numerica'] = new MongoDate();
		$data['autor_numerica'] = $f->session->userDB;
		if($data['tipo']=='A'){
			foreach ($data['activos'] as $i=>$item){
				$data['activos'][$i]['cuenta']['_id'] = new MongoId($item['cuenta']['_id']);
				$data['activos'][$i]['valor_bruto'] = floatval($item['valor_bruto']);
				$data['activos'][$i]['depreciacion'] = floatval($item['depreciacion']);
				$data['activos'][$i]['valor_neto'] = floatval($item['valor_neto']);
				if(strlen($item["cuenta"]["cod"])==4)
					$data['activos'][$i]['ultimo'] = false;
				else
					$data['activos'][$i]['ultimo'] = true;
			}
		}else{
			foreach ($data['otros'] as $i=>$item){
				$data['otros'][$i]['cuenta']['_id'] = new MongoId($item['cuenta']['_id']);
				$data['otros'][$i]['monto'] = floatval($item['monto']);
				if(strlen($item["cuenta"]["cod"])==4)
					$data['otros'][$i]['ultimo'] = false;
				else
					$data['otros'][$i]['ultimo'] = true;
			}
		}
		$f->model("ct/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/nota.edit");
	}
	function execute_lit_edit(){
		global $f;
		$f->response->view("ct/nota.lit.edit");
	}
	function execute_num_edit(){
		global $f;
		$f->response->view("ct/nota.num.edit");
	}
	function execute_select_lit(){
		global $f;
		$f->response->view("ct/nota.lit.select");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ct/nota')->params(array("_id"=>$f->request->id))->delete('tipo');
    	$f->response->print( "true" );
	}
}
?>