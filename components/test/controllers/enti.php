<?php
class Controller_mg_enti extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Agregar</button>');
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
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		);
		if(isset($f->request->data['filter'])){
			$params['filter'] = $f->request->data['filter'];
			foreach ($params['filter'] as $i=>$filter){
				if(gettype($filter['value'])=="array"){
					if(isset($filter['value']['$exists'])){
						if($filter['value']['$exists']=='true') $filter['value'] = array('$exists'=>1);
						else $filter['value'] = array('$exists'=>0);
					}
				}
				$params['filter'][$i]['value'] = $filter['value'];
			}
		}
		$model = $f->model("mg/entidad")->params($params)->get("search");
		$f->response->json( $model );
	}
	function execute_search_tra(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search_tra");
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
	function execute_all_ocu_pro(){
		global $f;
		$model = $f->model('mg/entidad')->get('all_ocu_pro');
		$f->response->json($model->items);
	}
	function execute_all_difu_pro(){
		global $f;
		$model = $f->model('mg/entidad')->params(array('_id'=>$f->request->data['_id']))->get('all_difu_pro');
		$f->response->json($model->items);
	}
	function execute_all_difu_espa(){
		global $f;
		$model = $f->model('mg/entidad')->get('all_difu_espa');
		$f->response->json($model->items);
	}
	function execute_search_empresas(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search_empresas");
		$f->response->json( $model );
	}
}
?>