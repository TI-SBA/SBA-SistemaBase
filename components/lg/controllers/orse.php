<?php
class Controller_lg_orse extends Controller {
	/*function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['autor']))
			if($f->request->data['autor']!='')
				$params['trabajador'] = $f->session->enti['_id'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/orse")->params($params)->get("lista") );
	}*/
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['autor']))
			if($f->request->data['autor']!='')
				$params['trabajador'] = $f->session->enti['_id'];
		if(isset($f->request->data['etapa']))
			if($f->request->data['etapa']!='')
				$params['etapa'] = $f->request->data['etapa'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("lg/orse")->params($params)->get("lista");
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				if(isset($item['padre'])){
					$padre = $f->model('lg/orse')->params(array('_id'=>$item['padre']['_id']))->get('one')->items;
					//$model->items[$i]['padre'] = $f->model('lg/orse')->params(array('_id'=>$item['padre']['_id']))->get('one')->items;
					if($padre!=null){
						if(!isset($item['solicitud'])){
							if(isset($padre['solicitud'])) $model->items[$i]['solicitud'] = $padre['solicitud'];
						}
						if(!isset($item['certificacion'])){
							if(isset($padre['certificacion'])) $model->items[$i]['certificacion'] = $padre['certificacion'];
						}
						if(!isset($item['orden'])){
							if(isset($padre['orden'])) $model->items[$i]['orden'] = $padre['orden'];
						}
						if(!isset($item['orden_servicio'])){
							if(isset($padre['orden_servicio'])) $model->items[$i]['orden_servicio'] = $padre['orden_servicio'];
						}
					}
				}
			}
		}
		$f->response->json($model);
	}
	function execute_lista_cont(){
		global $f;
		$model = $f->model("lg/orse")->params(array('estado_cont'=>array('$exists'=>true),"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/orse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_edit_data(){
		global $f;
		$cod = $f->model("lg/orse")->get("cod");
		if($cod->items==null) $cod->items="001000";
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
			'cod'=>$cod->items,
			'fuen'=>$f->model("pr/fuen")->get("all")->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		$data['solicitud']['estado'] = "E";
		$data['certificacion']['estado'] = "E";
		$data['orden']['estado'] = "P";
		$data['etapa']= "ORS";
		
		if(isset($data['fecapr'])){
			$data['fecapr'] = new MongoDate();
		}
		if(isset($data['fecenv'])){
			$data['fecenv'] = new MongoDate();
		}
		if(isset($data['estado'])){
			$estado_actual = $data['estado'];
			$data['orden']['estado'] = $estado_actual;
		}

		if(isset($data['fuente']['_id'])) $data['fuente']['_id'] = new MongoId($data['fuente']['_id']);
		if(isset($data['proveedor']['_id'])) $data['proveedor']['_id'] = new MongoId($data['proveedor']['_id']);
		if(isset($data['almacen']['_id'])) $data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
		if(isset($data['almacen']['local']['_id'])) $data['almacen']['local']['_id'] = new MongoId($data['almacen']['local']['_id']);
		if(isset($data['fecent'])) $data['fecent'] = new MongoDate(strtotime($data['fecent']));
		if(isset($data['precio_total'])) $data['precio_total'] = floatval($data['precio_total']);
		if(isset($data['productos'])){
			foreach ($data['productos'] as $index=>$prod){
				if(isset($prod['producto']['_id'])) $data['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
				if(isset($prod['producto']['unidad']['_id'])) $data['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
				if(isset($prod['producto']['clasif']['_id'])) $data['productos'][$index]['producto']['clasif']['_id'] = new MongoId($prod['producto']['clasif']['_id']);
				if(isset($prod['subtotal'])) $data['productos'][$index]['subtotal'] = floatval($prod['subtotal']);
				if(isset($prod['asignacion'])){
					foreach ($prod['asignacion'] as $ind=>$asig){
						if(isset($asig['organizacion']['_id'])) $data['productos'][$index]['asignacion'][$ind]['organizacion']['_id'] = new MongoId($asig['organizacion']['_id']);
						if(isset($asig['organizacion']['actividad'])) $data['productos'][$index]['asignacion'][$ind]['organizacion']['actividad']['_id'] = new MongoId($asig['organizacion']['actividad']['_id']);
						if(isset($asig['organizacion']['componente'])) $data['productos'][$index]['asignacion'][$ind]['organizacion']['componente']['_id'] = new MongoId($asig['organizacion']['componente']['_id']);
						if(isset($asig['monto'])) $data['productos'][$index]['asignacion'][$ind]['monto'] = floatval($asig['monto']);
					}
				}
			}
		}
		if(isset($data['afectacion'])){
			foreach ($data['afectacion'] as $index=>$afect){
				if(isset($afect['organizacion']['_id'])) $data['afectacion'][$index]['organizacion']['_id'] = new MongoId($afect['organizacion']['_id']);
				if(isset($afect['organizacion']['actividad'])) $data['afectacion'][$index]['organizacion']['actividad']['_id'] = new MongoId($afect['organizacion']['actividad']['_id']);
				if(isset($afect['organizacion']['componente'])) $data['afectacion'][$index]['organizacion']['componente']['_id'] = new MongoId($afect['organizacion']['componente']['_id']);
				foreach ($afect['gasto'] as $ind=>$gasto){
					if(isset($gasto['clasif']['_id'])) $data['afectacion'][$index]['gasto'][$ind]['clasif']['_id'] = new MongoId($gasto['clasif']['_id']);
					if(isset($gasto['monto'])) $data['afectacion'][$index]['gasto'][$ind]['monto'] = floatval($gasto['monto']);
				}
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'P';
			if(!isset($data['cod'])){
				$cod = $f->model("lg/orse")->get("cod");
				if($cod->items==null) $cod->items="001000";
				else{
					$tmp = intval($cod->items);
					$tmp++;
					$tmp = (string)$tmp;
					for($i=strlen($tmp); $i<6; $i++){
						$tmp = '0'.$tmp;
					}
					$cod->items = $tmp;
				}
				$data['cod'] = $cod;
			}
			$model = $f->model("lg/orse")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Orden de Compra',
				'descr'=>'Se creó la Orden de Compra <b>'.$data['cod'].'</b>.'
			))->save('insert');
		}else{
			if(isset($data['revision'])){
				$data['revision']['trabajador'] = $f->session->userDB;
				$data['revision']['fec'] = new MongoDate();
				$data['revision']['observ'] = $f->request->data['observ'];
				$f->model("lg/orse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
				if(isset($data['estado'])){
					$f->model("lg/orse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
					$orde = $f->model("lg/orse")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
					if($data['estado']=='A'){
						$f->model("lg/orse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado_cont'=>'P')))->save("update");
					}
				}
			}else{
				$f->model("lg/orse")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			}
			$model = $f->model("lg/orse")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'&Oacute;rdenes de Compra',
				'descr'=>'Se actualiz&oacute; la orden de compra <b>'.$model['cod'].'</b>.'
			))->save('insert');
		}
		$f->response->json($model);
	}
	function execute_cambiar_estado(){
		global $f;
		$data = $f->request->data;
		$update = array();
		if(isset($data['etapa']) && isset($data['estado'])){
			$update[$data['etapa'].'.estado'] = $data['estado'];
		}
		$fecha = new MongoDate();
		$autor = $f->session->userDBMin;

		switch($data['etapa']){
			case "solicitud":
				if($data['estado']=="E"){
					$update[$data['etapa'].'.fecenv'] = $fecha;
					$update[$data['etapa'].'.autenv'] = $autor;
				}else if($data['estado']=="R"){
					$update[$data['etapa'].'.fecrec'] = $fecha;
					$update[$data['etapa'].'.autrec'] = $autor;
				}else if($data['estado']=="A"){
					$update[$data['etapa'].'.fecapr'] = $fecha;
					$update[$data['etapa'].'.autapr'] = $autor;
				}
				break;
			case "certificacion":
				if($data['estado']=="E"){
					$update[$data['etapa'].'.fecenv'] = $fecha;
					$update[$data['etapa'].'.autenv'] = $autor;
				}else if($data['estado']=="R"){
					$update[$data['etapa'].'.fecrec'] = $fecha;
					$update[$data['etapa'].'.autrec'] = $autor;
				}else if($data['estado']=="A"){
					$update[$data['etapa'].'.fecapr'] = $fecha;
					$update[$data['etapa'].'.autapr'] = $autor;
				}
				break;
			case "orden":
				if($data['estado']=="A"){
					$update[$data['etapa'].'.fecapr'] = $fecha;
					$update[$data['etapa'].'.autapr'] = $autor;
				}else if($data['estado']=="E"){
					$update[$data['etapa'].'.fecenv'] = $fecha;
					$update[$data['etapa'].'.autenv'] = $autor;
				}else if($data['estado']=="R"){
					$update[$data['etapa'].'.fecenv'] = $fecha;
					$update[$data['etapa'].'.autenv'] = $autor;
					$recepcionar = $this->ejecutar_entrega($f->request->data['_id']);
					if($recepcionar==false){
						$f->response->json(array('status'=>'error','message'=>'Ha ocurrido un error al recepcionar la orden'));
						die();
					}
				}
				break;
			case "orden_servicio":
				if($data['estado']=="A"){
					$update[$data['etapa'].'.fecapr'] = $fecha;
					$update[$data['etapa'].'.autapr'] = $autor;
				}else if($data['estado']=="E"){
					$update[$data['etapa'].'.fecenv'] = $fecha;
					$update[$data['etapa'].'.autenv'] = $autor;
				}
				break;
			case "recepcion":
				break;
		}
		$model = $f->model('lg/orse')->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$update))->save('update');
		$f->response->json($model);
	}
	function ejecutar_entrega($_id){
		global $f;
		$fecreg = new MongoDate();
		$orden = $f->model('lg/orde')->params(array('_id'=>new MongoId($_id)))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'&Oacute;rdenes de Compra',
			'descr'=>'Se entreg&oacute; la orden de compra <b>'.$orden['orden']['cod'].'</b>.'
		))->save('insert');
		/** Creacion de La Cuenta por Cobrar */
		$cpp["fecreg"]=new MongoDate();
		$cpp["estado"]="P";
		$cpp["autor"]=$orden["trabajador"];
		$cpp["beneficiario"]=$orden["proveedor"];
		$cpp["motivo"]="Orden de Compra N&deg; ".$orden["orden"]["cod"];
		foreach($orden["orden"]["afectacion"] as $in=>$cpp_orga){
			$cpp["conceptos"][$in]["tipo"]="P";
			$cpp["conceptos"][$in]["observ"]=$cpp_orga["programa"]["nomb"];
			$cpp["conceptos"][$in]["moneda"]="S";
			$suma1=0;
			for($i=0;$i<count($cpp_orga["gasto"]);$i++){
				$suma1 = $cpp_orga["gasto"][$i]["monto"] + $suma1;
			}
			$cpp["conceptos"][$in]["monto"]=$suma1;
			$cpp["afectacion"][$in]["programa"] = $cpp_orga["programa"];
			for($k=0;$k<count($cpp_orga["gasto"]);$k++){
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["_id"]=$cpp_orga["gasto"][$k]["clasif"]["_id"];
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["cod"]=$cpp_orga["gasto"][$k]["clasif"]["cod"];
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["descr"]=$cpp_orga["gasto"][$k]["clasif"]["descr"];
				$cpp["afectacion"][$in]["gasto"][$k]["monto"]=$cpp_orga["gasto"][$k]["monto"];
			}
			$cpp["afectacion"][$in]["monto"]=$suma1;
		}
		$cpp["total_pago"]=floatval($orden["orden"]["precio_total"]);
		$cpp["total_desc"]=floatval("0");
		$cpp["total"]=floatval($orden["orden"]["precio_total"]);
		$cpp["documentos"][0]=$orden["_id"];
		$cpp["origen"]="P";
		$cpp["modulo"]="L";
		$f->model("ts/ctpp")->params(array('data'=>$cpp))->save("insert");

		/** ./Creacion de La Cuenta por Cobrar */

		
		foreach($orden['orden']['productos'] as $i=>$prod){
			$tipo_producto = $f->model('lg/prod')->params(array(
				'_id'=>$prod['producto']['_id'],
				'fields'=>array('tipo_producto'=>true)
			))->get('one')->items['tipo_producto'];
			if($tipo_producto!=null){
				if($tipo_producto!='P'){
					$bien = array(
						'fecreg'=>$fecreg,
						'estado'=>'I',
						'tipo'=>$tipo_producto,
						'producto'=>$prod['producto'],
						'almacen'=>$orden['almacen'],
						'valor_inicial'=>floatval($prod['precio']),
						'valor_actual'=>floatval($prod['precio']),
						'entrada'=>array(
							'tipo'=>'OC',
							'forma'=>'Compra',
							'cod'=>$orden['cod'],
							'_id'=>$orden['_id']
						)
					);
					for($j=0; $j<(int)$prod['cant']; $j++){
						unset($bien['_id']);
						$cod = $f->model('lg/bien')->get('cod')->items;
						if($cod==null) $cod="001000";
						else{
							$tmp = intval($cod);
							$tmp++;
							$tmp = (string)$tmp;
							for($i=strlen($tmp); $i<6; $i++){
								$tmp = '0'.$tmp;
							}
							$cod = $tmp;
						}
						$bien['cod'] = $cod;
						$f->model('lg/bien')->params(array('data'=>$bien))->save('insert');
					}
				}
			}
			/*******************************************************
			* OBTENER EL STOCK ACTUAL DEL PRODUCTO
			********************************************************/
			$producto_get = $f->model('lg/prod')->params(array('_id'=>$prod['producto']['_id']))->get('one')->items;
			$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$orden["almacen"]["_id"],"producto"=>$prod['producto']['_id'])))->get('one_custom')->items;
			if($stock==null){
				$stock = array(
					'_id'=>new MongoId(),
					'producto'=>$prod['producto']['_id'],
					'almacen'=>$orden["almacen"]["_id"],
					'stock'=>0,
					'costo'=>0
				);
				$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
			}
			$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>-1)))->get('custom')->items;

			$saldo_cant = 0;
			$saldo_monto = 0;
			if($saldo!=null){
				$saldo_cant = $saldo['saldo_cant'];
				$saldo_monto = $saldo['saldo_monto'];
			}

			$stock_actual = floatval($stock['stock']);
			/*if($producto_get!=null)
			{
				foreach($producto_get['stock'] as $stck)
				{
					if($orden['almacen']['_id']==$stck['almacen']['_id']){
						$stock_actual = floatval($stck['actual']);
					}
				}
			}*/

			/************************************************
			* REGISTRAR EL MOVIMIENTO -> KARDEX
			**************************************************/
			$mov = array(
				'fecreg'=>$fecreg,
				'documento'=>array(
					'_id'=>$orden['_id'],
					'cod'=>$orden['orden']['cod'],
					'tipo'=>'OC'
				),
				'glosa'=>'POR LA ORDEN DE COMPRA NRO. '.$orden['orden']['cod'],
				//'tipo'=>'E',
				'almacen'=>$orden['almacen'],
				'stock'=>$stock['_id'],
				'cant'=>floatval($prod['cant']),
				'entrada_cant'=>floatval($prod['cant']),
				'entrada_monto'=>floatval($prod['subtotal']),
				'salida_cant'=>0,
				'salida_monto'=>0,
				'precio_unitario'=>floatval($prod['precio']),
				'saldo_cant'=>floatval($prod['cant'])+$stock_actual,
				'saldo_monto'=>0,
				'periodo'=>date("Y-m",$fecreg->sec)
				//'total'=>floatval($prod['subtotal'])
			);
			$mov['saldo_cant'] = $saldo_cant+$mov['entrada_cant'];
			$mov['saldo_monto'] = $saldo_monto+$mov['entrada_monto'];
			$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
			$f->model("lg/stck")->params(array(
				'_id'=>$stock['_id'],
				'data'=>array(
					'$inc'=>array(
						'stock'=>floatval($prod['cant'])
					)
				)
			))->save("custom");
		}
		//$f->model("lg/orde")->params(array('_id'=>$orden['_id'],'data'=>$ent))->save("update");
		return true;
		//$f->response->print("true");
	}
	function execute_entrega(){
		global $f;
		$fecreg = new MongoDate();
		$orden = $f->model('lg/orse')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'&Oacute;rdenes de Compra',
			'descr'=>'Se entreg&oacute; la orden de compra <b>'.$orden['nomb'].'</b>.'
		))->save('insert');
		/** Creacion de La Cuenta por Cobrar */
		$cpp["fecreg"]=new MongoDate();
		$cpp["estado"]="P";
		$cpp["autor"]=$orden["trabajador"];
		$cpp["beneficiario"]=$orden["proveedor"];
		$cpp["motivo"]="Orden de Compra N&deg; ".$orden["cod"];
		foreach($orden["afectacion"] as $in=>$cpp_orga){
			$cpp["conceptos"][$in]["tipo"]="P";
			$cpp["conceptos"][$in]["observ"]=$cpp_orga["organizacion"]["nomb"];
			$cpp["conceptos"][$in]["moneda"]="S";
			$suma1=0;
			for($i=0;$i<count($cpp_orga["gasto"]);$i++){
				$suma1 = $cpp_orga["gasto"][$i]["monto"] + $suma1;
			}
			$cpp["conceptos"][$in]["monto"]=$suma1;
			$cpp["afectacion"][$in]["organizacion"] = $cpp_orga["organizacion"];
			for($k=0;$k<count($cpp_orga["gasto"]);$k++){
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["_id"]=$cpp_orga["gasto"][$k]["clasif"]["_id"];
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["cod"]=$cpp_orga["gasto"][$k]["clasif"]["cod"];
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["nomb"]=$cpp_orga["gasto"][$k]["clasif"]["nomb"];
				/*$clasif_one = $f->model("pr/clas")->params(array('_id'=>$cpp_orga["gasto"][$k]["clasif"]["_id"]))->get("one")->items;
				$cpp["afectacion"][$in]["gasto"][$k]["clasificador"]["cuenta"]=$clasif_one["cuenta"];*/
				$cpp["afectacion"][$in]["gasto"][$k]["monto"]=$cpp_orga["gasto"][$k]["monto"];
			}
			$cpp["afectacion"][$in]["monto"]=$suma1;
		}
		$cpp["total_pago"]=floatval($orden["precio_total"]);
		$cpp["total_desc"]=floatval("0");
		$cpp["total"]=floatval($orden["precio_total"]);
		$cpp["documentos"][0]=$orden["_id"];
		$cpp["origen"]="P";
		$cpp["modulo"]="L";
		$f->model("ts/ctpp")->params(array('data'=>$cpp))->save("insert");
		/** ./Creacion de La Cuenta por Cobrar */
		foreach($orden['productos'] as $i=>$prod){
			$tipo_producto = $f->model('lg/prod')->params(array(
				'_id'=>$prod['producto']['_id'],
				'fields'=>array('tipo_producto'=>true)
			))->get('one')->items['tipo_producto'];
			if($tipo_producto!=null){
				if($tipo_producto!='P'){
					$bien = array(
						'fecreg'=>$fecreg,
						'estado'=>'I',
						'tipo'=>$tipo_producto,
						'producto'=>$prod['producto'],
						'almacen'=>$orden['almacen'],
						'valor_inicial'=>floatval($prod['precio']),
						'valor_actual'=>floatval($prod['precio']),
						'entrada'=>array(
							'tipo'=>'OC',
							'forma'=>'Compra',
							'cod'=>$orden['cod'],
							'_id'=>$orden['_id']
						)
					);
					for($j=0; $j<(int)$prod['cant']; $j++){
						unset($bien['_id']);
						$cod = $f->model('lg/bien')->get('cod')->items;
						if($cod==null) $cod="001000";
						else{
							$tmp = intval($cod);
							$tmp++;
							$tmp = (string)$tmp;
							for($i=strlen($tmp); $i<6; $i++){
								$tmp = '0'.$tmp;
							}
							$cod = $tmp;
						}
						$bien['cod'] = $cod;
						$f->model('lg/bien')->params(array('data'=>$bien))->save('insert');
					}
				}
			}
			/*******************************************************
			* OBTENER EL STOCK ACTUAL DEL PRODUCTO
			********************************************************/

			$producto_get = $f->model('lg/prod')->params(array('_id'=>$prod['producto']['_id']))->get('one')->items;
			$stock_actual = 0;
			if($producto_get!=null)
			{
				foreach($producto_get['stock'] as $stck)
				{
					if($orden['almacen']['_id']==$stck['almacen']['_id']){
						$stock_actual = floatval($stck['actual']);
					}
				}
			}
			$mov = array(
				'fecreg'=>$fecreg,
				'documento'=>array(
					'_id'=>$orden['_id'],
					'cod'=>$orden['cod'],
					'tipo'=>'OC'
				),
				'tipo'=>'E',
				'almacen'=>$orden['almacen'],
				'producto'=>$prod['producto'],
				'cant'=>floatval($prod['cant']),
				'precio_unitario'=>floatval($prod['precio']),
				'saldo'=>floatval($prod['cant'])+$stock_actual,
				'total'=>floatval($prod['subtotal'])
			);
			$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
			$f->model("lg/prod")->params(array(
				'prod'=>$prod['producto']['_id'],
				'cant'=>$prod['cant'],
				'total'=>doubleval($prod['subtotal']),
				'precio_unit'=>doubleval($prod['precio']),
				'almacen'=>$orden['almacen']
			))->save("stock_add");
		}
		$ent = array(
			'estado'=>'R',
			'recepcion'=>array(
				'fec'=>$fecreg,
				'observ'=>$f->request->data['observ'],
				'trabajador'=>$f->session->userDB
			)
		);
		$f->model("lg/orse")->params(array('_id'=>$orden['_id'],'data'=>$ent))->save("update");
		$f->response->print("true");
	}
	function execute_save_cont(){
		global $f;
		$data = $f->request->data;
		if(isset($data["auxs"])){
			foreach($data["auxs"] as $i=>$aux){
				if(isset($data["auxs"][$i]["programa"])){
					$data["auxs"][$i]["programa"]["_id"] = new MongoId($data["auxs"][$i]["programa"]["_id"]);
				}
				$data["auxs"][$i]["cuenta"]["_id"] = new MongoId($data["auxs"][$i]["cuenta"]["_id"]);
			}
		}
		$f->model("lg/orse")->params(array("_id"=>new MongoId($f->request->data["_id"]),"data"=>$data))->save("update");
		$orden = $f->model('lg/orse')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CT',
			'bandeja'=>'&Oacute;rdenes de Servicio',
			'descr'=>'Se asignaron las cuentas contables para la orden de servicio <b>'.$orden['cod'].'</b>.'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_apro_cont(){
		global $f;
		$orde = $f->model('lg/orse')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		foreach ($orde['auxs'] as $auxs){
			$periodo = array(
				'ano'=>date('Y', $orde['fecreg']->sec),
				'mes'=>date('m', $orde['fecreg']->sec)
			);
			$params = array(
				'ano'=>$periodo['ano'],
				'mes'=>$periodo['mes'],
				'sub_cuenta'=>$auxs['cuenta']['_id']
			);
			if(isset($auxs['programa']['_id']))
				$params['programa'] = $auxs['programa']['_id'];
			$saldo = $f->model('ct/saux')->params($params)->get('saldo')->items;
			if($saldo==null){
				$saldo = array(
					'periodo'=>array(
						'ano'=>$periodo['ano'],
						'mes'=>$periodo['mes']
					),
					'estado'=>'A',
					'programa'=>$auxs['programa'],
					'cuenta'=>$auxs['cuenta'],
					'debe_inicial'=>0,
					'haber_inicial'=>0
				);
				if(isset($auxs['programa'])) $saldo['programa'] = $auxs['programa'];
				if($periodo['mes']!='1'){
					$params['mes'] = intval($params['mes']) - 1;
					$saldo_old = $f->model('ct/saux')->params($params)->get('saldo')->items;
					if($saldo_old!=null){
						$saldo['debe_inicial'] = floatval($saldo_old['debe_final']);
						$saldo['haber_inicial'] = floatval($saldo_old['haber_final']);
					}
				}
				$saldo = $f->model('ct/saux')->params(array('data'=>$saldo))->save('insert')->items;
			}
			$auxi = array(
				'fecreg'=>new MongoDate(),
				'autor'=>$f->session->userDB,
				'estado'=>'A',
				'saldos'=>array(
					'_id'=>$saldo['_id'],
					'periodo'=>$saldo['periodo'],
					'sub_cuenta'=>$saldo['sub_cuenta']
				),
				'fec'=>new MongoDate(),
				'clase'=>'OS',
				'num'=>$orde['cod'],
				'detalle'=>$orde['observ'],
				'tipo'=>$auxs['tipo'],
				'tipo_saldo'=>$auxs['saldo'],
				'monto'=>floatval($auxs['monto'])
			);
			if(isset($saldo['organizacion'])) $auxi['saldos']['organizacion'] = $saldo['organizacion'];
			$f->model("ct/auxs")->params(array('data'=>$auxi))->save("insert");
		}
		$orden = $f->model('lg/orde')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CT',
			'bandeja'=>'&Oacute;rdenes de Servicio',
			'descr'=>'Se aprob&oacute; la creaci&oacute;n de los auxiliares standar para la orden de servicio <b>'.$orden['cod'].'</b>.'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/orse.edit");
	}
	function execute_edit_veos(){
		global $f;
		$f->response->view("lg/orse.edit.cont");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/orse.details");
	}
	function execute_asig(){
		global $f;
		$f->response->view("lg/orse.asignar");
	}
}
?>