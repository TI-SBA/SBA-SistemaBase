<?php
class Controller_ts_ctban extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Cuenta</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>30 ),
			array( "nomb"=>"Nombre","w"=>250 ),
			array( "nomb"=>"N&uacute;mero","w"=>150 ),
			array( "nomb"=>"Moneda","w"=>150 ),
			array( "nomb"=>"Fecha de Registro","w"=>150)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ts/ctban")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ts/ctban")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ts/ctban')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_all_nr(){
		global $f;
		$fields = array();
		$model = $f->model('ts/ctban')->params(array('fields'=>$fields))->get('all');
		$cuens = array();
		if($model->items!=null){
			for($i=0;$i<count($model->items);$i++){
				$cursor = $f->model('ts/saldlibr')->params(array('_id'=>$model->items[$i]["_id"],'tipo'=>$f->request->tipo))->get('sald');
				if($cursor->items==null){
					array_push($cuens, $model->items[$i]);
				}
			}
		}
		$f->response->json($cuens);
	}
	function execute_all_cban(){
		global $f;
		$model = $f->model('ts/ctban')->params(array('fields'=>$fields))->get('all');
		$cuens = array();
		if($model->items!=null){
			for($i=0;$i<count($model->items);$i++){
				$cursor = $f->model('ct/cban')->params(array('_id'=>$model->items[$i]["_id"],'mes'=>$f->request->mes,'ano'=>$f->request->ano))->get('conc');
				if($cursor->items==null){
					array_push($cuens, $model->items[$i]);
				}
			}
		}
		$f->response->json($cuens);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/ctban")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"]=new MongoDate();
			$data["banco"]["_id"]=new MongoId($data["banco"]["_id"]);
			$data["cuenta"]["_id"]=new MongoId($data["cuenta"]["_id"]);
			$f->model("ts/ctban")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cuentas Bancarias',
				'descr'=>'Se Cre&oacute; La Cuenta Bancaria <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$data["cuenta"]["_id"]=new MongoId($data["cuenta"]["_id"]);
			$data["banco"]["_id"]=new MongoId($data["banco"]["_id"]);
			$f->model("ts/ctban")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cuentas Bancarias',
				'descr'=>'Se Modific&oacute; La Cuenta Bancaria <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/ctban.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/ctban.details");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ts/ctban')->params(array("_id"=>$f->request->id))->delete('fuen');
    	$f->response->print( "true" );
	}
}
?>