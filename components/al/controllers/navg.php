<?php
class Controller_al_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['al.dilg'])) $data[] = array("name"=>"alDili","descr"=>'Diligencias',"icon"=>"ui-icon-suitcase");
		if(isset($f->session->tasks['al.expd'])) $data[] = array("name"=>"alExpd","descr"=>'Expedientes',"icon"=>"ui-icon-folder-collapsed");		
		if(isset($f->session->tasks['al.cont'])) $data[] = array("name"=>"alCont","descr"=>'Contigencias',"icon"=>"ui-icon-bookmark");	
		if(isset($f->session->tasks['al.conv'])) $data[] = array("name"=>"alConv","descr"=>'Convenios',"icon"=>"ui-icon-script");
		if(isset($f->session->tasks['al.reps'])) $data[] = array("name"=>"alRepo","descr"=>'Reportes',"icon"=>"ui-icon-script");			
		$f->response->json( $data );
	}
	function execute_expd(){
		global $f;
		$data = array();		
		$data[] = array("name"=>"alExpdArch","descr"=>'Archivados',"icon"=>"ui-icon-folder-collapsed");
		$data[] = array("name"=>"alExpdActi","descr"=>'Activos',"icon"=>"ui-icon-notice");
		$f->response->json( $data );
	}
	function execute_cont(){
		global $f;
		$data = array();	
		$data[] = array("name"=>"alContCont","descr"=>'En Contra',"icon"=>"ui-icon-closethick");
		$data[] = array("name"=>"alContFav","descr"=>'A Favor',"icon"=>"ui-icon-plusthick");	
		$f->response->json( $data );
	}
	function execute_dili(){
		global $f;
		$data = array();		
		$data[] = array("name"=>"alDiliSusp","descr"=>'Suspendidas',"icon"=>"ui-icon-cancel");
		$data[] = array("name"=>"alDiliEjec","descr"=>'Ejecutadas',"icon"=>"ui-icon-check");
		$data[] = array("name"=>"alDiliProg","descr"=>'Programadas',"icon"=>"ui-icon-calendar");
		$f->response->json( $data );
	}
}
?>