<?php
class Controller_pr_estr extends Controller{
	function execute_index(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Funcion</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"C&oacute;digo","w"=>200 ),
			3=>array( "nomb"=>"Nombre","w"=>300 ),
			4=>array( "nomb"=>"Registrado","w"=>200 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pr/estr")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");		
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/estr")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_searchsubprog(){
		global $f;
		$model = $f->model("pr/estr")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("searchsubprog");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/estr")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_savefunc(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "FN";
			$f->model("pr/estr")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Estructura Funcional',
				'descr'=>'Se Cre&oacute; La Funcion <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/estr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$estr = $f->model("pr/estr")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Funcional',
					'descr'=>'Se '.$array[$data["estado"]].' La Funcion <b>'.$estr["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Funcional',
					'descr'=>'Se Modific&oacute; La Funcion <b>'.$estr["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_saveprog(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "PR";			
			$f->model("pr/estr")->params(array('data'=>$data))->save("insert");
			$prog['programas']['id'] = $data['_id'];
			$f->model("pr/estr")->params(array('_id'=>new MongoId($f->request->data['funcion']),'data'=>$prog))->save("push");	
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Estructura Funcional',
				'descr'=>'Se Cre&oacute; El Programa <b>'.$data["nomb"].'</b>.'
			))->save('insert');		
		}else{
			$f->model("pr/estr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$estr = $f->model("pr/estr")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Funcional',
					'descr'=>'Se '.$array[$data["estado"]].' El Programa <b>'.$estr["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Funcional',
					'descr'=>'Se Modific&oacute; El Programa <b>'.$estr["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_savesubprog(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "SP";			
			$f->model("pr/estr")->params(array('data'=>$data))->save("insert");
			$sprog['subprogramas']['id'] = $data['_id'];
			$f->model("pr/estr")->params(array('_id'=>new MongoId($f->request->data['programa']),'data'=>$sprog))->save("push");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Estructura Funcional',
				'descr'=>'Se Cre&oacute; El Sub-Programa <b>'.$data["nomb"].'</b>.'
			))->save('insert');	
		}else{
			$f->model("pr/estr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$estr = $f->model("pr/estr")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Funcional',
					'descr'=>'Se '.$array[$data["estado"]].' El Sub-Programa <b>'.$estr["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Funcional',
					'descr'=>'Se Modific&oacute; El Sub-Programa <b>'.$estr["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_editfunc(){
		global $f;
		$f->response->view("pr/estr.edit.func");		
	}
	function execute_editprog(){
		global $f;
		$f->response->view("pr/estr.edit.prog");		
	}
	function execute_editsubprog(){
		global $f;
		$f->response->view("pr/estr.edit.subprog");		
	}
	function execute_deletesubprog(){
		global $f;
		$model = $f->model('pr/estr')->params(array("amodificar"=>$f->request->data['programa'],"aeliminar"=>$f->request->id))->save('pull_subprog');
    	$model = $f->model('pr/estr')->params(array("_id"=>$f->request->id))->delete('estr');		
    	$f->response->print( "true" );
	}
	function execute_deleteprog(){
		global $f;
		if(isset($f->request->data['subprogramas'])){
			for($i=0;$i<count($f->request->data['subprogramas']);$i++){
    			$model = $f->model('pr/estr')->params(array("_id"=>$f->request->data['subprogramas'][$i]['id']['$id']))->delete('estr');
			}
		}
		$model = $f->model('pr/estr')->params(array("amodificar"=>$f->request->funcion,"aeliminar"=>$f->request->id))->save('pull_prog');
		$model = $f->model('pr/estr')->params(array("_id"=>$f->request->id))->delete('estr');
    	$f->response->print( "true" );
	}
	function execute_deletefunc(){
		global $f;
		for($i=0;$i<count($f->request->data['programas']);$i++){
			$model = $f->model("pr/estr")->params(array("_id"=>new MongoId($f->request->data['programas'][$i]['id']['$id'])))->get("one");
			if(count($model->items["subprogramas"])>0){
				for($e=0;$e<count($model->items["subprogramas"]);$e++){
					$model = $f->model('pr/estr')->params(array("_id"=>$model->items["subprogramas"][$e]['id']['$id']))->delete('estr');
				}
			}
    		$model = $f->model('pr/estr')->params(array("_id"=>$f->request->data['programas'][$i]['id']['$id']))->delete('estr');
		}
		$model = $f->model('pr/estr')->params(array("_id"=>$f->request->id))->delete('estr');
    	$f->response->print( "true" );
	}
}