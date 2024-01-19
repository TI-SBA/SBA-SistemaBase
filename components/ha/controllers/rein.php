<?php
class Controller_ha_rein extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			if($f->request->data['modulo']!='')
				$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/rein")->params($params)->get("lista") );
	}
	function execute_print(){
		global $f;
		$recibo = $f->model('cj/rein')->params(array(
			'_id'=>new MongoId($f->request->data['_id'])
		))->get('one')->items;
		$cuentas = array();
		//DETALLE
		foreach ($recibo['detalle'] as $k => $row) {
		    $ctas[$k] = $row['cuenta']['cod'];
			$row['comprobante'] = $f->model('cj/comp')->params(array('_id'=>$row['comprobante']['_id']))->get('one')->items;
			$recibo['detalle'][$k]['comprobante'] = $row['comprobante'];
			if(isset($cuentas[$row['cuenta']['cod']])){
				$cuentas[$row['cuenta']['cod']][] = $row;
			}else{
				$cuentas[$row['cuenta']['cod']] = array($row);
			}
		}
		array_multisort($ctas, SORT_ASC, $recibo['detalle']);
		//CONTABILIDAD PATRIMONIAL
		foreach ($recibo['cont_patrimonial'] as $k => $row) {
		    $ctasp[$k] = $row['cuenta']['cod'];
		    $ctast[$k] = $row['tipo'];
		}
		array_multisort($ctast,SORT_ASC,$ctasp,SORT_ASC,$recibo['cont_patrimonial']);
		//CUENTAS
		foreach ($cuentas as $k => $row) {
		    $ctasc[$k] = $row[0]['cuenta']['cod'];
		}
		array_multisort($ctasc,SORT_ASC,$cuentas);
		//print_r($recibo);die();
		$recibo['cuentas'] = $cuentas;
		$f->response->view("ha/rein.print",array('recibo'=>$recibo));
	}
}
?>