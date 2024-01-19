<?php
class Controller_in_aval extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Aval</button>');
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
		$model = $f->model("mg/entidad")->params(array("roles"=>"aval","page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"rol"=>array('nomb'=>'roles.aval','value'=>array('$exists'=>true))
		))->get("search");
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$model = $f->model('mg/entidad')->save('datos');
		$f->response->json( $model->obj );
	}
/*	function execute_new(){
		global $f;
		$f->response->view( 'in/loca.new' );
	}
	function execute_save(){
		global $f;
		if(isset($f->request->data['_id'])){
			$data = $f->request->data;
			$data["fecreg"] = new MongoDate();
			if(isset($data["imagen"])) $data["imagen"] = new MongoId($data["imagen"]);
			print_r($data);die();
			$f->model("in/loca")->params(array('data'=>$data))->save("insert");
		}
	}*/
}
?>