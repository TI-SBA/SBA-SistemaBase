<?php
class Controller_ts_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		$data[] = array("name"=>"tsCheq","descr"=>'Cheques',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsRede","descr"=>'Recibos Definitivos',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.ctap'])) $data[] = array("name"=>"tsCtpp","descr"=>'Cuentas Por Pagar',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.comp'])) $data[] = array("name"=>"tsComp","descr"=>'Comprobantes de Pago',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.movc.org']) or isset($f->session->tasks['ts.movc.dep'])) $data[] = array("name"=>"tsMocj","descr"=>'Movimientos de Caja Chica',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ts.cajc'])) $data[] = array("name"=>"tsCjch","descr"=>'Cajas Chicas',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ts.conc'])) $data[] = array("name"=>"tsConc","descr"=>'Conceptos',"icon"=>"ui-icon-bookmark");
		if(isset($f->session->tasks['ts.banc'])) $data[] = array("name"=>"tsCtban","descr"=>'Cuentas Bancarias',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.polz.org']) or isset($f->session->tasks['ts.polz.dep'])) $data[] = array("name"=>"tsPoli","descr"=>'P&oacute;lizas Contables',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.movs'])) $data[] = array("name"=>"tsMovi","descr"=>'Movimientos',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.tmed'])) $data[] = array("name"=>"tsTipo","descr"=>'Tipos de Medio de Pago',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.sald'])) $data[] = array("name"=>"tsSald","descr"=>'Saldos',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.reci.org']) or isset($f->session->tasks['ts.reci.dep'])) $data[] = array("name"=>"tsRein","descr"=>'Recibo de Ingresos',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_ctpp() {
		global $f;
		$data = array();
		$data[] = array("name"=>"tsCtppAll","descr"=>'Todas',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsCtppPen","descr"=>'Pendientes',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_mocj() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['ts.movc.org'])) $data[] = array("name"=>"tsMocjAll","descr"=>'Todas las dependencias',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.movc.dep'])) $data[] = array("name"=>"tsMocjDep","descr"=>'Por dependencia',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_comp() {
		global $f;
		$data = array();
		$data[] = array("name"=>"tsCompAll","descr"=>'Todos',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsCompNue","descr"=>'Nuevos',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_poli() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['ts.polz.org'])) $data[] = array("name"=>"tsPoliTod","descr"=>'Todas las dependencias',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.polz.dep'])) $data[] = array("name"=>"tsPoliPor","descr"=>'Por dependencia',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_movi() {
		global $f;
		$data = array();
		$data[] = array("name"=>"tsMoviCjb","descr"=>'Caja - Bancos',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsMoviBan","descr"=>'Bancos',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsMoviCue","descr"=>'Cuentas Corrientes',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsMoviEfe","descr"=>'Efectivo',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_sald() {
		global $f;
		$data = array();
		$data[] = array("name"=>"tsSaldBan","descr"=>'Bancos',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsSaldCue","descr"=>'Cuentas Corrientes',"icon"=>"ui-icon-document");
		$data[] = array("name"=>"tsSaldEfe","descr"=>'Efectivo',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
	function execute_rein() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['ts.reci.org'])) $data[] = array("name"=>"tsReinTod","descr"=>'Todas las dependencias',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ts.reci.dep'])) $data[] = array("name"=>"tsReinPor","descr"=>'Por dependencia',"icon"=>"ui-icon-document");
		$f->response->json( $data );
	}
}
?>