<?php
class Controller_cm_terr extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cm/terr")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		if(isset($f->request->data['fields'])) $fields = array('nomb'=>true);
		else $fields = array();
		$model = $f->model('cm/terr')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$items = $f->model("cm/terr")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_num(){
		global $f;
		$caja = '528fa3fea4b5c36405000003';
		$model = $f->model("cj/talo")->params(array("_id"=>new MongoId($caja)))->get("one");
		if (!is_null($model->items)) {
            foreach ($model->items as $i=>$item) {
                /*$cod = $f->model("cj/comp")->params(array(
                    'tipo'=>$item['tipo'],
                    'serie'=>$item['serie'],
                    'caja'=>$item['caja']['_id']
                ))->get("num");
                if($cod->items==null) $cod->items=$model->items[$i]['actual'];
                else $cod->items = intval($cod->items);
                $model->items[$i]['actual'] = $cod->items;
                */
            }
        }
		$f->response->json($model->items);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['fecve'])){
			$data['fecve']=new MongoDate(strtotime($data['fecve']));
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = "H";
			$f->model("cm/terr")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array('modulo'=>'cm','bandeja'=>'Circuito del Terror','descr'=>'Se cre&oacute; la fecha <b>'))->save('insert');
		}else{
			$vari = $f->model("cm/terr")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['fecven'])){
				$data['fecven']=new MongoDate(strtotime($data['fecven']));
			}
			if(isset($data['cliente'])){
				$data['cliente']['_id'] = new MongoId($data['cliente']['_id']);
			}

			/*$conceptos = array(
				'conceptos' => array(
					array(
						"concepto" => array(
							"_id" => new MongoId('6356cd763e603727628b4567'),
							"nomb" => " Entrada para el evento “Mitos y Leyendas” General Mitos "
						)
					)
				)
			);*/
			if(isset($data['items'])){
				if (isset($data['items'][0]['conceptos'])) {
					foreach ($data['items'][0]['conceptos'] as $i => &$concepto) {
						$concepto['concepto']['_id'] = new MongoId($concepto['concepto']['_id']);
						$concepto['monto'] = floatval($data['total']);
					}
				}
	//			die();
				if (isset($data['items'][0]['cuenta_cobrar'])) {
					$data['items'][0]['cuenta_cobrar']['_id'] = '-';
					$data['items'][0]['cuenta_cobrar']['servicio']['_id'] = new MongoId($data['items'][0]['cuenta_cobrar']['servicio']['_id']);
					$data['items'][0]['cuenta_cobrar']['servicio']['organizacion']['_id'] = new MongoId($data['items'][0]['cuenta_cobrar']['servicio']['organizacion']['_id']);
					$data['items'][0]['cuenta_cobrar']['cuenta']['_id'] = new MongoId($data['items'][0]['cuenta_cobrar']['cuenta']['_id']);
				}
				
			}
			if(isset($data['caja'])){
				$data['caja']['_id'] = new MongoId($data['caja']['_id']);
				$data['caja']['local']['_id'] = new MongoId($data['caja']['local']['_id']);
			}
			if(isset($data['num'])){
				$data['num'] = floatval($data['num']);

			}
			if(isset($data['total'])){
				$data['total'] = floatval($data['total']);
				$efectivo = array(
					array(
						'moneda' => 'S',
						'monto' => $data['total']
					),
					array(
						'moneda' => 'D',
						'monto' => 0
					)
				);
				  
				$data['efectivos'] = $efectivo;
			}
		$trabajador = $f->session->userDB;
        if ($trabajador['_id']==new MongoId('57f3e8eb8e7358b007000042')) {
            $trabajador = array(
                '_id'=>new MongoId('56fd3c148e73584c07000062'),
                 "tipo_enti"=>"P",
                 "nomb"=>"PEDRO PERCY",
                 "apmat"=>"REVILLA",
                 "appat"=>"AMESQUITA",
                 "cargo"=>array(
                   "funcion"=>"APOYO ADMINISTRATIVO",
                   "organizacion"=>array(
                     "_id"=>new MongoId("51a50f0f4d4a13c409000013"),
                     "nomb"=>"Unidad de Cementerio y Servicios Funerarios",
                     "componente"=>array(
                       "_id"=>new MongoId("51e99d7a4d4a13c404000016"),
                       "nomb"=>"SERVICIOS FUNERARIOS Y DE CEMENTERIO",
                       "cod"=>"001"
                    ),
                     "actividad"=>array(
                       "_id"=>new MongoId("51e996044d4a13440a00000e"),
                       "nomb"=>"SERVICIOS FUNERARIOS Y DE CEMENTERIO",
                       "cod"=>"5001194"
                    )
                  )
                )
            );
        }
			$evento = array(
				array(
					'_id' => new MongoId($data['_id']),
					'fecve' => $data['fecve']
					)
				);
			$data['evento'] = $evento;
			if(!isset($data['autor'])){
				$data['autor'] = $trabajador;
			}
			unset($data['_id']);
			$comprobante = $f->model('cj/comp')->params(array('data'=>$data))->save('insert')->items;
			$f->model('cj/talo')->params(array(
				'tipo'=>$data['tipo'],
				'serie'=>$data['serie'],
				'num'=>floatval($data['num']),
				'caja'=>$data['caja']['_id']
			))->save('num');
			if(!isset($data['comprobante'])){
				$data['comprobante '] = new MongoId($comprobante['_id']);
			}
			if(!isset($comprobante['fecreg'])){
				$comprobante['fecreg'] = $data['fecven'];
			}
			$cuenta = $f->model("cj/cuen")->params(array('data'=>$data))->save("insert")->items;
			
			$cuenta_cobrar_id = $comprobante['items'][0]['cuenta_cobrar']['_id'];
			$cuenta_cobrar_id = $cuenta['_id'];
			$comprobante['items'][0]['cuenta_cobrar']['_id'] = $cuenta_cobrar_id;
			$f->model("cj/comp")->params(array('_id'=> new MongoId($comprobante['_id']),'data'=>array(
				'items.0.cuenta_cobrar._id'=>$comprobante['items'][0]['cuenta_cobrar']['_id'],
				'fecreg' => $comprobante['fecreg']
				)))->save("update");
		}
		$f->response->json($comprobante);
	}
	function execute_details(){
		global $f;
		$f->response->view("cm/terr.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("cm/terr.edit");
	}
	function execute_ticket(){
		global $f;
		$f->response->view("cm/ticket.edit");
	}
}
?>