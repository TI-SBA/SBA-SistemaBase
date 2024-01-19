<?php
class Controller_lg_coti extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));

		$model = $f->model("lg/coti")->params($params)->get("lista");
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				if(isset($item['propuesta'])){
					foreach($item['propuesta'] as $j=>$prop){
						if(isset($prop['calificacion']['ganador']) && $prop['calificacion']['ganador']==true){
							if(isset($prop['productos'])){
								foreach($prop['productos'] as $k=>$prod){
									if(isset($prod['producto'])){
										$producto = $f->model('lg/prod')->params(array('_id'=>$prod['producto']['_id']))->get('one')->items;
										if(isset($producto['cuenta'])){
											$model->items[$i]['propuesta'][$j]['productos'][$k]['producto']['cuenta'] = $producto['cuenta'];
										}
										if(isset($producto['clasif'])){
											$model->items[$i]['propuesta'][$j]['productos'][$k]['producto']['clasif'] = $producto['clasif'];
										}
									}
								}
							}
							if(isset($prop['servicios'])){
								foreach($prop['servicios'] as $k=>$serv){
									if(isset($serv['servicio'])){
										$servicio = $f->model('lg/serv')->params(array('_id'=>$serv['servicio']['_id']))->get('one')->items;
										if(isset($servicio['cuenta'])){
											$model->items[$i]['propuesta'][$j]['servicios'][$k]['servicio']['cuenta'] = $servicio['cuenta'];
										}
										if(isset($servicio['clasif'])){
											$model->items[$i]['propuesta'][$j]['servicios'][$k]['servicio']['clasif'] = $servicio['clasif'];
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$f->response->json($model);
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/coti")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($items['requerimientos'])){
			foreach($items['requerimientos'] as $i => $req) {

				$items['requerimientos'][$i] = $f->model('lg/pedi')->params(array('_id'=>$req['_id']))->get('one')->items;
				//print_r($items['requerimientos'][$i]);
			}
		}
		if(isset($f->request->data['full'])){
			if(isset($items['propuesta']))
				foreach ($items['propuesta'] as $index=>$prop){
					$prov = $f->model('mg/entidad')->params(array(
						'filter'=>array('_id'=>$prop['proveedor']['_id']),
						'fields'=>array('docident'=>1),
						'sort'=>array()
					))->get('custom_data');
					if(isset($prov->items[0]['docident']))
						$items['propuesta'][$index]['proveedor']['docident'] = $prov->items[0]['docident'];
				}
		}
		$f->response->json( $items );
	}
	function execute_cod(){
		global $f;
		$cod = $f->model("lg/coti")->get("cod");
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
			'cod'=>$cod->items
		) );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;

		if(isset($data['fecent'])) $data['fecent'] = new MongoDate(strtotime($data['fecent']));
		if(isset($data['feccierre'])) $data['feccierre'] = new MongoDate(strtotime($data['feccierre']));
		if(isset($data['requerimientos'])){
			foreach ($data['requerimientos'] as $index=>$req){
				if(isset($req['_id'])) $data['requerimientos'][$index]['_id'] = new MongoId($req['_id']);
			}
		}
		if(isset($data['productos'])){
			foreach ($data['productos'] as $index=>$prod){
				if(isset($prod['cant']))
					$data['productos'][$index]['cant'] = floatval($prod['cant']);
				if(isset($prod['producto']['_id']))
					$data['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
				if(isset($prod['producto']['unidad']['_id']))
					$data['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
				if(isset($prod['producto']['clasif']['_id']))
					$data['productos'][$index]['producto']['clasif']['_id'] = new MongoId($prod['producto']['clasif']['_id']);
			}
		}
		if(isset($data['servicios'])){
			foreach ($data['servicios'] as $index=>$serv){
				if(isset($serv['cant']))
					$data['servicios'][$index]['cant'] = floatval($serv['cant']);
				if(isset($serv['servicio']['_id']))
					$data['servicios'][$index]['servicio']['_id'] = new MongoId($serv['servicio']['_id']);
				if(isset($serv['servicio']['unidad']['_id']))
					$data['servicios'][$index]['servicio']['unidad']['_id'] = new MongoId($serv['servicio']['unidad']['_id']);
				if(isset($serv['servicio']['clasif']['_id']))
					$data['servicios'][$index]['servicio']['clasif']['_id'] = new MongoId($serv['servicio']['clasif']['_id']);
			}
		}
		if(isset($data['propuesta'])){
			foreach ($data['propuesta'] as $i=>$prop){
				$data['propuesta'][$i]['proveedor']['_id'] = new MongoId($prop['proveedor']['_id']);
				$data['propuesta'][$i]['fecent'] = new MongoDate(strtotime($prop['fecent']));
				if(isset($prop['productos'])){
					foreach ($prop['productos'] as $j=>$prod){
						if(isset($prod['producto']['_id']))
							$data['propuesta'][$i]['productos'][$j]['producto']['_id'] = new MongoId($prod['producto']['_id']);
						if(isset($data['propuesta'][$i]['productos'][$j]['producto']['unidad']['_id']))
							$data['propuesta'][$i]['productos'][$j]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
					}
				}
				if(isset($prop['servicios'])){
					foreach ($prop['servicios'] as $j=>$prod){
						if(isset($prod['servicio']['_id']))
							$data['propuesta'][$i]['servicios'][$j]['servicio']['_id'] = new MongoId($prod['servicio']['_id']);
						if(isset($data['propuesta'][$i]['servicios'][$j]['servicio']['unidad']['_id']))
							$data['propuesta'][$i]['servicios'][$j]['servicio']['unidad']['_id'] = new MongoId($prod['servicio']['unidad']['_id']);
					}
				}
				if(isset($prop['calificacion'])){
					if($prop['calificacion']['ganador']=='false') $data['propuesta'][$i]['calificacion']['ganador'] = false;
					else $data['propuesta'][$i]['calificacion']['ganador'] = true;
				}
			}
		}
		if(isset($data['cierre'])){
			$data['cierre'] = array(
				'fec'=>new MongoDate()
			);
			$data['cierre']['trabajador'] = $f->session->userDB;
		}
		if(isset($data['fin'])){
			$data['fin'] = array(
				'fec'=>new MongoDate()
			);
			$data['fin']['trabajador'] = $f->session->userDB;
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'PE';
			$model = $f->model("lg/coti")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Cotizaciones',
				'descr'=>'Se creó la Cotizacion <b>'.$data['cod'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("lg/coti")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Cotizaciones',
				'descr'=>'Se actualizó la Cotizacion <b>'.$vari['cod'].'</b>.'
			))->save('insert');
			$model = $f->model("lg/coti")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_save_prop(){
		global $f;
		$data = $f->request->data['data'];
		$data['proveedor']['_id'] = new MongoId($data['proveedor']['_id']);
		$data['fecent'] = new MongoDate(strtotime($data['fecent']));
		if(isset($data['productos'])){
			foreach ($data['productos'] as $index=>$prod){
				if(isset($prod['cant']))
					$data['productos'][$index]['cant'] = floatval($prod['cant']);
				if(isset($prod['precio_unit']))
					$data['productos'][$index]['precio_unit'] = floatval($prod['precio_unit']);
				if(isset($prod['precio_total']))
					$data['productos'][$index]['precio_total'] = floatval($prod['precio_total']);
				if(isset($prod['producto']['_id']))
					$data['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
				if(isset($prod['producto']['unidad']['_id']))
					$data['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
			}
		}
		if(isset($data['servicios'])){
			foreach ($data['servicios'] as $index=>$serv){
				if(isset($serv['cant']))
					$data['servicios'][$index]['cant'] = floatval($serv['cant']);
				if(isset($serv['precio_unit']))
					$data['servicios'][$index]['precio_unit'] = floatval($serv['precio_unit']);
				if(isset($serv['precio_total']))
					$data['servicios'][$index]['precio_total'] = floatval($serv['precio_total']);
				if(isset($serv['servicio']['_id']))
					$data['servicios'][$index]['servicio']['_id'] = new MongoId($serv['servicio']['_id']);
				if(isset($serv['servicio']['unidad']['_id']))
					$data['servicios'][$index]['servicio']['unidad']['_id'] = new MongoId($serv['servicio']['unidad']['_id']);
			}
		}
		if(isset($data['precio_total'])) $data['precio_total'] = floatval($data['precio_total']);
		$f->model("lg/coti")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("propuesta");
		$coti = $f->model("lg/coti")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Cotizaciones',
			'descr'=>'Se ingres&oacute; una propuesta para la cotizaci&oacute;n <b>'.$coti['cod'].'</b>.'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_del_coti(){
		global $f;
		$f->model("lg/coti")->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'num'=>$f->request->data['num']
		))->delete("coti");
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/coti.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/coti.details");
	}
	function execute_edit_pro(){
		global $f;
		$f->response->view("lg/coti.ing");
	}
	function execute_cuad_comp(){
		global $f;
		$f->response->view("lg/coti.cuad");
	}
	function execute_cuad_comp_cer(){
		global $f;
		$f->response->view("lg/coti.cuad.cer");
	}
}
?>