<?php
class Controller_mg_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if($f->session->tasks['mg.titu']) $data[] = array("name"=>"mgTitu","descr"=>'Titular',"icon"=>"ui-icon-person");
		if($f->session->tasks['mg.orga']) $data[] = array("name"=>"mgOrga","descr"=>'Estructura Organizacional',"icon"=>"ui-icon-note");
		if($f->session->tasks['mg.titu']) $data[] = array("name"=>"mgVari","descr"=>'Variables Globales',"icon"=>"ui-icon-gear");
		//if($f->session->tasks['mg/tides']) $data[] = array("name"=>"mgTides","descr"=>'Documentos de Identidad',"icon"=>"ui-icon-contact");
		$f->response->json( $data );
	}
}
?>