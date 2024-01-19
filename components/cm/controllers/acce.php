<?php
class Controller_cm_acce extends Controller {

	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Agregar</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>500 ),
			array( "nomb"=>"Precio","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("cm/acce")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_edit(){
		global $f;
		$f->response->view("cm/acce.edit");
	}
	function execute_save(){
    	global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['estado'] = 'H';
			$model = $f->model("cm/acce")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'CM',
				'bandeja'=>'Accesorios',
				'descr'=>'Se cre&oacute; el accesorio <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("cm/acce")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'CM',
					'bandeja'=>'Accesorios',
					'descr'=>'Se '.$word.' el accesorio <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'CM',
					'bandeja'=>'Accesorios',
					'descr'=>'Se actualiz&oacute; el accesorio <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$model = $f->model("cm/acce")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
    	$f->response->json( $model->obj );
	}
	function execute_update(){
    	global $f;
    	$model = $f->model('cm/acce')->save('datos');
    	$f->response->print( "true" );
	}
	function execute_delete(){
    	global $f;
    	$model = $f->model('cm/acce')->delete('datos');
    	$f->response->print( "true" );
	}
	function execute_search(){
		global $f;
		$model = $f->model("cm/acce")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json($model);
	}
	function execute_all(){
		global $f;
		$model = $f->model('cm/acce')->get('all');
		$f->response->json($model->items);
	}
	function execute_view_search(){
		global $f;
		$f->response->view("cm/acce.search");
	}
}
?>