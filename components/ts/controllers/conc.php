<?php
class Controller_ts_conc extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Concepto
		</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"Nombre","w"=>150 ),
			3=>array( "nomb"=>"Tipo","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ts/conc")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ts/conc")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$filter = array();
		if(isset($f->request->data['autocomplete'])){
			$regex = new MongoRegex("/^".$f->request->data['text']."/i");
			$filter['nomb'] = $regex;
		}
		$model = $f->model('ts/conc')->params(array('fields'=>$fields,'filter'=>$filter))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/conc")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecmod"]=new MongoDate();
			if(isset($data['clasificador']))$data['clasificador']['_id']=new MongoId($data['clasificador']['_id']);
			if(isset($data['cuenta']))$data['cuenta']['_id']=new MongoId($data['cuenta']['_id']);
			$data['autor'] = $f->session->userDB;
			$data['historico'] = array(
					'fecreg'=>$data["fecmod"],
					'enti'=>$data['autor'],
					'nomb'=>$data['nomb'],
					'descr'=>$data['descr']
				);
			$f->model("ts/conc")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Conceptos',
				'descr'=>'Se Cre&oacute; El Concepto <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$data["fecmod"]=new MongoDate();
			if(isset($data['clasificador']))$data['clasificador']['_id']=new MongoId($data['clasificador']['_id']);
			if(isset($data['cuenta']))$data['cuenta']['_id']=new MongoId($data['cuenta']['_id']);	
			$data['autor'] = $f->session->userDB;
			$f->model("ts/conc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model("ts/conc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				'$push'=>array('historico'=>array(
					'fecreg'=>new MongoDate(),
					'enti'=>$data['autor'],
					'nomb'=>$data['nomb'],
					'descr'=>$data['descr']
				))
			)))->save("editar");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Conceptos',
				'descr'=>'Se Modific&oacute; El Concepto <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/conc.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ts/conc.select");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ts/conc')->params(array("_id"=>$f->request->id))->delete('fuen');
    	$f->response->print( "true" );
	}
}
?>