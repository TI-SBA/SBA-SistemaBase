<?php
class Controller_ha_hosp extends Controller {
/********************************************
*		Hospitalización - Adicciones		*
********************************************/
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		$params['roles'] = 'paciente';
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['modulo']))
				if($f->request->data['modulo']!='')
					$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['pend'])){
			$params['estado'] = 'P';
			$params['pend'] = true;
		}
		if(isset($f->request->data['alta'])){
			$params['estado'] = 'P';
			$params['alta'] = true;
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ha/hosp")->params($params)->get("lista") );
	}
	
	function execute_get(){
		global $f;
		$items = $f->model("ha/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$ficha_social = $f->model('mh/paci')->params(array('filter'=>array('paciente._id'=>$items['paciente']['_id'])))->get('all')->items;
		if($ficha_social!=null) $ficha_social = $ficha_social[0];
		$items['ficha_social'] = $ficha_social;
		$items['tarifas'] = $f->model('ha/tari')->params(array('filter'=>array('estado'=>'H')))->get('all')->items;
		$items['recibo'] = $f->model('cj/comp')->params(array(
			'modulo'=>'HO',
			'serie'=>'003',
			'tipo'=>'R'
		))->get('num_mod')->items;
		$items['recibo'] = floatval($items['recibo']);
		$items['recibo']++;
		$f->response->json( $items );
	}
	function execute_get_hospi_dia(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data["fecini"]) && isset($data["fecfin"])){
			$fecini = strtotime($data["fecini"].' 00:00:00');
			$fecfin = strtotime($data["fecfin"].' 23:59:59');
			$params['$and'] = array(
				array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
				array('fecreg'=>array('$lte'=>new MongoDate($fecfin)))
				);
			}
		$items = $f->model("ha/hosp")->params($params)->get("hospi_dia")->items;
		$f->response->view("ha/hospi_dia.php",array('hospi'=>$items));
	}
	function execute_get_recidia(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data["fecini"]) && isset($data["fecfin"])){
			$fecini = strtotime($data["fecini"].' 00:00:00');
			$fecfin = strtotime($data["fecfin"].' 23:59:59');
			$params['$and'] = array(
				array('fecpag'=>array('$gte'=>new MongoDate($fecini))),
				array('fecpag'=>array('$lte'=>new MongoDate($fecfin)))
				);
			}
		$items = $f->model("ha/hosp")->params($params)->get("recidia")->items;
		$f->response->view("ha/recibos_dia.php",array('hospi'=>$items));
	}
	function execute_get_hospi(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data["fecini"]) && isset($data["fecfin"])){
			$fecini = strtotime($data["fecini"].' 00:00:00');
			$fecfin = strtotime($data["fecfin"].' 23:59:59');
			$params['$and'] = array(
				array('fecpag'=>array('$gte'=>new MongoDate($fecini))),
				array('fecpag'=>array('$lte'=>new MongoDate($fecfin)))
				);
			}
		$items = $f->model("ha/hosp")->params($params)->get("recidia")->items;
		$f->response->view("ha/reporte.hospi.php",array('hospi'=>$items));
	}

	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(!isset($f->request->data['_id'])){
			//'hist_cli'=>floatval($data['hist_cli']),
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("ha/hosp")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'HA',
				'bandeja'=>'Hospitalizaci&oacute;n',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("ha/hosp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'HA',
				'bandeja'=>'Hospitalizaci&oacute;n',
				'descr'=>'Se actualizó el Paciente <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("ha/hosp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_save_rec(){
		global $f;
		$data = $f->request->data;
		$data['_id'] = new MongoId($data['_id']);
		$data['paciente']['_id'] = new MongoId($data['paciente']['_id']);
		$data['fecini'] = new MongoDate(strtotime($data['fecini']));
		$data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
		//$data['fecalt'] = new MongoDate(strtotime($data['fecalt']));
		$data['fecpag'] = new MongoDate(strtotime($data['fecpag']));
		$caja = $f->model('cj/caja')->params(array('modulo'=>'HA'))->get('mod')->items;
		$caja = array(
			'_id'=>$caja['_id'],
			'nomb'=>$caja['nomb'],
			'local'=>$caja['local']
		);
		$conf = $f->model('cj/conf')->params(array('cod'=>'HO'))->get('cod')->items;
		//print_r($conf);
		
		if(floatval($data['importe']>0)){
			$recibo = array(
				'modulo'=>'AD',
				'fecreal'=>new MongoDate(),
				'fecreg'=>$data['fecpag'],
				'estado'=>'R',
				'hist_cli'=>$data['hist_cli'],
				'periodo'=>date('ym00'),
				'autor'=>$f->session->userDB,
				'cliente'=>$data['paciente'],
				'caja'=>$caja,
				'tipo'=>'R',
				'serie'=>'003',
				'num'=>floatval($data['num']),
				'moneda'=>'S',
				'observ'=>'',
				'total'=>floatval($data['importe']),
				'efectivos'=>array(
					array('moneda'=>'S','monto'=>$data['importe']),
					array('moneda'=>'D','monto'=>0)
				),
				'hospitalizacion'=>array(
					'_id'=>$data['_id'],
					'hist_cli'=>floatval($data['hist_cli']),
					'cant'=>$data['cant'],
					'modalidad'=>$data['modalidad'],
					'tipo_hosp'=>$data['tipo_hosp'],
					'categoria'=>$data['categoria'],
					'fecini'=>$data['fecini'],
					'fecfin'=>$data['fecfin']
				),
				'cuenta'=>$conf['HOSP']
			);
			$recibo = $f->model('cj/comp')->params(array('data'=>$recibo))->save('insert')->items;
			$recibo_ = array(
				'_id'=>$recibo['_id'],
				'tipo'=>$recibo['tipo'],
				'serie'=>$recibo['serie'],
				'num'=>$recibo['num'],
				'fecini'=>$data['fecini'],
				'fecfin'=>$data['fecfin']
			);
			$data['recibo'] = $recibo_;
			//$data['estado'] = 'F';
			$tmp = $data['importe'];
			unset($data['importe']);
			$f->model('ha/hosp')->params(array('_id'=>$data['_id'],'data'=>$data))->save('update');
			$f->model('ha/hosp')->params(array('_id'=>$data['_id'],'data'=>array('$push'=>array('recibos'=>$recibo_))))->save('custom');
			$f->model('ha/hosp')->params(array('_id'=>$data['_id'],'data'=>array('$inc'=>array('importe'=>floatval($tmp)))))->save('custom');
		}
		
		$f->response->json(array('recibo'=>$recibo['_id']));
	}
	function execute_save_alta(){
		global $f;
		$data = $f->request->data;
		$data['_id'] = new MongoId($data['_id']);
		$data['estado'] = 'A';
		$data['fecalt'] = new MongoDate();
		$f->model('ha/hosp')->params(array('_id'=>$data['_id'],'data'=>$data))->save('update');
		$f->response->print('true');
	}
	function execute_delete(){
		global $f;
		$item = $f->model('ha/hosp')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'ho_hospitalizaciones';
		$item['trabajador_delete'] = $f->session->userDBMin;
		$f->datastore->temp_del->insert($item);
		$f->datastore->ho_hospitalizaciones->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ha/hosp.edit");
	}
	function execute_edit_alta(){
		global $f;
		$f->response->view("ha/hosp.alta.edit");
	}
	function execute_string_number(){
		global $f;
		$model = $f->model('ha/hosp')->get('hist_cli_string')->items;
		if($model!=null){
			foreach($model as $i=>$item){
				$set = array(
					'hist_cli'=>floatval($item['hist_cli'])
				);
				$f->model('ha/hosp')->params(array('_id'=>$item['_id'],'data'=>$set))->save('update');
				echo 'HISTORIA CLINICA '.$item['hist_cli'].' REPARADA';
			}
		}
		//echo count($model);
	}
}
?>