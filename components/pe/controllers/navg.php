<?php
class Controller_pe_navg extends Controller {
	function execute_index() {
		global $f;
		$data = array();
		if(isset($f->session->tasks['pe.pres'])) $data[] = array("name"=>"pePres","descr"=>'Presupuesto Anal&iacute;tico',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['pe.cuas'])) $data[] = array("name"=>"peCuad","descr"=>'Cuadro de Asignaci&oacute;n',"icon"=>"ui-icon-note");
		if(isset($f->session->tasks['pe.asis'])) $data[] = array("name"=>"peCoas","descr"=>'Control de Asistencia',"icon"=>"ui-icon-clipboard");
		if(isset($f->session->tasks['pe.prop'])) $data[] = array("name"=>"peProp","descr"=>'Propinas',"icon"=>"ui-icon-mail-open");
		if(isset($f->session->tasks['pe.plan.276']) or isset($f->session->tasks['pe.plan.cas']) or isset($f->session->tasks['pe.plan.25a']) or isset($f->session->tasks['pe.plan.30a']) or isset($f->session->tasks['pe.plan.vac'])) $data[] = array("name"=>"pePlan","descr"=>'Planillas',"icon"=>"ui-icon-folder-collapsed");
		if(isset($f->session->tasks['pe.turn'])) $data[] = array("name"=>"peTurn","descr"=>'Turnos',"icon"=>"ui-icon-clock");
		if(isset($f->session->tasks['pe.feri'])) $data[] = array("name"=>"peFeri","descr"=>'Feriados',"icon"=>"ui-icon-calendar");
		/*if($f->session->tasks['mg.orga'])*/ //$data[] = array("name"=>"peVaca","descr"=>'Vacaciones',"icon"=>"ui-icon-calendar");
		/*if($f->session->tasks['mg.orga'])*/ //$data[] = array("name"=>"peLice","descr"=>'Licencias',"icon"=>"ui-icon-calendar");
		if(isset($f->session->tasks['pe.ctas.trab.276']) or isset($f->session->tasks['pe.ctas.trab.cas']) or isset($f->session->tasks['pe.ctas.trab.prac'])) $data[] = array("name"=>"peEnti","descr"=>'Cuentas',"icon"=>"ui-icon-contact");
		if(isset($f->session->tasks['pe.perm'])) $data[] = array("name"=>"pePerm","descr"=>'Permisos',"icon"=>"ui-icon-gear");
		if(isset($f->session->tasks['pe.equi'])) $data[] = array("name"=>"peEqui","descr"=>'Equipos',"icon"=>"ui-icon-gear");
		if(isset($f->session->tasks['pe.conc'])) $data[] = array("name"=>"peConc","descr"=>'Conceptos',"icon"=>"ui-icon-copy");
		if(isset($f->session->tasks['pe.tcon'])) $data[] = array("name"=>"peCont","descr"=>'Tipos de Contrato',"icon"=>"ui-icon-script");
		if(isset($f->session->tasks['pe.pens'])) $data[] = array("name"=>"peSist","descr"=>'Sistemas de Pensiones',"icon"=>"ui-icon-suitcase");
		if(isset($f->session->tasks['pe.tinc'])) $data[] = array("name"=>"peTipo","descr"=>'Tipos de Incidencia',"icon"=>"ui-icon-tag");
		if(isset($f->session->tasks['pe.nivr'])) $data[] = array("name"=>"peNive","descr"=>'Niveles Remunerativos',"icon"=>"ui-icon-signal");
		if(isset($f->session->tasks['pe.carg'])) $data[] = array("name"=>"peCarg","descr"=>'Cargos',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['pe.carc'])) $data[] = array("name"=>"peClas","descr"=>'Cargos Clasificados',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['pe.grup'])) $data[] = array("name"=>"peGrup","descr"=>'Grupos Ocupacionales',"icon"=>"ui-icon-person");
		if(isset($f->session->tasks['pe.reps'])) $data[] = array("name"=>"peRepo","descr"=>'Reportes',"icon"=>"ui-icon-signal");
		$f->response->json( $data );
	}
	function execute_enti(){
		global $f;
		$data = array();
		if(isset($f->session->tasks['pe.ctas.trab.prac'])) $data[] = array("name"=>"peEntiPra","descr"=>'Practicantes',"icon"=>"ui-icon-contact");
		/*if($f->session->tasks['pe.ctas.trab.cas']) $data[] = array("name"=>"peEntiCas","descr"=>'Trabajadores CAS',"icon"=>"ui-icon-contact");
		if($f->session->tasks['pe.ctas.trab.276']) $data[] = array("name"=>"peEntiPer","descr"=>'Trabajadores 276',"icon"=>"ui-icon-contact");*/
		$bd = $f->datastore->pe_tipos_contratos->find(array('estado'=>'H'));
		foreach ($bd as $ob) {
		    $data[] = array("name"=>"peEntiTrab".$ob['cod'],"descr"=>$ob['nomb'],"icon"=>"ui-icon-contact");
		}
		$f->response->json( $data );
	}
	function execute_coas(){
		global $f;
		$data = array();
		if(isset($f->session->tasks['pe.asis'])) $data[] = array("name"=>"peCoasInci","descr"=>'Incidencias',"icon"=>"ui-icon-notice");
		if(isset($f->session->tasks['pe.asis'])) $data[] = array("name"=>"peCoasAsis","descr"=>'Asistencia',"icon"=>"ui-icon-check");
		if(isset($f->session->tasks['pe.asis'])) $data[] = array("name"=>"peCoasProg","descr"=>'Programaci&oacute;n de Incidencias',"icon"=>"ui-icon-calculator");
		if(isset($f->session->tasks['pe.asis'])) $data[] = array("name"=>"peCoasHora","descr"=>'Horarios',"icon"=>"ui-icon-calculator");
		$f->response->json( $data );
	}
	function execute_plan(){
		global $f;
		$data = array();
		if(isset($f->session->tasks['pe.plan'])) $data[] = array("name"=>"pePlanQui","descr"=>'Liquidaci&oacute;n de Quinta Categor&iacute;a',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['pe.plan'])) $data[] = array("name"=>"pePlanMat","descr"=>'Reembolso Subsidio por Maternidad',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['pe.plan'])) $data[] = array("name"=>"pePlanEnf","descr"=>'Subsidios por enfermedad',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['pe.plan'])) $data[] = array("name"=>"pePlanSep","descr"=>'Subsidios por Sepelio',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['pe.plan.vac'])) $data[] = array("name"=>"pePlanVac","descr"=>'Compensaci&oacute;n Vacacional',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['pe.plan.30a'])) $data[] = array("name"=>"pePlanTre","descr"=>'Compensaci&oacute;n 30 a&ntilde;os',"icon"=>"ui-icon-document");
		if(isset($f->session->tasks['pe.plan.25a'])) $data[] = array("name"=>"pePlanVei","descr"=>'Compensaci&oacute;n 25 a&ntilde;os',"icon"=>"ui-icon-document");
		$bd = $f->datastore->pe_tipos_contratos->find(array('estado'=>'H'));
		foreach ($bd as $ob) {
		    $data[] = array("name"=>"pePlanBole".$ob['cod'],"descr"=>$ob['nomb'],"icon"=>"ui-icon-contact");
		}
		$f->response->json( $data );
	}
	/*function execute_conc(){
		global $f;
		$data = array();
		/*if($f->session->tasks['mg.orga'])* $data[] = array("name"=>"peConcCas","descr"=>'CAS',"icon"=>"ui-icon-copy");
		/*if($f->session->tasks['mg.orga'])* $data[] = array("name"=>"peConcPer","descr"=>'276',"icon"=>"ui-icon-copy");
		$f->response->json( $data );
	}*/
	function execute_repo(){
		global $f;
		$data = array();
		/*if($f->session->tasks['mg.orga'])*/ $data[] = array("name"=>"peRepoInci","descr"=>'Incidencias',"icon"=>"ui-icon-clipboard");
		$data[] = array("name"=>"peRepoGene","descr"=>'Reportes Generales',"icon"=>"ui-icon-clipboard");
		$f->response->json( $data );
	}
}
?>