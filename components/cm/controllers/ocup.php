<?php
class Controller_cm_ocup extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Ocupante</button><button name="btnRegiOcup">Registrar Ocupante Anterior</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>50 ),
			1=>array( "nomb"=>"Nombre","w"=>390 ),
			2=>array( "nomb"=>"Doc. de Identidad","w"=>130 ),
			3=>array( "nomb"=>"Ubicaci&oacute;n","w"=>420 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array(
			"rol"=>array('nomb'=>'roles.ocupante','value'=>array('$exists'=>true)),
			"texto"=>$f->request->data['texto'],
			"page"=>$f->request->page,
			"page_rows"=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['texto']))
			$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("mg/entidad")->params($params)->get("search");
		if(isset($model->items)){
			foreach ($model->items as $key => $item) {
				if(isset($model->items[$key]['roles']))
					if(isset($model->items[$key]['roles']['ocupante']))
						if(isset($model->items[$key]['roles']['ocupante']['propietario']))
							$model->items[$key]['roles']['ocupante']['propietario'] = $f->model("mg/entidad")->params(array("_id"=>$item['roles']['ocupante']['propietario']['_id']))->get("one")->items;
			}
		}
		//$model = $f->model("mg/entidad")->params(array("roles"=>"ocupante","page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"rol"=>array('nomb'=>'roles.ocupante','value'=>array('$exists'=>true))
		))->get("search");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$entidad = $f->model("mg/entidad")->params(array("_id"=>$f->request->_id))->get("one");
		if(isset($entidad->items['roles']))
			if(isset($entidad->items['roles']['ocupante']))
				if(isset($entidad->items['roles']['ocupante']['propietario']))
					$entidad->items['roles']['ocupante']['propietario'] = $f->model("mg/entidad")->params(array("_id"=>$entidad->items['roles']['ocupante']['propietario']['_id']))->get("one")->items;
		$opers = $f->model("cm/oper")->params(array("_id"=>$f->request->_id))->get("ocup")->items;
		if($opers!=null){
			foreach ($opers as $key => $item) {
				if(isset($item['recibos'])){
					foreach ($item['recibos'] as $k => $rec) {
						$opers[$key]['recibos'][$k] = $f->model("cj/comp")->params(array("_id"=>$rec['_id']))->get("one")->items;
					}
				}
			}
		}
		$f->response->json( array('entidad'=>$entidad->items,'opers'=>$opers) );
	}
	function execute_details(){
		global $f;
		$f->response->view("cm/ocup.details");
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
	function execute_expd(){
		global $f;
		$f->response->print("<div name='mainGrid'>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			2=>array( "nomb"=>"N&uacutemero","w"=>100 ),
			3=>array( "nomb"=>"Ubicaci&oacute;n","w"=>210 ),
			4=>array( "nomb"=>"Asunto","w"=>210 ),
			5=>array( "nomb"=>"Registrado","w"=>110 ),
			6=>array( "nomb"=>"Vencimiento","w"=>110 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div></div>");
	}
	function execute_all_ocu_pro(){
		global $f;
		$model = $f->model('cm/ocup')->params(array('_id'=>$f->request->_id))->get('all_ocu_pro');
		$f->response->json($model->items);
	}
}
?>