<?php
class Controller_re_posc extends Controller {
	function execute_lista(){
		global $f;
		$model = $f->model("re/posc")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_get_by_caja(){
		global $f;
		$params = array();
		if(isset($f->request->data['caja'])){
			$params['caja._id'] = new MongoId($f->request->data['caja']);
		}
		$model = $f->model('re/posc')->params($params)->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("re/posc")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		/*if(!isset($f->request->data['edit'])){
			$cod = $f->model("cj/comp")->params(array('tipo'=>$model->items['tipo'],'serie'=>$model->items['serie'],'caja'=>$model->items['caja']['_id']))->get("num");
			if($cod->items==null) $cod->items=1;
			else $cod->items = intval($cod->items);
			$model->items['actual'] = $cod->items;
		}*/
		$f->response->json( $model->items );
	}
	function execute_get_caja(){
		global $f;
		$model = $f->model("cj/talo")->params(array("caja"=>new MongoId($f->request->caja)))->get("by_caja");
		if(!is_null($model->items)){
			foreach ($model->items as $i=>$item){
				/*$cod = $f->model("cj/comp")->params(array(
					'tipo'=>$item['tipo'],
					'serie'=>$item['serie'],
					'caja'=>$item['caja']['_id']
				))->get("num");
				if($cod->items==null) $cod->items=$model->items[$i]['actual'];
				else $cod->items = intval($cod->items);
				$model->items[$i]['actual'] = $cod->items;
				*/
			}
		}
		$f->response->json( $model->items );
	}
	
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['caja']['_id'])) $data['caja']['_id'] = new MongoId($data['caja']['_id']);
		if(isset($data['caja']['local']['_id'])) $data['caja']['local']['_id'] = new MongoId($data['caja']['local']['_id']);
		if(isset($data['cbancaria']['_id'])) $data['cbancaria']['_id'] = new MongoId($data['cbancaria']['_id']);
		if(isset($data['cbancaria']['cuenta']['_id'])) $data['cbancaria']['cuenta']['_id'] = new MongoId($data['cbancaria']['cuenta']['_id']);
		if(isset($data['ccontable']['_id'])) $data['ccontable']['_id'] = new MongoId($data['ccontable']['_id']);
		if(isset($data['sdiverso']['_id'])) $data['sdiverso']['_id'] = new MongoId($data['sdiverso']['_id']);
		if(isset($data['sdiverso']['organizacion']['_id'])) $data['sdiverso']['organizacion']['_id'] = new MongoId($data['sdiverso']['organizacion']['_id']);
		if(isset($data['cdiverso']['_id'])) $data['cdiverso']['_id'] = new MongoId($data['cdiverso']['_id']);
		if(isset($data['comision'])) $data['comision'] = floatval($data['comision']);
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		$proveedor = $data['proveedor'];
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			if(!isset($data['estado'])) $data['estado'] = "H";
			$f->model("re/posc")->params(array('data'=>$data))->save("insert");
			
			$f->model('ac/log')->params(array(
				'modulo'=>'RE',
				'bandeja'=>'Configuraci&oacute;n de POS',
				'descr'=>'Se cre&oacute; el POS de <b>'.$proveedor.'</b> con nombre <b>'.$data['nombre'].'</b> para la caja <b>'.$data['caja']['nomb'].'</b> ubicada en <b>'.$data['caja']['local']['direccion'].'</b>'
			))->save('insert');
		}else{
			$f->model("re/posc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			if(!isset($data['estado'])) $data['estado'] = "D";
			$f->model('ac/log')->params(array(
				'modulo'=>'RE',
				'bandeja'=>'Configuraci&oacute;n de POS',
				'descr'=>'Se actualiz&oacute; el POS de <b>'.$proveedor.'</b> con nombre <b>'.$data['nombre'].'</b> para la caja <b>'.$data['caja']['nomb'].'</b> ubicada en <b>'.$data['caja']['local']['direccion'].'</b>'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("re/posc.edit");
	}
	/*function execute_select(){
		global $f;
		$f->response->view("re/talo.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("re/talo.details");
	}*/
}
?>