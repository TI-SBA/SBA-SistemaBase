<?php
class Controller_td_tupa extends Controller {
	function execute_details(){
		global $f;
		$f->response->view("td/tupa.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("td/tupa.edit");
	}
	function execute_new(){
		global $f;
		$f->response->view("td/tupa.new");
	}
	function execute_tipo(){
		global $f;
		$f->response->view("td/tupa.tipo");
	}
	function execute_bleg(){
		global $f;
		$f->response->view("td/tupa.bleg");
	}
	function execute_reqs(){
		global $f;
		$f->response->view("td/tupa.reqs");
	}
	function execute_get_info(){
		global $f;
		$orga = $f->model("mg/orga")->get("all")->items;
		$ext = $f->model("td/orga")->get("all")->items;
		$f->response->json(array('orga'=>$orga,'ext'=>$ext));
	}
	function execute_listatupas(){
		global $f;
		$model = $f->model("td/tupa")->get("alltupas");
		$f->response->json( $model->items );
	}
	function execute_lista(){
		global $f;
		$model = $f->model("td/tupa")->params(array("tupa"=>$f->request->tupa))->get("lista");
		$aux = array();
		$orga = array();
		if($model->items!=null){
			$f->library("helpers");
			$helper = new helper();
			$model->items = $helper->orderMultiDimensionalArray($model->items, 'item');
			foreach($model->items as $item){
				$index = array_search($item['organizacion']['_id'], $aux);
				if(is_int($index)){
					$data[$index][] = $item;
				}else{
					$aux[] = $item['organizacion']['_id'];
					$orga[] = array('_id'=>$item['organizacion']['_id'],'nomb'=>$item['organizacion']['nomb']);
					$data[] = array(0=>$item);
				}
			}
		}
		$json->items = $data;
		$json->orga = $orga;
		$f->response->json( $json );
	}
	function execute_search(){
		global $f;
		$model = $f->model("td/tupa")->params(array("texto"=>$f->request->texto,"grupo"=>$f->request->grupo,"page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json($model);
	}
	function execute_save(){
    	global $f;
		$data = $f->request->data['data'];
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'TUPA',
			'descr'=>'Se cre&oacute; el procedimiento <b>'.$data['titulo'].'</b>.'
		))->save('insert');
		$data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		foreach($data['modalidades'] as $i=>$obj) {
			if(isset($this->params['_id'])) $data['modalidades'][$i]['_id'] = new MongoId($obj['_id']);
			else $data['modalidades'][$i]['_id'] = new MongoId();
			$data['modalidades'][$i]['inicia']['organismo']['_id'] = new MongoId($obj['inicia']['organismo']['_id']);
			if($obj['inicia']['organismo']['ext']==true)
				$data['modalidades'][$i]['inicia']['organismo']['ext'] = true;
			else
				$data['modalidades'][$i]['inicia']['organismo']['ext'] = false;
			$data['modalidades'][$i]['aprueba']['organismo']['_id'] = new MongoId($obj['aprueba']['organismo']['_id']);
			if($obj['aprueba']['organismo']['ext']==true)
				$data['modalidades'][$i]['aprueba']['organismo']['ext'] = true;
			else
				$data['modalidades'][$i]['aprueba']['organismo']['ext'] = false;
			if($obj['reconsidera']!=null){
				$data['modalidades'][$i]['reconsidera']['organismo']['_id'] = new MongoId($obj['reconsidera']['organismo']['_id']);
				if($obj['reconsidera']['organismo']['ext']==true)
					$data['modalidades'][$i]['reconsidera']['organismo']['ext'] = true;
				else
					$data['modalidades'][$i]['reconsidera']['organismo']['ext'] = false;
			}
			if($obj['apela']!=null){
				$data['modalidades'][$i]['apela']['organismo']['_id'] = new MongoId($obj['apela']['organismo']['_id']);
				if($obj['apela']['organismo']['ext']==true)
					$data['modalidades'][$i]['apela']['organismo']['ext'] = true;
				else
					$data['modalidades'][$i]['apela']['organismo']['ext'] = false;
			}
		}
		$data['_id'] = new MongoId();
		$data['estado'] = 'H';
		$f->model('td/tupa')->params(array("tupa"=>new MongoId($f->request->tupa),'data'=>$data))->save('push');
		$f->response->print( "true" );
	}
	function execute_create(){
    	global $f;
    	$model = $f->model('td/tupa')->params(array("data"=>$f->request->data))->save('create');
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'TUPA',
			'descr'=>'Se cre&oacute; el TUPA <b>'.$f->request->data['anio'].'</b>.'
		))->save('insert');
		$f->response->print( "true" );
	}
	function execute_update(){
    	global $f;
		$data = $f->request->data['data'];
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'TUPA',
			'descr'=>'Se actualiz&oacute; el procedimiento <b>'.$data['titulo'].'</b>.'
		))->save('insert');
		$data['estado'] = 'H';
		$data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
		foreach($data['modalidades'] as $i=>$obj) {
			if(isset($this->params['_id'])) $data['modalidades'][$i]['_id'] = new MongoId($obj['_id']);
			else $data['modalidades'][$i]['_id'] = new MongoId();
			$data['modalidades'][$i]['inicia']['organismo']['_id'] = new MongoId($obj['inicia']['organismo']['_id']);
			if($obj['inicia']['organismo']['ext']==true)
				$data['modalidades'][$i]['inicia']['organismo']['ext'] = true;
			else
				$data['modalidades'][$i]['inicia']['organismo']['ext'] = false;
			$data['modalidades'][$i]['aprueba']['organismo']['_id'] = new MongoId($obj['aprueba']['organismo']['_id']);
			if($obj['aprueba']['organismo']['ext']==true)
				$data['modalidades'][$i]['aprueba']['organismo']['ext'] = true;
			else
				$data['modalidades'][$i]['aprueba']['organismo']['ext'] = false;
			if($obj['reconsidera']!=null){
				$data['modalidades'][$i]['reconsidera']['organismo']['_id'] = new MongoId($obj['reconsidera']['organismo']['_id']);
				if($obj['reconsidera']['organismo']['ext']==true)
					$data['modalidades'][$i]['reconsidera']['organismo']['ext'] = true;
				else
					$data['modalidades'][$i]['reconsidera']['organismo']['ext'] = false;
			}
			if($obj['apela']!=null){
				$data['modalidades'][$i]['apela']['organismo']['_id'] = new MongoId($obj['apela']['organismo']['_id']);
				if($obj['apela']['organismo']['ext']==true)
					$data['modalidades'][$i]['apela']['organismo']['ext'] = true;
				else
					$data['modalidades'][$i]['apela']['organismo']['ext'] = false;
			}
		}
		$data['_id'] = new MongoId($f->request->data['_id']);
    	$f->model('td/tupa')->params(array("_id"=>new MongoId($f->request->data['_id']),"data"=>$data))->save('set');
    	$f->response->print("true");
	}
	function execute_estado(){
		global $f;
    	$f->model('td/tupa')->params(array("_id"=>new MongoId($f->request->data['_id']),"estado"=>$f->request->data['estado']))->save('estado');
    	if($data['estado']=='H') $word = 'habilit&oacute;';
		else $word = 'deshabilit&oacute;';
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'TUPA',
			'descr'=>'Se '.$word.' el procedimiento <b>'.$f->request->data['nomb'].'</b>.'
		))->save('insert');
    	$f->response->print("true");
	}
	function execute_get(){
		global $f;
		$model = $f->model("td/tupa")->params(array("_id"=>$f->request->id))->get("one");
		$f->response->json( $model->items );
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('td/tupa')->params(array("_id"=>$f->request->id))->delete('procedimiento');
    	$f->response->print( "true" );
	}
	function execute_view_search(){
		global $f;
		$f->response->print('<div name="orga" class="ui-layout-west">');
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"Dependencias","w"=>166 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
    	$f->response->print('</div>');
		$f->response->print('<div class="ui-layout-center">');
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"Nombre de Procedimiento","w"=>380 ),
			1=>array( "nomb"=>"Modalidades","w"=>290 ),
			2=>array( "nomb"=>"Plazo","w"=>90 ),
			3=>array( "nomb"=>"Costo","w"=>90 ),
			4=>array( "nomb"=>"&nbsp;","w"=>10 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print('</div>');
	}
	function execute_vigente(){
		global $f;
		$model = $f->model("td/tupa");
		if($f->request->textSearch!=''){
			$model->params(array("textSearch"=>$f->request->textSearch));
		}
		$model->get("vigente");
		$aux = array();
		$orga = array();
		$json->_id = $model->items['_id'];
		$json->anio = $model->items['anio'];
		if(isset($model->items['procedimientos'])){
			$f->library("helpers");
			$helper = new helper();
			$model->items['procedimientos'] = $helper->orderMultiDimensionalArray($model->items['procedimientos'], 'item');
			foreach($model->items['procedimientos'] as $item){
				$index = array_search($item['organizacion']['_id'], $aux);
				if(is_int($index)){
					$data[$index][] = $item;
				}else{
					$aux[] = $item['organizacion']['_id'];
					$orga[] = array('_id'=>$item['organizacion']['_id'],'nomb'=>$item['organizacion']['nomb']);
					$data[] = array(0=>$item);
				}
			}
		}
		$json->items = $data;
		$json->orga = $orga;
		$f->response->json( $json );
	}
}
?>