<?php
class Controller_mg_tdoc extends Controller {

	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Agregar</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>50 ),
			1=>array( "nomb"=>"Nombre","w"=>500 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("mg/tdoc")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/tdoc.edit");
	}
	function execute_save(){
    	global $f;
    	$model = $f->model('mg/tdoc')->save('datos');
    	$f->response->print( "true" );
	}
	function execute_update(){
    	global $f;
    	$model = $f->model('mg/tdoc')->save('datos');
    	$f->response->print( "true" );
	}
	function execute_delete(){
    	global $f;
    	$model = $f->model('mg/tdoc')->delete('datos');
    	$f->response->print( "true" );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/tdoc")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json($model);
	}
	function execute_all(){
		global $f;
		$model = $f->model('mg/tdoc')->get('all');
		$f->response->json($model->items);
	}
}
?>