<?php
class Controller_cj_conc extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Concepto</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>350 ),
			array( "nomb"=>"C&oacute;digo","w"=>200 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/conc")->params($params)->get("lista") );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("cj/conc")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_get_serv(){
		global $f;
		$model = $f->model('cj/conc')->params(array('serv'=>new MongoId($f->request->data['id'])))->get('all')->items;
		if(!isset($f->request->data['ecom'])){
			$vars = array();
			$varss = $f->model("mg/vari")->params(array("fields"=>array(
				'cod'=>true,
				'nomb'=>true,
				'valor'=>true
			)))->get("all");
			foreach ($varss->items as $item){
				$vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
			}
			$f->response->json(array('serv'=>$model,'vars'=>$vars));
		}else{
			$f->response->json($model);
		}
	}
	function execute_all(){
		global $f;
		$model = $f->model('cj/conc')->get('all');
		$f->response->json($model->items);
	}
	function execute_all_var(){
		global $f;
		$model = $f->model('cj/conc')->params(array('fields'=>array('cod'=>true,'nomb'=>true)))->get('all');
		$vari = $f->model('mg/vari')->params(array('fields'=>array('cod'=>true,'nomb'=>true)))->get('all');
		$defs = array(
			array('cod'=>'SALDO','nomb'=>'Saldo Pendiente Pago del Servicio'),
			array('cod'=>'FECVEN','nomb'=>'Fecha de Vencimiento de Pago del Servicio'),
			array('cod'=>'CM_PREC_PERP','nomb'=>'Cementerio - Precio del espacio perpetuo'),
			array('cod'=>'CM_PREC_TEMP','nomb'=>'Cementerio - Precio del espacio temporal'),
			array('cod'=>'CM_PREC_VIDA','nomb'=>'Cementerio - Precio del espacio en vida'),
			array('cod'=>'CM_ACCE_PREC','nomb'=>'Cementerio - Precio del accesorio'),
			array('cod'=>'CM_TIPO_ESPA','nomb'=>'Cementerio - Tipo de espacio'),
			array('cod'=>'IN_GARANTIA','nomb'=>'Inmuebles - Precio de la garant&iacute;a'),
			array('cod'=>'IN_PREC','nomb'=>'Inmuebles - Precio del espacio'),
			array('cod'=>'IN_MONEDA','nomb'=>'Inmuebles - Tipo de moneda de renta'),
			array('cod'=>'IN_OCUP_INI','nomb'=>'Inmuebles - Fecha de inicio de ocupaci&oacute;n'),
			array('cod'=>'IN_OCUP_FIN','nomb'=>'Inmuebles - Fecha de fin de ocupaci&oacute;n'),
			array('cod'=>'TU_PREC_SOL','nomb'=>'TUPA - Precio del procedimiento en soles'),
			array('cod'=>'TU_PREC_UIT','nomb'=>'TUPA - Precio del procedimiento en UIT')
		);
		$f->response->json(array('conc'=>$model->items,'vari'=>$vari->items,'defs'=>$defs));
	}
	function execute_get(){
		global $f;
		$model = $f->model("cj/conc")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		foreach ($model['servicios'] as $i=>$serv){
			$ref = array(
				'$ref'=>'mg_servicios',
				'$id'=>$serv
			);
			$model['servicios'][$i] = $f->datastore->getDBRef($ref);
		}
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$data['fecmod'] = new MongoDate();
		if(isset($data['cuenta']['_id'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['clasificador']['_id'])) $data['clasificador']['_id'] = new MongoId($data['clasificador']['_id']);
		if(isset($data['cuenta']['_id'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		foreach ($data['servicios'] as $i=>$serv){
			$data['servicios'][$i] = new MongoId($serv);
		}
		if(!isset($f->request->data['_id'])){
			$data['estado'] = 'H';
			$data['autor'] = $trabajador;
			$data['historico'] = array(0=>array(
				'autor'=>$trabajador,
				'descr'=>$data['descr'],
				'formula'=>$data['formula'],
				'fecreg'=>$data['fecmod'],
				'servicios'=>$data['servicios']
			));
			$f->model("cj/conc")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'CJ',
				'bandeja'=>'Conceptos',
				'descr'=>'Se cre&oacute; el concepto <b>'.$data['nomb'].'</b> con c&oacute;digo <b>'.$data['cod'].'</b>'
			))->save('insert');
		}else{
			unset($data['_id']);
			$f->model("cj/conc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				'$push'=>array('historico'=>array(
					'autor'=>$trabajador,
					'descr'=>$data['descr'],
					'formula'=>$data['formula'],
					'fecreg'=>$data['fecmod'],
					'servicios'=>$data['servicios']
				)),
				'$set'=>$data
			)))->save("update");
			$vari = $f->model("cj/conc")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'CJ',
					'bandeja'=>'Conceptos',
					'descr'=>'Se '.$word.' el concepto <b>'.$vari['nomb'].'</b> con c&oacute;digo <b>'.$vari['cod'].'</b>'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'CJ',
					'bandeja'=>'Conceptos',
					'descr'=>'Se actualiz&oacute; el concepto <b>'.$vari['nomb'].'</b> con c&oacute;digo <b>'.$vari['cod'].'</b>'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("cj/conc.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("cj/conc.select");
	}
	function execute_form(){
		global $f;
		$f->response->view("cj/conc.form");
	}
	function execute_details(){
		global $f;
		$f->response->view("cj/conc.details");
	}
}
?>