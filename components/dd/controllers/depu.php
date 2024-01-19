<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Controller_dd_depu extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("dd/depu")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("dd/depu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_depuracion(){
		global $f;
		$data = $f->request->data;
		$params = array();
		$items = $f->model("dd/depu")->params($params)->get("depuracion")->items;
		$f->response->view("dd/depu.print.php",array('depu'=>$items));
		print_r($items);
		
		die();
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['femi'])){
			$data['femi']=new MongoDate(strtotime($data['femi']));
		}
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			//$data['estado'] = 'H';
			$model = $f->model("dd/depu")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("dd/depu")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("dd/depu")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("dd/depu.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("dd/depu.details");
	}
	function execute_delete(){
		global $f;
		$f->model('dd/depu')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('depu');
		$f->response->print("true");
	}
	function execute_importar(){
		global $f;
		set_time_limit(1);
		$conexión = new MongoClient("mongodb://localhost:27017/");
		$bd = $conexión->dbdigitalizacion;
		$page = 1;
		$page_rows = 100;
		$col = $bd->tbl_Transfer;
		$items = $col->find(array('check1'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
		foreach($items as $key => $item){
			print_r($item);
			$data = array(
		    	'old_id'=>$item['_id'],
		    	'cant'=>$item['cantidad'],
				'year'=>array(
					array('fec1'=>$item['fechasExt'][0]['Year']),
					array('fec2'=>$item['fechasExt'][0]['Year'])
				),
					//'tituloRegistro'=>$item['titu'],
				//'numeroEntrega'=>$item['nro'],
		    	//'observacion'=>$item['obse'],
		    	//'tituloRegistro'=>$item['titu'],
		    	//'kind_id'=>$item['id_tipo_']
		    	//'ubicacion'=>$item['ubic'],
		    	//'nombreFunc'=>$item['remi'],
		    	//'program_id'=>$item['id_dire'],
		    	//'kindSerie_id'=>$item['id_tise'], 
				"titu"=>$item['tituloRegistro'],
				'nro'=>$item["numeroEntrega"],
				"obse"=>$item['observacion'],
		    	'titu'=>$item["tituloRegistro"],
		    	'ubic'=>$item['ubicacion'],
				"remi"=>$item['nombreFunc'],
				'id_dire'=>$item['program_id'],
		    	'id_tise'=>$item['kindSerie_id'],
		    	'id_tipo_'=>$item['kind_id']
		    	

		    );
		    $f->datastore->dd_recepcion_documentaria->insert($data);
		    $bd->tbl_Transfer->update(array('_id'=>$item['_id']),array('$set'=>array('check1'=>true)));
		}
		//print_r($data);
		//echo "Importado";
	}
}
?>