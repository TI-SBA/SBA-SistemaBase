<?php
class Controller_lg_bien extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/bien")->params($params)->get("lista") );
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function execute_search_prod(){
		global $f;
		$model = $f->model("lg/bien")->params(array("prod"=>new MongoId($f->request->prod),"alma"=>new MongoId($f->request->alma)))->get("search_prod");
		$f->response->json( $model->items );
	}
	function execute_all(){
		global $f;
		$model = $f->model('lg/bien')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_cod(){
		global $f;
		$cod = $f->model('lg/bien')->get('cod');
		if($cod->items==null) $cod->items="000001";
		else{
			$tmp = intval($cod->items);
			$tmp++;
			$tmp = (string)$tmp;
			for($i=strlen($tmp); $i<6; $i++){
				$tmp = '0'.$tmp;
			}
			$cod->items = $tmp;
		}
		$f->response->json( array(
			'cod'=>$cod->items
		) );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['cuenta'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['producto'])) $data['producto']['_id'] = new MongoId($data['producto']['_id']);
		if(isset($data['producto']['unidad'])) $data['producto']['unidad']['_id'] = new MongoId($data['producto']['unidad']['_id']);
		if(isset($data['producto']['clasif'])) $data['producto']['clasif']['_id'] = new MongoId($data['producto']['clasif']['_id']);
		if(isset($data['valor_inicial'])) $data['valor_inicial'] = floatval($data['valor_inicial']);
		if(isset($data['valor_actual'])) $data['valor_actual'] = floatval($data['valor_actual']);
		if(isset($data['porc_depreciacion'])) $data['porc_depreciacion'] = floatval($data['porc_depreciacion']);
		if(isset($data['entrada']['fec'])) $data['entrada']['fec'] = new MongoDate(strtotime($data['entrada']['fec']));
		if(isset($data['solicitante'])) $data['solicitante']['_id'] = new MongoId($data['solicitante']['_id']);
		if(isset($data['solicitante']['cargo']['organizacion'])) $data['solicitante']['cargo']['organizacion']['_id'] = new MongoId($data['solicitante']['cargo']['organizacion']['_id']);
		if(isset($data['responsable'])) $data['responsable']['_id'] = new MongoId($data['responsable']['_id']);
		if(isset($data['responsable']['cargo']['organizacion'])) $data['responsable']['cargo']['organizacion']['_id'] = new MongoId($data['responsable']['cargo']['organizacion']['_id']);
		if(isset($data['fecinst'])) $data['fecinst'] = new MongoDate(strtotime($data['fecinst']));
		if(isset($data['salida']['fec'])) $data['salida']['fec'] = new MongoDate(strtotime($data['salida']['fec']));
		if(isset($data['almacen'])){
			$data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
			$data['almacen']['local']['_id'] = new MongoId($data['almacen']['local']['_id']);
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$f->model("lg/bien")->params(array('data'=>$data))->save("insert");
			switch($data['tipo']){
				case 'A': $tipo = 'Activo'; break;
				case 'N': $tipo = 'Bien No Depreciable'; break;
				case 'U': $tipo = 'Bien Auxiliar'; break;
			}
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Activos y Bienes',
				'descr'=>'Se cre&oacute; el <b>'.$tipo.'</b>: <b>'.$data['cod'].'</b> que representa al producto <b>'.$data['producto']['nomb'].'</b>'
			))->save('insert');
		}else{
			$f->model("lg/bien")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$bien = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			switch($bien['tipo']){
				case 'A': $tipo = 'Activo'; break;
				case 'N': $tipo = 'Bien No Depreciable'; break;
				case 'U': $tipo = 'Bien Auxiliar'; break;
			}
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Activos y Bienes',
				'descr'=>'Se actualiz&oacute; el <b>'.$tipo.'</b> <b>'.$bien['cod'].'</b> que representa al producto <b>'.$bien['producto']['nomb'].'</b>'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_depreciar(){
		global $f;
		$fecreg = new MongoDate();
		$data = $f->request->data;
		$dep = array();
		foreach ($data['depreciacion'] as $depre){
			$depre['fecreg'] = $fecreg;
			$depre['periodo'] = new MongoDate(strtotime($depre['periodo']));
			$depre['porc'] = floatval($depre['porc']);
			$depre['total'] = floatval($depre['total']);
			$depre['acumulado'] = floatval($depre['acumulado']);
			if(isset($depre['mejora'])) $depre['mejora'] = floatval($depre['mejora']);
			$dep[] = $depre;
		}
		$upd = array(
			'$set'=>array('valor_actual'=>floatval($data['valor_actual'])),
			'$pushAll'=>array('depreciacion'=>$dep)
		);
		$f->model("lg/bien")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("custom");
		$bien = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		switch($bien['tipo']){
			case 'A': $tipo = 'Activo'; break;
			case 'N': $tipo = 'Bien No Depreciable'; break;
			case 'U': $tipo = 'Bien Auxiliar'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Activos y Bienes',
			'descr'=>'Se <b>Depreci&oacute;</b> el <b>'.$tipo.'</b> <b>'.$bien['cod'].'</b> que representa al producto <b>'.$bien['producto']['nomb'].'</b>'
		))->save('insert');
	}
	function execute_save_baja(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$upd = array(
			'conservacion'=>$data['conservacion'],
			'estado'=>'B',
			'baja'=>array(
				'fecreg'=>new MongoDate(),
				'fecres'=>new MongoDate(strtotime($data['fecres'])),
				'trabajador'=>$trabajador,
				'resolucion'=>$data['resolucion'],
				'causal'=>$data['causal'],
				'ref'=>$data['ref']
			)
		);
		$f->model("lg/bien")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");
		$bien = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		switch($bien['tipo']){
			case 'A': $tipo = 'Activo'; break;
			case 'N': $tipo = 'Bien No Depreciable'; break;
			case 'U': $tipo = 'Bien Auxiliar'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Activos y Bienes',
			'descr'=>'Se di&oacute; de <b>Baja</b> el <b>'.$tipo.'</b> <b>'.$bien['cod'].'</b> que representa al producto <b>'.$bien['producto']['nomb'].'</b>'
		))->save('insert');
	}
	function execute_save_alta(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$upd = array(
			'conservacion'=>$data['conservacion'],
			'estado'=>'A',
			'alta'=>array(
				'fecreg'=>new MongoDate(),
				'fecres'=>new MongoDate(strtotime($data['fecres'])),
				'trabajador'=>$trabajador,
				'resolucion'=>$data['resolucion'],
				'causal'=>$data['causal'],
				'ref'=>$data['ref']
			)
		);
		$f->model("lg/bien")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");
		$bien = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		switch($bien['tipo']){
			case 'A': $tipo = 'Activo'; break;
			case 'N': $tipo = 'Bien No Depreciable'; break;
			case 'U': $tipo = 'Bien Auxiliar'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Activos y Bienes',
			'descr'=>'Se di&oacute; de <b>Alta</b> el <b>'.$tipo.'</b> <b>'.$bien['cod'].'</b> que representa al producto <b>'.$bien['producto']['nomb'].'</b>'
		))->save('insert');
	}
	function execute_save_trasp(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		$new = $data['destino'];
		if(isset($new['_id']))
			$new['_id'] = new MongoId($new['_id']);
		if(isset($new['cargo']['_id']))
			$new['cargo']['_id'] = new MongoId($new['cargo']['_id']);
		if(isset($new['cargo']['organizacion']['_id']))
			$new['cargo']['organizacion']['_id'] = new MongoId($new['cargo']['organizacion']['_id']);
		$old = $data['origen'];
		if(isset($old['_id']))
			$old['_id'] = new MongoId($old['_id']);
		if(isset($old['cargo']['_id']))
			$old['cargo']['_id'] = new MongoId($old['cargo']['_id']);
		if(isset($old['cargo']['organizacion']['_id']))
			$old['cargo']['organizacion']['_id'] = new MongoId($old['cargo']['organizacion']['_id']);
		$upd = array(
			'responsable'=>$new
		);
		$f->model("lg/bien")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");
		$upd = array(
			'$push'=>array('responsable_antiguo'=>array(
				'trabajador'=>$old,
				'fecreg'=>new MongoDate(),
				'destino'=>$new
			))
		);
		$f->model("lg/bien")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("custom");
		$bien = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		switch($bien['tipo']){
			case 'A': $tipo = 'Activo'; break;
			case 'N': $tipo = 'Bien No Depreciable'; break;
			case 'U': $tipo = 'Bien Auxiliar'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Activos y Bienes',
			'descr'=>'Se <b>Traspas&oacute;</b> el <b>'.$tipo.'</b> <b>'.$bien['cod'].'</b> que representa al producto <b>'.$bien['producto']['nomb'].
				'</b> desde el trabajador <b>'.$old['nomb'].' '.$old['appat'].' '.$old['apmat'].'</b>'.
				'hacia el trabajador <b>'.$new['nomb'].' '.$new['appat'].' '.$new['apmat'].'</b>'
		))->save('insert');
		$f->response->print('true');
	}























	function execute_details(){
		global $f;
		$f->response->view("lg/bien.details");
	}
	function execute_new(){
		global $f;
		$f->response->view("lg/bien.new");
	}
	function execute_newno(){
		global $f;
		$f->response->view("lg/bien.newno");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/bien.edit");
	}
	function execute_editno(){
		global $f;
		$f->response->view("lg/bien.editno");
	}
	function execute_select(){
		global $f;
		$f->response->view("lg/bien.select");
	}
	function execute_dep(){
		global $f;
		$f->response->view("lg/bien.dep");
	}
	function execute_alt(){
		global $f;
		$f->response->view("lg/bien.alt");
	}
	function execute_baj(){
		global $f;
		$f->response->view("lg/bien.baj");
	}
	function execute_trasp(){
		global $f;
		$f->response->view("lg/bien.trasp");
	}
	function execute_print_trasp(){
		global $f;
		$data = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$index = sizeof($data->items['responsable_antiguo'])-1;
		$data->items['responsable_antiguo'][$index]['trabajador'] = $f->model('mg/entidad')->params(array('_id'=>$data->items['responsable_antiguo'][$index]['trabajador']['_id']))->get('one')->items;
		$data->items['responsable_antiguo'][$index]['destino'] = $f->model('mg/entidad')->params(array('_id'=>$data->items['responsable_antiguo'][$index]['destino']['_id']))->get('one')->items;
		$data->items['responsable_antiguo'] = $data->items['responsable_antiguo'][$index];
		/*
		 * donde el campo responsable_antiguo es el que les servira
		 * este campo sera un array de n items, seleccionen el ultimo item y usen los siguientes campos
		 * 
		 * -trabajador: trabajador que traspasa
		 * -destino: trabajador que recepciona
		 * 
		 * los demas datos de las caracterisitcas del bien ya las tienen en la coleccion
		 */
		 //print_r($data);
		 $f->response->view("lg/repo.bien.trasp.print",$data);
	}
}
?>