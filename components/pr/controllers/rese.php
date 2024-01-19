<?php
class Controller_pr_rese extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		//$f->response->print('<button name="btnAgregar">Nueva Reserva</button>');
		$f->response->print('
		<table>
		<tr>
		<td><label>Periodo</label></td>
			<td><input type="text" name="periodo" style="width: 50px"></td>
			<td><label>Mes</label></td>
			<td><select name="mes">
				<option value="0">Todos</option>
				<option value="1" selected>Enero</option>
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
			<td><label name="organomb"></label></td>
		</tr>
		</table>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"Periodo","w"=>120 ),
			2=>array( "nomb"=>"Descripci&oacute;n","w"=>300 ),
			3=>array( "nomb"=>"Organizaci&oacute;n","w"=>300),
			4=>array( "nomb"=>"Clasificador","w"=>120),
			5=>array( "nomb"=>"Fuente","w"=>120),
			6=>array( "nomb"=>"Monto","w"=>80)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/rese")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"ano"=>$f->request->ano,"mes"=>$f->request->mes))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/rese")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('pr/rese')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/rese")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_valida_saldo(){
		global $f;
		//$model = $f->model();
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$f->model("pr/rese")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("pr/rese")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pr/rese.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('pr/rese')->params(array("_id"=>new MongoId($f->request->_id)))->delete('datos');
    	$f->response->print( "true" );
	}
}
?>