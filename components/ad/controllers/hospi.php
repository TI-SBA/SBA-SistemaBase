<?php
class Controller_ad_hospi extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ad/hospi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ha/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_ingreso(){
		
		global $f;
		$data = $f->request->data;
		$params = array('paciente._id'=>new MongoId($data['_id'])
		);
		$items = $f->model("ad/hospi")->params($params)->get("ingreso")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo(){
		global $f;
		$items = $f->model("ha/hosp")->params()->get("codigo")->items;
		$f->response->json( $items );
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
			$social = $f->model('ad/paci')->params(array(
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
			$model = $f->model("ha/hosp")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AD',
				'bandeja'=>'Hospitalizaciones',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ha/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'AD',
				'bandeja'=>'Hospitalizaciones',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ha/hosp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json(array('success'=>true));
	}
	function execute_edit(){
		global $f;
		$f->response->view("ad/hospi.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ad/hospi.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ad/hospi')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('hospi');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$hospitalizacion = $f->model('ha/hosp')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ad/hospi.print",array('hospitalizacion'=>$hospitalizacion));

	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ha/hosp')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ha/hosp")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
}
?>