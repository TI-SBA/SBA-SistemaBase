<?php
class Controller_td_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['td.expd'])) $data[] = array("name"=>"tdExp","descr"=>'Expedientes',"icon"=>"ui-icon-folder-collapsed");
		if(isset($f->session->tasks['td.expd.hist'])) $data[] = array("name"=>"tdHis","descr"=>'Expedientes: Historico',"icon"=>"ui-icon-folder-collapsed");
		if(isset($f->session->tasks['td.expd.gest.int']) || isset($f->session->tasks['td.expd.gest.ext'])) $data[] = array("name"=>"tdGest","descr"=>'Cuentas',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['td.tdoc'])) $data[] = array("name"=>"tdTdocs","descr"=>'Tipos de Documentos',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['td.orga'])) $data[] = array("name"=>"tdOrga","descr"=>'&Oacute;rganos Externos para el TUPA',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['td.orga'])) $data[] = array("name"=>"tdComi","descr"=>'Comites',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['td.tupa'])) $data[] = array("name"=>"tdTupa","descr"=>'TUPA',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['td.repo'])) $data[] = array("name"=>"tdRepo","descr"=>'Reportes',"icon"=>"ui-icon-signal");
		$f->response->json( $data );
	}
	function execute_gest() {
		global $f;
		$data = array(
			0=>array("name"=>"tdGestExt","descr"=>'Gestores Externos',"icon"=>"ui-icon-contact"),
			1=>array("name"=>"tdGestInt","descr"=>'Gestores Internos',"icon"=>"ui-icon-contact")
		);
		$f->response->json( $data );
	}
	function execute_repo() {
		global $f;
		$data = array();
		/*if($f->session->tasks['td.expd'])*/ $data[] = array("name"=>"tdRepoAll","descr"=>'Reportes Generales',"icon"=>"ui-icon-folder-collapsed");
		$f->response->json( $data );
	}
	function execute_exps() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['td.expd'])) $data[] = array("name"=>"tdExpdArch","descr"=>'Archivados',"icon"=>"ui-icon-folder-collapsed");
		if(isset($f->session->tasks['td.expd'])) $data[] = array("name"=>"tdExpdCopi","descr"=>'Copias',"icon"=>"ui-icon-copy");
		if(isset($f->session->tasks['td.expd'])) $data[] = array("name"=>"tdExpdVenc","descr"=>'Por Vencer',"icon"=>"ui-icon-alert");
		if(isset($f->session->tasks['td.expd'])) $data[] = array("name"=>"tdExpdReci","descr"=>'Nuevos / Recibidos',"icon"=>"ui-icon-pin-s");
		if(isset($f->session->tasks['td.expd'])) $data[] = array("name"=>"tdExpdPor","descr"=>'Por Recibir',"icon"=>"ui-icon-calendar");
		$f->response->json( $data );
	}
	function execute_his() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['td.expd.hist'])) $data[] = array("name"=>"tdExpdHistEnvi","descr"=>'Enviados',"icon"=>"ui-icon-arrowthick-1-ne");
		if(isset($f->session->tasks['td.expd.hist'])) $data[] = array("name"=>"tdExpdHistRebi","descr"=>'Recibidos',"icon"=>"ui-icon-arrowthick-1-sw");
		if(isset($f->session->tasks['td.expd.hist.org'])) $data[] = array("name"=>"tdExpdHistVenc","descr"=>'Por Vencer',"icon"=>"ui-icon-alert");
		if(isset($f->session->tasks['td.expd.hist.org'])) $data[] = array("name"=>"tdExpdHistAll","descr"=>'Todos',"icon"=>"ui-icon-copy");
		$f->response->json( $data );
	}
}
?>