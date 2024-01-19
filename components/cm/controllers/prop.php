<?php
class Controller_cm_prop extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Propietario</button>');
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
		$model = $f->model("mg/entidad")->params(array("roles"=>"propietario","page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"rol"=>array('nomb'=>'roles.propietario','value'=>array('$exists'=>true))
		))->get("search");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$entidad = $f->model("mg/entidad")->params(array("_id"=>$f->request->_id))->get("one");
		$opers = $f->model("cm/oper")->params(array("_id"=>$f->request->_id))->get("prop");
		$json = array('entidad'=>$entidad->items,'opers'=>$opers->items);
		$tmp['ent_a'] = $entidad->items['roles']['propietario']['ocupantes'];
		$tmp['ent_i'] = $entidad->items['roles']['propietario']['ocupantes'];
		if(sizeof($opers->items)>0){
			foreach ($opers->items as $oper){
				if(isset($oper['asignacion']) && $oper['asignacion']!=null){
					if($entidad->items['roles']['propietario']!=true){
						foreach ($entidad->items['roles']['propietario']['ocupantes'] as $index=>$ocp){
							if($oper['ocupante']['_id']==$ocp['_id']){
								unset($tmp['ent_a'][$index]);
							}
						}
					}
				}
				if(isset($oper['inhumacion']) && $oper['inhumacion']!=null){
					if($entidad->items['roles']['propietario']!=true){
						foreach ($entidad->items['roles']['propietario']['ocupantes'] as $index=>$ocp){
							if($oper['ocupante']['_id']==$ocp['_id']){
								unset($tmp['ent_i'][$index]);
							}
						}
					}
				}
			}
		}
		if(sizeof($tmp['ent_a'])>0 && $entidad->items['roles']['propietario']!=true){
			foreach ($tmp['ent_a'] as $ent){
				$data = $f->model("cm/oper")->params(array("_id"=>$ent['_id']))->get("last_asignacion");
				if($data->items!='') $json['extras'][] = $data->items;
				$data = $f->model("cm/oper")->params(array("_id"=>$ent['_id']))->get("last_traslado");
				if($data->items!=''){
					if($json['extras'][sizeof($json['extras'])-1]['fecreg']<$data->items['fecreg'])
						$json['extras'][] = $data->items;
				}
			}
		}
		if(sizeof($tmp['ent_i'])>0 && $entidad->items['roles']['propietario']!=true){
			foreach ($tmp['ent_i'] as $ent){
				$data = $f->model("cm/oper")->params(array("_id"=>$ent['_id']))->get("last_inhumacion");
				if($data->items!='') $json['extras'][] = $data->items;
			}
		}
		$f->response->json( $json );
	}
	function execute_details(){
		global $f;
		$f->response->view("cm/prop.details");
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
	function execute_all_difu(){
		global $f;
		$model = $f->model('cm/prop')->params(array('_id'=>$f->request->data['_id']))->get('all_difu');
		$f->response->json($model->items);
	}
}
?>