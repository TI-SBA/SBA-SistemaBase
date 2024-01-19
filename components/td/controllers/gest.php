<?php
class Controller_td_gest extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnGestor">Nuevo Gestor</button><button name="btnExpd">Nuevo Expediente</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>50 ),
			1=>array( "nomb"=>"Nombre / Raz&oacute;n Social","w"=>390 ),
			2=>array( "nomb"=>"Doc. de Identidad","w"=>130 ),
			3=>array( "nomb"=>"Direcci&oacute;n","w"=>390 ),
			4=>array( "nomb"=>"Tel&eacute;fono","w"=>130 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_gest");
		$f->response->json( $model );
	}
	function execute_lista_trab(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("roles"=>"G","page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_gest_trab");
		$f->response->json( $model );
		/*$model = $f->model("mg/entidad")->params(array("roles"=>"gestor","page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );*/
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search_tra");
		$f->response->json( $model );
	}
	function execute_search_gest_int(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"filter"=>$f->request->texto))->get("search_gest_ext");
		$f->response->json( $model );
	}
	function execute_search_gest_ext(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"filter"=>$f->request->texto))->get("search_gest_ext");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("_id"=>$f->request->_id))->get("one");
		$f->response->json( $model->items );
	}
	function execute_details(){
		global $f;
		$f->response->view("mg/enti.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/enti.edit");
	}
	function execute_view_search(){
		global $f;
		$f->response->view("mg/enti.search");
	}
	function execute_save(){
		global $f;
		$model = $f->model('mg/entidad')->save('datos');
		$f->response->json( $model->obj );
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('mg/entidad')->delete('datos');
    	$f->response->print( "true" );
	}
	function execute_expd(){
		global $f;
		$f->response->print("<div name='mainGrid'>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			2=>array( "nomb"=>"N&uacutemero","w"=>100 ),
			3=>array( "nomb"=>"Ubicaci&oacute;n","w"=>210 ),
			4=>array( "nomb"=>"Asunto","w"=>210 ),
			5=>array( "nomb"=>"Registrado","w"=>110 ),
			6=>array( "nomb"=>"Vencimiento","w"=>110 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div></div>");
	}
}
?>