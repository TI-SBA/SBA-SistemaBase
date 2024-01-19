<?php
class Controller_ct_notc extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['periodo'])) $params['ano']= floatval($f->request->data['periodo']);
		if(isset($f->request->data['mes'])) $params['mes']= floatval($f->request->data['mes']);
		if(isset($f->request->data['tipo'])) $params['tipo']= $f->request->data['tipo'];
		$model = $f->model("ct/notc")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_lista_all(){
		global $f;
		if(isset($f->request->data['pcta'])){
			$model = array(
				'notas'=>$f->model("ct/notc")->params(array('filter'=>array("periodo.ano"=>$f->request->ano,"periodo.mes"=>intval($f->request->mes))))->get("custom")->items,
				'pcta'=>$f->model("ct/pcue")->get('all')->items
			);
		}else{
			if(isset($f->request->data['cuenta']))
				$model = $f->model("ct/notc")->params(array('filter'=>array("cuentas.cuenta._id"=>new MongoId($f->request->cuenta),"periodo.ano"=>$f->request->ano,"periodo.mes"=>intval($f->request->mes))))->get("custom")->items;
			else
				$model = $f->model("ct/notc")->params(array('filter'=>array("periodo.ano"=>$f->request->ano,"periodo.mes"=>intval($f->request->mes))))->get("custom")->items;
		}
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ct/notc")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_num(){
		global $f;
		$cod = $f->model("ct/notc")->params(array("periodo"=>$f->request->periodo,"tipo"=>new MongoId($f->request->tipo)))->get("num")->items;
		if($cod==null) $cod=1;
		else $cod = floatval($cod)+1;
		$f->response->json(array(
			'num'=>$cod
		));
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(isset($data["tipo"]["_id"])) $data["tipo"]["_id"] = new MongoId($data["tipo"]["_id"]);
		if(isset($data['cuentas'])){
			foreach ($data['cuentas'] as $i=>$item){
				$data['cuentas'][$i]['cuenta']['_id'] = new MongoId($item['cuenta']['_id']);
				if($item['ultimo']=='1') $data['cuentas'][$i]['ultimo'] = true;
				else $data['cuentas'][$i]['ultimo'] = false;
				if(isset($item['monto'])) $data['cuentas'][$i]['monto'] = floatval($item['monto']);
			}
		}
		if(isset($data['total_debe'])) $data['total_debe'] = floatval($data['total_debe']);
		if(isset($data['total_haber'])) $data['total_haber'] = floatval($data['total_haber']);
		if(isset($data["periodo"])){
			if(isset($data["periodo"]['mes'])){
				$data["periodo"]["mes"] = floatval($data["periodo"]["mes"]);
			}
			if(isset($data["periodo"]['ano'])){
				$data["periodo"]["ano"] = floatval($data["periodo"]["ano"]);
			}
		}
		$data["fecmod"] = new MongoDate();
		$data['modificado'] = $f->session->userDBMin;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$f->model("ct/notc")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/notc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/notc.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ct/notc')->params(array("_id"=>$f->request->id))->delete('tipo');
    	$f->response->print( "true" );
	}
}
?>