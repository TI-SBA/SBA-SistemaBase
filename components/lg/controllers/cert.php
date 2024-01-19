<?php
class Controller_lg_cert extends Controller {
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
				$params['trabajador'] = $f->session->enti['_id'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/cert")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/cert")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['modificado'] = $f->session->userDBMin;
		if(isset($data['productos'])){
			foreach ($data['productos'] as $index=>$prod){
				if(isset($prod['producto']['_id'])) $data['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
				if(isset($prod['producto']['unidad']['_id'])) $data['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
				if(isset($prod['producto']['clasif']['_id'])) $data['productos'][$index]['producto']['clasif']['_id'] = new MongoId($prod['producto']['clasif']['_id']);
			}
		}
		if(isset($data['fuente'])){
			$data["fuente"]["_id"] = new MongoId($data["fuente"]["_id"]);
		}
		if(isset($data['programa']['_id'])) $data['programa']['_id'] = new MongoId($data['programa']['_id']);
		if(isset($data['oficina']['_id'])) $data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'P';
			$data['trabajador'] = $f->session->userDB;
			$cod = $f->model("lg/cert")->get("cod");
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
			$model = $f->model("lg/cert")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Certificacion Presupuestaria',
				'descr'=>'Se creó la Certificacion Presupuestaria <b>'.$data['cod'].'</b>.'
			))->save('insert');
		}else{
			if(isset($data['revision'])){
				$data['revision']['trabajador'] = $f->session->userDB;
				/*$data['revision']['trabajador']['cargo'] = array(
					'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
				);*/
				$data['revision']['fec'] = new MongoDate();
				$f->model("lg/cert")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
				if(isset($data['estado'])){
					$f->model("lg/cert")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
					if($data['estado']=="A"){
						$cert = $f->model("lg/cert")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
						foreach($cert->items["productos"] as $j=>$prod){
							$rese = array(
								"periodo"=>$cert->items["periodo"],
								//"organizacion"=>$cert->items["organizacion"],
								"fuente"=>$cert->items["fuente"],
								"clasificador"=>$prod["producto"]["clasif"],
								"producto"=>array(
									"_id"=>$prod["producto"]["_id"],
									"cod"=>$prod["producto"]["cod"],
									"nomb"=>$prod["producto"]["nomb"],
									"unidad"=>$prod["producto"]["unidad"]
								),
								"cant"=>$prod["cant"],
								"monto"=>$prod["cred_solic"],
								"justificacion"=>$prod["justificacion"],
								"certificacion"=>array(
									"_id"=>$cert->items["_id"],
									"cod"=>$cert->items["cod"]
								)
							);
							$f->model("pr/rese")->params(array('data'=>$rese))->save("insert");
						}
					}
				}
			}else{
				$f->model("lg/cert")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			}
			$vari = $f->model("lg/cert")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Certificacion Presupuestaria',
				'descr'=>'Se actualizó la Certificacion Presupuestaria <b>'.$vari['cod'].'</b>.'
			))->save('insert');
			$model = $f->model("lg/cert")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/cert.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/cert.details");
	}
	function execute_print(){
		global $f;
		$model = $f->model("lg/cert")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->view("lg/repo.cert.print",$model);
	}
}
?>