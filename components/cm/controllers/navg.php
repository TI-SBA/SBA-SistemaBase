<?php
class Controller_cm_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		/*if($f->session->tasks['cm.comp']) $data[] = array("name"=>"cmComp","descr"=>'Comprobantes',"icon"=>"ui-icon-document");*/
		//if(isset($f->session->tasks['cm.oper'])) $data[] = array("name"=>"cmOper","descr"=>'Operaciones',"icon"=>"ui-icon-bookmark");
		if(isset($f->session->tasks['cm.oper'])) $data[] = array("name"=>"cmOperAll","descr"=>'Operaciones',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['cm.oper'])) $data[] = array("name"=>"cmRegi","descr"=>'Registro Hist&oacute;rico',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['cm.espc'])) $data[] = array("name"=>"cmEspa","descr"=>'Espacios',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['cm.ctas'])) $data[] = array("name"=>"cmCuenOcu","descr"=>'Ocupantes',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['cm.ctas'])) $data[] = array("name"=>"cmCuenPro","descr"=>'Propietarios',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['cm.espc'])) $data[] = array("name"=>"cmMapa","descr"=>'Mapa',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['cm.espc'])) $data[] = array("name"=>"cmPabe","descr"=>'Pabellones',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['cm.accs'])) $data[] = array("name"=>"cmAcce","descr"=>'Accesorios',"icon"=>"ui-icon-copy");
		//if(isset($f->session->tasks['cm.oper.conc'])) $data[] = array("name"=>"cmConc","descr"=>'Concesiones',"icon"=>"ui-icon-bookmark");
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjRede","descr"=>'Recibos Definitivos',"icon"=>"ui-icon-person");
		$data[] = array("name"=>"cmCuen","descr"=>'Cuentas por Cobrar Emisi&oacute;n',"icon"=>"ui-icon-note");
		$data[] = array("name"=>"cjCompRec","descr"=>'Caja Cementerio Recibos',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjCompPen","descr"=>'Pendientes de Cambio de Nombre',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cm.reps'])) $data[] = array("name"=>"cmRepo","descr"=>'Reportes',"icon"=>"ui-icon-copy");
		if(isset($f->session->tasks['cj.reps'])) $data[] = array("name"=>"cjRepo","descr"=>'Reportes de Caja Tesorer&iacute;a',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['cm.espc'])) $data[] = array("name"=>"cmRehi","descr"=>'Digitalizaci&oacute;n de Recibos',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['cm.espc'])) $data[] = array("name"=>"cmTerr","descr"=>'Circuito de Terror',"icon"=>"ui-icon-tag");
		$f->response->json( $data );
	}
	function execute_oper() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['cm.oper'])) $data[] = array("name"=>"cmOperAll","descr"=>'Todas',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['cm.oper'])) $data[] = array("name"=>"cmOperPro","descr"=>'Programadas',"icon"=>"ui-icon-calendar");
		$f->response->json( $data );
	}
	function execute_comp() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['cm.comp'])) $data[] = array("name"=>"cmCompAll","descr"=>'Todos',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['cm.comp'])) $data[] = array("name"=>"cmCompPen","descr"=>'Pendientes',"icon"=>"ui-icon-script");
		$f->response->json( $data );
	}
	function execute_conc() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['cm.oper.conc'])) $data[] = array("name"=>"cmConcAll","descr"=>'Todas',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['cm.oper.conc'])) $data[] = array("name"=>"cmConcVen","descr"=>'Vencidas',"icon"=>"ui-icon-alert");
		if(isset($f->session->tasks['cm.oper.conc'])) $data[] = array("name"=>"cmConcPor","descr"=>'Por Vencer',"icon"=>"ui-icon-notice");
		if(isset($f->session->tasks['cm.oper.conc'])) $data[] = array("name"=>"cmConcRec","descr"=>'Recientes',"icon"=>"ui-icon-script");
		$f->response->json( $data );
	}
}
?>