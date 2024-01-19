<?php
class Controller_mg_prog extends Controller {
	function execute_get(){
		global $f;
		$model = $f->model("mg/prog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$f->response->json( $model->items );
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['filter'])){
			$params['filter'] = $f->request->data['filter'];
			foreach ($params['filter'] as $i=>$filter){
				if(gettype($filter['value'])=="array"){
					if(isset($filter['value']['$exists'])){
						if($filter['value']['$exists']=='true') $filter['value'] = array('$exists'=>1);
						else $filter['value'] = array('$exists'=>0);
					}
				}
				$params['filter'][$i]['value'] = $filter['value'];
			}
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mg/prog")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		$f->response->json( $f->model("mg/prog")->params(array(
			'filter'=>array(
				'estado'=>'H'
			),
			'fields'=>array(
				'nomb'=>true
			)
		))->get("all")->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['trabajador'] = $f->session->userDB;
		$data['fecmod'] = new MongoDate();
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$data['autor'] = $f->session->userDB;
			$model = $f->model("mg/prog")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MG',
				'bandeja'=>'Programas',
				'descr'=>'Se cre&oacute; el programa <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("mg/prog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'MG',
					'bandeja'=>'Oficinas',
					'descr'=>'Se '.$word.' la oficina <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'MG',
					'bandeja'=>'Programas',
					'descr'=>'Se actualiz&oacute; el programa <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$model = $f->model("mg/prog")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/prog.edit");
	}
}
?>