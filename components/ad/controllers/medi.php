<?php
class Controller_ad_medi extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ad/medi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ad/medi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo(){
		global $f;
		$items = $f->model("ad/medi")->params()->get("codigo")->items;
		$f->response->json( $items );
	}
	/*
	function execute_get_reporte(){
		global $f;
		$items = $f->model("ad/medi")->params()->get("reporte")->items;
		$f->response->view("ad/medi.print",array('medica'=>$items));
		
	}
	*/

	
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(!isset($f->request->data['_id'])){
			$data['his_cli']= floatval($data['his_cli']);
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("ad/medi")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ad/medi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ad/medi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ad/medi.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ad/medi.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ad/medi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('medi');
		$f->response->print("true");
	}
	
	function execute_print(){
		global $f;
		$medica = $f->model('ad/medi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/medi.print",array('medica'=>$medica));

	}
}
?>