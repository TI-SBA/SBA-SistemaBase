<?php
class Controller_lg_nota extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		/*if(isset($f->request->data['autor']))
			if($f->request->data['autor']!='')
				$params['trabajador'] = $f->session->enti['_id'];*/
		$model = $f->model("lg/nota")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_lista_pend(){
		global $f;
		$filter = array('estado'=>'P');
		$model = $f->model("lg/nota")->params(array("filter"=>$filter,"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_lista_todo(){
		global $f;
		$filter = array();
		$model = $f->model("lg/nota")->params(array("filter"=>$filter,"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$filter;
		switch($f->request->tipo){
			case 'N': $filter = array('estado'=>'P','trabajador._id'=>$f->session->enti['_id']); break;
			case 'P': $filter = array('estado'=>'P'); break;
			case 'T': $filter = array(); break;
		}
		$model = $f->model("lg/nota")->params(array('filter'=>$filter,"page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('lg/nota')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("lg/nota")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$f->response->json( $model->items );
	}
	function execute_edit_data(){
		global $f;
		$cod = $f->model("lg/nota")->get("cod");
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
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['productos'])){
			foreach ($data['productos'] as $index=>$prod){
				if(isset($prod['producto']['_id'])) $data['productos'][$index]['producto']['_id'] = new MongoId($prod['producto']['_id']);
				if(isset($prod['producto']['unidad']['_id'])) $data['productos'][$index]['producto']['unidad']['_id'] = new MongoId($prod['producto']['unidad']['_id']);
				if(isset($prod['producto']['cuenta']['_id'])) $data['productos'][$index]['producto']['cuenta']['_id'] = new MongoId($prod['producto']['cuenta']['_id']);
			}
		}
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['procedencia'])) $data['procedencia']['_id'] = new MongoId($data['procedencia']['_id']);
		if(isset($data['almacen'])) $data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
		if(isset($data['programa'])) $data['programa']['_id'] = new MongoId($data['programa']['_id']);
		if(isset($data['destino_a'])){
			$data['destino_a']['_id'] = new MongoId($data['destino_a']['_id']);
			$data['destino_a']['local']['_id'] = new MongoId($data['destino_a']['local']['_id']);
		}
		if(!isset($f->request->data['_id'])){
			$data['trabajador'] = $f->session->userDB;
			$data['fecreg'] = new MongoDate();
			$data['estado'] = "R";
			$f->model("lg/nota")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Notas de Entrada',
				'descr'=>'Se cre&oacute; la nota de entrada <b>'.$data['cod'].'</b>.'
			))->save('insert');
			foreach($data['productos'] as $i=>$prod){
				$tmp_prod = $f->model('lg/prod')->params(array(
					'_id'=>new MongoId($prod['producto']['_id']),
					'fields'=>array('tipo_producto'=>true,'clasif'=>true,'cuenta'=>true)
				))->get('one')->items;
				$tipo_producto = $tmp_prod['tipo_producto'];
				if($tipo_producto!=null){
					if($tipo_producto!='P'){
						$bien = array(
							'fecreg'=>$fecreg,
							'estado'=>'I',
							'tipo'=>$tipo_producto,
							'producto'=>$prod['producto'],
							'almacen'=>$data['destino_a'],
							'valor_inicial'=>floatval($prod['precio_unit']),
							'valor_actual'=>floatval($prod['precio_unit']),
							'entrada'=>array(
								'tipo'=>'NE',
								'forma'=>$data['motivo'],
								'cod'=>$data['cod'],
								'_id'=>$data['_id']
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
				if(isset($mov)){
					unset($mov['_id']);
				}
				$stock = $f->model('lg/stck')->params(array(
					'producto'=>$prod['producto']['_id'],
					'almacen'=>$data['destino_a']['_id']
				))->get('prod')->items;
				$mov = array(
					'stock'=>$stock['_id'],
					'entrada_cant'=>floatval($prod['cant']),
					'salida_cant'=>0,
					'entrada_monto'=>floatval($prod['subtotal']),
					'salida_monto'=>0,
					'saldo_cant'=>floatval($prod['cant'])+floatval($stock['stock']),
					'saldo_monto'=>floatval($prod['subtotal'])+floatval($stock['costo']),
					'referencia_id'=>$data['_id'],
					'referencia_tipo'=>'lg_notas',
					'glosa'=>$data['cod'],
					'fec'=>$data['fecreg'],
					'cuenta'=>$tmp_prod['cuenta']['cod'],
					'partida'=>$tmp_prod['clasif']['cod'],
					'periodo'=>date('Y-m'),
					'programa'=>$data['programa']['_id']
				);
				$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
				$stock = $f->model('lg/stck')->params(array(
					'_id'=>$stock['_id'],
					'data'=>array(
						'$inc'=>array(
							'stock'=>floatval($prod['cant']),
							'costo'=>floatval($prod['subtotal'])
						)
					)
				))->save('custom')->items;
			}
		}else{
			/*if(isset($data['revision'])){
				$data['revision']['trabajador'] = $f->session->userDB;
				$data['revision']['fec'] = new MongoDate();
				$f->model("lg/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
				if(isset($data['estado'])){
					$f->model("lg/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
				}
			}else{
				$f->model("lg/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			}
			$nota = $f->model('lg/nota')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Notas de Entrada',
				'descr'=>'Se actualiz&oacute; la nota de entrada <b>'.$nota['cod'].'</b>.'
			))->save('insert');*/
		}
		$f->response->print("true");
	}
	function execute_finalizar(){
		global $f;
		$data = $f->request->data;
		$fecreg = new MongoDate();
		if($data['estado']=='A'){
			$nota = $f->model('lg/nota')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			foreach($nota['productos'] as $i=>$prod){
				$tipo_producto = $f->model('lg/prod')->params(array(
					'_id'=>new MongoId($prod['producto']['_id']),
					'fields'=>array('tipo_producto'=>true)
				))->get('one')->items['tipo_producto'];
				if($tipo_producto!=null){
					if($tipo_producto!='P'){
						$bien = array(
							'fecreg'=>$fecreg,
							'estado'=>'I',
							'tipo'=>$tipo_producto,
							'producto'=>$prod['producto'],
							'almacen'=>$nota['destino_a'],
							'valor_inicial'=>floatval($prod['precio_unit']),
							'valor_actual'=>floatval($prod['precio_unit']),
							'entrada'=>array(
								'tipo'=>'NE',
								'forma'=>$nota['motivo'],
								'cod'=>$nota['cod'],
								'_id'=>$nota['_id']
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
				unset($mov['_id']);
				$actual = $f->model("lg/prod")->params(array(
					'prod'=>$prod['producto']['_id'],
					'cant'=>$prod['cant'],
					'almacen'=>$nota['destino_a'],
					'total'=>doubleval($prod['subtotal']),
					'precio_unit'=>doubleval($prod['precio_unit'])
				))->save("stock_add")->items;
				$mov = array(
					'fecreg'=>$fecreg,
					'documento'=>array(
						'_id'=>$nota['_id'],
						'cod'=>$nota['cod'],
						'tipo'=>'NE'
					),
					'tipo'=>'E',
					'almacen'=>$nota['destino_a'],
					'producto'=>$prod['producto'],
					'cant'=>(int)$prod['cant'],
					'salida'=>0,
					'saldo'=>0,
					'entrada'=>(int)$prod['cant'],
					'precio_unitario'=>doubleval($prod['precio_unit']),
					'total'=>doubleval($prod['subtotal'])
				);
				if(isset($actual['stock'])){
					foreach($actual['stock'] as $i=>$stock){
						if($stock['almacen']['_id']==$nota['destino_a']['_id']){
							if(isset($stock['actual'])) $mov['saldo'] = (float)$stock['actual'];
							break;
						}
					}
				}
				$f->model("lg/movi")->params(array('data'=>$mov))->save("insert");
			}
		}
		$data['revision']['trabajador'] = $f->session->userDB;
		$data['revision']['fec'] = $fecreg;
		$f->model("lg/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data['revision']))->save("push");
		$f->model("lg/nota")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('estado'=>$data['estado'])))->save("update");
		$nota = $f->model('lg/nota')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Notas de Entrada',
			'descr'=>'Se finaliz&oacute; la nota de entrada <b>'.$nota['cod'].'</b>.'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/nota.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/nota.details");
	}
	function execute_select(){
		global $f;
		$f->response->view("lg/nota.select");
	}
	function execute_rev(){
		global $f;
		$f->response->view("lg/nota.rev");
	}
}
?>