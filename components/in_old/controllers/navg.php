<?php
class Controller_in_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		//if($f->session->tasks['in.comp']) $data[] = array("name"=>"inComp","descr"=>'Comprobantes',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['in.arre'])) $data[] = array("name"=>"inArre","descr"=>'Arrendamientos',"icon"=>"ui-icon-bookmark");
		/*if($f->session->tasks['in.oper'])*/ /*$data[] = array("name"=>"inOper","descr"=>'Operaciones',"icon"=>"ui-icon-bookmark");*/
		if(isset($f->session->tasks['in.rent'])) $data[] = array("name"=>"inRent","descr"=>'Rentas',"icon"=>"ui-icon-bookmark");
		if(isset($f->session->tasks['in.ctas'])) $data[] = array("name"=>"inCtas","descr"=>'Cuentas',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['in.loca'])) $data[] = array("name"=>"inLoca","descr"=>'Inmueble Matriz',"icon"=>"ui-icon-home");
		$data[] = array("name"=>"inRepo","descr"=>'Reportes',"icon"=>"ui-icon-home");
		$f->response->json( $data );
	}
	function execute_comp() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['in.comp'])) $data[] = array("name"=>"inCompAll","descr"=>'Todos',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['in.comp'])) $data[] = array("name"=>"inCompPen","descr"=>'Pendientes',"icon"=>"ui-icon-script");
		$f->response->json( $data );
	}
	function execute_arre() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['in.arre'])) $data[] = array("name"=>"inArreAll","descr"=>'Todos',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['in.arre'])) $data[] = array("name"=>"inArreVen","descr"=>'Vencidos',"icon"=>"ui-icon-alert");
		if(isset($f->session->tasks['in.arre'])) $data[] = array("name"=>"inArrePor","descr"=>'Por Vencer',"icon"=>"ui-icon-notice");
		if(isset($f->session->tasks['in.arre'])) $data[] = array("name"=>"inArreRec","descr"=>'Recientes',"icon"=>"ui-icon-script");
		if(isset($f->session->tasks['in.arre'])) $data[] = array("name"=>"inArrePen","descr"=>'Pendientes',"icon"=>"ui-icon-script");
		$f->response->json( $data );
	}
	function execute_oper() {
		global $f;
		$data = array();
		/*if($f->session->tasks['cm.espa'])*/ $data[] = array("name"=>"inOperAll","descr"=>'Todas',"icon"=>"ui-icon-search");
		/*if($f->session->tasks['cm.espa'])*/ $data[] = array("name"=>"inOperPro","descr"=>'Programadas',"icon"=>"ui-icon-calendar");
		$f->response->json( $data );
	}
	function execute_rent() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['in.rent'])) $data[] = array("name"=>"inRentAll","descr"=>'Todas',"icon"=>"ui-icon-search");
		if(isset($f->session->tasks['in.rent'])) $data[] = array("name"=>"inRentVen","descr"=>'Vencidas',"icon"=>"ui-icon-calendar");
		if(isset($f->session->tasks['in.rent'])) $data[] = array("name"=>"inRentPro","descr"=>'Protestadas',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_ctas() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['in.ctas'])) $data[] = array("name"=>"inCtasAval","descr"=>'Avales',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['in.ctas'])) $data[] = array("name"=>"inCtasRepr","descr"=>'Representantes',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['in.ctas'])) $data[] = array("name"=>"inCtasArre","descr"=>'Arrendatarios',"icon"=>"ui-icon-contact");
		$f->response->json( $data );
	}
}
?>