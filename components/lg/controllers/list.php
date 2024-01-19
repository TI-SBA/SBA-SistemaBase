<?php
class Controller_lg_list extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('lg/list');
	}
	function execute_resumen(){
		global $f;
		$cuentas = array();
		$tmp_cuentas = $f->model('ct/pcon')->params(array(
			'filter'=>array(
				'logistica'=>'true'
			)
		))->get('custom')->items;
		foreach ($tmp_cuentas as $key => $cta) {
			$cuentas[$cta['cod']] = $cta;
		}
		$movs = $f->model('lg/movi')->params(array(
			'mes'=>$f->request->data['mes'],
			'ano'=>$f->request->data['ano'],
			'init'=>true,
			'fields'=>array(
				'cant'=>true,
				'cuenta'=>true,
				'clasif'=>true,
				'tipo'=>true,
				'fecreg'=>true,
				'saldo'=>true,
				'total'=>true,
				'saldo_imp'=>true
			)
		))->get('periodo')->items;
		/*foreach ($movs as $key => $value) {
			echo $value['_id'].'  --  '.$value['cuenta']['cod'].'  --  '.date('Y-m-d',$value['fecreg']->sec)."<br />";
		}
		die();*/
		foreach ($movs as $key => $mov) {
			if(!isset($cuentas[$mov['cuenta']['cod']]['total'])){
				$cuentas[$mov['cuenta']['cod']]['total'] = 0;
				$cuentas[$mov['cuenta']['cod']]['ent'] = 0;
				$cuentas[$mov['cuenta']['cod']]['sal'] = 0;
			}
			switch ($mov['tipo']) {
				case 'E':
					$cuentas[$mov['cuenta']['cod']]['ent'] += $mov['total'];
					break;
				case 'S':
					$cuentas[$mov['cuenta']['cod']]['sal'] += $mov['total'];
					break;
			}
			$cuentas[$mov['cuenta']['cod']]['total'] = $mov['saldo_imp'];
		}
		print_r($cuentas);die();
		if($f->request->data['tipo']=='xls'){
			
		}else{
			//
		}
	}
}
?>