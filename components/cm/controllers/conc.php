<?php
class Controller_cm_conc extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>50 ),
			2=>array( "nomb"=>"N&uacute;mero","w"=>150 ),
			3=>array( "nomb"=>"Emitido","w"=>150 ),
			4=>array( "nomb"=>"Cuenta","w"=>200 ),
			5=>array( "nomb"=>"Local / Caja","w"=>200 ),
			6=>array( "nomb"=>"Registrado por","w"=>200 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista_all(){
		global $f;
		/*$model = $f->model("cm/comp")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_all");
		$f->response->json( $model );*/
		$json['items'] = array(
			0=>array("_id"=>new MongoId("1"),"serie"=>"001","num"=>2451,"entidad"=>array("_id"=>new MongoId("4f748a999c76843008000008"),"tipo_enti"=>"P","nomb"=>"Duzcelly","appat"=>"Náquira","apmat"=>""),"servicios"=>array(0=>array("_id"=>1,"descr"=>"Concesión"),1=>array("_id"=>2,"descr"=>"Asignación"),2=>array("_id"=>3,"descr"=>"Inhumación"))),
			1=>array("_id"=>new MongoId("2"),"serie"=>"003","num"=>352,"entidad"=>array("_id"=>new MongoId("4f748a999c76843008000008"),"tipo_enti"=>"P","nomb"=>"Duzcelly","appat"=>"Náquira","apmat"=>""),"servicios"=>array(0=>array("_id"=>1,"descr"=>"Concesión"))),
		);
		$json['paging'] = array(
			"page"=>"1",
			"items_page"=>"20",
			"total_items"=>2,
			"total_pages"=>1,
			"total_page_items"=>2
		);
		$f->response->json( $json );
	}
	function execute_lista_pen(){
		global $f;
		/*$model = $f->model("cm/comp")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_pen");
		$f->response->json( $model );*/
		$json['items'] = array(
			0=>array("_id"=>new MongoId("1"),"serie"=>"001","num"=>2451,"estado"=>"P","entidad"=>array("_id"=>new MongoId("4f748a999c76843008000008"),"tipo_enti"=>"P","nomb"=>"Duzcelly","appat"=>"Náquira","apmat"=>""),"servicios"=>array(0=>array("_id"=>1,"descr"=>"Concesión"),1=>array("_id"=>2,"descr"=>"Asignación"),2=>array("_id"=>3,"descr"=>"Inhumación"))),
			1=>array("_id"=>new MongoId("2"),"serie"=>"003","num"=>352,"estado"=>"P","entidad"=>array("_id"=>new MongoId("4f748a999c76843008000008"),"tipo_enti"=>"P","nomb"=>"Duzcelly","appat"=>"Náquira","apmat"=>""),"servicios"=>array(0=>array("_id"=>1,"descr"=>"Concesión"))),
		);
		$json['paging'] = array(
			"page"=>"1",
			"items_page"=>"20",
			"total_items"=>2,
			"total_pages"=>1,
			"total_page_items"=>2
		);
		$f->response->json( $json );
	}
	function execute_search(){
		global $f;
		$model = $f->model("cm/comp")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		/*$model = $f->model("cm/comp")->params(array("_id"=>$f->request->_id))->get("one");
		$f->response->json( $model->items );*/
		$json = array("_id"=>new MongoId("1"),"serie"=>"001","num"=>2451,"estado"=>"P","entidad"=>array("_id"=>new MongoId("4f748a999c76843008000008"),"tipo_enti"=>"P","nomb"=>"Duzcelly","appat"=>"Náquira","apmat"=>""),"servicios"=>array(0=>array("_id"=>1,"descr"=>"Concesión"),1=>array("_id"=>2,"descr"=>"Asignación"),2=>array("_id"=>3,"descr"=>"Inhumación")));
		$f->response->json( $json );
	}
	function execute_details(){
		global $f;
		$f->response->view("cm/comp.details");
	}
	function execute_new(){
		global $f;
		$f->response->view("cm/conc.new");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/enti.edit");
	}
	function execute_view_search(){
		global $f;
		$f->response->view("mg/enti.search");
	}
	function execute_conver(){
		global $f;
		$f->response->view("cm/conver.edit");
	}
	function execute_save(){
		global $f;
		$model = $f->model('cm/comp')->save('datos');
		$f->response->json( $model->obj );
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('cm/comp')->delete('datos');
    	$f->response->print( "true" );
	}
}
?>