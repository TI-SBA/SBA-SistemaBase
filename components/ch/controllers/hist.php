<?php
class Controller_ch_hist extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/hist")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/hist")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$items['paciente'] = $f->model('ch/paci')->params(array('filter'=>array('paciente.nomb'=>$items['paciente']['paciente']['nomb'])))->get('all')->items[0];
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		

		if(isset($data['paciente'])){
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
			$data['paciente']['paciente']['_id'] = new MongoId($data['paciente']['paciente']['_id']);
		}
		for($i = 0;$i<count($data["evoluciones"]);$i++){
			if(isset($data['evoluciones'][$i]['fec'])){
				$data['evoluciones'][$i]['fec']=new MongoDate(strtotime($data['evoluciones'][$i]['fec']));
			}
			if(isset($data['evoluciones'][$i]['user']["_id"])){
				$data['evoluciones'][$i]['user']["_id"]=new MongoId($data['evoluciones'][$i]['user']["_id"]);
			}


		}
		if(isset($data['clin'])){
			$data['clin']= floatval($data['clin']);
		}
		if(isset($data['paciente']['his_cli'])){
			$data['paciente']['his_cli'] = floatval($data['paciente']['his_cli']);
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("ch/hist")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ch/hist")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ch/hist")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	
	function execute_edit(){
		global $f;
		$f->response->view("ch/hist.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ch/hist.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ch/hist')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('hist');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$hist = $f->model('ch/hist')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/hist.print",array('hist'=>$hist));

	}
	function execute_importar(){
		global $f;
		set_time_limit(1);
		$conexión = new MongoClient("mongodb://localhost:27017/");
		$bd = $conexión->beneficencia;
		$page = 1;
		$page_rows = 100;
		$col = $bd->ch_pacientes;
		$items = $col->find(array('check1'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
		foreach($items as $key => $item){
			print_r($item);
			$data = array(
		    	'paciente'=>$item['paciente'],
		    	'clin'=>$item['his_cli'],
				"evoluciones"=>"",
				"fecreg"=>$item['fe_regi']
		
		    );
		    $f->datastore->ch_historias_clinicas->insert($data);
		    $bd->ch_pacientes->update(array('_id'=>$item['_id']),array('$set'=>array('check1'=>true)));
		}
		
	}
}
?>