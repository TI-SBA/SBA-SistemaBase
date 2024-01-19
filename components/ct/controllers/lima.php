<?php
class Controller_ct_lima extends Controller {
	function execute_bene() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Periodo: <input type="text" name="periodo" />');
		$f->response->print('<button name="btnCerrar">Cerrar Periodo</button>');
		$f->response->print('<button name="btnSel">Seleccionar Notas</button>
			<br />
		<table>
			<tr>
				<td><label>Cuenta</label></td>
				<td><span name="cuenta"></span>&nbsp;<button name="btnCta">Seleccionar</button></td>
				<td><span name="descr"></span></td>
			</tr>
		</table>');
		$f->response->print("</div>");
		$f->response->view("ct/lima.bene");
	}
	function execute_suna() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Periodo: <input type="text" name="periodo" />');
		$f->response->print('<button name="btnCerrar">Cerrar Periodo</button>');
		$f->response->print('<button name="btnSel">Generar Libro Mayor</button>
			<br />
		<table>
			<tr>
				<td><label>Cuenta</label></td>
				<td><span name="cuenta"></span>&nbsp;<button name="btnCta">Seleccionar</button></td>
				<td><span name="descr"></span></td>
			</tr>
		</table>');
		$f->response->print("</div>");
		$f->response->view("ct/lima.suna");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/lima")->params(array("mes"=>$f->request->data['mes'],"ano"=>$f->request->data['ano'],'cuenta'=>new MongoId($f->request->data['cuenta'])))->get("periodo");
		$f->response->json( $model->items );
	}
	function execute_lista_suna(){
		global $f;
		$periodo = $f->request->data['ano'].((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'00';
		$model = $f->model("ct/lmsu")->params(array("filter"=>array('periodo'=>$periodo,'cuenta._id'=>new MongoId($f->request->data['cuenta']))))->get("custom");
		$f->response->json( $model->items );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ct/lima")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ct/lima')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/lima")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'H';
			$f->model("ct/lima")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/lima")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_bene(){
		global $f;
		$data = $f->request->data;
		$libro = $f->model('ct/lima')->params(array("cuenta"=>new MongoId($f->request->data['cuenta']['_id']),"mes"=>$f->request->data['mes'],"ano"=>$f->request->data['ano']))->get('periodo')->items;
		if($libro==null){
			$libro = $f->model('ct/lima')->params(array('data'=>array(
				'estado'=>'A',
				'periodo'=>array(
					'ano'=>$f->request->data['ano'],
					'mes'=>$f->request->data['mes']
				),
				'cuenta'=>array(
					'_id'=>new MongoId($f->request->data['cuenta']['_id']),
					'cod'=>$f->request->data['cuenta']['cod'],
					'descr'=>$f->request->data['cuenta']['descr']
				)
			)))->save('insert')->items;
		}else{
			$f->model('ct/lima')->params(array('_id'=>$libro['_id'],'data'=>array(
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
				'num'=>$item['num'],
				'tipo_nota'=>$item['tipo']
			);
			$nota['tipo_nota']['_id'] = new MongoId($nota['tipo_nota']['_id']);
			foreach ($item['cuentas'] as $cta){
				if($cta['cuenta']['_id']==$data['cuenta']['_id']){
					$nota['tipo'] = $cta['tipo'];
					$nota['monto'] = floatval($cta['monto']);
					if($nota['tipo']=='D') $libro['debe_fin'] += floatval($cta['monto']);
					else $libro['haber_fin'] += floatval($cta['monto']);
				}
			}
			$tmp = array();
			foreach ($item['cuentas'] as $cta){
				if($nota['tipo']!=$cta['tipo']){
					$tmp[] = $cta['cuenta']['descr'];
				}
			}
			if(sizeof($tmp)==1) $nota['concepto'] = $tmp[0];
			else{
				if($nota['tipo']=='D') $nota['concepto'] = "A varios";
				else $nota['concepto'] = "Por varios";
			}
			$libro['notas'][] = $nota;
		}
		if(intval($f->request->data['mes'])==1){
			$libro['debe_ini'] = 0;
			$libro['haber_ini'] = 0;
		}else{
			$libro_old = $f->model('ct/lima')->params(array("cuenta"=>new MongoId($f->request->data['cuenta']['_id']),"mes"=>''.(intval($f->request->data['mes'])-1),"ano"=>$f->request->data['ano']))->get('periodo')->items;
			$libro['debe_ini'] = floatval($libro_old['debe_fin']);
			$libro['haber_ini'] = floatval($libro_old['haber_fin']);
		}
		$libro['debe_fin'] += $libro['debe_ini'];
		$libro['haber_fin'] += $libro['haber_ini'];
		$f->model("ct/lima")->params(array('_id'=>$libro['_id'],'data'=>$libro))->save("update");
		$f->response->print("true");
	}
	function execute_cerrar_bene(){
		global $f;
		$data = $f->request->data;
		$libros = $f->model('ct/lima')->params(array('filter'=>array("periodo.mes"=>$data['mes'],"periodo.ano"=>$data['ano'])))->get('custom')->items;
		if($libros!=null){
			foreach ($libros as $libro){
				$f->model('ct/lima')->params(array('_id'=>$libro['_id'],'data'=>array(
					'estado'=>'C',
					'fec'=>new MongoDate(),
					'autor'=>$f->session->userDB
				)))->save('update');
			}
		}
		$f->response->print("true");
	}
	function execute_folio_bene(){
		global $f;
		$data = $f->request->data;
		$libro = $f->model('ct/lima')->params(array("cuenta"=>new MongoId($f->request->data['cuenta']),"mes"=>$data['mes'],"ano"=>$data['ano']))->get('periodo')->items;
		if($libro!=null){
			$f->model('ct/lima')->params(array('_id'=>$libro['_id'],'data'=>array(
				'notas.'.$data['nota'].'.folio'=>$data['folio']
			)))->save('update');
		}
		$f->response->print("true");
	}
	function execute_save_suna(){
		global $f;
		$data = $f->request->data;
		$periodo = $f->request->data['ano'].((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'00';
		$libros = $f->model("ct/lmsu")->params(array("filter"=>array(
			'periodo'=>$periodo,
			'cuenta._id'=>new MongoId($f->request->data['cuenta'])
		)))->get("custom")->items;
		if($libros!=null){
			foreach ($libros as $item){
				$f->model('ct/lmsu')->params(array('_id'=>$item['_id']))->delete('item');
			}
		}
		$libros = $f->model('ct/lisu')->params(array('filter'=>array(
			"periodo"=>$periodo,
			'cuenta._id'=>new MongoId($f->request->data['cuenta'])
		)))->get('custom')->items;
		if($libros!=null){
			if($libros[0]['estado']=='A'){
				$f->response->json(array('rpta'=>false));
			}else{
				foreach ($libros as $item){
					$tmp = array(
						'periodo'=>$periodo,
						'estado'=>'A',
						'fec'=>new MongoDate(),
						'cuenta'=>array(
							'_id'=>$item['cuenta']['_id'],
							'cod'=>$item['cuenta']['cod'],
							'descr'=>$item['cuenta']['descr']
						),
						'tipo'=>$item['tipo'],
						'debe'=>$item['debe'],
						'haber'=>$item['haber'],
						'glosa'=>$item['glosa'],
						'estado_sunat'=>'1'
					);
					$tmp['cod'] = $f->model('ct/lmsu')->params(array('periodo'=>$tmp['periodo']))->get('cod')->items;
					$f->model('ct/lmsu')->params(array('data'=>$tmp))->save('insert');
				}
				$f->response->json(array('rpta'=>true));
			}
		}else $f->response->json(array('rpta'=>false));
	}
	function execute_comp_suna(){
		global $f;
		$data = $f->request->data;
		$f->model('ct/lmsu')->params(array(
			'_id'=>new MongoId($data[_id]),
			'data'=>$data
		))->save('update');
		$f->response->print("true");
	}
	function execute_cerrar_suna(){
		global $f;
		$periodo = $f->request->data['ano'].((strlen($f->request->data['mes'])==1)?'0'.$f->request->data['mes']:$f->request->data['mes']).'00';
		$libros = $f->model('ct/lmsu')->params(array('filter'=>array(
			"periodo"=>$periodo
		)))->get('custom')->items;
		if($libros!=null){
			foreach ($libros as $item){
				$f->model('ct/lmsu')->params(array('_id'=>$item['_id'],'data'=>array('estado'=>'C')))->save('update');
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/lima.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/lima.select");
	}
	function execute_folio(){
		global $f;
		$f->response->view("ct/lima.folio");
	}
	function execute_comp(){
		global $f;
		$f->response->view("ct/lmsu.comp");
	}
}
?>