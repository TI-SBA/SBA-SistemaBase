<?php
class Controller_lg_alma extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/alma")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		if(isset($f->request->data['fields'])) $fields = array('nomb'=>true);
		else $fields = array();
		$model = $f->model('lg/alma')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/alma")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['local']))
			$data['local']['_id'] = new MongoId($data['local']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = "H";
			$f->model("lg/alma")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Almacenes',
				'descr'=>'Se cre&oacute; el almac&eacute;n <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("lg/alma")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'LG',
					'bandeja'=>'Almacenes',
					'descr'=>'Se '.$word.' el almac&eacute;n <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'LG',
					'bandeja'=>'Almacenes',
					'descr'=>'Se actualiz&oacute; el almac&eacute;n <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("lg/alma")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/alma.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/alma.edit");
	}
}
?>