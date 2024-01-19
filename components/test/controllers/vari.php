<?php
class Controller_mg_vari extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Variable</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"C&oacute;digo","w"=>150 ),
			array( "nomb"=>"Nombre","w"=>350 ),
			array( "nomb"=>"Valor","w"=>150 ),
			array( "nomb"=>"Modificado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("mg/vari")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/vari")->params(array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('mg/vari')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/vari")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		if(!isset($f->request->data['_id'])){
			$f->model("mg/vari")->params(array('data'=>$data))->save("insert");
		}else{
			$vari = $f->model("mg/vari")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model("mg/vari")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				'$set'=>array('fecmod'=>$data['fecmod'],'valor'=>$data['valor'],'nomb'=>$data['nomb']),
				'$push'=>array('historico'=>array('valor'=>$vari['valor'],'fecreg'=>$vari['fecmod']))
			)))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/vari.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("mg/vari.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("mg/vari.details");
	}
}
?>