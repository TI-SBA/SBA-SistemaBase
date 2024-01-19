<?php
class Controller_lg_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("lg/repo.grid");
	}
	function execute_index2(){
		global $f;
		$f->response->view("lg/repo.view");
	}
	function execute_cert(){
		global $f;
		$model = $f->model("lg/orde")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$model->items["proveedor"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["proveedor"]["_id"]))->get("one")->items;
			$model->items["cotizacion"] = $f->model('lg/coti')->params(array('_id'=>$model->items["proveedor"]["_id"]))->get('one')->items;
			$model->items["clasificadores"] = array();
			foreach($model->items["orden"]["productos"] as $i=>$prod){
				$model->items["productos"][$i]["producto"] = $f->model("lg/prod")->params(array("_id"=>$prod["producto"]["_id"]))->get("one")->items;
				if(!isset($model->items["clasificadores"][$prod["producto"]["clasif"]["_id"]->{'$id'}])){
					$clas = $f->model("pr/clas")->params(array("_id"=>$prod["producto"]["clasif"]["_id"]))->get("one");
					$model->items["clasificadores"][$prod["producto"]["clasif"]["_id"]->{'$id'}] = $clas->items;
				}
			}
			$model->filtros = array(
				"nro"=>$model->items["certificacion"]["cod"],
				"fecreg"=>$model->items["certificacion"]["fecreg"]
			);
			$f->response->view("lg/repo.cert.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_orde(){
		global $f;
		$model = $f->model("lg/orde")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$model->items["proveedor"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["proveedor"]["_id"]))->get("one")->items;
			$model->items["cotizacion"] = $f->model('lg/coti')->params(array('_id'=>$model->items["proveedor"]["_id"]))->get('one')->items;
			$model->items["clasificadores"] = array();
			foreach($model->items["orden"]["productos"] as $i=>$prod){
				$model->items["productos"][$i]["producto"] = $f->model("lg/prod")->params(array("_id"=>$prod["producto"]["_id"]))->get("one")->items;
				if(!isset($model->items["clasificadores"][$prod["producto"]["clasif"]["_id"]->{'$id'}])){
					$clas = $f->model("pr/clas")->params(array("_id"=>$prod["producto"]["clasif"]["_id"]))->get("one");
					$model->items["clasificadores"][$prod["producto"]["clasif"]["_id"]->{'$id'}] = $clas->items;
				}
			}
			$model->filtros = array(
				"nro"=>$model->items["orden"]["cod"],
				"fecreg"=>$model->items["orden"]["fecreg"]
			);
			$f->response->view("lg/orde.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_orse(){
		global $f;
		$model = $f->model("lg/orse")->params(array("_id"=>new MongoId($f->request->id)))->get("one");	
		if($model->items!=null){
			$model->items["proveedor"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["proveedor"]["_id"]))->get("one")->items;	
			$model->filtros = array(
				"nro"=>$model->items["orden_servicio"]["cod"],
				"fecreg"=>$model->items["orden_servicio"]["fecreg"]
			);
			$f->response->view("lg/orse.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_nota(){
		global $f;
		$model = $f->model("lg/nota")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$model->filtros = array(
				"nro"=>$model->items["cod"],
				"fecreg"=>$model->items["fecreg"]
			);
			$f->response->view("lg/nota.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
/*
	function execute_peco_legacy(){
		global $f;
		$model = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			foreach($model->items["productos"] as $i=>$prod){
				$clas = $f->model("pr/clas")->params(array("_id"=>$prod["producto"]["clasif"]["_id"]))->get("one");
				$model->items["productos"][$i]["producto"]["clasif"] = $clas->items;
			}
			$model->filtros = array(
				"nro"=>$model->items["cod"],
				"fecreg"=>$model->items["fecreg"]
			);
			$f->response->view("lg/peco.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
*/	
	function execute_peco(){
		global $f;
		$model = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			foreach($model->items["productos"] as $i=>$prod){
				$clas = $f->model("pr/clas")->params(array("_id"=>$prod["producto"]["clasif"]["_id"]))->get("one");
				$stck = $f->model("lg/stck")->params(array("almacen"=>$model->items["almacen"]["_id"],"producto"=>$model->items["almacen"]["_id"]))->get("one")->items;
				if(!is_null($stock)){
					$model->items["productos"][$i]["producto"]["clasif"] = $clas->items;
				}
				//$model->items["productos"][$i]["producto"]["model"] = $clas->items;
				$model->items["productos"][$i]["producto"]["clasif"] = $clas->items;
			}
			$model->filtros = array(
				"nro"=>$model->items["cod"],
				"fecreg"=>$model->items["fecreg"]
			);
			$f->response->view("lg/peco.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_pedi(){
		global $f;
		$model = $f->model("lg/pedi")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){			
			$model->filtros = array(
				"nro"=>$model->items["cod"],
				"fecreg"=>$model->items["fecreg"]
			);
			//print_r($model->items);
			switch($model->items['tipo']){
				case "B":
					$f->response->view("lg/pedi.bien.print",$model);
					break;
				case "S":
					$f->response->view("lg/pedi.serv.print",$model);
					break;
				case "L":
					$f->response->view("lg/pedi.loca.print",$model);
					break;
			}
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_bien(){
		global $f;
		$model = $f->model("lg/bien")->params(array("trab"=>new MongoId($f->request->id)))->get("all");
		if($model->items!=null){
			/*$array = array();
			foreach($model->items as $i=>$item){
				if(!isset($array[$item["producto"]["_id"]->{'$id'}])){
					$clas = $f->model("pr/clas")->params(array("_id"=>$prod["producto"]["clasif"]["_id"]))->get("one");
					$item["producto"]["clasif"] = $clas->items;
					$array[$item["producto"]["_id"]->{'$id'}]=$item;
					$array[$item["producto"]["_id"]->{'$id'}]["cantidad"]=1;
					$array[$item["producto"]["_id"]->{'$id'}]["codigos"]=array();
				}else{
					$array[$item["producto"]["_id"]->{'$id'}]["cantidad"]=$array[$item["producto"]["_id"]->{'$id'}]["cantidad"]+1;
				}
				array_push($array[$item["producto"]["_id"]->{'$id'}]["codigos"],$item["producto"]["cod"]);
			}
			$array = array_values($array);*/
			foreach($model->items as $i=>$item){
				$model->items[$i]["responsable"] = $f->model("mg/entidad")->params(array("_id"=>$item["responsable"]["_id"]))->get("one")->items;
				$model->items[$i]["producto"] = $f->model("lg/prod")->params(array("_id"=>$item["producto"]["_id"]))->get("one")->items;
				$model->items[$i]["cantidad"] = 1;
			}
			$f->response->view("lg/bien.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_tact(){
		global $f;
		$model = $f->model("lg/bien")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if($model->items!=null){
			$f->response->view("lg/tact.print",$model);
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_dacu(){
		global $f;
		$model = $f->model("lg/bien")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all");
		//$f->response->json($model);
		if($model->items!=null){
			$array["items"] = array();
			$array["cuentas"] = array();
			foreach($model->items as $item){
				if(!isset($array["items"][$item["producto"]["cuenta"]["_id"]->{'$id'}])){
					$array["items"][$item["producto"]["cuenta"]["_id"]->{'$id'}] = $item["producto"]["cuenta"];
					$array["items"][$item["producto"]["cuenta"]["_id"]->{'$id'}]["items"] = array();					
				}
				array_push($array["items"][$item["producto"]["cuenta"]["_id"]->{'$id'}]["items"],$item);
				/*if(!isset($array["cuentas"][$item["cuenta"]["_id"]->{'$id'}])){
					arra
				}*/
				//array_push($array["cuentas"],$item["cuenta"]);
			}
			$array["filtros"] = array(
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano
			);
			$f->response->view("lg/repo.dacu.print",$array);	 
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_iact14(){
		global $f;
		$model = $f->model("lg/bien")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano,"cuenta"=>new MongoId($f->request->cuenta),"orga"=>new MongoId($f->request->orga)))->get("all");
		if($model->items!=null){
			$model->filtros = array(
				"mes"=>$f->request->mes,
				"ano"=>$f->request->ano
			);
			$f->response->view("lg/repo.iact14.print",$model);	 
		}else{
			$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}
	}
	function execute_cuad(){
		global $f;
		$model = $f->model('lg/cuad')->params(array("periodo"=>"2012"))->get('cons');
		if($model->items!=null){
			foreach ($model->items as $index=>$cuad) {
				$model->items[$index]['organizacion']['sigla'] = $f->model('mg/orga')->param(array('_id'=>$cuad['organizacion']['_id']))->get('one')->items['sigla'];
			}
			$f->response->json($model);
			//$f->response->view("lg/repo.cons_cuad.export",$model);	 
		}else{
			//$f->response->print("No se ha encotrado data disponble para generar este reporte");
		}		
	}
	function execute_resumen_partida(){
		global $f;
		$data = array();
		$saldo_fin = $f->model('lg/sald')->params(array('_id'=>new MongoId($f->request->data['actual'])))->get('one')->items;
		if($f->request->data['pasado']!='0'){
			$saldo_ini = $f->model('lg/sald')->params(array('_id'=>new MongoId($f->request->data['pasado'])))->get('one')->items;
		}
		$params = array(
			'fecreg'=>array(
				'$lte'=>$saldo_fin['fecreg']
			)
		);
		if(isset($f->request->data['almc']))
			$params['almacen._id'] = new MongoId($f->request->data['almc']);	
		if($f->request->data['pasado']!='0')
			$params['fecreg']['$gt'] = $saldo_ini['fecreg'];
		$movs = $f->model('lg/movi')->params(array('filter'=>$params))->get('custom')->items;
		/*
		 * movimientos durante el periodo
		 */
		if($movs!=null){
			foreach ($movs as $movi){
				$prod = $f->model('lg/prod')->params(array('_id'=>$movi['producto']['_id']))->get('one')->items;
				if($f->request->data['tipo']=='CU') $cod = $prod['cuenta']['cod'];
				else $cod = $prod['clasif']['cod'];
				$data[] = array(
					'cod'=>$cod,
					'tipo'=>$movi['tipo'],
					'monto'=>floatval($movi['total'])
				);
			}
		}
		/*
		 * saldos iniciales
		 */
		if($f->request->data['pasado']!='0'){
			$movs = $f->model('lg/auxs')->params(array('filter'=>array(
				'saldo'=>new MongoId($f->request->data['pasado'])
			)))->get('custom')->items;
			foreach ($movs as $movi){
				$prod = $f->model('lg/prod')->params(array('_id'=>$movi['producto']['_id']))->get('one')->items;
				$monto = 0;
				foreach($movi['stock'] as $stck){
					if($stck['almacen']['_id']==new MongoId($f->request->data['almc'])){
						if($f->request->data['tipo']=='CU') $cod = $prod['cuenta']['cod'];
						else $cod = $prod['clasif']['cod'];
						$data[] = array(
							'cod'=>$cod,
							'tipo'=>'I',
							'monto'=>floatval($movi['total'])
						);
					}
				}
			}
		}
		$array = array();
		foreach($data as $item){
			if(!isset($array[$item["cuenta"]])){
				$array[$item["cuenta"]]["cod"] = $item["cuenta"];
				$array[$item["cuenta"]]["sald_ini"] = 0;
				$array[$item["cuenta"]]["debe"] = 0;
				$array[$item["cuenta"]]["haber"] = 0;
			}
			switch($item["tipo"]){
				case "E":
					$array[$item["cuenta"]]["debe"]+=$item["monto"];
					break;
				case "S":
					$array[$item["cuenta"]]["haber"]+=$item["monto"];
					break;
				case "I":
					$array[$item["cuenta"]]["sald_ini"]+=$item["monto"];
					break;
			}
		}
		//print_r($array);die();
		$filtros = array(
			"actual"=>"SALDO AL ".Date::format($saldo_fin["fecreg"]->sec,"d/m/Y")
		);
		if($f->request->data['pasado']!='0'){
			$filtros["anterior"] = "SALDO AL ".Date::format($saldo_ini["fecreg"]->sec,"d/m/Y");
		}else{
			$filtros["anterior"] = "SALDO INICIAL";
		}
		$f->response->view("lg/repo.resu.print",array("items"=>$array,"filtros"=>$filtros));
	}
	function execute_saldo_entrada(){
		global $f;
		$data = array();
		$params = array(
			'tipo'=>'E',
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano']
		);
		if(isset($f->request->data['almc']))
			$params['alma'] = new MongoId($f->request->data['almc']);
		$movs = $f->model('lg/movi')->params($params)->get('periodo')->items;
		if($movs!=null){
			foreach ($movs as $movi){
				$prod = $f->model('lg/prod')->params(array('_id'=>$movi['producto']['_id']))->get('one')->items;
				$doc;
				switch($movi['documento']['tipo']){
					case 'OC': $doc = $f->model('lg/orde')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items; break;
					case 'NE': $doc = $f->model('lg/nota')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items; break;
				}
				if(isset($prod['cuenta']['cod'])){
					if($movi['documento']['tipo']!='NE'){
						$data[$prod['cuenta']['cod']] = array(
							array(
								'monto'=>$movi['total'],
								'organizacion'=>$doc['trabajador']['cargo']['organizacion']
							)
						);
					}else{
						$data[$prod['cuenta']['cod']] = array(
							array(
								'monto'=>$movi['total'],
								'tipo'=>$doc['motivo']
							)
						);
					}
				}else{
					if($movi['documento']['tipo']!='NE'){
						$data[$prod['cuenta']['cod']][] = array(
							'monto'=>$movi['total'],
							'organizacion'=>$doc['trabajador']['cargo']['organizacion']
						);
					}else{
						$data[$prod['cuenta']['cod']][] = array(
							'monto'=>$movi['total'],
							'tipo'=>$doc['motivo']
						);
					}
				}
			}
		}
		print_r(array($data));die();
	}
	function execute_saldo_salida(){
		global $f;
		$data = array();
		$params = array(
			'tipo'=>'S',
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano']
		);
		if(isset($f->request->data['almc']))
			$params['alma'] = new MongoId($f->request->data['almc']);
		$movs = $f->model('lg/movi')->params($params)->get('periodo')->items;
		if($movs!=null){
			foreach ($movs as $movi){
				$prod = $f->model('lg/prod')->params(array('_id'=>$movi['producto']['_id']))->get('one')->items;
				$doc;
				switch($movi['documento']['tipo']){
					case 'PE': $doc = $f->model('lg/peco')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items; break;
				}
				//print_r($doc['responsable']['cargo']['organizacion']);die();
				if(!isset($prod['cuenta']['cod'])){
					$data[$prod['cuenta']['cod']] = array(
						array(
							'monto'=>$movi['total'],
							'organizacion'=>$doc['responsable']['cargo']['organizacion']
						)
					);
				}else{
					$data[$prod['cuenta']['cod']][] = array(
						'monto'=>$movi['total'],
						'organizacion'=>$doc['responsable']['cargo']['organizacion']
					);
				}
			}
		}
		print_r(array($data));die();
	}
	/*
	function execute_listado_saldos(){
		global $f;
		$model = $f->model('lg/stck')->params(array('almacen'=>new MongoId($f->request->data['almacen'])))->get('all')->items;
		$items = array();
		if($model!=null){
			foreach($model as $item){
				$producto = $f->model('lg/prod')->params(array('_id'=>$item['producto']))->get('one')->items;
				if($producto!=null){
					if(isset($producto['clasif']) && isset($producto['cuenta'])){
						if(($producto['clasif']['_id']->{'$id'}==$f->request->data['clasif']) && ($producto['cuenta']['_id']->{'$id'}==$f->request->data['cuenta'])){
							$saldo = $f->model('lg/movi')->params(array(
								'filter'=>array(
									'stock'=>$item['_id'],
									'fecreg'=>array('$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59')))
								),
								'sort'=>array('fecreg'=>-1)
							))->get('custom')->items;
							if($saldo!=null){
								$saldo = $saldo[0];
								$items[$producto['cod']] = array(
									'producto'=>$producto,
									'saldo_cant'=>$saldo['saldo_cant'],
									'saldo_monto'=>$saldo['saldo_monto']
								);
							}
						}
					}
				}
			}
			$filtros = array(
				'cuenta'=>$f->model('ct/pcon')->params(array('_id'=>new MongoId($f->request->data['cuenta'])))->get('one')->items,
				'clasif'=>$f->model('pr/clas')->params(array('_id'=>new MongoId($f->request->data['clasif'])))->get('one')->items,
				'fecfin'=>$f->request->data['fecfin'],
				'almacen'=>$f->model('lg/alma')->params(array('_id'=>new MongoId($f->request->data['almacen'])))->get('one')->items,
			);
			$f->response->view('lg/repo.sald.print', array('items'=>$items,'filtros'=>$filtros));
		}else{
			echo 'No se encontraron resultados';
		}
	}
	*/
	function execute_listado_saldos(){
		global $f;
		$model = $f->model('lg/stck')->params(array('almacen'=>new MongoId($f->request->data['almacen'])))->get('all')->items;
		$items = array();
		if($model!=null){
			foreach($model as $item){
				$producto = $f->model('lg/prod')->params(array('_id'=>$item['producto']))->get('one')->items;
				if($producto!=null){
					if(isset($producto['clasif']) && isset($producto['cuenta'])){
						if(($producto['clasif']['_id']->{'$id'}==$f->request->data['clasif']) && ($producto['cuenta']['_id']->{'$id'}==$f->request->data['cuenta'])){
							$saldo = $f->model('lg/movi')->params(array(
								'filter'=>array(
									'stock'=>$item['_id'],
									'fecreg'=>array('$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59')))
								),
								'sort'=>array('fecreg'=>-1)
							))->get('custom')->items;
							/*En caso que no se tenga el campo stock, tambien realizar la busqueda de movimientos por el campo almacen y por el campo producto y se guarde en la variable saldo_mov*/
							if($saldo==null){
							//echo '<pre>';
							//print_r($item);
							//echo '</pre>';
							//echo '<pre>';
							//print_r($producto);
							//echo '</pre>';
								$saldo_mov = $f->model('lg/movi')->params(array(
									'filter'=>array(
										'fecreg'=>array('$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59'))),
										'producto._id'=> $producto['_id'],
										'cuenta._id'=> $producto['cuenta']['_id'],
										'clasif._id'=>$producto['clasif']['_id'],
									),
									'sort'=>array('fecreg'=>-1)
								))->get('custom')->items;
								//echo '<pre>';
								//print_r($producto['_id']);
								//print_r($producto['cuenta']['_id']);
								//print_r($producto['clasif']['_id']);
								//print_r($saldo_mov);
								//echo '</pre>';
							}
							//if($saldo!=null ){
							if($saldo!=null || $saldo_mov!=null){
								if($saldo_mov!=null) $saldo=$saldo_mov;
								$saldo = $saldo[0];
								//print_r($saldo_mov);
								$items[$producto['cod']] = array(
									'producto'=>$producto,
//									'saldo_cant'=>$saldo['saldo_cant'],
//									'saldo_monto'=>$saldo['saldo_monto']
									'saldo_cant'=>$saldo['cant'],
									'saldo_monto'=>$saldo['saldo']
								);
							}
							unset($saldo);
							unset($saldo_mov);
						}
					}
				}
			}
			//die();
			$filtros = array(
				'cuenta'=>$f->model('ct/pcon')->params(array('_id'=>new MongoId($f->request->data['cuenta'])))->get('one')->items,
				'clasif'=>$f->model('pr/clas')->params(array('_id'=>new MongoId($f->request->data['clasif'])))->get('one')->items,
				'fecfin'=>$f->request->data['fecfin'],
				'almacen'=>$f->model('lg/alma')->params(array('_id'=>new MongoId($f->request->data['almacen'])))->get('one')->items,
			);
			$f->response->view('lg/repo.sald.print', array('items'=>$items,'filtros'=>$filtros));
		}else{
			echo 'No se encontraron resultados';
		}
	}

	function execute_listado_partidas(){
		global $f;
		/*
		 * 
		 */
	}
	function execute_sete(){
		global $f;
		$model = $f->model("lg/sete")->params(array("organizacion"=>new MongoId($f->request->organizacion)))->get("all");
		foreach($model->items as $i=>$item){
			$params = array();
			if(isset($f->request->data["mes"])){
				if($f->request->mes!="0"){
					$params["mes"] = $f->request->mes;
				}
			}
			if(isset($f->request->data["ano"])){
				$params["ano"] = $f->request->ano;
			}
			$params["servicio"] = $item["_id"];
			$model->items[$i]["pagos"] = $f->model("lg/setep")->params($params)->get("all")->items;
		}
		$model->filtros = array(
			"organizacion"=>$f->model("mg/orga")->params(array("_id"=>new MongoId($f->request->organizacion)))->get("one")->items,
			"mes"=>$f->request->mes,
			"ano"=>$f->request->ano
		);
		$f->response->view("lg/repo.sete.print",$model);
	}
	function execute_daot(){
		global $f;
		$model = $f->model("ct/rcom")->params(array("ano"=>$f->request->ano))->get("all_filter");
		//print_r($model);die();
		foreach($model->items as $i=>$item){
			$model->items[$i]["proveedor"] = $f->model("mg/entidad")->params(array("_id"=>$item["proveedor"]["_id"]))->get("one")->items;
		}
		$model->filtros = array(
			"ano"=>$f->request->ano
		);
		$f->response->view("lg/repo.daot.export",$model);
	}
	function execute_movi_depe(){
		global $f;
		$data = array();
		$params = array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano']
		);
		if(isset($f->request->data['almc']))
			$params['alma'] = new MongoId($f->request->data['almc']);
		$movs = $f->model('lg/movi')->params($params)->get('periodo')->items;
		//print_r($movs);die();
		foreach($movs as $movi){
			switch($movi['documento']['tipo']){
				case 'PE':
					$doc = $f->model('lg/peco')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items;
					if($doc['solicitante']['cargo']['organizacion']['_id']==new MongoId($f->request->data['organizacion'])){
						$data[] = array(
							'fecha'=>$movi['fecreg'],
							'documento'=>'PECOSA Nº'.$doc['cod'],
							'producto'=>$movi['producto']['nomb'],
							'entrada'=>'',
							'salida'=>$movi['cant'],
							'precio_unit'=>'S/.'.$movi['precio_unitario'],
							'total'=>'S/.'.$movi['total']
						);
					}
					break;
				case 'OC':
					$doc = $f->model('lg/orde')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items;
					foreach ($doc['productos'] as $item){
						if($item['producto']['_id']==$movi['producto']['_id']){
							foreach ($item['asignacion'] as $asig){
								if($asig['organizacion']['_id']==$movi['organizacion']['_id']){
									$data[] = array(
										'fecha'=>$movi['fecreg'],
										'documento'=>'Orden de Compra Nº'.$doc['cod'],
										'producto'=>$movi['producto']['nomb'],
										'entrada'=>$asig['cantidad'],
										'salida'=>'',
										'precio_unit'=>'S/.'.$item['precio'],
										'total'=>'S/.'.$asig['monto']
									);
								}
							}
						}
					}
					break;
				case 'NE':
					$doc = $f->model('lg/nota')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items;
					if($doc['organizacion']['_id']==new MongoId($f->request->data['organizacion'])){
						foreach ($doc['productos'] as $item){
							if($item['producto']['_id']==$movi['producto']['_id']){
									$data[] = array(
										'fecha'=>$movi['fecreg'],
										'documento'=>'Nota de Entrada Nº'.$doc['cod'],
										'producto'=>$movi['producto']['nomb'],
										'entrada'=>$item['cant'],
										'salida'=>'',
										'precio_unit'=>'S/.'.$item['precio_unit'],
										'total'=>'S/.'.$item['subtotal']
									);
							}
						}
					}
					break;
			}
		}
		//print_r(array($data));die();
		$f->response->view("lg/repo.movi.print",array("items"=>$data));
	}
	function execute_listado_saldo(){
		global $f;
		$data = $f->model('lg/auxs')->params(array(
			'filter'=>array(
				'saldo'=>new MongoId($f->request->data['actual']),
				'producto.cuenta._id'=>new MongoId($f->request->data['cuenta']),
				'producto.stock.almacen._id'=>new MongoId($f->request->data['almc']),
				'producto.clasif._id'=>new MongoId($f->request->data['clasif'])
				//"cuenta"=>$f->request->cuenta['nomb']
			),
			'tipo'=>$f->request->data['tipo'],
			'fields'=>array(
				'producto'=>true
			)
			
		))->get('custom');
		//))->get('custom', $items);
		//print_r($data);die();
		//$f->response->view("lg/repo.lisamenpar.export",array("items"=>$data, "nombres"=>$f->request->cuenta['nomb']));
		$f->response->view("lg/repo.lisamenpar.export", $data);
	}


	function execute_kardex(){
		global $f;
		$data = $f->request->data;
		$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>new MongoId($data['almacen']),"producto"=>new MongoId($data['producto']))))->get('one_custom')->items;
		$params = array(
			'filter'=>array(
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['fecini'].' 00:00:00')),
					'$lte'=>new MongoDate(strtotime($f->request->data['fecfin'].' 23:59:59'))
				),
				'stock'=>$stock['_id']
			),
			'sort'=>array(
				'fecreg'=>1
			)
		);
		$producto = $f->model('lg/prod')->params(array('_id'=>new MongoId($data['producto'])))->get('one')->items; 
		$almacen = $f->model('lg/alma')->params(array('_id'=>new MongoId($data['almacen'])))->get('one')->items;
		$data = $f->model('lg/movi')->params($params)->get('all')->items;
		$params = array(
			'producto'=>$producto,
			'almacen'=>$almacen,
			'items'=>$data
		);
		//print_r($params);die();
		/*$saldo = 0;
		$saldo_monto = 0;
		foreach($data as $item){
			$saldo = $saldo+$item['entrada_cant']-$item['salida_cant'];
			$saldo = floatval($saldo);
			$saldo_monto = $saldo_monto+$item['entrada_monto']-$item['salida_monto'];
			$saldo_monto = floatval($saldo_monto);
			$f->model('lg/movi')->params(array('_id'=>$item['_id'],'data'=>array('saldo_cant'=>$saldo,'saldo_monto'=>$saldo_monto)))->save('update');
			//echo $item['glosa'].'     ==== '.$saldo.'<br/>';
		}
		$f->model('lg/stck')->params(array('_id'=>$stock['_id'],'data'=>array('stock'=>$saldo_cant)))->save('update');*/
		//echo 'de coleccion stock '.$stock['stock'];
		//$producto['stock_calculado'] = $stock;
		//$producto['stock_bd'] = $stock; 

		$f->response->view("lg/repo.kardex_productos.print", $params);
	}
}
?>