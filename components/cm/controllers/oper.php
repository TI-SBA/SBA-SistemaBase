<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'google/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;

class Controller_cm_oper extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("cm/oper");
	}
	function execute_subir(){
		global $f;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
				$output = array();
				$client = new Google_Client();
				$client->setAuthConfig(IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$projectId = 'sbpa-153705';
				$bucketName = 'cementerio-storage';
				$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
				$storage = new Google_Service_Storage($client);
				$fileTmpName = $target_path;
				$file_name = $_FILES['file_upload']['name'];
				$obj = new Google_Service_Storage_StorageObject();
				$obj->setName($file_name);
				$insert = $storage->objects->insert("cementerio-storage",$obj,array(
						'name' => $file_name,
						'data' => file_get_contents($fileTmpName),
						'uploadType' => 'media',
						'predefinedAcl' => 'publicRead'
					)
				);
				$output =$insert;
				
				$descarga = $storage->objects->get($bucketName,$file_name);
				
				$descarga->mediaLink;
			}else{
				$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 1!');
			}
		}else{
			$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 2!');
		}
		$f->response->json($output);
	}
	function execute_reporte_analisis(){
		global $f;
		$data = $f->datastore->cm_operaciones->aggregate(array(
			array(
				'$match'=>array(
					'inhumacion.fecdef'=>array('$exists'=>true)
				)
			),
			array(
				'$project'=>array(
					//'fecha_inhumacion'=> array( '$dateToString'=> array( 'format'=> "%Y-%m-%d", 'date'=> '$inhumacion.fecdef' ) ),
					'fecha_inhumacion'=>'$inhumacion.fecdef'
					//'total'=>'$count'
				)
			),
			/*array(
				'$group'=>array(
					//'_id'=>array( 'month'=>array( '$month'=> "$inhumacion.fecdef" ), day=> array( '$dayOfMonth'=> "$inhumacion.fecdef" ), 'year'=> array( '$year'=> "$inhumacion.fecdef" ) ),
					'_id'=>array('fecdef'=>'$fecha_inhumacion'),
					'count'=>array('$sum'=>1)
				),
			),*/
			/*array(
				'$project'=>array(
					//'ano'=>'$year',
					//'mes'=>'$month',
					//'dia'=>'$day',
					//'fecha_inhumacion'=>'$fecdef',
					'fecha_inhumacion'=> array( '$dateToString'=> array( 'format'=> "%Y-%m-%d", 'date'=> '$fecdef' ) ),
					'count'=>'$count'
				)
			)*/
		));

		$_data=array();
		$count=1;
		for($i=2011;$i<=2017;$i++){
			for($j=1;$j<=12;$j++){
				$_data[$i.'-'.$j] = array(
					'ano'=>$i,
					'mes'=>$j,
					'orden'=>$count,
					'count'=>0
				);
				$count++;
			}
		}

		$rpta = array();

		foreach($data['result'] as $item){
			if($item['fecha_inhumacion']!=null){
				/*if(!isset($rpta[date('Y-m-d', $item['fecha_inhumacion']->sec)])){
					$rpta[date('Y-m-d', $item['fecha_inhumacion']->sec)] = array(
						'fecha'=>date('Y-m-d', $item['fecha_inhumacion']->sec),
						'ano'=>date('Y', $item['fecha_inhumacion']->sec),
						'mes'=>date('m', $item['fecha_inhumacion']->sec),
						'dia'=>date('d', $item['fecha_inhumacion']->sec),
						'count'=>0
					);
				}
				$rpta[date('Y-m-d', $item['fecha_inhumacion']->sec)]['count']++;*/
				$ano = floatval(date('Y', $item['fecha_inhumacion']->sec));
				$mes = floatval(date('m', $item['fecha_inhumacion']->sec));
				if(isset($_data[$ano.'-'.$mes])){
					$_data[$ano.'-'.$mes]['count']++;
				}
			}
		}

		echo '<table>';
		foreach($_data as $item){
			echo '<tr>';
			echo '<td>'.$item['orden'].'</td>';
			echo '<td>'.$item['ano'].'</td>';
			echo '<td>'.$item['mes'].'</td>';
			echo '<td>'.$item['count'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
		//print_r($data);

	}
	function execute_reporte_analisis2(){
		$data = $f->datastore->cm_operaciones->find(array('inhumacion.fecdef'=>array('$exists'=>true)));
		echo '<table>';
		foreach($data as $item){
			echo '<tr>';
			echo '<td>'.date('Y-m-d',$item['inhumacion']['fecdef']->sec).'</td>';
			echo '<td>1</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	function execute_get(){
		global $f;
		$model = $f->model("cm/oper")->params(array("_id"=>$f->request->id))->get("one");
		$model->items['espacio'] = $f->model("cm/espa")->params(array("_id"=>$model->items['espacio']['_id']))->get("one2")->items;
		if(isset($f->request->data['servicio'])){
			if(isset($model->items['cuentas_cobrar'])){
				if(is_array($model->items['cuentas_cobrar'])){
					$model->items['cuentas_cobrar'] = $f->model('cj/cuen')->params(array('_id'=>$model->items['cuentas_cobrar'][0]))->get('one')->items;
				}else{
					$model->items['cuentas_cobrar'] = $f->model('cj/cuen')->params(array('_id'=>$model->items['cuentas_cobrar']))->get('one')->items;
				}
			}
			
		}
		$f->response->json( $model->items );
	}
	function execute_lista_hist(){
		global $f;
		$params = array(
			"texto"=>$f->request->data['texto'],
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['fecbus']))
			$params['fecbus'] = $f->request->data['fecbus'];
		if(isset($f->request->data['oper'])){
			if($f->request->data['oper']!='')
				$params['oper'] = $f->request->data['oper'];
		}
		if(isset($f->request->data['tipo'])){
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("cm/hope")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_lista(){
		global $f;
		$params = array(
			"texto"=>$f->request->data['texto'],
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['progra']))
			$params['progra'] = $f->request->data['progra'];
		if(isset($f->request->data['fecbus']))
			$params['fecbus'] = $f->request->data['fecbus'];
		if(isset($f->request->data['oper'])){
			if($f->request->data['oper']!='')
				$params['oper'] = $f->request->data['oper'];
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("cm/oper")->params($params)->get("lista");
		foreach ($model->items as $key => $item) {
			if(isset($item['espacio']))
				$model->items[$key]['espacio'] = $f->model('cm/espa')->params(array('_id'=>$item['espacio']['_id']))->get('one2')->items;
		}
		$f->response->json( $model );
	}
	function execute_listaconcrec(){
		global $f;
		$model = $f->model("cm/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaconcrec");
		$f->response->json( $model );
	}
	function execute_listaconcpor(){
		global $f;
		$model = $f->model("cm/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaconcpor");
		$f->response->json( $model );
	}
	function execute_listaconcven(){
		global $f;
		$model = $f->model("cm/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaconcven");
		$f->response->json( $model );
	}
	function execute_listaconc(){
		global $f;
		$model = $f->model("cm/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaconc");
		$f->response->json( $model );
	}
	function execute_listaprog(){
		global $f;
		$params = array(
			"texto"=>"",
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['fecbus']))
			$params['fecbus'] = $f->request->data['fecbus'];
		if(isset($f->request->data['all']))
			$params['all'] = true;
		if(isset($f->request->data['oper'])){
			if($f->request->data['oper']!='')
				$params['oper'] = $f->request->data['oper'];
		}
		if(isset($f->request->data['texto']))
			$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("cm/oper")->params($params)->get("listaprog");
		$f->response->json( $model );
	}
	function execute_listaprog_all(){
		global $f;
		$params = array(
			"texto"=>"",
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows']
		);
		if(isset($f->request->data['texto']))
			$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$model = $f->model("cm/oper")->params($params)->get("listaprog_all");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("cm/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search_all");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('cm/oper')->get('all');
		$f->response->json($model->items);
	}
	function execute_all_hist(){
		global $f;
		$model = $f->model('cm/hope')->params(array("espacio"=>new MongoId($f->request->espacio)))->get('all');
		$f->response->json($model->items);
	}
	function execute_get_hist(){
		global $f;
		$model = $f->model("cm/hope")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_conce(){
		global $f;
		$params = array(
			'concesion.estado'=>'A'
		);
		if(isset($f->request->data['espacio']))
			$params['espacio._id'] = new MongoId($f->request->data['espacio']);
		if(isset($f->request->data['propietario']))
			$params['propietario._id'] = new MongoId($f->request->data['propietario']);
		$model = $f->model("cm/oper")->params(array('filter'=>$params))->get("custom");
		$f->response->json( $model->items[0] );
	}
	function execute_save_conc(){
		global $f;
		$enti = $f->request->data['propietario']['nomb'];
		if($f->request->data['propietario']['tipo_enti']=='P')
			$enti .= ' '.$f->request->data['propietario']['appat'].' '.$f->request->data['propietario']['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se concesion&oacute; el espacio <b>'.$f->request->data['espacio']['nomb'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
    	$oper = $f->model('cm/oper')->save('datos_conc');
    	$array['_id'] = new MongoId($f->request->data['espacio']['_id']);
    	$array['propietario'] = $f->request->data['propietario'];
    	$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		//$f->model('cm/espa')->params($array)->save('propietario');
		$f->model('cm/espa')->params($array)->save('prop');
		$pro['_id'] = $array['propietario']['_id'];
		$pro['espacio']['_id'] = new MongoId($f->request->data['espacio']['_id']);
		$pro['espacio']['nomb'] = $f->request->data['espacio']['nomb'];
		$f->model('mg/entidad')->params($pro)->save('propietario_espa');
		if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $f->request->data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$cuenta['cliente']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
		}
    	$f->response->print( "true" );
	}
	function execute_save_inhu(){
		global $f;
		$enti = $f->request->data['data']['ocupante']['nomb'];
		if(isset($f->request->data['data']['ocupante']['appat']))
			$enti .= ' '.$f->request->data['data']['ocupante']['appat'].' '.$f->request->data['data']['ocupante']['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Inhumaci&oacute;n</b> en el espacio <b>'.$f->request->data['data']['espacio']['nomb'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
    	$oper = $f->model('cm/oper')->save('datos_inhu');
    	$array['_id'] = new MongoId($f->request->data['data']['ocupante']['_id']);
    	if(isset($f->request->data['fecnac']))
    		$array['fecnac'] = $f->request->data['fecnac'];
    	else
    		$array['fecnac'] = '';
    	if(isset($f->request->data['inhumacion']['fecdef']))
    		$array['fecdef'] = $f->request->data['inhumacion']['fecdef'];
    	else
    		$array['fecdef'] = '';
		$f->model('mg/entidad')->params($array)->save('estado_difunto');






		if(isset($f->request->data['data']['fec_auto'])){
			$f->model('cm/oper')->params(array(
				'_id'=>$oper->items['_id'],
				'data'=>array(
					'$set'=>array(
						'fecreg'=>new MongoDate(strtotime($f->request->data['data']['fec_auto'])),
						'ejecucion'=>array(
							"fecini"=>new MongoDate(strtotime($f->request->data['data']['fec_auto'])),
							"fecfin"=>new MongoDate(strtotime($f->request->data['data']['fec_auto'])),
							"trabajador"=>$oper->items['trabajador'],
							"observ"=>''
						)
					)
				)
			))->save('update');
		}








		if(isset($f->request->data['data']['cuenta_cobrar'])){
			$cuenta = $f->request->data['data']['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
		}
    	$f->response->print( "true" );
	}
	function execute_save_cons(){
		global $f;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Construcci&oacute;n de Mausoleo</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
    	$oper = $f->model('cm/oper')->save('datos_cons');
    	if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $f->request->data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
    	}
    	$f->response->print( "true" );
	}
	function execute_save_amp(){
		global $f;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Ampliaci&oacute;n de Mausoleo</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
    	$oper = $f->model('cm/oper')->save('datos_amp');
    	if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $f->request->data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
    	}
    	$f->response->print( "true" );
	}
	function execute_save_colo(){
		global $f;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Colocaci&oacute;n de Accesorios</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
    	$oper = $f->model('cm/oper')->save('datos_colo');
    	if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $f->request->data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
    	}
    	$f->response->print( "true" );
	}
	function execute_save_tras(){
		global $f;
		$enti = $f->request->data['ocupante']['nomb'];
		if(isset($f->request->data['ocupante']['appat']))
			$enti .= ' '.$f->request->data['ocupante']['appat'].' '.$f->request->data['ocupante']['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Traslado</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
    	$oper = $f->model('cm/oper')->save('datos_tras');




		$f->model('cm/espa')->params(array('_id'=>$oper->items['espacio']['_id'],'data'=>array('$set'=>array(
			'estado'=>'D'
		))))->save('custom');
		$f->model('cm/espa')->params(array('_id'=>$oper->items['espacio']['_id'],'data'=>array(
			'$unset'=>array('propietario'=>true,'ocupante'=>true)
		)))->save('custom');




    	if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $f->request->data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
				$f->model('mg/entidad')->params(array(
					'_id'=>$oper->items['propietario']['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
    	}
    	$f->response->print( "true" );
	}
	function execute_save_asig(){
		global $f;//print_r($f->request->data);die();
		$operaciones;
		$prop;
		$trab;
		$adj = false;
		$enti = $f->request->data['ocupante']['nomb'];
		if($f->request->data['ocupante']['tipo_enti']=='P')
			$enti .= ' '.$f->request->data['ocupante']['appat'].' '.$f->request->data['ocupante']['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Asignaci&oacute;n</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b> para <b>'.$enti.'</b>'
		))->save('insert');
		if(isset($f->request->data['ocupante_old'])){
			if(sizeof($f->request->data['ocupante_old'])>0){
				foreach ($f->request->data['ocupante_old'] as $key => $oc) {
					$ocupante = array(
						"_id"=>$oc['_id'],
						"espacio"=>$f->request->data['espacio'],
						"propietario"=>$f->request->data['propietario']
					);
					$f->model('cm/ocup')->params(array('data'=>$ocupante))->save('update');
					$propietario = array(
						"_id"=>$f->request->data['propietario']['_id'],
						"ocupante"=>$oc
					);
					$f->model('cm/prop')->params(array('data'=>$propietario))->save('update');
					$espa = array(
						"_id"=>$f->request->data['espacio']['_id'],
						"ocupante"=>$oc
					);
					$f->model('cm/espa')->params(array('data'=>$espa))->save('update_ocup');
				}
			}
		}
		if($f->request->data['asignacion']!='true'){
			$array = array(
				"fecreg"=>new MongoDate(),
				"trabajador"=>$f->session->userDB,
				"programacion"=>array(
					"fecprog"=>new MongoDate(strtotime($f->request->data['asignacion']['programacion']['fecprog'])),
					"observ"=>""
				),
				"propietario"=>$f->request->data['propietario'],
				"ocupante"=>$f->request->data['asignacion']['ocupante'],
				"espacio"=>$f->request->data['espacio'],
				"adjuntacion"=>true
			);
			$f->model('cm/oper')->params(array('data'=>$array))->save('new_adju');
			$adj = true;
		}
		if(isset($f->request->data['ocupante']['_id'])){
			$tmp_asig = $f->request->data;
			if($adj==true)
				$tmp_asig['tmp_adj'] = true;
			$oper = $f->model('cm/oper')->params(array('data'=>$tmp_asig))->save('new_asig');
			$operaciones = $oper->items['_id'];
			$prop = $oper->items['propietario'];
			$trab = $oper->items['trabajador'];
			$ocupante = array(
				"_id"=>$f->request->data['ocupante']['_id'],
				"espacio"=>$f->request->data['espacio'],
				"propietario"=>$f->request->data['propietario']
			);
			$f->model('cm/ocup')->params(array('data'=>$ocupante))->save('update');
			$propietario = array(
				"_id"=>$f->request->data['propietario']['_id'],
				"ocupante"=>$f->request->data['ocupante']
			);
			$f->model('cm/prop')->params(array('data'=>$propietario))->save('update');
			$espa = array(
				"_id"=>$f->request->data['espacio']['_id'],
				"ocupante"=>$f->request->data['ocupante']
			);
			$f->model('cm/espa')->params(array('data'=>$espa))->save('update_ocup');
		}else{
			$operaciones = array();
			foreach ($f->request->data['ocupante'] as $ocup){
				$array = $f->request->data;
				$array['ocupante'] = $ocup;
				if($adj==true)
					$array['tmp_adj'] = true;
				$oper = $f->model('cm/oper')->params(array('data'=>$array))->save('new_asig');
				$operaciones[] = $oper->items['_id'];
				$prop = $oper->items['propietario'];
				$trab = $oper->items['trabajador'];
				$ocupante = array(
					"_id"=>$ocup['_id'],
					"espacio"=>$f->request->data['espacio'],
					"propietario"=>$f->request->data['propietario']
				);
				$f->model('cm/ocup')->params(array('data'=>$ocupante))->save('update');
				$propietario = array(
					"_id"=>$f->request->data['propietario']['_id'],
					"ocupante"=>$ocup
				);
				$f->model('cm/prop')->params(array('data'=>$propietario))->save('update');
				$espa = array(
					"_id"=>$f->request->data['espacio']['_id'],
					"ocupante"=>$ocup
				);
				$f->model('cm/espa')->params(array('data'=>$espa))->save('update_ocup');
			}
		}
		$espa_tm = $f->model('cm/espa')->params(array('_id'=>new MongoId($f->request->data['espacio']['_id'])))->get('one2')->items;
		$espa = array(
			'_id'=>$espa_tm['_id'],
			'nomb'=>$espa_tm['nomb']
		);
		if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $f->request->data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $prop;
				$cuenta['espacio'] = $espa;
				$cuenta['cliente'] = $prop;
				$cuenta['autor'] = $trab;
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $operaciones;
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				if(is_array($operaciones)){
					foreach ($operaciones as $op){
						$f->model('cm/oper')->params(array(
							'_id'=>$op,
							'data'=>array('$push'=>array('cuentas_cobrar'=>$cuen->items['_id']))
						))->save('update');
					}
					$f->model('cm/oper')->params(array(
						'_id'=>$op,
						'data'=>array('$set'=>array('cuentas_cobrar_mul'=>true))
					))->save('update');
				}else{
					$f->model('cm/oper')->params(array(
						'_id'=>$operaciones,
						'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
					))->save('update');
				}
				$f->model('mg/entidad')->params(array(
					'_id'=>$prop['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $prop;
					$cu['espacio'] = $espa;
					$cu['cliente'] = $prop;
					$cu['autor'] = $trab;
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $operaciones;
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				if(is_array($operaciones)){
					foreach ($operaciones as $op){
						$f->model('cm/oper')->params(array(
							'_id'=>$op,
							'data'=>array('$push'=>array('cuentas_cobrar'=>$cuentas))
						))->save('update');
					}
					$f->model('cm/oper')->params(array(
						'_id'=>$op,
						'data'=>array('$set'=>array('cuentas_cobrar_mul'=>true))
					))->save('update');
				}else{
					$f->model('cm/oper')->params(array(
						'_id'=>$operaciones,
						'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas))
					))->save('update');
				}
				$f->model('mg/entidad')->params(array(
					'_id'=>$prop['_id'],
					'rol'=>'roles.cliente'
				))->save('rol');
			}
		}
    	$f->response->print( "true" );
	}
	function execute_save_ejecutar(){
		global $f;
		$f->model('cm/oper')->params(array('data'=>$f->request->data))->save('ejecutar');
		if(isset($f->request->data['traslado'])){
			$f->model('cm/espa')->params(array('_id'=>$f->request->data['espacio']['_id'],'ocupante'=>$f->request->data['ocupante']['_id']))->delete('ocupante');
			if(isset($f->request->data['traslado']['espacio_destino'])){
				/*if($f->request->data['traslado']['espacio_destino']['nomb']!='Osario'){

				}else{*/
					$f->model('cm/espa')->params(array('data'=>array(
						'_id'=>$f->request->data['traslado']['espacio_destino']['_id'],
						'ocupante'=>$f->request->data['ocupante'])
					))->save('update_ocup');
					$f->response->print("aaaa<br />\n");
					$f->model('cm/ocup')->params(array('data'=>array(
						'_id'=>$f->request->data['ocupante']['_id'],
						'espacio'=>$f->request->data['traslado']['espacio_destino'])
					))->save('upd_espacio');
				//}
			}else{
				$f->model('cm/ocup')->params(array('_id'=>$f->request->data['ocupante']['_id']))->delete('espacio');
			}
		}
		$oper = $f->model('cm/oper')->params(array('_id'=>$f->request->data['_id']))->get('one')->items;
		$enti = $oper['ocupante']['nomb'];
		if(isset($oper['ocupante']['appat']))
			$enti .= ' '.$oper['ocupante']['appat'].' '.$oper['ocupante']['apmat'];
		$operacion = "";
		if(isset($oper['concesion']))
			$operacion = 'Concesion';
		else if(isset($oper['construccion']))
			$operacion = 'Construcci&oacute;n';
		else if(isset($oper['asignacion']))
			$operacion = 'Asignaci&oacute;n';
		else if(isset($oper['inhumacion']))
			$operacion = 'Inhumaci&oacute;n';
		else if(isset($oper['traslado']))
			$operacion = 'Traslado';
		else if(isset($oper['colocacion']))
			$operacion = 'Colocaci&oacute;n';
		else if(isset($oper['adjuntacion']))
			$operacion = 'Adjuntaci&oacute;n';
		$oper = $f->model('cm/oper')->params(array('_id'=>$f->request->data['_id']))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la operaci&oacute;n de <b>'.$operacion.'</b> en el espacio <b>'.$oper['espacio']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_exe_cons(){
		global $f;
		$recibido = $f->request->data['recibido'];
		$recibido['_id'] = new MongoId($recibido['_id']);
		$f->model('cm/oper')->params(array(
			'_id'=>$f->request->data['_id'],
			'observ'=>$f->request->data['observ'],
			'espacio'=>$f->request->data['espacio'],
			'fecini'=>$f->request->data['fecini'],
			'capacidad'=>$f->request->data['capacidad'],
			'ancho'=>$f->request->data['ancho'],
			'largo'=>$f->request->data['largo'],
			'altura1'=>$f->request->data['altura1'],
			'altura2'=>$f->request->data['altura2'],
			'tipo'=>$f->request->data['tipo'],
			'recibido'=>$recibido
		))->save('exe_cons');
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la operaci&oacute;n de <b>Construcci&oacute;n</b> en el espacio <b>'.$oper['espacio']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_exe_amp(){
		global $f;
		$recibido = $f->request->data['recibido'];
		$recibido['_id'] = new MongoId($recibido['_id']);
		$f->model('cm/oper')->params(array(
			'_id'=>$f->request->data['_id'],
			'observ'=>$f->request->data['observ'],
			'espacio'=>$f->request->data['espacio'],
			'fecini'=>$f->request->data['fecini'],
			'capacidad'=>$f->request->data['capacidad'],
			'ancho'=>$f->request->data['ancho'],
			'largo'=>$f->request->data['largo'],
			'altura1'=>$f->request->data['altura1'],
			'altura2'=>$f->request->data['altura2'],
			'tipo'=>$f->request->data['tipo'],
			'recibido'=>$recibido
		))->save('exe_amp');
		$oper = $f->model('cm/oper')->params(array('_id'=>$f->request->data['_id']))->get('one')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la operaci&oacute;n de <b>Ampliaci&oacute;n</b> en el espacio <b>'.$oper['espacio']['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_save_anul_conce(){
		global $f;
		$f->model("cm/oper")->params(array(
			"espacio"=>$f->request->data['espacio']['_id'],
			"observ"=>$f->request->data['observ']
		))->save("anul_conce");
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la finalizaci&oacute;n de <b>Concesi&oacute;n</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
		if(isset($f->request->data['asignados'])){
			if(sizeof($f->request->data['asignados'])>0){
				foreach ($f->request->data['asignados'] as $ocup){
					$f->model('cm/oper')->params(array(
			    		'ocupante'=>$ocup['_id'],
			    		'observ'=>$f->request->data['observ']
			    	))->save('anul_asig');
			    	$f->model('mg/entidad')->params(array(
			    		"ocupante"=>$ocup['_id'],
			    		"propietario"=>$f->request->data['propietario']['_id']
			    	))->save('anu_asignacion');
			    	$f->model('cm/prop')->params(array(
			    		'_id'=>$f->request->data['propietario']['_id'],
			    		'ocupante'=>$ocup['_id']
			    	))->delete('ocupante');
			    	$f->model('cm/espa')->params(array(
				    	'_id'=>$f->request->data['espacio']['_id'],
				    	'ocupante'=>$ocup['_id']
			    	))->delete('ocupante');
				}
			}
		}
		if(isset($f->request->data['difuntos'])){
			if(sizeof($f->request->data['difuntos'])>0){
				$osario = $f->model("cm/espa")->get("osario");
				$osario = $osario->items;
				foreach ($f->request->data['difuntos'] as $difu){
					$array = array();
					$array['fecreg'] = new MongoDate();
					$array['propietario'] = $f->request->data['propietario'];
					$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
					$array['espacio'] = $f->request->data['espacio'];
					$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
					$array['ocupante'] = $difu;
					$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
					$array['trabajador'] = $f->session->userDB;
					$array['programacion']['fecprog'] = new MongoDate(strtotime($f->request->data['fecprog']));
					$array['traslado']['espacio_destino']['nomb'] = $osario['nomb'];
					$array['traslado']['espacio_destino']['_id'] = $osario['_id'];
					$f->model('cm/oper')->params(array(
						'data'=>$array
					))->save('operacion');
				}
			}
		}
		$f->model('cm/prop')->params(array(
			'_id'=>$f->request->data['propietario']['_id'],
			'espacio'=>$f->request->data['espacio']['_id']
		))->delete('espacio');
		$f->model('cm/espa')->params(array(
			'_id'=>$f->request->data['espacio']['_id']
		))->save('anul_conce');
	}
	function execute_save_trasp(){
		global $f;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Traspaso</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
		if(isset($f->request->data['ocupante'])){
			for($i=0; $i<sizeof($f->request->data['ocupante']); $i++){
				$array = array(
					"fecreg"=>new MongoDate(),
					"trabajador"=>$f->session->userDB,
					"propietario"=>$f->request->data['propietario'],
					"ocupante"=>$f->request->data['ocupante'][$i],
					"espacio"=>$f->request->data['espacio'],
					"traspaso"=>array("nuevo_propietario"=>$f->request->data['new_propietario'])
				);
				$f->model('cm/oper')->params(array('data'=>$array))->save('traspaso');
				$f->model('cm/ocup')->params(array(
					'_id'=>$f->request->data['ocupante'][$i]['_id'],
					'propietario'=>$f->request->data['new_propietario']
				))->save('upd_prop');
				$f->model('cm/prop')->params(array('data'=>array(
					'_id'=>$f->request->data['new_propietario']['_id'],
					'ocupante'=>$f->request->data['ocupante'][$i]
				)))->save('update');
				$f->model('cm/prop')->params(array(
					'_id'=>$f->request->data['propietario']['_id'],
					'ocupante'=>$f->request->data['ocupante'][$i]['_id']
				))->delete('ocupante');
			}
		}else{
			$array = array(
				"fecreg"=>new MongoDate(),
				"trabajador"=>$f->session->userDB,
				"propietario"=>$f->request->data['propietario'],
				"espacio"=>$f->request->data['espacio'],
				"traspaso"=>array("nuevo_propietario"=>$f->request->data['new_propietario'])
			);
			$f->model('cm/oper')->params(array('data'=>$array))->save('traspaso');
		}
		$f->model('cm/oper')->params(array(
			'espacio'=>$f->request->data['espacio'],
			'propietario'=>$f->request->data['propietario'],
			'new_propietario'=>$f->request->data['new_propietario']
		))->save('upd_trasp_conce');
		$f->model('cm/espa')->params(array(
			'_id'=>$f->request->data['espacio']['_id'],
			'propietario'=>$f->request->data['new_propietario']
		))->save('upd_prop');
		$f->model('cm/prop')->params(array(
			'_id'=>$f->request->data['new_propietario']['_id'],
			'espacio'=>$f->request->data['espacio']
		))->save('upd_espacio');
		$f->model('cm/prop')->params(array(
			'_id'=>$f->request->data['propietario']['_id'],
			'espacio'=>$f->request->data['espacio']['_id']
		))->delete('espacio');
    	$f->response->print( "true" );
	}
	function execute_save_reasig(){
		global $f;
    	$model = $f->model('cm/oper')->params(array(
    		'ocupante'=>$f->request->data['ocupante']['_id'],
    		'observ'=>$f->request->data['observ']
    	))->save('anul_asig');
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la anulaci&oacute;n de <b>Asignaci&oacute;n</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
    	$data = $f->request->data['data'];
    	$f->model('mg/entidad')->params(array("ocupante"=>$f->request->data['ocupante']['_id'], "propietario" => $data['propietario']['_id']))->save('anu_asignacion');
    	$f->model('cm/prop')->params(array('_id'=>$data['propietario']['_id'],'ocupante'=>$f->request->data['ocupante']['_id']))->delete('ocupante');
    	$f->model('cm/espa')->params(array('_id'=>$data['espacio']['_id'],'ocupante'=>$f->request->data['ocupante']['_id']))->delete('ocupante');
    	$f->response->print( "true" );
	}
	function execute_save_ocup_ante(){
		global $f;
		$tmp = $f->request->data;
		$tmp['trabajador'] = $f->session->userDB;
		$tmp['espacio']['_id'] = new MongoId($tmp['espacio']['_id']);
		$tmp['propietario']['_id'] = new MongoId($tmp['propietario']['_id']);
		if(isset($tmp['adjuntacion']))
			$tmp['adjuntacion']['ocupante']['_id'] = new MongoId($tmp['adjuntacion']['ocupante']['_id']);
		if(isset($tmp['inhumacion'])){
			if(isset($tmp['inhumacion']['funeraria']))
				$tmp['inhumacion']['funeraria']['_id'] = new MongoId($tmp['inhumacion']['funeraria']['_id']);
			if(isset($tmp['inhumacion']['municipalidad']))
				$tmp['inhumacion']['municipalidad']['_id'] = new MongoId($tmp['inhumacion']['municipalidad']['_id']);
			if(isset($tmp['inhumacion']['fecdef']))
				if($tmp['inhumacion']['fecdef']!='')
					$tmp['inhumacion']['fecdef'] = new MongoDate(strtotime($tmp['inhumacion']['fecdef']));
			if(isset($tmp['inhumacion']['fecinh']))
				if($tmp['inhumacion']['fecinh']!='')
					$tmp['inhumacion']['fecinh'] = new MongoDate(strtotime($tmp['inhumacion']['fecinh']));
		}
		foreach ($tmp['ocupantes'] as $ii=>$ocup){
			$ocup = $f->model('cm/ocup')->params( array('data'=>array(
	    		"fecreg"=>new MongoDate(),
	    		"nomb"=>$ocup['nomb'],
		    	"appat"=>$ocup['appat'],
		    	"apmat"=>$ocup['apmat'],
		    	"docident"=>array(0=>array(
	    			'tipo'=>'DNI',
	    			'num'=>$ocup['dni']
	    		)),
	    		"tipo_enti"=>"P",
	    		"roles"=>array(
	    			"ocupante"=>array(
	    				"difunto"=>false,
	    				"espacio"=>$tmp['espacio'],
	    				"propietario"=>$tmp['propietario']
	    			)
	    		)
	    	)) )->save('ocup')->obj;
	    	$tmp['ocupantes'][$ii] = $ocup;
		}
    	if(!isset($tmp['espacio_propietario'])){
    		//generar concesion
    		$concesion = array(
    			'ocupante_anterior'=>true,
    			'fecreg'=>new MongoDate(),
    			'trabajador'=>$tmp['trabajador'],
    			'propietario'=>$tmp['propietario'],
    			'espacio'=>$tmp['espacio'],
    			'concesion'=>array('estado'=>'A','condicion'=>$tmp['concesion']['condicion'])
    		);
    		if($tmp['concesion']['condicion']=='T'){
    			$concesion['concesion']['fecven'] = new MongoDate(strtotime($tmp['concesion']['fecven']));
    		}
    		$f->model('cm/oper')->params(array('data'=>$concesion))->save('operacion');
    		//Actualizar espacio
			$f->model('cm/espa')->params(array(
				'_id'=>$tmp['espacio']['_id'],
				'propietario'=>$tmp['propietario']
			))->save('prop');
			//Actualizar propietario
			$f->model('cm/prop')->params(array(
				'_id'=>$tmp['propietario']['_id'],
				'espacio'=>$tmp['espacio']
			))->save('new_espa');
    	}
		foreach ($tmp['ocupantes'] as $ii=>$ocup){
			//Asignacion
	    	$asignacion = array(
    			'ocupante_anterior'=>true,
				'propietario'=>$tmp['propietario'],
				'ocupante'=>$ocup,
				'espacio'=>$tmp['espacio'],
	    		'asignacion'=>true,
	    		'fecreg'=>new MongoDate(),
	    		'trabajador'=>$tmp['trabajador']
			);
			$f->model('cm/oper')->params(array('data'=>$asignacion))->save('operacion');
			$ocupante = array(
				"_id"=>$ocup['_id']->{'$id'},
				"espacio"=>$f->request->data['espacio'],
				"propietario"=>$f->request->data['propietario']
			);
			$f->model('cm/ocup')->params(array('data'=>$ocupante))->save('update');
			$propietario = array(
				"_id"=>$f->request->data['propietario']['_id'],
				"ocupante"=>array('_id'=>$ocup['_id']->{'$id'})
			);
			$f->model('cm/prop')->params(array('data'=>$propietario))->save('update');
			$espa = array(
				"_id"=>$f->request->data['espacio']['_id'],
				"ocupante"=>$ocup
			);
			$f->model('cm/espa')->params(array('data'=>$espa))->save('update_ocup_conId');
		}
		//Adjuntacion
		if(isset($tmp['adjuntacion'])){
			$adjuntacion = array(
    			'ocupante_anterior'=>true,
				"fecreg"=>new MongoDate(),
				"trabajador"=>$tmp['trabajador'],
				"programacion"=>array(
					"fecprog"=>new MongoDate(),
					"observ"=>""
				),
				"ejecucion"=>array(
					"fecini"=>new MongoDate(),
					"fecfin"=>new MongoDate(),
					"observ"=>"",
					'trabajador'=>$f->session->userDB
				),
				"propietario"=>$tmp['propietario'],
				"ocupante"=>$tmp['adjuntacion']['ocupante'],
				"espacio"=>$tmp['espacio'],
				"adjuntacion"=>true
			);
			$f->model('cm/oper')->params(array('data'=>$adjuntacion))->save('operacion');
		}
		if($tmp['inh']==true){
			foreach ($tmp['ocupantes'] as $ii=>$ocup){
	    		$inhumacion = array(
    				'ocupante_anterior'=>true,
					"fecreg"=>new MongoDate(),
					"trabajador"=>$tmp['trabajador'],
					"propietario"=>$tmp['propietario'],
					"ocupante"=>$ocup,
					"espacio"=>$tmp['espacio'],
					"inhumacion"=>$tmp['inhumacion'],
					"ejecucion"=>array(
						"fecini"=>'',
						"fecfin"=>'',
						"trabajador"=>$tmp['trabajador'],
						"observ"=>$tmp['inhumacion']['observ']
					)
				);
				if($tmp['inhumacion']['fecinh']!=''){
					$inhumacion['ejecucion']['fecini'] = $tmp['inhumacion']['fecinh'];
					$inhumacion['ejecucion']['fecfin'] = $tmp['inhumacion']['fecinh'];
				}
				$f->model('cm/oper')->params(array('data'=>$inhumacion))->save('operacion');
				$act = array(
					'_id'=>$ocup['_id']/*,
					'fecdef'=>new MongoDate(strtotime($f->request->data['inhumacion']['fecdef']))*/
				);
				if(isset($f->request->data['inhumacion']['fecdef']))
					$act['fecdef'] = new MongoDate(strtotime($f->request->data['inhumacion']['fecdef']));
				if(isset($ocup['fecnac']))
					$act['fecnac'] = new MongoDate(strtotime($ocup['fecnac']));
				$f->model('cm/ocup')->params($act)->save('estado_difunto');
			}
    	}
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; el registro de Ocupante Anterior en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_save_anular(){
		global $f;
		$oper = $f->model('cm/oper')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		switch ($f->request->data['oper']){
			case "concesion":
				$oper_nomb = 'Concesi&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom');
				$f->model('cm/espa')->params(array(
					'_id'=>$oper['espacio']['_id']->{'$id'},
					'ocupante'=>$oper['propietario']['_id']->{'$id'}
				))->delete('ocupante');
				$f->model('cm/espa')->params(array('_id'=>$oper['espacio']['_id'],'data'=>array('$set'=>array(
					'estado'=>'D'
				))))->save('custom');
				$f->model('cm/espa')->params(array('_id'=>$oper['espacio']['_id'],'data'=>array(
					'$unset'=>array('propietario'=>true)
				)))->save('custom');
				$f->model('cm/prop')->params(array(
					'espacio'=>$oper['espacio']['_id']->{'$id'},
					'_id'=>$oper['propietario']['_id']->{'$id'}
				))->delete('espacio');
				break;
			case "construccion":
				$oper_nomb = 'Construcci&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				break;
			case "asignacion":
				$oper_nomb = 'Asignaci&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				$f->model('cm/ocup')->params(array(
					'_id'=>$oper['ocupante']['_id']
				))->delete('rol');
				$f->model('cm/prop')->params(array(
					'_id'=>$oper['propietario']['_id']->{'$id'},
					'ocupante'=>$oper['ocupante']['_id']->{'$id'}
				))->delete('ocupante');
				$f->model('cm/espa')->params(array(
					'_id'=>$oper['espacio']['_id']->{'$id'},
					'ocupante'=>$oper['ocupante']['_id']->{'$id'}
				))->delete('ocupante');
				break;
			case "adjuntacion":
				$oper_nomb = 'Adjuntaci&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				break;
			case "traspaso":
				/*Segun Mar�a no hay*/
				break;
			case "inhumacion":
				$oper_nomb = 'Inhumaci&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				$f->model('cm/ocup')->params(array(
					'_id'=>$oper['ocupante']['_id'],
					'data'=>array('$set'=>array('roles.ocupante.difunto'=>false))
				))->save('custom');
				$f->model('cm/ocup')->params(array(
					'_id'=>$oper['ocupante']['_id'],
					'data'=>array('$unset'=>array('fecnac'=>true,'fecdef'=>true))
				))->save('custom');
				break;
			case "traslado":
				$oper_nomb = 'Traslado';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				break;
			case "colocacion":
				$oper_nomb = 'Colocaci&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				break;
			case "conversion":
				$oper_nomb = 'Conversi&oacute;n';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom');
				$conce_old = $f->model('cm/oper')->params(array('_id'=>$oper['conversion']['original']))->get('one')->items;
				$f->model('cm/oper')->params(array('_id'=>$conce_old['_id'],'data'=>array(
					'$unset'=>array(
						'concesion.propietario_original'=>1,
						'concesion.fecven_original'=>1,
						'concesion.original'=>1
					)
				)))->save('update');
				$act_conce = array(
					'$set'=>array(
						'concesion.condicion'=>'T',
						'concesion.fecven'=>$conce_old['concesion']['fecven']
					)
				);
				if($conce_old['propietario']['_id']!=$conce_old['concesion']['propietario_original']['_id'])
					$act_conce['$set']['propietario'] = $conce_old['concesion']['propietario_original'];
				$f->model('cm/oper')->params(array('_id'=>$conce_old['_id'],'data'=>$act_conce))->save('update');
				if($conce_old['propietario']['_id']!=$conce_old['concesion']['propietario_original']['_id']){
					$f->model('cm/espa')->params(array('_id'=>$oper['espacio']['_id'],'data'=>array(
						'$set'=>array('propietario'=>$conce_old['concesion']['propietario_original'])
					)))->save('custom');
					$f->model('cm/prop')->params(array(
						'espacio'=>$oper['espacio']['_id']->{'$id'},
						'_id'=>$conce_old['propietario']['_id']->{'$id'}
					))->delete('espacio');
					$f->model('mg/entidad')->params(array('_id'=>$oper['conversion']['propietario_original']['_id'],'data'=>array(
						'$set'=>array('roles.propietario.espacios'=>array(0=>$oper['espacio']))
					)))->save('custom');
					/*
					 * En caso de haber ocupantes, actualizarlos al antiguo propietario
					 */
					$espa = $f->model('cm/espa')->params(array('_id'=>$oper['espacio']['_id']))->get('one2')->items;
					if(isset($espa['ocupantes'])){
						$f->model('mg/entidad')->params(array(
							'_id'=>$ocup['_id'],
							'roles.ocupante.propietario'=>$oper['conversion']['propietario_original']
						))->save('update');
						$f->model('cm/prop')->params(array(
							'_id'=>$oper['conversion']['propietario_nuevo']['_id']->{'$id'},
							'ocupante'=>$ocup['_id']->{'$id'}
						))->delete('ocupante');
						$f->model('mg/entidad')->params(array(
							'_id'=>$oper['conversion']['propietario_original']['_id'],
							'data'=>array(
								'$push'=>array('roles.propietario.ocupantes'=>$ocup)
							)
						))->save('custom');
					}
				}
				break;
			case "traslado_ext":
				$oper_nomb = 'Traslado Externo';
				$f->model('cm/oper')->params(array('_id'=>$oper['_id'],'data'=>array('$set'=>array(
					'estado'=>'X',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza por operaci&oacute;n inv&aacute;lida.'
					)
				))))->save('custom')->items;
				$f->model('cm/ocup')->params(array(
					'_id'=>$oper['ocupante']['_id']
				))->delete('rol');
				$f->model('cm/prop')->params(array(
					'_id'=>$oper['propietario']['_id']->{'$id'},
					'ocupante'=>$oper['ocupante']['_id']->{'$id'}
				))->delete('ocupante');
				$f->model('cm/espa')->params(array(
					'_id'=>$oper['espacio']['_id']->{'$id'},
					'ocupante'=>$oper['ocupante']['_id']->{'$id'}
				))->delete('ocupante');
				break;
		}
		if(isset($oper['cuentas_cobrar'])){
			if(is_array($oper['cuentas_cobrar'])){
				foreach ($oper['cuentas_cobrar'] as $cta){
					$f->model('cj/cuen')->params(array('_id'=>$cta,'data'=>array(
						'estado'=>'X'
					)))->save('update');
				}
			}else{
				$f->model('cj/cuen')->params(array('_id'=>$oper['cuentas_cobrar'],'data'=>array(
					'estado'=>'X'
				)))->save('update');
			}
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la anulaci&oacute;n de <b>'.$oper_nomb.'</b> en el espacio <b>'.$oper['espacio']['nomb'].'</b>'
		))->save('insert');
		$f->response->print( "true" );
	}
	function execute_save_reno(){
		global $f;
		$data = $f->request->data;
		$oper = $f->model('cm/oper')->save('datos_conc');
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la <b>Renovaci&oacute;n</b> en el espacio <b>'.$f->request->data['espacio']['nomb'].'</b>'
		))->save('insert');
		$f->model('cm/oper')->params(array(
			'filter'=>array(
				'espacio._id'=>new MongoId($data['espacio']['_id']),
				'estado'=>'A'
			),
			'data'=>array('$set'=>array(
					'estado'=>'F',
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>'Se realiza para renovaci&oacute;n de concesi&oacute;n.'
					)
				)
			)
		))->save('filter');
		if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper->items['propietario'];
				$cuenta['espacio'] = $oper->items['espacio'];
				$cuenta['cliente'] = $oper->items['propietario'];
				$cuenta['autor'] = $oper->items['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper->items['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper->items['propietario'];
					$cu['espacio'] = $oper->items['espacio'];
					$cu['cliente'] = $oper->items['propietario'];
					$cu['autor'] = $oper->items['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper->items['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper->items['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
			}
		}
		$f->response->print('true');
	}
	function execute_save_conver(){
		global $f;
		$data = $f->request->data;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecut&oacute; la <b>Conversi&oacute;n de Concesi&oacute;n</b> en el espacio <b>'.$data['espacio']['nomb'].'</b>'
		))->save('insert');
		$data['propietario']['_id'] = new MongoId($data['propietario']['_id']);
		$data['espacio']['_id'] = new MongoId($data['espacio']['_id']);
		if(isset($data['conversion']['propietario_antiguo'])){
			$data['conversion']['propietario_antiguo']['_id'] = new MongoId($data['conversion']['propietario_antiguo']['_id']);
		}
		if(isset($data['conversion']['propietario_nuevo'])){
			$data['conversion']['propietario_nuevo']['_id'] = new MongoId($data['conversion']['propietario_nuevo']['_id']);
		}
		$conver = array(
			'trabajador'=>$f->session->userDB,
			'fecreg'=>new MongoDate(),
			'propietario'=>$data['propietario'],
			'espacio'=>$data['espacio'],
			'conversion'=>$data['conversion']
		);
		$conce = $f->model('cm/oper')->params(array('filter'=>array(
			'concesion.estado'=>'A',
			'espacio._id'=>$data['espacio']['_id'],
			'propietario._id'=>$data['propietario']['_id']
		)))->get('custom')->items[0];
		
		$conver['conversion']['original'] = $conce['_id'];
		$oper = $f->model('cm/oper')->params(array('data'=>$conver))->save('operacion')->items;
		/*
		 * Se actualiza la concesion anterior
		 */
		$f->model('cm/oper')->params(array('_id'=>$conce['_id'],'data'=>array(
			'$unset'=>array('concesion.fecven'=>1)
		)))->save('update');
		if(isset($conce['concesion']['fecven'])){
			$act_conce = array(
				'$set'=>array(
					'concesion.condicion'=>'P',
					'concesion.propietario_original'=>$data['propietario'],
					'concesion.fecven_original'=>$conce['concesion']['fecven'],
					'concesion.original'=>$conce['_id']
				)
			);
		}else{
			$act_conce = array(
				'$set'=>array(
					'concesion.condicion'=>'P',
					'concesion.propietario_original'=>$data['propietario'],
					'concesion.fecven_original'=>'',
					'concesion.original'=>$conce['_id']
				)
			);
		}
		if(isset($data['conversion']['propietario_nuevo']))
			$act_conce['$set']['propietario'] = $data['conversion']['propietario_nuevo'];
		$f->model('cm/oper')->params(array('_id'=>$conce['_id'],'data'=>$act_conce))->save('update');
		/*
		 * En caso de haber nuevo propietario, actualizarlo y actualizar el espacio
		 */
		if(isset($data['conversion']['propietario_nuevo'])){
			$f->model('cm/espa')->params(array('_id'=>$data['espacio']['_id'],'data'=>array(
				'$set'=>array('propietario'=>$data['conversion']['propietario_nuevo'])
			)))->save('custom');
			$f->model('cm/prop')->params(array(
				'espacio'=>$data['espacio']['_id']->{'$id'},
				'_id'=>$data['propietario']['_id']->{'$id'}
			))->delete('espacio');
			$f->model('mg/entidad')->params(array('_id'=>$data['conversion']['propietario_nuevo']['_id'],'data'=>array(
				'$set'=>array('roles.propietario.espacios'=>array(0=>$data['espacio']))
			)))->save('custom');
			/*
			 * En caso de haber ocupantes en el espacio, actualizarlos al nuevo propietario
			 */
			$espa = $f->model('cm/espa')->params(array('_id'=>$data['espacio']['_id']))->get('one2')->items;
			if(isset($espa['ocupantes'])){
				foreach ($espa['ocupantes'] as $ocup){
					$f->model('mg/entidad')->params(array(
						'_id'=>$ocup['_id'],
						'roles.ocupante.propietario'=>$data['propietario']
					))->save('update');
					$f->model('cm/prop')->params(array(
						'_id'=>$data['propietario']['_id']->{'$id'},
						'ocupante'=>$ocup['_id']->{'$id'}
					))->delete('ocupante');
					$f->model('mg/entidad')->params(array(
						'_id'=>$data['propietario']['_id'],
						'data'=>array(
							'$push'=>array('roles.propietario.ocupantes'=>$ocup)
						)
					))->save('custom');
				}
			}
		}
		/*
		 * Se generan las cuentas por cobrar
		 */
		if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper['propietario'];
				$cuenta['espacio'] = $oper['espacio'];
				$cuenta['cliente'] = $oper['propietario'];
				$cuenta['autor'] = $oper['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper['propietario'];
					$cu['espacio'] = $oper['espacio'];
					$cu['cliente'] = $oper['propietario'];
					$cu['autor'] = $oper['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
			}
		}
		$f->response->print('true');
	}
	function execute_save_tras_ext(){
		global $f;
		$data = $f->request->data;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se cre&oacute; la operaci&oacute;n de <b>Traslado Externo</b> en el espacio <b>'.$data['espacio']['nomb'].'</b>'
		))->save('insert');
		$data['propietario']['_id'] = new MongoId($data['propietario']['_id']);
		$data['espacio']['_id'] = new MongoId($data['espacio']['_id']);
		$data['ocupante']['_id'] = new MongoId($data['ocupante']['_id']);
		if(isset($data['traslado_ext']['destino'])){
			$data['traslado_ext']['destino']['_id'] = new MongoId($data['traslado_ext']['destino']['_id']);
		}
		if(isset($data['traslado_ext']['origen']['cementerio'])){
			$data['traslado_ext']['origen']['cementerio']['_id'] = new MongoId($data['traslado_ext']['origen']['cementerio']['_id']);
		}
		if(isset($data['programacion']['fecprog'])){
			$data['programacion']['fecprog'] = new MongoDate(strtotime($data['programacion']['fecprog']));
		}
		$tras_ext = array(
			'trabajador'=>$f->session->userDB,
			'fecreg'=>new MongoDate(),
			'propietario'=>$data['propietario'],
			'espacio'=>$data['espacio'],
			'programacion'=>$data['programacion'],
			'traslado_ext'=>$data['traslado_ext']
		);
		$oper = $f->model('cm/oper')->params(array('data'=>$tras_ext))->save('operacion')->items;
		/*
		 * Se actualiza el ocupante con su espacio y propietario
		 */
		$ocupante = array(
			"_id"=>$f->request->data['ocupante']['_id'],
			"espacio"=>$f->request->data['espacio'],
			"propietario"=>$f->request->data['propietario']
		);
		$f->model('cm/ocup')->params(array('data'=>$ocupante))->save('update');
		/*
		 * Se actualiza el propietario con el ocupante
		 */
		$propietario = array(
			"_id"=>$f->request->data['propietario']['_id'],
			"ocupante"=>$f->request->data['ocupante']
		);
		$f->model('cm/prop')->params(array('data'=>$propietario))->save('update');
		/*
		 * se actualiza el espacio con el ocupante
		 */
		$espa = array(
			"_id"=>$f->request->data['espacio']['_id'],
			"ocupante"=>$f->request->data['ocupante']
		);
		$f->model('cm/espa')->params(array('data'=>$espa))->save('update_ocup');
		/*
		 * Se generan las cuentas por cobrar
		 */
		if(isset($f->request->data['cuenta_cobrar'])){
			$cuenta = $data['cuenta_cobrar'];
			if(isset($cuenta['servicio'])){
				$cuenta['fecreg'] = new MongoDate();
				$cuenta['estado'] = 'P';
				$cuenta['modulo'] = 'CM';
				$cuenta['propietario'] = $oper['propietario'];
				$cuenta['espacio'] = $oper['espacio'];
				$cuenta['cliente'] = $oper['propietario'];
				$cuenta['autor'] = $oper['trabajador'];
				if(isset($cuenta['servicio']['_id'])) $cuenta['servicio']['_id'] = new MongoId($cuenta['servicio']['_id']);
				if(isset($cuenta['servicio']['organizacion']['_id'])) $cuenta['servicio']['organizacion']['_id'] = new MongoId($cuenta['servicio']['organizacion']['_id']);
				if(isset($cuenta['fecven'])) $cuenta['fecven'] = new MongoDate(strtotime($cuenta['fecven']));
				foreach ($cuenta['conceptos'] as $j=>$con){
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['_id'])) $cuenta['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
					if(isset($con['concepto']['clasificador']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
					if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
					if(isset($con['concepto']['cuenta']['_id'])) $cuenta['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
					if(isset($con['saldo'])) $cuenta['conceptos'][$j]['saldo'] = floatval($con['saldo']);
					if(isset($con['monto'])) $cuenta['conceptos'][$j]['monto'] = floatval($con['monto']);
				}
				if(isset($cuenta['saldo'])) $cuenta['saldo'] = floatval($cuenta['saldo']);
				if(isset($cuenta['monto'])) $cuenta['monto'] = floatval($cuenta['monto']);
				$cuenta['operacion'] = $oper['_id'];
				$cuen = $f->model('cj/cuen')->params(array('data'=>$cuenta))->save('insert');
				$f->model('cm/oper')->params(array(
					'_id'=>$oper['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuen->items['_id']))
				))->save('update');
			}else{
				$cuentas = array();
				foreach ($cuenta as $i=>$cu){
					$cu['fecreg'] = new MongoDate();
					$cu['estado'] = 'P';
					$cu['modulo'] = 'CM';
					$cu['propietario'] = $oper['propietario'];
					$cu['espacio'] = $oper['espacio'];
					$cu['cliente'] = $oper['propietario'];
					$cu['autor'] = $oper['trabajador'];
					if(isset($cu['servicio']['_id'])) $cu['servicio']['_id'] = new MongoId($cu['servicio']['_id']);
					if(isset($cu['servicio']['organizacion']['_id'])) $cu['servicio']['organizacion']['_id'] = new MongoId($cu['servicio']['organizacion']['_id']);
					if(isset($cu['fecven'])) $cu['fecven'] = new MongoDate(strtotime($cu['fecven']));
					foreach ($cu['conceptos'] as $j=>$con){
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['_id'])) $cu['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
						if(isset($con['concepto']['clasificador']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
						if(isset($con['concepto']['clasificador']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
						if(isset($con['concepto']['cuenta']['_id'])) $cu['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
						if(isset($con['saldo'])) $cu['conceptos'][$j]['saldo'] = floatval($con['saldo']);
						if(isset($con['monto'])) $cu['conceptos'][$j]['monto'] = floatval($con['monto']);
					}
					if(isset($cu['saldo'])) $cu['saldo'] = floatval($cu['saldo']);
					if(isset($cu['monto'])) $cu['monto'] = floatval($cu['monto']);
					$cu['operacion'] = $oper['_id'];
					$cuen = $f->model('cj/cuen')->params(array('data'=>$cu))->save('insert');
					$cuentas[] = $cuen->items['_id'];
				}
				$f->model('cm/oper')->params(array(
					'_id'=>$oper['_id'],
					'data'=>array('$set'=>array('cuentas_cobrar'=>$cuentas,'cuentas_cobrar_mul'=>true))
				))->save('update');
			}
		}
		$f->response->print('true');
	}
	function execute_save_edit_inhu(){
		global $f;
		$data = $f->request->data;
		if(isset($data['funeraria']['_id']))
			$data['funeraria']['_id'] = new MongoId($data['funeraria']['_id']);
		if(isset($data['municipalidad']['_id']))
			$data['municipalidad']['_id'] = new MongoId($data['municipalidad']['_id']);
		$tmp = array();
		foreach ($data as $key => $value) {
			if($key!='_id')
				$tmp['inhumacion.'.$key] = $value;
		}
		$f->model('cm/oper')->params(array('_id'=>new MongoId($data['_id']),'data'=>array('$set'=>$tmp)))->save('custom');
		$f->response->print('true');
	}













	function execute_save_relacion(){
		global $f;
		$tmp = $f->request->data;
		$tmp['trabajador'] = $f->session->userDB;
		if(isset($tmp['inhumacion'])){
			if(isset($tmp['inhumacion']['funeraria']))
				$tmp['inhumacion']['funeraria']['_id'] = new MongoId($tmp['inhumacion']['funeraria']['_id']);
			if(isset($tmp['inhumacion']['municipalidad']))
				$tmp['inhumacion']['municipalidad']['_id'] = new MongoId($tmp['inhumacion']['municipalidad']['_id']);
			if(isset($tmp['inhumacion']['fecdef']))
				if($tmp['inhumacion']['fecdef']!='')
					$tmp['inhumacion']['fecdef'] = new MongoDate(strtotime($tmp['inhumacion']['fecdef']));
			if(isset($tmp['inhumacion']['fecinh']))
				if($tmp['inhumacion']['fecinh']!='')
					$tmp['inhumacion']['fecinh'] = new MongoDate(strtotime($tmp['inhumacion']['fecinh']));
		}
		$tmp['ocupante']['_id'] = new MongoId($tmp['ocupante']['_id']);
		$tmp_ocup = $f->model('mg/entidad')->params(array('_id'=>$tmp['ocupante']['_id']))->get('one')->items;
		if(isset($tmp_ocup['roles'])){
			$f->model('mg/entidad')->params(array(
				'_id'=>$tmp['ocupante']['_id'],
				'data'=>array(
					'inhumacion'=>$tmp['inhumacion'],
					'roles.ocupante'=>array(
						'difunto'=>true
					)
				)
			))->save('update');
		}else{
			$f->model('mg/entidad')->params(array(
				'_id'=>$tmp['ocupante']['_id'],
				'data'=>array(
					'inhumacion'=>$tmp['inhumacion'],
					'roles'=>array('ocupante'=>array(
						'difunto'=>true
					))
				)
			))->save('update');
		}
		if($tmp['espacio']!=null){
			$tmp['espacio']['_id'] = new MongoId($tmp['espacio']['_id']);
			//Asignacion
			$f->model('cm/ocup')->params(array('data'=>array(
				"_id"=>$tmp['ocupante']['_id']->{'$id'},
				"espacio"=>$f->request->data['espacio']
			)))->save('update');
			$f->model('cm/espa')->params(array('data'=>array(
				"_id"=>$f->request->data['espacio']['_id'],
				"ocupante"=>$ocup
			)))->save('update_ocup_conId');
			$act = array(
				'_id'=>$tmp['ocupante']['_id']/*,
				'fecdef'=>new MongoDate(strtotime($f->request->data['inhumacion']['fecdef']))*/
			);
			if(isset($f->request->data['inhumacion']['fecdef']))
				$act['fecdef'] = new MongoDate(strtotime($f->request->data['inhumacion']['fecdef']));
			if(isset($ocup['fecnac']))
				$act['fecnac'] = new MongoDate(strtotime($tmp['ocupante']['fecnac']));
			$f->model('cm/ocup')->params($act)->save('estado_difunto');
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se ejecuto el registro del Difunto <b>'.$tmp['ocupante']['nomb'].' '.$tmp['ocupante']['appat'].' '.$tmp['ocupante']['apmat'].'</b> en el sistema'
		))->save('insert');
    	$f->response->print( "true" );
	}

















	function execute_save_hist(){
		global $f;
		$data = $f->request->data;
		$data['trabajador'] = $f->session->userDB;
		$data["fecreg"] = new MongoDate();
		$data['executed'] = false;
		if($data["fecoper"]!="")
			$data["fecoper"] = new MongoDate(strtotime($data["fecoper"]));
		if(isset($data["espacio"])){
			if(isset($data["espacio"]['_id'])){
				$data["espacio"]["_id"] = new MongoId($data["espacio"]["_id"]);
				$data["espacio"] = $f->model('cm/espa')->params(array('_id'=>$data["espacio"]["_id"]))->get('one2')->items;
			}
		}
		if(isset($data["espacio_dest"])){
			if(isset($data["espacio_dest"]['_id'])){
				$data["espacio_dest"]["_id"] = new MongoId($data["espacio_dest"]["_id"]);
				$data["espacio_dest"] = $f->model('cm/espa')->params(array('_id'=>$data["espacio_dest"]["_id"]))->get('one2')->items;
			}
		}
		if(isset($data["fecdef"])){
			if($data["fecdef"]!='')
				$data["fecdef"] = new MongoDate(strtotime($data["fecdef"]));
		}
		if(!isset($f->request->data['_id'])){
			$f->model('cm/hope')->params(array('data'=>$data))->save('insert');
			$id = $data['_id'];
		}else{
			$f->model('cm/hope')->params(array(
				'_id'=>new MongoId($f->request->data['_id']),
				'data'=>$data
			))->save('update');
			$id = new MongoId($f->request->data['_id']);
		}
		$hist_t = $f->model('cm/hope')->params(array('_id'=>$id))->get('one')->items;
		$espacio = $f->model('cm/espa')->params(array('_id'=>$hist_t['espacio']['_id']))->get('one2')->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'CM',
			'bandeja'=>'Operaciones de Cementerio',
			'descr'=>'Se agreg&oacute; un <b>Registro de H&iacute;storico</b> en el espacio <b>'.$espacio['nomb'].'</b>'
		))->save('insert');
		$f->response->print("true");
	}
	function execute_eliminar(){
		global $f;
		$item = $f->model('cm/oper')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one2')->items;
		$item['feceli'] = new MongoDate();
		$item['coleccion'] = 'cm_operaciones';
		$item['trabajador_delete'] = $f->session->userDB;
		$f->datastore->temp_del->insert($item);
		$f->datastore->cm_operaciones->remove(array('_id'=>$item['_id']));
		$f->response->print(true);
	}
	function execute_new_reasig(){
		global $f;
		$f->response->view("cm/reasig.new");
	}
	function execute_new_asig(){
		global $f;
		$f->response->view("cm/asig.new");
	}
	function execute_new_inhu(){
		global $f;
		$f->response->view("cm/inhu.new");
	}
	function execute_new_cons(){
		global $f;
		$f->response->view("cm/cons.new");
	}
	function execute_new_colo(){
		global $f;
		$f->response->view("cm/colo.new");
	}
	function execute_new_tras(){
		global $f;
		$f->response->view("cm/tras.new");
	}
	function execute_anul_oper(){
		global $f;
		$f->response->view('cm/oper.anul');
	}
	function execute_anul_conce(){
		global $f;
		$f->response->view("cm/oper.anul_conce");
	}
	function execute_consult(){
		global $f;
		$f->response->view("cm/oper.consul");
	}
	function execute_exe_cons(){
		global $f;
		$f->response->view("cm/oper.execu.cons");
	}
	function execute_ocup_ante(){
		global $f;
		$f->response->view( "cm/oper.ocup_ante" );
	}
	function execute_ejecutar(){
		global $f;
		$f->response->view( "cm/oper.ejecutar" );
	}
	function execute_trasp(){
		global $f;
		$f->response->view( "cm/oper.traspaso" );
	}
	function execute_reno(){
		global $f;
		$f->response->view( "cm/oper.reno" );
	}
	function execute_hist(){
		global $f;
		$f->response->view( "cm/oper.hist.edit" );
	}
	function execute_tras_ext(){
		global $f;
		$f->response->view( "cm/oper.tras_ext" );
	}
	function execute_relacion(){
		global $f;
		$f->response->view( "cm/oper.ocupantes" );
	}
	function execute_edit_inhu(){
		global $f;
		$f->response->view('cm/oper.inhu.edit');
	}
}
?>
