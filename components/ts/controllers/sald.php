<?php
class Controller_ts_sald extends Controller {
	function execute_index_efe() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Generar Saldo Inicial</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"Periodo","w"=>100 ),
			3=>array( "nomb"=>"Saldo deudor inicial","w"=>150 ),
			4=>array( "nomb"=>"Saldo acreedor inicial","w"=>150 ),
			5=>array( "nomb"=>"Saldo deudor final","w"=>150 ),
			6=>array( "nomb"=>"Saldo acreedor final","w"=>150 ),
			7=>array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index_cue() {
		global $f;
		$f->response->print("<div>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Generar Saldo Inicial</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"Periodo","w"=>100 ),
			3=>array( "nomb"=>"Cuenta Corriente","w"=>150 ),
			4=>array( "nomb"=>"Saldo deudor inicial","w"=>150 ),
			5=>array( "nomb"=>"Saldo acreedor inicial","w"=>150 ),
			6=>array( "nomb"=>"Saldo deudor final","w"=>150 ),
			7=>array( "nomb"=>"Saldo acreedor final","w"=>150 ),
			8=>array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$model = $f->model("ts/saldlibr")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"tipo"=>$f->request->tipo))->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ts/saldlibr")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ts/saldlibr')->params(array('fields'=>$fields,"tipo"=>$f->request->tipo))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/saldlibr")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["apertura"]["fec"]=new MongoDate();
			//$data["periodo"]=new MongoDate(strtotime($data["periodo"]["ano"]."-".$data["periodo"]["mes"]."-01"));
			$data["apertura"]["autor"]=$f->session->userDB;
			$data["saldo_deudor_inicial"]=floatval($data["saldo_deudor_inicial"]);
			$data["saldo_acreedor_inicial"]=floatval($data["saldo_acreedor_inicial"]);
			$data["saldo_deudor_final"]=floatval($data["saldo_deudor_final"]);
			$data["saldo_acreedor_final"]=floatval($data["saldo_acreedor_final"]);
			$f->model("ts/saldlibr")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Saldos: Efectivo',
				'descr'=>'Se Genero&oacute; un saldo inicial.'
			))->save('insert');
		}else{
			$f->model("ts/saldlibr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("editar");
		}
		$f->response->print("true");
	}
	function execute_save_cue(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["apertura"]["fec"]=new MongoDate();
			//$data["periodo"]=$data["periodo"]["ano"].$data["periodo"]["mes"]."00";
			$data["apertura"]["autor"]=$f->session->userDB;
			$data["saldo_deudor_inicial"]=floatval($data["saldo_deudor_inicial"]);
			$data["saldo_acreedor_inicial"]=floatval($data["saldo_acreedor_inicial"]);
			$data["saldo_deudor_final"]=floatval($data["saldo_deudor_final"]);
			$data["saldo_acreedor_final"]=floatval($data["saldo_acreedor_final"]);
			$data["cuenta_banco"]["_id"]=new MongoId($data["cuenta_banco"]["_id"]);
			$f->model("ts/saldlibr")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Saldos: Bancos',
				'descr'=>'Se Genero&oacute; un saldo inicial.'
			))->save('insert');
		}else{
			$f->model("ts/saldlibr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("editar");
		}
		$f->response->print("true");
	}
	function execute_save_cuen(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			//$data["periodo"]=$data["periodo"]["ano"].$data["periodo"]["mes"]."00";
			$data["apertura"]["fec"]=new MongoDate();
			$data["apertura"]["autor"]=$f->session->userDB;
			$data["saldo_deudor_inicial"]=floatval($data["saldo_deudor_inicial"]);
			$data["saldo_acreedor_inicial"]=floatval($data["saldo_acreedor_inicial"]);
			$data["saldo_deudor_final"]=floatval($data["saldo_deudor_final"]);
			$data["saldo_acreedor_final"]=floatval($data["saldo_acreedor_final"]);
			$data["cuenta_banco"]["_id"]=new MongoId($data["cuenta_banco"]["_id"]);
			$f->model("ts/saldlibr")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Saldos: Cuentas Corrientes',
				'descr'=>'Se Genero&oacute; un saldo inicial.'
			))->save('insert');
		}else{
			$f->model("ts/saldlibr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("editar");
		}
		$f->response->print("true");
	}	
	function execute_save_ban(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data["apertura"]["fec"]=new MongoDate();
			//$data["periodo"]=$data["periodo"]["ano"].$data["periodo"]["mes"]."00";
			$data["apertura"]["autor"]=$f->session->userDB;
			$data["saldo_deudor_inicial"]=floatval($data["saldo_deudor_inicial"]);
			$data["saldo_acreedor_inicial"]=floatval($data["saldo_acreedor_inicial"]);
			$data["saldo_deudor_final"]=floatval($data["saldo_deudor_final"]);
			$data["saldo_acreedor_final"]=floatval($data["saldo_acreedor_final"]);
			$data["cuenta_banco"]["_id"]=new MongoId($data["cuenta_banco"]["_id"]);
			$f->model("ts/saldlibr")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ts/saldlibr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("editar");
		}
		$f->response->print("true");
	}
	function execute_cerrar(){
		global $f;
		$f->model("ts/saldlibr")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
			'estado'=>'C',
			'cierre'=>array(
				'fec'=>new MongoDate(),
				'autor'=>array(
					'_id'=>$f->session->enti['_id'],
					'tipo_enti'=>$f->session->enti['tipo_enti'],
					'nomb'=>$f->session->enti['nomb'],
					'appat'=>$f->session->enti['appat'],
					'apmat'=>$f->session->enti['apmat'],
					'cargo'=>array(
						'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
						'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
						'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
					)
				)
			)
		)))->save("update");
		$f->response->print("true");
	}
	function execute_edit_efe(){
		global $f;
		$f->response->view("ts/edit.saldefe");
	}
	function execute_edit_cue(){
		global $f;
		$f->response->view("ts/edit.saldcue");
	}
}
?>