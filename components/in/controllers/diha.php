<?php
class Controller_in_diha extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("in/Controller_in_diha")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("in/diha")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;

		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
				$data['fecmod'] = new MongoDate();
				if(!isset($data['mes']))	throw new Exception("Error: no se recibió el mes del día habil");
				if(!isset($data['dia_cobro']))	throw new Exception("Error: no se recibió día habil");

				if(!isset($f->request->data['_id'])){
					$data['fecreg'] = new MongoDate();
					$data['autor'] = $f->session->userDB;
					$data['estado'] = 'H';
					$model = $f->model("in/diha")->params(array('data'=>$data))->save("insert")->items;
					$f->model('ac/log')->params(array(
						'modulo'=>'IN',
						'bandeja'=>'Día Habiles',
						'descr'=>'Se creó un dia Habil <b>'.$data['mes'].'</b>.'
					))->save('insert');
					$response['status'] = 'success';
					$response['message'] = 'Se inserto los cambios correctamente.';
					$response['data'] = $model;
				}else{
					$vari = $f->model("in/diha")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
					$f->model('ac/log')->params(array(
						'modulo'=>'IN',
						'bandeja'=>'Día Habiles',
						'descr'=>'Se actualizo un día Habiles <b>'.$data['mes'].'</b>.'
					))->save('insert');
					$model = $f->model("in/diha")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
					$response['status'] = 'success';
					$response['message'] = 'Se actualizo los cambios correctamente.';
					$response['data'] = $model;
				}
			}catch(Exception $e){
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
			}
		$f->response->json($response);

	}
	/*
	function execute_edit(){
		global $f;
		$f->response->view("in/acta.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("in/acta.details");
	}
	*/
}
?>