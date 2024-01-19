<?php
class Controller_ts_tipo extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Tipo de Medio de Pago</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"C&oacute;digo","w"=>150 ),
			array( "nomb"=>"Denominaci&oacute;n Completa","w"=>300 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ts/tipo")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ts/tipo")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ts/tipo')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/tipo")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("ts/tipo")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Tipos de Medio de Pago',
				'descr'=>'Se Cre&oacute; el medio de pago <b>'.$data["cod"].' - '.$data["descr"].'</b>.'
			))->save('insert');
		}else{
			$f->model("ts/tipo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Tipos de Medio de Pago',
				'descr'=>'Se Actualiz&oacute; el medio de pago <b>'.$data["cod"].' - '.$data["descr"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/tipo.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/tipo.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ts/tipo.select");
	}
}
?>