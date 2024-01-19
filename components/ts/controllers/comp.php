<?php
class Controller_ts_comp extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ts/comp")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		if($model->items!=null){
			foreach ($model->items["items"] as $i=>$item){
				$model->items['items'][$i] = $f->model("ts/ctpp")->params(array(
					"_id"=>$item["cuenta_pagar"]['_id']
				))->get("one")->items;
			}
			foreach ($model->items["beneficiarios"] as $i=>$item){
				$model->items['beneficiarios'][$i]["beneficiario"]["docident"] = $f->model("mg/entidad")->params(array(
					"_id"=>$item["beneficiario"]['_id']
				))->get("one")->items["docident"];
			}
		}
		$f->response->json( $model->items );
	}
	function execute_all_cheques(){
		global $f;
		$mes = (int)$f->request->mes;
		$next_mes = $mes + 1;
		if($mes<10) $mes = '0'+$mes;
		if($next_mes<10) $next_mes = '0'+$next_mes;
		$fields = array(
			"estado"=>$f->request->estado,
			"cuenta_banco"=>new MongoId($f->request->cuenta),
			"tipo_pago"=>$f->request->tipo_pago,
			"desde"=>new MongoDate(strtotime($f->request->ano."-".$mes."-01")),
			"hasta"=>new MongoDate(strtotime($f->request->ano."-".$next_mes."-01"))
		);
		$model = $f->model('ts/comp')->params($fields)->get('all');
		$f->response->json($model);
	}
	function execute_edit_data(){
		global $f;
		$cod = $f->model("ts/comp")->get("cod");
		if($cod->items==null) $cod->items="000001";
		else{
			$tmp = intval($cod->items);
			$tmp++;
			$tmp = (string)$tmp;
			for($i=strlen($tmp); $i<6; $i++){
				$tmp = '0'.$tmp;
			}
			$cod->items = $tmp;
		}
		$f->response->json( array(
			'cod'=>$cod->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"]=new MongoDate();
			$data["cod"]=floatval($data["cod"]);
			$data['autor'] = $f->session->userDB;
			$data["cuenta_banco"]["_id"]=new MongoId($data["cuenta_banco"]["_id"]);
			$data["cuenta_banco"]["cuenta"]["_id"] = new MongoId($data["cuenta_banco"]["cuenta"]["_id"]);
			/******
			* COMPROBANTE DE PAGO MANUAL (CREACION DE CUENTAS POR PAGAR)
			******/
			if(isset($data['manual']))
			{
				$tmp_items = array();
				if(count($data["items"])>0)
				{
					foreach($data["items"] as $i=>$item_ctpp)
					{
						$ctpp = $item_ctpp;
						$ctpp['_id'] = new MongoId();
						$ctpp['autor'] = $f->session->userDBMin;
						$ctpp['total_pago'] = floatval($ctpp['total_pago']);
						$ctpp['total_desc'] = floatval($ctpp['total_desc']);
						$ctpp['total'] = floatval($ctpp['total']);
						if(isset($ctpp['conceptos']))
						{
							foreach ($ctpp['conceptos'] as $j => $ctpp_conc)
							{
								$ctpp['conceptos'][$j]['monto'] = floatval($ctpp['conceptos'][$j]['monto']);
							}
						}
						$f->model("ts/ctpp")->params(array('data'=>$ctpp))->save("insert");

						array_push($tmp_items, array(
							'cuenta_pagar'=>array(
								'_id'=>$ctpp['_id'],
								'motivo'=>$ctpp['motivo']
							),
							'conceptos'=>$ctpp['conceptos']
						));
					}
				}
				$data['items'] = $tmp_items;
			}
			/******
			* ./COMPROBANTE DE PAGO MANUAL (CREACION DE CUENTAS POR PAGAR)
			******/
			for($i=0;$i<count($data["items"]);$i++){
				$data["items"][$i]["cuenta_pagar"]["_id"]=new MongoId($data["items"][$i]["cuenta_pagar"]["_id"]);
				$estado["estado"]="C";
				$f->model("ts/ctpp")->params(array('_id'=>$data["items"][$i]["cuenta_pagar"]["_id"],'data'=>$estado))->save("update");
				$cursor = $f->model("ts/ctpp")->params(array("_id"=>$data["items"][$i]["cuenta_pagar"]["_id"]))->get("one")->items;
				if(isset($cursor["modulo"])){
					if($cursor["modulo"]=="P"){
						for($k=0;$k<count($cursor["modulo"]);$k++){
							$f->model("pe/docs")->params(array('_id'=>$cursor["documentos"][$k],'data'=>array("estado"=>"P")))->save("update");
						}
					}	
				}
				if(isset($data["items"][$i]["cuenta_pagar"]["beneficiario"])) $data["items"][$i]["cuenta_pagar"]["beneficiario"]["_id"]=new MongoId($data["items"][$i]["cuenta_pagar"]["beneficiario"]["_id"]['$id']);
				for($j=0;$j<count($data["items"][$i]["conceptos"]);$j++){
					if(isset($data["items"][$i]["conceptos"][$j]["concepto"])){
						$data["items"][$i]["conceptos"][$j]["concepto"]["_id"]=new MongoId($data["items"][$i]["conceptos"][$j]["concepto"]["_id"]['$id']);
						if(isset($data["items"][$i]["conceptos"][$j]["concepto"]["clasificador"])){
							$data["items"][$i]["conceptos"][$j]["concepto"]["clasificador"]["_id"]=new MongoId($data["items"][$i]["conceptos"][$j]["concepto"]["clasificador"]["_id"]['$id']);
						}else if(isset($data["items"][$i]["conceptos"][$j]["concepto"]["cuenta"])){
							$data["items"][$i]["conceptos"][$j]["concepto"]["cuenta"]["_id"]=new MongoId($data["items"][$i]["conceptos"][$j]["concepto"]["cuenta"]["_id"]['$id']);
						}
					}
					$data["items"][$i]["conceptos"][$j]["monto"]=floatval($data["items"][$i]["conceptos"][$j]["monto"]);
				}
			}
			for($i=0;$i<count($data["beneficiarios"]);$i++){
				$data["beneficiarios"][$i]["beneficiario"]["_id"]=new MongoId($data["beneficiarios"][$i]["beneficiario"]["_id"]['$id']);
			}
			if(isset($data["objeto_gasto"])){
				for($i=0;$i<count($data["objeto_gasto"]);$i++){
					$data["objeto_gasto"][$i]["clasificador"]["_id"]=new MongoId($data["objeto_gasto"][$i]["clasificador"]["_id"]);
					$data["objeto_gasto"][$i]["monto"]=floatval($data["objeto_gasto"][$i]["monto"]);
				}
			}
			if(isset($data["cont_patrimonial"])){
				for($i=0;$i<count($data["cont_patrimonial"]);$i++){
					$data["cont_patrimonial"][$i]["cuenta"]["_id"]=new MongoId($data["cont_patrimonial"][$i]["cuenta"]["_id"]);
					$data["cont_patrimonial"][$i]["monto"]=floatval($data["cont_patrimonial"][$i]["monto"]);
				}
			}
			if(isset($data["cont_presupuestal"])){
				for($i=0;$i<count($data["cont_presupuestal"]);$i++){
					$data["cont_presupuestal"][$i]["cuenta"]["_id"]=new MongoId($data["cont_presupuestal"][$i]["cuenta"]["_id"]);
					$data["cont_presupuestal"][$i]["monto"]=floatval($data["cont_presupuestal"][$i]["monto"]);
				}
			}
			if(isset($data["retenciones"])){
				for($i=0;$i<count($data["retenciones"]);$i++){
					$data["retenciones"][$i]["cuenta"]["_id"]=new MongoId($data["retenciones"][$i]["cuenta"]["_id"]);
					$data["retenciones"][$i]["monto"]=floatval($data["retenciones"][$i]["monto"]);
				}
			}
			$data["fuente"]["_id"]=new MongoId($data["fuente"]["_id"]);
			$tmp = array ();

			/*foreach ($data["cod_programatica"] as $row) 
    			if (!in_array($row,$tmp)) array_push($tmp,$row);
    		$data["cod_programatica"]=$tmp;
    		
			for($i=0;$i<count($data["cod_programatica"]);$i++){
    			$data["cod_programatica"][$i]=array(
    					"funcion"=>array(
    							"_id"=>new MongoId($data["cod_programatica"][$i]["pliego"]["_id"]['$id']),
    							"cod"=>$data["cod_programatica"][$i]["pliego"]["cod"],
    							"nomb"=>$data["cod_programatica"][$i]["pliego"]["nomb"]
    					),
    					"programa"=>array(
    							"_id"=>new MongoId($data["cod_programatica"][$i]["programa"]["_id"]['$id']),
    							"cod"=>$data["cod_programatica"][$i]["programa"]["cod"],
    							"nomb"=>$data["cod_programatica"][$i]["programa"]["nomb"]
    					),
    					"subprograma"=>array(
    							"_id"=>new MongoId($data["cod_programatica"][$i]["subprograma"]["_id"]['$id']),
    							"cod"=>$data["cod_programatica"][$i]["subprograma"]["cod"],
    							"nomb"=>$data["cod_programatica"][$i]["subprograma"]["nomb"]
    					),
    					"actividad"=>array(
    							"_id"=>new MongoId($data["cod_programatica"][$i]["proyecto"]["_id"]['$id']),
    							"cod"=>$data["cod_programatica"][$i]["proyecto"]["cod"],
    							"nomb"=>$data["cod_programatica"][$i]["proyecto"]["nomb"]
    					),
    					"componente"=>array(
    							"_id"=>new MongoId($data["cod_programatica"][$i]["obra"]["_id"]['$id']),
    							"cod"=>$data["cod_programatica"][$i]["obra"]["cod"],
    							"nomb"=>$data["cod_programatica"][$i]["obra"]["nomb"]
    					)
    			);
    		}*/
    		
			$f->model("ts/comp")->params(array('data'=>$data))->save("insert");
			for($i=0;$i<count($data["items"]);$i++){
				$data["items"][$i]["cuenta_pagar"]["_id"]=new MongoId($data["items"][$i]["cuenta_pagar"]["_id"]);
				$comprobante["comprobante"]=$data["_id"];
				$f->model("ts/ctpp")->params(array('_id'=>$data["items"][$i]["cuenta_pagar"]["_id"],'data'=>$comprobante))->save("update");	
			}
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Comprobantes',
				'descr'=>'Se gener&oacute; el Comprobante <b>N &deg; '.$data["cod"].'</b> para <b>'.$data["nomb"].'</b>.'
			))->save('insert');
			$f->response->print($data["_id"]);
		}else{
			$data["cuenta_banco"]["_id"]=new MongoId($data["cuenta_banco"]["_id"]);
			//$data["cuenta_banco"]["cuenta"]["_id"]=new MongoId($data["cuenta_banco"]["cuenta"]["_id"]['$id']);
			$data["cuenta_banco"]["cuenta"]["_id"] = new MongoId($data["cuenta_banco"]["cuenta"]["_id"]);
			for($i=0;$i<count($data["beneficiarios"]);$i++){
				$data["beneficiarios"][$i]["beneficiario"]["_id"]=new MongoId($data["beneficiarios"][$i]["beneficiario"]["_id"]['$id']);
			}
			$f->model("ts/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				"cont_patrimonial"=>array(),
				"cont_presupuestal"=>array()
			)))->save("update");
			if(isset($data["cont_patrimonial"])){
				for($i=0;$i<count($data["cont_patrimonial"]);$i++){
					$data["cont_patrimonial"][$i]["cuenta"]["_id"]=new MongoId($data["cont_patrimonial"][$i]["cuenta"]["_id"]);
					$data["cont_patrimonial"][$i]["monto"]=floatval($data["cont_patrimonial"][$i]["monto"]);
				}
			}
			if(isset($data["cont_presupuestal"])){
				for($i=0;$i<count($data["cont_presupuestal"]);$i++){
					$data["cont_presupuestal"][$i]["cuenta"]["_id"]=new MongoId($data["cont_presupuestal"][$i]["cuenta"]["_id"]);
					$data["cont_presupuestal"][$i]["monto"]=floatval($data["cont_presupuestal"][$i]["monto"]);
				}
			}
			if(isset($data["retenciones"])){
				for($i=0;$i<count($data["retenciones"]);$i++){
					$data["retenciones"][$i]["cuenta"]["_id"]=new MongoId($data["retenciones"][$i]["cuenta"]["_id"]);
					$data["retenciones"][$i]["monto"]=floatval($data["retenciones"][$i]["monto"]);
				}
			}
			$data["fuente"]["_id"]=new MongoId($data["fuente"]["_id"]);
			/*if(isset($data["cod_programatica"])){
				$tmp = array ();
				foreach ($data["cod_programatica"] as $row) 
	    			if (!in_array($row,$tmp)) array_push($tmp,$row);
	    		$data["cod_programatica"]=$tmp;
	    		
				for($i=0;$i<count($data["cod_programatica"]);$i++){
	    			$data["cod_programatica"][$i]=array(
	    					"funcion"=>array(
	    							"_id"=>new MongoId($data["cod_programatica"][$i]["pliego"]["_id"]['$id']),
	    							"cod"=>$data["cod_programatica"][$i]["pliego"]["cod"],
	    							"nomb"=>$data["cod_programatica"][$i]["pliego"]["nomb"]
	    					),
	    					"programa"=>array(
	    							"_id"=>new MongoId($data["cod_programatica"][$i]["programa"]["_id"]['$id']),
	    							"cod"=>$data["cod_programatica"][$i]["programa"]["cod"],
	    							"nomb"=>$data["cod_programatica"][$i]["programa"]["nomb"]
	    					),
	    					"subprograma"=>array(
	    							"_id"=>new MongoId($data["cod_programatica"][$i]["subprograma"]["_id"]['$id']),
	    							"cod"=>$data["cod_programatica"][$i]["subprograma"]["cod"],
	    							"nomb"=>$data["cod_programatica"][$i]["subprograma"]["nomb"]
	    					),
	    					"actividad"=>array(
	    							"_id"=>new MongoId($data["cod_programatica"][$i]["proyecto"]["_id"]['$id']),
	    							"cod"=>$data["cod_programatica"][$i]["proyecto"]["cod"],
	    							"nomb"=>$data["cod_programatica"][$i]["proyecto"]["nomb"]
	    					),
	    					"componente"=>array(
	    							"_id"=>new MongoId($data["cod_programatica"][$i]["obra"]["_id"]['$id']),
	    							"cod"=>$data["cod_programatica"][$i]["obra"]["cod"],
	    							"nomb"=>$data["cod_programatica"][$i]["obra"]["nomb"]
	    					)
	    			);
	    		}
			}*/
			$f->model("ts/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$comp = $f->model("ts/comp")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Comprobantes',
				'descr'=>'Se Modific&oacute; el Comprobante <b>N&deg;'.$comp["cod"].'</b> para <b>'.$comp["nomb"].'</b>.'
			))->save('insert');
			$f->response->print($data["_id"]);
		}				
	}
	function execute_pagar_save(){
		global $f;
		/** Descripcion de errores
		 * 1: No existen Saldos Iniciales
		 * */
		$data = $f->request->data;
		//create Movimiento
		$cursor = $f->model("ts/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$fec = new MongoDate();
		$autor = $f->session->userDB;
		$periodo = gmdate("Ym00", $cursor['fecreg']->sec);
		$peri_mes = intval(gmdate("m", $cursor['fecreg']->sec))."";
		$peri_ano = gmdate("Y", $cursor['fecreg']->sec);
		/*if($cursor["tipo_pago"]=="C"){*///Solo si el tipo de pago es en cheque se Realizan los procesos
			$mov["estado_sunat"]=$data["estado_sunat"];
			$mov["cod_operacion"]=$data["cod_operacion"];
			$mov["descr"]=$data["descr"];
			$mov["entidades"]=$data["entidades"];
			$mov["cuenta"]=$data["cuenta"];
			$mov["cuenta"]["_id"]=new MongoId($mov["cuenta"]["_id"]);
			$mov["tipo"]=$data["tipo"];
			$mov["medio_pago"]=$data["medio_pago"];
			$mov["medio_pago"]["_id"]=new MongoId($mov["medio_pago"]["_id"]);
			$mov["fecreg"] = $cursor["fecreg"]; 
			$mov["cuenta_banco"] = $cursor["cuenta_banco"];
			$saldo = $f->model("ts/saldlibr")->params(array("tipo"=>"C","cuenta"=>$cursor["cuenta_banco"]["_id"],"periodo"=>$periodo))->get("activo")->items;
			if($saldo==null){	
				/* Creamos nuevo Saldo tipo "C" */
				$saldo_last = $f->model('ts/saldlibr')->params(array('tipo'=>'C',"cuenta"=>$cursor["cuenta_banco"]["_id"]))->get('last_rein')->items;
				if($saldo_last==null){
					$f->response->json(array('error'=>1));
					die();
				}
				$saldo = array(
					'tipo'=>'C',
					'periodo'=>$periodo,
					'estado'=>'A',
					'apertura'=>array(
						'fec'=>$fec,
						'autor'=>$autor
					),
					'cuenta_banco'=>$mov["cuenta_banco"],
					'saldo_deudor_inicial'=>$saldo_last['saldo_deudor_final'],
					'saldo_acreedor_inicial'=>$saldo_last['saldo_acreedor_final'],
					'saldo_deudor_final'=>$saldo_last['saldo_deudor_final'],
					'saldo_acreedor_final'=>$saldo_last['saldo_acreedor_final']
				);
				$saldo = $f->model('ts/saldlibr')->params(array('data'=>$saldo))->save('insert')->items;
			}else{
				$mov["saldo"]["_id"] = $saldo["_id"];
				$mov["saldo"]["periodo"] = $saldo["periodo"];
			}		
			$mov["periodo"] = $periodo;
			$mov["monto"] = floatval($data["monto"]);
			$mov["id_operacion"] = $cursor["_id"];
			if($cursor["tipo_pago"]=="C"){
				$mov["documentos"] = array();
				$docs = "";
				$index = 0;
				for($i=0;$i<count($cursor["beneficiarios"]);$i++){
					array_push($mov["documentos"], array(
						"tipo"=>$cursor["tipo_pago"],
						"num"=>$cursor["beneficiarios"][$i]["cheque"]
					));
					if($index>1)$docs .=" ";
					$docs .= $cursor["beneficiarios"][$i]["cheque"];				
					$index++;
				}
				$mov["documentos_libro"] = $docs;
			}
			$f->model('ts/saldlibr')->params(array(
					'filter'=>array('_id'=>$saldo["_id"]),
					'data'=>array('$inc'=>array('saldo_acreedor_final'=>$mov["monto"]))
			))->save('custom');
			$f->model("ts/movcue")->params(array('data'=>$mov))->save("insert");		
			if($cursor["tipo_pago"]=="C"){//Movimiento libro Bancos
				$mlb["cuenta_banco"] = $mov["cuenta_banco"];
				$saldo = $f->model("ts/saldlibr")->params(array("tipo"=>"B","cuenta"=>$mlb["cuenta_banco"]["_id"],"periodo"=>$periodo))->get("activo")->items;
				if($saldo==null){
					//Creamos un nuevo saldo tipo "B"
					$saldo_last = $f->model('ts/saldlibr')->params(array('tipo'=>'B',"cuenta"=>$mlb["cuenta_banco"]["_id"]))->get('last_rein')->items;
					if($saldo_last==null){
						$f->response->json(array('error'=>1));
						die();
					}
					$saldo = array(
							'tipo'=>'B',
							'periodo'=>$periodo,
							'estado'=>'A',
							'apertura'=>array(
								'fec'=>$fec,
								'autor'=>$autor
							),
							'cuenta_banco'=>$mov["cuenta_banco"],
							'saldo_deudor_inicial'=>$saldo_last['saldo_deudor_final'],
							'saldo_acreedor_inicial'=>$saldo_last['saldo_acreedor_final'],
							'saldo_deudor_final'=>$saldo_last['saldo_deudor_final'],
							'saldo_acreedor_final'=>$saldo_last['saldo_acreedor_final']
						);
					$saldo = $f->model('ts/saldlibr')->params(array('data'=>$saldo))->save('insert')->items;
				}else{
					$mlb["saldo"]["_id"] = $saldo["_id"];
					$mlb["saldo"]["periodo"] = $saldo["periodo"];
				}			
				$mlb["periodo"] = $periodo;
				$mlb["fec"] = $cursor["fecreg"];
				$mlb["tipo_doc"] = "C";
				$mlb["num_doc"] = $docs;
				$mlb["tipo_origen"] = "CP";
				$mlb["num_origen"] = $data["cod"];
				$mlb["detalle"] = $data["descr"];
				$mlb["moneda"] = "S";
				$mlb["monto_original"] = $mov["monto"];
				$mlb["tipo"] = "H";
				$mlb["monto"] = $mov["monto"];
				$f->model("ts/moba")->params(array('data'=>$mlb))->save("insert");
				/** Ingrementar el saldo acreedor final */
				$f->model('ts/saldlibr')->params(array(
						'filter'=>array('_id'=>$saldo["_id"]),
						'data'=>array('$inc'=>array('saldo_acreedor_final'=>$mlb["monto"]))
				))->save('custom');
			}	
			//Movimiento Caja Bancos
			$mcb["periodo"]["mes"] = $peri_mes;
			$mcb["periodo"]["anio"] = $peri_ano;
			$mcb["fecreg"] = $cursor["fecreg"];
			$mcb["doc"] = "CP";
			$mcb["num_doc"] = $data["cod"];
			$mcb["concepto"] = $data["descr"];
			if(isset($data["organizacion"])){
				$mcb["organizacion"] = $data["organizacion"];
				for($i=0;$i<count($mcb["organizacion"]);$i++){
					$mcb["organizacion"][$i]["_id"] = new MongoId($mcb["organizacion"][$i]["_id"]);
				}
			}
			$mcb["cuenta_banco"] = $cursor["cuenta_banco"];
			$mcb["cheque"] = str_replace(" ","/",$docs);
			$mcb["cuentas"] = array();
			/** Contabilidad patrimonial */
			$mcb["debe"] = 0;
			$mcb["haber"] = 0;
			if(isset($cursor["cont_patrimonial"])){
				foreach($cursor["cont_patrimonial"] as $cp){
					if($cp["tipo"]=="D"){
						$mcb["debe"] +=floatval($cp["monto"]);
					}elseif($cp["tipo"]=="H"){
						$mcb["haber"] +=floatval($cp["monto"]);
					}
					array_push($mcb["cuentas"], array(
						"tipo"=>$cp["tipo"],
						"cuenta"=>$cp["cuenta"],
						"monto"=>$cp["monto"]
					));
				}
			}
			$f->model("ts/cjba")->params(array('data'=>$mcb))->save("insert");
		/*}*/
		$f->model("ts/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array("estado"=>"C")))->save("update");
		$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Comprobantes',
				'descr'=>'Se Pag&oacute; el Comprobante <b>N&deg;'.$cursor["cod"].'</b> para <b>'.$cursor["nomb"].'</b>.'
			))->save('insert');
		$f->response->print("true");
	}
	function execute_anu2(){
		global $f;
		$data = $f->request->data;
		$f->model("ts/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array("estado"=>"X")))->save("update");
		//create Movimiento
		$cursor = $f->model("ts/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		foreach($cursor["items"] as $item){
			$ctpp = array();
			$ctpp["estado"] = "P";
			$ctpp["comprobante"] = null;
			$f->model("ts/ctpp")->params(array('_id'=>$item['cuenta_pagar']['_id'],'data'=>$ctpp))->save("update");
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'TS',
			'bandeja'=>'Comprobantes',
			'descr'=>'Se Anul&oacute; el Comprobante <b>N&deg;'.$cursor["cod"].'</b> para <b>'.$cursor["nomb"].'</b>.'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/comp.edit");
	}
	function execute_select_cheques(){
		global $f;
		$f->response->view("ts/comp.cheques.select");
	}
	function execute_recti_cheques(){
		global $f;
		$f->response->view("ts/comp.cheques.recti.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/comp.details");
	}
	function execute_pagar(){
		global $f;
		$f->response->view("ts/comp.pagar.edit");
	}	
	function execute_cod_prog(){
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
		$model = array(
			"pliego"=>$pliego,
			"programa"=>$programa,
			"subprograma"=>$subprograma,
			"proyecto"=>$proyecto,
			"obra"=>$obra
		);
		$f->response->json($model);
	}
	function execute_print(){
		global $f;
		$model = $f->model("ts/comp")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			foreach ($model->items["items"] as $i=>$item){
				$model->items['items'][$i] = $f->model("ts/ctpp")->params(array(
					"_id"=>$item["cuenta_pagar"]['_id']
				))->get("one")->items;
			}
			$f->response->view("ts/comp.print",$model);	
		}else{
			$f->response->print("Error: no se ha encontrado el comprobante");
		}
	}
	function execute_preview(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		foreach ($data['cod_programatica'] as $key => $orga) {
			$acti = $orga["actividad"];
			$comp = $orga["componente"];
			$actividad = $f->model("pr/acti")->params(array("_id"=>new MongoId($acti)))->get("one")->items;
			$componente = $f->model("pr/acti")->params(array("_id"=>new MongoId($comp)))->get("one")->items;
			$subprograma = $f->model("pr/estr")->params(array("_id"=>new MongoId($componente["subprograma"]["id"])))->get("one")->items;
			//$proyecto = $f->model("pr/eprog")->params(array("_id"=>new MongoId($componente["proyecto"]["id"])))->get("one");
			$proyecto = $actividad;
			$programa = $f->model("pr/estr")->params(array("_id"=>new MongoId($subprograma["programa"])))->get("one")->items;
			$obra = $componente;
			$pliego = $f->model("pr/estr")->params(array("_id"=>new MongoId($programa["funcion"])))->get("one")->items;
			$data['cod_programatica'][$key] = array(
				"pliego"=>$pliego,
				"programa"=>$programa,
				"subprograma"=>$subprograma,
				"proyecto"=>$proyecto,
				"obra"=>$obra
			);
		}
		$f->response->view("ts/comp.preview",array('data'=>$data,'preview'=>true));	
	}
}
?>