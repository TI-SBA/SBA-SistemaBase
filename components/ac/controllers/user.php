<?php
class Controller_ac_user extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ac/user")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ac/user")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($items['owner']))
			$items['owner'] = $f->model('mg/entidad')->params(array('_id'=>$items['owner']['_id']))->get('one')->items;
		$f->response->json( $items );
	}
	function execute_validar(){
		global $f;
		$f->response->json( $f->model("ac/user")->params(array("userid"=>$f->request->data['userid']))->get("validar")->items );
	}
	function execute_all(){
		global $f;
		$params = array();
		if(isset($f->request->data['online'])){
			if($f->request->data['online']=='1'){
				$params['online'] = true;
			}
		}
		$model = $f->model("ac/user")->params($params)->get("all");
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['passwd'])) $data['passwd'] = sha1($data['passwd']);
		if(isset($data['owner']['_id'])) $data['owner']['_id'] = new MongoId($data['owner']['_id']);
		foreach($data['groups'] as $i => $obj){
			$data['groups'][$i]['_id'] = new MongoId($obj['_id']);
		}
		if(isset($data['oficina']['_id'])) $data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
		if(isset($data['programa']['_id'])) $data['programa']['_id'] = new MongoId($data['programa']['_id']);
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['actividad']['_id']['$id']))
			$data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']['$id']);
		if(isset($data['organizacion']['actividad']['_id']))
			$data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente']['_id']['$id']))
			$data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']['$id']);
		if(isset($data['organizacion']['componente']['_id']))
			$data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$user = $f->model("ac/user")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AC',
				'bandeja'=>'Usuarios',
				'descr'=>'Se creo el usuario de seguridad para <b>'.$user['owner']['nomb'].'</b> con cuenta <b>'.$user['userid'].'</b>.'
			))->save('insert');
		}else{
			$f->model("ac/user")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			$user = $f->model("ac/user")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AC',
				'bandeja'=>'Usuarios',
				'descr'=>'Se actualizaron los datos del usuario de seguridad para <b>'.$user['owner']['nomb'].'</b> con cuenta <b>'.$user['userid'].'</b>.'
			))->save('insert');
		}
		if(isset($data['programa']) && isset($data['oficina'])){
			$mod_entidad = array(
				//'roles.trabajador.organizacion'=>$data['organizacion'],
				'roles.trabajador.cargo.funcion'=>$data['funcion'],
				//'roles.trabajador.cargo.organizacion'=>$data['organizacion'],
				'roles.trabajador.oficina'=>$data['oficina'],
				'roles.trabajador.programa'=>$data['programa'],
				//'roles.trabajador.estado'=>'H'
			);
			$f->model("mg/entidad")->params(array('_id'=>$data['owner']['_id'],'data'=>$mod_entidad))->save("update");
		}
		$f->model('ac/grup')->params(array(
			'userid'=>$user['userid'],
			'_id'=>$user['_id'],
			'groups'=>$data['groups']
		))->save('reset');
		$f->response->json($user);
	}
	function execute_save_pass(){
		global $f;
		$data = $f->request->data;
		$user = $f->model("ac/user")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
		if(sha1($data['old'])!=$user['passwd']){
			$f->response->json(array('error'=>true));
		}else{
			$f->model("ac/user")->params(array('_id'=>new MongoId($data['_id']),'data'=>array(
				'passwd'=>sha1($data['pwd'])
			)))->save("update");
			$f->response->print("true");
		}
	}
	function execute_new(){
		global $f;
		$f->response->view("ac/user.new");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ac/user.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ac/user.details");
	}
	function execute_main(){
		global $f;
		$f->response->view('ac/user.main');
	}
	function execute_new_pass(){
		global $f;
		$f->response->view('ac/user.pass');
	}
}
?>