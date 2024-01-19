<?php
class Controller_ct_movi extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres.movi");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/movi")->params(array(
					"mes"=>floatval($f->request->mes),
					"ano"=>$f->request->ano			
		))->get("lista");
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				$cursor2 = $f->model("ct/pcon")->params(
							array(
								"_id"=>$item["cuenta"]["_id"],						
							)
				)->get("one")->items;
				$model->items[$i]["cuenta"] = $cursor2;
			}
		}
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/movi")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/movi')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/movi")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_cerrar(){
		global $f;
		$data = $f->request->data;
		$mes = floatval($data["periodo"]["mes"]);
		$ano = $data["periodo"]["ano"];
		$cursor = $f->model('ct/movi')->params(array("ano"=>$ano,"mes"=>$mes))->get('lista');
		if($cursor->items!=null){
			foreach($cursor->items as $toDel){
				$mov = array();
				$mov["estado"]="C";
				$f->model("ct/movi")->params(array('_id'=>$toDel['_id'],'data'=>$mov))->save("update");
			}
		}
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$mes = floatval($data["periodo"]["mes"]);
			$ano = $data["periodo"]["ano"];
			$cursor = $f->model('ct/movi')->params(array("ano"=>$ano,"mes"=>$mes))->get('lista');
			if($cursor->items!=null){
				foreach($cursor->items as $toDel){
					$f->model('ct/movi')->params(array("_id"=>$toDel["_id"]))->delete('movi');
				}
			}
			$data["fecreg"] = new MongoDate();
			$data["autor"]["_id"] = new MongoId($data["autor"]["_id"]);
			$data["autor"]["cargo"]["_id"] = new MongoId($data["autor"]["cargo"]["_id"]);
			$data["autor"]["cargo"]["organizacion"]["_id"] = new MongoId($data["autor"]["cargo"]["organizacion"]["_id"]);
			for($i=0;$i<count($data["movimientos"]);$i++){
				$mov = array();
				$mov["fecreg"] = $data["fecreg"];
				$mov["autor"] = $data["autor"];
				$mov["periodo"] = $data["periodo"];
				$mov["periodo"]["mes"] = floatval($mov["periodo"]["mes"]);
				$mov["estado"] = $data["estado"];
				$mov["cuenta"] = $data["movimientos"][$i]["cuenta"];
				$mov["cuenta"]["_id"] = new MongoId($mov["cuenta"]["_id"]);
				$mov["debe_anterior"] = $data["movimientos"][$i]["debe_anterior"];
				$mov["haber_anterior"] = $data["movimientos"][$i]["haber_anterior"];
				$mov["debe_actual"] = $data["movimientos"][$i]["debe_actual"];
				$mov["haber_actual"] = $data["movimientos"][$i]["haber_actual"];
				$f->model("ct/movi")->params(array('data'=>$mov))->save("insert");
			}			
		}else{
			$f->model("ct/movi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/epres.movi.edit");
	}
	function execute_estructura(){
		global $f;
		$f->response->view("ct/epres.movi.estr");
	}
}
?>