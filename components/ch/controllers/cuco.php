<?php
class Controller_ch_cuco extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ch/cuco")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ch/cuco")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_save_insert(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		$response=array(
			'status' => "error", 
			'mensaje' => "Error desconocido", 
			'data' => array(),
		);

		try{
			$a_insert=array(
				'estado' => '',
				'centro' => '',
				'paciente' => array(),
				'importe_total' => 0,
				'hospitalizacion' =>  "",
				'hist_cli' => '',
				'pabe' => '',
				'tipo_hosp' => '',
				'diag' => '',
				'fecfin' => "",
				'categoria' => '',
				'modalidad' => '',
				'cant' => '',
				'fecpag' => "",
				'recibos' => array(),
				'recibo_' => array(),
				'deuda' =>  0,
				'monto_hospitalizacion' => 0,
			);

			/*comprobar comprobante*/
			if(!isset($data['_id'])) throw new Exception("Error: no se recibio un _id de caja comprobante");
			$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(is_null($comp)) throw new Exception("Error: no se encontro el comprobante con el _id recibido");

			if(!isset($comp['tipo'])) throw new Exception("Error: no se encontro el campo tipo en el comprobante");
			if(!isset($comp['serie'])) throw new Exception("Error: no se encontro el campo serie en el comprobante");
			if(!isset($comp['num'])) throw new Exception("Error: no se encontro el campo num en el comprobante");
			if(!isset($comp['total'])) throw new Exception("Error: no se encontro el campo total en el comprobante");


			$a_insert_paciente= array(
				'_id' => $comp['_id'], 
				'tipo' => $comp['tipo'],
				'serie' => $comp['serie'],
				'numero' => $comp['num'],
				'fecini' => new MongoDate(),
       			'fecfin' => new MongoDate(),
       			'importe' => $comp['total'],
			);
			array_push($a_insert['paciente'], $a_insert_paciente);

			/*comprobar entidad*/
			if(!isset($comp['cliente']['_id'])) throw new Exception("Error: no se encontro el _id de cliente con la caja comprobante");
			$enti = $f->model("mg/enti")->params(array("_id"=>$enti['cliente']['_id']))->get("one")->items;
			if(is_null($enti)) throw new Exception("Error: no se encontro a el paciente como entidad");
			if(!isset($enti['roles']['paciente'])) throw new Exception("Error: el cliente no es un paciente");

			/*de hospitalización*/
			$hospi = $f->model("ho/hosp")->params(array("_id"=>$enti['cliente']['_id']))->get("one_entidad")->items;
			if(is_null($hospi)) throw new Exception("Error: no se encontro una ficha de hospitalizacion al paciente");
			//if(!isset($hospi['_id'])) throw new Exception("Error: no se encontro el campo de paciente _id");
			//$frontal = $f->model("ch/paci")->params(array("_id"=>$paciente['_id']))->get("one")->items;
			//if(is_null($frontal)) throw new Exception("Error: no se encontro la ficha frontal del paciente hospitalizado");

			if(!isset($hospi['categoria'])) throw new Exception("Error: no se agrego categoria en la hospitalizacion");
			if(!isset($hospi['hist_cli'])) throw new Exception("Error: no se encontro el campo historia clinica");
			if(!isset($hospi['pabe'])) throw new Exception("Error: no se encontro el campo pabellon");
			if(!isset($hospi['tipo_hosp'])) throw new Exception("Error: no se encontro el campo tipo de hospit");
			if(!isset($hospi['modalidad'])) throw new Exception("Error: no se encontro el campo modaliad");
			if(!isset($hospi['cant'])) throw new Exception("Error: no se encontro el campo cant");

			$a_insert['paciente']=$hospi['paciente'];
			$a_insert['categoria']=$hospi['categoria'];
			$a_insert['hospitalizacion']=$hospi['_id'];
			$a_insert['hist_cli']=$hospi['hist_cli'];
			$a_insert['pabe']=$hospi['pabe'];
			$a_insert['tipo_hosp']=$hospi['tipo_hosp'];
			$a_insert['modalidad']=$hospi['modalidad'];
			$a_insert['cant']=$hospi['cant'];

			/*monto hospitalización*/
			$tari = $f->model("ho/tari")->params(array(
				"tipo_hosp"=>$hosp['tipo_hosp'],
				"categoria"=>$hosp['categoria'],
			))->get("por_categoria_tipo")->items;
			if(is_null($tari)) throw new Exception("Error: no se encontro las categorias de lo que se desea");
			if(!isset($tari['mensual'])) throw new Exception("Error: no se encontro el monto mensual en dicha categoria adecuada para el paciente");
			$a_insert['monto_hospitalizacion']=$tari['mensual'];
			$a_insert['importe_total']+=$comp['total'];
			$a_insert['deuda']=$a_insert['monto_hospitalizacion']-$a_insert['importe_total'];

			$hospi = $f->model("ch/cuco")->params($a_insert)->save("insert")->items;

			$response['status'] = 'success';
			$response['message'] = 'se envió el mensaje correctamente';
			$response['data'] = $hospi;

		}
		catch(Exception $e){
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
			$response['data'] = $data;
		}
		$f->response->json($response);
	}
	function execute_edit(){
		global $f;
		$f->response->view("ch/conpa.move");
	}
	/*
	function execute_details(){
		global $f;
		$f->response->view("ch/cuco.details");
	}
	*/
	/*
	function execute_delete(){
		global $f;
		$f->model('ch/cuco')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('paho');
		$f->response->print("true");
	}
	*/
	/*
	function execute_print(){
		global $f;
		$pahotalizacion = $f->model('ch/cuco')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
		$f->response->view("ch/cuco.print",array('pahotalizacion'=>$pahotalizacion));
	}
	*/
}
?>