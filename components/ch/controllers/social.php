<?php
class Controller_ch_social extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/social")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/social")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if($items!=null){
			$items['paciente'] = $f->model('ch/paci')->params(array('filter'=>array('paciente._id'=>$items['paciente']['_id'])))->get('all')->items[0];
		}
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['paciente'])){
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
			$paciente = $f->model('mg/entidad')->params(array('_id'=>$data['paciente']['_id']))->get('one')->items;
			if(isset($data['paciente']['roles'])){
				$data['paciente']['roles']['paciente'] = $paciente['roles']['paciente'];
			}else{
				$data['paciente']['roles']=array(
					'paciente'=>$paciente['roles']['paciente']
				);
			}
		}

		if(isset($data['apoderado']))
			$data['apoderado']['_id'] = new MongoId($data['apoderado']['_id']);
		if(isset($data['fena'])){
			$data['fena']=new MongoDate(strtotime($data['fena']));
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			if(isset($data['paciente'])){
				$data['paciente']['fullname'] = $data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'];
			}
			$model = $f->model("ch/social")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MH',
				'bandeja'=>'Ficha Social',
				'descr'=>'Se creó la Ficha Social <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ch/social")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MH',
				'bandeja'=>'Ficha Social',
				'descr'=>'Se actualizó la Ficha Social <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ch/social")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);


	}
	
	function execute_edit(){
		global $f;
		$f->response->view("ch/social.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ch/social.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ch/social')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('social');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$social = $f->model('ch/social')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$social['paciente'] = $f->model('ch/paci')->params(array('_id'=>$social['paciente']['_id']))->get('one_entidad')->items;
		//$social['paciente'] = $f->model('mg/entidad')->params(array('_id'=>$social['paciente']['_id']))->get('one')->items;
		//print_r($social);die();
		$f->response->view("ch/social.print",array('social'=>$social));

	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ch/social')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ch/social")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
}
?>