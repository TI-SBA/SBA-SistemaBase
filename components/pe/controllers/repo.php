<?php
class Controller_pe_repo extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("pe/repo.grid");
	}
	function execute_index2(){
		global $f;
		$f->response->view("pe/repo.view");
	}
	function execute_inci() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<label>Periodo:</label>&nbsp;<input name="periodo" type="text" size="15">&nbsp;');
		$f->response->print('<select name="tipo_contrato"></select>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>30 ),
			array( "nomb"=>"Trabajador","w"=>300 ),
			array( "nomb"=>"Vacaciones","w"=>120 ),
			array( "nomb"=>"Licencias","w"=>120 ),
			array( "nomb"=>"Permisos","w"=>120 ),
			array( "nomb"=>"Tolerancias","w"=>120 ),
			array( "nomb"=>"Tardanzas","w"=>120 ),
			array( "nomb"=>"Inasistencias","w"=>120 ),
			array( "nomb"=>"Compensaciones","w"=>120 ),
			array( "nomb"=>"Tiempos Extra","w"=>120 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
	}
	function execute_lista_inci(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			'filter'=>array(
				array('nomb'=>'tipo_enti','value'=>'P'),
				array('nomb'=>'roles.trabajador','value'=>array('$exists'=>true)),
				array('nomb'=>'roles.trabajador.contrato.cod','value'=>$f->request->data['tipo'])
			),
			'fields'=>array(
				'nomb'=>true,
				'appat'=>true,
				'apmat'=>true,
				'tipo_enti'=>true
			)
		))->get("search_all");
		if($model->items!=null){
			foreach ($model->items as $i=>$enti){
				$model->items[$i]['inci'] = $f->model("pe/inci")->params(array(
					"enti"=>$enti['_id'],
					'fecini'=>new MongoDate(strtotime($f->request->data['ano'].'-'.$f->request->data['mes'].'-01')),
					'fecfin'=>new MongoDate(strtotime($f->request->data['ano'].'-'.$f->request->data['mes'].'-01 +1 month'))
				))->get("bole")->items;
			}
		}
		$f->response->json( $model->items );
	}
	function execute_details_inci(){
		global $f;
		$f->response->view("pe/repo.inci");
	}
	function execute_conc(){
		global $f;
		$model = $f->model("pe/conc")->params(array("tipo"=>$f->request->tipo))->get("all");
		$f->response->view("pe/repo.conc.export",$model);
	}
	/*
	 * Reporte que jala el concepto y lo clasifica por organizacion
	 * 
	 * Necesita:
	 * -_id de concepto
	 * -periodo mes
	 * periodo ano
	 * _id de contrato
	 */
	function execute_conc_orga(){
		global $f;
		$data = $f->request->data;
		$rpta = array();
		$orga_tmp = array();
		$boles = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		))->get('bole_periodo_all')->items;
		
		
		
		
		
		/*
		 * falta total
		 */
		
		
		
		
		
		foreach ($boles as $bole){
			foreach ($bole['conceptos'] as $conc){
				if($conc['concepto']['_id']->{'$id'}==$data['_id']){
					$index = array_search($bole['trabajador']['cargo']['organizacion']['_id']->{'$id'}, $orga_tmp);
					if($index!==false){
						$ind = -1;
						foreach ($rpta[$index]['contratos'] as $i=>$cont){
							if($cont['contrato']['_id']==$bole['contrato']['_id'])
								$ind = $i;
						}
						if($ind==-1){
							$rpta[$index]['contratos'][] = array(
								'contrato'=>$bole['contrato'],
								'trabajadores'=>array()
							);
							$rpta[$index]['contratos'][sizeof($rpta[$index]['contratos'])-1]['trabajadores'][] = array(
								'trabajador'=>$bole['trabajador'],
								'monto'=>$conc['subtotal']
							);
						}else{
							$rpta[$index]['contratos'][$ind]['trabajadores'][] = array(
								'trabajador'=>$bole['trabajador'],
								'monto'=>$conc['subtotal']
							);
						}
					}else{
						$orga_tmp[] = $bole['trabajador']['cargo']['organizacion']['_id']->{'$id'};
						$tmp = array(
							'organizacion'=>$bole['trabajador']['cargo']['organizacion'],
							'contratos'=>array()
						);
						$tmp['contratos'][] = array(
							'contrato'=>$bole['contrato'],
							'trabajadores'=>array()
						);
						$tmp['contratos'][0]['trabajadores'][] = array(
							'trabajador'=>$bole['trabajador'],
							'monto'=>$conc['subtotal']
						);
						$rpta[] = $tmp;
					}
				}
			}
		}
		$filtros = array(
			"concepto"=>$f->model("pe/conc")->params(array("_id"=>new MongoId($data["_id"])))->get("one")->items,
			"mes"=>$data["mes"],
			"ano"=>$data["ano"]
		);
		$f->response->view("pe/repo.pag_by_conc.print",array("items"=>$rpta,"filtros"=>$filtros));
	}
	function execute_trabs(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"roles"=>'trabajador',
			'estado'=>'H',
			"page"=>1,
			"page_rows"=>9999,
			'sort'=>array('roles.trabajador.cod_tarjeta'=>1)
		))->get("all_trab");
		$f->response->view("pe/repo.trabs.export",$model);
	}
	function execute_leyes(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			//'contrato'=>'1057'
		))->get('bole_periodo_all')->items;
		$array = array();
		$filter = array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		);
		switch($data["tipo"]){
			case "SNP":
				foreach($model as $bole){
					foreach($bole["conceptos"] as $conc){
						if($conc["concepto"]["cod"]=="SNP" || $conc["concepto"]["cod"]=="SNP_276" || $conc["concepto"]["cod"]=="ONP"){
							if(!isset($array[$bole["programa"]["_id"]->{'$id'}])){
								$array[$bole["programa"]["_id"]->{'$id'}]["organizacion"] = $bole["trabajador"]["cargo"]["organizacion"];
								$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"] = array();
							}
							$trab = $bole["trabajador"];
							$trab["monto"] = $conc["subtotal"];
							$trab["glosa"] = $conc["glosa"];
							array_push($array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"],$trab);
						}
					}
				}
				//print_r($array);
				$f->response->view("pe/repo.snp.print",array("items"=>$array,"filtros"=>$filter));
				break;
			case "AFP":
				$conceptos_afp = array(
					"SPP_AP",
					"APORTE_OBLIGATORIO",
					"SPP_COM",
					"COMISION_FLUJO_REMUNERACION",
					"SPP_PR",
					"PRIMA_SEGURO",
					"AFP_PRIMA",
					"SPP_MIX",
					"COMISION_MIXTA_REMUNERACION"
				);
				foreach($model as $bole){
					$trabajador = $f->model("mg/entidad")->params(array("_id"=>$bole["trabajador"]["_id"]))->get("one")->items;
					if($bole["pension"]["tipo"]=="D.L. 19990")continue;
					foreach($bole["conceptos"] as $conc){
						if(in_array($conc["concepto"]["cod"], $conceptos_afp)){
								
							if(!isset($array[$bole["pension"]["_id"]->{'$id'}])){
								$array[$bole["pension"]["_id"]->{'$id'}]["pension"] = $bole["pension"];
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"] = array();
							}
							if(!isset($array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}])){
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["organizacion"] = $bole["programa"];
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"] = array();
							}
							if(!isset($array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}])){
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}] = $trabajador;
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["ap"] = 0;
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["com"] = 0;
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["pr"] = 0;
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["mix"] = 0;
							}
							if($conc["concepto"]["cod"]=="SPP_AP" || $conc["concepto"]["cod"]=="APORTE_OBLIGATORIO"){
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["ap"]+=$conc["subtotal"];
							}elseif($conc["concepto"]["cod"]=="SPP_COM" || $conc["concepto"]["cod"]=="COMISION_FLUJO_REMUNERACION"){
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["com"]+=$conc["subtotal"];
							}elseif($conc["concepto"]["cod"]=="SPP_PR" || $conc["concepto"]["cod"]=="PRIMA_SEGURO" || $conc["concepto"]["cod"]=="AFP_PRIMA"){ 
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["pr"]+=$conc["subtotal"];
							}elseif($conc["concepto"]["cod"]=="SPP_MIX" || $conc["concepto"]["cod"]=="COMISION_MIXTA_REMUNERACION"){
								$array[$bole["pension"]["_id"]->{'$id'}]["organizaciones"][$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["mix"]+=$conc["subtotal"];
							}

						}
					}
				}
				//print_r($array);die();
				$f->response->view("pe/repo.afp.print",array("items"=>$array,"filtros"=>$filter));
				break;
			case "ESS":
				foreach($model as $bole){
					foreach($bole["conceptos"] as $conc){
						if($conc["concepto"]["cod"]=="ESS" || $conc["concepto"]["cod"]=="SEGURO_SOCIAL" || $conc["concepto"]["cod"]=="ESSALUD"){
							if(!isset($array[$bole["programa"]["_id"]->{'$id'}])){
								$array[$bole["programa"]["_id"]->{'$id'}]["organizacion"] = $bole["programa"];
								$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"] = array();
							}
							$trab = $bole["trabajador"];
							$trab["monto"] = $conc["subtotal"];
							$trab["glosa"] = $conc["glosa"];
							array_push($array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"],$trab);
						}
					}
				}
				//print_r($array);
				$f->response->view("pe/repo.ess.print",array("items"=>$array,"filtros"=>$filter));
				break;
		}
	}
	function execute_desc(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			//'contrato'=>'1057'
		))->get('bole_periodo_all')->items;
		$array = array();
		foreach($model as $bole){
			foreach($bole["conceptos"] as $conc){
				if($conc["concepto"]["cod"]=="PP"||$conc["concepto"]["cod"]=="FAL"||$conc["concepto"]["cod"]=="TAR"||$conc["concepto"]["cod"]=="TARDANZAS"){
					if(!isset($array[$bole["programa"]["_id"]->{'$id'}])){
						$array[$bole["programa"]["_id"]->{'$id'}]["organizacion"] = $bole["programa"];
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"] = array();
					}
					if(!isset($array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}])){
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["trabajador"] = $bole["trabajador"];
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["pp"] = 0;
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["fal"] = 0;
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["tar"] = 0;
					}
					if($conc["cod"]=="PP"){
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["pp"]+=$conc["subtotal"];
					}elseif($conc["cod"]=="FAL"){
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["fal"]+=$conc["subtotal"];
					}elseif($conc["cod"]=="TAR"){
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["tar"]+=$conc["subtotal"];
					}
				}
			}
		}
		$filter = array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		);
		$f->response->view("pe/repo.desc.print",array("items"=>$array,"filtros"=>$filter));
	}
	function execute_desc_cont(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			//'contrato'=>'1057'
		))->get('bole_periodo_all')->items;
		$array = array();
		foreach($model as $bole){
			if(!isset($array[$bole["programa"]["_id"]->{'$id'}])){
				$array[$bole["programa"]["_id"]->{'$id'}]["organizacion"] = $bole["programa"];
				$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"] = array();
			}
			if(!isset($array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}])){
				$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["trabajador"] = $bole["trabajador"];
				$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["MIN_PERMISO"] = $bole['maestro']['MIN_PERMISO'];
				$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["DIAS_PERMISO"] = $bole['maestro']['DIAS_PERMISO'];
				$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["MIN_TAR"] = $bole['maestro']['MIN_TAR'];
				$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["DIAS_INA"] = $bole['maestro']['DIAS_INA'];
			}
		}
		$filter = array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		);
		$f->response->view("pe/repo.desc_cont.print",array("items"=>$array,"filtros"=>$filter));
	}
	function execute_desc_otros(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			'contrato'=>'1057'
		))->get('bole_periodo_all')->items;
		switch($data["cond"]){
			case "1":
				$fil_conc = "DSCTO_D";
				$condicion ="OTROS DESCUENTOS AFECTOS";
				break;
			case "2":
				$fil_conc = "DSCTO_ND";
				$condicion ="OTROS DESCUENTOS INAFECTOS";
				break;
		}
		$array = array();
		foreach($model as $bole){
			foreach($bole["conceptos"] as $conc){
				if($conc["concepto"]["cod"]==$fil_conc){
					if(!isset($array[$bole["programa"]["_id"]->{'$id'}])){
						$array[$bole["programa"]["_id"]->{'$id'}]["organizacion"] = $bole["programa"];
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"] = array();
					}
					if(!isset($array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}])){
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["trabajador"] = $bole["trabajador"];
						$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["monto"] = 0;
					}
					$array[$bole["programa"]["_id"]->{'$id'}]["trabajadores"][$bole["trabajador"]["_id"]->{'$id'}]["monto"]+=$conc["subtotal"];
				}
			}
		}
		$filter = array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			'title'=>$condicion
		);
		$f->response->view("pe/repo.desc_otros.print",array("items"=>$array,"filtros"=>$filter));
	}
	function execute_pdt601(){
		global $f;		
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		))->get('bole_periodo_all')->items;
		$array = array();
		foreach($model as $bole){
			foreach($bole["conceptos"] as $conc){
				$concepto = $f->model("pe/conc")->params(array("_id"=>$conc["concepto"]["_id"]))->get("one")->items;
				if(!isset($array[$bole["trabajador"]["_id"]->{'$id'}])){
					$array[$bole["trabajador"]["_id"]->{'$id'}]["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$bole["trabajador"]["_id"]))->get("one")->items;
					$array[$bole["trabajador"]["_id"]->{'$id'}]["conceptos"] = array();
				}
				if(!isset($array[$bole["trabajador"]["_id"]->{'$id'}]["conceptos"][$concepto["cod_sunat"]])){
					$array[$bole["trabajador"]["_id"]->{'$id'}]["conceptos"][$concepto["cod_sunat"]]["cod"] = $concepto["cod_sunat"];
					$array[$bole["trabajador"]["_id"]->{'$id'}]["conceptos"][$concepto["cod_sunat"]]["monto"] = 0;
				}
				$array[$bole["trabajador"]["_id"]->{'$id'}]["conceptos"][$concepto["cod_sunat"]]["monto"]+=$conc["subtotal"];
			}
		}	
		//print_r($array);die();
		/** Out-Data */
		foreach($array as $item){
			$_doc_tipo = "01";
			$_doc_num = "ERROR: NO SE ENCONTRO DNI";
			foreach($item["trabajador"]["docident"] as $doc){
				if($doc["tipo"]=="DNI")$_doc_num=$doc["num"];
			}
			foreach($item["conceptos"] as $conc){
				if($conc["cod"]==""||$conc["cod"]=="0"||floatval($conc["monto"])==0)continue;
				$f->response->print($_doc_tipo."|");
				$f->response->print($_doc_num."|");
				$f->response->print($conc["cod"]."|");
				$f->response->print(number_format($conc["monto"],2,'.','')."|");
				$f->response->print(number_format($conc["monto"],2,'.','')."|");
				$f->response->print("\r\n");
			}
		}
		$mes = $data["mes"];
		if(floatval($mes)<10)$mes = "0".$mes;
		$f->response->download('0601'.$data["ano"].$mes.'20120958136.rem','text/plain');
		/** ./Out Data */
	}
	function execute_ince(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		))->get('bole_periodo_all')->items;
		$array = array();
		foreach($model as $bole){
			
		}
	}
	function execute_esco(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			'contrato'=>'276'
		))->get('bole_periodo_all')->items;
		$array = array();
		foreach($model as $bole){
			foreach($bole["conceptos"] as $conc){
				if($conc["concepto"]["cod"]=="ESCOLARIDAD"){
					$trabajador = $f->model("mg/entidad")->params(array("_id"=>$bole["trabajador"]["_id"]))->get("one")->items;
					$tipo = $trabajador["roles"]["trabajador"]["tipo"];
					if(!isset($array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}])){
						$array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}]["organizacion"] = $bole["trabajador"]["cargo"]["organizacion"];
						$array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}]["trabajadores"] = array();
					}
					if(!isset($array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}]["trabajadores"][$tipo][$bole["trabajador"]["_id"]->{'$id'}])){
						$array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}]["trabajadores"][$tipo][$bole["trabajador"]["_id"]->{'$id'}]["trabajador"] = $bole["trabajador"];
						$array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}]["trabajadores"][$tipo][$bole["trabajador"]["_id"]->{'$id'}]["monto"] = 0;
					}
					$array[$bole["trabajador"]["cargo"]["organizacion"]["_id"]->{'$id'}]["trabajadores"][$tipo][$bole["trabajador"]["_id"]->{'$id'}]["monto"]+=$conc["subtotal"];
				}
			}
		}
		$filter = array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano']
		);
		//print_r($array);die();
		$f->response->view("pe/repo.esco.print",array("items"=>$array,"filtros"=>$filter));
	}
	function execute_planilla_cas_pdf(){
		global $f;
		$data = array(
			'total'=>array()
		);
		$boletas = $f->model('pe/docs')->params(array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano'],
			'contrato'=>'1057',
			//'organizacion'=>new MongoId($f->request->data['orga'])
		))->get('bole_periodo_all')->items;
		$total = $f->model('pe/docs')->params(array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano'],
			//'organizacion'=>new MongoId($f->request->data['orga'])
		))->get('bole_periodo_all')->items;
		
		/*foreach ($total as $item){
			
		}*/
		$data['boletas'] = $boletas;
		$data['totales'] = $total;
		//print_r($data);
		$f->response->view("pe/plan.print", $data);
		//$f->response->view("pe/repo.plani.cas", $data);
	}
	function execute_planilla_cas_excel(){
		global $f;
		$data = array(
			'total'=>array()
		);
		$boletas = $f->model('pe/docs')->params(array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano'],
			'contrato'=>'1057',
			//'organizacion'=>new MongoId($f->request->data['orga'])
		))->get('bole_periodo_all')->items;
		$total = $f->model('pe/docs')->params(array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano'],
			//'organizacion'=>new MongoId($f->request->data['orga'])
		))->get('bole_periodo_all')->items;
		
		/*foreach ($total as $item){
			
		}*/
		$data['boletas'] = $boletas;
		$data['totales'] = $total;
		//print_r($data);
		//$f->response->view("pe/plan.print", $data);
		$f->response->view("pe/repo.plani.cas", $data);
	}
	function execute_asistencia_cas(){
		global $f;
		
	}
	function execute_asistencia_contratados(){
		global $f;
		/*$model = $f->model("mg/entidad")->params(array(
			'filter'=>array(
				array('nomb'=>'tipo_enti','value'=>'P'),
				array('nomb'=>'roles.trabajador','value'=>array('$exists'=>true)),
				//array('nomb'=>'roles.trabajador.contrato.cod','value'=>'276')
			),
			'fields'=>array(
				'nomb'=>true,
				'appat'=>true,
				'apmat'=>true,
				'tipo_enti'=>true
			)
		))->get("search_all");
		if($model->items!=null){
			foreach ($model->items as $i=>$enti){
				$model->items[$i]['inci'] = $f->model("pe/inci")->params(array(
					"enti"=>$enti['_id'],
					'fecini'=>new MongoDate(strtotime($f->request->data['fecini'].' 00:00:00')),
					'fecfin'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59'))
				))->get("bole")->items;
			}
		}*/
		$inci = $f->model("pe/inci")->params(array(
			//"enti"=>$enti['_id'],
			'fecini'=>new MongoDate(strtotime($f->request->data['fecini'].' 00:00:00')),
			'fecfin'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59'))
		))->get("bole");
		/*var_dump($inci->items);
		die();*/
		$tmp = array();
		$trabajadores = array();
		foreach($inci->items as $inc){
			if(!isset($trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}])){
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['programa'] = $inc['trabajador']['programa'];
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'] = array();
			}
			if(!isset($trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}])){
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['trabajador'] = $inc['trabajador'];
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['DIAS_INA'] = 0;
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['MIN_PERMISO'] = 0;
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['MIN_TAR'] = 0;
				switch ($inc['tipo']['tipo']) {
					case 'IN':
						if($inc['tipo']['goce_haber']==false){
							if($inc['tipo']['nomb']!='SUSPENSIÓN'&&$inc['tipo']['nomb']!='Suspension')
								$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['DIAS_INA']++;
						}
						break;
					case 'PE':
						if($in['tipo']['goce_haber']==false){
							if($in['tipo']['todo']==true){
								//$tmp['dia_per'][date('Y-M-d', $in['fecfin']->sec)] = true;
							}else{
								$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['MIN_PERMISO'] += abs(($inc['fecfin']->sec)-($inc['fecini']->sec));
							}
						}
						break;
					case 'TA':
						if($inc['tipo']['goce_haber']==false){
							$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['MIN_TAR'] += abs(($inc['fecfin']->sec)-($inc['fecini']->sec))/60;
						}
						break;
					default:
						# code...
						break;
				}
			}
		}
		/*print_r($trabajadores);
		die();*/
		$f->response->view("pe/repo.desc_cont.print", array('items'=>$trabajadores, 'filtros'=>array()));
	}
	function execute_asistencia_marcacion(){
		global $f;
		$asis = $f->model("pe/asis")->params(array(
			//"enti"=>$enti['_id'],
			'$and'=>array(
				array('ejecutado.entrada.fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['fecini'].' 00:00:00'))
				)),
				array('ejecutado.entrada.fecreg'=>array(
					'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59'))
				))
			)
		))->get("all");
		$trabajadores = array();
		foreach($asis->items as $inc){
			if(!isset($trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}])){
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['programa'] = $inc['trabajador']['programa'];
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'] = array();
			}
			if(!isset($trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}])){
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['trabajador'] = $inc['trabajador'];
				$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['bloques'] = array();
			}
			$trabajadores[$inc['trabajador']['programa']['_id']->{'$id'}]['trabajadores'][$inc['trabajador']['_id']->{'$id'}]['bloques'][] = $inc['ejecutado'];
		}
		//print_r($trabajadores);
		$f->response->view("pe/repo.asis_marcacion.print", array('items'=>$trabajadores, 'filtros'=>array(
			"fecini"=>$f->request->data['fecini'],
			"fecfin"=>$f->request->data['fecfin']
		)));
	}
	function execute_afpnet(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('pe/docs')->params(array(
			'mes'=>$data['mes'],
			'ano'=>$data['ano'],
			//'contrato'=>'276'
		))->get('bole_periodo_all')->items;
		/*var_dump($model);
		die();*/
		$trabajadores = array();
		if($model!=null){
			foreach($model as $i=>$item){
				if(isset($item['pension']) && $item['pension']!=null){
					if($item['pension']['tipo']!='D.L. 19990'){
						$model[$i]["remuneracion_asegurable"] = 0;
						if(isset($item['conceptos'])){
							foreach($item['conceptos'] as $conc){
								if($conc['concepto']['cod']=="REMUNERACION_ASEGURABLE"){
									$model[$i]["remuneracion_asegurable"]=floatval($conc['subtotal']);
								}
							}
						}
						if($model[$i]["remuneracion_asegurable"]>0){
							$trabajadores[] = $model[$i];
						}
					}
				}
			}
		}
		//print_r($trabajadores);die();
		$f->response->view("pe/repo.afpnet.expo", array('data'=>$trabajadores));
	}
}
?>