<?php
class Controller_lg_prod extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows);
		if(isset($f->request->data['cuenta']))
			$params['cuenta'] = new MongoId($f->request->data['cuenta']);
		if(isset($f->request->data['clasif']))
			$params['clasif'] = new MongoId($f->request->data['clasif']);
		if(isset($f->request->data['tipo']))
			$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['modulo']))
			$params['modulo'] = $f->request->data['modulo'];
		/*if(isset($f->request->data['almacen']))
			$params['almacen'] = new MongoId($f->request->data['almacen']);*/
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("lg/prod")->params($params)->get("lista");
		if(isset($f->request->data['almacen'])){
			//$alma=$f->model("lg/alma")->get("farmacia")->items;
			if($f->request->data['almacen']!='' && $f->request->data['almacen']!='0'){
				$allp=$model->items;
				/*if (isset($f->request->data['debug'])) {
					echo "<pre>";
					print_r($allp);
					echo "</pre>";
					die();
				}*/
				foreach($allp as $i=>$item){
					$stock = $f->model("lg/stck")->params(array(
						'producto'=>$item['_id'],
						'almacen'=>new MongoId($f->request->data['almacen'])
					))->get("prod")->items;
					if(isset($f->request->data['debug'])){
						echo "<pre>";
						print_r($item['_id']);
						print_r(new MongoId($f->request->data['almacen']));
						var_dump($stock);
						echo "</pre>";
						die();
					}
					//if($f->request->data['stock']=="directo") $model->items[$i]['stock'] = $stock['stock'];
					//else $model->items[$i]['stock'] = $stock;
					if(!isset($f->request->data['stock'])) $model->items[$i]['stock'] = $stock;
					else $model->items[$i]['stock'] =  $model->items[$i]['stock'] = $stock['stock'];
					//$model->items[$i]['stock'] = $stock['stock'];
				}
			}
		}
		$f->response->json( $model );
	}
	function execute_lista_stock(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		if(isset($f->request->data['almacen']))
			if($f->request->data['almacen']!='')
				$params['almacen'] = new MongoId($f->request->data['almacen']);
		$rpta = $f->model("lg/stck")->params($params)->get("lista");
		if($rpta->items!=null){
			foreach ($rpta->items as $key => $value) {
				$rpta->items[$key]['producto'] = $f->model("lg/prod")->params(array('_id'=>new MongoId($value['producto'])))->get("one")->items;
			}
		}
		$f->response->json($rpta);
	}
	function execute_all(){
		global $f;
		$model = $f->model('lg/prod')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	#PARCHE
	function execute_getget(){
		global $f;
		$model = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_details(){
		global $f;
		$prod = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$stock = $f->model("lg/stck")->params(array(
						'filter'=>array('producto'=>$prod['_id']),
						//'producto'=>$prod['_id'],
		))->get("all")->items;
		foreach ($stock as $i => $stck) {
			$alma=$f->model("lg/alma")->params(array('_id'=>$stck['almacen']))->get("one")->items;
			unset($stock[$i]['almacen']);
			$stock[$i]['almacen']['_id']=$alma['_id'];
			$stock[$i]['almacen']['nomb']=$alma['nomb'];
			$stock[$i]['actual']=$stock[$i]['stock'];
			unset($stock[$i]['stock']);
		}
		$prod['stock']=$stock;
		$detalle=array(
			'prod'=>$prod
		);
		$f->response->json($detalle);
	}
	/*function execute_get_details(){
		global $f;
		$prod = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( array(
			'prod'=>$prod->items
		) );
	}*/
	function execute_edit_data(){
		global $f;
		$cod = $f->model('lg/prod')->get('cod');
		/*$clasif = $f->model('pr/clas')->get('all');
		$cuenta = $f->model('ct/pcon')->get('all');*/
		$unid = $f->model('lg/unid')->get('all');
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
		$f->response->json( array(
			'cod'=>$cod->items,
			/*'clasif'=>$clasif->items,
			'cuenta'=>$cuenta->items,*/
			'unid'=>$unid->items
		) );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['clasif']['_id'])) $data['clasif']['_id'] = new MongoId($data['clasif']['_id']);
		if(isset($data['cuenta']['_id'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['unidad']['_id'])) $data['unidad']['_id'] = new MongoId($data['unidad']['_id']);
		if(isset($data['precio'])) $data['precio'] = floatval($data['precio']);
		if(isset($data['precio_venta'])) $data['precio_venta'] = floatval($data['precio_venta']);
		if(isset($data['precio_promedio'])) $data['precio_promedio'] = floatval($data['precio_promedio']);
		if(isset($data['modulo'])){
			if($data['modulo']=='--') unset($data['modulo']);
		}

		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['fecmod'] = new MongoDate();
			$data['estado'] = "H";
			$data['cant'] = 0;
			$data['autor'] = $f->session->userDBMin;
			$data['trabajador'] = $f->session->userDBMin;
			$data['valor_total'] = 0;
			$prod = $f->model("lg/prod")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'LG',
				'bandeja'=>'Productos',
				'descr'=>'Se cre&oacute; el producto <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$data['trabajador'] = $f->session->userDBMin;
			$data['fecmod'] = new MongoDate();
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'LG',
					'bandeja'=>'Productos',
					'descr'=>'Se '.$word.' el producto <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'LG',
					'bandeja'=>'Productos',
					'descr'=>'Se actualiz&oacute; el producto <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$prod = $f->model("lg/prod")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($prod);
	}
	function execute_delete(){
		global $f;
		$model = $f->model("lg/prod")->params(array("_id"=>new MongoId($f->request->id)))->delete("datos");
		$f->response->print("true");
	}
	function execute_details(){
		global $f;
		$f->response->view("lg/prod.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("lg/prod.edit");
	}
	function execute_movi(){
		global $f;
		$f->response->view("lg/prod.movi");
	}
	function execute_new_movi(){
		global $f;
		$f->response->view("lg/prod.new_movi");
	}
}
?>