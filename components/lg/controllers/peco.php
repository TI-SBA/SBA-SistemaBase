<?php
class Controller_lg_peco extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['autor'])){
			/*********************************************************************************************
			* PREGUNTAR SI ES OFICINA O PROGRAMA
			*********************************************************************************************/
			$params['oficina'] = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/peco")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$data = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($f->request->data['all'])){
			foreach ($data['productos'] as $i=>$item) {
				$data['productos'][$i]['stock'] = $f->model('lg/stck')->params(array(
					'producto'=>$item['producto']['_id'],
					'almacen'=>$data['almacen']['_id']
				))->get('prod')->items['stock'];
			}
		}
		$f->response->json($data);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['responsable']['_id'])) $data['responsable']['_id'] = new MongoId($data['responsable']['_id']);
		if(isset($data['responsable']['cargo']['_id'])) $data['responsable']['cargo']['_id'] = new MongoId($data['responsable']['cargo']['_id']);
		if(isset($data['responsable']['cargo']['organizacion']['_id'])) $data['responsable']['cargo']['organizacion']['_id'] = new MongoId($data['responsable']['cargo']['organizacion']['_id']);
		if(isset($data['almacen'])){
			$data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
			$data['almacen']['local']['_id'] = new MongoId($data['almacen']['local']['_id']);
		}
		if(isset($data['productos'])){
			foreach ($data['productos'] as $index=>$prod){
				if(isset($prod['producto']['_id'])) $data['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
				if(isset($prod['producto']['unidad']['_id'])) $data['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
				if(isset($prod['producto']['clasif']['_id'])) $data['productos'][$index]['producto']['clasif']['_id'] = new MongoId($prod['producto']['clasif']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['solicitante'] = $f->session->userDBMin;
			$data['estado'] = 'P';
			$cod = $f->model("lg/peco")->get("cod");
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
			$data['cod'] = $cod->items;
			if(isset($f->session->enti['roles'])){
				if(isset($f->session->enti['roles']['trabajador'])){
					if(isset($f->session->enti['roles']['trabajador']['oficina'])){
						$data['oficina'] = $f->session->enti['roles']['trabajador']['oficina'];
					}
					if(isset($f->session->enti['roles']['trabajador']['programa'])){
						$data['programa'] = $f->session->enti['roles']['trabajador']['programa'];
					}
				}
			}
			$model = $f->model("lg/peco")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'PECOSAs',
				'descr'=>'Se cre&oacute; la PECOSA <b>'.$data['cod'].'</b>.'
			))->save('insert');
		}else{
			if(isset($data['revision'])){
				$data['revision']['trabajador'] = $f->session->userDB;
				$data['revision']['fec'] = new MongoDate();
				$f->model("lg/peco")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
				if(isset($data['estado'])){
					$f->model("lg/peco")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
				}
			}else{
				$f->model("lg/peco")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			}
			$vari = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'PECOSAS',
				'descr'=>'Se actualiz&oacute; la PECOSA <b>'.$vari['cod'].'</b>.'
			))->save('insert');
		}
		$f->response->print('true');
	}
	function execute_definir(){
		global $f;
		$data = $f->request->data;
		$fecreg = new MongoDate();
		$trabajador = $f->session->userDBMin;
		$upd = array(
			'entregado'=>array(
				'trabajador'=>$trabajador,
				'fec'=>$fecreg
			),
			'estado'=>'E',
			//'precio_total'=>floatval($data['total'])
		);
		foreach($data['productos'] as $i=>$prod){
			if(floatval($prod['solicitado'])!=0){
				if(!isset($prod['precio'])) $prod['precio'] = 0;
				$upd['productos.'.$i.'.despachado'] = floatval($prod['solicitado']);
				$upd['productos.'.$i.'.precio_unit'] = floatval($prod['precio']);
				$upd['productos.'.$i.'.subtotal'] = floatval($prod['precio'])*floatval($prod['solicitado']);
				if(isset($prod['bienes'])){
					$upd['productos.'.$i.'.bienes'] = array();
					foreach ($prod['bienes'] as $j=>$bien){
						$upd['productos.'.$i.'.bienes'][] = new MongoId($bien);
					}
					$upd['con_bienes'] = true;
					$upd['alta'] = false;
				}
			}
		}
		$f->model("lg/peco")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");
		$vari = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'PECOSAS',
			'descr'=>'Se defini&oacute; la <b>Entrega</b> de la PECOSA <b>'.$vari['cod'].'</b>.'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_confirmar(){
		global $f;
		$fecreg = new MongoDate();
		$pecosa = $f->model('lg/peco')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		//$cuadro = $f->model('lg/cuad')->params(array('_id'=>$pecosa['solicitante']['cargo']['organizacion']['_id']))->get('orga')->items;
		foreach ($pecosa['productos'] as $prod){
			$tmp_prod = $f->model('lg/prod')->params(array(
				'_id'=>new MongoId($prod['producto']['_id']),
				'fields'=>array('tipo_producto'=>true,'clasif'=>true,'cuenta'=>true)
			))->get('one')->items;
			if(isset($mov)){
				unset($mov['_id']);
			}
			$stock = $f->model('lg/stck')->params(array(
				'producto'=>$prod['producto']['_id'],
				'almacen'=>$pecosa['almacen']['_id']
			))->get('prod')->items;
			if(!isset($prod['subtotal'])){
				$prod['subtotal'] = 0;
			}
			$mov = array(
				'stock'=>$stock['_id'],
				'entrada_cant'=>0,
				'salida_cant'=>floatval($prod['despachado']),
				'entrada_monto'=>0,
				'salida_monto'=>floatval($prod['subtotal']),
				'saldo_cant'=>floatval($stock['stock'])-floatval($prod['despachado']),
				'saldo_monto'=>floatval($stock['costo'])-floatval($prod['subtotal']),
				'referencia_id'=>$pecosa['_id'],
				'referencia_tipo'=>'lg_pecosas',
				'glosa'=>$pecosa['cod'],
				'fec'=>$fecreg,
				'cuenta'=>$tmp_prod['cuenta']['cod'],
				'partida'=>$tmp_prod['clasif']['cod'],
				'periodo'=>date('Y-m'),
				'programa'=>$data['programa']['_id']
			);
			$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
			$bien = array(
				//'estado'=>'A',
				'solicitante'=>$pecosa['solicitante'],
				'responsable'=>$pecosa['responsable'],
				'ubicacion'=>$pecosa['destino'],
				'fecinst'=>$fecreg,
				'salida'=>array(
					'tipo'=>'PE',
					'cod'=>$pecosa['cod'],
					'_id'=>$pecosa['_id']
				)
			);
			foreach ($prod['bienes'] as $bi){
				$f->model("lg/bien")->params(array('_id'=>$bi,'data'=>$bien))->save("update");
			}
			$stock = $f->model('lg/stck')->params(array(
				'_id'=>$stock['_id'],
				'data'=>array(
					'$inc'=>array(
						'stock'=>-floatval($prod['cant']),
						'costo'=>-floatval($prod['subtotal'])
					)
				)
			))->save('custom')->items;
			/*foreach ($cuadro['items'] as $k=>$items){
				if($items['producto']['_id']==$prod['producto']['_id'])
					$f->model("lg/cuad")->params(array(
						'_id'=>$cuadro['_id'],
						'field'=>'items.'.$k.'.saldo',
						'cant'=>abs($prod['despachado'])
					))->save("dec");
			}*/
		}
		$trabajador = $f->session->userDBMin;
		$upd = array(
			'recibido'=>array(
				'trabajador'=>$trabajador,
				'fec'=>$fecreg,
				'observ'=>$f->request->data['observ']
			),
			'estado'=>'R'
		);
		$f->model("lg/peco")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$upd))->save("update");
		$vari = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'PECOSAS',
			'descr'=>'Se confirm&oacute; la <b>Recepci&oacute;n</b> de la PECOSA <b>'.$vari['cod'].'</b>.'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_alta(){
		global $f;
		$data = $f->request->data;
		$trabajador = array(
			'_id'=>$f->session->enti['_id'],
			'tipo_enti'=>$f->session->enti['tipo_enti'],
			'nomb'=>$f->session->enti['nomb'],
		);
		if($f->session->enti['tipo_enti']=='P'){
			$trabajador['appat'] = $f->session->enti['appat'];
			$trabajador['apmat'] = $f->session->enti['apmat'];
		}
		$trabajador['cargo'] = array(
			'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
		);
		$upd = array(
			'conservacion'=>$data['conservacion'],
			'estado'=>'A',
			'alta'=>array(
				'fecres'=>new MongoDate(strtotime($data['fecres'])),
				'trabajador'=>$trabajador,
				'resolucion'=>$data['resolucion'],
				'causal'=>$data['causal']
			)
		);
		foreach($data['bienes'] as $bien){
			$upd['alta']['fecreg'] = new MongoDate(strtotime($bien['fecinst']));
			$f->model("lg/bien")->params(array('_id'=>new MongoId($bien['_id']),'data'=>$upd))->save("update");
		}
		$vari = $f->model("lg/peco")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'PECOSAS',
			'descr'=>'Se di&oacute; de <b>Alta</b> a los bienes contenidos en la PECOSA <b>'.$vari['cod'].'</b>.'
		))->save('insert');
		$f->model("lg/peco")->params(array('_id'=>new MongoId($data['_id']),'data'=>array('alta'=>true)))->save("update");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/peco.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/peco.details");
	}
}
?>