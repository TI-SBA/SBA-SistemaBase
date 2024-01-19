<?php
class Controller_ho_reci extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['modulo']))
			if($f->request->data['modulo']!='')
				$params['modulo'] = $f->request->data['modulo'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/comp")->params($params)->get("lista_mh") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_recidia(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data["fecini"]) && isset($data["fecfin"])){
			$fecini = strtotime($data["fecini"].' 00:00:00');
			$fecfin = strtotime($data["fecfin"].' 23:59:59');
			$params['$and'] = array(
				array('fecpag'=>array('$gte'=>new MongoDate($fecini))),
				array('fecpag'=>array('$lte'=>new MongoDate($fecfin)))
				);
			}
		$items = $f->model("ho/hosp")->params($params)->get("recidia")->items;
		$f->response->view("ho/recibos_dia.php",array('hospi'=>$items));
	}

	function execute_next_num(){
		global $f;
		$last = $f->model('cj/comp')->params(array(
			'tipo'=>'R',
			'serie'=>'003',
			'modulo'=>'MH'
		))->get('num_mod')->items;
		if($last!=null){
			$last++;
		}else{
			$last = $f->model('cj/talo')->params(array(
				'filter'=>array('modulo'=>$f->request->data['modulo'])
			))->get('custom')->items;
			$last = intval($last[0]['actual'])+1;
		}
		$f->response->json(array('num'=>$last));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['costo']))
			$data['costo'] = floatval($data['costo']);
		if(isset($data['cliente']))
			$data['cliente']['_id'] = new MongoId($data['cliente']['_id']);
		foreach ($data['items'] as $key => $item) {
			if(isset($item['servicio']))
				$data['items'][$key]['servicio']['_id'] = new MongoId($item['servicio']['_id']);
			if(isset($item['cuenta']))
				$data['items'][$key]['cuenta']['_id'] = new MongoId($item['cuenta']['_id']);
			if(isset($item['tari']))
				$data['items'][$key]['tari']['_id'] = new MongoId($item['tari']['_id']);
			if(isset($data['servicio']))
				$data['items'][$key]['costo'] = floatval($data['costo']);
		}
		if(isset($data['servicio']))
			$data['servicio']['_id'] = new MongoId($data['servicio']['_id']);
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['total']))
			$data['total'] = floatval($data['total']);
		$comp = array(
			'modulo'=>$data['modulo'],
			'estado'=>'R',
			'tipo'=>'R',
			'serie'=>'003',
			'num'=>intval($data['num']),
			'cliente'=>$data['cliente'],
			'observ'=>$data['observ'],
			'moneda'=>'S',
			'total'=>floatval($data['total']),
			'igv'=>0,
			'subtotal'=>floatval($data['total']),
			'fecreal'=>new MongoDate(),
			'fecreg'=>new MongoDate(),
			'autor'=>$f->session->userDB,
			'items'=>$data['items']
		);
		if(isset($data['fecemi'])){
			if($data['fecemi']!=''){
				if(strtotime($data['fecemi'])>0){
					$comp['fecreg'] = new MongoDate(strtotime($data['fecemi']));
				}
			}
		}
		$model = $f->model("cj/comp")->params(array('data'=>$comp))->save("insert")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'HO',
			'bandeja'=>'Tarifa de Hospitalizaciones',
			'descr'=>'Se creó el Recibo de Caja de Moises Heresi <b>'.$data['num'].'</b>.'
		))->save('insert');
		$f->response->json($model);
	}
	function execute_anular(){
		global $f;
		$data = $f->request->data;
		$f->model("cj/comp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'X'
		)))->save("update");
		$comprobante = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		if($comprobante!=null){
			if(isset($comprobante['hospitalizacion'])){
				$hospitalizacion = $f->model('mh/hospi')->params(array('_id'=>$comprobante['hospitalizacion']['_id']))->get('one')->items;
				if($hospitalizacion!=null){
					$f->model('mh/hospi')->params(array('_id'=>$hospitalizacion['_id']),array('$unset'=>array('recibo'=>1,'recibos'=>1)))->save('custom');	
				}
			}
		}
		$f->response->print('true');
	}
	function execute_eliminar(){
		global $f;
		$data = $f->request->data;
		$item = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		if($item!=null){
			if($item['estado']=='X'){
				$item['feceli'] = new MongoDate();
				$item['coleccion'] = 'cj_comprobantes';
				$item['trabajador_delete'] = $f->session->userDB;
				$f->datastore->temp_del->insert($item);
				$f->datastore->cj_comprobantes->remove(array('_id'=>$item['_id']));
				$f->response->print(true);	
			}else{
				$f->response->print('false');
			}
		}
	}
	function execute_edit(){
		global $f;
		$f->response->view("ho/reci.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ho/reci.details");
	}
	function execute_tari(){
		global $f;
		$f->response->view("ho/reci.tari");
	}
	function execute_generar(){
		global $f;
		$f->response->view("ho/reci.generar");
	}
	function execute_print(){
		global $f;
		$comp = $f->model("cj/comp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($comp['cliente']['_id']))
		$comp['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$comp['cliente']['_id']))->get("enti")->items;
        $comp['paciente'] = $f->model("mh/paci")->params(array("filter"=>array("paciente._id"=>$comp['cliente']['_id'])))->get("all")->items;
      	if($comp['paciente']!=null){
        	if(count($comp['paciente'])>0) $comp['paciente'] = $comp['paciente'][0];
        }
		if(isset($comp['items'])){
			foreach($comp["items"] as $i=>$item){
				if(isset($item["cuenta_cobrar"])){
					$comp["items"][$i]["cuenta_cobrar"] = $f->model("cj/cuen")->params(array("_id"=>$item["cuenta_cobrar"]["_id"]))->get("one")->items;
					//$comp["items"][$i]["cuenta_cobrar"]["operacion"] = $f->model("in/oper")->params(array("_id"=>$comp["items"][$i]["cuenta_cobrar"]["operacion"]))->get("one")->items;
				}
			}
		}
		$print = false;
		if(isset($f->request->data['print'])) $print = true;
		if(isset($f->request->data['xls'])){
			switch ($comp['tipo']) {
				case 'B':
					$f->response->view("ho/comp.bole.xls",array("data"=>$comp));
					break;
				case 'F':
					$f->response->view("ho/comp.fact.xls",array("data"=>$comp));
					break;
				case 'R':
					$f->response->view("ho/comp.reci.xls",array("data"=>$comp));
					break;
			}
		}else{
			switch ($comp['tipo']) {
				case 'B':
					$f->response->view("ho/comp.bole.pdf",array("items"=>$comp,'print'=>$print));
					break;
				case 'F':
					$f->response->view("ho/comp.fact.pdf",array("items"=>$comp,'print'=>$print));
					break;
				case 'R':
					$f->response->view("ho/comp.reci.pdf",array("items"=>$comp,'print'=>$print));
					break;
			}
		}
	}
}
?>