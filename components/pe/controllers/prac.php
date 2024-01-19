<?php
class Controller_pe_prac extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Practicante</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>350 ),
			array( "nomb"=>"Organizaci&oacute;n","w"=>350 ),
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
		$model = $f->model("mg/entidad")->params(array(
			"roles"=>'practicante',
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("lista");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('mg/entidad')->params(array('rol'=>'roles.practicante'))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = array(
			'fec_ini'=>new MongoDate(strtotime($f->request->data['fec_ini'])),
			'propina'=>$f->request->data['propina'],
			'estado'=>'H',
			'organizacion'=>array(
				'_id'=>new MongoId($f->request->data['orga']['_id']),
				'nomb'=>$f->request->data['orga']['nomb']
			)
		);
		$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'roles.practicante'=>$data
		)))->save("update");
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Practicantes',
			'descr'=>'Se design&oacute; a <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b> como <b>Practicante</b> de la organizaci&oacute;n <b>'.$f->request->data['orga']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_upd(){
		global $f;
		$tmp = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items['roles']['practicante'];
		$data = array_merge($tmp,$f->request->data);
		unset($data['_id']);
		$f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('roles.practicante'=>$data)))->save("update");
		$f->response->print("true");
		$vari = $f->model("mg/entidad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($f->request->data['estado'])){
			if($f->request->data['estado']=='H') $word = 'habilit&oacute;';
			else $word = 'deshabilit&oacute;';
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Practicantes',
				'descr'=>'Se '.$word.' al practicante <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b>.'
			))->save('insert');
		}else{
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Practicantes',
				'descr'=>'Se actualiz&oacute; la informaci&oacute;n del practicante <b>'.$vari['nomb'].' '.$vari['appat'].' '.$vari['apmat'].'</b>.'
			))->save('insert');
		}
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/prac.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/prac.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("pe/prac.details");
	}
}
?>