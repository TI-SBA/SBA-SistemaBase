<?php
global $f;
//$f->library('composer');
//		use TADPHP\TADFactory;
//		use TADPHP\TAD;
//		use TADPHP\TADResponse;
class Controller_pe_cargo extends Controller {
	/*function execute_subir(){
		global $f;
		use TADFactory;
		use TAD;
		/*echo "asdsad";
		die();*/
	/*	try {
			$comands = TAD::commands_available();
			$tad = (new TADFactory(['ip'=>'192.168.1.240', 'com_key'=>0]))->get_instance();
			//print_r($tad);
			$commands_list = TAD::commands_available();
			//print_r($commands_list);
			$logs = $tad->get_att_log();
			//echo $logs->get_response(['format'=>'json']);
			print_r($logs->to_array());
			//print_r($logs);	
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
	}*/
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
		$f->response->json( $f->model("pe/cargo")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/cargo')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/cargo")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_getby(){
		global $f;
		$model = $f->model("pe/cargo")->params(array("orga"=>new MongoId($f->request->orga)))->get("byOrga");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['organizacion'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['actividad'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['componente'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);

		if(isset($data['programa'])) $data['programa']['_id'] = new MongoId($data['programa']['_id']);

		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$f->model("pe/cargo")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Cargos',
				'descr'=>'Se cre&oacute; el Cargo <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("pe/cargo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Cargos',
					'descr'=>'Se '.$word.' el Cargo <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Cargos',
					'descr'=>'Se actualiz&oacute; el Cargo <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("pe/cargo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/cargo.edit");
	}
}
?>