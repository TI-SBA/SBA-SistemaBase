<?php
class Controller_pe_docs extends Controller {
	function execute_get(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
		$model['trabajador'] = $f->model('mg/entidad')->params(array('_id'=>$model['trabajador']['_id']))->get('one')->items;
		/*$boleta = $f->model('pe/docs')->params(array('filter'=>array(
			'trabajador._id'=>$model['trabajador']['_id'],
			'estado'=>array('$ne'=>'A'),
			'boletas'=>true
		),'sort'=>array('cod'=>1),'limit'=>1))->get('custom_limit')->items;
		if($boleta!=null){
			$model['trabajador']['ultima_boleta'] = $boleta[0];
		}*/
		$f->response->json( $model );
	}
	function execute_lista(){
		global $f,$helper;
		if(isset($f->request->data['ano'])){
			$filter = array(
				'periodo.ano'=>$f->request->data['ano'],
				'periodo.mes'=>$f->request->data['mes']
			);
		}else $filter = array();
		$filter[$f->request->data['doc']] = array('$exists'=>true);
		$model = $f->model("pe/docs")->params(array(
			'filter'=>$filter,
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>$f->request->data['doc'],
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_lista_vei(){
		global $f,$helper;
		$model = $f->model("pe/docs")->params(array(
			'filter'=>array(
				'bonificaciones.tipo'=>'V',
				'periodo.ano'=>$f->request->data['ano'],
				'periodo.mes'=>$f->request->data['mes']
			),
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_search_vei(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"bonificaciones",
			'bonificaciones_tipo'=>'V',
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_lista_tre(){
		global $f,$helper;
		$model = $f->model("pe/docs")->params(array(
			'filter'=>array(
				'bonificaciones.tipo'=>'T',
				'periodo.ano'=>$f->request->data['ano'],
				'periodo.mes'=>$f->request->data['mes']
			),
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_last_bole(){
		global $f;
		$model = $f->model('pe/docs')->params(array('filter'=>array(
			'trabajador._id'=>new MongoId($f->request->data["_id"]),
			'estado'=>array('$ne'=>'A'),
			'boletas'=>true
		),'sort'=>array('cod'=>-1),'limit'=>1))->get('custom_limit');
		$f->response->json($model);
	}
	function execute_search_tre(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"bonificaciones",
			'bonificaciones_tipo'=>'T',
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_lista_qui(){
		global $f,$helper;
		$model = $f->model("pe/docs")->params(array(
			'filter'=>array(
				'quinta'=>array('$exists'=>true),
				'periodo.ano'=>$f->request->data['ano'],
				'periodo.mes'=>$f->request->data['mes']
			),
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_search_qui(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"quinta",
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		if($model->items!=null){
			foreach ($model->items as $i=>$item){
				$model->items[$i]['trabajador'] = $f->model("mg/entidad")->params(array(
					"_id"=>$model->items[$i]['trabajador']['_id']
				))->get("one")->items;
			}
		}
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$f->model("pe/docs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
	}
	function execute_save_doc(){
		global $f;
		$data = $f->request->data;
		if(isset($data['periodo']['inicio'])) $data['periodo']['inicio'] = new MongoDate(strtotime($data['periodo']['inicio']));
		if(isset($data['periodo']['fin'])) $data['periodo']['fin'] = new MongoDate(strtotime($data['periodo']['fin']));
		if(isset($data['contrato'])) $data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['trabajador'])) $data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['trabajador']['organizacion'])) $data['trabajador']['organizacion']['_id'] = new MongoId($data['trabajador']['organizacion']['_id']);
		if(isset($data['trabajador']['cargo'])) $data['trabajador']['cargo']['_id'] = new MongoId($data['trabajador']['cargo']['_id']);
		if(isset($data['trabajador']['cargo']['organizacion'])) $data['trabajador']['cargo']['organizacion']['_id'] = new MongoId($data['trabajador']['cargo']['organizacion']['_id']);
		if(isset($data['trabajador']['contrato'])) $data['trabajador']['contrato']['_id'] = new MongoId($data['trabajador']['contrato']['_id']);
		if(isset($data['trabajador']['pension'])) $data['trabajador']['pension']['_id'] = new MongoId($data['trabajador']['pension']['_id']);
		if(isset($data['trabajador']['nivel'])) $data['trabajador']['nivel']['_id'] = new MongoId($data['trabajador']['nivel']['_id']);
		if(isset($data['trabajador']['nivel_carrera'])) $data['trabajador']['nivel_carrera']['_id'] = new MongoId($data['trabajador']['nivel_carrera']['_id']);
		if(isset($data['trabajador']['cese'])) $data['trabajador']['cese']['fec'] = new MongoDate(strtotime($data['trabajador']['cese']['fec']));
		if(isset($data['trabajador']['fecing'])) $data['trabajador']['fecing'] = new MongoDate(strtotime($data['trabajador']['fecing']));
		if(isset($data['conceptos'])){
			foreach ($data['conceptos'] as $i=>$con){
				if(isset($con['concepto']['_id']))
					$data['conceptos'][$i]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['autor'] = $f->session->userDB;
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'R';
			if(isset($data['bonificaciones'])){
				$filter = array(
					'bonificaciones.tipo'=>$data['bonificaciones']['tipo']
				);
			}
			if(isset($data['enfermedad'])){
				$filter = array(
					'enfermedad'=>array('$exists'=>true)
				);
			}
			if(isset($data['maternidad'])){
				$filter = array(
					'maternidad'=>array('$exists'=>true)
				);
			}
			if(isset($data['vacaciones'])){
				$filter = array(
					'vacaciones'=>array('$exists'=>true)
				);
			}
			if(isset($data['quinta'])){
				$filter = array(
					'quinta'=>array('$exists'=>true)
				);
			}
			$cod = $f->model("pe/docs")->params(array('filter'=>$filter))->get("cod");
			if($cod->items==null) $cod->items="001000";
			else{
				$tmp = intval($cod->items);
				$tmp++;
				$tmp = (string)$tmp;
				for($i=strlen($tmp); $i<6; $i++){
					$tmp = '0'.$tmp;
				}
				$cod->items = $tmp;
			}
			$data['cod'] = $cod->items;
			$f->model("pe/docs")->params(array('data'=>$data))->save("insert");
			if(isset($data['bonificaciones'])){
				if($data['bonificaciones']['tipo']=='V') $word_doc = ' una <b>Compensaci&oacute;n por 25 a&ntilde;os</b>';
				if($data['bonificaciones']['tipo']=='T') $word_doc = ' una <b>Compensaci&oacute;n por 30 a&ntilde;os</b>';
			}
			if(isset($data['periodo']['inicio'])) $word_inicio = date('Y-M-d h:i:s',$data['periodo']['inicio']->sec);
			if(isset($data['periodo']['fin'])) $word_fin = date('Y-M-d h:i:s',$data['periodo']['fin']->sec);
			$texto = 'Se cre&oacute; '.$word_doc.' para el trabajador <b>'.
				$data['trabajador']['nomb'].' '.$data['trabajador']['appat'].' '.$data['trabajador']['apmat'].'</b> ';
			if(isset($data['periodo']))
				$texto .= 'correspondiente al periodo '.$word_inicio.'-'.$word_fin;
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Planillas',
				'descr'=>$texto
			))->save('insert');
		}else{
			$f->model("pe/docs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_comp() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Compensaci&oacute;n</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"N&deg;","w"=>100 ),
			array( "nomb"=>"Trabajador","w"=>350 ),
			array( "nomb"=>"Neto a Pagar","w"=>100 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_comp_lista(){
		global $f;
		$model = $f->model("pe/docs")->params(array(
			"filter"=>array(
				'bonificaciones.tipo'=>$f->request->data['tipo']
			),
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_comp_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"bonificaciones",
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_comp_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['periodo']['inicio'])) $data['periodo']['inicio'] = new MongoDate(strtotime($data['periodo']['inicio']));
		if(isset($data['periodo']['fin'])) $data['periodo']['fin'] = new MongoDate(strtotime($data['periodo']['fin']));
		if(isset($data['contrato'])) $data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['trabajador'])) $data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['trabajador']['organizacion'])) $data['trabajador']['organizacion']['_id'] = new MongoId($data['trabajador']['organizacion']['_id']);
		if(isset($data['trabajador']['cargo'])) $data['trabajador']['cargo']['_id'] = new MongoId($data['trabajador']['cargo']['_id']);
		if(isset($data['trabajador']['cargo']['organizacion'])) $data['trabajador']['cargo']['organizacion']['_id'] = new MongoId($data['trabajador']['cargo']['organizacion']['_id']);
		if(isset($data['trabajador']['contrato'])) $data['trabajador']['contrato']['_id'] = new MongoId($data['trabajador']['contrato']['_id']);
		if(isset($data['trabajador']['pension'])) $data['trabajador']['pension']['_id'] = new MongoId($data['trabajador']['pension']['_id']);
		if(isset($data['trabajador']['nivel'])) $data['trabajador']['nivel']['_id'] = new MongoId($data['trabajador']['nivel']['_id']);
		if(isset($data['trabajador']['nivel_carrera'])) $data['trabajador']['nivel_carrera']['_id'] = new MongoId($data['trabajador']['nivel_carrera']['_id']);
		if(isset($data['trabajador']['cese'])) $data['trabajador']['cese']['fec'] = new MongoDate(strtotime($data['trabajador']['cese']['fec']));
		if(isset($data['trabajador']['fecing'])) $data['trabajador']['fecing'] = new MongoDate(strtotime($data['trabajador']['fecing']));
		if(isset($data['conceptos'])){
			foreach ($data['conceptos'] as $i=>$con){
				if(isset($con['concepto']['_id']))
					$data['conceptos'][$i]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['autor'] = $f->session->userDB;
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'R';
			if(isset($data['bonificaciones'])){
				$filter = array(
					'bonificaciones.tipo'=>$data['bonificaciones']['tipo']
				);
			}
			$cod = $f->model("pe/docs")->params(array('filter'=>$filter))->get("cod");
			if($cod->items==null) $cod->items="001000";
			else{
				$tmp = intval($cod->items);
				$tmp++;
				$tmp = (string)$tmp;
				for($i=strlen($tmp); $i<6; $i++){
					$tmp = '0'.$tmp;
				}
				$cod->items = $tmp;
			}
			$data['cod'] = $cod->items;
			$f->model("pe/docs")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("pe/docs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_comp_edit(){
		global $f;
		$f->response->view("pe/docs.comp.edit");
	}
	function execute_comp_details(){
		global $f;
		$f->response->view("pe/docs.comp.details");
	}
	function execute_vaca() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Compensaci&oacute;n</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"N&deg;","w"=>100 ),
			array( "nomb"=>"Trabajador","w"=>350 ),
			array( "nomb"=>"Pagos","w"=>100 ),
			array( "nomb"=>"Descuentos","w"=>100 ),
			array( "nomb"=>"Neto a Pagar","w"=>100 ),
			array( "nomb"=>"Aportes","w"=>100 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_vaca_lista(){
		global $f;
		$model = $f->model("pe/docs")->params(array(
			"filter"=>array(
				'vacaciones'=>array('$exists'=>true)
			),
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_vaca_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"vacaciones",
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_vaca_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['periodo']['inicio'])) $data['periodo']['inicio'] = new MongoDate(strtotime($data['periodo']['inicio']));
		if(isset($data['periodo']['fin'])) $data['periodo']['fin'] = new MongoDate(strtotime($data['periodo']['fin']));
		if(isset($data['contrato'])) $data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['trabajador'])) $data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['trabajador']['organizacion'])) $data['trabajador']['organizacion']['_id'] = new MongoId($data['trabajador']['organizacion']['_id']);
		if(isset($data['trabajador']['cargo'])) $data['trabajador']['cargo']['_id'] = new MongoId($data['trabajador']['cargo']['_id']);
		if(isset($data['trabajador']['cargo']['organizacion'])) $data['trabajador']['cargo']['organizacion']['_id'] = new MongoId($data['trabajador']['cargo']['organizacion']['_id']);
		if(isset($data['trabajador']['contrato'])) $data['trabajador']['contrato']['_id'] = new MongoId($data['trabajador']['contrato']['_id']);
		if(isset($data['trabajador']['pension'])) $data['trabajador']['pension']['_id'] = new MongoId($data['trabajador']['pension']['_id']);
		if(isset($data['trabajador']['nivel'])) $data['trabajador']['nivel']['_id'] = new MongoId($data['trabajador']['nivel']['_id']);
		if(isset($data['trabajador']['nivel_carrera'])) $data['trabajador']['nivel_carrera']['_id'] = new MongoId($data['trabajador']['nivel_carrera']['_id']);
		if(isset($data['trabajador']['cese'])) $data['trabajador']['cese']['fec'] = new MongoDate(strtotime($data['trabajador']['cese']['fec']));
		if(isset($data['trabajador']['fecing'])) $data['trabajador']['fecing'] = new MongoDate(strtotime($data['trabajador']['fecing']));
		if(isset($data['conceptos'])){
			foreach ($data['conceptos'] as $i=>$con){
				if(isset($con['concepto']['_id']))
					$data['conceptos'][$i]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['autor'] = array(
				'_id'=>$f->session->enti['_id'],
				'tipo_enti'=>$f->session->enti['tipo_enti'],
				'nomb'=>$f->session->enti['nomb'],
				'cargo'=>array(
					'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
					'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
					'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
				)
			);
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'R';
			if(isset($data['bonificaciones'])){
				$filter = array(
					'bonificaciones.tipo'=>$data['bonificaciones']['tipo']
				);
			}
			$cod = $f->model("pe/docs")->params(array('filter'=>$filter))->get("cod");
			if($cod->items==null) $cod->items="001000";
			else{
				$tmp = intval($cod->items);
				$tmp++;
				$tmp = (string)$tmp;
				for($i=strlen($tmp); $i<6; $i++){
					$tmp = '0'.$tmp;
				}
				$cod->items = $tmp;
			}
			$data['cod'] = $cod->items;
			$f->model("pe/docs")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("pe/docs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_generar_pago(){
		global $f;
		$data = $f->request->data;
		$fec = new MongoDate();
		$autor = $f->session->userDB;
		$filter = array(
			'estado'=>'R'
		);
		if(isset($data['ano'])){
			$filter['periodo.ano'] = $data['ano'];
			$filter['periodo.mes'] = $data['mes'];
		}
		$filter[$f->request->data['doc']] = array('$exists'=>true);
		if($f->request->data['doc']=='boletas'){
			$filter['contrato.cod'] = $f->request->data['tipo'];
		}else if($f->request->data['doc']=='bonificaciones'){
			$filter['bonificaciones.tipo'] = $f->request->data['tipo'];
		}
		$boletas = $f->model("pe/docs")->params(array(
			'filter'=>$filter
		))->get("lista_all")->items;print_r($boletas);die();
		if($boletas!=null){
			$orga = array();
			$orgs = array();
			foreach ($boletas as $bole){
				$f->model('pe/docs')->params(array(
					'_id'=>$bole['_id'],
					'data'=>array(
						'estado'=>'P'
					)
				))->save('update');
				$index = array_search($bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['actividad']['_id']->{'$id'}, $orgs);
				if($index===false){
					$orgs[] = $bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['actividad']['_id']->{'$id'};
					$orga[] = array($bole);
				}else{
					$orga[$index][] = $bole;
				}
			}
			foreach ($orga as $org){
				$org[0]['trabajador'] = $f->model('mg/entidad')->params(array('_id'=>$org[0]['trabajador']['_id']))->get('one')->items;
				$cpp = array(
					'fecreg'=>$fec,
					'modulo'=>'PE',
					'estado'=>'P',
					'origen'=>'P',
					'autor'=>$autor,
					'motivo'=>'Planillas '.$data['tipo'].' de '.$org[0]['trabajador']['roles']['trabajador']['cargo']['organizacion']['actividad']['nomb'].' - Periodo: '.$data['ano'].'-'.$data['mes'],
					'documentos'=>array(),
					'conceptos'=>array(),
					'afectacion'=>array(),
					'total'=>0,
					'total_pago'=>0,
					'total_desc'=>0
				);
				$cpp_desc = array();
				$desc = array();
				$cpp_apor = array();
				$apor = array();
				$concs = array();
				$clas = array();
				$orgs = array();
				foreach ($org as $bole){
					$cpp['documentos'][] = $bole['_id'];
					foreach ($bole['conceptos'] as $conc){
						//print_r($conc);die();
						//$f->response->print($conc['concepto']['tipo']);
						if(floatval($conc['subtotal'])!=0){
							$index = array_search($conc['concepto']['nomb'],$concs);
							if($index===false){
								$concs[] = $conc['concepto']['nomb'];
								$tipo_tmp = $conc['concepto']['tipo'];
								if($tipo_tmp=='A') $tipo_tmp = 'D';
								$cpp['conceptos'][] = array(
									'tipo'=>$tipo_tmp,
									'observ'=>$conc['concepto']['nomb'],
									'monto'=>floatval($conc['subtotal']),
									'moneda'=>'S',
									'concepto'=>$conc['concepto'],
									'modulo'=>'PE'
								);
								if($conc['concepto']['tipo']=='P'){
									$index_org = array_search($bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']->{'$id'},$orgs);
									if($index_org===false){
										$orgs[] = $bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']->{'$id'};
										$tmp = $f->model('mg/orga')->params(array('_id'=>$bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']))->get('one')->items;
										$cpp['afectacion'][] = array(
											'organizacion'=>array(
												'_id'=>$tmp['_id'],
												'nomb'=>$tmp['nomb'],
												'actividad'=>array(
													'_id'=>$tmp['actividad']['_id'],
													'nomb'=>$tmp['actividad']['nomb'],
													'cod'=>$tmp['actividad']['cod']
												),
												'componente'=>array(
													'_id'=>$tmp['componente']['_id'],
													'nomb'=>$tmp['componente']['nomb'],
													'cod'=>$tmp['componente']['cod']
												)
											),
											'monto'=>floatval($conc['subtotal'])
										);
									}
								}
							}else{
								$cpp['conceptos'][$index]['monto'] += floatval($conc['subtotal']);
							}
							if($conc['concepto']['tipo']=='P'){
								$index_org = array_search($bole['trabajador']['roles']['trabajador']['cargo']['organizacion']['_id']->{'$id'},$orgs);
								if(isset($conc['concepto']['clasificador'])){
									$index = array_search($conc['concepto']['clasificador']['_id']->{'$id'}, $clas);
									if($index===false){
										$clas[] = $conc['concepto']['clasificador']['_id']->{'$id'};
										$cpp['afectacion'][$index_org]['gasto'][] = array(
											'clasificador'=>$conc['concepto']['clasificador'],
											'monto'=>floatval($conc['subtotal'])
										);
										$cpp['afectacion'][$index_org]['monto'] += floatval($conc['subtotal']);
									}else{
										$cpp['afectacion'][$index_org]['gasto'][$index]['monto'] += floatval($conc['subtotal']);
										$cpp['afectacion'][$index_org]['monto'] += floatval($conc['subtotal']);
									}
								}
							}
							if($conc['concepto']['tipo']=='P'){
								$cpp['total_pago'] += floatval($conc['subtotal']);
								$cpp['total'] += floatval($conc['subtotal']);
							}elseif($conc['concepto']['tipo']=='D'){
								$cpp['total'] -= floatval($conc['subtotal']);
								$cpp['total_desc'] += floatval($conc['subtotal']);
							}elseif($conc['concepto']['tipo']=='A'){
								$cpp['total'] -= floatval($conc['subtotal']);
								$cpp['total_desc'] += floatval($conc['subtotal']);
							}
							/*
							 * Si es un concepto de tipo descuento
							 * generar una cuenta por pagar
							 * o actualizar la ya creada
							 * */
							if($conc['concepto']['tipo']=='D'){
								$index = array_search($conc['concepto']['_id']->{'$id'}, $desc);
								if($index===false){
									$desc[] = $conc['concepto']['_id']->{'$id'};
									$tmp = $f->model('pe/conc')->params(array('_id'=>$conc['concepto']['_id']))->get('one')->items;
									if(is_array($tmp['beneficiario'])){
										$cpp_desc[] = array(
											'fecreg'=>$fec,
											'estado'=>'P',
											'modulo'=>'PE',
											'origen'=>'D',
											'autor'=>$autor,
											'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
											'beneficiario'=>$tmp['beneficiario'],
											'conceptos'=>array(array(
												'tipo'=>'P',
												'observ'=>$conc['concepto']['nomb'],
												'monto'=>floatval($conc['subtotal']),
												'moneda'=>'S',
												'concepto'=>$conc['concepto'],
												'modulo'=>'PE'
											)),
											'total'=>floatval($conc['subtotal']),
											'total_pago'=>floatval($conc['subtotal']),
											'total_desc'=>0
										);
									}else{
										$trab = $f->model('mg/entidad')->params(array('_id'=>$bole['trabajador']['_id']))->get('one')->items;
										if($tmp['beneficiario']=='RJ'){
											foreach ($trab['roles']['trabajador']['retencion'] as $reten){
												$cpp_desc[] = array(
													'fecreg'=>$fec,
													'estado'=>'P',
													'modulo'=>'PE',
													'origen'=>'D',
													'autor'=>$autor,
													'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
													'beneficiario'=>$reten['entidad'],
													'conceptos'=>array(array(
														'tipo'=>'P',
														'observ'=>$conc['concepto']['nomb'],
														'monto'=>floatval($conc['subtotal']),
														'moneda'=>'S',
														'concepto'=>$conc['concepto'],
														'modulo'=>'PE'
													)),
													'total'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_pago'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_desc'=>0
												);
											}
										}elseif($tmp['beneficiario']=='AFP'){
											$afp = $f->model('pe/sist')->params(array('_id'=>$trab['roles']['trabajador']['pension']['_id']))->get('one')->items;
											$cpp_desc[] = array(
												'fecreg'=>$fec,
												'estado'=>'P',
												'origen'=>'D',
												'modulo'=>'PE',
												'autor'=>$autor,
												'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
												'beneficiario'=>$afp['entidad'],
												'conceptos'=>array(array(
													'tipo'=>'P',
													'observ'=>$conc['concepto']['nomb'],
													'monto'=>floatval($conc['subtotal']),
													'moneda'=>'S',
													'concepto'=>$conc['concepto'],
													'modulo'=>'PE'
												)),
												'total'=>floatval($conc['subtotal']),
												'total_pago'=>floatval($conc['subtotal']),
												'total_desc'=>0
											);
										}
									}
								}else{
									$cpp_desc[$index_org]['total'] += floatval($conc['subtotal']);
									$cpp_desc[$index_org]['total_pago'] += floatval($conc['subtotal']);
								}
							}
							/*
							 * Si es un concepto de tipo aporte
							 * generar una cuenta por pagar
							 * o actualizar la ya creada
							 * */
							if($conc['concepto']['tipo']=='A'){
								$index = array_search($conc['concepto']['_id']->{'$id'}, $apor);
								if($index===false){
									$apor[] = $conc['concepto']['_id']->{'$id'};
									$tmp = $f->model('pe/conc')->params(array('_id'=>$conc['concepto']['_id']))->get('one')->items;
									if(is_array($tmp['beneficiario'])){
										$cpp_apor[] = array(
											'fecreg'=>$fec,
											'estado'=>'P',
											'origen'=>'D',
											'modulo'=>'PE',
											'autor'=>$autor,
											'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
											'beneficiario'=>$tmp['beneficiario'],
											'conceptos'=>array(array(
												'tipo'=>'P',
												'observ'=>$conc['concepto']['nomb'],
												'monto'=>floatval($conc['subtotal']),
												'moneda'=>'S',
												'concepto'=>$conc['concepto'],
												'modulo'=>'PE'
											)),
											'total'=>floatval($conc['subtotal']),
											'total_pago'=>floatval($conc['subtotal']),
											'total_desc'=>0
										);
									}else{
										$trab = $f->model('mg/entidad')->params(array('_id'=>$bole['trabajador']['_id']))->get('one')->items;
										if($tmp['beneficiario']=='RJ'){
											foreach ($trab['roles']['trabajador']['retencion'] as $reten){
												$cpp_apor[] = array(
													'fecreg'=>$fec,
													'estado'=>'P',
													'modulo'=>'PE',
													'origen'=>'D',
													'autor'=>$autor,
													'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
													'beneficiario'=>$reten['entidad'],
													'conceptos'=>array(array(
														'tipo'=>'P',
														'observ'=>$conc['concepto']['nomb'],
														'monto'=>floatval($conc['subtotal']),
														'moneda'=>'S',
														'concepto'=>$conc['concepto'],
														'modulo'=>'PE'
													)),
													'total'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_pago'=>floatval($conc['subtotal'])/$reten['val']/100,
													'total_desc'=>0
												);
											}
										}elseif($tmp['beneficiario']=='AFP'){
											$afp = $f->model('pe/sist')->params(array('_id'=>$trab['roles']['trabajador']['pension']['_id']))->get('one')->items;
											$cpp_apor[] = array(
												'fecreg'=>$fec,
												'estado'=>'P',
												'modulo'=>'PE',
												'origen'=>'D',
												'autor'=>$autor,
												'motivo'=>$conc['concepto']['nomb']." (".$cpp['motivo']." )",
												'beneficiario'=>$afp['entidad'],
												'conceptos'=>array(array(
													'tipo'=>'P',
													'observ'=>$conc['concepto']['nomb'],
													'monto'=>floatval($conc['subtotal']),
													'moneda'=>'S',
													'concepto'=>$conc['concepto'],
													'modulo'=>'PE'
												)),
												'total'=>floatval($conc['subtotal']),
												'total_pago'=>floatval($conc['subtotal']),
												'total_desc'=>0
											);
										}
									}
								}else{
									$cpp_apor[$index_org]['total'] += floatval($conc['subtotal']);
									$cpp_apor[$index_org]['total_pago'] += floatval($conc['subtotal']);
								}
							}
						}
					}
				}
				//$f->response->json($cpp_desc);
				
				
				
				
				
				
				
				
				
				/*$f->response->json($cpp);
				die();*/
				
				
				
				
				
				
				
				
				foreach ($cpp_desc as $cta){
					$f->model('ts/ctpp')->params(array('data'=>$cta))->save('insert');
				}
				foreach ($cpp_apor as $cta){
					$f->model('ts/ctpp')->params(array('data'=>$cta))->save('insert');
				}
				$f->model('ts/ctpp')->params(array('data'=>$cpp))->save('insert');
			}
			$f->response->print("true");
		}else $f->response->json(array());
		$f->model('ac/log')->params(array(
			'modulo'=>'PE',
			'bandeja'=>'Planillas',
			'descr'=>'Se realiza el pago de las Boletas de Trabajador del periodo <b>'.$data['ano'].'-'.$data['mes'].'</b>.'
		))->save('insert');
	}

	function execute_sepe() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Compensaci&oacute;n</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>30 ),
			array( "nomb"=>"Trabajador","w"=>350 ),
			array( "nomb"=>"Beneficiario","w"=>350 ),
			array( "nomb"=>"Neto a Pagar","w"=>100 ),
			array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_sepe_print(){
		global $f;
		$model = $model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->view("pe/repo.bole_sepe.print",$model);
	}
	function execute_sepe_lista(){
		global $f;
		$model = $f->model("pe/docs")->params(array(
			"filter"=>array(
				'sepelios'=>array('$exists'=>true)
			),
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_sepe_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/docs")->params(array(
			"doc"=>"sepelios",
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_sepe_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['contrato'])) $data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['trabajador'])) $data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['trabajador']['organizacion'])) $data['trabajador']['organizacion']['_id'] = new MongoId($data['trabajador']['organizacion']['_id']);
		if(isset($data['trabajador']['cargo'])) $data['trabajador']['cargo']['_id'] = new MongoId($data['trabajador']['cargo']['_id']);
		if(isset($data['trabajador']['cargo']['organizacion'])) $data['trabajador']['cargo']['organizacion']['_id'] = new MongoId($data['trabajador']['cargo']['organizacion']['_id']);
		if(isset($data['trabajador']['contrato'])) $data['trabajador']['contrato']['_id'] = new MongoId($data['trabajador']['contrato']['_id']);
		if(isset($data['trabajador']['pension'])) $data['trabajador']['pension']['_id'] = new MongoId($data['trabajador']['pension']['_id']);
		if(isset($data['trabajador']['nivel'])) $data['trabajador']['nivel']['_id'] = new MongoId($data['trabajador']['nivel']['_id']);
		if(isset($data['trabajador']['nivel_carrera'])) $data['trabajador']['nivel_carrera']['_id'] = new MongoId($data['trabajador']['nivel_carrera']['_id']);
		if(isset($data['sepelios']['sepelio'])){
			if($data['sepelios']['sepelio']=="1"){
				$data['sepelios']['sepelio']=true;
			}else{
				$data['sepelios']['sepelio']=false;
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['autor'] = $f->session->userDB;
			$data['fecreg'] = new MongoDate();
			$filter = array(
				'sepelios'=>array('$exists'=>true)
			);
			$cod = $f->model("pe/docs")->params(array('filter'=>$filter))->get("cod");
			if($cod->items==null) $cod->items="001000";
			else{
				$tmp = intval($cod->items);
				$tmp++;
				$tmp = (string)$tmp;
				for($i=strlen($tmp); $i<6; $i++){
					$tmp = '0'.$tmp;
				}
				$cod->items = $tmp;
			}
			$data['cod'] = $cod->items;
			$f->model("pe/docs")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("pe/docs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_get_enfermedad(){
		global $f;
		$model = $f->model('pe/docs')->params(array('filter'=>array(
			'trabajador._id'=>new MongoId($f->request->data["_id"]),
			'estado'=>array('$ne'=>'A'),
			'boletas'=>true,
			'periodo.inicio'=>array(
				'$lt'=>new MongoDate(),
				'$gt'=>new MongoDate(strtotime(date("Y-m-d", mktime()) . " - 365 day"))
			)
		),'sort'=>array('cod'=>-1),'limit'=>12))->get('custom_limit');
		$f->response->json($model->items);
	}
	function execute_vaca_edit(){
		global $f;
		$f->response->view("pe/docs.vaca.edit");
	}
	function execute_vaca_details(){
		global $f;
		$f->response->view("pe/docs.vaca.details");
	}
	function execute_vei_edit(){
		global $f;
		$f->response->view("pe/docs.vei.edit");
	}
	function execute_print_vei(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["trabajador"]["_id"]->{'$id'}))->get("one")->items;
		$f->response->view("pe/repo.bole_vei.print",$model);
	}
	function execute_print_tre(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["trabajador"]["_id"]->{'$id'}))->get("one")->items;
		$f->response->view("pe/repo.bole_tre.print",$model);
	}
	function execute_vac_edit(){
		global $f;
		$f->response->view("pe/docs.vac.edit");
	}
	function execute_vac_print(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["trabajador"]["_id"]->{'$id'}))->get("one")->items;
		$f->response->view("pe/repo.vaca_trun.print",$model);
	}
	function execute_bon_edit(){
		global $f;
		$f->response->view("pe/docs.bon.edit");
	}
	function execute_sep_edit(){
		global $f;
		$f->response->view("pe/docs.sep.edit");
	}
	function execute_enf_edit(){
		global $f;
		$f->response->view("pe/docs.enf.edit");
	}
	function execute_enf_details(){
		global $f;
		$f->response->view("pe/docs.enf.details");
	}
	function execute_enf_print(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["trabajador"]["_id"]->{'$id'}))->get("one")->items;
		$f->response->view("pe/repo.bole_enf.print",$model);
	}
	function execute_vac_ben_edit(){
		global $f;
		$f->response->view("pe/docs.vac_ben.edit");
	}
	function execute_vac_ben_details(){
		global $f;
		$f->response->view("pe/docs.vac_ben.details");
	}
	function execute_mat_edit(){
		global $f;
		$f->response->view("pe/docs.mat.edit");
	}
	function execute_mat_details(){
		global $f;
		$f->response->view("pe/docs.mat.details");
	}
	function execute_mat_print(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["trabajador"]["_id"]->{'$id'}))->get("one")->items;
		$f->response->view("pe/repo.bole_mat.print",$model);
	}
	function execute_qui_print(){
		global $f;
		$model = $f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$model->items["trabajador"] = $f->model("mg/entidad")->params(array("_id"=>$model->items["trabajador"]["_id"]->{'$id'}))->get("one")->items;
		$f->response->view("pe/repo.bole_quin.print",$model);
	}
	function execute_delete(){
		global $f;
		$f->model("pe/docs")->params(array("_id"=>new MongoId($f->request->data["_id"])))->delete("one");
		$f->response->print("true");
	}
}
?>