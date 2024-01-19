<?php
class Controller_cj_caje extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print("<button name='btnAgregar'>Nuevo Cajero</button>");
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
	function execute_get(){
		global $f;
		$enti = $f->model("mg/entidad")->params(array("_id"=>$f->request->data['_id']))->get("one");
		//$arren = $f->model("in/oper")->params(array("cliente"=>new MongoId($f->request->data['_id'])))->get("arrenprop");
		//$f->response->json( array('enti'=>$enti->items,'arre'=>$arren->items) );
		$f->response->json( array('enti'=>$enti->items) );
	}
	function execute_lista(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("roles"=>"cajero","page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"rol"=>array('nomb'=>'roles.cajero','value'=>array('$exists'=>true))
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$cajas = $f->request->data['cajas'];
		foreach ($cajas as $i=>$caja){
			$cajas[$i] = new MongoId($caja);
		}
		$f->model("mg/entidad")->params(array(
			"_id"=>new MongoId($f->request->data['_id']),
			"data"=>array('roles.cajero'=>array(
				'cajas'=>$cajas
			))
		))->save("update");
		$f->response->print('true');
	}
	function execute_cajas_trabajador(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"_id"=>new MongoId($f->request->data['_id'])
		))->get("one")->items;
		$rpta = null;
		if($model!=null){
			if(isset($model['roles']['cajero']['cajas'])){
				$rpta = array();
				foreach ($model['roles']['cajero']['cajas'] as $i => $id_caja) {
					$caja = $f->model('cj/caja')->params(array('_id'=>$id_caja))->get('one')->items;
					if($caja!=null) $rpta[] = $caja;
				}
			}
		}
		$f->response->json($rpta);
	}
	function execute_remove(){
		global $f;
		$f->model("mg/entidad")->params(array(
			"_id"=>new MongoId($f->request->data['_id']),
			"data"=>array('roles.cajero'=>true)
		))->delete("data");
		$f->response->print('true');
	}
	function execute_details(){
		global $f;
		$f->response->view("cj/ardario.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("cj/caje.edit");
	}
}
?>