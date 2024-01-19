<?php
class Controller_po_visi extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("po/visi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("po/visi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['funcionario']['_id'])){
			$data['funcionario']['_id'] = new MongoId($data['funcionario']['_id']);
			$func = $f->model("mg/entidad")->params(array("_id"=>$data['funcionario']['_id']))->get("one")->items;
			$data['oficina']=$func['roles']['trabajador']['oficina'];
			$data['cargo']=$func['roles']['trabajador']['cargo'];
		}
		if(isset($data['visitante']['_id']))
			$data['visitante']['_id'] = new MongoId($data['visitante']['_id']);
		if(isset($data['entidad']['es_empresa'])){
			if($data['entidad']['es_empresa'] === "true"){
				$data['entidad']['entidad']['_id'] = new MongoId($data['entidad']['entidad']['_id']);
				$data['entidad']['es_empresa'] = true;
			}else $data['entidad']['es_empresa'] = false;
		}

		$data['fecent'] = new MongoDate(strtotime($data['fec']." ".$data['ent']));
		$data['fecsal'] = new MongoDate(strtotime($data['fec']." ".$data['sal']));
		if(!isset($f->request->data['_id'])){
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'S';
			$model = $f->model("po/visi")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PO',
				'bandeja'=>'Visitas',
				'descr'=>'Se registro al Visitante <b>'.$data['visitante']['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("po/visi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PO',
				'bandeja'=>'Visitas',
				'descr'=>'Se actualizó al Visitante <b>'.$vari['visitante']['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("po/visi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("po/visi.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("po/visi.details");
	}
	function execute_print(){
		global $f;
		$cheque = $f->model("po/visi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->view("po/visi.print",array("cheque"=>$cheque));
	}
}
?>