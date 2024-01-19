<?php
class Controller_fa_repo extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('fa/repo');
	}
	/*function execute_reporte_movimientos(){
		global $f;
		$movimientos = $f->model('lg/movi')->params(array(
			'filter'=>array(
				'producto._id'=>new MongoId($f->request->data['prod']),
				//'producto._id'=>new MongoId('5706745b8e7358ac07000029'), //URCIN
				//'producto._id'=>new MongoId('57091ab38e73584c0800002d'), //VALPRAX
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['ini'])),
					'$lt'=>new MongoDate(strtotime($f->request->data['fin'].' +1 day'))
				),
				//'tipo'=>'E'
			),
			'sort'=>array('fecreg'=>1)
		))->get('all')->items;
		$resumen = array();
		foreach ($movimientos as $i=>$movi) {
			if(!isset($resumen[$movi['producto']['_id']->{'$id'}])){
				$resumen[$movi['producto']['_id']->{'$id'}] = array(
					'producto' => $movi['producto'],
					'total' => 0,
					'ent'=>0,
					'sal'=>0
				);
			}
			//SALIDA PORQUE ES UN COMPROBANTE NORMAL
			if($movi['tipo']=='S' && isset($movi['documento']['serie'])){
				$cjcomp=$f->model('cj/comp')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items;
				if (!is_null($cjcomp)) {
					$movimientos[$i]['comprobante'] = $cjcomp;
					foreach ($movimientos[$i]['comprobante']['items'] as $item) {
						if($item['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
							$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($item['monto']) * floatval($item['cant']);
							break;
						}
					}	
				}
			}
			//SALIDA PORQUE ES UN COMPROBANTE ELECTRoNICO
			if($movi['tipo']=='S' && isset($movi['documento']['serie'])){
				$cjecom=$f->model('cj/ecom')->params(array('_id'=>$movi['documento']['_id']))->get('one')->items;
				if (!is_null($cjecom)) {
					$movimientos[$i]['comprobante'] = $cjecom;
					foreach ($movimientos[$i]['comprobante']['items'] as $item) {
						if(isset($item['conceptos']))
							foreach ($item['conceptos'] as $concepto){
								if(isset($concepto['producto'])){
									if($concepto['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
										$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($concepto['monto']) * floatval($concepto['cant']);
									break;
								}
							}
						}
					}	
				}
			}
			//ENTRADA PORQUE ES UNA GUIA DE REMISION
			if(isset($movi['guia'])){
				$resumen[$movi['producto']['_id']->{'$id'}]['ent'] += floatval($movi['cant']);
			//}elseif(isset($movi['comprobante'])){
			}elseif($movi['tipo']=='S' && isset($movi['documento']['serie'])){
				$resumen[$movi['producto']['_id']->{'$id'}]['sal'] -= floatval($movi['cant']);
			}
		}
		//print_r($movimientos);print_r($resumen);die();
		$f->response->view("fa/repo.reporte_movimientos.xls",array(
			'data'=>$movimientos,
			'resumen'=>$resumen
		));
	}
	*/
	/*function execute_listado_prod(){
		global $f;
		$productos = $f->model('fa/prod')->params(array())->get('all')->items;
		$f->response->view("fa/repo.listado_prod.xls",array(
			'data'=>$productos
		));
	}
	*/
	function execute_reporte_movimientos_legacy(){
		global $f;
		$movimientos = $f->model('fa/movi')->params(array(
			'filter'=>array(
				'producto._id'=>new MongoId($f->request->data['prod']),
				//'producto._id'=>new MongoId('5706745b8e7358ac07000029'), //URCIN
				//'producto._id'=>new MongoId('57091ab38e73584c0800002d'), //VALPRAX
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['ini'])),
					'$lt'=>new MongoDate(strtotime($f->request->data['fin'].' +1 day'))
				),
				'estado'=>'E'
			),
			'sort'=>array('fecreg'=>1)
		))->get('all')->items;
		$resumen = array();
		foreach ($movimientos as $i=>$movi) {
			if(!isset($resumen[$movi['producto']['_id']->{'$id'}])){
				$resumen[$movi['producto']['_id']->{'$id'}] = array(
					'producto' => $movi['producto'],
					'total' => 0,
					'ent'=>0,
					'sal'=>0
				);
			}
			if(isset($movi['comprobante'])){
				$movimientos[$i]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				foreach ($movimientos[$i]['comprobante']['items'] as $item) {
					if($item['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
						$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($item['monto']) * floatval($item['cant']);
						break;
					}
				}
			}
			if(isset($movi['guia'])){
				$resumen[$movi['producto']['_id']->{'$id'}]['ent'] += floatval($movi['cant']);
			}elseif(isset($movi['comprobante'])){
				$resumen[$movi['producto']['_id']->{'$id'}]['sal'] -= floatval($movi['cant']);
			}
		}
		//print_r($resumen);die();
		$f->response->view("fa/repo.reporte_movimientos.xls",array(
			'data'=>$movimientos,
			'resumen'=>$resumen
		));
	}
	function execute_registro_ventas(){
		global $f;
		$fec = $f->request->data['ano'].'-'.$f->request->data['mes'].'-01';
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'modulo'=>'FA',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			)/*,
			'estado'=>array('$ne'=>'X')*/
		),'fields'=>array(
			'fecreg'=>true,
			'tipo'=>true,
			'serie'=>true,
			'num'=>true,
			'cliente'=>true,
			'total'=>true,
			'estado'=>true,
			'cliente_nuevo'=>true
		),'sort'=>array(
			'fecreg'=>1,
			'serie'=>1,
			'num'=>1
		)))->get("all")->items;
		//echo date('Y-m-d H:i:s',strtotime($fec.' +1 month -1 hour'));die();

		$efilter = array(
			'fecemi'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			),
			'items.tipo' => array('$in' => array('farmacia')),
			'serie' => array('$in' => array('B002','F002')),
		);

		$ecom = $f->model("cj/ecom")->params(array("filter"=>$efilter,
			'fields'=>array(
				'fecemi'=>true,
				'tipo'=>true,
				'serie'=>true,
				'numero'=>true,
				'cliente_nomb'=>true,
				'cliente_doc'=>true,
				'tipo_doc'=>true,
				'total_ope_gravadas'=>true,
				'total_igv'=>true,
				'total'=>true,
				'estado'=>true,
			),'sort'=>array(
				'fecemi'=>1,
				'serie'=>1,
				'numero'=>1
		)))->get("all")->items;



		if(is_null($comp) && !is_null($ecom)){
			$comp=$ecom;
		}elseif (is_null($ecom) && !is_null($comp)) {
			$comp=$comp;
		}elseif (!is_null($ecom) && !is_null($comp)) {
			$comp=array_merge($comp,$ecom);
		}
		
		
		if($f->request->data['type']=='xls'){
			$f->response->view("fa/repo.registro_ventas.xls",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("fa/repo.registro_ventas.pdf",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}
	}
	function execute_reporte_movimientos(){
		global $f;
		$movimientos = $f->model('lg/movi')->params(array(
			'filter'=>array(
				'producto._id'=>new MongoId($f->request->data['prod']),
				//'producto._id'=>new MongoId('5706745b8e7358ac07000029'), //URCIN
				//'producto._id'=>new MongoId('57091ab38e73584c0800002d'), //VALPRAX
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['ini'])),
					'$lt'=>new MongoDate(strtotime($f->request->data['fin'].' +1 day'))
				),
				//'estado'=>'E'
			),
			'sort'=>array('fecreg'=>1)
		))->get('all')->items;
		//print_r($movimientos);die();
		$resumen = array();
		foreach ($movimientos as $i=>$movi) {
			if(!isset($resumen[$movi['producto']['_id']->{'$id'}])){
				$resumen[$movi['producto']['_id']->{'$id'}] = array(
					'producto' => $movi['producto'],
					'total' => 0,
					'ent'=>0,
					'sal'=>0
				);
			}
			if(isset($movi['comprobante'])){
				$comp=$f->model('cj/ecom')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				if(is_null($comp)) $comp=$f->model('cj/comp')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				$movimientos[$i]['comprobante'] = $comp;
				foreach ($movimientos[$i]['comprobante']['items'] as $item) {
					if(isset($item['conceptos'])){
						foreach ($item['conceptos'] as $j => $concepto) {
							#Si el producto esta en concepto, entonces es un comprobante electronico
							if(isset($concepto['producto'])){
								if($concepto['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
									$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($item['monto']) * floatval($item['cant']);
									break;
								}
							}
						}
					}
					#Si el producto esta en item es un comprobante manual
					if(isset($item['producto'])){
						if($item['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
							$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($item['monto']) * floatval($item['cant']);
							break;
						}
					}
				}
			}
			if(isset($movi['guia'])){
				$guia_comprobante=$f->model('fa/guia')->params(array('_id'=>$movi['guia']))->get('one')->items;
				unset($movimientos[$i]['guia']);
				//$guia_temp_nomb=$guia_comprobante['guia'];
				if(!isset($guia_comprobante['guia']) || empty($guia_comprobante['guia'])) $guia_temp_nomb=$guia_comprobante['num'];
				else $guia_temp_nomb=$guia_comprobante['guia'];
				$movimientos[$i]['guia']['nomb']=$guia_temp_nomb;
				$movimientos[$i]['guia']['num']=$guia_comprobante['num'];
				//PNER ID EN EL REPORTE
				$movimientos[$i]['guia']['_id']=$guia_comprobante['_id'];
				$movimientos[$i]['guia']['num']=$guia_comprobante['num'];
				#$movimientos[$i]['guia']['nomb']=-$guia_comprobante['nomb'];
				$resumen[$movi['producto']['_id']->{'$id'}]['ent'] += floatval($movi['cant']);
			}elseif(isset($movi['comprobante'])){
				$resumen[$movi['producto']['_id']->{'$id'}]['sal'] -= floatval($movi['cant']);
			}
		}
		$f->response->view("fa/repo.reporte_movimientos.xls",array(
			'data'=>$movimientos,
			'resumen'=>$resumen
		));
	}
	function execute_listado_prod(){
		global $f;
		$alma=$f->model("lg/alma")->get("farmacia")->items;
		$productos = $f->model('lg/prod')->params(array(
			'filter'=>array(
				'modulo'=>'FA',
				//'old_id'=>array('$exists'=>1),
				'generico'=>array('$exists'=>1),
			)
		))->get('filter_all')->items;
		foreach ($productos as $p => $prod) {
			$stock = $f->model('lg/stck')->params(array(
				'producto'=>$prod["_id"],
				'almacen'=>$alma["_id"],
			))->get('prod')->items;
			$productos[$p]['stock']=$stock['stock'];;
		}
		//print_r(count($productos));die();
		//$stck = $f->model('lg/stck')->params(array(
		//	'filter'=>array(
		//		'almacen'=>$alma['_id'],
		//	)
		//))->get('all')->items;

		
		//print_r($productos);print_r($stck);die();

		$f->response->view("fa/repo.listado_prod.xls",array(
			'data'=>$productos,
		));
	}
}
?>