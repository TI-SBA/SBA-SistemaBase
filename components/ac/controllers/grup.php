<?php
class Controller_ac_grup extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ac/grup")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$model = $f->model("ac/grup")->params(array("_id"=>$f->request->_id))->get("one");
		$f->response->json( $model->items );
	}
	function execute_details(){
		global $f;
		$f->response->view("ac/enti.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ac/grup.edit");
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['members'])){
			if(sizeof($data['members'])>0){
				foreach($data['members'] as $i => $obj){
					$data['members'][$i]['_id'] = new MongoId($obj['_id']);
				}
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$grup = $f->model("ac/grup")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AC',
				'bandeja'=>'Grupos',
				'descr'=>'Se creo el grupo de seguridad <b>'.$grup['descr'].'</b> con c&oacute;digo <b>'.$grup['groupid'].'</b>.'
			))->save('insert');
		}else{
			$f->model("ac/grup")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			$grup = $f->model("ac/grup")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AC',
				'bandeja'=>'Grupos',
				'descr'=>'Se actualizaron los datos del grupo de seguridad <b>'.$grup['descr'].'</b> con c&oacute;digo <b>'.$grup['groupid'].'</b>.'
			))->save('insert');
		}
		$f->response->json( $grup );
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ac/grup')->delete('datos');
    	$f->response->print( "true" );
	}
	function execute_validar(){
		global $f;
		$model = $f->model("ac/grup")->params(array("groupid"=>$f->request->groupid))->get("validar");
		$f->response->json( $model->obj );
	}
	function execute_tasks(){
		global $f;
		$model = $f->model("ac/task")->get("lista");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model("ac/grup")->get("all");
		$f->response->json( $model->items );
	}
}
?>