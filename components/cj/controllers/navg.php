<?php
class Controller_cj_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['cj.caja'])) $data[] = array("name"=>"cjCaja","descr"=>'Cajas',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.taln'])) $data[] = array("name"=>"cjTalo","descr"=>'Talonarios',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.conc'])) $data[] = array("name"=>"cjConc","descr"=>'Conceptos',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.ccob.org']) or isset($f->session->tasks['cj.ccob.dep'])) $data[] = array("name"=>"cjCuen","descr"=>'Cuentas por Cobrar',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.ctas'])) $data[] = array("name"=>"cjEnti","descr"=>'Cuentas',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['cj.ctas'])) $data[] = array("name"=>"cjInmu","descr"=>'Inmuebles',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['cj.ctas'])) $data[] = array("name"=>"cjCeme","descr"=>'Cementerio',"icon"=>"ui-icon-person");
		//if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjComp","descr"=>'Comprobantes',"icon"=>"ui-icon-person");
		
		
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjCompRec","descr"=>'Recibos de Caja',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjCompBol","descr"=>'Boletas de Venta',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.ecom'])) $data[] = array("name"=>"cjEcom","descr"=>'Comprobantes de Pago Electr&oacute;nico',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjCompFac","descr"=>'Facturas',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjCompPen","descr"=>'Pendientes de Cambio de Nombre',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.comp'])) $data[] = array("name"=>"cjRede","descr"=>'Recibos Definitivos',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['cj.reps'])) $data[] = array("name"=>"cjRepo","descr"=>'Reportes',"icon"=>"ui-icon-person");
		$f->response->json( $data );
	}
	function execute_cuen() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['cj.ccob.org'])) $data[] = array("name"=>"cjCuenTod","descr"=>'Todas las dependencias',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.ccob.dep'])) $data[] = array("name"=>"cjCuenPor","descr"=>'Por dependencia',"icon"=>"ui-icon-note");
		$f->response->json( $data );
	}
	function execute_enti() {
		global $f;
		$data = array();
		$data[] = array("name"=>"cjEntiCaje","descr"=>'Cajeros',"icon"=>"ui-icon-note");
		$data[] = array("name"=>"cjEntiClie","descr"=>'Clientes',"icon"=>"ui-icon-note");
		$f->response->json( $data );
	}
	function execute_comp() {
		global $f;
		$data = array();
		$data[] = array("name"=>"cjCompPen","descr"=>'Pendientes de Cambio de Nombre',"icon"=>"ui-icon-note");
		$data[] = array("name"=>"cjCompRec","descr"=>'Recibos de Caja',"icon"=>"ui-icon-note");
		$data[] = array("name"=>"cjCompBol","descr"=>'Boletas de Venta',"icon"=>"ui-icon-note");
		$data[] = array("name"=>"cjCompFac","descr"=>'Facturas',"icon"=>"ui-icon-note");
		$f->response->json( $data );
	}
	function execute_ceme() {
		global $f;
		$data = array();
		$data[] = array("name"=>"cjCeme","descr"=>'Cuenta por Cobrar',"icon"=>"ui-icon-note");
		$data[] = array("name"=>"cjCompRec","descr"=>'Recibos de Caja',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['cj.reps'])) $data[] = array("name"=>"cjRepo","descr"=>'Reportes',"icon"=>"ui-icon-person");
		$f->response->json( $data );
	}
}
?>