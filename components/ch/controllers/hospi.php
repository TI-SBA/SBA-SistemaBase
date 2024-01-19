<?php
class Controller_ch_hospi extends Controller {
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
		$f->response->json( $f->model("ch/hospi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/paho")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(is_null($items)){
			$items = $f->model("ho/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		}
		//var_dump($items);
		$f->response->json($items);
	}
	function execute_get_legacy(){
		global $f;
		$items = $f->model("ho/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_ingreso(){
		global $f;
		$data = $f->request->data;
		$params = array('paciente._id'=>new MongoId($data['_id'])
		);
		$items = $f->model("ch/hospi")->params($params)->get("ingreso")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo_legacy(){
		global $f;
		$items = $f->model("ho/hosp")->params()->get("codigo")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo(){
		global $f;
		$items = $f->model("ch/hospi")->params()->get("codigo")->items;
		$f->response->json( $items );
	}
	
	function execute_save_legacy(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['fing'])){
			$data['fing']=new MongoDate(strtotime($data['fing']));
		}
		if(isset($data['fecini'])){
			$data['fecini']=new MongoDate(strtotime($data['fecini']));
		}
		if(isset($data['paciente']))
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		/*if(isset($data['apoderado'])){
			if(isset($data['apoderado']['_id'])) = new MongoId($data['apoderado']['_id']);
		}*/
		if(!isset($f->request->data['_id'])){
			$social = $f->model('ch/paci')->params(array(
				'_id'=>$data['paciente']['_id']
			))->get('one_entidad')->items;
			if($social==null){
				return $f->response->json(array('error'=>true));
			}else{
				$data['categoria'] = $social['categoria'];
			}
			if(isset($data['his_cli'])){
			$data['his_cli'] = floatval($data['his_cli']);
		}
			$data['hist_cli']= floatval($data['hist_cli']);
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			if(isset($data['paciente'])){
				$data['paciente']['fullname'] = $data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'];
			}
			$model = $f->model("ho/hosp")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MH',
				'bandeja'=>'Hospitalizaciones',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ho/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MH',
				'bandeja'=>'Hospitalizaciones',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ho/hosp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json(array('success'=>true));
	}

	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['fing'])){
			$data['fing']=new MongoDate(strtotime($data['fing']));
		}
		if(isset($data['fecini'])){
			$data['fecini']=new MongoDate(strtotime($data['fecini']));
		}
		if(isset($data['paciente']))
			$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		/*if(isset($data['apoderado'])){
			if(isset($data['apoderado']['_id'])) = new MongoId($data['apoderado']['_id']);
		}*/
		if(!isset($f->request->data['_id'])){
			$social = $f->model('ch/paci')->params(array(
				'_id'=>$data['paciente']['_id']
			))->get('one_entidad')->items;
			if($social==null){
				return $f->response->json(array('error'=>true));
			}else{
				$data['categoria'] = $social['categoria'];
			}
			if(isset($data['his_cli'])){
			$data['his_cli'] = floatval($data['his_cli']);
		}
			$data['hist_cli']= floatval($data['hist_cli']);
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			if(isset($data['paciente'])){
				$data['paciente']['fullname'] = $data['paciente']['nomb'].' '.$data['paciente']['appat'].' '.$data['paciente']['apmat'];
			}
			$model = $f->model("ch/hospi")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MH',
				'bandeja'=>'Hospitalizaciones',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ch/hospi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'MH',
				'bandeja'=>'Hospitalizaciones',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ch/hospi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json(array('success'=>true));
	}


	function execute_edit(){
		global $f;
		$f->response->view("ch/hospi.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ch/hospi.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ch/hospi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('hospi');
		$f->response->print("true");
	}
	function execute_print_legacy(){
		global $f;
		$hospitalizacion = $f->model('ho/hosp')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/hospi.print",array('hospitalizacion'=>$hospitalizacion));

	}

	function execute_print(){
		global $f;
		$hospitalizacion = $f->model('ch/hospi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/hospi.print",array('hospitalizacion'=>$hospitalizacion));
	}

	function execute_agregarfullname_legacy(){
		global $f;
		$model = $f->model('ho/hosp')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ho/hosp")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}

	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ch/hospi')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ch/hospi")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
}
?>