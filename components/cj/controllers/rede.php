<?php
class Controller_cj_rede extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows);
		if(isset($f->request->data['texto']))
			$params['texto'] = $f->request->data['texto'];
		$model = $f->model("cj/rede")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("cj/rede")->params(array("_id"=>new MongoId($f->request->_id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_info(){
		global $f;
		$f->response->json(array(
			'tasa'=>$f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items,
			'ctban'=>$f->model("ts/ctban")->get("all")->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fec']))
			$data['fec_db'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['total']))
			$data['total'] = floatval($data['total']);
		if(isset($data['entidad']['_id']))
			$data['entidad']['_id'] = new MongoId($data['entidad']['_id']);
		if(isset($data['cuenta']['_id']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $i=>$vou){
				$data['vouchers'][$i]['monto'] = floatval($vou['monto']);
				$data['vouchers'][$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$data['trabajador'] = $f->session->userDB;
			$f->model("cj/rede")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'CJ',
				'bandeja'=>'Recibos Definitivos',
				'descr'=>'Se creo el Recibo Definitivo <b>'.$data['num'].'</b>.'
			))->save('insert');
		}else{
			$doc = $f->model('cj/rede')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			if(isset($data['estado'])){
				if($data['estado']=='X') $word = 'anul&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'CJ',
					'bandeja'=>'Recibos Definitivos',
					'descr'=>'Se '.$word.' el Recibo Definitivo <b>'.$doc['num'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'CJ',
					'bandeja'=>'Recibos Definitivos',
					'descr'=>'Se actualiz&oacute; el Recibo Definitivo <b>'.$doc['num'].'</b>.'
				))->save('insert');
			}
			$f->model("cj/rede")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("cj/rede.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("cj/rede.details");
	}
}
?>