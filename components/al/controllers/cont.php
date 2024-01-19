<?php
class Controller_al_cont extends Controller {
	function execute_index_fav() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		/*$f->response->print('<span name="FilTipo">
			<input type="hidden" name="tipo">
			<input type="radio" name="rbtnTipo" id="rbtnSelectC" value="C" checked="checked"><label for="rbtnSelectC">Civiles</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectP" value="P"><label for="rbtnSelectP">Penales</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectO" value="O"><label for="rbtnSelectO">Otros</label>
		</span>');*/
		$f->response->print('&nbsp;Tipo: <select name="tipo">
			<option value="C">Civiles</option>
			<option value="P">Penales</option>
			<option value="O">Otros</option>
		</select>');
		$f->response->print('<button name="btnAgregar">Nueva Contingencia</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"N&uacute;mero","w"=>100 ),
			2=>array( "nomb"=>"Demandante","w"=>200 ),
			3=>array( "nomb"=>"Demandado","w"=>200),
			4=>array( "nomb"=>"Materia","w"=>130),
			5=>array( "nomb"=>"A Favor (S/.)","w"=>100),
			6=>array( "nomb"=>"A Favor ($)","w"=>100),
			7=>array( "nomb"=>"Estimacion de Gasto","w"=>130),
			8=>array( "nomb"=>"Fecha Probable","w"=>130)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index_cont() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		/*$f->response->print('<span name="FilTipo">
			<input type="hidden" name="tipo">
			<input type="radio" name="rbtnTipo" id="rbtnSelectC" value="C" checked="checked"><label for="rbtnSelectC">Contensioso Adm</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectL" value="L"><label for="rbtnSelectL">Laborales</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectA" value="A"><label for="rbtnSelectA">Administrativos</label>
		</span>');*/
		$f->response->print('&nbsp;Tipo: <select name="tipo">
			<option value="T">Contensioso Administrativo</option>
			<option value="L">Laborales</option>
			<option value="A">Administrativos</option>
		</select>');
		$f->response->print('<button name="btnAgregar">Nueva Contingencia</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"N&uacute;mero","w"=>100 ),
			2=>array( "nomb"=>"Demandante","w"=>200 ),
			3=>array( "nomb"=>"Demandado","w"=>200),
			4=>array( "nomb"=>"Materia","w"=>130),
			5=>array( "nomb"=>"En Contra (S/.)","w"=>100),
			6=>array( "nomb"=>"En contra ($)","w"=>100),
			7=>array( "nomb"=>"Estimacion de Gasto","w"=>130),
			8=>array( "nomb"=>"Fecha Probable","w"=>130)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("al/cont")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"clasificacion"=>$f->request->clasificacion,"tipo"=>$f->request->tipo))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("al/cont")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('al/cont')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("al/cont")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']= new MongoDate();
			//$data['demandante']['_id']=new MongoId($data['demandante']['_id']);
			//$data['demandado']['_id']=new MongoId($data['demandado']['_id']);
			$f->model("al/cont")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'AL',
				'bandeja'=>'Containgencias',
				'descr'=>'Se Cre&oacute; La Contingencia <b>N&deg; '.$data['numero'].'</b>.'
			))->save('insert');
		}else{
			//$data['demandante']['_id']=new MongoId($data['demandante']['_id']);
			//$data['demandado']['_id']=new MongoId($data['demandado']['_id']);
			$f->model("al/cont")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'AL',
				'bandeja'=>'Containgencias',
				'descr'=>'Se Modific&oacute; La Contingencia <b>N&deg; '.$data['numero'].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit_cont(){
		global $f;
		$f->response->view("al/cont.edit");
	}
	function execute_details_cont(){
		global $f;
		$f->response->view("al/cont.details");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('al/cont')->params(array("_id"=>$f->request->id))->delete('cont');
    	$f->response->print( "true" );
	}
}
?>