<?php
class Controller_pr_acti extends Controller{
	function execute_index(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Actividad</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"C&oacute;digo","w"=>200 ),
			3=>array( "nomb"=>"Nombre","w"=>300 ),
			4=>array( "nomb"=>"Tipo","w"=>100 ),
			5=>array( "nomb"=>"Registrado","w"=>200 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/acti")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");		
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/acti")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/acti")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_saveacti(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "AC";
			$f->model("pr/acti")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Actividades y Componentes',
				'descr'=>'Se Cre&oacute; la Actividad <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/acti")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$acti = $f->model("pr/acti")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Actividades y Componentes',
					'descr'=>'Se '.$array[$data["estado"]].' la Actividad <b>'.$acti["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Actividades y Componentes',
					'descr'=>'Se Modific&oacute; la Actividad <b>'.$acti["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_savecomp(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "CO";			
			$f->model("pr/acti")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Actividades y Componentes',
				'descr'=>'Se Cre&oacute; El Componente <b>'.$data["nomb"].'</b>.'
			))->save('insert');
			$prog['componentes']['id'] = $data['_id'];
			$f->model("pr/acti")->params(array('_id'=>new MongoId($f->request->data['actividad']),'data'=>$prog))->save("push");		
		}else{
			$f->model("pr/acti")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$acti = $f->model("pr/acti")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Actividades y Componentes',
					'descr'=>'Se '.$array[$data["estado"]].' El Componente <b>'.$acti["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Actividades y Componentes',
					'descr'=>'Se Modific&oacute; El Componente <b>'.$acti["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_editacti(){
		global $f;
		$f->response->view("pr/acti.edit.acti");		
	}
	function execute_editcomp(){
		global $f;
		$f->response->view("pr/acti.edit.comp");		
	}
	function execute_selectsubprog(){
		global $f;
		$f->response->view("pr/acti.select.subprog");		
	}
	function execute_selectproy(){
		global $f;
		$f->response->view("pr/acti.select.proy");		
	}
	function execute_deletecomp(){
		global $f;
    	$model = $f->model('pr/acti')->params(array("_id"=>$f->request->id))->delete('comp');
    	$f->response->print( "true" );
	}
	function execute_deleteacti(){
		global $f;
    	$model = $f->model('pr/acti')->params(array("_id"=>$f->request->id))->delete('acti');
    	$f->response->print( "true" );
	}
}