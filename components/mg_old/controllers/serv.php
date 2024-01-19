<?php
class Controller_mg_serv extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			if($f->request->data['modulo']!='')
				$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['organizacion']))
			$params['organizacion'] = new MongoId($f->request->data['organizacion']);
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mg/serv")->params($params)->get("lista") );
	}
	function execute_search_lista(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$params = array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		);
		if(isset($f->request->data['modulo'])) $params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['organizacion'])) $params['organizacion'] = new MongoId($f->request->data['organizacion']);
		$model = $f->model("mg/serv")->params($params)->get("search_lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$params = array(
			"estado"=>$estado,
			"orga"=>new MongoId($f->request->data['orga']),
			"tipo"=>$f->request->data['tipo']
		);
		if(isset($f->request->data['aplicacion']))
			$params['aplicacion'] = $f->request->data['aplicacion'];
		$model = $f->model("mg/serv")->params($params)->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('mg/serv')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/serv")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_orga(){
		global $f;
		
		
		
		$orga = $f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$orga['actividad'] = $f->model("pr/acti")->params(array("_id"=>$orga['actividad']['_id']))->get("one")->items;
		$orga['componente'] = $f->model("pr/acti")->params(array("_id"=>$orga['componente']['_id']))->get("one")->items;
		$orga['subprograma'] = $f->model("pr/estr")->params(array("_id"=>new MongoId($orga['componente']['subprograma']['id'])))->get("one")->items;
		$orga['funcion'] = $f->model("pr/estr")->params(array("_id"=>$orga['subprograma']['funcion']['_id']))->get("one")->items;
		$f->response->json( $orga );
		
		
	}
	function execute_all_orga(){
		global $f;
		$model = $f->model('mg/serv')->get('all_orga');
		$f->response->json($model->items);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['actividad']['_id'])) $data['actividad']['_id'] = new MongoId($data['actividad']['_id']);
		if(isset($data['componente']['_id'])) $data['componente']['_id'] = new MongoId($data['componente']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("mg/serv")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'MG',
				'bandeja'=>'Servicios',
				'descr'=>'Se cre&oacute; el servicio <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$doc = $f->model('mg/serv')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'MG',
					'bandeja'=>'Servicios',
					'descr'=>'Se '.$word.' el servicio <b>'.$doc['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'MG',
					'bandeja'=>'Servicios',
					'descr'=>'Se actualiz&oacute; el servicio <b>'.$doc['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("mg/serv")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/serv.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("mg/serv.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("mg/serv.details");
	}
}
?>