<?php
class Controller_cm_espa extends Controller {
	function execute_agregar(){
		global $f;
		$f->response->view("cm/espa.new");
	}
	function execute_elegir(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->print('<button name="btnAnter">Cuadrante Anterior</button>');
		$f->response->print('<button name="btnSigui">Siguiente Cuadrante</button>');
		$f->response->print("</div>");
	}
	function execute_edit(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->print('<button name="btnCrear">Nuevo Espacio</button>');
		$f->response->print('<button name="btnPabe">Nuevo Pabell&oacute;n</button>');
		$f->response->print('<button name="btnDesa">Limpiar</button>');
		$f->response->print("</div><div id='p' name='mainGrid'></div>");
	}
	function execute_mapa(){
		global $f;
		$f->model('ci/archivos')->params(array('_id'=>$f->request->_id,"data"=>$f->request->data['data']))->save('extra');
		$f->response->print("true");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if($f->request->data['tipo']!='')
			$params['tipo'] = $f->request->data['tipo'];
		if($f->request->data['sector']!='')
			$params['sector'] = $f->request->data['sector'];
		$f->response->json( $f->model("cm/espa")->params($params)->get("lista") );
	}
	function execute_lista_mapa(){
		global $f;
		$model = $f->model('cm/espa')->get('lista_mapa');
		$f->response->json( $model->items );
	}
	function execute_lista_nichos(){
		global $f;
		$model = $f->model('cm/espa')->params(array('pabellon'=>new MongoId($f->request->data['_id'])))->get('all_nichos');
		$f->response->json( $model->items );
	}
	function execute_new(){
		global $f;
		$f->response->view("cm/espa.save");
	}
	function execute_nicho(){
		global $f;
		$f->response->view("cm/nicho.edit");
	}
	function execute_mauso(){
		global $f;
		$f->response->view("cm/mauso.edit");
	}
	function execute_tumba(){
		global $f;
		$f->response->view("cm/tumba.edit");
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['nicho']['pabellon']['_id']))
			$data['nicho']['pabellon']['_id'] = new MongoId($data['nicho']['pabellon']['_id']);
		if(isset($data['imagen']))
			$data['imagen'] = new MongoId($data['imagen']);
		if(!isset($f->request->data['_id'])){
			$verify = null;
			if(isset($data['nicho'])){
				$data['nicho']['num'] = trim($data['nicho']['num']);
				$verify = $f->model("cm/espa")->params(array(
					'pabellon'=>$data['nicho']['pabellon']['_id'],
					'num'=>$data['nicho']['num'],
					'tipo'=>$data['nicho']['tipo'],
					'piso'=>$data['nicho']['piso'],
					'fila'=>$data['nicho']['fila']
				))->get("verify")->items;
				if($verify!=null){
					$f->response->json(array('error'=>true));
				}
			}
			if($verify==null){
				$data['fecreg'] = new MongoDate();
				$model = $f->model("cm/espa")->params(array('data'=>$data))->save("insert")->items;
				$f->model('ac/log')->params(array(
					'modulo'=>'CM',
					'bandeja'=>'Espacios',
					'descr'=>'Se cre&oacute; el espacio <b>'.$data['nomb'].'</b>.'
				))->save('insert');
				$f->response->json($model);
			}
		}else{
			$vari = $f->model("cm/espa")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'CM',
				'bandeja'=>'Espacios',
				'descr'=>'Se actualiz&oacute; el espacio <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("cm/espa")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
			$f->response->json($model);
		}
	}
	function execute_save_lote(){
		global $f;
		$data = $f->request->data;
		if(isset($data['nicho']['pabellon']['_id']))
			$data['nicho']['pabellon']['_id'] = new MongoId($data['nicho']['pabellon']['_id']);
		$data['fecreg'] = new MongoDate();
		$j = intval($data['nicho']['num_fin']);
		for($i=intval($data['nicho']['num_ini']); $i<=$j; $i++){
			$tmp = $data;
			$tmp['nicho']['num'] = ''.$i;
			$tmp['nomb'] = $tmp['nomb'].$i;
			$model = $f->model("cm/espa")->params(array('data'=>$tmp))->save("insert")->items;
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Espacios',
			'descr'=>'Se gener&oacute; el lote de nichos <b>'.$data['nicho']['num_ini'].'</b>-<b>'.$data['nicho']['num_fin'].'</b> del pabell&oacute;n <b>'.$data['nicho']['pabellon']['nomb'].'</b>.'
		))->save('insert');
		$f->response->print(array(true));
	}
	function execute_remove(){
		global $f;
		$model = $f->model('cm/espa')->save('remove');
		$f->response->print("true");
	}
	function execute_espa_prop(){
		global $f;
		$model = $f->model('cm/espa')->params(array("_id"=>$f->request->_id))->get('espa_prop');
		$f->response->json( $model->items );
	}
	function execute_espa_prop_ocup(){
		global $f;
		$espa = $f->model('cm/espa')->params(array("_id"=>$f->request->_id))->get('espa_prop');
		$ocup = $f->model('cm/ocup')->params(array('_id'=>$f->request->_id))->get('all_ocu_pro');
		$f->response->json( array('espa'=>$espa->items,'ocup'=>$ocup->items) );
	}
	function execute_all_difu_pro(){
		global $f;
		$model = $f->model('cm/espa')->params(array('_id'=>$f->request->_id))->get('all_difu_pro');
		$f->response->json($model->items);
	}
	function execute_get_one(){
		global $f;
		$model = $f->model('cm/espa')->get('one');
		$f->response->json($model->items);
	}
	function execute_all_mausoleos(){
		global $f;
		$model = $f->model('cm/espa')->get('all_mausoleos');
		$f->response->json($model->items);
	}
	function execute_get_osario(){
		global $f;
		$model = $f->model("cm/espa")->get("osario");
		$f->response->json( $model->items );
	}
	function execute_details_mauso(){
		global $f;
		$f->response->view("cm/mauso.details");
	}
	function execute_details_nicho(){
		global $f;
		$f->response->view("cm/nicho.details");
	}
	function execute_details_tumba(){
		global $f;
		$f->response->view("cm/tumba.details");
	}
	function execute_operaciones(){
		global $f;
		$model = $f->model('cm/oper')->params(array("_id"=>$f->request->_id,"oper"=>$f->request->oper,"campo"=>$f->request->campo))->get('operacion');
		$f->response->json( $model->items );
	}
	function execute_get_opers(){
		global $f;
		$opers = $f->model("cm/oper")->params(array("_id"=>$f->request->_id,'oper'=>'','campo'=>'espacio._id'))->get("operacion");
		$json = array('opers'=>$opers->items);
		$f->response->json( $json );
	}
	function execute_datos_ocupante(){
		global $f;
		$model = $f->model('cm/oper')->params(array("_id"=>$f->request->_id,"oper"=>'inhumacion',"campo"=>'ocupante._id'))->get('operacion');
		if(isset($model->items[0]['ejecucion'])){
			$json['fecinhu']=$model->items[0]['ejecucion']['fecfin'];
		}
		$json['ocupante']=$model->items[0]['ocupante']['nomb'].' '.$model->items[0]['ocupante']['appat'].' '.$model->items[0]['ocupante']['apmat'];
			
		$tras = $f->model('cm/oper')->params(array("_id"=>$f->request->_id,"oper"=>'traslado',"campo"=>'ocupante._id'))->get('operacion');
		$asigna=TRUE;
		if(isset($tras->items[0])){
			foreach ($tras->items as $trasl) {
				if(isset($trasl['ejecucion'])){
					$json['tipoper']='Trasladado';
					$json['fecoper']=$model->items[0]['ejecucion']['fecfin'];
					$asigna=FALSE;
					break;
				};
			}
		}
		if($asigna){
			$asig = $f->model('cm/oper')->params(array("_id"=>$f->request->_id,"oper"=>'asignacion',"campo"=>'ocupante._id'))->get('operacion');
			$json['ocupante_id']=$asig->items[0]['ocupante']['_id']->{'$id'};
			$json['ocupante']=$asig->items[0]['ocupante']['nomb'].' '.$asig->items[0]['ocupante']['appat'].' '.$asig->items[0]['ocupante']['apmat'];
			$json['tipoper']='Asignado';
			$json['fecoper']=$asig->items[0]['fecreg'];
		}
		$f->response->json( $json );
	
	}
	function execute_select(){
		global $f;
		$f->response->view('cm/espa.select');
	}
	
	function execute_search(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		);
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		if(isset($f->request->data['tipo']))
			$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['sector']))
			$params['sector'] = $f->request->data['sector'];
		if(isset($f->request->data['fila']))
			$params['fila'] = $f->request->data['fila'];
		if(isset($f->request->data['piso']))
			$params['piso'] = $f->request->data['piso'];
		if(isset($f->request->data['num']))
			$params['num'] = $f->request->data['num'];
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
		$model = $f->model("cm/espa")->params($params)->get("search");
		if(isset($model->items)){
			foreach($model->items as $i=>$item){
				$hist = $f->model('cm/hope')->params(array('espacio'=>$item['_id']))->get('all')->items;
				if(sizeof($hist)>0)
					$model->items[$i]['historia'] = true;
				else
					$model->items[$i]['historia'] = false;
			}
		}
		
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("cm/espa")->params(array("_id"=>new MongoId($f->request->_id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_delete_mapa(){
		global $f;
		$f->model("cm/espa")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'$unset'=>array('imagen'=>true,'coordenadas'=>true)
		)))->save("custom")->items;
	}
	function execute_cuen(){
		global $f;
		$f->response->view("cm/espa.cuen");
	}
}
?>