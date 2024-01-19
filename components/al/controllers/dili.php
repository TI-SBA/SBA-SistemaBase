<?php
class Controller_al_dili extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('Tipo:<span name="FilTipo">
			<input type="hidden" name="tipo">
			<input type="radio" name="rbtnTipo" id="rbtnSelectJ" value="J" checked="checked"><label for="rbtnSelectJ">Judiciales</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectE" value="E"><label for="rbtnSelectE">Extra Judiciales</label>
		</span>');
		$f->response->print('<button name="btnAgregar">Nueva Diligencia</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"N&deg; Expediente","w"=>100 ),
			2=>array( "nomb"=>"Asunto","w"=>250 ),
			3=>array( "nomb"=>"Fecha","w"=>120),
			4=>array( "nomb"=>"Lugar","w"=>250),
			5=>array( "nomb"=>"Observaciones","w"=>250),
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("al/dili")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo,"estado"=>$f->request->estado))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("al/dili")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('al/dili')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("al/dili")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']= new MongoDate();
			$data['fecha'] = new MongoDate(strtotime($data['fecha']));
			$data['expediente']['_id'] = new MongoId($data['expediente']['_id']);
			$data['request'] = $f->request->data;
			$f->model("al/dili")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'AL',
				'bandeja'=>'Diligencias Programadas',
				'descr'=>'Se Cre&oacute; La Diligencia del expediente <b>N&deg; '.$data["expediente"]['numero'].' con fecha programada para el '.Date::format($data["fecha"]->sec, "d/m/Y h:i").'</b>.'
			))->save('insert');
		}else{
			if(!isset($f->request->data['motivo'])){
				$data['fecha'] = new MongoDate(strtotime($data['fecha']));
				$data['expediente']['_id'] = new MongoId($data['expediente']['_id']);
				$f->model('ac/log')->params(array(
					'modulo'=>'AL',
					'bandeja'=>'Diligencias Programadas',
					'descr'=>'Se Modific&oacute; La Diligencia del expediente <b>N&deg; '.$data["expediente"]['numero'].' con fecha programada para el '.Date::format($data["fecha"]->sec, "d/m/Y h:i").'</b>.'
				))->save('insert');
			}else{
				$dili = $f->model("al/dili")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'AL',
					'bandeja'=>'Diligencias Programadas',
					'descr'=>'Se Suspendio&oacute; La Diligencia del expediente <b>N&deg; '.$dili["expediente"]['numero'].' con fecha programada para el '.Date::format($dili["fecha"]->sec, "d/m/Y h:i").'</b>.'
				))->save('insert');
			}
			$f->model("al/dili")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit_dili(){
		global $f;
		$f->response->view("al/dili.edit");
	}
	function execute_details_dili(){
		global $f;
		$f->response->view("al/dili.details");
	}
	function execute_susp_dili(){
		global $f;
		$f->response->view("al/dili.susp.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('al/dili')->params(array("_id"=>$f->request->id))->delete('dili');
    	$f->response->print( "true" );
	}
	
}
?>