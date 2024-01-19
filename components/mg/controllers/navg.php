<?php
class Controller_mg_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['mg.titu'])) $data[] = array("name"=>"mgTitu","descr"=>'Titular',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['mg.orga'])) $data[] = array("name"=>"mgOrga","descr"=>'Estructura Organizacional',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['mg.titu'])) $data[] = array("name"=>"mgVari","descr"=>'Variables Globales',"icon"=>"ui-icon-gear");
		/*if($f->session->tasks['mg.titu'])*/ $data[] = array("name"=>"mgServ","descr"=>'Servicios',"icon"=>"ui-icon-gear");
		/*if($f->session->tasks['mg.titu'])*/ $data[] = array("name"=>"mgOfic","descr"=>'Oficinas',"icon"=>"ui-icon-gear");
		/*if($f->session->tasks['mg.titu'])*/ $data[] = array("name"=>"mgEnti","descr"=>'Entidades',"icon"=>"ui-icon-gear");
		//if($f->session->tasks['mg/tides']) $data[] = array("name"=>"mgTides","descr"=>'Documentos de Identidad',"icon"=>"ui-icon-contact");
		$f->response->json( $data );
	}
}
?>