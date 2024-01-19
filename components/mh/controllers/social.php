<?php
class Controller_mh_social extends Controller {
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
		$f->response->json( $f->model("mh/social")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("mh/social")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if($items!=null){
			$items['paciente'] = $f->model('mh/paci')->params(array('filter'=>array('paciente._id'=>$items['paciente']['_id'])))->get('all')->items[0];
			if(!isset($items['categoria'])){
				$items['categoria'] = $items['paciente']['categoria'];
			}
		}
		$f->response->json( $items );
	}
	function execute_get_reporte(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
				array('fech'=>array('$gte'=>new MongoDate($fecini))),
				array('fech'=>array('$lte'=>new MongoDate($fecfin)))
			);
		}
		$items = $f->model("mh/social")->params($params)->get("all")->items;
		$f->response->view("mh/social.report.php",array('diario'=>$items));
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
			$model = $f->model("mh/social")->params(array('data'=>$data))->save("insert")->items;
			
		}else{
			//Obtengo el ID de la ficha social
			$vari = $f->model("mh/social")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			//traigo la hospitalizacion del paciente.
			$hospi = $f->model("ho/hosp")->params(array("_id"=>new MongoId($vari['paciente']['_id'])))->get("one_entidad")->items;
			//Llamo a todas las hospitalizaciones registradas y tomo el primero del array.
			$rpta = $f->model('ho/hosp')->params(array(
				'filter' => array('paciente._id'=>$hospi['paciente']['_id']),
				'fields' => array(),
				'sort' => array('_id'=>-1)
			))->get('custom_data')->items;
			$rpta[0]["categoria"] = $data['categoria'];

			//Traigo los datos de la ficha frontal
			$paci = $f->model('mh/paci')->params(array('_id'=>new MongoId($vari['paciente']['_id'])))->get('one_entidad')->items;

			$paci['categoria'] = $data['categoria'];
			$his =  $f->model('mh/paci')->params(array('_id'=>new MongoId($vari['paciente']['_id']),'data'=>$paci))->save("update_2")->items;
			$hospi = $f->model("ho/hosp")->params(array('_id'=>new MongoId($vari['paciente']['_id']),'data'=>$rpta[0]))->save("update")->items;
			$model = $f->model("mh/social")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			
		}
		$f->response->json($model);
		
		


	}
	
	function execute_edit(){
		global $f;
		$f->response->view("mh/social.edit");
	}
	function execute_cate(){
		global $f;
		$f->response->view('mh/social.cate');
	}
	function execute_details(){
		global $f;
		$f->response->view("mh/social.details");
	}
	function execute_delete(){
		global $f;
		$f->model('mh/social')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('social');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$social = $f->model('mh/social')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$social['paciente'] = $f->model('mh/paci')->params(array('_id'=>$social['paciente']['_id']))->get('one_entidad')->items;
		//$social['paciente'] = $f->model('mg/entidad')->params(array('_id'=>$social['paciente']['_id']))->get('one')->items;
		//print_r($social);die();
		$f->response->view("mh/social.print",array('social'=>$social));

	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('mh/social')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("mh/social")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
}
?>