<?php
class Controller_in_movi extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("in/movi.main");
	}
	function execute_get_cont(){
		global $f;
		$f->response->json($f->model('in/cont')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items);
	}
	function execute_get_tipo_sub(){
		global $f;
		$f->response->json(array(
			'tipo'=>$f->model('in/tipo')->get('all')->items,
			'sublocal'=>$f->model('in/subl')->get('all')->items
		));
	}
	function execute_get_all_cont(){
		global $f;
		if($f->request->data['mostrar']=='1'){
			$contratos = $f->model('in/cont')->params(array(
				'inmueble'=>new MongoId($f->request->data['_id'])
			))->get('all')->items;
		}else{
			$contratos = $f->model('in/cont')->params(array(
				'inmueble'=>new MongoId($f->request->data['_id']),
				'estado'=>'P'
			))->get('all')->items;
		}
		if(isset($contratos)){
			foreach($contratos as $i=>$cont){
				if(isset($cont['titular']['_id']))
					$contratos[$i]['titular'] = $f->model('mg/entidad')->params(array('_id'=>$cont['titular']['_id']))->get('one')->items;
				$contratos[$i]['cobros'] = $f->model('cj/cuen')->params(array('contrato'=>$cont['_id']))->get('all')->items;
				if(isset($contratos[$i]['cobros'])){
					foreach ($contratos[$i]['cobros'] as $j => $cuen) {
						if(isset($cuen['comprobantes'])){
							foreach ($cuen['comprobantes'] as $k => $comp) {
								$comprobante = $f->model('cj/comp')->params(array('_id'=>$comp))->get('one')->items;
								if(is_null($comprobante)) $comprobante = $f->model('cj/ecom')->params(array('_id'=>$comp))->get('one')->items;
								//$contratos[$i]['cobros'][$j]['comprobantes'][$k] = $f->model('cj/comp')->params(array('_id'=>$comp))->get('one')->items;
								$contratos[$i]['cobros'][$j]['comprobantes'][$k] = $comprobante;
							}
						}
					}
				}
			}
		}
		$actas = $f->model('in/acta')->params(array('filter'=>array(
			'inmueble._id'=>new MongoId($f->request->data['_id'])
		)))->get('all')->items;
		$f->response->json(array(
			'contratos'=>$contratos,
			'actas'=>$actas
		));
	}
	function execute_lista_cont(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("in/cont")->params($params)->get("lista") );
	}
	function execute_get_by_titu(){
		global $f;
		$model = $f->model('in/cont')->params(array('titular'=>new MongoId($f->request->data['_id'])))->get('all')->items;
		$inmuebles = null;
		if($model!=null)
		{
			$inmuebles = array();
			foreach ($model as $i=>$item)
			{
				if(!isset($inmuebles[$item['inmueble']['_id']->{'$id'}]))
				{
					$inmuebles[$item['inmueble']['_id']->{'$id'}]['inmueble'] = $item['inmueble'];
				}
			}
		}
		$f->response->json($inmuebles);
	}
	function execute_get_var_mes(){
		global $f;
		$tc;
		$igv;
		$vars = array();
		$varss = $f->model("mg/vari")->params(array("fields"=>array(
			'cod'=>true,
			'nomb'=>true,
			'valor'=>true
		)))->get("all");
		foreach ($varss->items as $item){
			$vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
			if($item['cod']=='TC')
				$tc = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
		}
		$cajas = array();
		if(isset($f->session->enti['roles']['cajero'])){
			foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja){
				$cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
			}
		}
		$rpta = array(
			'cajas'=>$cajas,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items,
			'tc'=>$tc,
			'vars'=>$vars,
			'ctban'=>$f->model("ts/ctban")->get("all")->items
		);
		if(isset($f->request->data['dia_ini'])){
			$rpta['calf'] = $f->model('in/calp')->params(array(
				'mes'=>date('n'),
				'ano'=>date('Y'),
				'tipo'=>$f->request->data['dia_ini']
			))->get('periodo')->items;
		}
		if(isset($f->request->data['tipo'])){
			$tipo = $f->model('in/tipo')->params(array('_id'=>new MongoId($f->request->data['tipo'])))->get('one')->items;
			$rpta['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>$tipo['cuenta']['_id']))->get('one')->items;
		}
		$f->response->json($rpta);
	}
	function execute_get_var_cobro(){
		global $f;
		$tc;
		$igv;
		$vars = array();
		$varss = $f->model("mg/vari")->params(array("fields"=>array(
			'cod'=>true,
			'nomb'=>true,
			'valor'=>true
		)))->get("all");
		foreach ($varss->items as $item){
			$vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
			if($item['cod']=='TC')
				$tc = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
		}
		$cajas = array();
		if(isset($f->session->enti['roles']['cajero'])){
			foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja){
				$cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
			}
		}
		$f->response->json(array(
			'cajas'=>$cajas,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items,
			'tc'=>$tc,
			'vars'=>$vars,
			'ctban'=>$f->model("ts/ctban")->get("all")->items
		));
	}
	function execute_get_garantias(){
		global $f;
		$f->response->json($f->model('in/cont')->params(array(
			'titular'=>new MongoId($f->request->data['titular']),
			'inmueble'=>new MongoId($f->request->data['inmueble']),
			'garantias'=>true,
			'fields'=>array('garantias'=>true)
		))->get('all')->items);
	}
	function execute_all_contratos(){
		global $f;
		$data = $f->request->data;
		$filter = array();
		if(isset($data['titular'])) $filter['titular'] = new MongoId($data['titular']);
		if(isset($data['inmueble'])) $filter['inmueble'] = new MongoId($data['inmueble']);
		$items = $f->model("in/cont")->params($filter)->get("all")->items;
		$f->response->json( $items );
	}
	function execute_save_cont(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['inmueble']))
			$data['inmueble']['_id'] = new MongoId($data['inmueble']['_id']);
		if(isset($data['inmueble']['sublocal']))
			$data['inmueble']['sublocal']['_id'] = new MongoId($data['inmueble']['sublocal']['_id']);
		if(isset($data['inmueble']['tipo']))
			$data['inmueble']['tipo']['_id'] = new MongoId($data['inmueble']['tipo']['_id']);
		if(isset($data['titular']))
			$data['titular']['_id'] = new MongoId($data['titular']['_id']);
		if(isset($data['aval']))
			$data['aval']['_id'] = new MongoId($data['aval']['_id']);
		if(isset($data['motivo']))
			$data['motivo']['_id'] = new MongoId($data['motivo']['_id']);
		if(isset($data['representantes'])){
			foreach ($data['representantes'] as $i=>$item) {
				$data['representantes'][$i]['_id'] = new MongoId($item['_id']);
			}
		}else{
			$data['representantes'] = array();
		}
		if(isset($data['garantias'])){
			foreach ($data['garantias'] as $i=>$item) {
				if($item['fec']!='')
					$data['garantias'][$i]['fec'] = new MongoDate(strtotime($item['fec']));
				if($item['dev_fec']!='')
					$data['garantias'][$i]['dev_fec'] = new MongoDate(strtotime($item['dev_fec']));
				if(isset($item['traslados'])){
					foreach ($item['traslados'] as $k=>$tras) {
						$data['garantias'][$i]['traslados'][$k]['contrato_anterior'] = new MongoId($tras['contrato_anterior']);
					}
				}
			}
		}else{
			$data['garantias'] = array();
		}
		if(isset($data['fecdes']))
			$data['fecdes'] = new MongoDate(strtotime($data['fecdes']));
		if(isset($data['fecini']))
			$data['fecini'] = new MongoDate(strtotime($data['fecini']));
		if(isset($data['fecfin']))
			$data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
		if(isset($data['pagos'])){
			foreach ($data['pagos'] as $i=>$item) {
				if(isset($item['comprobante'])){
					if(isset($item['comprobante']['_id'])) $data['pagos'][$i]['comprobante']['_id'] = new MongoId($item['comprobante']['_id']);
					if(isset($item['comprobante']['num'])) $data['pagos'][$i]['comprobante']['num'] = intval($item['comprobante']['num']);
					if(isset($item['detalle']['alquiler'])) $data['pagos'][$i]['detalle']['alquiler'] = floatval($item['detalle']['alquiler']);
					if(isset($item['detalle']['igv'])) $data['pagos'][$i]['detalle']['igv'] = floatval($item['detalle']['igv']);
					if(isset($item['detalle']['moras'])) $data['pagos'][$i]['detalle']['moras'] = floatval($item['detalle']['moras']);
					if(isset($item['item_c'])) $data['pagos'][$i]['item_c'] = intval($item['item_c']);
				}
				if(isset($item['historico'])){
					foreach ($item['historico'] as $k => $hist) {
						if(isset($hist['fec'])) $data['pagos'][$i]['historico'][$k]['fec'] = new MongoDate(strtotime($hist['fec']));
						if(isset($hist['num'])) $data['pagos'][$i]['historico'][$k]['num'] = intval($hist['num']);
						if(isset($hist['total'])) $data['pagos'][$i]['historico'][$k]['total'] = floatval($hist['total']);
					}
				}
				if(isset($item['comprobantes'])){
					if(isset($item['item_c'])) $data['pagos'][$i]['item_c'] = intval($item['item_c']);
					if(isset($item['total'])) $data['pagos'][$i]['total'] = floatval($item['total']);
					foreach ($item['comprobantes'] as $k => $comp) {
						if(isset($comp['_id'])) $data['pagos'][$i]['comprobantes'][$k]['_id'] = new MongoId($comp['_id']);
						if(isset($comp['num'])) $data['pagos'][$i]['comprobantes'][$k]['num'] = intval($comp['num']);
						if(isset($comp['detalle']['alquiler'])) $data['pagos'][$i]['comprobantes'][$k]['detalle']['alquiler'] = floatval($comp['detalle']['alquiler']);
						if(isset($comp['detalle']['igv'])) $data['pagos'][$i]['comprobantes'][$k]['detalle']['igv'] = floatval($comp['detalle']['igv']);
						if(isset($comp['detalle']['moras'])) $data['pagos'][$i]['comprobantes'][$k]['detalle']['moras'] = floatval($comp['detalle']['moras']);
					}
				}
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$model = $f->model("in/cont")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Contrato de Inmueble',
				'descr'=>'Se creó el Contrato para el Inmueble <b>'.$data['inmueble']['direccion'].'</b>.'
			))->save('insert');
			$message="Insert success";
			$data['message_controller']=$message;
			$f->response->json($data);
		}else{
			$vari = $f->model("in/cont")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Contrato de Inmueble',
				'descr'=>'Se actualizó el Contrato para el Inmueble <b>'.$vari['inmueble']['direccion'].'</b>.'
			))->save('insert');
			$model = $f->model("in/cont")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$items = $model->items;
			$message="Update success";
			$model = $f->model("in/cont")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			$items['message_controller']=$message;
			$f->response->json($items);
		}
	}
	function execute_save_trans(){
		global $f;
		$data = $f->request->data;
		$old = $f->model("in/cont")->params(array("_id"=>new MongoId($data['origen'])))->get("one")->items;
		$new = $f->model("in/cont")->params(array("_id"=>new MongoId($data['destino'])))->get("one")->items;
		$index = -1;
		$garantias = array();
		foreach ($old['garantias'] as $key => $value) {
			if($value['tipo']==$data['tipo']&&$value['num']==$data['num']){
				$index = $key;
				$new_garantia = $old['garantias'][$index];
				if(!isset($new_garantia['historia']))
					$new_garantia['historia'] = array();
				$new_garantia['historia'][] = $old['_id'];
			}else{
				$garantias[] = $value;
			}
		}
		if($index!=-1){
			$f->model('in/cont')->params(array(
				'_id'=>$new['_id'],
				'data'=>array(
					'$push'=>array(
						'garantias'=>$new_garantia
					)
				)
			))->save('custom');
			$f->model('in/cont')->params(array(
				'_id'=>$old['_id'],
				'data'=>array(
					'$set'=>array(
						'garantias'=>$garantias
					)
				)
			))->save('custom');
		}
		$f->response->print('true');
	}
	function execute_delete_contrato(){
		global $f;
		$item = $f->model('in/cont')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'in_contratos';
		$item['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($item);
		$f->datastore->in_contratos->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_edit_cont(){
		global $f;
		$f->response->view("in/movi.cont.edit");
	}
	function execute_letra(){
		global $f;
		$f->response->view("in/movi.cont.letra");
	}
	function execute_garantia(){
		global $f;
		$f->response->view("in/movi.cont.garantia");
	}
	function execute_edit_oper(){
		global $f;
		$f->response->view("in/movi.oper.edit");
	}
	function execute_cuenta(){
		global $f;
		$f->response->view("in/movi.cuenta");
	}
	function execute_comp_mes(){
		global $f;
		$f->response->view("in/movi.comp.mes");
	}
	function execute_comp_cobro(){
		global $f;
		$f->response->view("in/movi.comp.cobro");
	}
	function execute_comp_compen(){
		global $f;
		$f->response->view("in/movi.comp.compen");
	}
	function execute_print_garantias(){
		global $f;
		$f->response->view("in/repo.garantias.xls",array(
			'data'=>$f->model('in/cont')->params(array(
				'titular'=>new MongoId($f->request->data['titular']),
				'inmueble'=>new MongoId($f->request->data['inmueble']),
				'garantias'=>true,
				'fields'=>array('garantias'=>true)
			))->get('all')->items,
			'titular'=>$f->model('mg/entidad')->params(array('_id'=>new MongoId($f->request->data['titular'])))->get('one')->items,
			'inmueble'=>$f->model('in/inmu')->params(array('_id'=>new MongoId($f->request->data['inmueble'])))->get('one')->items,
			'tc'=>$f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items
		));
	}
	function execute_get_mora(){
		global $f;
		$params = array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano'],
			'fecini'=>$f->request->data['fecini']
		);
		$model = $f->model('in/cont')->params($params)->get('mora')->items;
		$f->response->json($model);
	}
	function execute_detalle_contrato(){
		global $f;
		#Se recibe mes y año, tambien se recibe la fecha inicial
		$data=$f->request->data;
		//POR AHORA SOLO CONRATOS
		/*
		# FILTRAR COMPROBANTES MANUALES SIMPLES EMITIDOS POR INMUEBLES Y PLAYAS
		$filter = array(
			'modulo'=>'IN',
			"playa" => array('$exists'=>false),
			"alquiler"=> array('$exists'=>true),
			"estado"=> 'R',
			"sin_pago" => array('$ne'=>true)
		);
		$fields = array(
			'contrato' => 1,
			'parcial' => 1,
			"acta_conciliacion"=>1,
		);

		$comp_simple = $f->model("cj/comp")->params(array("filter"=>$filter,'fields'=>$fields))->get("all")->items;

		# FILTRAR COMPROBANTES MANUALES UNIDOS EMITIDOS POR INMUEBLES Y PLAYAS
		$filter = array(
			'modulo'=>'IN',
			"playa" => array('$exists'=>false),
			"combinar_alq" => array('$exists'=>true),
			"estado"=> 'R',
			"sin_pago" => array('$ne'=>true)
		);
		$fields = array(
			'items.contrato' => 1,
			"items.acta_conciliacion"=>1,
		);

		$comp_complejo = $f->model("cj/comp")->params(array(
			"filter"=>$filter,
			"sort"=>array('_id'=>-1),
			'fields'=>$fields
		))->get("all")->items;

		if (!is_null($comp_simple) && !is_null($comp_complejo)) {
			$comp=array_merge_recursive($comp_simple,$comp_complejo);
		} else if (!is_null($comp_simple) && is_null($comp_complejo)) {
			$comp=$comp_simple;
		} else if (is_null($comp_simple) && !is_null($comp_complejo)) {
			$comp=$comp_complejo;
		} else die();
*/
		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR INMUEBLES Y PLAYAS
		$inm=array('$in' => array('pago_meses','pago_parcial','pago_acta'));
		#Alquileres utiliza la serie 001
		$serie=array('$in' => array('B001','F001','B004','F004'));
		$efilter = array(
			//'items.conceptos.alquiler.contrato' => $contrato['_id'],
			'estado' => array('$in' => array('FI','ES')),
			'items.tipo' => $inm,
			'serie' => $serie,
			'tipo' => array('$in' => array('F','B')),
		);
		$efields['items.conceptos.alquiler']=1;
		$efields['items.conceptos.acta_conciliacion']=1;
		$efields['items.tipo']=1;
		$ecom = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			'sort'=>array('serie'=>-1,'numero'=>1),
			'fields'=>$efields,
		))->get("all")->items;

		$nulos=[];
		$vacios=[];
		$multiples=[];

/*		foreach ($comp as $cm => $manual) {
			if(isset($manual['contrato'])){
					if(isset($manual['parcial']) && $manual['parcial']==1) $params=array('pagos.comprobantes._id'=>$manual['_id']);
					else $params=array('pagos.comprobante._id'=>$manual['_id']);
					$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
					if(is_null($cuenta)) $nulo[]=$manual;
					else if($cuenta==0) {
						$params=array('pagos.comprobantes._id'=>$manual['_id']);
						$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
						if($cuenta==0) $vacios[]=$manual;
					}
					else if($cuenta>=2) {
						$manual['contratos'] = $f->model('in/cont')->params(array('filter'=>$params,'fields'=>array('inmueble' => 1, 'pagos'=>1 )))->get('all')->items;
						$multiples[] = $manual;
					}
			}
			if(isset($manual['acta_conciliacion'])){
					$params=array('items.comprobante._id'=>$manual['_id']);
					$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
					if(is_null($cuenta)) $nulo[]=$manual;
					else if($cuenta==0) $vacios[]=$manual;
					else if($cuenta>=2) {
						$manual['acta_conciliacion'] = $f->model('in/cont')->params(array('filter'=>$params,'fields'=>array('inmueble' => 1, 'pagos'=>1 )))->get('all')->items;
						$multiples[] = $manual;
					}
			}
		}
		*/
		foreach ($ecom as $ecm => $elect) {
			if(isset($elect['items'])){
				foreach ($elect['items'] as $i => $item) {
					if(isset($item['conceptos'])){
						foreach ($item['conceptos'] as $c => $concepto) {
							if(isset($concepto['alquiler']['contrato'])){
								if($item['tipo']=="pago_meses") 		$params=array('pagos.comprobante._id'=>$elect['_id']);
								else if($item['tipo']=="pago_parcial")  	$params=array('pagos.comprobantes._id'=>$elect['_id']);
								$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
								if(is_null($cuenta)) $nulo[]=$elect;
								else if($cuenta==0) {
									$params=array('pagos.comprobantes._id'=>$elect['_id']);
									$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
									if($cuenta==0) {
										$params=array('pagos.comprobante._id'=>$elect['_id']);
										$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
										if($cuenta==0) $vacios[]=$elect;
									}
								}
								else if($cuenta>=2) {
									$elect['contratos'] = $f->model('in/cont')->params(array('filter'=>$params,'fields'=>array('inmueble' => 1, 'pagos'=>1 )))->get('all')->items;
									$multiples[] = $elect;
									unset($ecom[$ecm]);
								}
								else unset($ecom[$ecm]);
							}
							if(isset($concepto['acta_conciliacion'])){
								$params=array('items.comprobante._id'=>$elect['_id']);
								$cuenta = $f->model('in/acta')->params(array('data'=>$params))->get('count')->items;
								if(is_null($cuenta)) $nulo[]=$elect;
								else if($cuenta==0) {
									$params=array('pagos.comprobantes._id'=>$elect['_id']);
									$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
									if($cuenta==0) {
										$params=array('pagos.comprobante._id'=>$elect['_id']);
										$cuenta = $f->model('in/cont')->params(array('data'=>$params))->get('count')->items;
										if($cuenta==0) $vacios[]=$elect;
									}
								}
								else if($cuenta>=2) {
									$elect['acta_conciliacion'] = $f->model('in/acta')->params(array('filter'=>$params,'fields'=>array('inmueble' => 1, 'items'=>1 )))->get('all')->items;
									$multiples[] = $elect;
									unset($ecom[$ecm]);
								}
								else unset($ecom[$ecm]);
							}
						}
					}

				}
			}
		}

		header("Content-type:application/json");
		echo json_encode(array(
			'observaciones'=>array(
				'nulos'=>$nulos,
				'vacios'=>$vacios,
				'multiples'=>$multiples,
			),
			//'comp'=>$comp,
			'ecom'=>$ecom,
		));


	}
}
?>
