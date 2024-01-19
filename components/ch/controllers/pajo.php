<?php
class Controller_ch_pajo extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['modulo']))
			$params['modulo'] = "AD";
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/pajo")->params($params)->get("lista") );
	}
	function execute_lista_(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$lista=$f->model("ch/pajo")->params($params)->get("lista")->items;
		$list_r=array();
		foreach($lista as $pajo)
		{
			if($pajo['estado']=='H')
			{
				array_push($list_r,$pajo);
			}
		}
		$f->response->json(array('items'=>$list_r));
	}
	function execute_get_reporte_alta(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
				array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
				array('fecreg'=>array('$lte'=>new MongoDate($fecfin)))
			);
		}
		$items = $f->model("ch/pajo")->params($params)->get("alta")->items;
		$f->response->view("ch/pajo.alta.report.php",array('paciente'=>$items));
	}
	function execute_all(){
		global $f;
		$model = $f->model('ch/pajo')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/pajo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_ingreso(){
		
		global $f;
		$data = $f->request->data;
		$params = array('paciente._id'=>new MongoId($data['_id'])
		);
		$items = $f->model("ch/pajo")->params($params)->get("ingreso")->items;
		$f->response->json( $items );
	}
	function execute_get_codigo(){
		global $f;
		$items = $f->model("ch/pajo")->params()->get("codigo")->items;
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
		if(isset($data['fec_inicio'])){
			$data['fec_inicio']=new MongoDate(strtotime($data['fec_inicio']));
		}
		if(isset($data['fec_fin'])){
			$data['fec_fin']=new MongoDate(strtotime($data['fec_fin']));
		}
		if(isset($data['fec_tras'])){
			$data['fec_tras']=new MongoDate(strtotime($data['fec_tras']));
		}
		if(isset($data['fec_alta'])){
			$data['fec_alta']=new MongoDate(strtotime($data['fec_alta']));
		}
		if(isset($data['monto'])){
				$data['monto'] = floatval($data['monto']);
		}
		if(isset($data['hist_cli'])){
			$data['hist_cli']= floatval($data['hist_cli']);
		}
		if(isset($data['paciente']['paciente']['_id'])){
			$data['paciente']['_id']=new MongoId($data['paciente']['paciente']['_id']);
			$data['paciente']['paciente']['_id']=new MongoId($data['paciente']['paciente']['_id']);
		}
		else $data['paciente']['_id']=new MongoId($data['paciente']['_id']);

		if(!isset($f->request->data['_id'])){
			//$social = $f->model('ch/paci')->params(array(
			//	'_id'=>$data['paciente']['_id']
			//))->get('one_entidad')->items;
			$social = $f->model('ch/hospi')->params(array(
				'_id'=>($data['paciente']['_id'])
			))->get('one_entidad')->items;
			
			if($social==null){
				return $f->response->json(array('error'=>true,'data'=>$data));
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
			$model = $f->model("ch/pajo")->params(array('data'=>$data))->save("insert")->items;
			
		}else{
			$vari = $f->model("ch/pajo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("ch/pajo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json(array('success'=>true));
	}
	function execute_edit(){
		global $f;
		$f->response->view("ch/conpa.edit");
	}
	function execute_move(){
		global $f;
		$f->response->view("ch/conpa.move");
	}
	function execute_alta(){
		global $f;
		$f->response->view("ch/pajo.alta");
	}
	function execute_details(){
		global $f;
		$f->response->view("ch/pajo.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ch/pajo')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('pajo');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$paciente = $f->model('ch/pajo')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('alta')->items;
		//print_r($paciente);
		//die();
		$f->response->view("ch/pajo.alta.print",array('paciente'=>$paciente));


	}
	function execute_agregarfullname(){
		global $f;
		$model = $f->model('ch/pajo')->params(array('limit'=>1,'filter'=>array('paciente.fullname'=>array('$exists'=>false))))->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				$fullname = $item['paciente']['nomb'].' '.$item['paciente']['appat'].' '.$paciente['apmat'];
				$f->model("ch/pajo")->params(array('_id'=>$item['_id'],'data'=>array('paciente.fullname'=>$fullname)))->save("update")->items;
			}
			echo 'true';
		}
	}
}
?>