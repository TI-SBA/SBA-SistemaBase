<?php
class Controller_ts_libo extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ts/libo")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ts/libo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_saldo(){
		global $f;
		$items = $f->model("ts/libo")->params()->get("saldo")->items;
		$f->response->json( $items );
		//print_r($f);
	}
	
	function execute_save(){
		global $f;
		$data = $f->request->data;
		//print_r($data);
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'A';
			$model = $f->model("ts/libo")->params(array('data'=>$data))->save("insert")->items;
			
		}else{
			$vari = $f->model("ts/libo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("ts/libo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	
/*
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error',
			'data'=>array()
		);
		try{
			if(!isset($data['Libro']))
				throw new Exception("Error: no se envio el libro");
			if(!isset($data['Libro']['cuenta']))
				throw new Exception("Error: no se envio la cuenta");
			if(!isset($data['comprobantes']))
				throw new Exception("Error: no se envió comprobantes");

			#Reacomodar la data con tipo MongoDB
			if(!isset($data['Libro']['cuenta']['_id']))
				throw new Exception("Debug: No se detecto un _id de cuenta");
			$data['Libro']['cuenta']['_id'] = new MongoId($data['Libro']['cuenta']['_id']);
/*			for ($i=0; $i < count($data['comprobantes']); $i++) { 
				$data['comprobantes'][$i]['fec'] = new MongoId($data['comprobantes'][$i]['fec']);
			
				if(!isset($data['comprobantes']['fec']))
					throw new Exception("Error: no se envió el campo fec en el ".$i." comprobante");
				foreach ($data['comprobantes'][$i]['rec_ingreso'] as $key => $recibo) {
					if(!isset($recibo['_id']))
						throw new Exception("Error: no se envió el campo _id en el recibo ".$recibo);
					$recibo['_id']=new MongoId($recibo['_id']);
				}
			
			}

		
			if(!isset($f->request->data['_id'])){
				$data['fecreg'] = new MongoDate();
				$data['autor'] = $f->session->userDBMin;
				$data['estado'] = 'A';
				$model = $f->model("ts/libo")->params(array('data'=>$data))->save("insert")->items;
				
			}else{
				$vari = $f->model("ts/libo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
				if(isnull($vari))
					throw new Exception("Error: no se encontro este id");
				$model = $f->model("ts/libo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			}
			$response['status'] = 'success';
			$response['message'] = 'Fueron realizados los cambios correctamente.';
		}
		catch (Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
*/
		
	function execute_edit(){
		global $f;
		$f->response->view("ts/libo.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/libo.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ts/libo')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('libo');
		$f->response->print("true");
	}
	function execute_reporte(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'A ocurrido un error en la sesion',
			'data'=>array()
		);
		try{
			$items = $f->model("ts/libo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("all")->items;
			//$f->response->view("ts/cjse.report.php",array('sesion'=>$items));			
			$f->response->view("ts/libo.report.php",array('libro'=>$items));
		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
}
?>