<?php
class Controller_ct_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['ct.plan'])) $data[] = array("name"=>"ctPcon","descr"=>'Plan Contable',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ct.tnot'])) $data[] = array("name"=>"ctTnot","descr"=>'Tipos de Nota',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ct.plac'])) $data[] = array("name"=>"ctPcue","descr"=>'Plan de Cuentas',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ct.codl'])) $data[] = array("name"=>"ctColi","descr"=>'C&oacute;digos de Libros',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['ct.notc'])) $data[] = array("name"=>"ctNotc","descr"=>'Notas de Contabilidad',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.notf'])) $data[] = array("name"=>"ctNota","descr"=>'Nota a los estados financieros',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.regc'])) $data[] = array("name"=>"ctRcom","descr"=>'Registro de Compras',"icon"=>"ui-icon-bookmark");
		if(isset($f->session->tasks['ct.regv'])) $data[] = array("name"=>"ctRven","descr"=>'Registro de Ventas',"icon"=>"ui-icon-bookmark");
		///*if($f->session->tasks['cm.ctas'])*/ $data[] = array("name"=>"ctLibr","descr"=>'Libro',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['ct.conp'])) $data[] = array("name"=>"ctCpat","descr"=>'Control Patrimonial',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.conb'])) $data[] = array("name"=>"ctCban","descr"=>'Conciliaciones Bancarias',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.comp'])) $data[] = array("name"=>"ctTico","descr"=>'Tipos de Comprobante de Pago',"icon"=>"ui-icon-tag");
		/*if($f->session->tasks['ct.libm'])*/ $data[] = array("name"=>"ctOrde","descr"=>'Logistica',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['ct.auxe'])) $data[] = array("name"=>"ctAuxs","descr"=>'Auxiliares Standard',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.ejpr'])) $data[] = array("name"=>"ctEpres","descr"=>'Ejecuci&oacute;n Presupuestaria',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.mayr'])) $data[] = array("name"=>"ctMayc","descr"=>'Mayorizaci&oacute;n de Cuentas',"icon"=>"ui-icon-tag");
		/*if($f->session->tasks['ct.movc']) $data[] = array("name"=>"ctMocu","descr"=>'Movimiento de Cuentas',"icon"=>"ui-icon-tag");*/
		if(isset($f->session->tasks['ct.almc'])) $data[] = array("name"=>"ctCoal","descr"=>'Contabilidad de Almac&eacute;n',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['ct.libd'])) $data[] = array("name"=>"ctLidi","descr"=>'Libro Diario',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['ct.libm'])) $data[] = array("name"=>"ctLima","descr"=>'Libro Mayor',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['ct.reps'])) $data[] = array("name"=>"ctRepo","descr"=>'Reportes',"icon"=>"ui-icon-contact");
		$f->response->json( $data );
	}
	function execute_orde() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctOrdeComp","descr"=>'Ordenes de Compra',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctOrdeServ","descr"=>'Ordenes de Servicios',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_nota() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctNotaNum","descr"=>'N&uacute;mericas',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctNotaLit","descr"=>'Literales',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_epres() {
		global $f;
		$data = array();
		//$data[] = array("name"=>"ctEpresMovi","descr"=>'Movimientos de Cuentas',"icon"=>"ui-icon-search");
		//$data[] = array("name"=>"ctEpresBala","descr"=>'Balance de Ingresos y Gastos',"icon"=>"ui-icon-search");
		//$data[] = array("name"=>"ctEpresPpres","descr"=>'Proceso Presupuestario',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctEpresCuadce","descr"=>'Cuadro del Compr. Y Ejec.',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctEpresAuxG","descr"=>'Auxiliar de Gasto',"icon"=>"ui-icon-search");		
		$data[] = array("name"=>"ctEpresAuxI","descr"=>'Auxiliar de Ingreso',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_auxs() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctAuxsDeor","descr"=>'De Orden',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsPres","descr"=>'Presupuesto',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsResu","descr"=>'Resultados',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsGast","descr"=>'Gasto',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsIngr","descr"=>'Ingreso',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsPatr","descr"=>'Patrimonio',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsPasi","descr"=>'Pasivo',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctAuxsActi","descr"=>'Activo',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_mayc() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctMaycDeor","descr"=>'De Orden',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctMaycResu","descr"=>'Resultados',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctMaycPasi","descr"=>'Pasivo',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctMaycActi","descr"=>'Activo',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_mocu() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctMocuDeor","descr"=>'De Orden',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctMocuResu","descr"=>'Resultados',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctMocuPasi","descr"=>'Pasivo',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctMocuActi","descr"=>'Activo',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_coal() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctCoalSali","descr"=>'Salidas',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctCoalEntr","descr"=>'Entradas',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_lidi() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctLidiSuna","descr"=>'SUNAT',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctLidiBene","descr"=>'Beneficencia',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
	function execute_lima() {
		global $f;
		$data = array();
		$data[] = array("name"=>"ctLimaSuna","descr"=>'SUNAT',"icon"=>"ui-icon-search");
		$data[] = array("name"=>"ctLimaBene","descr"=>'Beneficencia',"icon"=>"ui-icon-search");
		$f->response->json( $data );
	}
}
?>