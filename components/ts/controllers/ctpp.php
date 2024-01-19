<?php
class Controller_ts_ctpp extends Controller {
	function execute_index_pen() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nueva Cuenta por pagar</button>');
		$f->response->print('<button name="btnComprob">Nuevo Comprobante</button>');
		$f->response->print('<select name="modulo">
			<option value="">Todos</option>
			<option value="PE">Personal</option>
			<option value="L">Logistica</option>
		</select>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>30 ),
			1=>array( "nomb"=>"<input type=\"checkbox\" name=\"checkall\">","w"=>20 ),
			2=>array( "nomb"=>"Beneficiado","w"=>150 ),
			3=>array( "nomb"=>"Motivo","w"=>250 ),
			4=>array( "nomb"=>"Organizaci&oacute;n","w"=>150),
			5=>array( "nomb"=>"Pagos","w"=>100),
			6=>array( "nomb"=>"Descuentos","w"=>100),
			7=>array( "nomb"=>"Neto a pagar","w"=>100),
			8=>array( "nomb"=>"Registrado","w"=>150)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index_all() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"Beneficiado","w"=>150 ),
			3=>array( "nomb"=>"Motivo","w"=>250 ),
			4=>array( "nomb"=>"Organizaci&oacute;n","w"=>150),
			5=>array( "nomb"=>"Pagos","w"=>100),
			6=>array( "nomb"=>"Descuentos","w"=>100),
			7=>array( "nomb"=>"Neto a pagar","w"=>100),
			8=>array( "nomb"=>"Registrado","w"=>150)
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"estado"=>$f->request->estado);
		if(isset($f->request->data["modulo"]))$params["modulo"] = $f->request->data["modulo"];
		$model = $f->model("ts/ctpp")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_search(){
		global $f;
		$model = $f->model("ts/ctpp")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$fields = array();
		$model = $f->model('ts/ctpp')->params(array('fields'=>$fields))->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/ctpp")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg']= new MongoDate();
			$data['origen'] = 'P';
			$data['autor'] = $f->session->userDB;
			$data['beneficiario']['_id']=new MongoId($data['beneficiario']['_id']);
			$bene = $data['beneficiario']['nomb'];
			if($data['beneficiario']['tipo_enti']=="P"){
				$bene .= " ".$data['beneficiario']['appat']." ".$data['beneficiario']['apmat'];
			}
			for($i=0;$i<count($data['conceptos']);$i++){
				$data['conceptos'][$i]['concepto']['_id']=new MongoId($data['conceptos'][$i]['concepto']['_id']);
				if(isset($data['conceptos'][$i]['concepto']['cuenta'])){
					$data['conceptos'][$i]['concepto']['cuenta']['_id']=new MongoId($data['conceptos'][$i]['concepto']['cuenta']['_id']);
				}
				if(isset($data['conceptos'][$i]['concepto']['clasificador'])){
					$data['conceptos'][$i]['concepto']['clasificador']['_id']=new MongoId($data['conceptos'][$i]['concepto']['clasificador']['_id']);
				}
				if($data['conceptos'][$i]['tipo']=="D"){
					$desc = array();
					$desc['fecreg']=new MongoDate();
					$desc['origen'] = 'D';
					$desc['autor']=$data['autor'];
					$desc['estado']="P";
					$desc['beneficiario'] = $data['conceptos'][$i]['beneficiario'];
					$desc['beneficiario']['_id'] = new MongoId($desc['beneficiario']['_id']);
					$desc['motivo'] = $data['conceptos'][$i]['concepto']['nomb']." (".$data['motivo'].')';
					$desc['conceptos'][0]['tipo']="P";
					$desc['conceptos'][0]['observ']=$data['conceptos'][$i]['concepto']['nomb'];
					$desc['conceptos'][0]['moneda']=$data['conceptos'][$i]['moneda'];
					$desc['conceptos'][0]['monto']=$data['conceptos'][$i]['monto'];
					$desc['total_pago']=floatval($data['conceptos'][$i]['monto']);
					$desc['total_desc']=floatval("0");
					$desc['total']=$desc['total_pago'];
					$bene_d = $desc['beneficiario']['nomb'];
					if($desc['beneficiario']['tipo_enti']=="P"){
						$bene_d .= " ".$desc['beneficiario']['appat']." ".$desc['beneficiario']['apmat'];
					}
					$f->model("ts/ctpp")->params(array('data'=>$desc))->save("insert");
					$f->model('ac/log')->params(array(
						'modulo'=>'TS',
						'bandeja'=>'Cuentas por Pagar',
						'descr'=>'Se Cre&oacute; la Cuenta por Pagar con motivo <b>'.$desc['motivo'].'</b> para <b>'.$bene_d.'</b>.'
					))->save('insert');
				}
				$data['conceptos'][$i]['beneficiario']=null;
				if(count($data['conceptos'][$i]['asignacion'])>0){
					for($j=0;$j<count($data['conceptos'][$i]['asignacion']);$j++){
						$data['conceptos'][$i]['asignacion'][$j]['organizacion']['_id']=new MongoId($data['conceptos'][$i]['asignacion'][$j]['organizacion']['_id']);
						$data['conceptos'][$i]['asignacion'][$j]['organizacion']['actividad']['_id']=new MongoId($data['conceptos'][$i]['asignacion'][$j]['organizacion']['actividad']['_id']['$id']);
						$data['conceptos'][$i]['asignacion'][$j]['organizacion']['componente']['_id']=new MongoId($data['conceptos'][$i]['asignacion'][$j]['organizacion']['componente']['_id']['$id']);
					}
				}
			}
			for($i=0;$i<count($data['afectacion']);$i++){
				$data['afectacion'][$i]['organizacion']['_id']=new MongoId($data['afectacion'][$i]['organizacion']['_id']);
				$data['afectacion'][$i]['organizacion']['actividad']['_id']=new MongoId($data['afectacion'][$i]['organizacion']['actividad']['_id']);
				$data['afectacion'][$i]['organizacion']['componente']['_id']=new MongoId($data['afectacion'][$i]['organizacion']['componente']['_id']);
				if(count($data['afectacion'][$i]['gastos'])>0){
					for($j=0;$j<count($data['afectacion'][$i]['gastos']);$j++){
						$data['afectacion'][$i]['gastos'][$j]['clasificador']['_id']=new MongoId($data['afectacion'][$i]['gastos'][$j]['clasificador']['_id']);
					}
				}
			}
			$f->model("ts/ctpp")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cuentas por Pagar',
				'descr'=>'Se Cre&oacute; la Cuenta por Pagar con motivo <b>'.$data["motivo"].'</b> para <b>'.$bene.'</b>.'
			))->save('insert');
		}else{	
			$f->model("ts/ctpp")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$cursor = $f->model("ts/ctpp")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$bene = $cursor['beneficiario']['nomb'];
			if($cursor['beneficiario']['tipo_enti']=="P"){
				$bene .= " ".$cursor['beneficiario']['appat']." ".$cursor['beneficiario']['apmat'];
			}
			$f->model('ac/log')->params(array(
				'modulo'=>'TS',
				'bandeja'=>'Cuentas por Pagar',
				'descr'=>'Se Anul&oacute; la Cuenta por Pagar con motivo <b>'.$cursor["motivo"].'</b> para <b>'.$bene.'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/ctpp.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/ctpp.details");
	}
	function execute_asignar(){
		global $f;
		$f->response->view("ts/ctpp.asignar");
	}
	function execute_asignar2(){
		global $f;
		$f->response->view("ts/ctpp.asignar2");
	}
}
?>