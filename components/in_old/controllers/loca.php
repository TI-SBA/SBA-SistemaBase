<?php
class Controller_in_old_loca extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Inmueble Matriz</button>');
		$f->response->print('<select name="estado"><option value="H">Habilitado</option><option value="I">Inhabilitado</option><option value="B">Baja</option></select>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"Nombre","w"=>200 ),
			array( "nomb"=>"Direcci&oacute;n","w"=>300 ),
			array( "nomb"=>"Tipo","w"=>150 ),
			array( "nomb"=>"Registrado","w"=>130 ),
			array( "nomb"=>"Conservaci&oacute;n","w"=>130 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array(
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['estado']))
			$params['estado'] = $f->request->data['estado'];
		$model = $f->model("in_old/loca")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$ocupado = false;
		$model = $f->model("in_old/loca")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$espa = $f->model("in_old/espa")->params(array("_id"=>new MongoId($f->request->id)))->get("espa")->items;
		if($espa!=null){
			foreach($espa as $es){
				if(isset($es["ocupado"])){
					if($es["ocupado"]==true){
						$ocupado = true;
					}
				} 
			}
		}
		$model->items["ocupado"] = $ocupado;
		$f->response->json( $model->items );
	}
	function execute_get_espa(){
		global $f;
		$model = $f->model("in_old/espa")->params(array("_id"=>new MongoId($f->request->id)))->get("espa");
		$f->response->json( $model->items );
	}
	function execute_get_one_espa(){
		global $f;
		$model = $f->model("in_old/espa")->params(array("_id"=>new MongoId($f->request->id)))->get("one_all");
		$f->response->json( $model->items );
	}
	function execute_get_one_espa_all(){
		global $f;
		$model = $f->model("in_old/espa")->params(array("_id"=>new MongoId($f->request->id)))->get("one_all");
		$act = $f->model("in_old/oper")->params(array(
			"filter"=>array(
				'espacio._id'=>new MongoId($f->request->id),
				'actualizar_ficha'=>array('$exists'=>true)
			),
			"fields"=>array(
				'fecreg'=>true,
				'observ'=>true,
				'arrendatario'=>true,
				'actualizar_ficha'=>true
			),
			"sort"=>array('fecreg'=>-1)
		))->get("custom_data");
		$model->items['act'] = $act->items;
		$f->response->json( $model->items );
	}
	function execute_search(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"text"=>$f->request->texto
		);
		if(isset($f->request->data['estado']))
			$params['estado'] = $f->request->data['estado'];
		$model = $f->model("in_old/loca")->params($params)->get("search");
		$f->response->json( $model );
	}
	function execute_search_loca(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"text"=>$f->request->texto
		);
		if(isset($f->request->data['estado'])) $params['estado'] = $f->request->data['estado'];
		$model = $f->model("in_old/loca")->params($params)->get("search");
		$f->response->json( $model );
	}
	function execute_search_espa(){
		global $f;
		$model = $f->model("in_old/espa")->params(array(
			"local"=>new MongoId($f->request->local),
			"text"=>$f->request->texto
		))->get("search");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		if(!isset($f->request->data['_id'])){
			$data = $f->request->data;
			$data["fecreg"] = new MongoDate();
			if(isset($data["propietario"])){
				$data["propietario"]['_id'] = new MongoId($data["propietario"]['_id']);
			}
			if($data["habilitado"]==1) $data["habilitado"] = true;
			else $data["habilitado"] = false;
			if(isset($data["imagen"])) $data["imagen"] = new MongoId($data["imagen"]);
			$f->model("in_old/loca")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Inmueble Matriz',
				'descr'=>'Se cre&oacute; el inmueble matriz <b>'.$data['nomb'].'</b>'
			))->save('insert');
		}
	}
	function execute_update(){
		global $f;
		$data = $f->request->data;
		$data['_id'] = new MongoId($data['_id']);
		if($data["habilitado"]==1) $data["habilitado"] = true;
		else $data["habilitado"] = false;
		if(isset($data["imagen"])) $data["imagen"] = new MongoId($data["imagen"]);
		if(isset($data["propietario"])){
			$data["propietario"]['_id'] = new MongoId($data["propietario"]['_id']);
		}
		$f->model("in_old/loca")->params(array('_id'=>$data['_id'],'data'=>$data))->save("update");
		$f->model("in_old/espa")->params(array(
			'filter'=>array('ubic.local._id'=>$data['_id']),
			'data'=>array('$set'=>array(
				'ubic.local'=>array(
					'_id'=>$data['_id'],
					'nomb'=>$data['nomb'],
					'direc'=>$data['ubic']['direc'],
				)
			))
		))->save("upd_custom");
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Inmueble Matriz',
			'descr'=>'Se actualiz&oacute; el inmueble matriz <b>'.$data['nomb'].'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_save_estado(){
		global $f;
		$data = $f->request->data;
		$data['_id'] = new MongoId($data['_id']);
		if($data["habilitado"]==1) $data["habilitado"] = true;
		else $data["habilitado"] = false;
		$hist = array();
		$hist["fecreg"] = new MongoDate();
		$hist["estado"] = $data["estado"];
		$hist["observ"] = $data["observ"];
		$f->model("in_old/loca")->params(array('_id'=>$data['_id'],'data'=>$data))->save("update");
		$f->model("in_old/loca")->params(array('_id'=>$data['_id'],'data'=>array(
			'$push'=>array('historico'=>$hist)
		)))->save("custom");
		switch($data['estado']){
			case 'H': $estado = 'habilit&oacute;'; break;
			case 'I': $estado = 'inhabilit&oacute;'; break;
			case 'B': $estado = 'di&oacute; de baja'; break;
		}
		$loca = $f->model("in_old/loca")->params(array("_id"=>new MongoId($data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Inmueble Matriz',
			'descr'=>'Se '.$estado.' el inmueble matriz <b>'.$loca["nomb"].'</b>'
		))->save('insert');
	}
	function execute_espa_save(){
		global $f;
		if(!isset($f->request->data['_id'])){
			$data = $f->request->data;
			$data["fecreg"] = new MongoDate();
			if($data["habilitado"]==1) $data["habilitado"] = true;
			else $data["habilitado"] = false;
			$data["ubic"]["local"]["_id"] = new MongoId($data["ubic"]["local"]["_id"]);
			$f->model("in_old/espa")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Inmueble Matriz',
				'descr'=>'Se cre&oacute; el inmueble <b>'.$data['descr'].'</b> en el inmueble matriz <b>'.$data["ubic"]["local"]["nomb"].'</b>'
			))->save('insert');
		}
	}
	function execute_espa_act(){
		global $f;
		$trabajador = $f->session->userDB;
		$data = $f->request->data;
		$data["_id"] = new MongoId($data['_id']);
		if($data["habilitado"]==1) $data["habilitado"] = true;
		else $data["habilitado"] = false;
		$espacio_old = $data['espacio_old'];
		if(!isset($espacio_old['mobiliario']))
			$espacio_old['mobiliario'] = array();
		if(isset($data['mobiliario']))
			$mobiliario = $data['mobiliario'];
		else
			$mobiliario = array();
		$f->model('in_old/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'espacio'=>array(
				'_id'=>$data['_id'],
				'descr'=>$data['descr']
			),
			'actualizar_ficha'=>array(
				'anterior'=>array(
					'descr'=>$espacio_old['descr'],
					'uso'=>$espacio_old['uso'],
					'conserv'=>$espacio_old['conserv'],
					'habilitado'=>$espacio_old['habilitado'],
					'ubic'=>array('ref'=>$espacio_old['ubic']['ref']),
					'valor'=>array(
						'renta'=>$espacio_old['valor']['renta'],
						'garantia'=>$espacio_old['valor']['garantia'],
						'moneda'=>$espacio_old['valor']['moneda']
					),
					'cod_arbitrios'=>$espacio_old['cod_arbitrios'],
					'area_terreno'=>$espacio_old['area_terreno'],
					'area_construida'=>$espacio_old['area_construida'],
					'medidor_agua'=>$espacio_old['medidor_agua'],
					'medidor_luz'=>$espacio_old['medidor_luz'],
					'mobiliario'=>$espacio_old['mobiliario']
				),
				'nuevo'=>array(
					'ubic'=>array('ref'=>$data['ubic']['ref']),
					'uso'=>$data['uso'],
					'conserv'=>$data['conserv'],
					'valor'=>array(
						'renta'=>$data['valor']['renta'],
						'garantia'=>$data['valor']['garantia'],
						'moneda'=>$data['valor']['moneda']
					),
					'cod_arbitrios'=>$data['cod_arbitrios'],
					'area_terreno'=>$data['area_terreno'],
					'area_construida'=>$data['area_construida'],
					'medidor_agua'=>$data['medidor_agua'],
					'medidor_luz'=>$data['medidor_luz'],
					'mobiliario'=>$mobiliario,
					'descr'=>$data['descr'],
					'habilitado'=>$data['habilitado']
				)
			)
		)))->save('oper');
		//Actualizar espacio
		$f->model('in_old/espa')->params(array(
			'descr'=>$data['descr'],
			'espacio'=>$data['_id'],
			'uso'=>$data['uso'],
			'conserv'=>$data['conserv'],
			'ref'=>$data['ubic']['ref'],
			'renta'=>$data['valor']['renta'],
			'garantia'=>$data['valor']['garantia'],
			'moneda'=>$data['valor']['moneda'],
			'cod_arbitrios'=>$data['cod_arbitrios'],
			'area_terreno'=>$data['area_terreno'],
			'area_construida'=>$data['area_construida'],
			'medidor_agua'=>$data['medidor_agua'],
			'medidor_luz'=>$data['medidor_luz'],
			'mobiliario'=>$mobiliario
		))->save('act2');
		$f->model('ac/log')->params(array(
			'modulo'=>'IN',
			'bandeja'=>'Inmueble Matriz',
			'descr'=>'Se actualiz&oacute; el inmueble <b>'.$data['descr'].' - '.$data['ubic']['ref'].'</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_details(){
		global $f;
		$f->response->view( 'in_old/loca.details' );
	}
	function execute_loca_espa(){
		global $f;
		$f->response->view( 'in_old/loca.espa' );
	}
	function execute_espa_details(){
		global $f;
		$f->response->view( 'in_old/espa.details' );
	}
	function execute_act_fich(){
		global $f;
		$f->response->view( 'in_old/espa.edit' );
	}
	function execute_new(){
		global $f;
		$f->response->view( 'in_old/loca.new' );
	}
	function execute_new_espa(){
		global $f;
		$f->response->view( 'in_old/espa.new' );
	}
	function execute_new_mobi(){
		global $f;
		$f->response->view( 'in_old/espa.mobi' );
	}
	function execute_view_search_loca(){
		global $f;
		$f->response->view( 'in_old/loca.search' );
	}
	function execute_select_loca(){
		global $f;
		$f->response->view( 'in_old/loca.select' );
	}
	function execute_inha(){
		global $f;
		$f->response->view( 'in_old/loca.inha');
	}
	function execute_print_espa(){
		global $f;
		$model = $f->model("in_old/espa")->params(array("_id"=>new MongoId($f->request->data["_id"])))->get("one");
		//print_r($model);die();
		$f->response->view( 'in_old/repo.espa.print',$model);
	}
	function execute_corregir_espacios(){
		global $f;
		$model = $f->model('in_old/espa')->params()->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				echo $item['_id']->{'$id'}.'<br />';
				$loca = $f->model('in_old/loca')->params(array('_id'=>$item['ubic']['local']['_id']))->get('one')->items;
				$f->model('in_old/espa')->params(array('_id'=>$item['_id'],'data'=>array('$set'=>array('descr'=>$item['ubic']['ref'],'ubic.local.nomb'=>$loca['nomb']))))->save('upd');
			}
		}
	}
	function execute_corregir_locales(){
		global $f;
		/*$model = $f->model('in_old/loca')->params()->get('all')->items;
		if($model!=null){
			foreach($model as $item){
				echo $item['_id']->{'$id'}.'<br />'; 
				$f->model('in_old/loca')->params(array('_id'=>$item['_id'],'data'=>array('nomb'=>$item['ubic']['direc'])))->save('update');
			}
		}*/
	}
}
?>