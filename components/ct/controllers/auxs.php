<?php
class Controller_ct_auxs extends Controller {
	function execute_lista(){
		global $f;
		$data = $f->request->data;
		if(isset($data['programa'])) $data['programa'] = new MongoId($data['programa']);
		if(isset($data['inmueble'])) $data['inmueble'] = new MongoId($data['inmueble']);
		if(isset($data['cusu'])) $data['cusu'] = new MongoId($data['cusu']);

		$params = array();
		if(isset($data['ano'])){
			$params['ano'] = floatval($data['ano']);
		}else{
			$params['ano'] = '--';
		}
		if(isset($data['mes'])){
			$params['mes'] = floatval($data['mes']);	
		}else{
			$params['mes'] = '--';
		}
		if(isset($data['cusu'])){
			$params['cuenta'] = $data['cusu'];	
		}else{
			$params['cuenta'] = '--';
		}
		//print_R($params);
		if(isset($data['programa'])) $params['programa'] = $data['programa'];
		if(isset($data['inmueble'])) $params['inmueble'] = $data['inmueble'];
		//print_r($params);
		$saldo = $f->model('ct/saux')->params($params)->get('saldo')->items;
		$items = $f->model("ct/auxs")->params($params)->get("lista")->items;
		$f->response->json(array(
			'saldo'=>$saldo,
			'items'=>$items
		));
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("ct/auxs")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ct/auxs')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/auxs")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_get_resu(){
		global $f;
		$model = $f->model("ct/saux")->params(array(
			'ano'=>$f->request->data['ano'],
			'tipo'=>$f->request->data['tipo']
		))->get("resu");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		if(isset($data['programa']['_id'])) $data['programa']['_id'] = new MongoId($data['programa']['_id']);
		if(isset($data['inmueble']['_id'])) $data['inmueble']['_id'] = new MongoId($data['inmueble']['_id']);
		if(isset($data['arrendatario']['_id'])) $data['arrendatario']['_id'] = new MongoId($data['arrendatario']['_id']);
		if(isset($data['cuenta']['_id'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['periodo'])){
			if(isset($data['periodo']['mes'])){
				$data['periodo']['mes'] = floatval($data['periodo']['mes']);
			}
			if(isset($data['periodo']['ano'])){
				$data['periodo']['ano'] = floatval($data['periodo']['ano']);
			}
		}
		$params = array(
			'ano'=>$data['periodo']['ano'],
			'mes'=>$data['periodo']['mes'],
			'cuenta'=>$data['cuenta']['_id'],
			'programa'=>$data['programa']['_id']
		);
		if(isset($data['organizacion']['_id'])) $params['organizacion'] = $data['organizacion']['_id'];
		if(isset($data['inmueble']['_id'])) $params['inmueble'] = $data['inmueble']['_id'];
		if(isset($data['arrendatario']['_id'])) $params['arrendatario'] = $data['arrendatario']['_id'];

		if(!isset($f->request->data['_id'])){
			$saldo = $f->model('ct/saux')->params($params)->get('saldo')->items;
			if($saldo==null){
				$saldo = array(
					'periodo'=>array(
						'ano'=>$data['periodo']['ano'],
						'mes'=>$data['periodo']['mes']
					),
					'programa'=>$data['programa'],
					'estado'=>'A',
					'cuenta'=>$data['cuenta'],
					'debe_inicial'=>0,
					'haber_inicial'=>0
				);
				if(isset($data['organizacion'])) $saldo['organizacion'] = $data['organizacion'];
				if(isset($data['inmueble'])) $saldo['inmueble'] = $data['inmueble'];
				if(isset($data['arrendatario'])) $saldo['arrendatario'] = $data['arrendatario'];
				if($params['mes']!='1'){
					$params['mes'] = intval($params['mes']) - 1;
					$saldo_old = $f->model('ct/saux')->params($params)->get('saldo')->items;
					if($saldo_old!=null){
						$saldo['debe_inicial'] = floatval($saldo_old['debe_final']);
						$saldo['haber_inicial'] = floatval($saldo_old['haber_final']);
					}
				}
				$saldo = $f->model('ct/saux')->params(array('data'=>$saldo))->save('insert')->items;
			}
			$auxi = array(
				'fecreg'=>new MongoDate(),
				'autor'=>$f->session->userDB,
				'estado'=>'A',
				'saldos'=>array(
					'_id'=>$saldo['_id'],
					'periodo'=>$saldo['periodo'],
					'cuenta'=>$saldo['cuenta'],
					'programa'=>$saldo['programa']
				),
				'fec'=>$data['fec'],
				'clase'=>$data['clase'],
				'num'=>$data['num'],
				'detalle'=>$data['detalle'],
				'tipo'=>$data['tipo'],
				'monto'=>floatval($data['monto']),
				'tipo_saldo'=>$data['tipo_saldo']
			);
			if(isset($saldo['organizacion'])) $auxi['saldos']['organizacion'] = $saldo['organizacion'];
			if(isset($saldo['inmueble'])) $auxi['saldos']['inmueble'] = $saldo['inmueble'];
			$f->model("ct/auxs")->params(array('data'=>$auxi))->save("insert");
		}else{
			$auxi = array(
				'fecrmod'=>new MongoDate(),
				'modificado'=>$f->session->userDB,
				'estado'=>'A',
				'fec'=>$data['fec'],
				'clase'=>$data['clase'],
				'num'=>$data['num'],
				'detalle'=>$data['detalle'],
				'tipo'=>$data['tipo'],
				'monto'=>floatval($data['monto']),
				'tipo_saldo'=>$data['tipo_saldo']
			);
			$f->model("ct/auxs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$auxi))->save("update");
		}
		$f->response->print("true");
	}
	function execute_update(){
		global $f;
		$data = $f->request->data;
		if(isset($data['fec'])) $data['fec'] = new MongoDate(strtotime($data['fec']));
		$f->model("ct/auxs")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		$f->response->print("true");
	}
	function execute_cerrar(){
		global $f;
		$saldo = $f->model('ct/saux')->params(array(
			'ano'=>floatval($f->request->data['ano']),
			'mes'=>floatval($f->request->data['mes']),
			'cuenta'=>new MongoId($f->request->data['cuenta']),
			'programa'=>new MongoId($f->request->data['programa'])
		))->get('saldo')->items;
		if($saldo!=null){
			$debe = $saldo['debe_inicial'];
			$haber = $saldo['haber_inicial'];
			$items = $f->model("ct/auxs")->params(array(
				'saldo'=>$saldo['_id']
			))->get("periodo")->items;
			foreach ($items as $item){
				if($item['tipo']=='D')
					$debe += floatval($item['monto']);
				else
					$haber += floatval($item['monto']);
				$f->model("ct/auxs")->params(array('_id'=>$item['_id'],'data'=>array('estado'=>'C')))->save("update");
			}
			$f->model('ct/saux')->params(array('_id'=>$saldo['_id'],'data'=>array(
				'estado'=>'C',
				'debe_final'=>$debe,
				'haber_final'=>$haber
			)))->save('update');
			$f->response->print("true");
		}
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/auxs.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ct/auxs.select");
	}
	function execute_gene(){
		global $f;
		$f->response->view("ct/auxs.gene");
	}
}
?>