<?php
class Controller_ag_repo extends Controller {
	/*	function execute_registro_ventas_legacy(){
		global $f;
		$fec = $f->request->data['ano'].'-'.$f->request->data['mes'].'-01';
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'modulo'=>'AG',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			),
			//'estado'=>array('$ne'=>'X')
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
		
		if($f->request->data['type']=='xls'){
			//$f->response->view("ag/repo.registro_ventas.xls",array(
			$f->response->view("fa/repo.registro_ventas.xls",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			//$f->response->view("ag/repo.registro_ventas.pdf",array(
			$f->response->view("fa/repo.registro_ventas.pdf",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}
	}*/
	function execute_index(){
		global $f;
		$f->response->view('ag/repo');
	}
	function execute_registro_ventas(){
		global $f;
		$fec = $f->request->data['ano'].'-'.$f->request->data['mes'].'-01';
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'modulo'=>'AG',
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
			'items.tipo' => array('$in' => array('agua_chapi')),
			//'serie' => array('$in' => array('B003','F003')),
			'estado' => array('$in' => array('FI','ES')),
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
				'total'=>true,
				'total_ope_gravadas'=>true,
				'total_igv'=>true,
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
	/*function execute_reporte_movimientos_legacy(){
		global $f;
		$movimientos = $f->model('ag/movi')->params(array(
			'filter'=>array(
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
		$f->response->view("ag/repo.reporte_movimientos.xls",array(
			'data'=>$movimientos,
			'resumen'=>$resumen
		));
	}*/
	function execute_reporte_movimientos(){
		global $f;
		if(isset($f->request->data['almacen'])) $almacen=$f->model("lg/alma")->params(array('almacen._id'=>$almacen["_id"]))->get("one")->items;
		else $almacen=$f->model("lg/alma")->get("agua_chapi")->items;
		$movimientos = $f->model('lg/movi')->params(array(
			'filter'=>array(
				'fecreg'=>array(
					'$gte'=>new MongoDate(strtotime($f->request->data['ini'])),
					'$lt'=>new MongoDate(strtotime($f->request->data['fin'].' +1 day'))
				),
				'almacen._id'=>$almacen["_id"],
			),
			'sort'=>array('fecreg'=>1)
		))->get('all')->items;
		$resumen = array();
		//print_r($movimientos);
		foreach ($movimientos as $i=>$movi) {

			//print_r($resumen);
			//print_r($movi);
			//die();

			if(!isset($resumen[$movi['producto']['_id']->{'$id'}])){
				$resumen[$movi['producto']['_id']->{'$id'}] = array(
					'producto' => $movi['producto'],
					'total' => 0,
					'ent'=>0,
					'sal'=>0
				);
			}
			/*
			if(isset($movi['comprobante'])){
				$comprobante = $f->model('cj/ecom')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				if(isset($comprobante)) $comprobante = $f->model('cj/comp')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				$movimientos[$i]['comprobante'] = $comprobante;
				//$movimientos[$i]['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
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
			*/
			/**
			*	SALIDAS DE ALMACEN
			*/
			if(isset($movi['comprobante'])){
				$comp=$f->model('cj/ecom')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				if(is_null($comp)) $comp=$f->model('cj/comp')->params(array('_id'=>$movi['comprobante']['_id']))->get('one')->items;
				$movimientos[$i]['comprobante'] = $comp;
				foreach ($movimientos[$i]['comprobante']['items'] as $item) {
					if(isset($item['conceptos'])){
						foreach ($item['conceptos'] as $j => $concepto) {
							#Si el producto existe en el concepto, es un comprobante electronico
							if(isset($concepto['producto'])){
								if($concepto['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
									//print_r($concepto);
									//die();
									//$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($item['monto']) * floatval($item['cant']);
									$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($concepto['monto']) * floatval($concepto['cant']);
									break;
								}
							}
						}
					}
					#Si el producto se encuentra en item es un comprobante manual
					if(isset($item['producto'])){
						if($item['producto']['_id']->{'$id'}==$movi['producto']['_id']->{'$id'}){
							$resumen[$movi['producto']['_id']->{'$id'}]['total'] += floatval($item['monto']) * floatval($item['cant']);
							break;
						}
					}
				}
			}
			/**
			*	ENTRADAS DE ALMACEN
			*/
			if(isset($movi['guia'])){
				$guia_comprobante=$f->model('ag/guia')->params(array('_id'=>$movi['guia']))->get('one')->items;
				$guia_temp_nomb=$movimientos[$i]['glosa'];
				unset($movimientos[$i]['guia']);
				if(isset($guia_comprobante['glosa']) || empty($guia_comprobante['glosa'])) $guia_temp_nomb=$guia_comprobante['num'];
				$movimientos[$i]['guia']['nomb']=$guia_temp_nomb;
				$movimientos[$i]['guia']['num']=$guia_comprobante['num'];
				#SALIDA DE ALMACEN
				if($guia_comprobante['almacen_origen']!=null){
					$resumen[$movi['producto']['_id']->{'$id'}]['sal'] -= floatval($movi['cant']);
					$movimientos[$i]['guia']['salida']=true;
				}
				#ENTRADA DE ALMACEN
				if($guia_comprobante['almacen_destino']!=null){
					$resumen[$movi['producto']['_id']->{'$id'}]['ent'] += floatval($movi['cant']);
					$movimientos[$i]['guia']['entrada']=true;
				}
				
			}elseif(isset($movi['comprobante'])){
				$resumen[$movi['producto']['_id']->{'$id'}]['sal'] -= floatval($movi['cant']);
			}
		}
		//print_r($resumen);die();
		$f->response->view("ag/repo.reporte_movimientos.xls",array(
			'data'=>$movimientos,
			'resumen'=>$resumen
		));
	}
}
?>