<?php
class Controller_mg_orga extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Agregar</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>50 ),
			1=>array( "nomb"=>"Nombre","w"=>500 ),
			2=>array( "nomb"=>"Sigla","w"=>90 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		$params['estado'] = "H";
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		if(isset($f->request->data['todos']))
			unset($params['estado']);
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['oficina'])){
			$params['oficina'] = $f->request->data['oficina'];
		}
		if(isset($f->request->data['actividad'])){
			if($f->request->data['actividad'] == true)
				$params['actividad'] = array('$exists'=> true);
		}
		if(isset($f->request->data['componente'])){
			if($f->request->data['componente'] == true)
				$params['componente'] = array('$exists'=> true);
		}
		$f->response->json( $f->model("mg/orga")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/orga.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("mg/orga.select");
	}
	function execute_save(){
    	global $f;
    	$model = $f->model('mg/orga')->save('datos');
		$f->model('ac/log')->params(array(
			'modulo'=>'MG',
			'bandeja'=>'Estructura Organizacional',
			'descr'=>'Se cre&oacute; la organizaci&oaute;n <b>'.$f->request->data['data']['nomb'].'</b>.'
		))->save('insert');
    	$f->response->json( $model->items );
	}
	/*function execute_save_legacy(){
    	global $f;
    	$model = $f->model('mg/orga')->save('datos');
		$f->model('ac/log')->params(array(
			'modulo'=>'MG',
			'bandeja'=>'Estructura Organizacional',
			'descr'=>'Se cre&oacute; la organizaci&oaute;n <b>'.$f->request->data['data']['nomb'].'</b>.'
		))->save('insert');
    	$f->response->json( $model->obj );
	}*/
	function execute_update(){
    	global $f;
    	$model = $f->model('mg/orga')->save('datos');
		$f->model('ac/log')->params(array(
			'modulo'=>'MG',
			'bandeja'=>'Estructura Organizacional',
			'descr'=>'Se actualiz&oacute; la organizaci&oacute;n <b>'.$f->request->data['data']['nomb'].'</b>.'
		))->save('insert');
    	$f->response->json( "true" );
	}
	function execute_delete(){
    	global $f;
    	$model = $f->model('mg/orga')->delete('datos');
    	$f->response->print( "true" );
	}/*
	function execute_search(){
		global $f;
		$model = $f->model("mg/orga")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json($model);
	}*/
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado']))
			$estado = $f->request->data['estado'];
		$params = array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		);
		if(isset($f->request->data['oficina']))
			$params['oficina'] = $f->request->data['oficina'];
		$model = $f->model("mg/orga")->params($params)->get("search");
		$f->response->json( $model );
	}
	function execute_lis(){
		global $f;
		$model = $f->model("mg/orga")->get("lo");
		$f->response->json($model->items);
	}
	function execute_lisnodos(){
		global $f;
		$model = $f->model("mg/orga")->get("nodos");
		$f->response->json($model->items);
	}
	function execute_all(){
		global $f;
		$model = $f->model("mg/orga")->get("all");
		$f->response->json($model->items);
	}
	function execute_listanodos(){
		global $f;
		$model = $f->model("mg/orga")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listanodos");
		$f->response->json($model);
	}
	function execute_ordenar(){
		global $f;
		$model = $f->model("mg/orga")->get("ordenar");
		$f->response->json($model);
	}
}
?>