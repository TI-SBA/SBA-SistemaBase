<?php
class Controller_in_arre extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Arrendamiento</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Inmueble","w"=>200 ),
			array( "nomb"=>"Arrendatario","w"=>300 ),
			array( "nomb"=>"Inicio","w"=>150 ),
			array( "nomb"=>"Tipo","w"=>130 ),
			array( "nomb"=>"Vencimiento","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index_all() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Arrendamiento</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Inmueble","w"=>200 ),
			array( "nomb"=>"Arrendatario","w"=>300 ),
			array( "nomb"=>"Inicio","w"=>150 ),
			array( "nomb"=>"Tipo","w"=>130 ),
			array( "nomb"=>"Vencimiento","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_search(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"text"=>$f->request->texto
		);
		if(isset($f->request->data['estado'])) $params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['oper'])) $params['oper'] = $f->request->data['oper'];
		$model = $f->model("in/oper")->params($params)->get("search");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
					"_id"=>$item['espacio']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("in/oper")->params(array(
			"_id"=>new MongoId($f->request->id)
		))->get("one");
		$model->items['espacio'] = $f->model("in/espa")->params(array(
			"_id"=>$model->items['espacio']['_id']
		))->get("one")->items;
		if(isset($model->items['arrendamiento'])){
			$or = array(
				array('_id'=>$model->items['arrendatario']['_id'])
			);
			if(isset($model->items['arrendamiento']['representante']))
				$or[] = array('_id'=>$model->items['arrendamiento']['representante']['_id']);
			if(isset($model->items['arrendamiento']['aval'])){
				foreach ($model->items['arrendamiento']['aval'] as $aval){
					$or[] = array('_id'=>$aval['_id']);
				}
			}
			$enti = $f->model("mg/entidad")->params(array(
				"filter"=>array(
					'$or'=>$or
				),
				"fields"=>array('_id'=>true,'docident'=>true,'domicilios'=>true),
				"sort"=>array()
			))->get("custom_data");
			foreach ($enti->items as $ent){
				if($model->items['arrendatario']['_id']==$ent['_id'])
					$model->items['arrendatario'] = $model->items['arrendatario'] + $ent;
				if(isset($model->items['arrendamiento']['aval'])){
					foreach ($model->items['arrendamiento']['aval'] as $i=>$aval){
						if($aval['_id']==$ent['_id'])
							$model->items['arrendamiento']['aval'][$i] = $aval + $ent;
					}
				}
				if(isset($model->items['arrendamiento']['representante']))
					if($model->items['arrendamiento']['representante']['_id']==$ent['_id'])
						$model->items['arrendamiento']['representante'] = $model->items['arrendamiento']['representante'] + $ent;
			}
			$mobi = $f->model("in/espa")->params(array(
				"_id"=>$model->items['espacio']['_id']
			))->get("one");
			$model->items['espacio_old'] = $mobi->items;
			$model->items['cuentas_cobrar'] = array();
			if(isset($model->items['arrendamiento']['rentas'])){
				foreach ($model->items['arrendamiento']['rentas'] as $re){
					$model->items['cuentas_cobrar'][] = $f->model('cj/cuen')->params(array(
						'_id'=>$re['cuenta_cobrar']
					))->get('one')->items;
				}
			}
		}
		if(isset($model->items['actualizar_arrendamiento'])){
			$or = array(
				array('_id'=>$model->items['arrendatario']['_id'])
			);
			if(isset($model->items['actualizar_arrendamiento']['anterior']['representante']))
				$or[] = array('_id'=>$model->items['actualizar_arrendamiento']['anterior']['representante']['_id']);
			if(isset($model->items['arrendamiento']['aval'])){
				foreach ($model->items['arrendamiento']['aval'] as $aval){
					$or[] = array('_id'=>$aval['_id']);
				}
			}
			$enti = $f->model("mg/entidad")->params(array(
				"filter"=>array(
					'$or'=>$or
				),
				"fields"=>array('_id'=>true,'docident'=>true,'domicilios'=>true),
				"sort"=>array()
			))->get("custom_data");
			foreach ($enti->items as $ent){
				if($model->items['arrendatario']['_id']==$ent['_id'])
					$model->items['arrendatario'] = $model->items['arrendatario'] + $ent;
				if(isset($model->items['arrendamiento']['aval'])){
					foreach ($model->items['arrendamiento']['aval'] as $i=>$aval){
						if($aval['_id']==$ent['_id'])
							$model->items['arrendamiento']['aval'][$i] = $aval + $ent;
					}
				}
				if($model->items['actualizar_arrendamiento']['anterior']['representante']!=null)
					if($model->items['actualizar_arrendamiento']['anterior']['representante']['_id']==$ent['_id'])
						$model->items['actualizar_arrendamiento']['anterior']['representante'] = $model->items['actualizar_arrendamiento']['anterior']['representante'] + $ent;
			}
			$mobi = $f->model("in/espa")->params(array(
				"_id"=>$model->items['espacio']['_id']
			))->get("one");
			$model->items['espacio'] = $mobi->items;
			$arre = $f->model("in/oper")->params(array(
				"filter"=>array(
					'_id'=>new MongoId($f->request->arre)
				),
				"fields"=>array(
					'_id'=>true,
					'arrendamiento.condicion'=>true,
					'arrendamiento.tipo'=>true,
					'arrendamiento.estado'=>true,
					'arrendamiento.fecocu'=>true
				),
				"sort"=>array()
			))->get("custom_data");
			$model->items['arrendamiento'] = $arre->items[0]['arrendamiento'];
			$act = $f->model("in/oper")->params(array(
				"filter"=>array(
					'_id'=>$model->items['actualizar_arrendamiento']['anterior']['ficha']
				),
				"fields"=>array(
					'_id'=>true,
					'actualizar_ficha.anterior.mobiliario'=>true
				),
				"sort"=>array()
			))->get("custom_data");
			$model->items['actualizar_ficha'] = $act->items[0]['actualizar_ficha'];
		}
		$f->response->json( $model->items );
	}
	function execute_get_all(){
		global $f;
		$model = $f->model("in/oper")->params(array(
			"_id"=>new MongoId($f->request->_id)
		))->get("one");
		if($model->items['arrendamiento']!=null){
			if(!isset($model->items['arrendamiento']['representante'])){
				$or = array(
					array('_id'=>$model->items['arrendatario']['_id'])
				);
			}else{
				$or = array(
					array('_id'=>$model->items['arrendatario']['_id']),
					array('_id'=>$model->items['arrendamiento']['representante']['_id'])
				);
			}
			if(isset($model->items['arrendamiento']['aval'])){
				foreach ($model->items['arrendamiento']['aval'] as $aval){
					$or[] = array('_id'=>$aval['_id']);
				}
			}
			$enti = $f->model("mg/entidad")->params(array(
				"filter"=>array(
					'$or'=>$or
				),
				"fields"=>array('_id'=>true,'docident'=>true,'domicilios'=>true),
				"sort"=>array()
			))->get("custom_data");
			foreach ($enti->items as $ent){
				if($model->items['arrendatario']['_id']==$ent['_id']) $model->items['arrendatario'] = $model->items['arrendatario'] + $ent;
				if(isset($model->items['arrendamiento']['representante']))
					if($model->items['arrendamiento']['representante']['_id']==$ent['_id'])
						$model->items['arrendamiento']['representante'] = $model->items['arrendamiento']['representante'] + $ent;
				if(isset($model->items['arrendamiento']['aval'])){
					foreach ($model->items['arrendamiento']['aval'] as $i=>$aval){
						if($aval['_id']==$ent['_id'])
							$model->items['arrendamiento']['aval'][$i] = $aval + $ent;
					}
				}
			}
			$mobi = $f->model("in/espa")->params(array(
				"_id"=>$model->items['espacio']['_id']
			))->get("one");
			$model->items['espacio'] = $mobi->items;
			if(isset($model->items['arrendamiento']['traspaso_desde'])){
				$tra = $f->model("in/oper")->params(array(
					"filter"=>array(
						'_id'=>$model->items['arrendamiento']['traspaso_desde']
					),
					"fields"=>array('_id'=>true,'arrendatario'=>true),
					"sort"=>array()
				))->get("custom_data");
				$model->items['arrendamiento']['traspaso_desde'] = $tra->items[0];
			}
			if(isset($model->items['arrendamiento']['traspaso_a'])){
				$tra = $f->model("in/oper")->params(array(
					"filter"=>array(
						'_id'=>$model->items['arrendamiento']['traspaso_a']
					),
					"fields"=>array('_id'=>true,'arrendatario'=>true),
					"sort"=>array()
				))->get("custom_data");
				$model->items['arrendamiento']['traspaso_a'] = $tra->items[0];
			}
			if(isset($model->items['arrendamiento']['actualizaciones'])){
				$or = array();
				foreach ($model->items['arrendamiento']['actualizaciones'] as $act){
					array_push($or, array('_id'=>$act) );
				}
				$act = $f->model("in/oper")->params(array(
					"filter"=>array(
						'$or'=>$or
					),
					"fields"=>array('_id'=>true,'fecreg'=>true),
					"sort"=>array()
				))->get("custom_data");
				$model->items['arrendamiento']['actualizaciones'] = $act->items;
				$tmp = $f->model("in/oper")->params(array(
					"filter"=>array(
						'_id'=>$act->items[sizeof($act->items)-1]['_id']
					),
					"fields"=>array('_id'=>true,'fecreg'=>true),
					"sort"=>array()
				))->get("custom_data");
				$model->items['arrendamiento']['actualizaciones'][sizeof($act->items)-1] = $tmp->items[0];
			}
			$servicios = $f->model('cj/cuen')->params(array('espacio'=>$model->items['espacio']['_id']))->get('espacio')->items;
			if($servicios!=null){
				foreach ($servicios as $ii=>$serv){
					if(isset($serv['comprobantes'])){
						foreach ($serv['comprobantes'] as $jj=>$comp){
							$servicios[$ii]['comprobantes'][$jj] = $f->model('cj/comp')->params(array('_id'=>$comp))->get('one')->items;
						}
					}
				}
				$model->items['servicios'] = $servicios;
			}
		}
		$f->response->json( $model->items );
	}
	function execute_listapen(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_arre_pen");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
					"_id"=>$item['espacio']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_listarec(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_arre_rec");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
					"_id"=>$item['espacio']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_listaven(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_arre_ven");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
					"_id"=>$item['espacio']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_listapor(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_arre_por");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
					"_id"=>$item['espacio']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_listaall(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_arre_all");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
					"_id"=>$item['espacio']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_get_arre_all(){
		global $f;
		$all_arre = $f->model('in/oper')->params(array(
			'filter'=>array(
				'espacio._id'=>new MongoId($f->request->data['_id']),
				'arrendamiento'=>array('$exists'=>true)
			)
		))->get('custom_data')->items;
		$f->response->json( $all_arre );
	}
	function execute_save_regi_pend(){
		global $f;
		$trabajador = $f->session->userDB;
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']);
		$aval = array();
		if(isset($f->request->data['aval'])){
			foreach ($f->request->data['aval'] as $item){
				$item['_id'] = new MongoId($item['_id']);
				$aval[] = $item;
			}
		}
		if($arrendatario['tipo_enti']=='E'){
			if(isset($f->request->data['representante'])){
				$representante = $f->request->data['representante'];
				$representante['_id'] = new MongoId($representante['_id']);
			}
		}
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']);
		$espacio_old = $f->request->data['espacio_old'];
		$espacio_old['_id'] = new MongoId($espacio_old['_id']);
		$espacio_old['ubic']['local']['_id'] = new MongoId($espacio_old['ubic']['local']['_id']);
		if(!isset($espacio_old['mobiliario']))
			$espacio_old['mobiliario'] = array();
		$arrendamiento = $f->request->data['arrendamiento'];
		if(isset($f->request->data['mobiliario']))
			$mobiliario = $f->request->data['mobiliario'];
		else
			$mobiliario = array();
		//Actualizar Ficha
		$act_fic = $f->model('in/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'arrendatario'=>$arrendatario,
			'trabajador'=>$trabajador,
			'espacio'=>$espacio,
			'observ'=>$arrendamiento['observ'],
			'actualizar_ficha'=>array(
				'anterior'=>array(
					'descr'=>$espacio_old['descr'],
					'uso'=>$espacio_old['uso'],
					'conserv'=>$espacio_old['conserv'],
					'habilitado'=>$espacio_old['habilitado'],
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'valor'=>array(
						'renta'=>$espacio_old['valor']['renta'],
						'moneda'=>$espacio_old['valor']['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$espacio_old['mobiliario']
				),
				'nuevo'=>array(
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'uso'=>$arrendamiento['uso'],
					'conserv'=>$arrendamiento['conserv'],
					'valor'=>array(
						'renta'=>$arrendamiento['renta'],
						'moneda'=>$arrendamiento['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$mobiliario,
					'descr'=>$espacio_old['descr'],
					'habilitado'=>$espacio_old['habilitado']
				)
			)
		)))->save('oper')->obj;
		//Crear Arrendamiento
		if(!isset($arrendamiento['moneda_garantia']))
			$arrendamiento['moneda_garantia'] = $arrendamiento['moneda'];
		$crear_arre = array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'arrendatario'=>$arrendatario,
			'espacio'=>$espacio,
			'observ'=>$arrendamiento['observ'],
			'arrendamiento'=>array(
				'fecocu'=>'',
				'fecven'=>'',
				'condicion'=>$arrendamiento['condicion'],
				'tipo'=>'P',
				'estado'=>'P',
				'moneda'=>$arrendamiento['moneda'],
				'renta'=>$arrendamiento['renta'],
				'garantia'=>array(
					'importe'=>$arrendamiento['garantia'],
					'moneda'=>$arrendamiento['moneda_garantia']
				),
				'inmueble_garantia'=>array(
					'descr'=>$arrendamiento['inmueble_garantia'],
					'hipotecado'=>false
				),
				'aval'=>$aval,
				'ficha'=>$act_fic['_id']
			)
		);
		if($arrendamiento['fecocu']!=''){
			$crear_arre['arrendamiento']['fecocu'] = new MongoDate(strtotime($arrendamiento['fecocu']));
		}
		if($arrendamiento['fecven']!=''){
			$crear_arre['arrendamiento']['fecven'] = new MongoDate(strtotime($arrendamiento['fecven']));
		}
		if($arrendamiento['feccon']!=''){
			$crear_arre['arrendamiento']['feccon'] = new MongoDate(strtotime($arrendamiento['feccon']));
		}
		if($arrendamiento['contrato']!=''){
			$crear_arre['arrendamiento']['contrato'] = $arrendamiento['contrato'];
		}
		if($arrendatario['tipo_enti']=='E'){
			$crear_arre['arrendamiento']['representante'] = $representante;
		}
		$oper_arre = $f->model('in/oper')->params(array('data'=>$crear_arre))->save('oper');
		//Actualizar Arrendatario
		$f->model('in/arre')->params(array(
			'entidad'=>$arrendatario['_id'],
			'espacio'=>array(
				'_id'=>$espacio['_id'],
				'descr'=>$espacio_old['descr']
			)
		))->save('new_espa');
		if(sizeof($aval)>0){
			//Crear Aval
			foreach($aval as $item){
				$f->model('mg/entidad')->params(array(
					'_id'=>$item['_id'],
					'rol'=>'roles.aval'
				))->save('rol');
			}
		}
		if(isset($representante)){
			//Crear Representante
			if($arrendatario['tipo_enti']=='E'){
				$f->model('mg/entidad')->params(array(
					'_id'=>$representante['_id'],
					'rol'=>'roles.representante_legal'
				))->save('rol');
			}
		}
		//Actualizar espacio
		$f->model('in/espa')->params(array(
			'espacio'=>$espacio['_id'],
			'ocupado'=>true,
			'renta'=>$arrendamiento['renta'],
			'garantia'=>$arrendamiento['garantia'],
			'moneda'=>$arrendamiento['moneda'],
			'arrendatario'=>$arrendatario,
			'mobiliario'=>$mobiliario
		))->save('act');
		$enti = $arrendatario['nomb'];
		if($arrendatario['tipo_enti']=='P')
			$enti .= ' '.$arrendatario['appat'].' '.$arrendatario['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se cre&oacute; un arrendamiento en estado <b>Pendiente</b> para el inmueble <b>'.$espacio['descr'].', '.$espacio['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_save_regi_arre(){
		global $f;
		$trabajador = $f->session->userDB;
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']);
		$aval = array();
		if(isset($f->request->data['aval'])){
			foreach ($f->request->data['aval'] as $item){
				$item['_id'] = new MongoId($item['_id']);
				$aval[] = $item;
			}
		}
		if($arrendatario['tipo_enti']=='E'){
			$representante = $f->request->data['representante'];
			$representante['_id'] = new MongoId($representante['_id']);
		}
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']);
		$espacio_old = $f->request->data['espacio_old'];
		$espacio_old['_id'] = new MongoId($espacio_old['_id']);
		$espacio_old['ubic']['local']['_id'] = new MongoId($espacio_old['ubic']['local']['_id']);
		if(!isset($espacio_old['mobiliario']))
			$espacio_old['mobiliario'] = array();
		$arrendamiento = $f->request->data['arrendamiento'];
		if(isset($f->request->data['mobiliario']))
			$mobiliario = $f->request->data['mobiliario'];
		else
			$mobiliario = array();
		$rentas = $f->request->data['letras'];
		$cuentas = $f->request->data['cuentas_cobrar'];
		if($rentas!=null){
			foreach ($rentas as $i=>$rent){
				$rentas[$i]['fecpago'] = new MongoDate(strtotime($rent['fecpago']));
				$rentas[$i]['fecven'] = new MongoDate(strtotime($rent['fecven']));
				$rentas[$i]['fecpro'] = new MongoDate(strtotime($rent['fecpro']));
				$cuentas[$i]['observ'] = 'Cobro de <b>Arrendamiento N&deg; '.($i+1).' '.(($rent['letra']!='')?'('.$rent['letra'].')':'').'</b> para <b>'.$espacio['descr'].'</b> de <b>'.$espacio['ubic']['local']['nomb'].'</b>';
				$cuentas[$i]['fecreg'] = new MongoDate();
				$cuentas[$i]['estado'] = 'P';
				$cuentas[$i]['modulo'] = 'IN';
				$cuentas[$i]['inmueble'] = $espacio;
				$cuentas[$i]['cliente'] = $arrendatario;
				$cuentas[$i]['autor'] = $trabajador;
				if(isset($cuentas[$i]['servicio']['_id']))
					$cuentas[$i]['servicio']['_id'] = new MongoId($cuentas[$i]['servicio']['_id']);
				if(isset($cuentas[$i]['servicio']['organizacion']['_id']))
					$cuentas[$i]['servicio']['organizacion']['_id'] = new MongoId($cuentas[$i]['servicio']['organizacion']['_id']);
				if(isset($cuentas[$i]['fecven']))
					$cuentas[$i]['fecven'] = new MongoDate(strtotime($cuentas[$i]['fecven']));
				foreach ($cuentas[$i]['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuentas[$i]['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuentas[$i]['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuentas[$i]['saldo'])) $cuentas[$i]['saldo'] = floatval($cuentas[$i]['saldo']);
				if(isset($cuentas[$i]['monto'])) $cuentas[$i]['monto'] = floatval($cuentas[$i]['monto']);
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuentas[$i]))->save('insert');
				$rentas[$i]['cuenta_cobrar'] = $cuen->items['_id'];
			}
			$f->model('mg/entidad')->params(array(
				'_id'=>$arrendatario['_id'],
				'rol'=>'roles.cliente'
			))->save('rol');
		}
		//Actualizar Ficha
		$act_fic = $f->model('in/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'arrendatario'=>$arrendatario,
			'trabajador'=>$trabajador,
			'espacio'=>$espacio,
			'observ'=>$arrendamiento['observ'],
			'actualizar_ficha'=>array(
				'anterior'=>array(
					'descr'=>$espacio_old['descr'],
					'uso'=>$espacio_old['uso'],
					'conserv'=>$espacio_old['conserv'],
					'habilitado'=>$espacio_old['habilitado'],
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'valor'=>array(
						'renta'=>$espacio_old['valor']['renta'],
						'moneda'=>$espacio_old['valor']['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$espacio_old['mobiliario']
				),
				'nuevo'=>array(
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'uso'=>$arrendamiento['uso'],
					'conserv'=>$arrendamiento['conserv'],
					'valor'=>array(
						'renta'=>$arrendamiento['renta'],
						'moneda'=>$arrendamiento['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$mobiliario,
					'descr'=>$espacio_old['descr'],
					'habilitado'=>$espacio_old['habilitado']
				)
			)
		)))->save('oper');
		$act_fic = $act_fic->obj;
		//Actualizar Arrendamiento
		if(!isset($arrendamiento['moneda_garantia']))
			$arrendamiento['moneda_garantia'] = $arrendamiento['moneda'];
		$crear_arre = array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'arrendatario'=>$arrendatario,
			'espacio'=>$espacio,
			'observ'=>$arrendamiento['observ'],
			'arrendamiento'=>array(
				'fecocu'=>new MongoDate(strtotime($arrendamiento['fecocu'])),
				'fecven'=>new MongoDate(strtotime($arrendamiento['fecven'])),
				'condicion'=>$arrendamiento['condicion'],
				'tipo'=>'P',
				'estado'=>'A',
				'moneda'=>$arrendamiento['moneda'],
				'renta'=>$arrendamiento['renta'],
				'garantia'=>array(
					'importe'=>$arrendamiento['garantia'],
					'moneda'=>$arrendamiento['moneda_garantia']
				),
				'inmueble_garantia'=>array(
					'descr'=>$arrendamiento['inmueble_garantia'],
					'hipotecado'=>false
				),
				'aval'=>$aval,
				'rentas'=>$rentas,
				'ficha'=>$act_fic['_id']
			)
		);
		if($arrendamiento['contrato']!=''){
			$crear_arre['arrendamiento']['feccon'] = new MongoDate(strtotime($arrendamiento['feccon']));
			$crear_arre['arrendamiento']['contrato'] = $arrendamiento['contrato'];
		}
		if($arrendatario['tipo_enti']=='E'){
			$crear_arre['arrendamiento']['representante'] = $representante;
		}
		$oper_arre = $f->model('in/oper')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>$crear_arre
		))->save('upd');
		foreach ($rentas as $rent){
			$f->model('cj/cuen')->params(array(
				'_id'=>$rent['cuenta_cobrar'],
				'data'=>array('$set'=>array('operacion'=>$oper_arre->obj['_id']))
			))->save('custom');
		}
		//Crear Aval
		foreach($aval as $item){
			$f->model('mg/entidad')->params(array(
				'_id'=>$item['_id'],
				'rol'=>'roles.aval'
			))->save('rol');
		}
		//Crear Representante
		if($arrendatario['tipo_enti']=='E'){
			$f->model('mg/entidad')->params(array(
				'_id'=>$representante['_id'],
				'rol'=>'roles.representante_legal'
			))->save('rol');
		}
		//Actualizar espacio
		$f->model('in/espa')->params(array(
			'espacio'=>$espacio['_id'],
			'ocupado'=>true,
			'renta'=>$arrendamiento['renta'],
			'garantia'=>$arrendamiento['garantia'],
			'moneda'=>$arrendamiento['moneda'],
			'arrendatario'=>$arrendatario,
			'mobiliario'=>$mobiliario
		))->save('act');
		$enti = $arrendatario['nomb'];
		if($arrendatario['tipo_enti']=='P')
			$enti .= ' '.$arrendatario['appat'].' '.$arrendatario['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se actualiz&oacute; el arrendamiento en estado <b>Habilitado</b> para el inmueble <b>'.$espacio['descr'].', '.$espacio['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_save_act_arre(){
		global $f;
		$trabajador = $f->session->userDB;
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']);
		$aval = array();
		if(isset($f->request->data['aval'])){
			foreach ($f->request->data['aval'] as $item){
				$item['_id'] = new MongoId($item['_id']);
				$aval[] = $item;
			}
		}
		if($arrendatario['tipo_enti']=='E'){
			$representante = $f->request->data['representante'];
			$representante['_id'] = new MongoId($representante['_id']);
		}
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']);
		$espacio_old = $f->request->data['espacio_old'];
		$espacio_old['_id'] = new MongoId($espacio_old['_id']);
		$espacio_old['ubic']['local']['_id'] = new MongoId($espacio_old['ubic']['local']['_id']);
		$arrendamiento = $f->request->data['arrendamiento'];
		$mobiliario = $f->request->data['mobiliario'];
		$cuentas_cobrar = $f->request->data['cuentas_cobrar'];
		$rentas = $f->request->data['letras'];
		if($rentas!=null){
			foreach ($rentas as $i=>$rent){
				$rentas[$i]['fecpago'] = new MongoDate(strtotime($rent['fecpago']));
				$rentas[$i]['fecven'] = new MongoDate(strtotime($rent['fecven']));
				$rentas[$i]['fecpro'] = new MongoDate(strtotime($rent['fecpro']));
				$rentas[$i]['cuenta_cobrar'] = new MongoId($rent['cuenta_cobrar']);
			}
		}
		$old = $f->request->data['old'];
		$old['_id'] = new MongoId($old['_id']);
		if(isset($old['aval'])){
			foreach ($old['aval'] as $i=>$item){
				$old['aval'][$i]['_id'] = new MongoId($item['_id']);
			}
		}
		if($arrendatario['tipo_enti']=='E'){
			$old['representante']['_id'] = new MongoId($old['representante']['_id']);
		}
		if($old['ficha']!=null){
			$old['ficha'] = new MongoId($old['ficha']);
		}
		if($old['rentas']!=null){
			foreach ($old['rentas'] as $i=>$rent){
				$old['rentas'][$i]['fecpago'] = new MongoDate(strtotime($rent['fecpago']));
				$old['rentas'][$i]['fecven'] = new MongoDate(strtotime($rent['fecven']));
				$old['rentas'][$i]['fecpro'] = new MongoDate(strtotime($rent['fecpro']));
				$old['rentas'][$i]['cuenta_cobrar'] = new MongoId($rent['cuenta_cobrar']);
			}
		}
		//Actualizar Ficha
		if($mobiliario!=null){
			$act_fic = $f->model('in/oper')->params(array('data'=>array(
				'fecreg'=>new MongoDate(),
				'trabajador'=>$trabajador,
				'arrendatario'=>$arrendatario,
				'espacio'=>$espacio,
				'actualizar_ficha'=>array(
					'anterior'=>array(
						'descr'=>$espacio_old['descr'],
						'uso'=>$espacio_old['uso'],
						'conserv'=>$espacio_old['conserv'],
						'habilitado'=>$espacio_old['habilitado'],
						'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
						'valor'=>array(
							'renta'=>$espacio_old['valor']['renta'],
							'moneda'=>$espacio_old['valor']['moneda']
						),
						'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
						'area_terreno'=>$espacio_old['area_terreno'],
						'area_construida'=>$espacio_old['area_construida'],
						'medidor_agua'=>$espacio_old['medidor_agua'],
						'medidor_luz'=>$espacio_old['medidor_luz'],
						'mobiliario'=>$espacio_old['mobiliario']
					),
					'nuevo'=>array(
						'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
						'uso'=>$arrendamiento['uso'],
						'conserv'=>$arrendamiento['conserv'],
						'valor'=>array(
							'renta'=>$espacio_old['valor']['renta'],
							'moneda'=>$espacio_old['valor']['moneda']
						),
						'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
						'area_terreno'=>$espacio_old['area_terreno'],
						'area_construida'=>$espacio_old['area_construida'],
						'medidor_agua'=>$espacio_old['medidor_agua'],
						'medidor_luz'=>$espacio_old['medidor_luz'],
						'mobiliario'=>$mobiliario,
						'descr'=>$espacio_old['descr'],
						'habilitado'=>$espacio_old['habilitado']
					)
				)
			)))->save('oper');
			$act_fic = $act_fic->obj;
		}
		//Actualizar Arrendamiento
		$ante_arre = array(
			'fecven'=>new MongoDate(strtotime($old['fecven'])),
			'contrato'=>$old['contrato'],
			'garantia'=>$old['garantia'],
			'aval'=>$old['aval'],
			'inmueble_garantia'=>$old['inmueble_garantia']
		);
		if($arrendatario['tipo_enti']=='E'){
			$ante_arre['representante'] = $old['representante'];
		}
		if($old['rentas']!=null){
			$ante_arre['rentas'] = $old['rentas'];
		}
		if($old['ficha']!=null){
			$ante_arre['ficha'] = $old['ficha'];
		}
		$nuevo_arre = array(
			'fecven'=>new MongoDate(strtotime($arrendamiento['fecven'])),
			'condicion'=>$arrendamiento['condicion'],
			'garantia'=>array(
				'importe'=>$arrendamiento['garantia'],
				'moneda'=>$arrendamiento['moneda']
			),
			'aval'=>$aval,
			'inmueble_garantia'=>$arrendamiento['inmueble_garantia']
		);
		if($arrendamiento['contrato']!=''){
			$nuevo_arre['arrendamiento']['feccon'] = new MongoDate(strtotime($arrendamiento['feccon']));
			$nuevo_arre['arrendamiento']['contrato'] = $arrendamiento['contrato'];
		}
		if($arrendatario['tipo_enti']=='E'){
			$nuevo_arre['representante'] = $representante;
		}
		if($rentas!=null){
			$nuevo_arre['rentas'] = $rentas;
		}
		if($mobiliario!=null){
			$nuevo_arre['ficha'] = $act_fic['_id'];
		}
		$act_arre = array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'arrendatario'=>$arrendatario,
			'espacio'=>$espacio,
			'actualizar_arrendamiento'=>array(
				'anterior'=>$ante_arre,
				'nuevo'=>$nuevo_arre
			)
		);
		$nuevo_arre = $f->model('in/oper')->params(array('data'=>$act_arre))->save('oper');
		$nuevo_arre = $nuevo_arre->obj;
		//Crear Aval
		foreach($aval as $item){
			$f->model('mg/entidad')->params(array(
				'_id'=>$item['_id'],
				'rol'=>'roles.aval'
			))->save('rol');
		}
		//Crear Representante
		if($arrendatario['tipo_enti']=='E'){
			$f->model('mg/entidad')->params(array(
				'_id'=>$representante['_id'],
				'rol'=>'roles.representante_legal'
			))->save('rol');
		}
		if(!isset($arrendamiento['moneda_garantia']))
			$arrendamiento['moneda_garantia'] = $arrendamiento['moneda'];
		$upd_arren = array(
			'$set'=>array(
				'arrendamiento.fecven'=>new MongoDate(strtotime($arrendamiento['fecven'])),
				'arrendamiento.condicion'=>$arrendamiento['condicion'],
				'arrendamiento.moneda'=>$arrendamiento['moneda'],
				'arrendamiento.renta'=>$arrendamiento['renta'],
				'arrendamiento.garantia'=>array(
					'importe'=>$arrendamiento['garantia'],
					'moneda'=>$arrendamiento['moneda_garantia']
				),
				'arrendamiento.aval'=>$aval,
				'arrendamiento.inmueble_garantia'=>array('descr'=>$arrendamiento['inmueble_garantia'])
			),
			'$push'=>array(
				'arrendamiento.actualizaciones'=>$nuevo_arre['_id']
			)
		);
		if($arrendamiento['contrato']!=''){
			$upd_arren['$set']['arrendamiento.feccon'] = new MongoDate(strtotime($arrendamiento['feccon']));
			$upd_arren['$set']['arrendamiento.contrato'] = $arrendamiento['contrato'];
		}
		if($arrendatario['tipo_enti']=='E'){
			$upd_arren['$set']['arrendamiento.representante'] = $representante;
		}
		if($rentas!=null){
			$upd_arren['$set']['arrendamiento.rentas'] = $rentas;
		}
		if($mobiliario!=null){
			$upd_arren['$set']['arrendamiento.ficha'] = $act_fic['_id'];
		}
		$f->model('in/oper')->params(array(
			'_id'=>$old['_id'],
			'data'=>$upd_arren
		))->save('upd');
		//Actualizar espacio
		$act_espa = array(
			'espacio'=>$espacio['_id'],
			'renta'=>$arrendamiento['renta'],
			'garantia'=>$arrendamiento['garantia'],
			'moneda'=>$arrendamiento['moneda'],
			'arrendatario'=>$arrendatario,
			'ocupado'=>true
		);
		if($mobiliario!=null) $act_espa['mobiliario'] = $mobiliario;
		else $act_espa['mobiliario'] = $espacio_old['mobiliario'];
		$f->model('in/espa')->params($act_espa)->save('act');
		foreach ($cuentas_cobrar as $i=>$cuenta){
			foreach ($cuenta['conceptos'] as $j=>$con){
				if(isset($con['concepto']['_id']))
					$cuentas_cobrar[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
				if(isset($con['concepto']['_id']))
					$cuentas_cobrar[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
				if(isset($con['concepto']['clasificador']['_id']))
					$cuentas_cobrar[$i]['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
				if(isset($con['concepto']['clasificador']['cuenta']['_id']))
					$cuentas_cobrar[$i]['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
				if(isset($con['concepto']['cuenta']['_id']))
					$cuentas_cobrar[$i]['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
				if(isset($con['saldo'])) $cuentas_cobrar[$i]['conceptos'][$j]['saldo'] = floatval($con['saldo']);
				if(isset($con['monto'])) $cuentas_cobrar[$i]['conceptos'][$j]['monto'] = floatval($con['monto']);
			}
			if(isset($cuenta['saldo'])) $cuentas_cobrar[$i]['saldo'] = floatval($cuenta['saldo']);
			if(isset($cuenta['monto'])) $cuentas_cobrar[$i]['monto'] = floatval($cuenta['monto']);
		}
		foreach ($rentas as $i=>$rent){
			if($rent['estado']=='CR'){
				$f->model('cj/cuen')->params(array(
					'_id'=>$rent['cuenta_cobrar'],
					'data'=>array('$set'=>array(
						'conceptos'=>$cuentas_cobrar[$i]['conceptos'],
						'moneda'=>$cuentas_cobrar[$i]['moneda'],
						'fecven'=>$rentas[$i]['fecven'],
						'total'=>$cuentas_cobrar[$i]['total'],
						'saldo'=>$cuentas_cobrar[$i]['total']
					))
				))->save('custom');
			}
		}
		$f->model('mg/entidad')->params(array(
			'_id'=>$arrendatario['_id'],
			'rol'=>'roles.cliente'
		))->save('rol');
		$enti = $arrendatario['nomb'];
		if($arrendatario['tipo_enti']=='P')
			$enti .= ' '.$arrendatario['appat'].' '.$arrendatario['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se actualiz&oacute; el arrendamiento para el inmueble <b>'.$espacio['descr'].', '.$espacio['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_save_desoc(){
		global $f;
		$trabajador = $f->session->userDB;
		$id = new MongoId($f->request->data['_id']);
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']);
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']);
		$espacio_old = $f->request->data['espacio_old'];
		$espacio_old['_id'] = new MongoId($espacio_old['_id']);
		$espacio_old['ubic']['local']['_id'] = new MongoId($espacio_old['ubic']['local']['_id']);
		$mobiliario = $f->request->data['mobiliario'];
		//Actualizar Ficha
		$act_fic = $f->model('in/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'arrendatario'=>$arrendatario,
			'espacio'=>$espacio,
			'actualizar_ficha'=>array(
				'anterior'=>array(
					'descr'=>$espacio_old['descr'],
					'uso'=>$espacio_old['uso'],
					'conserv'=>$espacio_old['conserv'],
					'habilitado'=>$espacio_old['habilitado'],
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'valor'=>array(
						'renta'=>$espacio_old['valor']['renta'],
						'moneda'=>$espacio_old['valor']['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$espacio_old['mobiliario']
				),
				'nuevo'=>array(
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'uso'=>$espacio_old['uso'],
					'conserv'=>$f->request->data['conserv'],
					'valor'=>array(
						'renta'=>$espacio_old['valor']['renta'],
						'moneda'=>$espacio_old['valor']['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$mobiliario,
					'descr'=>$espacio_old['descr'],
					'habilitado'=>$espacio_old['habilitado']
				)
			)
		)))->save('oper');
		$act_fic = $act_fic->obj;
		//Actualizar Arrendamiento
		$upd_arrend = array(
			'arrendamiento.estado'=>'F',
			'arrendamiento.ficha'=>$act_fic['_id'],
			'arrendamiento.desocupacion'=>array(
				'fecdes'=>new MongoDate(strtotime($f->request->data['motivo'])),
				'observ'=>$f->request->data['observ'],
				'motivo'=>$f->request->data['motivo']
			)
		);
		$old_oper = $f->model('in/oper')->params(array('_id'=>$id))->get('one')->items;
		foreach ($old_oper['arrendamiento']['rentas'] as $i=>$rent){
			if($rent['estado']=='CR'){
				$upd_arrend['arrendamiento.rentas.'.$i.'.estado'] = 'X';
				$f->model('cj/cuen')->params(array(
					'_id'=>$rent['cuenta_cobrar'],
					'data'=>array('$set'=>array('estado'=>'X'))
				))->save('custom');
			}
		}
		$f->model('in/oper')->params(array(
			'_id'=>$id,
			'data'=>array('$set'=>$upd_arrend)
		))->save('upd');
		//Actualizar arrendatario
		$f->model('in/arre')->params(array(
			'_id'=>$arrendatario['_id'],
			'espacio'=>$espacio['_id']
		))->delete('espacio');
		//Actualizar Espacio
		$upd_esp = array(
			'$unset'=>array(
				'arrendatario'=>true
			),
			'$set'=>array(
				'ocupado'=>false,
				'conserv'=>$f->request->data['conserv'],
				'mobiliario'=>$mobiliario
			)
		);
		$f->model('in/espa')->params(array(
			'_id'=>$espacio['_id'],
			'data'=>$upd_esp
		))->save('upd');
		$enti = $arrendatario['nomb'];
		if($arrendatario['tipo_enti']=='P')
			$enti .= ' '.$arrendatario['appat'].' '.$arrendatario['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se desocup&oacute; el arrendamiento para el inmueble <b>'.$espacio['descr'].', '.$espacio['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_save_renov(){
		global $f;
		$trabajador = $f->session->userDB;
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']);
		$aval = array();
		if(isset($f->request->data['aval'])){
			foreach ($f->request->data['aval'] as $item){
				$item['_id'] = new MongoId($item['_id']);
				$aval[] = $item;
			}
		}
		if($arrendatario['tipo_enti']=='E'){
			$representante = $f->request->data['representante'];
			$representante['_id'] = new MongoId($representante['_id']);
		}
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']);
		$espacio_old = $f->request->data['espacio_old'];
		$espacio_old['_id'] = new MongoId($espacio_old['_id']);
		$espacio_old['ubic']['local']['_id'] = new MongoId($espacio_old['ubic']['local']['_id']);
		if(!isset($espacio_old['mobiliario']))
			$espacio_old['mobiliario'] = array();
		$arrendamiento = $f->request->data['arrendamiento'];
		if(isset($f->request->data['mobiliario']))
			$mobiliario = $f->request->data['mobiliario'];
		else
			$mobiliario = array();
		$rentas = $f->request->data['letras'];
		$cuentas = $f->request->data['cuentas_cobrar'];
		if($rentas!=null){
			foreach ($rentas as $i=>$rent){
				$rentas[$i]['fecpago'] = new MongoDate(strtotime($rent['fecpago']));
				$rentas[$i]['fecven'] = new MongoDate(strtotime($rent['fecven']));
				$rentas[$i]['fecpro'] = new MongoDate(strtotime($rent['fecpro']));
				$cuentas[$i]['fecreg'] = new MongoDate();
				$cuentas[$i]['observ'] = 'Cobro de <b>Arrendamiento N&deg; '.($i+1).'</b> para <b>'.$espacio['descr'].'</b> de <b>'.$espacio['ubic']['local']['nomb'].'</b>';
				$cuentas[$i]['estado'] = 'P';
				$cuentas[$i]['modulo'] = 'IN';
				$cuentas[$i]['inmueble'] = $espacio;
				$cuentas[$i]['cliente'] = $arrendatario;
				$cuentas[$i]['autor'] = $trabajador;
				if(isset($cuentas[$i]['servicio']['_id']))
					$cuentas[$i]['servicio']['_id'] = new MongoId($cuentas[$i]['servicio']['_id']);
				if(isset($cuentas[$i]['servicio']['organizacion']['_id']))
					$cuentas[$i]['servicio']['organizacion']['_id'] = new MongoId($cuentas[$i]['servicio']['organizacion']['_id']);
				if(isset($cuentas[$i]['fecven']))
					$cuentas[$i]['fecven'] = new MongoDate(strtotime($cuentas[$i]['fecven']));
				foreach ($cuentas[$i]['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuentas[$i]['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuentas[$i]['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuentas[$i]['saldo'])) $cuentas[$i]['saldo'] = floatval($cuentas[$i]['saldo']);
				if(isset($cuentas[$i]['monto'])) $cuentas[$i]['monto'] = floatval($cuentas[$i]['monto']);
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuentas[$i]))->save('insert');
				$rentas[$i]['cuenta_cobrar'] = $cuen->items['_id'];
			}
			$f->model('mg/entidad')->params(array(
				'_id'=>$arrendatario['_id'],
				'rol'=>'roles.cliente'
			))->save('rol');
		}
		//Actualizar Ficha
		$act_fic = $f->model('in/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'arrendatario'=>$arrendatario,
			'trabajador'=>$trabajador,
			'espacio'=>$espacio,
			'observ'=>$arrendamiento['observ'],
			'actualizar_ficha'=>array(
				'anterior'=>array(
					'descr'=>$espacio_old['descr'],
					'uso'=>$espacio_old['uso'],
					'conserv'=>$espacio_old['conserv'],
					'habilitado'=>$espacio_old['habilitado'],
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'valor'=>array(
						'renta'=>$espacio_old['valor']['renta'],
						'moneda'=>$espacio_old['valor']['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$espacio_old['mobiliario']
				),
				'nuevo'=>array(
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'uso'=>$arrendamiento['uso'],
					'conserv'=>$arrendamiento['conserv'],
					'valor'=>array(
						'renta'=>$arrendamiento['renta'],
						'moneda'=>$arrendamiento['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$mobiliario,
					'descr'=>$espacio_old['descr'],
					'habilitado'=>$espacio_old['habilitado']
				)
			)
		)))->save('oper');
		$act_fic = $act_fic->obj;
		//Actualizar Arrendamiento
		$f->model('in/oper')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array(
				'$set'=>array(
					'arrendamiento.estado'=>'F',
					'arrendamiento.ficha'=>$act_fic['_id']
				)
			)
		))->save('upd');
		//Crear Arrendamiento
		if(!isset($arrendamiento['moneda_garantia']))
			$arrendamiento['moneda_garantia'] = $arrendamiento['moneda'];
		$crear_arre = array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'arrendatario'=>$arrendatario,
			'espacio'=>$espacio,
			'observ'=>$arrendamiento['observ'],
			'arrendamiento'=>array(
				'fecocu'=>new MongoDate(strtotime($arrendamiento['fecocu'])),
				'fecven'=>new MongoDate(strtotime($arrendamiento['fecven'])),
				'condicion'=>$arrendamiento['condicion'],
				'tipo'=>'P',
				'estado'=>'A',
				'moneda'=>$arrendamiento['moneda'],
				'renta'=>$arrendamiento['renta'],
				'garantia'=>array(
					'importe'=>$arrendamiento['garantia'],
					'moneda'=>$arrendamiento['moneda_garantia']
				),
				'inmueble_garantia'=>array(
					'descr'=>$arrendamiento['inmueble_garantia'],
					'hipotecado'=>false
				),
				'aval'=>$aval,
				'rentas'=>$rentas,
				'ficha'=>$act_fic['_id']
			)
		);
		if($arrendamiento['contrato']!=''){
			$crear_arre['arrendamiento']['feccon'] = new MongoDate(strtotime($arrendamiento['feccon']));
			$crear_arre['arrendamiento']['contrato'] = $arrendamiento['contrato'];
		}
		if($arrendatario['tipo_enti']=='E'){
			$crear_arre['arrendamiento']['representante'] = $representante;
		}
		$oper_arre = $f->model('in/oper')->params(array('data'=>$crear_arre))->save('oper');
		foreach ($rentas as $rent){
			$f->model('cj/cuen')->params(array(
				'_id'=>$rent['cuenta_cobrar'],
				'data'=>array('$set'=>array('operacion'=>$oper_arre->obj['_id']))
			))->save('custom');
		}
		//Actualizar Arrendatario
		$f->model('in/arre')->params(array(
			'entidad'=>$arrendatario['_id'],
			'espacio'=>array(
				'_id'=>$espacio['_id'],
				'descr'=>$espacio_old['descr']
			)
		))->save('new_espa');
		//Crear Aval
		foreach ($aval as $item){
			$f->model('mg/entidad')->params(array(
				'_id'=>$item['_id'],
				'rol'=>'roles.aval'
			))->save('rol');
		}
		//Crear Representante
		if($arrendatario['tipo_enti']=='E'){
			$f->model('mg/entidad')->params(array(
				'_id'=>$representante['_id'],
				'rol'=>'roles.representante_legal'
			))->save('rol');
		}
		//Actualizar espacio
		$f->model('in/espa')->params(array(
			'espacio'=>$espacio['_id'],
			'ocupado'=>true,
			'renta'=>$arrendamiento['renta'],
			'garantia'=>$arrendamiento['garantia'],
			'moneda'=>$arrendamiento['moneda'],
			'arrendatario'=>$arrendatario,
			'mobiliario'=>$mobiliario
		))->save('act');
		$enti = $arrendatario['nomb'];
		if($arrendatario['tipo_enti']=='P')
			$enti .= ' '.$arrendatario['appat'].' '.$arrendatario['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se renov&oacute; el arrendamiento para el inmueble <b>'.$espacio['descr'].', '.$espacio['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_save_trasp(){
		global $f;
		$trabajador = $f->session->userDB;
		$id = new MongoId($f->request->data['_id']);
		$arrendatario_old = new MongoId($f->request->data['arrendatario_old']);
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']);
		$aval = array();
		if(isset($f->request->data['aval'])){
			foreach ($f->request->data['aval'] as $item){
				$item['_id'] = new MongoId($item['_id']);
				$aval[] = $item;
			}
		}
		if($arrendatario['tipo_enti']=='E'){
			$representante = $f->request->data['representante'];
			$representante['_id'] = new MongoId($representante['_id']);
		}
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']);
		$arrendamiento = $f->request->data['arrendamiento'];
		$arrendamiento['ficha'] = new MongoId($arrendamiento['ficha']);
		$rentas = $f->request->data['letras'];
		$cuentas = $f->request->data['cuentas_cobrar'];
		if($rentas!=null){
			foreach ($rentas as $i=>$rent){
				$rentas[$i]['fecpago'] = new MongoDate(strtotime($rent['fecpago']));
				$rentas[$i]['fecven'] = new MongoDate(strtotime($rent['fecven']));
				$rentas[$i]['fecpro'] = new MongoDate(strtotime($rent['fecpro']));
				$cuentas[$i]['observ'] = 'Cobro de <b>Arrendamiento N&deg; '.($i+1).'</b> para <b>'.$espacio['descr'].'</b> de <b>'.$espacio['ubic']['local']['nomb'].'</b>';
				$cuentas[$i]['fecreg'] = new MongoDate();
				$cuentas[$i]['estado'] = 'P';
				$cuentas[$i]['modulo'] = 'IN';
				$cuentas[$i]['inmueble'] = $espacio;
				$cuentas[$i]['cliente'] = $arrendatario;
				$cuentas[$i]['autor'] = $trabajador;
				if(isset($cuentas[$i]['servicio']['_id']))
					$cuentas[$i]['servicio']['_id'] = new MongoId($cuentas[$i]['servicio']['_id']);
				if(isset($cuentas[$i]['servicio']['organizacion']['_id']))
					$cuentas[$i]['servicio']['organizacion']['_id'] = new MongoId($cuentas[$i]['servicio']['organizacion']['_id']);
				if(isset($cuentas[$i]['fecven']))
					$cuentas[$i]['fecven'] = new MongoDate(strtotime($cuentas[$i]['fecven']));
				foreach ($cuentas[$i]['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id']))
						$cuentas[$i]['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuentas[$i]['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['total'])) $cuentas[$i]['conceptos'][$j]['total'] = floatval($con['total']);
				}
				if(isset($cuentas[$i]['saldo'])) $cuentas[$i]['saldo'] = floatval($cuentas[$i]['saldo']);
				if(isset($cuentas[$i]['total'])) $cuentas[$i]['total'] = floatval($cuentas[$i]['total']);
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuentas[$i]))->save('insert');
				$rentas[$i]['cuenta_cobrar'] = $cuen->items['_id'];
			}
			$f->model('mg/entidad')->params(array(
				'_id'=>$arrendatario['_id'],
				'rol'=>'roles.cliente'
			))->save('rol');
		}
		//Crear Arrendamiento
		if(!isset($arrendamiento['moneda_garantia']))
			$arrendamiento['moneda_garantia'] = $arrendamiento['moneda'];
		$crear_arre = array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'arrendatario'=>$arrendatario,
			'espacio'=>$espacio,
			'observ'=>'',
			'arrendamiento'=>array(
				'fecocu'=>new MongoDate(strtotime($arrendamiento['fecocu'])),
				'fecven'=>new MongoDate(strtotime($arrendamiento['fecven'])),
				'condicion'=>$arrendamiento['condicion'],
				'tipo'=>'R',
				'estado'=>'A',
				'renta'=>$arrendamiento['renta'],
				'moneda'=>$arrendamiento['moneda'],
				'garantia'=>array(
					'importe'=>$arrendamiento['garantia'],
					'moneda'=>$arrendamiento['moneda_garantia']
				),
				'inmueble_garantia'=>array(
					'descr'=>$arrendamiento['inmueble_garantia'],
					'hipotecado'=>false
				),
				'aval'=>$aval,
				'rentas'=>$rentas,
				'ficha'=>$arrendamiento['ficha'],
				'trapaso_desde'=>$id
			)
		);
		if($arrendamiento['contrato']!=''){
			$crear_arre['arrendamiento']['feccon'] = new MongoDate(strtotime($arrendamiento['feccon']));
			$crear_arre['arrendamiento']['contrato'] = $arrendamiento['contrato'];
		}
		if($arrendatario['tipo_enti']=='E'){
			$crear_arre['representante'] = $representante;
		}
		$crea_arre = $f->model('in/oper')->params(array('data'=>$crear_arre))->save('oper');
		$crea_arre = $crea_arre->obj;
		foreach ($rentas as $rent){
			$f->model('cj/cuen')->params(array(
				'_id'=>$rent['cuenta_cobrar'],
				'data'=>array('$set'=>array('operacion'=>$crea_arre['_id']))
			))->save('custom');
		}
		$upd_arrend = array(
			'arrendamiento.estado'=>'F',
			'arrendamiento.traspaso_a'=>$crea_arre['_id']
		);
		$old_oper = $f->model('in/oper')->params(array('_id'=>$id))->get('one')->items;
		foreach ($old_oper['arrendamiento']['rentas'] as $i=>$rent){
			if($rent['estado']=='CR'){
				$upd_arrend['arrendamiento.rentas.'.$i.'.estado'] = 'X';
				$f->model('cj/cuen')->params(array(
					'_id'=>$rent['cuenta_cobrar'],
					'data'=>array('$set'=>array('estado'=>'X'))
				))->save('custom');
			}
		}
		//Actualizar arrendamiento
		$f->model('in/oper')->params(array(
			'_id'=>$id,
			'data'=>array(
				'$set'=>$upd_arrend
			)
		))->save('upd');
		//Actualizar arrendatario antiguo
		$f->model('in/arre')->params(array(
			'_id'=>$arrendatario_old,
			'espacio'=>$espacio['_id']
		))->delete('espacio');
		//Actualizar Arrendatario
		$f->model('in/arre')->params(array(
			'entidad'=>$arrendatario['_id'],
			'espacio'=>array(
				'_id'=>$espacio['_id'],
				'descr'=>$espacio['descr']
			)
		))->save('new_espa');
		//Crear Aval
		foreach ($aval as $item){
			$f->model('mg/entidad')->params(array(
				'_id'=>$item['_id'],
				'rol'=>'roles.aval'
			))->save('rol');
		}
		//Crear Representante
		if($arrendatario['tipo_enti']=='E'){
			$f->model('mg/entidad')->params(array(
				'_id'=>$representante['_id'],
				'rol'=>'roles.representante_legal'
			))->save('rol');
		}
		//Actualizar espacio
		$upd_esp = array(
			'$set'=>array(
				'arrendatario'=>$arrendatario,
				'ocupado'=>true
			)
		);
		$f->model('in/espa')->params(array(
			'_id'=>$espacio['_id'],
			'data'=>$upd_esp
		))->save('upd');
		$enti = $arrendatario['nomb'];
		if($arrendatario['tipo_enti']=='P')
			$enti .= ' '.$arrendatario['appat'].' '.$arrendatario['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se traspas&oacute; el arrendamiento para el inmueble <b>'.$espacio['descr'].', '.$espacio['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_anu_arren(){
		global $f;
		$data = $f->request->data;
		$arre = $f->model("in/oper")->params(array("_id"=>new MongoId($data["_id"])))->get("one")->items;
		//actualizar arrendamiento
		//$arre_modif["arrendamiento.estado"] = "X";
		$arre_modif = array(
			'$set'=>array(
				'arrendamiento.estado'=>'X'
			)
		);
		$oper_arre = $f->model('in/oper')->params(array(
			'_id'=>new MongoId($data["_id"]),
			'data'=>$arre_modif
		))->save('upd');
		//Actualizar Espacio
		$upd_esp = array(
			'$unset'=>array(
				'arrendatario'=>true
			),
			'$set'=>array(
				'ocupado'=>false
			)
		);
		
		$f->model('in/espa')->params(array(
			'_id'=>$arre["espacio"]["_id"],
			'data'=>$upd_esp
		))->save('upd');
		//actualizar arrendatario
		$f->model('in/arre')->params(array(
			'_id'=>$arre['arrendatario']['_id'],
			'espacio'=>$arre["espacio"]["_id"]
		))->delete('espacio');
		$enti = $arre['arrendatario']['nomb'];
		if($arre['arrendatario']['tipo_enti']=='P')
			$enti .= ' '.$arre['arrendatario']['appat'].' '.$arre['arrendatario']['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Arrendamientos',
			'descr'=>'Se anul&oacute; el arrendamiento pendiente para el inmueble <b>'.$arre["espacio"]['descr'].', '.$arre["espacio"]['ubic']['local']['direc'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_new(){
		global $f;
		$f->response->view( 'in/arre.new' );
	}
	function execute_details(){
		global $f;
		$f->response->view( 'in/arre.details' );
	}
	function execute_details_act(){
		global $f;
		$f->response->view( 'in/arre.details.act' );
	}
	function execute_act(){
		global $f;
		$f->response->view( 'in/arre.edit' );
	}
	function execute_desoc(){
		global $f;
		$f->response->view( 'in/arre.desoc' );
	}
	function execute_renov(){
		global $f;
		$f->response->view( 'in/arre.renov' );
	}
	function execute_trasp(){
		global $f;
		$f->response->view( 'in/arre.trasp' );
	}
	function execute_cuen(){
		global $f;
		$f->response->view( 'in/arre.cuen' );
	}
	function execute_select(){
		global $f;
		$f->response->view( 'in/arre.select' );
	}
	function execute_refi(){
		global $f;
		$f->response->view( 'in/arre.refi' );
	}
}
?>