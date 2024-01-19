<?php
class Controller_ts_cjch extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Caja Chica</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>30 ),
			array( "nomb"=>"Caja","w"=>350 ),
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
		$model = $f->model("ts/cjch")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ts/cjch")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ts/cjch')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/cjch")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_by_orga(){
		global $f;
		$model = $f->model('ts/cjch')->params(array('orga'=>$f->session->enti['roles']['trabajador']['organizacion']['_id']))->get('by_orga');
		$f->response->json($model->items);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['local']['_id'])) $data['local']['_id'] = new MongoId($data['local']['_id']);
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['actividad']['_id'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente']['_id'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		$data['monto'] = floatval($data['monto']);
		if(!isset($f->request->data['_id'])){
			$data["fecreg"]=new MongoDate();
			$data['estado'] = 'H';
			$cjch = $f->model("ts/cjch")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cajas Chicas',
				'descr'=>'Se Cre&oacute; la Caja Chica <b>'.$data["nomb"].'</b>.'
			))->save('insert');
			$saldo = array(
				'fecreg'=>new MongoDate(),
				'estado'=>'A',
				'caja_chica'=>array(
					'_id'=>$cjch['_id'],
					'nomb'=>$cjch['nomb']
				),
				'cod'=>$cjch['cod'],
				'monto'=>floatval($cjch['monto']),
				'gasto'=>floatval(0),
				'saldo'=>floatval($cjch['monto'])
			);
			$f->model("ts/sald")->params(array('data'=>$saldo))->save("insert");
		}else{
			$f->model("ts/cjch")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cajas Chicas',
				'descr'=>'Se Actualiz&oacute; la Caja Chica <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/cjch.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/cjch.details");
	}
}
?>