<?php
class Controller_ct_lidi extends Controller {
	function execute_bene() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Periodo: <input type="text" name="periodo" />');
		$f->response->print('<button name="btnCerrar">Cerrar Periodo</button>');
		$f->response->print('<button name="btnSel">Seleccionar Notas</button>');
		$f->response->print("</div>");
		$f->response->view("ct/lidi.bene");
	}
	function execute_suna() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Periodo: <input type="text" name="periodo" />');
		$f->response->print('<button name="btnCerrar">Cerrar Periodo</button>');
		$f->response->print('<button name="btnSel">Seleccionar Notas</button>');
		$f->response->print("</div>");
		$f->response->view("ct/lidi.suna");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/lidi")->params(array("mes"=>$f->request->data['mes'],"ano"=>$f->request->data['ano']))->get("periodo");
		$f->response->json( $model->items );
	}
	function execute_lista_suna(){
		global $f;
		$periodo = $f->request->data['ano'].((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'00';
		$model = $f->model("ct/lisu")->params(array("filter"=>array('periodo'=>$periodo)))->get("custom");
		$f->response->json( $model->items );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ct/lidi")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ct/lidi')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/lidi")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save_bene(){
		global $f;
		$data = $f->request->data;
		$libro = $f->model('ct/lidi')->params(array("mes"=>$f->request->data['mes'],"ano"=>$f->request->data['ano']))->get('periodo')->items;
		if($libro==null){
			$libro = $f->model('ct/lidi')->params(array('data'=>array(
				'cerrado'=>false,
				'periodo'=>array(
					'ano'=>$f->request->data['ano'],
					'mes'=>$f->request->data['mes']
				)
			)))->save('insert')->items;
		}else{
			$f->model('ct/lidi')->params(array('_id'=>$libro['_id'],'data'=>array(
				'$unset'=>array(
					'notas'=>true,
					'debe_ini'=>true,
					'haber_ini'=>true,
					'debe_fin'=>true,
					'haber_fin'=>true
				)
			)))->save('custom');
		}
		$libro['notas'] = array();
		$libro['debe_fin'] = 0;
		$libro['haber_fin'] = 0;
		foreach ($data['notas'] as $item){
			$nota = array(
				'_id'=>new MongoId($item['_id']),
				'numero'=>$item['num'],
				'concepto'=>$item['concepto'],
				'cuentas'=>array()
			);
			foreach ($item['cuentas'] as $cta){
				$cod = $cta['cuenta']['cod'];
				$sig = substr_count($cod,'.');
				$tot = (strlen($cod)-$sig);
				if($tot==4){
					if($cta['tipo']=='D') $libro['debe_fin'] += floatval($cta['monto']);
					else $libro['haber_fin'] += floatval($cta['monto']);
				}
				//if($tot==4||$tot==6){
				if($sig<2){//$tot>=4
					$nota['cuentas'][] = array(
						'tipo'=>$cta['tipo'],
						'monto'=>floatval($cta['monto']),
						'cuenta'=>array(
							'_id'=>new MongoId($cta['cuenta']['_id']),
							'cod'=>$cta['cuenta']['cod'],
							'descr'=>$cta['cuenta']['descr']
						)
					);
				}
			}
			$libro['notas'][] = $nota;
		}
		if(intval($f->request->data['mes'])==1){
			$libro['debe_ini'] = 0;
			$libro['haber_ini'] = 0;
		}else{
			$libro_old = $f->model('ct/lidi')->params(array("mes"=>''.(intval($f->request->data['mes'])-1),"ano"=>$f->request->data['ano']))->get('periodo')->items;
			$libro['debe_ini'] = floatval($libro_old['debe_fin']);
			$libro['haber_ini'] = floatval($libro_old['haber_fin']);
		}
		$libro['debe_fin'] += $libro['debe_ini'];
		$libro['haber_fin'] += $libro['haber_ini'];
		$f->model("ct/lidi")->params(array('_id'=>$libro['_id'],'data'=>$libro))->save("update");
		$f->response->print("true");
	}
	function execute_cerrar_bene(){
		global $f;
		$libro = $f->model('ct/lidi')->params(array("mes"=>$f->request->data['mes'],"ano"=>$f->request->data['ano']))->get('periodo')->items;
		if($libro!=null){
			$f->model('ct/lidi')->params(array('_id'=>$libro['_id'],'data'=>array(
				'cerrado'=>true,
				'fec'=>new MongoDate(),
				'autor'=>$f->session->userDB
			)))->save('update');
		}
		$f->response->print("true");
	}
	function execute_folio_bene(){
		global $f;
		$data = $f->request->data;
		$libro = $f->model('ct/lidi')->params(array("mes"=>$data['mes'],"ano"=>$data['ano']))->get('periodo')->items;
		if($libro!=null){
			$f->model('ct/lidi')->params(array('_id'=>$libro['_id'],'data'=>array(
				'notas.'.$data['nota'].'.cuentas.'.$data['cuenta'].'.folio'=>$data['folio']
			)))->save('update');
		}
		$f->response->print("true");
	}
	function execute_save_suna(){
		global $f;
		$data = $f->request->data;
		$periodo = $f->request->data['ano'].((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'00';
		$libros = $f->model('ct/lisu')->params(array('filter'=>array(
			"periodo"=>$periodo
		)))->get('custom')->items;
		if($libros!=null){
			foreach ($libros as $item){
				$f->model('ct/lisu')->params(array('_id'=>$item['_id']))->delete('item');
			}
		}
		foreach ($data['notas'] as $item){
			foreach ($item['cuentas'] as $cta){
				$cod = $cta['cuenta']['cod'];
				$sig = substr_count($cod,'.');
				$tot = (strlen($cod)-$sig);
				//if($cta['ultimo']=='true'){
				if($sig<2){//$tot<=10
					$tmp = array(
						'periodo'=>$periodo,
						'estado'=>'A',
						'plan'=>array(
							'_id'=>new MongoId($f->request->pcta['_id']),
							'cod'=>$f->request->pcta['cod']
						),
						'fec'=>new MongoDate(),
						'cuenta'=>array(
							'_id'=>new MongoId($cta['cuenta']['_id']),
							'cod'=>$cta['cuenta']['cod'],
							'descr'=>$cta['cuenta']['descr']
						),
						'tipo'=>$cta['tipo'],
						'debe'=>0,
						'haber'=>0,
						'estado_sunat'=>'1'
					);
					$tmp['cod'] = $f->model('ct/lisu')->params(array('periodo'=>$tmp['periodo']))->get('cod')->items;
					if($tmp['tipo']=='D') $tmp['debe'] = floatval($cta['monto']);
					else $tmp['haber'] = floatval($cta['monto']);
					$f->model('ct/lisu')->params(array('data'=>$tmp))->save('insert');
				}
			}
		}
		$f->response->print("true");
	}
	function execute_comp_suna(){
		global $f;
		$data = $f->request->data;
		if(isset($data['cod_libro']['_id'])) $data['cod_libro']['_id'] = new MongoId($data['cod_libro']['_id']);
		$f->model('ct/lisu')->params(array(
			'_id'=>new MongoId($data[_id]),
			'data'=>$data
		))->save('update');
		$f->response->print("true");
	}
	function execute_cerrar_suna(){
		global $f;
		$periodo = $f->request->data['ano'].((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'00';
		$libros = $f->model('ct/lisu')->params(array('filter'=>array(
			"periodo"=>$periodo
		)))->get('custom')->items;
		if($libros!=null){
			foreach ($libros as $item){
				$f->model('ct/lisu')->params(array('_id'=>$item['_id'],'data'=>array('estado'=>'C')))->save('update');
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/lidi.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/lidi.select");
	}
	function execute_folio(){
		global $f;
		$f->response->view("ct/lidi.folio");
	}
	function execute_comp(){
		global $f;
		$f->response->view("ct/lisu.comp");
	}
}
?>