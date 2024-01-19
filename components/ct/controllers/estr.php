<?php
class Controller_ct_estr extends Controller {
	function execute_index() {
		global $f;
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/estr")->get("lista");
		foreach($model->items["cuentas"] as $i=>$item){
			$cursor = $f->model("ct/movi")->params(
						array(
							"cuenta"=>$item["_id"],						
						)
			)->get("ultimo")->items;
			$cursor2 = $f->model("ct/pcon")->params(
						array(
							"_id"=>$item["_id"],						
						)
			)->get("one")->items;
			$model->items["cuentas"][$i] = $cursor2;
			if($cursor=='reinicio'){
				$model->items["cuentas"][$i]["debe_anterior"] = "0.00";
				$model->items["cuentas"][$i]["haber_anterior"] = "0.00";
			}else{
				$model->items["cuentas"][$i]["debe_anterior"] = $cursor["debe_actual"];
				$model->items["cuentas"][$i]["haber_anterior"] = $cursor["haber_actual"];
			}	
		}
		$f->response->json( $model->items );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/estr")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/estr')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/estr")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["fecreg"] = new MongoDate();
			for($i=0;$i<count($data["cuentas"]);$i++){
				$data["cuentas"][$i]["_id"]=new MongoId($data["cuentas"][$i]["_id"]);
			}
			$f->model("ct/estr")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ct/estr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/tnot.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ct/tnot')->params(array("_id"=>$f->request->id))->delete('tipo');
    	$f->response->print( "true" );
	}
}
?>