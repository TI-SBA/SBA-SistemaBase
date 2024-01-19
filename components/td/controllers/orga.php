<?php
class Controller_td_orga extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo &Oacute;rgano Externo</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>350 ),
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
		$model = $f->model("td/orga")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("td/orga")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('td/orga')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("td/orga")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("td/orga")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TD',
				'bandeja'=>'&Oacute;rganos Externos',
				'descr'=>'Se cre&oacute; el &oacute;rganos externo <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$doc = $f->model('td/orga')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'TD',
					'bandeja'=>'&Oacute;rganos Externos',
					'descr'=>'Se '.$word.' el &oacute;rganos externo <b>'.$doc['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'TD',
					'bandeja'=>'&Oacute;rganos Externos',
					'descr'=>'Se actualiz&oacute; el &oacute;rganos externo <b>'.$doc['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("td/orga")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("td/orga.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("td/orga.select");
	}
}
?>