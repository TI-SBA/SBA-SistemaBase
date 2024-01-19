<?php
class Controller_lg_cuad extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("lg/cuad")->params($params)->get("lista") );
	}
	function execute_lista_all(){
		global $f;
		$model = $f->model("lg/cuad")->params(array("periodo"=>$f->request->periodo,"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista_all");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("lg/cuad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('lg/cuad')->get('all');
		$f->response->json($model->items);
	}
	function execute_cons_get(){
		global $f;
		$model = $f->model('lg/cuad')->params(array("periodo"=>$f->request->periodo))->get('cons');
		if($model->items!=null){
			foreach ($model->items as $index=>$cuad) {
				$model->items[$index]['organizacion']['sigla'] = $f->model('mg/orga')->params(array('_id'=>$cuad['organizacion']['_id']))->get('one')->items['sigla'];
			}
		}
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$cuad = $f->model("lg/cuad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($cuad['items'])){
			foreach ($cuad['items'] as $i=>$item){
				if(isset($item['producto']))
					$cuad['items'][$i]['producto'] = $f->model('lg/prod')->params(array(
						'_id'=>$item['producto'],
						'fields'=>array('nomb'=>true,'cod'=>true,'unidad'=>true)
					))->get('one')->items;
				if(isset($item['unidad']))
					$cuad['items'][$i]['unidad'] = $f->model('lg/unid')->params(array('_id'=>$item['unidad']))->get('one')->items;
				if(isset($item['clasif']))
					$cuad['items'][$i]['clasif'] = $f->model('pr/clas')->params(array('_id'=>$item['clasif']))->get('one')->items;
				if(isset($item['cuenta']))
					$cuad['items'][$i]['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>$item['cuenta']))->get('one')->items;
			}
			if(isset($f->request->data['historico'])){
				$cuad['historico'] = $f->model('lg/cuhi')->params(array('cuadro'=>$cuad['_id'],'fields'=>array('fecmod'=>true,'estado'=>true)))->get('all')->items;
				/*foreach ($cuad['historico'] as $key => $value) {
					foreach ($value['items'] as $i=>$item){
						if(isset($item['producto']))
							$cuad['historico'][$key]['items'][$i]['producto'] = $f->model('lg/prod')->params(array(
								'_id'=>$item['producto'],
								'fields'=>array('nomb'=>true,'cod'=>true,'unidad'=>true)
							))->get('one')->items;
						if(isset($item['unidad']))
							$cuad['historico'][$key]['items'][$i]['unidad'] = $f->model('lg/unid')->params(array('_id'=>$item['unidad']))->get('one')->items;
						if(isset($item['clasif']))
							$cuad['historico'][$key]['items'][$i]['clasif'] = $f->model('pr/clas')->params(array('_id'=>$item['clasif']))->get('one')->items;
						if(isset($item['cuenta']))
							$cuad['historico'][$key]['items'][$i]['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>$item['cuenta']))->get('one')->items;
					}
				}*/
			}
		}
		$f->response->json( $cuad );
	}
	function execute_get_hist(){
		global $f;
		$cuad = $f->model("lg/cuhi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($cuad['items'])){
			foreach ($cuad['items'] as $i=>$item){
				if(isset($item['producto']))
					$cuad['items'][$i]['producto'] = $f->model('lg/prod')->params(array(
						'_id'=>$item['producto'],
						'fields'=>array('nomb'=>true,'cod'=>true,'unidad'=>true)
					))->get('one')->items;
				if(isset($item['unidad']))
					$cuad['items'][$i]['unidad'] = $f->model('lg/unid')->params(array('_id'=>$item['unidad']))->get('one')->items;
				if(isset($item['clasif']))
					$cuad['items'][$i]['clasif'] = $f->model('pr/clas')->params(array('_id'=>$item['clasif']))->get('one')->items;
				if(isset($item['cuenta']))
					$cuad['items'][$i]['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>$item['cuenta']))->get('one')->items;
			}
		}
		$f->response->json( $cuad );
	}
	function execute_get_new(){
		global $f;
		$rpta = array(
			'unidades'=>$f->model('lg/unid')->get('all')->items
		);
		$cuad = $f->model("lg/cuad")->params(array(
			"_id"=>$f->session->enti['roles']['trabajador']['oficina']['_id'],
			'todo'=>true
		))->get("orga")->items;
		if($cuad!=null){
			if(isset($cuad['items'])){
				foreach ($cuad['items'] as $i=>$item){
					if(isset($item['producto']))
						$cuad['items'][$i]['producto'] = $f->model('lg/prod')->params(array('_id'=>$item['producto'],'fields'=>array('nomb'=>true,'cod'=>true,'unidad'=>true)))->get('one')->items;
					if(isset($item['unidad']))
						$cuad['items'][$i]['unidad'] = $f->model('lg/unid')->params(array('_id'=>$item['unidad']))->get('one')->items;
					if(isset($item['clasif']))
						$cuad['items'][$i]['clasif'] = $f->model('pr/clas')->params(array('_id'=>$item['clasif']))->get('one')->items;
					if(isset($item['cuenta']))
						$cuad['items'][$i]['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>$item['cuenta']))->get('one')->items;
				}
			}
			$rpta['cuadro'] = $cuad;
		}
		$f->response->json($rpta);
	}
	function execute_get_orga(){
		global $f;
		$model = $f->model("lg/cuad")->params(array("_id"=>new MongoId($f->request->id)))->get("orga");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(isset($data['fecvig'])) $data['fecvig'] = new MongoDate(strtotime($data['fecvig']));
		if(isset($data['items'])){
			foreach ($data['items'] as $index=>$item){
				if(isset($item['clasif'])) $data['items'][$index]['clasif'] = new MongoId($item['clasif']);
				if(isset($item['cuenta'])) $data['items'][$index]['cuenta'] = new MongoId($item['cuenta']);
				if(isset($item['producto'])) $data['items'][$index]['producto'] = new MongoId($item['producto']);
				if(isset($item['unidad'])) $data['items'][$index]['unidad'] = new MongoId($item['unidad']);
				if(isset($item['saldo'])) $data['items'][$index]['saldo'] = floatval($item['saldo']);
				if(isset($item['cant'])) $data['items'][$index]['cant'] = floatval($item['cant']);
				if(isset($item['aceptado'])){
					if($item['aceptado']=='true') $data['items'][$index]['aceptado'] = true;
					elseif($item['aceptado']=='false') $data['items'][$index]['aceptado'] = false;
				}
			}
		}
		if(isset($data['totales_clasif'])){
			foreach ($data['totales_clasif'] as $index=>$item){
				$data['totales_clasif'][$index]['clasif']['_id'] = new MongoId($item['clasif']['_id']);
			}
		}
		if(isset($data['aprobacion'])){
			$data['aprobacion']['fec'] = new MongoDate();
			$data['aprobacion']['trabajador']['_id'] = new MongoId($data['aprobacion']['trabajador']['_id']);
			$cuad = $f->model('lg/cuad')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
			foreach ($cuad['items'] as $i=>$prod){
				$data['items.'.$i.'.saldo'] = (int)$prod['cant'];
			}
			$data['vigente'] = false;
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['trabajador'] = $f->session->userDB;
			$data['organizacion'] = $f->session->enti['roles']['trabajador']['oficina'];
			$data = $f->model("lg/cuad")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Cuadro de Necesidades',
				'descr'=>'Se cre&oacute; el Cuadro de Necesidades para <b>'.$data['organizacion']['nomb'].'</b> del periodo <b>'.$data['periodo'].'</b>.'
			))->save('insert');
		}else{
			$f->model("lg/cuad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$data['_id'] = new MongoId($f->request->data['_id']);
			$cuad = $f->model('lg/cuad')->params(array('_id'=>$data['_id']))->get('one')->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Cuadro de Necesidades',
				'descr'=>'Se actualiz&oacute; el Cuadro de Necesidades para <b>'.$cuad['organizacion']['nomb'].'</b> del periodo <b>'.$cuad['periodo'].'</b>.'
			))->save('insert');
		}
		$cuad = $f->model('lg/cuad')->params(array('_id'=>$data['_id']))->get('one')->items;
		$f->model("lg/cuhi")->params(array('data'=>array(
			'fecmod'=>new MongoDate(),
			'modificador'=>$f->session->userDB,
			'estado'=>$cuad['estado'],
			'precio_total'=>$cuad['precio_total'],
			'totales_clasif'=>$cuad['totales_clasif'],
			'cuadro'=>$cuad['_id'],
			'items'=>$cuad['items']
		)))->save("insert");
		$f->response->print("true");
	}
	function execute_vigente(){
		global $f;
		$f->model("lg/cuad")->params(array('orga'=>new MongoId($f->request->data['orga'])))->save("reset_vig");
		$f->model("lg/cuad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array('vigente'=>true)))->save("update");
		$cuad = $f->model('lg/cuad')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'LG',
			'bandeja'=>'Cuadro de Necesidades',
			'descr'=>'Se estableci&oacute; como <b>Vigente</b> el Cuadro de Necesidades de <b>'.$cuad['organizacion']['nomb'].'</b> para el periodo <b>'.$cuad['periodo'].'</b>.'
		))->save('insert');
	}
	function execute_delete(){
		global $f;
		$item = $f->model('lg/cuad')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'lg_necesidades';
		$item['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($item);
		$f->datastore->lg_necesidades->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/cuad.details");
	}
	function execute_details_prog(){
		global $f;
		$f->response->view("lg/cuad.details.prog");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/cuad.edit");
	}
	function execute_edit_prog(){
		global $f;
		$f->response->view("lg/cuad.edit.prog");
	}
	function execute_select(){
		global $f;
		$f->response->view("lg/cuad.select");
	}
	function execute_cons(){
		global $f;
		$f->response->view("lg/cuad.cons");
	}
	function execute_ampli(){
		global $f;
		$f->response->view("lg/cuad.ampli");
	}
	function execute_excel(){
		global $f;
		$data = $f->model("lg/cuad")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($data['items'])){
			foreach ($data['items'] as $i=>$item){
				if(isset($item['producto']))
					$data['items'][$i]['producto'] = $f->model('lg/prod')->params(array('_id'=>$item['producto']))->get('one')->items;
				if(isset($item['unidad']))
					$data['items'][$i]['unidad'] = $f->model('lg/unid')->params(array('_id'=>$item['unidad']))->get('one')->items;
				if(isset($item['clasif']))
					$data['items'][$i]['clasif'] = $f->model('pr/clas')->params(array('_id'=>$item['clasif']))->get('one')->items;
				if(isset($item['cuenta']))
					$data['items'][$i]['cuenta'] = $f->model('ct/pcon')->params(array('_id'=>$item['cuenta']))->get('one')->items;
			}
		}
		$f->response->view("lg/cuad.excel",array('data'=>$data));
	}
	function execute_consolidado(){
		global $f;
		$data = $f->model('lg/cuad')->params(array("periodo"=>$f->request->periodo))->get('cons')->items;
		/*if($data!=null){
			foreach($data as $i=>$cuad){
				$data[$i]['organizacion']['sigla'] = $f->model('mg/orga')->params(array('_id'=>$cuad['organizacion']['_id']))->get('one')->items['sigla'];
			}
		}*/
		$clasifs = array();
		$clasifs_i = array();
		if($data!=null){
			foreach($data as $k => $cuad){
				foreach ($cuad['totales_clasif'] as $clasif){
					if(array_search($clasif['clasif']['_id']->{'$id'}, $clasifs_i)===FALSE){
						$clasifs[] = $f->model('pr/clas')->params(array('_id'=>$clasif['clasif']['_id']))->get('one')->items;
						$clasifs_i[] = $clasif['clasif']['_id']->{'$id'};
					}
				}
			}
		}
		array_multisort($clasifs_i,SORT_ASC,$clasifs);
		$f->response->view("lg/cuad.consolidado",array(
			'data'=>$data,
			'clasifs'=>$clasifs,
			'clasifs_i'=>$clasifs_i
		));
	}
}
?>