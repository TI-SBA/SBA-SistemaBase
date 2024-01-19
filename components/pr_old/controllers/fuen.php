<?php
class Controller_pr_fuen extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Fuente</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"C&oacute;digo","w"=>60 ),
			3=>array( "nomb"=>"Rubro","w"=>250 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/fuen")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/fuen")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('pr/fuen')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_savefuen(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$f->model("pr/fuen")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Fuentes de Financiamiento',
				'descr'=>'Se Cre&oacute; la fuente <b>'.$data["rubro"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/fuen")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$fuen = $f->model("pr/fuen")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Fuentes de Financiamiento',
					'descr'=>'Se '.$array[$data["estado"]].' La Fuente <b>'.$fuen["rubro"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Fuentes de Financiamiento',
					'descr'=>'Se Modific&oacute; La Fuente <b>'.$fuen["rubro"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_editfuen(){
		global $f;
		$f->response->view("pr/fuen.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('pr/fuen')->params(array("_id"=>$f->request->id))->delete('fuen');
    	$f->response->print( "true" );
	}
}
?>