<?php
class Controller_pe_turn extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("pe/turn")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($f->request->data['programas']))
			$items['programas'] = $f->model('mg/prog')->get('all')->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['programa']))
			$data['programa']['_id'] = new MongoId($data['programa']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("pe/turn")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Turnos',
				'descr'=>'Se creó el Turno <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("pe/turn")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Turnos',
				'descr'=>'Se actualizó el Turno <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("pe/turn")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json();
	}
	function execute_reporte(){
		global $f;
		$data = $f->request->data;
		$items = $f->model('pe/turn')->params($params)->get('all')->items;
		$f->response->view('pe/turnos.report.php',array('reporte'=>$items));
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/turn.edit");
	}
	function execute_modal(){
		global $f;
		$f->response->view("pe/turn.modal");
	}
}
?>