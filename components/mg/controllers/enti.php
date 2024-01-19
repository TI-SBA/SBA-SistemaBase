<?php
class Controller_mg_enti extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Agregar</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>50 ),
			1=>array( "nomb"=>"Nombre / Raz&oacute;n Social","w"=>390 ),
			2=>array( "nomb"=>"Doc. de Identidad","w"=>130 ),
			3=>array( "nomb"=>"Direcci&oacute;n","w"=>390 ),
			4=>array( "nomb"=>"Tel&eacute;fono","w"=>130 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
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
		if(isset($f->request->data['rol']))
			$params['rol'] = array(
				'nomb'=>'roles.'.$f->request->data['rol'],
				'value'=>array('$exists'=>true)
			);
		if(isset($f->request->data['fields'])) $params['fields'] = $f->request->data['fields'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mg/entidad")->params($params)->get("lista") );
	}
	function execute_search(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		);
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
		if(isset($f->request->data['rol']))
			$params['rol'] = array(
				'nomb'=>'roles.'.$f->request->data['rol'],
				'value'=>array('$exists'=>true)
			);
		if(isset($f->request->data['texto']))
			$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['fields'])) $params['fields'] = $f->request->data['fields'];
		$model = $f->model("mg/entidad")->params($params)->get("lista");
		//print_r($model->items);
		$f->response->json($model);
	}
	function execute_search_all(){
		global $f;
		$params = array();
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
		if(isset($f->request->data['fields'])) $params['fields'] = $f->request->data['fields'];
		$model = $f->model("mg/entidad")->params($params)->get("search_all");
		$f->response->json( $model->items );
	}
	function execute_search_tra(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search_tra");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$data = $f->model("mg/entidad")->params(array("_id"=>$f->request->data['_id']))->get("one")->items;
		if(isset($data['roles']['trabajador'])){
			if(isset($data['roles']['trabajador']['cargo']['organizacion'])){
				$data['roles']['trabajador']['cargo']['organizacion'] = $f->model("mg/orga")->params(array("_id"=>$data['roles']['trabajador']['cargo']['organizacion']['_id']))->get("one")->items;
			}
			if(isset($data['roles']['trabajador']['nivel'])){
				$data['roles']['trabajador']['nivel'] = $f->model('pe/nive')->params(array('_id'=>$data['roles']['trabajador']['nivel']['_id']))->get('one')->items;
			}
			if(isset($data['roles']['trabajador']['nivel_carrera'])){
				$data['roles']['trabajador']['nivel_carrera'] = $f->model('pe/nive')->params(array('_id'=>$data['roles']['trabajador']['nivel_carrera']['_id']))->get('one')->items;
			}
			if(isset($data['roles']['trabajador']['pension'])){
				$data['roles']['trabajador']['pension'] = $f->model('pe/sist')->params(array('_id'=>$data['roles']['trabajador']['pension']['_id']))->get('one')->items;
			}
			if(isset($data['roles']['trabajador']['turno'])){
				$data['roles']['trabajador']['turno'] = $f->model('pe/turn')->params(array('_id'=>$data['roles']['trabajador']['turno']['_id']))->get('one')->items;
			}
			/*$boleta = $f->model('pe/docs')->params(array('filter'=>array(
				'trabajador._id'=>new MongoId($f->request->data["_id"]),
				'estado'=>array('$ne'=>'A'),
				'boletas'=>true
			),'sort'=>array('cod'=>-1),'limit'=>1))->get('custom_limit')->items;
			if($boleta!=null){
				$data['roles']['trabajador']['ultima_boleta'] = $boleta[0];
			}*/
		}
		$f->response->json( $data );
	}
	function execute_check(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("doc"=>$f->request->data['doc']))->get("doc")->items;
		if($model==null){
			if(strlen($f->request->data['doc'])==8) $tipo = 'dni';
			else $tipo = 'ruc';
			if($tipo=='ruc'){
				$token =  $token = 'apis-token-4908.KojfBrVYtN64yhm6P4N6m4iwugWKHDbA';
				$ruc = $f->request->data['doc'];

				// Iniciar llamada a API
				$curl = curl_init();
				// Buscar ruc sunat
				curl_setopt_array($curl, array(
					// para usar la versión 2
					CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $ruc,
					// para usar la versión 1
					// CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
					'Referer: http://apis.net.pe/api-ruc',
					'Authorization: Bearer ' . $token
					),
				));
				
				$response = curl_exec($curl);
				
				curl_close($curl);
				$enti = json_decode($response);
				if(isset($enti->message)) {
					# EN CASO DE NO RESPONDER BIEN, RETORNAR FALSO PORQUE NO LO ENCONTRO
					$f->response->json( array('check'=>false) );
				}else{
					$data = array(
						'nomb'=>$enti->razonSocial,
						'tipo_enti'=>'E',
						"webservice"=>"apis.net.pe",
						'fecreg' => new MongoDate(),
						'autor' => $f->session->userDB,
						'docident'=>array(
							array('tipo'=>'RUC','num'=>$enti->numeroDocumento)
						),
						'domicilios'=>array(
							array('tipo'=>'FISCAL','direccion'=>$enti->direccion.' '.$enti->distrito.' - '.$enti->provincia.' - '.$enti->departamento)
						)
					);
						$enti = $f->model('mg/entidad')->params(array('data'=>$data))->save('insert')->items;
						$f->response->json( array('check'=>true,'enti'=>$enti) );
				}
			}else{
				$token = 'apis-token-4908.KojfBrVYtN64yhm6P4N6m4iwugWKHDbA';
				$dni = $f->request->data['doc'];
				$curl = curl_init();

				curl_setopt_array($curl, array(
					//VERSION 2
					CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 2,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Referer: https://apis.net.pe/consulta-dni-api',
						'Authorization: Bearer ' . $token
						),
					));
				$response = curl_exec($curl);
				$enti = json_decode($response);
				
				if (!isset($enti->nombres) || empty($enti->nombres) || !isset($enti->apellidoPaterno) || empty($enti->apellidoPaterno) || !isset($enti->apellidoMaterno) || empty($enti->apellidoMaterno)) {
					# EN CASO DE NO RESPONDER BIEN, RETORNAR FALSO PORQUE NO LO ENCONTRO
					$f->response->json( array('check'=>false) );
				} else {
					# PROCESAR INFORMACION DE APIS.NET.PE
					$data = array(
						'nomb' => $enti->nombres,
						'appat' => $enti->apellidoPaterno,
						'apmat' => $enti->apellidoMaterno,
						'fullname' => $enti->nombres.' '.$enti->apellidoPaterno.' '.$enti->apellidoMaterno,
						'tipo_enti' => 'P',
						'webservice' => 'apis.net.pe',
						'fecreg' => new MongoDate(),
						'autor' => $f->session->userDB,
						'docident' => array(
							array('tipo' => 'DNI', 'num' => $enti->numeroDocumento)
						)
					);
				
					$enti = $f->model('mg/entidad')->params(array('data' => $data))->save('insert')->items;
					$f->response->json(array('check' => true, 'enti' => $enti));
				}
			}
		}else{
			$f->response->json( array('check'=>true,'enti'=>$model[0]) );
		}
	}

	function execute_details(){
		global $f;
		$f->response->view("mg/enti.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/enti.edit");
	}
	function execute_edit_bootstrap(){
		global $f;
		$f->response->view("mg/enti.edit.bootstrap");
	}
	function execute_select(){
		global $f;
		$f->response->view("mg/enti.select");
	}
	function execute_view_search(){
		global $f;
		$f->response->view("mg/enti.search");
	}
	function execute_add_doc(){
		global $f;
		$f->response->view("mg/enti.doc");
	}
	function execute_add_domi(){
		global $f;
		$f->response->view("mg/enti.domi");
	}
	function execute_select_bootstrap(){
		global $f;
		$f->response->view("mg/enti.select.bootstrap");
	}
	function execute_save(){
		global $f;
		$temporal = $f->model('mg/entidad')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$data = $f->request->data;
		if(isset($data['nomb'])){
			if(isset($data['appat'])){
				$data['fullname'] = trim($data['nomb']).' '.trim($data['appat']).' '.trim($data['apmat']);
			}else{
				$data['fullname'] = trim($data['nomb']);
			}
		}
		if(isset($data['dni'])){
			if($data['dni']!='--'){
				if(isset($temporal)){
					if(isset($temporal['docident'])){
						$check = false;
						foreach ($temporal['docident'] as $i=>$doc) {
							if($doc['tipo']=='DNI'){
								$data['docident.'.$i.'.num'] = $data['dni'];
								$check = true;
							}
						}
						if($check==false){
							$data['docident.'.sizeof($temporal['docident'])] = array(
								'tipo'=>'DNI',
								'num'=>$data['dni']
							);
						}
					}else{
						if(!isset($data['docident']))
							$data['docident'] = array();
						$data['docident'][] = array(
							'tipo'=>'DNI',
							'num'=>$data['dni']
						);
					}
				}
				
			}
		}
		if(isset($data['ruc'])){
			if($data['ruc']!='--'){
				if(isset($temporal['docident'])){
					$check = false;
					foreach ($temporal['docident'] as $i=>$doc) {
						if($doc['tipo']=='RUC'){
							$data['docident.'.$i.'.num'] = $data['ruc'];
							$check = true;
						}
					}
					if($check==false){
						$data['docident.'.sizeof($temporal['docident'])] = array(
							'tipo'=>'RUC',
							'num'=>$data['ruc']
						);
					}
				}else{
					if(!isset($data['docident']))
						$data['docident'] = array();
					$data['docident'][] = array(
						'tipo'=>'RUC',
						'num'=>$data['ruc']
					);
				}
			}
		}
		if(isset($data['domicilios'])){
			if(isset($temporal['domicilios'])){
				if(sizeof($data['domicilios'])<sizeof($temporal['domicilios'])){
					foreach ($temporal['domicilios'] as $k=>$dom) {
						if(!isset($dom['tipo'])){
							$data['domicilios.'.$k.'.direccion'] = $data['domicilios'][0]['direccion'];
						}elseif($dom['tipo']=='PERSONAL'){
							$data['domicilios.'.$k.'.direccion'] = $data['domicilios'][0]['direccion'];
						}
					}
				}
			}
		}
		if(isset($data['imagen'])){
			if(substr($data['imagen'], 0, 3)!='/Us')
				$data['imagen'] = new MongoId($data['imagen']);
		}
		if(isset($data['roles']['proveedor'])) $data['roles']['proveedor'] = true;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['fecreg'] = new MongoDate();
			if(isset($data['roles'])){
				if($data['roles']['propietario']==true){
					$data['roles']['propietario'] = array(
						'ocupantes'=>array(),
						'espacios'=>array()
					);
				}
				if($data['roles']['ocupante']==true){
					$data['roles']['ocupante'] = array(
						'difunto'=>false,
						'propietario'=>true,
						'espacio'=>true
					);
				}
			}
			$model = $f->model("mg/entidad")->params(array('data'=>$data))->save("insert")->items;
		}else{
			$model = $f->model("mg/entidad")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('mg/entidad')->delete('datos');
    	$f->response->print( "true" );
	}
	function execute_all_ocu_pro(){
		global $f;
		$model = $f->model('mg/entidad')->get('all_ocu_pro');
		$f->response->json($model->items);
	}
	function execute_all_difu_pro(){
		global $f;
		$model = $f->model('mg/entidad')->params(array('_id'=>$f->request->data['_id']))->get('all_difu_pro');
		$f->response->json($model->items);
	}
	function execute_all_difu_espa(){
		global $f;
		$model = $f->model('mg/entidad')->get('all_difu_espa');
		$f->response->json($model->items);
	}
	function execute_search_empresas(){
		global $f;
		$model = $f->model("mg/entidad")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search_empresas");
		$f->response->json( $model );
	}
	function execute_save_add_doc(){
		global $f;
		$model = $f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['entidad']),
			'data'=>array('$push'=>array('docident'=>array(
				'tipo'=>$f->request->data['tipo'],
				'num'=>$f->request->data['num']
			)))
		))->save('custom');
	}
	function execute_save_add_domi(){
		global $f;
		$model = $f->model('mg/entidad')->params(array(
			'_id'=>new MongoId($f->request->data['_id']),
			'data'=>array('$push'=>array('domicilios'=>array(
				'direccion'=>$f->request->data['domicilio'],
				'descr'=>$f->request->data['descr']
			)))
		))->save('custom');
	}
	function execute_verify(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"roles"=>'trabajador'
		))->get("all_trab");
		$index = 1;
		foreach($model->items as $i=>$item){
			$orga = $f->model("mg/orga")->params(array("_id"=>$item["roles"]["trabajador"]["cargo"]["organizacion"]["_id"]))->get("one")->items;
			$text = $index.".- ".$item["nomb"]." ".$item["appat"]." ".$item["apmat"]." - organizacion no existe";
			if($orga==null){
				$text .=' [SIN ORGANIZACION]';
			}else{
				$text .=' [CON ORGANIZACION]';
			}
			$acti = $f->model("pr/acti")->params(array("_id"=>$item["roles"]["trabajador"]["cargo"]["organizacion"]["actividad"]["_id"]))->get("one");
			if($acti->items==null){
				$text .=' [SIN ACTIVIDAD]';
			}else{
				$text .=' [CON ACTIVIDAD]';
			}
			$comp = $f->model("pr/acti")->params(array("_id"=>$item["roles"]["trabajador"]["cargo"]["organizacion"]["componente"]["_id"]))->get("one");
			if($comp->items==null){
				$text .=' [SIN COMPONENTE]';
			}else{
				$text .=' [CON COMPONENTE]';
			}
			$f->response->print($text."<br />");
			$index++;
			/*$data["roles.trabajador.cargo.organizacion"] = array(
				"_id"=>$orga["_id"],
				"nomb"=>$orga['nomb'],
				"componente"=>$orga["componente"],
				"actividad"=>$orga["actividad"]
			);
			$data["roles.trabajador.organizacion"]= array(
				"_id"=>$orga["_id"],
				"nomb"=>$orga['nomb'],
				"componente"=>$orga["componente"],
				"actividad"=>$orga["actividad"]
			);
			$f->model("mg/entidad")->params(array("_id"=>$item['_id'],"data"=>$data))->save("update");*/
		}
	}
}
?>