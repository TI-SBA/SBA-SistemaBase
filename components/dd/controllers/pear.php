<?php
class Controller_dd_pear extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("dd/pear")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("dd/pear")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("dd/pear")->params(array('data'=>$data))->save("insert")->items;
			
		}else{
			$vari = $f->model("dd/pear")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("dd/pear")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$pedido = $f->model('dd/pedi')->params(array('filter'=>array('_id'=>$data['_id'])))->get('all')->items;
		if($pedido==null){
			$pedido = array(
				'nomb'=>$data['docu'],
				'dire'=>$data['dire'],
				'ofic'=>$data['ofic'],
				'movi'=>'0',
				'num'=>$data['nsol'],
				'disol'=>$data['disol'],
				'ofsol'=>$data['ofsol'],
				'asun'=>$data['asun'],
				'fecreg'=>new MongoDate,
				'fecmod'=> new MongoDate,
				'trabajador'=> $f->session->userDBMin,
				'autor'=> $f->session->userDBMin,
				'estado'=>'H'
			);

			$f->model('dd/pedi')->params(array('data'=>$pedido))->save('insert');
		}


		$f->response->json($model);
	}
	function execute_get_nro(){
		global $f;
		$items = $f->model("dd/pear")->params(array())->get("nro")->items;
		$f->response->json( $items );
	}
	function execute_print(){
		global $f;
		$pear = $f->model('dd/pear')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("dd/pear.print",array('pear'=>$pear));
	}
	function execute_edit(){
		global $f;
		$f->response->view("dd/pear.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("dd/pear.details");
	}
	function execute_delete(){
		global $f;
		$f->model('dd/pear')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('pear');
		$f->response->print("true");
	}
}
?>