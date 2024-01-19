<?php
class Controller_pe_cuad extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnConso">Generar Consolidado</button>&nbsp;');
		$f->response->print('<button name="btnAgregar">Nuevo CAP</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"A&ntilde;o","w"=>100 ),
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
		$model = $f->model("pe/cuad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/cuad")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/cuad')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/cuad")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_conso(){
		global $f;
		$model = $f->model("pe/cuad")->params(array('periodo'=>$f->request->data['periodo']))->get("conso");
		$f->response->json( $model->items );
	}
	function execute_veri(){
		global $f;
		$model = $f->model("pe/cuad")->params(array(
			"_id"=>new MongoId($f->request->data['orga']),
			"periodo"=>$f->request->data['peri']
		))->get("orga_peri");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['actividad'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(isset($data['items'])){
			foreach ($data['items'] as $i=>$item){
				if(isset($item['cargo_clasif']['_id'])) $data['items'][$i]['cargo_clasif']['_id'] = new MongoId($item['cargo_clasif']['_id']);
				if(isset($item['cargo']['_id'])) $data['items'][$i]['cargo']['_id'] = new MongoId($item['cargo']['_id']);
				if(isset($item['grupo_ocu']['_id'])) $data['items'][$i]['grupo_ocu']['_id'] = new MongoId($item['grupo_ocu']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'P';
			$data['trabajador'] = $f->session->userDB;
			$f->model("pe/cuad")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Cuadro de Asignaci&oacute;n de Personal (CAP)',
				'descr'=>'Se cre&oacute; el <b>Cuadro de Asignaci&oacute;n de Personal</b> para la organizaci&oacute;n <b>'.$data['organizacion']['nomb'].'</b> relativo al periodo <b>'.$data['periodo'].'</b>'
			))->save('insert');
		}else{
			$f->model("pe/cuad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$vari = $f->model("pe/cuad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='A') $word = 'aprob&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Cuadro de Asignaci&oacute;n de Personal (CAP)',
					'descr'=>'Se '.$word.' el <b>Cuadro de Asignaci&oacute;n de Personal</b> para la organizaci&oacute;n <b>'.$vari['organizacion']['nomb'].'</b> relativo al periodo <b>'.$vari['periodo'].'</b>'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Cuadro de Asignaci&oacute;n de Personal (CAP)',
					'descr'=>'Se actualiz&oacute; el <b>Cuadro de Asignaci&oacute;n de Personal</b> para la organizaci&oacute;n <b>'.$vari['organizacion']['nomb'].'</b> relativo al periodo <b>'.$vari['periodo'].'</b>'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_vigente(){
		global $f;
		$f->model("pe/cuad")->params(array('orga'=>new MongoId($f->request->data['orga'])))->save("reset_vig");
		$f->model("pe/cuad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>'V')))->save("update");
		$vari = $f->model("pe/cuad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Cuadro de Asignaci&oacute;n de Personal (CAP)',
			'descr'=>'Se design&oacute; como <b>Vigente</b> el <b>Cuadro de Asignaci&oacute;n de Personal</b> para la organizaci&oacute;n <b>'.$vari['organizacion']['nomb'].'</b> relativo al periodo <b>'.$vari['periodo'].'</b>'
		))->save('insert');
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/cuad.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/cuad.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("pe/cuad.details");
	}
	function execute_conso(){
		global $f;
		$f->response->view("pe/cuad.conso");
	}
	function execute_periodo(){
		global $f;
		$f->response->view("pe/periodo");
	}
}
?>