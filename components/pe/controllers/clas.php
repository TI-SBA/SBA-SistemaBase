<?php
class Controller_pe_clas extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("pe/clas")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/clas')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/clas")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$f->model("pe/clas")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Cargos Clasificados',
				'descr'=>'Se cre&oacute; el Cargo Clasificado <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("pe/clas")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Cargos Clasificados',
					'descr'=>'Se '.$word.' el Cargo Clasificado <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Cargos Clasificados',
					'descr'=>'Se actualiz&oacute; el Cargo Clasificado <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("pe/clas")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/clas.edit");
	}
}
?>