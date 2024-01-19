<?php
class Controller_ct_comej extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/epres.comej");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ct/comej")->params(array("ano"=>$f->request->ano,"mes"=>$f->request->mes,"tipo"=>$f->request->tipo))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ct/comej")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ct/comej')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ct/comej")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_cerrar(){
		global $f;
		$data = $f->request->data;
		$cursor = $f->model('ct/comej')->params(array("ano"=>$data["ano"],"mes"=>$data["mes"],"tipo"=>$data["tipo"]))->get('lista');
		if($cursor->items!=null){
			foreach($cursor->items as $toDel){
				$f->model('ct/comej')->params(array("_id"=>$toDel["_id"],"data"=>array("estado"=>"C")))->save('update');				
			}
		}
		$f->response->print("true");
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$cursor = $f->model('ct/comej')->params(array("ano"=>$data["periodo"]["ano"],"mes"=>$data["periodo"]["mes"],"tipo"=>$data["tipo"]))->get('lista');
			if($cursor->items!=null){
				foreach($cursor->items as $toDel){
					$f->model('ct/comej')->params(array("_id"=>$toDel["_id"]))->delete('cuadro');
				}
			}
			$data["fecreg"] = new MongoDate();
			$data["autor"]["_id"] = new MongoId($data["autor"]["_id"]);
			$data["autor"]["cargo"]["_id"] = new MongoId($data["autor"]["cargo"]["_id"]);
			$data["autor"]["cargo"]["organizacion"]["_id"] = new MongoId($data["autor"]["cargo"]["organizacion"]["_id"]);
			for($i=0;$i<count($data["cols"]);$i++){	
				$row = array();
				$row["estado"]="A";
				$row["fecreg"]=$data["fecreg"];
				$row["autor"]=$data["autor"];
				$row["periodo"]=$data["periodo"];
				$row["tipo"]=$data["tipo"];
				$row["columna"]=$data["cols"][$i]["columna"];
				$row["items"]=$data["cols"][$i]["items"];
				$row["donacion"]=$data["cols"][$i]["donacion"];
				$f->model("ct/comej")->params(array('data'=>$row))->save("insert");				
			}			
		}else{
			$f->model("ct/comej")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ct/tnot.edit");
	}
	function execute_delete(){
		global $f;
    	$model = $f->model('ct/comej')->params(array("_id"=>$f->request->id))->delete('tipo');
    	$f->response->print( "true" );
	}
}
?>