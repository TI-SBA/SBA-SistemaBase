<?php
class Controller_ad_char extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ad/char")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ad/char")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo(){
		global $f;
		$items = $f->model("ad/char")->params()->get("codigo")->items;
		$f->response->json( $items );
	}
	
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		


		if(!isset($f->request->data['_id'])){
			//$data['his_cli']= floatval($data['his_cli']);
			//$data['seso'] = floatval($data['seso']);
			//
			$data['seso'] = floatval($data['seso']);
			$data['psic'] = floatval($data['psic']);
			$data['enfe'] = floatval($data['enfe']);
			$data['psiq'] = floatval($data['psiq']);
			$data['vchi'] = floatval($data['vchi']);
			$data['mchi'] = floatval($data['mchi']);
			$data['vjes'] = floatval($data['vjes']);
			$data['mjes'] = floatval($data['mjes']);
			$data['vcam'] = floatval($data['vcam']);
			$data['mcam'] = floatval($data['mcam']);
			$data['valf'] = floatval($data['valf']);
			$data['malf'] = floatval($data['malf']);
			$data['vgon'] = floatval($data['vgon']);
			$data['mgon'] = floatval($data['mgon']);
			$data['vjos'] = floatval($data['vjos']);
			$data['mjos'] = floatval($data['mjos']);
			$data['vmar'] = floatval($data['vmar']);
			$data['mmar'] = floatval($data['mmar']);
			$data['vcar'] = floatval($data['vcar']);
			$data['mcar'] = floatval($data['mcar']);
			$data['vasi'] = floatval($data['vasi']);
			$data['masi'] = floatval($data['masi']);
			$data['vfam'] = floatval($data['vfam']);
			$data['mfam'] = floatval($data['mfam']);
			$data['vfis'] = floatval($data['vfis']);
			$data['mfis'] = floatval($data['mfis']);
			$data['vmoq'] = floatval($data['vmoq']);
			$data['mmoq'] = floatval($data['mmoq']);
			$data['topa'] = floatval($data['topa']);
			$data['atad'] = floatval($data['atad']);
			$data['atch'] = floatval($data['atch']);
			$data['atje'] = floatval($data['atje']);
			$data['atca'] = floatval($data['atca']);
			$data['taad'] = floatval($data['taad']);
			/*$data['mes'] = floatval($data['mes']);
			$data['año'] = floatval($data['año']);
			$data['firm'] = floatval($data['firm']);
			*/
			//

			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("ad/char")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ad/char")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ad/char")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ad/char.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ad/char.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ad/char')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('char');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$charla = $f->model('ad/char')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/char.print",array('charla'=>$charla));

	}
}
?>