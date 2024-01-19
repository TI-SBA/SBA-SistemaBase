<?php
class Controller_in_rent extends Controller {
	function execute_index() {
		global $f;
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>50 ),
			1=>array( "nomb"=>"Arrendatario","w"=>300 ),
			2=>array( "nomb"=>"Inmueble","w"=>300 ),
			3=>array( "nomb"=>"N&uacute;mero","w"=>100 ),
			4=>array( "nomb"=>"Letra","w"=>100 ),
			5=>array( "nomb"=>"Vencimiento","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index_all() {
		global $f;
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>40 ),
			2=>array( "nomb"=>"Arrendatario","w"=>300 ),
			3=>array( "nomb"=>"Inmueble","w"=>300 ),
			4=>array( "nomb"=>"N&uacute;mero","w"=>100 ),
			5=>array( "nomb"=>"Letra","w"=>100 ),
			6=>array( "nomb"=>"Vencimiento","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_listaall(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listarentall");
		foreach ($model->items as $i=>$item){
			$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
				"_id"=>$model->items[$i]['espacio']['_id']
			))->get("one")->items;
		}
		$f->response->json( $model );
	}
	function execute_listaven(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listarentven");
		foreach ($model->items as $i=>$item){
			$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
				"_id"=>$model->items[$i]['espacio']['_id']
			))->get("one")->items;
		}
		$f->response->json( $model );
	}
	function execute_listapro(){
		global $f;
		$model = $f->model("in/oper")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listarentpro");
		foreach ($model->items as $i=>$item){
			$model->items[$i]['espacio'] = $f->model("in/espa")->params(array(
				"_id"=>$model->items[$i]['espacio']['_id']
			))->get("one")->items;
		}
		$f->response->json( $model );
	}
	function execute_protestar(){
		global $f;
		$trabajador = $f->session->userDB;
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']['$id']);
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']['$id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']['$id']);
		$f->model('in/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'espacio'=>$espacio,
			'protestar_letra'=>array(
				'letra'=>$f->request->letra
			)
		)))->save('oper');
		$f->model('in/oper')->params(array(
			'filter'=>array('_id'=>new MongoId($f->request->id),'arrendamiento.rentas.letra'=>$f->request->letra),
			'data'=>array('$set'=>array('arrendamiento.rentas.$.estado'=>'PT'))
		))->save('upd_custom');
		if(isset($f->request->data['cuenta'])){
			$f->model('cj/cuen')->params(array(
				'_id'=>new MongoId($f->request->data['cuenta']),
				'data'=>array('$set'=>array('estado'=>'I'))
			))->save('custom');
		}
	}
	function execute_levantar(){
		global $f;
		$trabajador = array(
			'_id'=>$f->session->enti['_id'],
			'tipo_enti'=>$f->session->enti['tipo_enti'],
			'nomb'=>$f->session->enti['nomb']
		);
		if($f->session->enti['appat']!=null){
			$trabajador['appat'] = $f->session->enti['appat'];
			$trabajador['apmat'] = $f->session->enti['apmat'];
		}
		$arrendatario = $f->request->data['arrendatario'];
		$arrendatario['_id'] = new MongoId($arrendatario['_id']['$id']);
		$espacio = $f->request->data['espacio'];
		$espacio['_id'] = new MongoId($espacio['_id']['$id']);
		$espacio['ubic']['local']['_id'] = new MongoId($espacio['ubic']['local']['_id']['$id']);
		$f->model('in/oper')->params(array('data'=>array(
			'fecreg'=>new MongoDate(),
			'trabajador'=>$trabajador,
			'espacio'=>$espacio,
			'levantar_letra'=>array(
				'letra'=>$f->request->letra
			)
		)))->save('oper');
		$f->model('in/oper')->params(array(
			'filter'=>array('_id'=>new MongoId($f->request->id),'arrendamiento.rentas.letra'=>$f->request->letra),
			'data'=>array('$set'=>array('arrendamiento.rentas.$.estado'=>'PG'))
		))->save('upd_custom');
		if(isset($f->request->data['cuenta'])){
			$f->model('cj/cuen')->params(array(
				'_id'=>new MongoId($f->request->data['cuenta']),
				'data'=>array('$set'=>array('estado'=>'P'))
			))->save('custom');
		}
	}
}
?>