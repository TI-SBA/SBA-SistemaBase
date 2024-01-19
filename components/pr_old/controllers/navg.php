<?php
class Controller_pr_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['pr.pres.pim']) or isset($f->session->tasks['pr.pres.pia'])) $data[] = array("name"=>"prPres","descr"=>'Presupuesto Institucional',"icon"=>"ui-icon-bookmark");
		if(isset($f->session->tasks['pr.plan.ejec']) or isset($f->session->tasks['pr.plan.prog'])) $data[] = array("name"=>"prPlan","descr"=>'Plan Operativo',"icon"=>"ui-icon-bookmark");		
		if(isset($f->session->tasks['pr.clas'])) $data[] = array("name"=>"prClas","descr"=>'Clasificadores',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['pr.fuen'])) $data[] = array("name"=>"prFuen","descr"=>'Fuentes de Financiamiento',"icon"=>"ui-icon-document-b");		
		if(isset($f->session->tasks['pr.umed'])) $data[] = array("name"=>"prUnid","descr"=>'Unidades de Medida',"icon"=>"ui-icon-signal");
		if(isset($f->session->tasks['pr.func'])) $data[] = array("name"=>"prEstr","descr"=>'Estructura Funcional',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['pr.prog'])) $data[] = array("name"=>"prEprog","descr"=>'Estructura Programatica',"icon"=>"ui-icon-tag");		
		if(isset($f->session->tasks['pr.actv'])) $data[] = array("name"=>"prActi","descr"=>'Actividades y Componentes',"icon"=>"ui-icon-tag");
		/*if($f->session->tasks['pr.meta'])*/ $data[] = array("name"=>"prMeta","descr"=>'Metas',"icon"=>"ui-icon-tag");
		/*if($f->session->tasks['pr.meta'])*/ $data[] = array("name"=>"prMefi","descr"=>'Metas Fisicas',"icon"=>"ui-icon-tag");
		/*if($f->session->tasks['pr.meta'])*/ $data[] = array("name"=>"prRese","descr"=>'Reservas Presupuestales',"icon"=>"ui-icon-tag");
		/*if($f->session->tasks['pr.meta'])*/ //$data[] = array("name"=>"prSald","descr"=>'Saldos Presupuestales',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['pr.reps'])) $data[] = array("name"=>"prRepo","descr"=>'Reportes',"icon"=>"ui-icon-tag");		
		$f->response->json( $data );
	}
	function execute_pres() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['pr.pres.pim'])) $data[] = array("name"=>"prPresModi_Cred","descr"=>'Creditos Suplementarios',"icon"=>"ui-icon-pencil");
		if(isset($f->session->tasks['pr.pres.pim'])) $data[] = array("name"=>"prPresModi_Nota","descr"=>'Notas Modificatorias',"icon"=>"ui-icon-pencil");
		if(isset($f->session->tasks['pr.pres.pim'])) $data[] = array("name"=>"prPresModi","descr"=>'Modificación',"icon"=>"ui-icon-pencil");
		if(isset($f->session->tasks['pr.pres.pia'])) $data[] = array("name"=>"prPresAper","descr"=>'Apertura',"icon"=>"ui-icon-calendar");
		$f->response->json( $data );
	}
	function execute_plan() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['pr.plan.ejec'])) $data[] = array("name"=>"prPlanEjec","descr"=>'Ejecuci&oacute;n',"icon"=>"ui-icon-check");
		if(isset($f->session->tasks['pr.plan.prog'])) $data[] = array("name"=>"prPlanProg","descr"=>'Programaci&oacute;n',"icon"=>"ui-icon-calendar");		
		$f->response->json( $data );
	}
}
?>