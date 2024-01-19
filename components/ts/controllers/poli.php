<?php
class Controller_ts_poli extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('<label>Organizaci&oacute;n:</label> <b><span name="orga"></span></b>&nbsp;');
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva P&oacute;liza</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"C&oacute;digo","w"=>150 ),
			array( "nomb"=>"Motivo","w"=>200 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_toda() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<b><span name="orga">Organizaci&oacute;n</span></b>&nbsp;<div style="display:inline;" name="divOrga">
			<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
			<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
		</div>&nbsp;');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"C&oacute;digo","w"=>150 ),
			array( "nomb"=>"Motivo","w"=>200 ),
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
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		);
		if(isset($f->request->data['orga'])) $params['orga'] = new MongoId($f->request->data['orga']);
		$model = $f->model("ts/poli")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ts/poli")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ts/poli')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/poli")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_cta(){
		global $f;
		$f->response->json(array(
			'ctban'=>$f->model('ts/ctban')->get('all')->items,
			'tmed'=>$f->model('ts/tipo')->get('all')->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['cont_patrimonial'])){
			foreach ($data['cont_patrimonial'] as $i=>$item){
				if(isset($item['cuenta']['_id'])) $data['cont_patrimonial'][$i]['cuenta']['_id'] = new MongoId($item['cuenta']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'R';
      		$data['autor'] = $f->session->userDB;
			$f->model("ts/poli")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'P&oacute;lizas Contables',
				'descr'=>'Se <b>Cre&oacute;</b> la p&oacute;liza contable <b>'.$cursor["cod"].'</b> .'
			))->save('insert');
		}else{
			$f->model("ts/poli")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$cursor = $f->model("ts/poli")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'P&oacute;lizas Contables',
				'descr'=>'Se <b>Actualiz&oacute;</b> la p&oacute;liza contable <b>'.$cursor["cod"].'</b> .'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$model = $f->model("ts/poli")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$model->items["organizacion"] = $f->model("mg/orga")->params(array("_id"=>$model->items["autor"]["cargo"]["organizacion"]["_id"]))->get("one")->items;
			$f->response->view("ts/poli.print",$model);
		}else{
			$f->response->print("Ha ocurrido un error, no se ha encontrado la Poliza Contable");
		}
	}
	function execute_new(){
		global $f;
		$f->response->view("ts/poli.new");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/poli.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ts/poli.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/poli.details");
	}
	function execute_cta(){
		global $f;
		$f->response->view("ts/poli.cta");
	}
}
?>