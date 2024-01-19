<?php
class Controller_al_expd extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		/*$f->response->print('Tipo:<span name="FilTipo">
			<input type="hidden" name="tipo">
			<input type="radio" name="rbtnTipo" id="rbtnSelectTipo" value="T"><label for="rbtnSelectTipo">Todos</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectX" value="X" checked="checked"><label for="rbtnSelectX">X</label>
		</span>');*/
		$f->response->print('&nbsp;Tipo : <select name="tipo">
			<option value="0" selected="selected">Todos</option>
			<option value="C">Civiles</option>
			<option value="P">Penales</option>
			<option value="A">Administrativos</option>
			<option value="L">Laborales</option>
			<option value="T">Contesioso Administrativo</option>
			<option value="S">Sucesion Intestada</option>
		</select>');
		$f->response->print('&nbsp;Encargado: <select name="encargado">
			<option value="B" selected="selected">Beneficencia</option>
			<option value="P">Procuradoria</option>
			<option value="M">Mimdes</option>
		</select>');
		$f->response->print('<button name="btnAgregar">Nuevo Expediente</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"N&uacute;mero","w"=>80 ),
			2=>array( "nomb"=>"Demandante","w"=>230 ),
			3=>array( "nomb"=>"Demandado","w"=>230),
			4=>array( "nomb"=>"Materia","w"=>150),
			5=>array( "nomb"=>"Juzgado","w"=>150),
			6=>array( "nomb"=>"Estado","w"=>220),
			7=>array( "nomb"=>"Inmueble","w"=>80)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("al/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"archivado"=>$f->request->archivado,"tipo"=>$f->request->tipo,"encargado"=>$f->request->encargado))->get("lista");
		$f->response->json( $model );
	}
	function execute_lista_expd_hist(){
		global $f;
		$model = $f->model("al/expd")->params(array("page"=>$f->request->page,"page_rows"=>1,"numero"=>$f->request->numero))->get("lista_hist");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("al/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('al/expd')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("al/expd")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']= new MongoDate();
			$f->model("al/expd")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'AL',
				'bandeja'=>'Expedientes',
				'descr'=>'Se cre&oacute; el Expediente <b>N&deg; '.$data['numero'].'</b>.'
			))->save('insert');
		}else{
			$f->model("al/expd")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_hist(){
		global $f;
		$data = $f->request->data;
			$data['fecactualizacion']= new MongoDate();
			if(isset($data['archivado'])){
				$data['archivado']['fecini']=new MongoDate(strtotime($data['archivado']['fecini']));
				$data['archivado']['fecfin']=new MongoDate(strtotime($data['archivado']['fecfin']));
				$f->model('ac/log')->params(array(
					'modulo'=>'AL',
					'bandeja'=>'Expedientes',
					'descr'=>'Se Archiv&oacute; el Expediente <b>N&deg; '.$data['numero'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'AL',
					'bandeja'=>'Expedientes',
					'descr'=>'Se Modific&oacute; el Expediente <b>N&deg; '.$data['numero'].'</b>.'
				))->save('insert');
			}
			$f->model("al/expd")->params(array('data'=>$data))->save("insert_hist");
		$f->response->print("true");
	}
	function execute_edit_expd(){
		global $f;
		$f->response->view("al/expd.edit");
	}
	function execute_details_expd(){
		global $f;
		$f->response->view("al/expd.details");
	}
	function execute_select_expd(){
		global $f;
		$f->response->view("al/expd.acti.select");
	}
	function execute_arch_expd(){
		global $f;
		$f->response->view("al/expd.arch.edit");
	}
	function execute_edit_expd2(){
		global $f;
		$f->response->view("al/expd.edit2");
	}
	function execute_hist_expd(){
		global $f;
		$f->response->view("al/expd.hist");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('al/expd')->params(array("_id"=>$f->request->id))->delete('expd');
    	$f->response->print( "true" );
	}
	function execute_archivar(){
		global $f;
		$data = $f->request->data;
		$data['archivado']['fecini']=new MongoDate(strtotime($data['archivado']['fecini']));
		$data['archivado']['fecfin']=new MongoDate(strtotime($data['archivado']['fecfin']));
		$f->model("al/expd")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		$f->response->print( "true" );
	}
	function execute_print(){
		global $f;
		$model = $f->model("al/expd")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["historico"] = $f->model("al/expd")->params(array("numero"=>$model->items["numero"]))->get("all_hist")->items;
		$f->response->view("al/repo.expd.print", $model );
	}
}
?>