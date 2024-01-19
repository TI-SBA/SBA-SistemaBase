<?php
class Controller_ct_epres extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
		
	}
	function execute_index_g(){
		global $f;
		$f->response->view("ci/ci.search");
		$f->response->print('
		<table>
			<tr>			
				<td><label>Periodo</label></td>
				<td><select name="mes"><option value="1">Enero</option>
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
				<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6"></td>
				<td>Organizaci&oacute;n</td>
				<td name="FilOrga">
					<input type="hidden" name="organizacion" value="">
					<span name="organomb"></span>
					<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
					<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
				</td>
			</tr>
			<tr>
				<td><label>Cuenta de Mayor</label></td>
				<td name="FilClas">
					<input type="hidden" name="clasificador" value="">
					<span name="clasnomb"></span>
					<input type="radio" name="rbtnClas" id="rbtnClasSelect" value="C"><label for="rbtnClasSelect">Seleccionar</label>
					<input type="radio" name="rbtnClas" id="rbtnClasX" value="X" checked="checked"><label for="rbtnClasX">X</label>
				</td>
			</tr>
			<tr>
				<td><label>Descripci&oacute;n</label></td>
				<td><span name="clas1"></span></td>
			</tr>
		</table>
		');
		$f->response->view("ct/epresg");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");	
	}
	function execute_index_crc(){
		global $f;
		$f->response->view("ci/ci.search");
		$f->response->print('
		<table>
			<tr>			
				<td><label>Periodo</label></td>
				<td><select name="mes"><option value="1">Enero</option>
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
				<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6"></td>
				<td>Organizaci&oacute;n</td>
				<td name="FilOrga">
					<input type="hidden" name="organizacion" value="">
					<span name="organomb"></span>
					<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
					<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
				</td>
			</tr>
		</table>
		');
		$f->response->view("ct/epres.crc");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");	
	}
	function execute_index_cre(){
		global $f;
		$f->response->view("ci/ci.search");
		$f->response->print('
		<table>
			<tr>			
				<td><label>Periodo</label></td>
				<td><select name="mes"><option value="1">Enero</option>
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
				<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6"></td>
				<td>Organizaci&oacute;n</td>
				<td name="FilOrga">
					<input type="hidden" name="organizacion" value="">
					<span name="organomb"></span>
					<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
					<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
				</td>
			</tr>
		</table>
		');
		$f->response->view("ct/epres.crc");
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");	
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/epres")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/epres")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/epres')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/epres")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			$f->model("ct/epres")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/epres")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/epres.edit");
	}
}
?>