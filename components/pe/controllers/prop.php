<?php
class Controller_pe_prop extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Organizaci&oacute;n: <label name="organomb"></label>');
		$f->response->print('<span name="FilOrga">
			<input type="hidden" name="organizacion" value="">
			<span name="orgainput">
			<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
			<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
			</span>
			</span>');
		$f->response->print('&nbsp; Periodo <select name="mes">
				<option value="1">Enero</option>
				<option value="2">Febrero</option>
				<option value="3">Marzo</option>
				<option value="4">Abril</option>
				<option value="5">Mayo</option>
				<option value="6">Junio</option>
				<option value="7">Julio</option>
				<option value="8">Agosto</option>
				<option value="9">Setiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option>
		</select> &nbsp; <input type="text" name="periodo" size="4">');
		$f->response->print('<button name="btnEditar">Editar</button>');
		$f->response->print('<button name="btnGuardar" style="display:none">Guardar</button>');
		$f->response->print('<button name="btnImprimir">Imprimir</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"Practicante","w"=>250 ),
			1=>array( "nomb"=>"Propina","w"=>100 ),
			2=>array( "nomb"=>"Registrado por","w"=>250 ),
			3=>array( "nomb"=>"Registrado","w"=>150)
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
			"organizacion"=>new MongoId($f->request->organizacion),
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['practicas'] = $f->model("pe/prop")->params(array(
					"_id"=>$model->items[$i]['_id']->{'$id'},
					"mes"=>$f->request->mes,
					"periodo"=>$f->request->periodo
				))->get("one_prop")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_print(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"roles"=>'practicante',
			"organizacion"=>new MongoId($f->request->organizacion),
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['practicas'] = $f->model("pe/prop")->params(array(
					"_id"=>$model->items[$i]['_id']->{'$id'},
					"mes"=>$f->request->mes,
					"periodo"=>$f->request->periodo
				))->get("one_prop")->items;
			}
		}
		$model->filtros = $f->request->data;
		$f->response->view("pe/prop.print",$model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("pe/prop")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");		
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/prop')->params(array("_id"=>$f->request->id))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/prop")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_prop(){
		global $f;
		$model = $f->model("pe/prop")->params(array("_id"=>new MongoId($f->request->_id),"periodo"=>$f->request->periodo,"mes"=>$f->request->mes))->get("one_prop");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']=new MongoDate();
			$data["trabajador"] = $f->session->userDB;
			$f->model("pe/prop")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Propinas',
				'descr'=>'Se cre&oacute; la <b>Propina</b> para el practicante <b>'.$data['practicante']['nomb'].' '.$data['practicante']['appat'].' '.$data['practicante']['apmat'].'</b> de la organizaci&oacute;n <b>'.$data['organizaci&oacute;n']['nomb'].'</b> para el periodo de <b>'.$data['periodo']['ano'].'-'.$data['periodo']['mes'].'</b> por el monto de <b>S/.'.$data['propina'].'</b>'
			))->save('insert');
		}else{
			$f->model("pe/prop")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$vari = $f->model('pe/prop')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Propinas',
				'descr'=>'Se actualiz&oacute; la <b>Propina</b> para el practicante <b>'.$vari['practicante']['nomb'].' '.$vari['practicante']['appat'].' '.$vari['practicante']['apmat'].'</b> de la organizaci&oacute;n <b>'.$vari['organizaci&oacute;n']['nomb'].'</b> para el periodo de <b>'.$vari['periodo']['ano'].'-'.$vari['periodo']['mes'].'</b> por el monto de <b>S/.'.$vari['propina'].'</b>'
			))->save('insert');
		}
		$f->response->print("true");
	}
}
?>