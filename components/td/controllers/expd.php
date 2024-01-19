<?php
class Controller_td_expd extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Expediente</button>');
		$f->response->print("</div><div name='mainGrid'>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"N&uacutemero","w"=>100 ),
			array( "nomb"=>"Gestor","w"=>210 ),
			array( "nomb"=>"Primer Envio","w"=>210 ),
			array( "nomb"=>"Asunto","w"=>210 ),
			array( "nomb"=>"Observaciones","w"=>110 ),
			array( "nomb"=>"Registrado","w"=>110 ),
			array( "nomb"=>"Vencimiento","w"=>110 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div></div>");
	}
	function execute_arch() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Expediente</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"N&uacutemero","w"=>100 ),
			array( "nomb"=>"Gestor","w"=>210 ),
			array( "nomb"=>"Asunto","w"=>210 ),
			array( "nomb"=>"Observaciones","w"=>110 ),
			array( "nomb"=>"Registrado","w"=>110 ),
			array( "nomb"=>"Archivado","w"=>110 ),
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_por() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<button name="btnAgregar">Nuevo Expediente</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>10 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"N&uacutemero","w"=>100 ),
			array( "nomb"=>"Gestor","w"=>200 ),
			array( "nomb"=>"Asunto","w"=>200 ),
			array( "nomb"=>"Origen","w"=>200 ),
			array( "nomb"=>"Observaciones","w"=>110 ),
			array( "nomb"=>"Enviado","w"=>100 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_his() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->print('<div name="search_rango" style="height:30px;line-height:30px;float:left;display:none;"><span name="rango"></span>&nbsp;<button name="btnRango">Seleccionar Inicio y Fin</button>&nbsp;<button name="btnDel"></button></div>&nbsp;');
		$f->response->view("ci/ci.search");
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			array( "nomb"=>"&nbsp;","w"=>120 ),
			array( "nomb"=>"&nbsp;","w"=>50 ),
			array( "nomb"=>"N&uacute;mero","w"=>100 ),
			array( "nomb"=>"Gestor","w"=>200 ),
			array( "nomb"=>"Asunto","w"=>200 ),
			array( "nomb"=>"Ubicacion","w"=>200 ),
			array( "nomb"=>"Observaciones","w"=>110 ),
			array( "nomb"=>"Registrado","w"=>100 ),
			array( "nomb"=>"Concluido","w"=>100 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows);
		if(isset($f->request->data['ini'])){
			$params['ini'] = new MongoDate(strtotime($f->request->data['ini']));
			$params['fin'] = new MongoDate(strtotime($f->request->data['fin']));
		}
		$model = $f->model("td/expd")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_listaexpdpor(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdpor");
		$f->response->json( $model );
	}
	function execute_listaexpdreci(){
		global $f;
		if($f->session->user['userid']=='admin'){
			$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
		}else{
			$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdreci");
		}
		/*$expd = $f->datastore->td_expedientes->find()->sort(array('fecreg'=>1));
		$i = 0;
		foreach($expd as $index=>$e){
			$i++;
			$f->datastore->td_expedientes->update(
				array('_id'=>$e['_id']),
				array('$set'=>array(
					'num_c'=>$i,
					'num'=>$i.'-2014'
				))
			);
		}*/
		$f->response->json( $model );
	}
	function execute_listaexpdvenc(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdvenc");
		$f->response->json( $model );
	}
	function execute_listaexpdarch(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdarch");
		$f->response->json( $model );
	}
	function execute_listaexpdcopi(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdcopi");
		$f->response->json( $model );
	}
	function execute_listaexpdrebi(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdrebi");
		$f->response->json( $model );
	}
	function execute_listaexpdenvi(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdenvi");
		$f->response->json( $model );
	}
	function execute_listaexpdgest(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"gestor"=>new MongoId($f->request->gestor)))->get("listaexpdgest");
		$f->response->json( $model );
	}
	function execute_listaexpdhistvenc(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("listaexpdhistvenc");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("all");
		$f->response->json( $model );
	}
	function execute_get_legacy(){
		global $f;
		$model = $f->model("td/expd")->get("one");
		//print_r($model->items);die();
		
		$f->datastore->td_expedientes->update( array('_id'=>$model->items['_id']) , array('$pull'=>array('documentos'=>array('expd'=>array('$exists'=>true)))) );
		
		$f->response->json( $model->items );
	}

	function execute_get(){
		global $f;
		$data['trabajador'] = $f->session->userDBMin;
		$model = $f->model("td/expd")->get("one");
		if($data['trabajador']['_id']->{'$id'} == "598df48b3e603722628b4568") //CONTRALORIA
		{
			$myfile = fopen("logs_acceso.txt", "a") or die("Unable to open file!");
			$data = $f->request->data;
			$data['trabajador'] = $f->session->userDBMin;
			$td_exp=$model->items;
			$concepto=($td_exp["concepto"]);
			$num=$td_exp["num"];
			$txt = json_encode("La persona o trabajador ".$data['trabajador']['nomb']." ".$data['trabajador']['appat']." ".$data['trabajador']['apmat']." intento acceder en la fecha ".date("Y-m-d H:i:s"). " el cual es el expediente numero ".$num." de Asunto <<".$concepto.">>",JSON_UNESCAPED_UNICODE);
			fwrite($myfile, "\n". $txt."\n"."***************************");
			fclose($myfile);
		}
		$f->datastore->td_expedientes->update( array('_id'=>$model->items['_id']) , array('$pull'=>array('documentos'=>array('expd'=>array('$exists'=>true)))));
		$f->response->json( $model->items);
	}

	function execute_edit(){
		global $f;
		$f->response->view("td/expd.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("td/expd.details");
	}
	function execute_editdocu(){
		global $f;
		$f->response->view("td/docu.edit");
	}
	function execute_save(){
    	global $f;
	    	$model = $f->model('td/expd')->save('datos')->items;
	    	$f->model('mg/entidad')->params(array(
	    		'_id'=>new MongoId($f->request->data['data']['gestor']['_id']),
	    		'rol'=>'roles.gestor'
	    	))->save('rol');
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se cre&oacute; el Expediente <b>'.$model['num'].'</b>'
		))->save('insert');
    	$f->response->json( $model );
	}
	function execute_save_doc(){
    	global $f;
    	$model = $f->model('td/expd')->save('doc');
		$expd = $f->model("td/expd")->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se adjunt&oacute; un documento al Expediente <b>'.$expd['num'].'</b>'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_delete_doc(){
		global $f;
    	$model = $f->model('td/expd')->params(array('_id'=>new MongoId($f->request->data['_id']),'index'=>$f->request->data['index']))->delete('doc');
		$expd = $f->model("td/expd")->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se elimin&oacute; un documento del Expediente <b>'.$expd['num'].'</b>'
		))->save('insert');
    	$f->response->print( "true" );
	}
	/*function execute_update(){
    	global $f;
    	$model = $f->model('td/expd')->save('datos');
    	$f->response->print( "true" );
	}
	function execute_delete(){
    	global $f;
    	$model = $f->model('td/expd')->delete('datos');
    	$f->response->print( "true" );
	}*/
	function execute_search(){
		global $f;
		$model = $f->model("td/expd")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("search");
		$f->response->json($model);
	}
	function execute_new(){
		global $f;
		$f->response->view("td/expd.new");
	}
	function execute_send(){
		global $f;
		$f->response->view("td/expd.send");
	}
	function execute_send_out(){
		global $f;
		$f->response->view("td/expd.send_out");
	}
	function execute_expdsend(){
		global $f;
		$model = $f->model('td/expd')->save('expdsend');
		foreach ($model->expddata as $obj) {
			$temp = $obj;
		}
		//print_r($temp);die();
		$dataNoti = array(
			"action"=>"tdExpd.windowDetailsExpd({id: '".$f->request->expd."',por: true})",
			"readed"=>false,
			"sended"=>false,
			"message"=>"Se le ha enviado un nuevo expediente",
			"fecreg"=>new MongoDate(),
			"icon"=>"ui-icon-folder-collapsed",
			"organizacion"=>array(
				"_id"=>new MongoId($f->request->data['data']['organizacion']['_id']),
				"nomb"=>$f->request->data['data']['organizacion']['nomb']
			)
		);
		//echo $dataNoti['action'];die();
		$f->model('ac/noti')->params(array("data"=>$dataNoti))->save('one');
		if(isset($f->request->data['copias'])){
			$cop = (array)$f->request->data['copias'];
			$i = 0;
			foreach ($cop as $obj){
				if(isset($obj['id']))
					$co[$i]['organizacion']['_id'] = new MongoId($obj['id']);
				$co[$i]['organizacion']['nomb'] = $obj['nomb'];
				$copyNoti = array(
					"action"=>"tdExpd.windowDetailsExpd({id: '".$f->request->expd."',readOnly: true})",
					"readed"=>false,
					"sended"=>false,
					"message"=>"Se le ha enviado una copia de un expediente",
					"fecreg"=>new MongoDate(),
					"icon"=>"ui-icon-folder-collapsed",
					"organizacion"=>array(
						"_id" => $co[$i]['organizacion']['_id'],
						"nomb"=> $co[$i]['organizacion']['nomb']
					)
				);
				$f->model('ac/noti')->params(array("data"=>$copyNoti))->save('one');
				$i++;
			}
		}
		$expd = $f->model("td/expd")->params(array('_id'=>new MongoId($f->request->expd)))->get("one_id")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se envi&oacute; el Expediente <b>'.$expd['num'].'</b> a <b>'.$f->request->data['data']['organizacion']['nomb'].'</b>'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_expdsend_circular(){
		global $f;
		$model = $f->model('td/expd')->save('expdsend_circular');
		$f->response->print("true");
	}
	function execute_expdsendout(){
		global $f;
		$model = $f->model('td/expd')->save('expdsendout');
		foreach ($model->expddata as $obj) {
			$temp = $obj;
		}
		$expd = $f->model("td/expd")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one_id")->items;
		$enti = $f->request->data['entidad']['nomb'];
		if($f->request->data['entidad']['tipo_enti']=='P')
			$enti .= ' '.$f->request->data['entidad']['appat'].' '.$f->request->data['entidad']['apmat'];
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se envi&oacute; el Expediente <b>'.$expd['num'].'</b> a <b>'.$enti.'</b>'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_expdsendin(){
		global $f;
		$model = $f->model('td/expd')->save('expdsendin');
		foreach ($model->expddata as $obj) {
			$temp = $obj;
		}
		$expd = $f->model("td/expd")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one_id")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se recibi&oacute; el Expediente <b>'.$expd['num'].'</b> desde una entidad externa'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_expdestado(){
		global $f;
		$model = $f->model('td/expd')->save('expdestado');
		$msj = "El Expediente ".$f->request->num." fue modificado";
		if($f->request->fl=="0"){
			if($f->request->descr=="0"){
				$msj = "El Expediente ".$f->request->num." fue aceptado";
				$icon = "ui-icon-check";
			}else{
				$msj="El Expediente ".$f->request->num." fue rechazado";
				$icon = "ui-icon-closethick";
			}
			$f->model('ac/noti')->params(array("data"=>array(
			"action"=>"$.noop();",
			"readed"=>false,"sended"=>false,
			"message"=>$msj,"fecreg"=>new MongoDate(),"icon"=>$icon,
			"organizacion"=>array("_id"=>new MongoId($f->request->origen['_id']),"nomb"=>$f->request->origen['nomb']))))->save('one');
    	}
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>$msj
		))->save('insert');
		$f->response->print( "true" );
	}
	function execute_expdopen(){
		global $f;
		$model = $f->model('td/expd')->save('expdopen');
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'El Expediente '.$f->request->data['num'].' fue reabierto'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_expdarchivar(){
		global $f;
		$model = $f->model('td/expd')->save('expdarchivar');
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'El Expediente '.$f->request->data['num'].' fue archivado'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_expdcon(){
		global $f;
		$f->response->view("td/expd.con");
	}
	function execute_expdconcluir(){
		global $f;
		$model = $f->model('td/expd')->save('expdconcluir');
		$expd = $f->model("td/expd")->params(array('_id'=>new MongoId($f->request->id)))->get("one_id")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'TD',
			'bandeja'=>'Expedientes',
			'descr'=>'Se concluy&oacute; el Expediente <b>'.$expd['num'].'</b>'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_print(){
		global $f;
		$model = $f->model("td/expd")->params(array("_id"=>new MongoId($f->request->data['id'])))->get("one_id");
		$myfile = fopen("logs_impresiones.txt", "a") or die("Unable to open file!");
		$data = $f->request->data;
		$data['trabajador'] = $f->session->userDBMin;
		$td_exp=$model->items;
		$concepto=($td_exp["concepto"]);
		$num=$td_exp["num"];
		//$data['fecmod']=new MongoDate();
		$txt = json_encode("La persona o trabajador ".$data['trabajador']['nomb']." ".$data['trabajador']['appat']." ".$data['trabajador']['apmat']." intento imprimir en la fecha ".date("Y-m-d H:i:s"). " el cual es el expediente numero ".$num." de Asunto <<".$concepto.">>",JSON_UNESCAPED_UNICODE);

/*		//comprobamos si el contenido de la variable esta en UTF-8
		if(mb_check_encoding( $txt , "UTF-8" )){
    		//recodificamos el contenido
    		$txt = iconv("UTF-8", "WINDOWS-1252", $txt);
		}
*/
		fwrite($myfile, "\n". $txt."\n"."***************************");
		fclose($myfile);
		$f->response->view("td/expd_one.print",$model);
	}
	function execute_print_legacy(){
		global $f;
		$model = $f->model("td/expd")->params(array("_id"=>new MongoId($f->request->data['id'])))->get("one_id");
		$f->response->view("td/expd_one.print",$model);
	}
}
?>
