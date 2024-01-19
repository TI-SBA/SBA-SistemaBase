<?php
class Controller_pr_unid extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Unidad</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"C&oacute;digo","w"=>60 ),
			2=>array( "nomb"=>"Nombre","w"=>250 ),
			3=>array( "nomb"=>"Abreviatura","w"=>80)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/unid")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/unid")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('pr/unid')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/unid")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_saveunid(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$f->model("pr/unid")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Unidades de Medida',
				'descr'=>'Se Cre&oacute; La Unidad <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/unid")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Unidades de Medida',
				'descr'=>'Se Modifi&oacute; La Unidad <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_editunid(){
		global $f;
		$f->response->view("pr/unid.edit");
	}
	function execute_deleteunid(){
		global $f;
    	$model = $f->model('pr/unid')->params(array("_id"=>$f->request->id))->delete('unid');
		$unid = $f->model("pr/unid")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Unidades de Medida',
				'descr'=>'Se Elimin&oacute; La Unidad <b>'.$unid["nomb"].'</b>.'
			))->save('insert');
    	$f->response->print( "true" );
	}
}
?>