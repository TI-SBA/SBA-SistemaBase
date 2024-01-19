<?php
class Controller_cj_rein extends Controller {
	function execute_index() {
		global $f;
		/*$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Caja</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>150 ),
			array( "nomb"=>"Local","w"=>200 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");*/
	}
	function execute_get_ingresos_heresi(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
				array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
				array('fecreg'=>array('$lte'=>new MongoDate($fecfin))),
				array('modulo'=>array('$in' => array('LM','MH','AD')))


			);
		}
		$items = $f->model("cj/rein")->params($params)->get("all_ingreso")->items;
		$f->response->view("mh/ingresos.diario.php",array('ingreso'=>$items));
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			if($f->request->data['modulo']!='')
				$params['modulo'] = $f->request->data['modulo'];
		$model = $f->model("cj/rein")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("cj/rein")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('cj/rein')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("cj/rein")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_cod(){
		global $f;
		$cod = $f->model("cj/rein")->get("cod");
		if($cod->items==null) $cod->items=1;
		else $cod->items = intval($cod->items)+1;
		$f->response->json(array('cod'=>$cod->items,'fuen'=>$f->model("pr/fuen")->get("all")->items));
	}
	function execute_get_rec(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$comp_tmp1 = $f->model("cj/comp")->params(array("filter"=>array(
			'items.cuenta_cobrar.servicio.organizacion._id'=>new MongoId($f->request->data['orga']),
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		)))->get("all")->items;
		$comp_tmp2 = $f->model("cj/comp")->params(array("filter"=>array(
			'servicio.organizacion._id'=>new MongoId($f->request->data['orga']),
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		)))->get("all")->items;
		if($comp_tmp1!=null&&$comp_tmp2!=null){
			$comp = array_merge($comp_tmp1, $comp_tmp2);
		}else{
			$comp = array();
			if($comp_tmp1!=null)
				$comp = $comp_tmp1;
			if($comp_tmp2!=null)
				$comp = $comp_tmp2;
		}
		/*
		 * EN CASO LA ORGANIZACION SEA CEMENTERIO
		 */
		if($f->request->data['orga']=='51a50f0f4d4a13c409000013'){
			$rede = $f->model("cj/rede")->params(array("filter"=>array(
				'fec_db'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
					'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
				)
			)))->get("all")->items;
		}else{
			$rede = array();
		}
		if($comp!=null){
			foreach($comp as $i=>$co){
				if($co['estado']!='X')
				foreach($co['items'] as $j=>$item){
					foreach($item['conceptos'] as $k=>$conc){
						$comp[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$conc['concepto']['_id']))->get("one")->items;
						/*
						 * Ahora cambia porque siempre tendra cuenta
						if(isset($comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']))
							$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']['cuenta']['_id'];
						else*/
							$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['cuenta']['_id'];
						$comp[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
					}
				}
			}
		}
		$f->response->json(array(
			'prog'=>$prog,
			'comp'=>$comp,
			'rede'=>$rede
		));
	}
	function execute_get_rec_in(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$filter = array(
			'modulo'=>'IN',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		);
		if($f->request->data['tipo_inm']=='A'){
			$filter['playa'] = array('$exists'=>false);
		}elseif($f->request->data['tipo_inm']=='P'){
			$filter['playa'] = array('$exists'=>true);
		}else{
			$filter['playa_azul'] = array('$exists'=>true);
		}

		$filter['sin_pago'] = array('$ne'=>true);
		$comp = $f->model("cj/comp")->params(array("filter"=>$filter))->get("all")->items;
		if($comp!=null){
			foreach($comp as $i=>$co){
				if($co['estado']!='X'){
					if(isset($co['combinar_alq'])){
						$tmp = $f->model('in/cont')->params(array('_id'=>$co['items'][0]['contrato']))->get('one')->items;
						$comp[$i]['inmueble'] = $tmp['inmueble'];
					}
					if(isset($co['items'])){
						foreach($co['items'] as $j=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $k=>$conc){
									if(isset($comp[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
									}else{
										$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['cuenta']['_id'];
									}
									$comp[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
									if(gettype($conc['concepto'])=='array'){
										$comp[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$conc['concepto']['_id']))->get("one")->items;
									}
									/*
									 * Ahora cambia porque siempre tendra cuenta
									if(isset($comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']))
										$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']['cuenta']['_id'];
									else*/
								}
							}elseif(isset($item['items'])){
								foreach ($item['items'] as $l=>$itemm) {
									if(isset($itemm['conceptos'])){
										foreach($itemm['conceptos'] as $k=>$conc){
											if(isset($comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['cuenta'])){
												$cuenta_id = $comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['cuenta']['_id'];
											}else{
												$cuenta_id = $comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['concepto']['cuenta']['_id'];
											}
											$comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
											if(gettype($conc['concepto'])=='array'){
												$comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$conc['concepto']['_id']))->get("one")->items;
											}
											/*
											 * Ahora cambia porque siempre tendra cuenta
											if(isset($comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']))
												$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']['cuenta']['_id'];
											else*/
										}
									}
								}
							}
						}
					}
				}
			}
		}
		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR INMUEBLES Y PLAYAS
		$efields=array(
			'codigo_barras_pdf'=>0,
			'cliente_email_1'=>0,
			'estado_resumen'=>0,
			'estado_baja'=>0,
			'sunat_note'=>0,
			'sunat_description'=>0,
			'sunat_responsecode'=>0,
			'sunat_faultcode'=>0,
			'sunat_soap_error'=>0,
			'digest_value'=>0,
			'signature_value'=>0,
			'codigo_barras'=>0,
			'codigo_barras_pdf'=>0,
			'ruta_zip_firmado'=>0,
			'ruta_xml_firmado'=>0,
			'ruta_cdr_xml'=>0,
			'ruta_pdf'=>0,
			'supplier'=>0,
			'feccon'=>0,
			'autor_con'=>0,
			'conflux_see_id'=>0,
			'tipo_comprobante'=>0,
			'fecmod'=>0,
			'autor_modifcacion'=>0,
		);
		if($f->request->data['tipo_inm']=='A'){
			$inm=array('$in' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar','servicio'));
			#Alquileres utiliza la serie 001
			$serie=array('$in' => array('B001','F001'));
		}elseif($f->request->data['tipo_inm']=='P'){
			$inm=array('$in' => array('servicio'));
			#Playas utiliza la serie 004
			$serie=array('$in' => array('B004','F004'));
		}elseif($f->request->data['tipo_inm']=='PA'){
			$inm=array('$in' => array('servicio'));
			#Playas azules utiliza la serie 006
			$serie=array('$in' => array('B006','F006'));
		}
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			),
			'estado' => array('$in' => array('FI','ES')),
			'tipo' => array('$in' => array('F','B')),
			'items.tipo' => $inm,
			'serie' => $serie,
		);
		$ecom = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$efields,
			'sort'=>array('serie'=>-1,'numero'=>1)
		))->get("all")->items;

		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR OTROS PROGRAMAS CON LA SERIES DE INMUEBLES
		if($f->request->data['tipo_inm']=='A'){
			$inm=array('$ne' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar'));
			#Alquileres utiliza la serie 001
			$serie=array('$in' => array('B001','F001'));
		}elseif($f->request->data['tipo_inm']=='P'){
			$inm=array('$ne' => array('servicio'));
			#Playas utiliza la serie 004
			$serie=array('$in' => array('B004','F004'));
		}elseif($f->request->data['tipo_inm']=='PA'){
			$inm=array('$ne' => array('servicio'));
			#Playas utiliza la serie 006
			$serie=array('$in' => array('B006','F006'));
		}
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			),
			'estado' => 'X',
			'items.tipo' => $inm,
			'serie' => $serie,
		);
		$ecom_anul = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$efields,
			'sort'=>array('numero'=>-1)
		))->get("all")->items;

		if($ecom_anul!=null){
			foreach($ecom_anul as $i=>$co){
				# QUITAR CAMPOS INNECESARIOS DEL COMPROBANTE
				//unset($ecom_anul[$i]['codigo_barras_pdf']);
				//unset($ecom_anul[$i]['cliente_email_1']);
				//unset($ecom_anul[$i]['estado_resumen']);
				//unset($ecom_anul[$i]['estado_baja']);
				//unset($ecom_anul[$i]['sunat_note']);
				//unset($ecom_anul[$i]['sunat_description']);
				//unset($ecom_anul[$i]['sunat_responsecode']);
				//unset($ecom_anul[$i]['sunat_faultcode']);
				//unset($ecom_anul[$i]['sunat_soap_error']);
				//unset($ecom_anul[$i]['digest_value']);
				//unset($ecom_anul[$i]['signature_value']);
				//unset($ecom_anul[$i]['codigo_barras']);
				//unset($ecom_anul[$i]['codigo_barras_pdf']);
				//unset($ecom_anul[$i]['ruta_zip_firmado']);
				//unset($ecom_anul[$i]['ruta_xml_firmado']);
				//unset($ecom_anul[$i]['ruta_cdr_xml']);
				//unset($ecom_anul[$i]['ruta_pdf']);
			}
		}

		if($ecom!=null){
			foreach($ecom as $i=>$co){
				# QUITAR CAMPOS INNECESARIOS DEL COMPROBANTE
				
				if($co['estado']!=='X' || $co['estado']!=='BO' || $co['tipo']!=='RA'){
					if(isset($co['items'])){
						foreach($co['items'] as $j=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $k=>$conc){
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
									}else{
										# No tener cuenta por cobrar es una falla
										echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene cuenta por cobrar en uno de sus conceptos";die();
									}
									$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
									#EN CASO DE CUENTAS_COBRAR
									if($item['tipo']=='cuenta_cobrar'){
										if($conc["descr"]!="IGV"){
											$caja_cuen_cobr=$f->model("cj/cuen")->params(array("_id"=>$conc['cuenta_cobrar']['_id']))->get("one")->items;
											foreach ($caja_cuen_cobr['conceptos'] as $l => $caja_cuen_conc){
												$ecom[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$caja_cuen_conc['concepto']['_id']))->get("one")->items;
											}
										}else{
											$ecom[$i]['items'][$j]['conceptos'][$k]['concepto']="IGV (18%)";
										}
									}
									#EN CASO DE QUE TENGA UN CAMPO CONTRATO, SE OBTIENE EL INMUEBELE (ID Y DIRECCION) (SE ASUME QUE EL COPROBANTE SEA DE UN MISMO INMUEBLE)
									if(isset($conc['alquiler']['contrato'])){
										$inmu_cntr=$f->model("in/cont")->params(array("_id"=>$conc['alquiler']['contrato']))->get("one")->items;
										$ecom[$i]['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
										$ecom[$i]['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
									}
									#EN CASO DE LAS PLAYAS SE MODIFICARA LAS CUETAS POR COBRAR POR EL _ID DE USUARIO QUE REPRESENTARA CADA PLAYA, ESTO SE HACE YA QUE CADA AUTOR REPRESENTA UNA PLAYA.
									# "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON "cuenta._id":ObjectId("51c20a9d4d4a13740b00000d"),
									# "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON "cuenta._id":ObjectId("58629f2f3e6037531d8b4567"),
									# "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON "cuenta._id":ObjectId("51c20adc4d4a13740b00000f"),
									# "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE CON 'cuenta._id' => new MongoId("55846d54cc1e90500900006e"),
									# "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON "cuenta._id":ObjectId("51c20aaf4d4a13c80600000e"),
									# "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON "cuenta._id":ObjectId("51c20abd4d4a13740b00000e"),
									# Reemplazaran esta cuenta
									# "cuenta._id":ObjectId("51acf3314d4a136011000031"),
									#EN CASO DE QUE LA CUENTA IGV SEA LA 2101.010503 "cuenta._id":ObjectId("51a8f8e54d4a13a812000048") CON LA  "cuenta._id": ObjectId("51a8f8ac4d4a13a812000047"),  2101.010501
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
										if(($cuenta_id->{'$id'}=='51acf3314d4a136011000031')){
											if($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbea13e603742398b456a'){
												$cuenta_id=new MongoId('51c20a9d4d4a13740b00000d');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbecf3e603748398b4568'){
												$cuenta_id=new MongoId('55846d54cc1e90500900006e');
												#PLAYA LA PAZ
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71df613e603745448b4568'){
												$cuenta_id=new MongoId('58629f2f3e6037531d8b4567');
												#PLAYA PAUCARPATA
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71dead3e603728448b4568'){
												$cuenta_id=new MongoId('51c20adc4d4a13740b00000f');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbf3b3e603758398b456e'){
												$cuenta_id=new MongoId('51c20aaf4d4a13c80600000e');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbe3e3e603734398b4568'){
												$cuenta_id=new MongoId('51c20abd4d4a13740b00000e');
											}
											$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
										}
										else if(($cuenta_id->{'$id'}=='51a8f8e54d4a13a812000048')){
											$cuenta_id=new MongoId('51a8f8ac4d4a13a812000047');
											$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
										}
									}
								}
							}else{
								# No tener concepto es una falla
								echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";die();
							}
						}
					}
					else{
						# No tener el elemento items es una falla
						echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento items";die();
					}
				}
			}
		}
		//$ecom = array_merge_recursive($ecom, $ecom_anul);

		if (is_null($ecom) && !is_null($ecom_anul)) {
			$ecom = $ecom_anul;
		} elseif (is_null($ecom_anul) && !is_null($ecom)) {
			$ecom = $ecom;
		} elseif (!is_null($ecom_anul) && !is_null($ecom)) {
			$ecom = array_merge_recursive($ecom, $ecom_anul);
		}

		//die();
		#OBTENER LA ULTIMA PLANILLA
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		/*$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items
		));
		*/
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp,
			'ecom'=>$ecom,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items
		));
	}
	function execute_get_rec_fa(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$filter = array(
			'modulo'=>'FA',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		);
		$filter['sin_pago'] = array('$ne'=>true);
		$comp = $f->model("cj/comp")->params(array("filter"=>$filter))->get("all")->items;
		if($comp!=null){
			foreach($comp as $i=>$co){
				if($co['estado']!='X'){
					if(isset($co['combinar_alq'])){
						$tmp = $f->model('in/cont')->params(array('_id'=>$co['items'][0]['contrato']))->get('one')->items;
						$comp[$i]['inmueble'] = $tmp['inmueble'];
					}
					if(isset($co['items'])){
						foreach($co['items'] as $j=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $k=>$conc){
									if(isset($comp[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
									}else{
										$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['cuenta']['_id'];
									}
									$comp[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
									if(gettype($conc['concepto'])=='array'){
										$comp[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$conc['concepto']['_id']))->get("one")->items;
									}
									/*
									 * Ahora cambia porque siempre tendra cuenta
									if(isset($comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']))
										$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']['cuenta']['_id'];
									else*/
								}
							}elseif(isset($item['items'])){
								foreach ($item['items'] as $l=>$itemm) {
									if(isset($itemm['conceptos'])){
										foreach($itemm['conceptos'] as $k=>$conc){
											if(isset($comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['cuenta'])){
												$cuenta_id = $comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['cuenta']['_id'];
											}else{
												$cuenta_id = $comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['concepto']['cuenta']['_id'];
											}
											$comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
											if(gettype($conc['concepto'])=='array'){
												$comp[$i]['items'][$j]['items'][$l]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$conc['concepto']['_id']))->get("one")->items;
											}
											/*
											 * Ahora cambia porque siempre tendra cuenta
											if(isset($comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']))
												$cuenta_id = $comp[$i]['items'][$j]['conceptos'][$k]['concepto']['clasificador']['cuenta']['_id'];
											else*/
										}
									}
								}
							}
						}
					}
				}
			}
		}
		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR INMUEBLES Y PLAYAS
		$efields=array(
			'codigo_barras_pdf'=>0,
			'cliente_email_1'=>0,
			'estado_resumen'=>0,
			'estado_baja'=>0,
			'sunat_note'=>0,
			'sunat_description'=>0,
			'sunat_responsecode'=>0,
			'sunat_faultcode'=>0,
			'sunat_soap_error'=>0,
			'digest_value'=>0,
			'signature_value'=>0,
			'codigo_barras'=>0,
			'codigo_barras_pdf'=>0,
			'ruta_zip_firmado'=>0,
			'ruta_xml_firmado'=>0,
			'ruta_cdr_xml'=>0,
			'ruta_pdf'=>0,
			'supplier'=>0,
			'feccon'=>0,
			'autor_con'=>0,
			'conflux_see_id'=>0,
			'tipo_comprobante'=>0,
			'fecmod'=>0,
			'autor_modifcacion'=>0,
		);
		if($f->request->data['tipo_inm']=='A'){
			$inm=array('$in' => array('pago_meses','pago_parcial','pago_acta','cuenta_cobrar','servicio'));
			#Alquileres utiliza la serie 001
			$serie=array('$in' => array('B002','F002'));
		}elseif($f->request->data['tipo_inm']=='F'){
			$inm=array('$in' => array('farmacia'));
			#Farmacia utiliza la serie 002
			$serie=array('$in' => array('B002','F002','B002','F002'));
		}
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			),
			'estado' => array('$in' => array('FI','ES')),
			'tipo' => array('$in' => array('F','B')),
			'items.tipo' => $inm,
			'serie' => $serie,
		);
		$ecom = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$efields,
			'sort'=>array('serie'=>-1,'numero'=>1)
		))->get("all")->items;
 
		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR OTROS PROGRAMAS CON LA SERIES DE INMUEBLES
		if($f->request->data['tipo_inm']=='F'){
			$inm=array('$ne' => array('farmacia'));
			#Farmacia utiliza la serie 002
			$serie=array('$in' => array('B002','F002'));
		}
		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			),
			'estado' => 'X',
			'items.tipo' => $inm,
			'serie' => $serie,
		);
		$ecom_anul = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$efields,
			'sort'=>array('numero'=>-1)
		))->get("all")->items;

		if($ecom_anul!=null){
			foreach($ecom_anul as $i=>$co){
				# QUITAR CAMPOS INNECESARIOS DEL COMPROBANTE
				//unset($ecom_anul[$i]['codigo_barras_pdf']);
				//unset($ecom_anul[$i]['cliente_email_1']);
				//unset($ecom_anul[$i]['estado_resumen']);
				//unset($ecom_anul[$i]['estado_baja']);
				//unset($ecom_anul[$i]['sunat_note']);
				//unset($ecom_anul[$i]['sunat_description']);
				//unset($ecom_anul[$i]['sunat_responsecode']);
				//unset($ecom_anul[$i]['sunat_faultcode']);
				//unset($ecom_anul[$i]['sunat_soap_error']);
				//unset($ecom_anul[$i]['digest_value']);
				//unset($ecom_anul[$i]['signature_value']);
				//unset($ecom_anul[$i]['codigo_barras']);
				//unset($ecom_anul[$i]['codigo_barras_pdf']);
				//unset($ecom_anul[$i]['ruta_zip_firmado']);
				//unset($ecom_anul[$i]['ruta_xml_firmado']);
				//unset($ecom_anul[$i]['ruta_cdr_xml']);
				//unset($ecom_anul[$i]['ruta_pdf']);
			}
		}

		if($ecom!=null){
			foreach($ecom as $i=>$co){
				# QUITAR CAMPOS INNECESARIOS DEL COMPROBANTE
				//unset($ecom[$i]['codigo_barras_pdf']);
				//unset($ecom[$i]['cliente_email_1']);
				//unset($ecom[$i]['estado_resumen']);
				//unset($ecom[$i]['estado_baja']);
				//unset($ecom[$i]['sunat_note']);
				//unset($ecom[$i]['sunat_description']);
				//unset($ecom[$i]['sunat_responsecode']);
				//unset($ecom[$i]['sunat_faultcode']);
				//unset($ecom[$i]['sunat_soap_error']);
				//unset($ecom[$i]['digest_value']);
				//unset($ecom[$i]['signature_value']);
				//unset($ecom[$i]['codigo_barras']);
				//unset($ecom[$i]['codigo_barras_pdf']);
				//unset($ecom[$i]['ruta_zip_firmado']);
				//unset($ecom[$i]['ruta_xml_firmado']);
				//unset($ecom[$i]['ruta_cdr_xml']);
				//unset($ecom[$i]['ruta_pdf']);
				if($co['estado']!=='X' || $co['estado']!=='BO' || $co['tipo']!=='RA'){
					if(isset($co['items'])){
						foreach($co['items'] as $j=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $k=>$conc){
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
									}else{
										# No tener cuenta por cobrar es una falla
										echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene cuenta por cobrar en uno de sus conceptos";die();
									}
									$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
									#EN CASO DE CUENTAS_COBRAR
									if($item['tipo']=='cuenta_cobrar'){
										if($conc["descr"]!="IGV"){
											$caja_cuen_cobr=$f->model("cj/cuen")->params(array("_id"=>$conc['cuenta_cobrar']['_id']))->get("one")->items;
											foreach ($caja_cuen_cobr['conceptos'] as $l => $caja_cuen_conc){
												$ecom[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$caja_cuen_conc['concepto']['_id']))->get("one")->items;
											}
										}else{
											$ecom[$i]['items'][$j]['conceptos'][$k]['concepto']="IGV (18%)";
										}
									}
									#EN CASO DE QUE TENGA UN CAMPO CONTRATO, SE OBTIENE EL INMUEBELE (ID Y DIRECCION) (SE ASUME QUE EL COPROBANTE SEA DE UN MISMO INMUEBLE)
									if(isset($conc['alquiler']['contrato'])){
										$inmu_cntr=$f->model("in/cont")->params(array("_id"=>$conc['alquiler']['contrato']))->get("one")->items;
										$ecom[$i]['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
										$ecom[$i]['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
									}
									#EN CASO DE LAS PLAYAS SE MODIFICARA LAS CUETAS POR COBRAR POR EL _ID DE USUARIO QUE REPRESENTARA CADA PLAYA, ESTO SE HACE YA QUE CADA AUTOR REPRESENTA UNA PLAYA.
									# "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON "cuenta._id":ObjectId("51c20a9d4d4a13740b00000d"),
									# "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON "cuenta._id":ObjectId("58629f2f3e6037531d8b4567"),
									# "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON "cuenta._id":ObjectId("51c20adc4d4a13740b00000f"),
									# "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE CON 'cuenta._id' => new MongoId("55846d54cc1e90500900006e"),
									# "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON "cuenta._id":ObjectId("51c20aaf4d4a13c80600000e"),
									# "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON "cuenta._id":ObjectId("51c20abd4d4a13740b00000e"),
									# Reemplazaran esta cuenta
									# "cuenta._id":ObjectId("51acf3314d4a136011000031"),
									#EN CASO DE QUE LA CUENTA IGV SEA LA 2101.010503 "cuenta._id":ObjectId("51a8f8e54d4a13a812000048") CON LA  "cuenta._id": ObjectId("51a8f8ac4d4a13a812000047"),  2101.010501
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
										if(($cuenta_id->{'$id'}=='51acf3314d4a136011000031')){
											if($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbea13e603742398b456a'){
												$cuenta_id=new MongoId('51c20a9d4d4a13740b00000d');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbecf3e603748398b4568'){
												$cuenta_id=new MongoId('55846d54cc1e90500900006e');
												#PLAYA LA PAZ
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71df613e603745448b4568'){
												$cuenta_id=new MongoId('58629f2f3e6037531d8b4567');
												#PLAYA PAUCARPATA
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71dead3e603728448b4568'){
												$cuenta_id=new MongoId('51c20adc4d4a13740b00000f');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbf3b3e603758398b456e'){
												$cuenta_id=new MongoId('51c20aaf4d4a13c80600000e');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbe3e3e603734398b4568'){
												$cuenta_id=new MongoId('51c20abd4d4a13740b00000e');
											}
											$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
										}
										else if(($cuenta_id->{'$id'}=='51a8f8e54d4a13a812000048')){
											$cuenta_id=new MongoId('51a8f8ac4d4a13a812000047');
											$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
										}
									}
								}
							}else{
								# No tener concepto es una falla
								echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";die();
							}
						}
					}
					else{
						# No tener el elemento items es una falla
						echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento items";die();
					}
				}
			}
		}
		//$ecom = array_merge_recursive($ecom, $ecom_anul);

		if (is_null($ecom) && !is_null($ecom_anul)) {
			$ecom = $ecom_anul;
		} elseif (is_null($ecom_anul) && !is_null($ecom)) {
			$ecom = $ecom;
		} elseif (!is_null($ecom_anul) && !is_null($ecom)) {
			$ecom = array_merge_recursive($ecom, $ecom_anul);
		}

		//die();
		#OBTENER LA ULTIMA PLANILLA
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		/*$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items
		));
		*/
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp,
			'ecom'=>$ecom,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'FA'))->get('cod')->items
		));
	}
	function execute_get_rec_ecom(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);

		# FILTRAR COMPROBANTES ELECTRONICOS EMITIDOS POR INMUEBLES Y PLAYAS
		$efields=array(
			'codigo_barras_pdf'=>0,
			'cliente_email_1'=>0,
			'estado_resumen'=>0,
			'estado_baja'=>0,
			'sunat_note'=>0,
			'sunat_description'=>0,
			'sunat_responsecode'=>0,
			'sunat_faultcode'=>0,
			'sunat_soap_error'=>0,
			'digest_value'=>0,
			'signature_value'=>0,
			'codigo_barras'=>0,
			'codigo_barras_pdf'=>0,
			'ruta_zip_firmado'=>0,
			'ruta_xml_firmado'=>0,
			'ruta_cdr_xml'=>0,
			'ruta_pdf'=>0,
			'supplier'=>0,
			'feccon'=>0,
			'autor_con'=>0,
			'conflux_see_id'=>0,
			'tipo_comprobante'=>0,
			'fecmod'=>0,
			'autor_modifcacion'=>0,
		);

		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			),
			'estado' => array('$in' => array('FI','ES')),
			'tipo' => array('$in' => array('F','B')),

		);
		$ecom = $f->model("cj/ecom")->params(array(
			"filter"=>$efilter,
			"fields"=>$efields,
			'sort'=>array('serie'=>-1,'numero'=>1)
		))->get("all")->items;

		if($ecom!=null){
			foreach($ecom as $i=>$co){
				if($co['estado']!=='X' || $co['estado']!=='BO' || $co['tipo']!=='RA'){
					if(isset($co['items'])){
						foreach($co['items'] as $j=>$item){
							if(isset($item['conceptos'])){
								foreach($item['conceptos'] as $k=>$conc){
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
									}else{
										# No tener cuenta por cobrar es una falla
										echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene cuenta por cobrar en uno de sus conceptos";die();
									}
									$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
									#EN CASO DE CUENTAS_COBRAR
									if($item['tipo']=='cuenta_cobrar'){
										if($conc["descr"]!="IGV"){
											$caja_cuen_cobr=$f->model("cj/cuen")->params(array("_id"=>$conc['cuenta_cobrar']['_id']))->get("one")->items;
											foreach ($caja_cuen_cobr['conceptos'] as $l => $caja_cuen_conc){
												$ecom[$i]['items'][$j]['conceptos'][$k]['concepto'] = $f->model("cj/conc")->params(array("_id"=>$caja_cuen_conc['concepto']['_id']))->get("one")->items;
											}
										}else{
											$ecom[$i]['items'][$j]['conceptos'][$k]['concepto']="IGV (18%)";
										}
									}
									#EN CASO DE QUE TENGA UN CAMPO CONTRATO, SE OBTIENE EL INMUEBELE (ID Y DIRECCION) (SE ASUME QUE EL COPROBANTE SEA DE UN MISMO INMUEBLE)
									if(isset($conc['alquiler']['contrato'])){
										$inmu_cntr=$f->model("in/cont")->params(array("_id"=>$conc['alquiler']['contrato']))->get("one")->items;
										$ecom[$i]['inmueble']['_id'] = $inmu_cntr['inmueble']['_id'];
										$ecom[$i]['inmueble']['direccion'] = $inmu_cntr['inmueble']['direccion'];
									}
									#EN CASO DE LAS PLAYAS SE MODIFICARA LAS CUETAS POR COBRAR POR EL _ID DE USUARIO QUE REPRESENTARA CADA PLAYA, ESTO SE HACE YA QUE CADA AUTOR REPRESENTA UNA PLAYA.
									# "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON "cuenta._id":ObjectId("51c20a9d4d4a13740b00000d"),
									# "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON "cuenta._id":ObjectId("58629f2f3e6037531d8b4567"),
									# "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON "cuenta._id":ObjectId("51c20adc4d4a13740b00000f"),
									# "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE CON 'cuenta._id' => new MongoId("55846d54cc1e90500900006e"),
									# "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON "cuenta._id":ObjectId("51c20aaf4d4a13c80600000e"),
									# "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON "cuenta._id":ObjectId("51c20abd4d4a13740b00000e"),
									# Reemplazaran esta cuenta
									# "cuenta._id":ObjectId("51acf3314d4a136011000031"),
									#EN CASO DE QUE LA CUENTA IGV SEA LA 2101.010503 "cuenta._id":ObjectId("51a8f8e54d4a13a812000048") CON LA  "cuenta._id": ObjectId("51a8f8ac4d4a13a812000047"),  2101.010501
									if(isset($ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'])){
										$cuenta_id = $ecom[$i]['items'][$j]['conceptos'][$k]['cuenta']['_id'];
										if(($cuenta_id->{'$id'}=='51acf3314d4a136011000031')){
											if($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbea13e603742398b456a'){
												$cuenta_id=new MongoId('51c20a9d4d4a13740b00000d');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbecf3e603748398b4568'){
												$cuenta_id=new MongoId('55846d54cc1e90500900006e');
												#PLAYA LA PAZ
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71df613e603745448b4568'){
												$cuenta_id=new MongoId('58629f2f3e6037531d8b4567');
												#PLAYA PAUCARPATA
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a71dead3e603728448b4568'){
												$cuenta_id=new MongoId('51c20adc4d4a13740b00000f');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbf3b3e603758398b456e'){
												$cuenta_id=new MongoId('51c20aaf4d4a13c80600000e');
											}elseif ($ecom[$i]['autor']['_id']->{'$id'}=='5a5cbe3e3e603734398b4568'){
												$cuenta_id=new MongoId('51c20abd4d4a13740b00000e');
											}
											$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
										}
										else if(($cuenta_id->{'$id'}=='51a8f8e54d4a13a812000048')){
											$cuenta_id=new MongoId('51a8f8ac4d4a13a812000047');
											$ecom[$i]['items'][$j]['conceptos'][$k]['cuenta'] = $f->model("ct/pcon")->params(array("_id"=>$cuenta_id))->get("one")->items;
										}
									}
								}
							}else{
								# No tener concepto es una falla
								echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";die();
							}
						}
					}
					else{
						# No tener el elemento items es una falla
						echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento items";die();
					}
				}
			}
		}
		#OBTENER LA ULTIMA PLANILLA
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'ecom'=>$ecom,
			'conf'=>$f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items
		));
	}
	function execute_get_rec_ho(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$filter = array(
			'modulo'=>$f->request->data['modulo'],
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		);
		if($f->request->data['tipo']=='N'){

			$filter['$and'] = array(
				array('items.cuenta.cod'=>array('$not'=>new MongoRegex("/1201.0301/")))/*,
				array('items.cuenta.cod'=>array('$not'=>new MongoRegex("/4505.010499/"))),
				array('items.cuenta.cod'=>array('$not'=>new MongoRegex("/4505/")))*/
			);

		}else{
			$filter['$or'] = array(
				array('items.cuenta.cod'=>new MongoRegex("/1201.0301/"))/*,
				array('items.cuenta.cod'=>new MongoRegex("/4505.010499/")),
				array('items.cuenta.cod'=>new MongoRegex("/4505/"))*/
			);
		}
		$comp = $f->model("cj/comp")->params(array("filter"=>$filter))->get("all")->items;
		$data = array();
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp
		));
	}
	function execute_get_rec_ho_2(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$filter = array(
			'serie'=>$f->request->data['serie'],
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		);
		if($f->request->data['tipo']=='N'){

			$filter['$and'] = array(
				array('items.cuenta.cod'=>array('$not'=>new MongoRegex("/1201.0301/")))/*,
				array('items.cuenta.cod'=>array('$not'=>new MongoRegex("/4505.010499/"))),
				array('items.cuenta.cod'=>array('$not'=>new MongoRegex("/4505/")))*/
			);

		}else{
			$filter['$or'] = array(
				array('items.cuenta.cod'=>new MongoRegex("/1201.0301/"))/*,
				array('items.cuenta.cod'=>new MongoRegex("/4505.010499/")),
				array('items.cuenta.cod'=>new MongoRegex("/4505/"))*/
			);
		}
		$comp = $f->model("cj/ecomp")->params(array("filter"=>$filter))->get("all")->items;
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp
		));
	}
	/*function execute_get_rec_fa(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$filter = array(
			'serie'=>$f->request->data['serie'],
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		);
		$comp = $f->model("cj/ecom")->params(array("filter"=>$filter))->get("all")->items;
		//print_r($comp);
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp
		));
	}*/
	function execute_get_rec_ag(){
		global $f;
		$acti = $f->request->data["actividad"];
		$comp = $f->request->data["componente"];
		$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
		$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
		$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
		//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
		$proyecto = $actividad;
		$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
		$obra = $componente;
		$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
		$prog = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$filter = array(
			'modulo'=>$f->request->data['modulo'],
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($f->request->data['fec'])),
				'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' +1 day -1 minute'))
			)
		);
		$comp = $f->model("cj/comp")->params(array("filter"=>$filter))->get("all")->items;
		$ult_rein = $f->model("cj/rein")->get("planilla")->items;
		$f->response->json(array(
			'planilla'=>intval($ult_rein)+1,
			'prog'=>$prog,
			'comp'=>$comp
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['local']['_id'])) $data['local']['_id'] = new MongoId($data['local']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("cj/rein")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("cj/rein.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("cj/rein.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("cj/rein.details");
	}
}
?>