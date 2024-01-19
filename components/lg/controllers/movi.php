<?php
class Controller_lg_movi extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['producto']))
			if($f->request->data['producto']!='')
				$params['producto'] = new MongoId($f->request->data['producto']);
		if(isset($f->request->data['almacen']))
			if($f->request->data['almacen']!='')
				$params['almacen'] = new MongoId($f->request->data['almacen']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		//elseif($f->request->data['almacen']->{'$id'}=='5894e68c3e6037e8798b4567');
		//	$params['sort'] = array("fecreg"=>floatval(-1));
		$f->response->json( $f->model("lg/movi")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("lg/movi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['fec']))
			$data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['documento'])){
			if(isset($data['documento']['_id']))
				$data['documento']['_id'] = new MongoId($data['documento']['_id']);
		}
		if(isset($data['almacen']))
			$data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
		if(isset($data['producto']))
			$data['producto']['_id'] = new MongoId($data['producto']['_id']);
		if(isset($data['organizacion']))
			$data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		

		$f->model("lg/prod")->params(array(
			'prod'=>$prod['producto']['_id'],
			'cant'=>$prod['cant'],
			'total'=>doubleval($prod['subtotal']),
			'precio_unit'=>doubleval($prod['precio']),
			'almacen'=>$orden['almacen']
		))->save("stock_add");
		

		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$model = $f->model("lg/movi")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Movimientos',
				'descr'=>'Se creó el Movimiento <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("lg/movi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Movimientos',
				'descr'=>'Se actualizó el Movimiento <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("lg/movi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
/*	function execute_save_new(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['fec']))
			$data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['documento'])){
			if(isset($data['documento']['_id']))
				$data['documento']['_id'] = new MongoId($data['documento']['_id']);
		}
		if(isset($data['almacen']))
			$data['almacen']['_id'] = new MongoId($data['almacen']['_id']);
		if(isset($data['producto']))
			$data['producto']['_id'] = new MongoId($data['producto']['_id']);
		if(isset($data['organizacion']))
			$data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		
		if(isset($data['cant']))
			$data['cant'] = floatval($data['cant']);
		if(isset($data['total']))
			$data['total'] = floatval($data['total']);
		if(isset($data['precio_unitario']))
			$data['precio_unitario'] = floatval($data['precio_unitario']);
		if($data['tipo']=='E'){
			$f->model("lg/prod")->params(array(
				'prod'=>$data['producto']['_id'],
				'cant'=>$data['cant'],
				'total'=>doubleval($data['total']),
				'precio_unit'=>doubleval($data['precio_unitario']),
				'almacen'=>$data['almacen']
			))->save("stock_add");
		}else{
			$f->model('lg/prod')->params(array(
				'_id'=>$data['producto']['_id'],
				'almacen'=>$data['almacen']['_id'],
				'cant'=>$data['cant'],
				'total'=>floatval($data['total'])
			))->save('stock_dec');
		}
		$prod = $f->model('lg/prod')->params(array('_id'=>$data['producto']['_id']))->get('one')->items;
		foreach ($prod['stock'] as $key => $stock) {
			if($stock['almacen']['_id']==$data['almacen']['_id']){
				$data['saldo'] = $stock['actual'];
				break;
			}
		}
		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDB;
		$data['estado'] = 'H';
		$model = $f->model("lg/movi")->params(array('data'=>$data))->save("insert")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Movimientos',
			'descr'=>'Se creó el Movimiento <b>'.$data['nomb'].'</b>.'
		))->save('insert');
		$f->response->json($model);
	}
*/
	function execute_save_new(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['fec']))
			$data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['documento'])){
			if(isset($data['documento']['_id']))
				$data['documento']['_id'] = new MongoId($data['documento']['_id']);
		}
		if(isset($data['almacen']))
			$data['almacen']['_id'] = new MongoId($data['almacen']['_id']);

		if(isset($data['producto']))
			$data['producto']['_id'] = new MongoId($data['producto']['_id']);

		if(isset($data['organizacion']))
			$data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		
		if(isset($data['cant']))
			$data['cant'] = floatval($data['cant']);
		if(isset($data['total']))
			$data['total'] = floatval($data['total']);
		if(isset($data['precio_unitario']))
			$data['precio_unitario'] = floatval($data['precio_unitario']);


		/************************************************************************************
		*	AÑADIR Y CREAR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
		************************************************************************************/
		$stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$data['almacen']['_id'],"producto"=>$data['producto']['_id'])))->get('one_custom')->items;
		if($stock==null){
			$stock = array(
				'_id'=>new MongoId(),
				'producto'=>$data['producto']['_id'],
				'almacen'=>$data['almacen']['_id'],
				'stock'=>0,
				'costo'=>0
			);
			$f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
		}

		$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>-1)))->get('custom')->items;
		//$saldo = $f->model("lg/movi")->params(array('filter'=>array('stock'=>$stock['_id']),'sort'=>array('fecreg'=>1)))->get('custom')->items;

		$saldo_cant = 0;
		$saldo_monto = 0;
		if($saldo!=null){
			if(count($saldo)>0){
				$saldo_cant = $saldo[0]['saldo'];
				$saldo_monto = $saldo[0]['saldo_imp'];
			}
		}
		$stock_actual = floatval($saldo_cant);

		if($data['tipo']=='E'){
//			$f->model("lg/prod")->params(array(
//				'prod'=>$data['producto']['_id'],
//				'cant'=>$data['cant'],
//				'total'=>doubleval($data['total']),
//				'precio_unit'=>doubleval($data['precio_unitario']),
//				'almacen'=>$data['almacen']
//			))->save("stock_add");
			$data['saldo'] = $stock_actual+floatval($data['cant']);
			$data['saldo_imp'] =  $saldo_monto+floatval($data['cant'])*floatval($data['precio_unitario']);
		}else{
//			$f->model('lg/prod')->params(array(
//				'_id'=>$data['producto']['_id'],
//				'almacen'=>$data['almacen']['_id'],
//				'cant'=>$data['cant'],
//				'total'=>floatval($data['total'])
//			))->save('stock_dec');
			$data['saldo'] = $stock_actual-floatval($data['cant']);
			$data['saldo_imp'] =  $saldo_monto-floatval($data['cant'])*floatval($data['precio_unitario']);
		}


//		$prod = $f->model('lg/prod')->params(array('_id'=>$data['producto']['_id']))->get('one')->items;
//		foreach ($prod['stock'] as $key => $stock) {
//			if($stock['almacen']['_id']==$data['almacen']['_id']){
		//$data['saldo'] = $stock_actual+floatval($data['cant']);
//				//$data['total'] = $stock['valor_total'];
//				break;
//			}
//		}
//
//		#Actualizar tambien lg_stocks
//		$stck = $f->model('lg/stck')->params(array('almacen'=>$data['almacen']['_id'],'producto'=>$prod["_id"]))->get('prod')->items;
//		if(is_null($stck)){
//			$stck = $f->model('lg/stck')->params(array('data'=>array(
//				'almacen'=>$data['almacen']['_id'],
//				'producto'=>$prod['_id'],
//				'stock'=>doubleval($data['saldo']),
//				'costo'=>doubleval($stock['valor_total']),
//			)))->save('insert')->items;
//		}else{
//			$stck = $f->model('lg/stck')->params(array('_id'=>$stck['_id'],'data'=>array(
//					'stock'=>doubleval($data['saldo']),
//					'costo'=>doubleval($stock['valor_total']),
//			)))->save('update')->items;
//		}
		if(isset($prod['clasif'])){
			$data['clasif'] = $prod['clasif'];
			$data['clasif']['_id'] = new MongoId($data['clasif']['_id']);
		} 
		if(isset($prod['cuenta'])){
			$data['cuenta'] = $prod['cuenta'];
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		} 
		//$data['stock']=$stck['_id'];
		$data['stock']=$stock['_id'];

		$data['fecreg'] = new MongoDate();
		$data['autor'] = $f->session->userDB;
		$data['estado'] = 'H';
		$model = $f->model("lg/movi")->params(array('data'=>$data))->save("insert")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Movimientos',
			'descr'=>'Se creó el Movimiento <b>'.$data['documento']['cod'].'</b>.'
		))->save('insert');
		$f->model("lg/stck")->params(array(
						'_id'=>$stock['_id'],
						'data'=>array(
							'stock'=>$data['saldo'],
							'costo'=>$data['saldo_imp'],
						)
		))->save("update");
		$f->response->json($model);
	}


	function execute_edit(){
		global $f;
		$f->response->view("lg/movi.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/movi.details");
	}
}
?>