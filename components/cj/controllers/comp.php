<?php
class Controller_cj_comp extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnGen">Generar Recibo de Ingresos</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Comprobante","w"=>150 ),
			array( "nomb"=>"Cliente","w"=>250 ),
			array( "nomb"=>"Total","w"=>250 ),
			array( "nomb"=>"Registrado por","w"=>250 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows);
		if(isset($f->request->data['tipo']))
			$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['estado'])){
			$params['estado'] = $f->request->data['estado'];
		}
		$model = $f->model("cj/comp")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			if($f->request->data['modulo']!='')
				$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/comp")->params($params)->get("search") );
	}
	function execute_all(){
		global $f;
		$model = $f->model('cj/comp')->get('all');
		$f->response->json($model->items);
	}
	function execute_get_conceptos(){
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
		$items = $f->model("cj/comp")->params($params)->get("conceptos")->items;
		$f->response->view("cj/caja.report.php",array('conceptos'=>$items));
	}
	function execute_get(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		foreach ($comp['items'] as $i=>$item){
			$comp['items'][$i]['cuenta_cobrar'] = $f->model("cj/cuen")->params(array("_id"=>$item['cuenta_cobrar']['_id']))->get("one")->items;
		}
		if(isset($f->request->data['forma'])){
			$comp['ctban'] = $f->model("ts/ctban")->get("all")->items;
		}
		$f->response->json( $comp );
	}
	function execute_get_info_comp_cambiar(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		if(isset($comp['cliente_nuevo'])){
			$comp['cliente_nuevo'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente_nuevo']['_id']))->get("enti")->items;
		}
		foreach ($comp['items'] as $i=>$item){
			$comp['items'][$i]['cuenta_cobrar'] = $f->model("cj/cuen")->params(array("_id"=>$item['cuenta_cobrar']['_id']))->get("one")->items;
		}
		$cajas = array();
		if(isset($f->session->enti['roles']['cajero'])){
			foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja){
				$cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
			}
		}
		$tasa = $f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items;
		$ctban = $f->model("ts/ctban")->get("all")->items;
		$f->response->json(array(
			'comp'=>$comp,
			'cajas'=>$cajas,
			'tasa'=>$tasa,
			'ctban'=>$ctban
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['local']['_id'])) $data['local']['_id'] = new MongoId($data['local']['_id']);
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $key => $vou) {
				$data['vouchers'][$key]['monto'] = floatval($vou['monto']);
				$data['vouchers'][$key]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("cj/comp")->params(array('data'=>$data))->save("insert");
		}else{
			$tmp = $f->model('cj/comp')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model("cj/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			if(isset($tmp['vouchers'])&&!isset($data['vouchers'])){
				$f->model("cj/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
					'$unset'=>array('vouchers'=>true)
				)))->save("custom");
			}
		}
		$f->response->print("true");
	}
	function execute_save_anul(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		foreach($comp['items'] as $item){
			$total = 0;
			$upd_cuenta = array();
			$cuenta = $f->model("cj/cuen")->params(array("_id"=>$item['cuenta_cobrar']['_id']))->get("one")->items;
			$oper = $f->model('cm/oper')->params(array('_id'=>$cuenta['operacion']))->get('one')->items;
			if($oper!=null){
				if(isset($oper['recibos'])){
					$recibos = $oper['recibos'];
					foreach($recibos as $i=>$recibo){
						if($recibo['_id']==$comp['_id']){
							unset($recibos[$i]);
						}
					}
					$recibos = array_values($recibos);
					$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array('recibos'=>$recibos))))->save('update');
				}
			}
			foreach($cuenta['conceptos'] as $i=>$conc){
				$upd_cuenta['conceptos.'.$i.'.saldo'] = floatval($item['conceptos'][$i]['monto']);
				$total += floatval($item['conceptos'][$i]['monto']);
			}
			$upd_cuenta['saldo'] = $total;
			$f->model('cj/cuen')->params(array(
				'_id'=>$cuenta['_id'],
				'data'=>array('$pull'=>array('comprobantes'=>$comp['_id']))
			))->save('custom');
			$f->model('cj/cuen')->params(array(
				'_id'=>$cuenta['_id'],
				'data'=>array('$set'=>array('estado'=>'P'))
			))->save('custom');
			$f->model('cj/cuen')->params(array(
				'_id'=>$cuenta['_id'],
				'data'=>array('$inc'=>$upd_cuenta)
			))->save('custom');
		}
		$f->model('cj/comp')->params(array(
			'_id'=>$comp['_id'],
			'data'=>array('$set'=>array('estado'=>'X'))
		))->save('custom');
		$f->response->print("true");
	}
	function execute_save_rein(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['fecfin'])) $data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
		if(isset($data['total'])) $data['total'] = floatval($data['total']);
		if(isset($data['fuente']['_id'])) $data['fuente']['_id'] = new MongoId($data['fuente']['_id']);
		if(isset($data['organizacion']['_id'])) $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		if(isset($data['organizacion']['componente']['_id'])) $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
		if(isset($data['organizacion']['actividad']['_id'])) $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
		if(isset($data['organizacion']['subprograma']['_id'])) $data['organizacion']['subprograma']['_id'] = new MongoId($data['organizacion']['subprograma']['_id']);
		if(isset($data['organizacion']['programa']['_id'])) $data['organizacion']['programa']['_id'] = new MongoId($data['organizacion']['programa']['_id']);
		if(isset($data['organizacion']['funcion']['_id'])) $data['organizacion']['funcion']['_id'] = new MongoId($data['organizacion']['funcion']['_id']);
		if(isset($data['detalle'])){
			foreach ($data['detalle'] as $i=>$det){
				if(isset($det['cuenta']['_id'])) $data['detalle'][$i]['cuenta']['_id'] = new MongoId($det['cuenta']['_id']);
				if(isset($det['comprobante']['_id'])) $data['detalle'][$i]['comprobante']['_id'] = new MongoId($det['comprobante']['_id']);
				if(isset($det['operacion'])) $data['detalle'][$i]['operacion'] = new MongoId($det['operacion']);
				if(isset($det['cuenta_cobrar'])) $data['detalle'][$i]['cuenta_cobrar'] = new MongoId($det['cuenta_cobrar']);
			}
		}
		if(isset($data['comprobantes_anulados'])){
			foreach ($data['comprobantes_anulados'] as $i=>$det){
				if(isset($det['_id'])) $data['comprobantes_anulados'][$i]['_id'] = new MongoId($det['_id']);
			}
		}
		if(isset($data['cont_patrimonial'])){
			foreach ($data['cont_patrimonial'] as $i=>$det){
				if(isset($det['cuenta']['_id'])) $data['cont_patrimonial'][$i]['cuenta']['_id'] = new MongoId($det['cuenta']['_id']);
			}
		}
		if(isset($data['vouchers'])){
			foreach ($data['vouchers'] as $i=>$det){
				if(isset($det['cuenta_banco']['_id'])) $data['vouchers'][$i]['cuenta_banco']['_id'] = new MongoId($det['cuenta_banco']['_id']);
				if(isset($det['cliente']['_id'])) $data['vouchers'][$i]['cliente']['_id'] = new MongoId($det['cliente']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'RG';
			$data['autor'] = $f->session->userDB;
			$rein = $f->model("cj/rein")->params(array('data'=>$data))->save("insert")->items;
		}else{
			$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->json($rein);
	}
	function execute_save_cambiar(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDB;
		if($trabajador['_id']==new MongoId('57f3e8eb8e7358b007000042')){
			$trabajador = array(
				'_id'=>New MongoId('56fd3c148e73584c07000062'),
			     "tipo_enti"=>"P",
			     "nomb"=>"PEDRO PERCY",
			     "apmat"=>"REVILLA",
			     "appat"=>"AMESQUITA",
			     "cargo"=>array(
			       "funcion"=>"APOYO ADMINISTRATIVO",
			       "organizacion"=>array(
			         "_id"=>new MongoId("51a50f0f4d4a13c409000013"),
			         "nomb"=>"Unidad de Cementerio y Servicios Funerarios",
			         "componente"=>array(
			           "_id"=>new MongoId("51e99d7a4d4a13c404000016"),
			           "nomb"=>"SERVICIOS FUNERARIOS Y DE CEMENTERIO",
			           "cod"=>"001"
			        ),
			         "actividad"=>array(
			           "_id"=>new MongoId("51e996044d4a13440a00000e"),
			           "nomb"=>"SERVICIOS FUNERARIOS Y DE CEMENTERIO",
			           "cod"=>"5001194"
			        )
			      )
			    )
			);
		}
		$cliente = $data['cliente'];
		$cliente['_id'] = new MongoId($cliente['_id']);
		$servicio = $data['servicio'];
		$servicio['_id'] = new MongoId($servicio['_id']);
		if(isset($servicio['organizacion']))
			$servicio['organizacion']['_id'] = new MongoId($servicio['organizacion']['_id']);
		if(isset($data['fecven'])) $data['fecven'] = new MongoDate(strtotime($data['fecven']));
    	if(isset($data['saldo'])) $data['saldo'] = floatval($data['saldo']);
    	if(isset($data['total'])) $data['total'] = floatval($data['total']);
		foreach ($data['conceptos'] as $j=>$con){
      		if(isset($con['concepto']['_id'])) $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
      		if(isset($con['concepto']['_id'])) $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
      		if(isset($con['concepto']['clasificador']['_id'])) $data['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
      		if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $data['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
      		if(isset($con['concepto']['cuenta']['_id'])) $data['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
      		if(isset($con['saldo'])) $data['conceptos'][$j]['saldo'] = 0;
      		if(isset($con['total'])) $data['conceptos'][$j]['total'] = floatval($con['total']);
    	}
    	$comp_ori = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		switch ($comp_ori['tipo']){
			case 'B': $word = 'Boleta de Venta'; break;
			case 'R': $word = 'Recibo de Caja'; break;
			case 'F': $word = 'Factura'; break;
		}
		$cuenta = array(
			'fecreg'=>new MongoDate(),
			'estado'=>'C',
			'autor'=>$trabajador,
			'cliente'=>$cliente,
			'conceptos'=>$data['conceptos'],
			'fecven'=>$data['fecven'],
			'moneda'=>'S',
			'observ'=>'Cobro de Cambio de Nombre de '.$word.
				' con serie y n&uacute;mero <b>'.$comp_ori['serie'].'-'.$comp_ori['num'].'</b>',
			'saldo'=>0,
			'total'=>$data['total'],
   			'servicio'=>$servicio 
		);
		$cuenta = $f->model("cj/cuen")->params(array('data'=>$cuenta))->save("insert")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Cuentas por Cobrar',
			'descr'=>'Se hizo el <b>Cambio de Nombre de '.$word.'</b>.'.
				' con serie y n&uacute;mero <b>'.$comp_ori['serie'].'-'.$comp_ori['num'].'</b>'
		))->save('insert');
		/*
		 * Se realiza la creacion del comprobante
		 */
		$caja = $data['comp']['caja'];
		$caja['_id'] = new MongoId($caja['_id']);
		$caja['local']['_id'] = new MongoId($caja['local']['_id']);
		$items = array(
			array(
				'cuenta_cobrar'=>array(
					'_id'=>$cuenta['_id'],
					'servicio'=>$servicio
				),
				'conceptos'=>array()
			)
		);
		foreach ($data['conceptos'] as $conc){
			$items[0]['conceptos'][] = array(
				'concepto'=>array(
					'_id'=>$conc['concepto']['_id'],
					'nomb'=>$conc['concepto']['nomb']
				),
				'monto'=>$conc['monto']
			);
		}
		$efec = $data['comp']['efectivos'];
		foreach ($efec as $i=>$ef){
			$efec[$i]['monto'] = floatval($ef['monto']);
		}
		if(isset($data['comp']['vouchers'])){
			$vouchers = $data['comp']['vouchers'];
			foreach ($vouchers as $i=>$vou){
				$vouchers[$i]['monto'] = floatval($vou['monto']);
				$vouchers[$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
			}
		}
		if(!isset($data['fecreg']))
			$data['fecreg'] = date('Y-m-d');
		$comp = array(
			'fecreg'=>new MongoDate(strtotime($data['fecreg'])),
			'estado'=>'R',
			'periodo'=>date('ym00'),
			'autor'=>$trabajador,
			'cliente'=>$cliente,
			'caja'=>$caja,
			'modulo'=>$data['comp']['modulo'],
			'tipo'=>$data['comp']['tipo'],
			'serie'=>$data['comp']['serie'],
			'num'=>floatval($data['comp']['num']),
			'moneda'=>$data['comp']['moneda'],
			'items'=>$items,
			'total'=>floatval($data['comp']['total']),
			'tc'=>floatval($data['comp']['tc']),
			'efectivos'=>$efec
		);
		if(isset($vouchers)){
			$comp['vouchers'] = $vouchers;
		}
		$compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
		$f->model('cj/cuen')->params(array(
			'_id'=>$cuenta['_id'],
			'data'=>array('$push'=>array('comprobantes'=>$compro['_id']))
		))->save('custom');
		/*
		 * Se actualiza el comprobante original
		 */
    	$f->model('cj/comp')->params(array('_id'=>$comp_ori['_id'],'data'=>array(
    		'cliente_nuevo'=>$cliente,
    		'estado'=>'P'
    	)))->save('update');
		$f->response->print('true');
	}
	function execute_save_confirmar_camb(){
		global $f;
		$data = $f->request->data;
		$comp_ori = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		/*
		 * Se actualiza el comprobante original
		 */
    	$f->model('cj/comp')->params(array('_id'=>$comp_ori['_id'],'data'=>array(
    		'estado'=>'X'
    	)))->save('update');
		/*
		 * Se crea un nuevo comprobante
		 */
		$new = $comp_ori;
		//$new['fecreg'] = new MongoDate();
		//$new['_id'] = new MongoId();
		$new['fecreg'] = new MongoDate(strtotime($data['comp']['fecreg']));
		$new['estado'] = 'R';//C 
		$new['autor'] = $f->session->userDB;
		$new['caja'] = $data['comp']['caja'];
		$new['cliente'] = $comp_ori['cliente_nuevo'];
		$new['serie']= $data['comp']['serie'];
		$new['num']= floatval($data['comp']['num']);
		$new['comprobante_cambiado'] = array(
			'_id'=>$comp_ori['_id'],
			'serie'=>$comp_ori['serie'],
			'num'=>$comp_ori['num']
		);
		if(isset($new['caja']['_id']))
			$new['caja']['_id'] = new MongoId($new['caja']['_id']);
		if(isset($new['caja']['local']['_id']))
			$new['caja']['local']['_id'] = new MongoId($new['caja']['local']['_id']);
		unset($new['cliente_nuevo']);
		unset($new['_id']);
		$new['periodo'] = date('ym00');
		$compro = $f->model('cj/comp')->params(array('data'=>$new))->save('insert')->items;
		switch ($comp_ori['tipo']){
			case 'B': $word = 'Boleta de Venta'; break;
			case 'R': $word = 'Recibo de Caja'; break;
			case 'F': $word = 'Factura'; break;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Comprobantes',
			'descr'=>'Se confirm&oacute; el <b>Cambio de Nombre de '.$word.'</b>.'.
				' con serie y n&uacute;mero <b>'.$new['serie'].'-'.$new['num'].'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_edit(){
		global $f;
		$f->response->view("cj/comp.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("cj/comp.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("cj/comp.details");
	}
	function execute_anul(){
		global $f;
		$f->response->view("cj/comp.anular");
	}
	function execute_gen(){
		global $f;
		$f->response->view("cj/comp.gen");
	}
	function execute_cambiar(){
		global $f;
		$f->response->view("cj/comp.cambiar");
	}
	function execute_confirmar(){
		global $f;
		$f->response->view("cj/comp.confirmar");
	}
	function execute_voucher(){
		global $f;
		$f->response->view("cj/comp.voucher");
	}
	function execute_gen_bootstrap(){
		global $f;
		$f->response->view("cj/comp.gen.bootstrap");
	}
	function execute_gen_farmacia(){
		global $f;
		$f->response->view("cj/comp.gen.farmacia");
	}
	function execute_print_bole(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		foreach($comp["items"] as $i=>$item){
			$comp["items"][$i]["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$item["cuenta_cobrar"]["_id"]))->get("one")->items;
			if($comp["items"][$i]["cuenta_cobrar"]["modulo"]=="CM"){
				$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("cm/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
				if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"])){
					$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"] = $f->model("cm/espa")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["_id"]))->get("one")->items;
				}
			}elseif($comp["items"][$i]["cuenta_cobrar"]["modulo"]=="IN"){
				$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("in/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
			}
		}
		$f->response->view("cj/bole.print",array("items"=>$comp));
	}
	function execute_print_fact(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		foreach($comp["items"] as $i=>$item){
			$comp["items"][$i]["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$item["cuenta_cobrar"]["_id"]))->get("one")->items;
			if($comp["items"][$i]["cuenta_cobrar"]["modulo"]=="CM"){
				$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("cm/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
				if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"])){
					$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"] = $f->model("cm/espa")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["_id"]))->get("one")->items;
				}
			}elseif($comp["items"][$i]["cuenta_cobrar"]["modulo"]=="IN"){
				$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("in/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
			}
		}
		//print_r($comp);die();
		$f->response->view("cj/fact.print",array("items"=>$comp));
	}
	function execute_print_reci(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
		foreach($comp["items"] as $i=>$item){
			$comp["items"][$i]["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$item["cuenta_cobrar"]["_id"]))->get("one")->items;
			if(isset($comp["items"][$i]["cuenta_cobrar"]["modulo"])){
				if($comp["items"][$i]["cuenta_cobrar"]["modulo"]=="CM"){
					if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"])){
						$op_cuenta = $f->model("cm/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
						$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $op_cuenta;
					}else{
						$comp["items"][$i]["cuenta_cobrar"]["operacion"] = '';
					}
	
					if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"]["ocupante"])){
						$comp["items"][$i]["cuenta_cobrar"]["operacion"]["ocupante"] = $f->model("mg/entidad")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]["ocupante"]["_id"]))->get("one")->items;
					}
					if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"])){
						/*
						 * SE CREA CAMPO TEMPORAL ESPACIO_NOMB PARA NOMBRE ORIGINAL DE ESPACIO
						 */
						$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio_nomb"] = $comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["nomb"];
						$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"] = $f->model("cm/espa")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["_id"]))->get("one2")->items;
						if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]['nicho'])){
							$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]['nicho']['pabellon'] = $f->model("cm/pabe")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["nicho"]["pabellon"]["_id"]))->get("one")->items;
						}
						if(isset($comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["ocupantes"])){
							foreach ($comp["items"][$i]["cuenta_cobrar"]["operacion"]["espacio"]["ocupantes"] as $key_oc => $tmp_ocu) {
								$comp["items"][$i]["cuenta_cobrar"]["operacion"]["ocupantes"][$key_oc] = $f->model("mg/entidad")->params(array("_id"=>$tmp_ocu["_id"]))->get("one")->items;
							}
						}
					}
				}elseif($comp["items"][$i]["cuenta_cobrar"]["modulo"]=="IN"){
					$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("in/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
				}
			}
			
			/*print_r($comp);
			echo "----------------<br />";*/
		}
		//print_r($comp);die();
		$print = false;
		if(isset($f->request->data['print'])){
			$print = true;
		}
		$f->response->view("cj/reci.print",array("items"=>$comp,'print'=>$print));
	}
	function execute_eliminar(){
		global $f;
		$item = $f->model('cj/comp')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'cj_comprobantes';
		$item['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($item);
		$f->datastore->cj_comprobantes->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_get_hospitalizacion(){
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
		$items = $f->model("cj/comp")->params($params)->get("conceptos")->items;
		$f->response->view("cj/repo.hosp.php",array('conceptos'=>$items));
	}
}
?>