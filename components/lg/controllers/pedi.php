<?php
class Controller_lg_pedi extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['trabajador']))
			if($f->request->data['trabajador']!='')
				$params['trabajador'] = new MongoId($f->request->data['trabajador']);
		if(isset($f->request->data['oficina']))
			if($f->request->data['oficina']!='')
				$params['oficina'] = new MongoId($f->request->data['oficina']);
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/pedi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/pedi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['modificado'] = $f->session->userDBMin;
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['programa']['_id'])) {
			$data['programa']['_id'] = new MongoId($data['programa']['_id']);
			$programa = $f->model('mg/prog')->params(array('_id'=>$data['programa']['_id']))->get('one')->items;
			$data['programa']['actividad'] = $programa['actividad'];
			$data['programa']['componente'] = $programa['componente'];
		}
		if(isset($data['oficina']['_id'])){
			$data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
			$oficina = $f->model('mg/ofic')->params(array('_id'=>$data['oficina']['_id']))->get('one')->items;
			$data['oficina']['meta'] = $oficina['meta'];
		}
		if(isset($data['expediente']['_id'])) $data['expediente']['_id'] = new MongoId($data['expediente']['_id']);
		if(isset($data['productos'])){
			if(count($data['productos'])>0){
				foreach($data['productos'] as $i=>$prod){
					$data['productos'][$i]['unidad']['_id'] = new MongoId($data['productos'][$i]['unidad']['_id']);
				}
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'P';
			$cod = $f->model("lg/pedi")->get("cod");
			if($cod->items==null) $cod->items="001000";
			else{
				$tmp = intval($cod->items);
				$tmp++;
				$tmp = (string)$tmp;
				for($i=strlen($tmp); $i<6; $i++){
					$tmp = '0'.$tmp;
				}
				$cod->items = $tmp;
			}
			$data['cod'] = $cod->items;
			$model = $f->model("lg/pedi")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Pedidos Internos',
				'descr'=>'Se creó el Pedido Interno <b>'.$data['cod'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("lg/pedi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Pedidos Internos',
				'descr'=>'Se actualizó el Pedido Interno <b>'.$vari['cod'].'</b>.'
			))->save('insert');
			if(isset($data['revision'])){
				print_r($f->session);
				$data['revision']['trabajador'] = $f->session->userDB;
				$data['revision']['programa'] = $f->session->enti['roles']['trabajador']['programa'];
				$data['revision']['fec'] = new MongoDate();
				$f->model("lg/pedi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
				if(isset($data['estado'])){
					$model = $f->model("lg/pedi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
				}
			}else{
				$model = $f->model("lg/pedi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			}
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$data = $f->request->data;
		if($data['tipo']=="B"){
			$f->response->view("lg/pedi.bien.edit");
		}elseif($data['tipo']=="S"){
			$f->response->view("lg/pedi.serv.edit");
		}elseif($data['tipo']=="L"){
			$f->response->view("lg/pedi.loca.edit");
		}
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/pedi.details", array("tipo"=>$f->request->data['tipo']));
	}
}
?>