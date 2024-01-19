<?php
class Controller_cm_pabe extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sector']))
			if($f->request->data['sector']!='')
				$params['sector'] = $f->request->data['sector'];
		if(isset($f->request->data['filter'])){
			$params['filter'] = $f->request->data['filter'];
			foreach ($params['filter'] as $i=>$filter){
				if(gettype($filter['value'])=="array"){
					if(isset($filter['value']['$exists'])){
						if($filter['value']['$exists']=='true') $filter['value'] = array('$exists'=>1);
						else $filter['value'] = array('$exists'=>0);
					}
				}
				$params['filter'][$i]['value'] = $filter['value'];
			}
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cm/pabe")->params($params)->get("search") );
	}
	function execute_search(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows'],"texto"=>$f->request->data['texto']);
		if(isset($f->request->data['sector']))
			if($f->request->data['sector']!='')
				$params['sector'] = $f->request->data['sector'];
		if(isset($f->request->data['filter'])){
			$params['filter'] = $f->request->data['filter'];
			foreach ($params['filter'] as $i=>$filter){
				if(gettype($filter['value'])=="array"){
					if(isset($filter['value']['$exists'])){
						if($filter['value']['$exists']=='true') $filter['value'] = array('$exists'=>1);
						else $filter['value'] = array('$exists'=>0);
					}
				}
				$params['filter'][$i]['value'] = $filter['value'];
			}
		}
		$f->response->json( $f->model("cm/pabe")->params($params)->get("search") );
	}
	function execute_new(){
		global $f;
		$f->response->view("cm/pabe.new");
	}
	function execute_select(){
		global $f;
		$f->response->view("cm/pabe.select");
	}
	function execute_upload(){
		global $f;
		$f->response->view("cm/pabe.upload");
	}
	function execute_details(){
		global $f;
		$f->response->print("<div id='viewPabe' name='mainGrid'></div>");
	}
	function execute_edit(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->print('<button name="btnCrear">Nuevo Espacio</button>');
		$f->response->print('<button name="btnDesa">Limpiar</button>');
		$f->response->print("</div><div id='pabe' name='mainGrid'></div>");
	}
	function execute_all(){
		global $f;
		$model = $f->model('cm/pabe')->params(array('fields'=>array(
			'nomb'=>true,
			'num'=>true,
			'pisos'=>true
		)))->get('all')->items;
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['imagen']))
			$data['imagen'] = new MongoId($data['imagen']);
		if(isset($data['pabellon_imagen']))
			$data['pabellon_imagen'] = new MongoId($data['pabellon_imagen']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$model = $f->model("cm/pabe")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'CM',
				'bandeja'=>'Pabellones',
				'descr'=>'Se cre&oacute; el pabell&oacute;n <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("cm/pabe")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'CM',
					'bandeja'=>'Pabellones',
					'descr'=>'Se '.$word.' el pabell&oacute;n <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'CM',
					'bandeja'=>'Pabellones',
					'descr'=>'Se actualiz&oacute; el pabell&oacute;n <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$model = $f->model("cm/pabe")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_act(){
		global $f;
		$model = $f->model('cm/pabe')->params(array(
			'data'=>$f->request->data
		))->save('act');
	}
	function execute_get(){
		global $f;
		$model = $f->model("cm/pabe")->params(array("_id"=>new MongoId($f->request->_id)))->get("one");
		$f->response->json( $model->items );
	}
}
?>