<?php
class Controller_pr_meta extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Meta</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"C&oacute;digo","w"=>60 ),
			3=>array( "nomb"=>"Nombre","w"=>300 ),
			4=>array( "nomb"=>"Actividad","w"=>300 ),
			5=>array( "nomb"=>"Cantidad","w"=>70 ),
			6=>array( "nomb"=>"Ubigeo","w"=>100 ),
			7=>array( "nomb"=>"Distrito","w"=>100 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/meta")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/meta")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('pr/meta')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/meta")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["actividad"]["_id"] = new MongoId($data["actividad"]["_id"]);
			$data["subprograma"]["_id"] = new MongoId($data["subprograma"]["_id"]);
			$data["proyecto"]["_id"] = new MongoId($data["proyecto"]["_id"]);
			$f->model("pr/meta")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Metas',
				'descr'=>'Se Cre&oacute; La Meta <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			if(!isset($data["estado"])){
				$data["actividad"]["_id"] = new MongoId($data["actividad"]["_id"]);
				$data["subprograma"]["_id"] = new MongoId($data["subprograma"]["_id"]);
				$data["proyecto"]["_id"] = new MongoId($data["proyecto"]["_id"]);
			}	
			$f->model("pr/meta")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Metas',
				'descr'=>'Se Modific&oacute; La Meta <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pr/meta.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('pr/meta')->params(array("_id"=>$f->request->id))->delete('meta');
    	$f->response->print( "true" );
	}
	function execute_select(){
		global $f;
		$f->response->view("pr/meta.select");
	}
}
?>