<?php
class Controller_pr_eprog extends Controller{
	function execute_index(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Categor&iacute;a</button>');
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
		$model = $f->model("pr/eprog")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");		
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/eprog")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_searchproy(){
		global $f;
		$model = $f->model("pr/eprog")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("searchproy");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/eprog")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_savecate(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "CT";
			$f->model("pr/eprog")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Estructura Programatica',
				'descr'=>'Se Cre&oacute; La Categoria <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/eprog")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$prog = $f->model("pr/eprog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Programatica',
					'descr'=>'Se '.$array[$data["estado"]].' La Categoria <b>'.$prog["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Programatica',
					'descr'=>'Se Modific&oacute; La Categoria <b>'.$prog["nomb"].'</b>.'
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
			$f->model("pr/eprog")->params(array('data'=>$data))->save("insert");
			$prog['programas']['id'] = $data['_id'];
			$f->model("pr/eprog")->params(array('_id'=>new MongoId($f->request->data['categoria']),'data'=>$prog))->save("push");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Estructura Programatica',
				'descr'=>'Se Cre&oacute; El Programa <b>'.$data["nomb"].'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/eprog")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$prog = $f->model("pr/eprog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Programatica',
					'descr'=>'Se '.$array[$data["estado"]].' El Programa <b>'.$prog["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Programatica',
					'descr'=>'Se Modific&oacute; El Programa <b>'.$prog["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_saveproy(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['nivel'] = "PY";			
			$f->model("pr/eprog")->params(array('data'=>$data))->save("insert");
			$sprog['proyectos']['id'] = $data['_id'];
			$f->model("pr/eprog")->params(array('_id'=>new MongoId($f->request->data['programa']),'data'=>$sprog))->save("push");
				$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Estructura Programatica',
				'descr'=>'Se Cre&oacute; El Proyecto <b>'.$data["nomb"].'</b>.'
			))->save('insert');		
		}else{
			$f->model("pr/eprog")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$prog = $f->model("pr/eprog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				$array = array("H"=>"Habilit&oacute;","D"=>"Deshabilit&oacute;");
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Programatica',
					'descr'=>'Se '.$array[$data["estado"]].' El Proyecto <b>'.$prog["nomb"].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PR',
					'bandeja'=>'Estructura Programatica',
					'descr'=>'Se Modific&oacute; El Proyecto <b>'.$prog["nomb"].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_editcate(){
		global $f;
		$f->response->view("pr/eprog.edit.cate");		
	}
	function execute_editprog(){
		global $f;
		$f->response->view("pr/eprog.edit.prog");		
	}
	function execute_editproy(){
		global $f;
		$f->response->view("pr/eprog.edit.proy");		
	}
	function execute_deleteproy(){
		global $f;
		$model = $f->model('pr/eprog')->params(array("amodificar"=>$f->request->data['programa'],"aeliminar"=>$f->request->id))->save('pull_proy');
    	$model = $f->model('pr/eprog')->params(array("_id"=>$f->request->id))->delete('eprog');		
    	$f->response->print( "true" );
	}
	function execute_deleteprog(){
		global $f;
		if(isset($f->request->data['proyectos'])){
			for($i=0;$i<count($f->request->data['proyectos']);$i++){
    			$model = $f->model('pr/eprog')->params(array("_id"=>$f->request->data['proyectos'][$i]['id']['$id']))->delete('eprog');
			}
		}
		$model = $f->model('pr/eprog')->params(array("amodificar"=>$f->request->categoria,"aeliminar"=>$f->request->id))->save('pull_prog');
		$model = $f->model('pr/eprog')->params(array("_id"=>$f->request->id))->delete('eprog');
    	$f->response->print( "true" );
	}
	function execute_deletecate(){
		global $f;
		for($i=0;$i<count($f->request->data['programas']);$i++){
			$model = $f->model("pr/eprog")->params(array("_id"=>new MongoId($f->request->data['programas'][$i]['id']['$id'])))->get("one");
			if(count($model->items["proyectos"])>0){
				for($e=0;$e<count($model->items["proyectos"]);$e++){
					$model = $f->model('pr/eprog')->params(array("_id"=>$model->items["proyectos"][$e]['id']['$id']))->delete('eprog');
				}
			}
    		$model = $f->model('pr/eprog')->params(array("_id"=>$f->request->data['programas'][$i]['id']['$id']))->delete('eprog');
		}
		$model = $f->model('pr/eprog')->params(array("_id"=>$f->request->id))->delete('eprog');
    	$f->response->print( "true" );
	}
}