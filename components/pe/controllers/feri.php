<?php
class Controller_pe_feri extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("pe/feri.main");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("pe/feri")->params(array("year"=>$f->request->data['year']))->get("periodo");
		$f->response->json( $model->items );
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		if(isset($f->request->data['estado'])) $estado = $f->request->data['estado'];
		$model = $f->model("pe/feri")->params(array(
			"estado"=>$estado,
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$params = array();
		if(isset($f->request->data['start'])){
			$params['start'] = new MongoDate(strtotime($f->request->data['start']));
			$params['end'] = new MongoDate(strtotime($f->request->data['end']));
		}
		$rpta = $f->model('pe/feri')->params($params)->get('all')->items;
		if(isset($f->request->data['calendario'])){
			if($rpta!=null){
				foreach ($rpta as $i=>$item) {
					$rpta[$i]['title'] = $item['nomb'];
					$rpta[$i]['start'] = date('Y-m-d',$item['fec']->sec);
				}
			}
		}
		$f->response->json($rpta);
	}
	function execute_get(){
		global $f;
		$model = $f->model("pe/feri")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$f->model("pe/feri")->params(array('year'=>$data['year']))->delete("year");
		foreach($data['data'] as $fer){
			$fer['fec'] = new MongoDate(strtotime($fer['fec']));
			$fer['fecreg'] = new MongoDate();
			$f->model("pe/feri")->params(array('data'=>$fer))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Feriados',
				'descr'=>'Se ha actualizado la lista de <b>Feriados</b> para el a&ntilde;o <b>'.$data['year'].'</b>'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/feri.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("pe/feri.select");
	}
}
?>