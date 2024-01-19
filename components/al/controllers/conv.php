<?php
class Controller_al_conv extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("al/conv")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('al/conv')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("al/conv")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']= new MongoDate();
			$data['fecini'] = new MongoDate(strtotime($data['fecini']));
			$data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
			$data['entidad']['_id'] = new MongoId($data['entidad']['_id']);
			$f->model("al/conv")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'AL',
				'bandeja'=>'Convenios',
				'descr'=>'Se cre&oacute; el Convenio con <b>'.$data['entidad']['nomb'].'</b>.'
			))->save('insert');
		}else{
			$data['fecini'] = new MongoDate(strtotime($data['fecini']));
			$data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
			$data['entidad']['_id'] = new MongoId($data['entidad']['_id']);
			$f->model("al/conv")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$f->model('ac/log')->params(array(
				'modulo'=>'AL',
				'bandeja'=>'Convenios',
				'descr'=>'Se actualiz&oacute; el Convenio con <b>'.$data['entidad']['nomb'].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('al/conv')->params(array("_id"=>new MongoId($f->request->data['_id'])))->delete('conv');
    	$f->response->print( "true" );
	}
	function execute_edit_conv(){
		global $f;
		$f->response->view("al/conv.edit");
	}
	function execute_details_conv(){
		global $f;
		$f->response->view("al/conv.details");
	}
}
?>