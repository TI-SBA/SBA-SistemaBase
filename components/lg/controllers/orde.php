<?php
class Controller_lg_orde extends Controller {
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
		$model = $f->model("lg/orde")->params($params)->get("lista");
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				if(isset($item['padre'])){
					$padre = $f->model('lg/orde')->params(array('_id'=>$item['padre']['_id']))->get('one')->items;
					//$model->items[$i]['padre'] = $f->model('lg/orde')->params(array('_id'=>$item['padre']['_id']))->get('one')->items;
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
		$filter = array('estado_cont'=>array('$exists'=>true));
		$model = $f->model("lg/orde")->params(array("filter"=>$filter,"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/orde")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($items['padre'])){
			$padre = $f->model('lg/orde')->params(array('_id'=>$items['padre']['_id']))->get('one')->items;
			//$model->items[$i]['padre'] = $f->model('lg/orde')->params(array('_id'=>$item['padre']['_id']))->get('one')->items;
			if($padre!=null){
				if(!isset($item['solicitud'])){
					if(isset($padre['solicitud'])) $items['solicitud'] = $padre['solicitud'];
				}
				if(!isset($item['certificacion'])){
					if(isset($padre['certificacion'])) $items['certificacion'] = $padre['certificacion'];
				}
				if(!isset($item['orden'])){
					if(isset($padre['orden'])) $items['orden'] = $padre['orden'];
				}
				if(!isset($item['orden_servicio'])){
					if(isset($padre['orden_servicio'])) $items['orden_servicio'] = $padre['orden_servicio'];
				}
			}
		}
		$f->response->json( $items );
	}
	function execute_edit_data(){
		global $f;
		/*$cod = $f->model("lg/orde")->get("cod");
		if($cod->items==null) $cod->items="001000";
		else{
			$tmp = intval($cod->items);
			$tmp++;
			$tmp = (string)$tmp;
			for($i=strlen($tmp); $i<6; $i++){
				$tmp = '0'.$tmp;
			}
			$cod->items = $tmp;
		}*/
		$f->response->json( array(
			//'cod'=>$cod->items,
			'fuen'=>$f->model("pr/fuen")->get("all")->items
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['cotizacion']['_id'])) $data['cotizacion']['_id'] = new MongoId($data['cotizacion']['_id']);
		if(isset($data['fuente']['_id'])) $data['fuente']['_id'] = new MongoId($data['fuente']['_id']);
		if(isset($data['proveedor']['_id'])) $data['proveedor']['_id'] = new MongoId($data['proveedor']['_id']);
		if(isset($data['almacen']['_id'])) $data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
		if(isset($data['almacen']['local']['_id'])) $data['almacen']['local']['_id'] = new MongoId($data['almacen']['local']['_id']);
		if(isset($data['fecent'])) $data['fecent'] = new MongoDate(strtotime($data['fecent']));
		if(isset($data['precio_total'])) $data['precio_total'] = floatval($data['precio_total']);

		if(isset($data['etapa'])){
			$etapa_text = '';
			switch ($data['etapa']){
				case 'SOL':
					$etapa_text = 'solicitud';
					break;
				case 'CER':
					$etapa_text = 'certificacion';
					break;
				case 'ORD':
					$etapa_text = 'orden';
					break;
				case 'ORS':
					$etapa_text = 'orden_servicio';
					break;
				case 'REC':
					$etapa_text = 'recepcion';
					break;
			}
			if($etapa_text!=''){
				if(isset($data[$etapa_text]['productos'])){
					foreach ($data[$etapa_text]['productos'] as $index=>$prod){
						if(isset($prod['precio'])) $data[$etapa_text]['productos'][$index]['precio'] = floatval($prod['precio']);
						if(isset($prod['cant'])) $data[$etapa_text]['productos'][$index]['cant'] = floatval($prod['cant']);
						if(isset($prod['subtotal'])) $data[$etapa_text]['productos'][$index]['subtotal'] = floatval($prod['subtotal']);
						if(isset($prod['producto']['_id'])) $data[$etapa_text]['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
						if(isset($prod['producto']['unidad']['_id'])) $data[$etapa_text]['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
						if(isset($prod['producto']['clasif']['_id'])) $data[$etapa_text]['productos'][$index]['producto']['clasif']['_id'] = new MongoId($prod['producto']['clasif']['_id']);
						if(isset($prod['producto']['cuenta']['_id'])) $data[$etapa_text]['productos'][$index]['producto']['cuenta']['_id'] = new MongoId($prod['producto']['cuenta']['_id']);

						if(isset($prod['servicio']['_id'])) $data[$etapa_text]['productos'][$index]['servicio']['_id'] = new MongoId($prod['servicio']['_id']);
						if(isset($prod['servicio']['unidad']['_id'])) $data[$etapa_text]['productos'][$index]['servicio']['unidad']['_id'] = new MongoId($prod['servicio']['unidad']['_id']);
						if(isset($prod['servicio']['clasif']['_id'])) $data[$etapa_text]['productos'][$index]['servicio']['clasif']['_id'] = new MongoId($prod['servicio']['clasif']['_id']);
						if(isset($prod['servicio']['cuenta']['_id'])) $data[$etapa_text]['productos'][$index]['servicio']['cuenta']['_id'] = new MongoId($prod['servicio']['cuenta']['_id']);

						if(isset($prod['asignacion'])){
							foreach ($prod['asignacion'] as $ind=>$asig){
								if(isset($asig['programa']['_id'])) $data[$etapa_text]['productos'][$index]['asignacion'][$ind]['programa']['_id'] = new MongoId($asig['programa']['_id']);
								if(isset($asig['monto'])) $data[$etapa_text]['productos'][$index]['asignacion'][$ind]['monto'] = floatval($asig['monto']);
							}
						}
					}
				}
			}
		}


		if(isset($data['solicitud']['afectacion'])){
			foreach ($data['solicitud']['afectacion'] as $index=>$afect){
				if(isset($afect['programa']['_id'])) $data['solicitud']['afectacion'][$index]['programa']['_id'] = new MongoId($afect['programa']['_id']);
				foreach ($afect['gasto'] as $ind=>$gasto){
					if(isset($gasto['clasif']['_id'])) $data['solicitud']['afectacion'][$index]['gasto'][$ind]['clasif']['_id'] = new MongoId($gasto['clasif']['_id']);
					if(isset($gasto['monto'])) $data['solicitud']['afectacion'][$index]['gasto'][$ind]['monto'] = floatval($gasto['monto']);
				}
			}
		}

		if(isset($data['certificacion']['afectacion'])){
			foreach ($data['certificacion']['afectacion'] as $index=>$afect){
				if(isset($afect['programa']['_id'])) $data['certificacion']['afectacion'][$index]['programa']['_id'] = new MongoId($afect['programa']['_id']);
				foreach ($afect['gasto'] as $ind=>$gasto){
					if(isset($gasto['clasif']['_id'])) $data['certificacion']['afectacion'][$index]['gasto'][$ind]['clasif']['_id'] = new MongoId($gasto['clasif']['_id']);
					if(isset($gasto['monto'])) $data['certificacion']['afectacion'][$index]['gasto'][$ind]['monto'] = floatval($gasto['monto']);
				}
			}
		}

		if(isset($data['orden']['afectacion'])){
			foreach ($data['orden']['afectacion'] as $index=>$afect){
				if(isset($afect['programa']['_id'])) $data['orden']['afectacion'][$index]['programa']['_id'] = new MongoId($afect['programa']['_id']);
				foreach ($afect['gasto'] as $ind=>$gasto){
					if(isset($gasto['clasif']['_id'])) $data['orden']['afectacion'][$index]['gasto'][$ind]['clasif']['_id'] = new MongoId($gasto['clasif']['_id']);
					if(isset($gasto['monto'])) $data['orden']['afectacion'][$index]['gasto'][$ind]['monto'] = floatval($gasto['monto']);
				}
			}
		}

		if(isset($data['orden_servicio']['afectacion'])){
			foreach ($data['orden_servicio']['afectacion'] as $index=>$afect){
				if(isset($afect['programa']['_id'])) $data['orden_servicio']['afectacion'][$index]['programa']['_id'] = new MongoId($afect['programa']['_id']);
				foreach ($afect['gasto'] as $ind=>$gasto){
					if(isset($gasto['clasif']['_id'])) $data['orden_servicio']['afectacion'][$index]['gasto'][$ind]['clasif']['_id'] = new MongoId($gasto['clasif']['_id']);
					if(isset($gasto['monto'])) $data['orden_servicio']['afectacion'][$index]['gasto'][$ind]['monto'] = floatval($gasto['monto']);
				}
			}
		}

		if(isset($data['recepcion']['afectacion'])){
			foreach ($data['recepcion']['afectacion'] as $index=>$afect){
				if(isset($afect['programa']['_id'])) $data['recepcion']['afectacion'][$index]['programa']['_id'] = new MongoId($afect['programa']['_id']);
				foreach ($afect['gasto'] as $ind=>$gasto){
					if(isset($gasto['clasif']['_id'])) $data['recepcion']['afectacion'][$index]['gasto'][$ind]['clasif']['_id'] = new MongoId($gasto['clasif']['_id']);
					if(isset($gasto['monto'])) $data['recepcion']['afectacion'][$index]['gasto'][$ind]['monto'] = floatval($gasto['monto']);
				}
			}
		}

		if(isset($data['solicitud']['autreg']['_id']['$id'])){
			$data['solicitud']['autreg']['_id'] = new MongoId($data['solicitud']['autreg']['_id']['$id']);
		}
		if(isset($data['solicitud']['fecreg']['sec'])){
			$data['solicitud']['fecreg'] = new MongoDate($data['solicitud']['fecreg']['sec']);
		}
		if(isset($data['solicitud']['autenv']['_id']['$id'])){
			$data['solicitud']['autenv']['_id'] = new MongoId($data['solicitud']['autenv']['_id']['$id']);
		}
		if(isset($data['solicitud']['fecenv']['sec'])){
			$data['solicitud']['fecenv'] = new MongoDate($data['solicitud']['fecenv']['sec']);
		}
		if(isset($data['solicitud']['autrec']['_id']['$id'])){
			$data['solicitud']['autrec']['_id'] = new MongoId($data['solicitud']['autrec']['_id']['$id']);
		}
		if(isset($data['solicitud']['fecrec']['sec'])){
			$data['solicitud']['fecrec'] = new MongoDate($data['solicitud']['fecrec']['sec']);
		}
		if(isset($data['solicitud']['autapr']['_id']['$id'])){
			$data['solicitud']['autapr']['_id'] = new MongoId($data['solicitud']['autapr']['_id']['$id']);
		}
		if(isset($data['solicitud']['fecapr']['sec'])){
			$data['solicitud']['fecapr'] = new MongoDate($data['solicitud']['fecapr']['sec']);
		}



		if(isset($data['certificacion']['autreg']['_id']['$id'])){
			$data['certificacion']['autreg']['_id'] = new MongoId($data['certificacion']['autreg']['_id']['$id']);
		}
		if(isset($data['certificacion']['fecreg']['sec'])){
			$data['certificacion']['fecreg'] = new MongoDate($data['certificacion']['fecreg']['sec']);
		}
		if(isset($data['certificacion']['autenv']['_id']['$id'])){
			$data['certificacion']['autenv']['_id'] = new MongoId($data['certificacion']['autenv']['_id']['$id']);
		}
		if(isset($data['certificacion']['fecenv']['sec'])){
			$data['certificacion']['fecenv'] = new MongoDate($data['certificacion']['fecenv']['sec']);
		}
		if(isset($data['certificacion']['autrec']['_id']['$id'])){
			$data['certificacion']['autrec']['_id'] = new MongoId($data['certificacion']['autrec']['_id']['$id']);
		}
		if(isset($data['certificacion']['fecrec']['sec'])){
			$data['certificacion']['fecrec'] = new MongoDate($data['certificacion']['fecrec']['sec']);
		}
		if(isset($data['certificacion']['autapr']['_id']['$id'])){
			$data['certificacion']['autapr']['_id'] = new MongoId($data['certificacion']['autapr']['_id']['$id']);
		}
		if(isset($data['certificacion']['fecapr']['sec'])){
			$data['certificacion']['fecapr'] = new MongoDate($data['certificacion']['fecapr']['sec']);
		}

		if(isset($data['orden']['autreg']['_id']['$id'])){
			$data['orden']['autreg']['_id'] = new MongoId($data['orden']['autreg']['_id']['$id']);
		}
		if(isset($data['orden']['fecreg']['sec'])){
			$data['orden']['fecreg'] = new MongoDate($data['orden']['fecreg']['sec']);
		}
		if(isset($data['orden']['autenv']['_id']['$id'])){
			$data['orden']['autenv']['_id'] = new MongoId($data['orden']['autenv']['_id']['$id']);
		}
		if(isset($data['orden']['fecenv']['sec'])){
			$data['orden']['fecenv'] = new MongoDate($data['orden']['fecenv']['sec']);
		}
		if(isset($data['orden']['autrec']['_id']['$id'])){
			$data['orden']['autrec']['_id'] = new MongoId($data['orden']['autrec']['_id']['$id']);
		}
		if(isset($data['orden']['fecrec']['sec'])){
			$data['orden']['fecrec'] = new MongoDate($data['orden']['fecrec']['sec']);
		}
		if(isset($data['orden']['autapr']['_id']['$id'])){
			$data['orden']['autapr']['_id'] = new MongoId($data['orden']['autapr']['_id']['$id']);
		}
		if(isset($data['orden']['fecapr']['sec'])){
			$data['orden']['fecapr'] = new MongoDate($data['orden']['fecapr']['sec']);
		}

		if(isset($data['orden_servicio']['autreg']['_id']['$id'])){
			$data['orden_servicio']['autreg']['_id'] = new MongoId($data['orden_servicio']['autreg']['_id']['$id']);
		}
		if(isset($data['orden_servicio']['fecreg']['sec'])){
			$data['orden_servicio']['fecreg'] = new MongoDate($data['orden_servicio']['fecreg']['sec']);
		}
		if(isset($data['orden_servicio']['autenv']['_id']['$id'])){
			$data['orden_servicio']['autenv']['_id'] = new MongoId($data['orden_servicio']['autenv']['_id']['$id']);
		}
		if(isset($data['orden_servicio']['fecenv']['sec'])){
			$data['orden_servicio']['fecenv'] = new MongoDate($data['orden_servicio']['fecenv']['sec']);
		}
		if(isset($data['orden_servicio']['autrec']['_id']['$id'])){
			$data['orden_servicio']['autrec']['_id'] = new MongoId($data['orden_servicio']['autrec']['_id']['$id']);
		}
		if(isset($data['orden_servicio']['fecrec']['sec'])){
			$data['orden_servicio']['fecrec'] = new MongoDate($data['orden_servicio']['fecrec']['sec']);
		}
		if(isset($data['orden_servicio']['autapr']['_id']['$id'])){
			$data['orden_servicio']['autapr']['_id'] = new MongoId($data['orden_servicio']['autapr']['_id']['$id']);
		}
		if(isset($data['orden_servicio']['fecapr']['sec'])){
			$data['orden_servicio']['fecapr'] = new MongoDate($data['orden_servicio']['fecapr']['sec']);
		}



		/*if(isset($data['orden']['fecreg']['sec'])){
			$data['orden']['fecreg'] = new MongoDate($data['orden']['fecreg']['sec']);
		}

		if(isset($data['orden_servicio']['fecreg']['sec'])){
			$data['orden_servicio']['fecreg'] = new MongoDate($data['orden_servicio']['fecreg']['sec']);
		}

		if(isset($data['recepcion']['fecreg']['sec'])){
			$data['recepcion']['fecreg'] = new MongoDate($data['recepcion']['fecreg']['sec']);
		}*/

		if(isset($data['talonario'])){
			$talonario = $f->model("cj/talo")->params(array('_id'=>new MongoId($data['talonario'])))->get("one");
			if($talonario->items!=null){
				$cod = $talonario->items['prefijo'].$talonario->items['actual'].$talonario->items['sufijo'];
				$f->model("cj/talo")->params(array('_id'=>new MongoId($data['talonario']),'data'=>array('$inc'=>array('actual'=>1))))->save("custom");
				switch ($data['etapa']) {
					case 'SOL':
						$data['solicitud']['cod'] = $cod;
						$data['solicitud']['fecreg'] = new MongoDate();
						$data['solicitud']['autreg'] = $f->session->userDBMin;
						break;
					case 'CER':
						$data['certificacion']['cod'] = $cod;
						$data['certificacion']['fecreg'] = new MongoDate();
						$data['certificacion']['autreg'] = $f->session->userDBMin;
						break;
					case 'ORD':
						$data['orden']['cod'] = $cod;
						$data['orden']['fecreg'] = new MongoDate();
						$data['orden']['autreg'] = $f->session->userDBMin;
						break;
					case 'ORS':
						$data['orden_servicio']['cod'] = $cod;
						$data['orden_servicio']['fecreg'] = new MongoDate();
						$data['orden_servicio']['autreg'] = $f->session->userDBMin;
						break;
					case 'REC':
						$data['recepcion']['cod'] = $cod;
						$data['recepcion']['fecreg'] = new MongoDate();
						$data['recepcion']['autreg'] = $f->session->userDBMin;
						break;
				}
			}
			unset($data['talonario']);
		}
		//print_r($data);

		if(isset($data['padre']['_id'])){
			$data['padre']['_id'] = new MongoId($data['padre']['_id']);
			unset($data['_id']);
		}

		if(!isset($data['_id'])){
			//$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'P';
			$model = $f->model("lg/orde")->params(array('data'=>$data))->save("insert")->items;
			/*$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Orden de Compra',
				'descr'=>'Se creó la Orden de Compra <b>'.$data['cod'].'</b>.'
			))->save('insert');*/
		}else{
			if(isset($data['revision'])){
				$data['revision']['trabajador'] = $f->session->userDB;
				$data['revision']['fec'] = new MongoDate();
				$data['revision']['observ'] = $f->request->data['observ'];
				$f->model("lg/orde")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
				if(isset($data['estado'])){
					$f->model("lg/orde")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
					$orde = $f->model("lg/orde")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
					if($data['estado']=='A'){
						$f->model("lg/orde")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado_cont'=>'P')))->save("update");
					}
				}
			}else{
				$f->model("lg/orde")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			}
			$model = $f->model("lg/orde")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
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
		$model = $f->model('lg/orde')->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$update))->save('update');
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
		$f->model("lg/orde")->params(array("_id"=>new MongoId($f->request->data["_id"]),"data"=>$data))->save("update");
		$orden = $f->model('lg/orde')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CT',
			'bandeja'=>'&Oacute;rdenes de Compra',
			'descr'=>'Se asignaron las cuentas contables para la orden de compra <b>'.$orden['cod'].'</b>.'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_apro_cont(){
		global $f;
		$orde = $f->model('lg/orde')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		foreach ($orde['auxs'] as $auxs){
			$periodo = array(
				'ano'=>floatval(date('Y', $orde['fecreg']->sec)),
				'mes'=>floatval(date('m', $orde['fecreg']->sec))
			);
			$params = array(
				'ano'=>$periodo['ano'],
				'mes'=>$periodo['mes'],
				'cuenta'=>$auxs['cuenta']['_id']
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
				if($periodo['mes']!=1){
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
					'programa'=>$saldo['programa'],
					'cuenta'=>$saldo['cuenta']
				),
				'fec'=>new MongoDate(),
				'clase'=>'OC',
				'num'=>$orde['cod'],
				'detalle'=>$orde['observ'],
				'tipo'=>$auxs['tipo'],
				'tipo_saldo'=>$auxs['saldo'],
				'monto'=>floatval($auxs['monto'])
			);
			if(isset($saldo['programa'])) $auxi['saldos']['programa'] = $saldo['programa'];
			$f->model("ct/auxs")->params(array('data'=>$auxi))->save("insert");
		}
		$orden = $f->model('lg/orde')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;

		$f->model("lg/orde")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado_cont'=>'A')))->save("update");
		$f->model('ac/log')->params(array(
			'modulo'=>'CT',
			'bandeja'=>'&Oacute;rdenes de Compra',
			'descr'=>'Se aprob&oacute; la creaci&oacute;n de los auxiliares standar para la orden de compra <b>'.$orden['cod'].'</b>.'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/orde.edit");
	}
	function execute_edit_veoc(){
		global $f;
		$f->response->view("lg/orde.edit.cont");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/orde.details");
	}
	function execute_asig(){
		global $f;
		$f->response->view("lg/orde.asignar");
	}
}
?>