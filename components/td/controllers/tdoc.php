<?php
class Controller_td_tdoc extends Controller {

	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>500 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		);
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("td/tdoc")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_edit(){
		global $f;
		$f->response->view("td/tdoc.edit");
	}
	function execute_save(){
    	global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$verify = null;
			$data['nomb'] = strtoupper(trim($data['nomb']));
			$verify = $f->model("td/tdoc")->params(array(
				'nomb'=>$data['nomb']
			))->get("by_nomb")->items;
			if($verify!=null){
				$verify['error'] = true;
				$f->response->json($verify);
			}
			if($verify==null){
				$data['estado'] = 'H';
				$f->model("td/tdoc")->params(array('data'=>$data))->save("insert");
				$f->model('ac/log')->params(array(
					'modulo'=>'TD',
					'bandeja'=>'Tipos de Documento',
					'descr'=>'Se cre&oacute; el documento <b>'.$data['nomb'].'</b>.'
				))->save('insert');
				$f->response->json($data);
			}
		}else{
			$doc = $f->model('td/tdoc')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'TD',
					'bandeja'=>'Tipos de Documento',
					'descr'=>'Se '.$word.' el documento <b>'.$doc['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'TD',
					'bandeja'=>'Tipos de Documento',
					'descr'=>'Se actualiz&oacute; el documento <b>'.$doc['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("td/tdoc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->response->json($doc);
		}
	}
	function execute_update(){
    	global $f;
    	$model = $f->model('td/tdoc')->save('datos');
    	$f->response->print( "true" );
	}
	function execute_delete(){
    	global $f;
    	$model = $f->model('td/tdoc')->delete('datos');
    	$f->response->print( "true" );
	}
	function execute_search(){
		global $f;
		$model = $f->model("td/tdoc")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json($model);
	}
	function execute_all(){
		global $f;
		$model = $f->model('td/tdoc')->get('all');
		$f->response->json($model->items);
	}
}
?>