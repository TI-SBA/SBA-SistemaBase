<?php
class Controller_ts_reca extends Controller {
	function execute_lista(){
		global $f;
		
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));

		$params['oficina'] = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		
		$f->response->json($f->model("ts/reca")->params($params)->get('lista_'));
	}
	function execute_get_reporte(){
		global $f;
		$data = $f->request->data;
		$params = array();
		
		if($data['oficina']!='Todos'){
			if(isset($data['mes']) && isset($data['ano']) && isset($data['oficina'])){
				$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
				$day_fin = date('t',$fecini);
				$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
				$params['$and'] = array(
					array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
					array('fecreg'=>array('$lte'=>new MongoDate($fecfin))),
					array('cargo.oficina.nomb'=>array('$eq'=>$data['oficina']))
				);
			}
		}else{
			if(isset($data['mes']) && isset($data['ano'])){
				$fecini = strtotime($data['ano'].'-'.$data['mes'].'-01 00:00:00');
				$day_fin = date('t',$fecini);
				$fecfin = strtotime($data['ano'].'-'.$data['mes'].'-'.$day_fin.' 23:59:59');
				$params['$and'] = array(
					array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
					array('fecreg'=>array('$lte'=>new MongoDate($fecfin)))
				);
			}
		}
		
		$items = $f->model("ts/reca")->params($params)->get("reporte")->items;
			
			$f->response->json( $items );
		}
	function execute_get(){
		global $f;
		$items = $f->model("ts/reca")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_num(){
		global $f;
		$filter= array(
			'tipo'=>'P'
		);
		$items = $f->model("ts/reca")->params(array(
			'filter'=>$filter,
			'fields'=>array(
				'num'=>true
			),
			'sort'=>array(
				'num'=>-1
			)
			))->get("all")->items;
		$f->response->json( $items );
	}
	function execute_get_num_d(){
		global $f;
		$filter= array(
			'tipo'=>'D'
		);
		$items = $f->model("ts/reca")->params(array(
			'filter'=>$filter,
			'fields'=>array(
				'num'=>true
			),
			'sort'=>array(
				'num'=>-1
			)
			))->get("all")->items;
		$f->response->json( $items );
	}
	function execute_get_all(){
		global $f;
		$items = $items = $f->model('ts/reca')->params(array())->get('all')->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		$prueba = $f->session->userDBMin;
		if(isset($data['entidad']['_id']))
			$data['entidad']['_id'] = new MongoId($data['entidad']['_id']);
		if(isset($data['fec']))
			$data['fec'] = new MongoDate(strtotime($data['fec']));
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['autor']['_id'] = new MongoId($data['autor']['_id']);
			$data['estado'] = 'R';
			$trab = $data['autor'];
			$trab['_id'] = new MongoId($trab['_id']);
			$user = $f->model('mg/entidad')->params(array('_id'=>new MongoId($trab['_id'])))->get('one')->items;
			$data['cargo']['oficina'] = $user['roles']['trabajador']['oficina'];
			$data['autor']['docident'] = $user['docident'];
			$data['monto']=floatval($data['monto']);
			$model = $f->model("ts/reca")->params(array('data'=>$data))->save("insert")->items;	
		}else{
			$vari = $f->model("ts/reca")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$model = $f->model("ts/reca")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_prov(){
		global $f;
		$f->response->view("ts/reca.prov");
	}
	function execute_defi(){
		global $f;
		$f->response->view("ts/reca.def");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/reca.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/reca.details");
	}
	function execute_delete(){
		global $f;
		$f->model('ts/reca')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('reca');
		$f->response->print("true");
	}
	function execute_print(){
		global $f;
		$recibo = $f->model("ts/reca")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->view("ts/reca.print",array("recibo"=>$recibo));
	}
}
?>