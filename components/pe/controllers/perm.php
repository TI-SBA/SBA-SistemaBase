<?php
class Controller_pe_perm extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Permiso</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Trabajador que solicita","w"=>350 ),
			array( "nomb"=>"Organizaci&oacute;n","w"=>100 ),
			array( "nomb"=>"Solicitado","w"=>150 ),
			array( "nomb"=>"Para el","w"=>150 ),
			array( "nomb"=>"Hasta el","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pe/perm")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/perm")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/perm')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/perm")->params(array("_id"=>new MongoId($f->request->_id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['_id']))
			$data['_id'] = new MongoId($data['_id']);
		if(isset($data['trabajador']['_id']))
			$data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['trabajador']['cargo']['_id']))
			$data['trabajador']['cargo']['_id'] = new MongoId($data['trabajador']['cargo']['_id']);
		if(isset($data['trabajador']['cargo']['organizacion']['_id']))
			$data['trabajador']['cargo']['organizacion']['_id'] = new MongoId($data['trabajador']['cargo']['organizacion']['_id']);
		if(isset($data['fecini']))
			$data['fecini'] = new MongoDate(strtotime($data['fecini']));
		if(isset($data['fecfin']))
			$data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'P';
			$f->model("pe/perm")->params(array('data'=>$data))->save("insert");
			$enti = $data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'];
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Permisos',
				'descr'=>'Se solicit&oacute; <b>Permiso</b> para el trabajador <b>'.$enti.'</b> desde <b>'.date('Y-M-d h:i:s', $data['fecini']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s', $data['fecfin']->sec).'</b>'
			))->save('insert');
		}else{
			$f->model("pe/perm")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$vari = $f->model("pe/perm")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$enti = $vari['trabajador']['nomb'].' '.$vari['trabajador']['appat'].' '.$vari['trabajador']['apmat'];
			if(isset($data['estado'])){
				if($data['estado']=='A') $word = 'aprob&oacute;';
				else $word = 'anul&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Permisos',
					'descr'=>'Se '.$word.' el permiso para el trabajador <b>'.$enti.'</b> desde <b>'.date('Y-M-d h:i:s', $vari['fecini']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s', $vari['fecfin']->sec).'</b>'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Permisos',
					'descr'=>'Se actualiz&oacute; el permiso para el trabajador <b>'.$enti.'</b> desde <b>'.date('Y-M-d h:i:s', $vari['fecini']->sec).'</b> hasta <b>'.date('Y-M-d h:i:s', $vari['fecfin']->sec).'</b>'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/perm.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/perm.select");
	}
}
?>