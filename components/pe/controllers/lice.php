<?php
class Controller_pe_lice extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pe/hora.calen");
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/prog")->params(array("tipo"=>"LI","enti"=>new MongoId($f->request->data['enti'])))->get("trab");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['trabajador']['_id'] = new MongoId($data['trabajador']['_id']);
		if(isset($data['del'])){
			foreach ($data['del'] as $blo){
				$f->model("pe/prog")->params(array('_id'=>new MongoId($blo)))->delete("data");
			}
		}
		if(isset($data['data'])){
			foreach ($data['data'] as $blo){
				$blo['trabajador'] = $data['trabajador'];
				$blo['tipo']['_id'] = new MongoId($blo['tipo']['_id']);
				$blo['fecini'] = new MongoDate(strtotime($blo['fecini']));
				$blo['fecfin'] = new MongoDate(strtotime($blo['fecfin']));
				if(!isset($blo['_id'])){
					$blo['fecreg'] = new MongoDate();
					$f->model("pe/prog")->params(array('data'=>$blo))->save("insert");
				}else{
					$f->model("pe/prog")->params(array('_id'=>new MongoId($blo['_id']),'data'=>$blo))->save("update");
				}
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/vaca.edit");
	}
	function execute_modal(){
		global $f;
		$f->response->view("pe/vaca.modal");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/vaca.select");
	}
}
?>