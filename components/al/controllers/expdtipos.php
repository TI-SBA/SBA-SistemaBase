<?php
class Controller_al_expdtipos extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Tipo</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"Nombre","w"=>250 ),
			2=>array( "nomb"=>"Observaciones","w"=>350 ),
			3=>array( "nomb"=>"Fecha de Registro","w"=>200)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("al/expdtipos")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("al/expdtipos")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('al/expdtipos')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("al/expdtipos")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']= new MongoDate();
			$f->model("al/expdtipos")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("al/expdtipos")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit_expd_tipos(){
		global $f;
		$f->response->view("al/expd.tipo.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('al/expdtipos')->params(array("_id"=>$f->request->id))->delete('tipos');
    	$f->response->print( "true" );
	}
}
?>