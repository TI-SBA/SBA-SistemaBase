<?php
global $f;
$baseURL = $f->request->root;
$templateURL = $baseURL . 'themes/' . $f->config('template', 'name');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html style="height: 100%; overflow: hidden;" xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Sistema</title>
	<link type="image/x-icon" rel="shortcut icon" href="<?=$baseURL?>images/favicon.ico">
	<link name="css_jquery_ui" rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/redmond/jquery-ui.css" />
	<!--<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/custom-theme/jquery-ui-1.10.0.custom.css">-->
	<link name="css_kunan" type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/kunan.css">
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/dashboardui.css">
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/fileuploader.css">
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/jquery.noty.css">
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/tree_component.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/jquery.pnotify.default.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/jquery.svg.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/fullcalendar.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/jquery.ui.timepicker.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/jquery.ui.chatbox.css" />
	<script type="text/javascript" src="<?=$baseURL?>scripts/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/jquery-ui.1.9.1.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/fileuploader.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.ba-resize.min.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.scrollTo.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.editable-select.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.custom.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.json-2.2.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jstorage.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.layout-latest.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.numeric.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.jkey-1.2.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.dashboard.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.contextmenu.r2.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.blockUI.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.pnotify.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/_lib/_all.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/tree_component.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.ui.datepicker-es.js"></script>
	<!--<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.ui.timepicker.js"></script>-->
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.svg.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/sylvester.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/purecssmatrix.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.animtrans.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.zoomooz.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/fullcalendar.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/globalize.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/globalize.culture.es-ES.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery-ui.multidatespicker.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.jqGrid.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/moment.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/highcharts/highcharts.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/highcharts/modules/exporting.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/math.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.countdown.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/kunan.js"></script>
	<!--<script type="text/javascript" src="http://server/libraries/kunan.js/kunan.js"></script>-->
</head>
<body style="background: #fff">
<div style="position: absolute; z-index: 1000; width: 100%; height: 100%; opacity: 0.5; background-color: rgb(255, 255, 255);" id="K_preload">
	<div style="position: absolute;left: 50%;top: 50%;height: 200px;margin-top: -100px;width: 300px;margin-left: -150px;text-align:center;">
		<label style="font-size: 18px;">Cargando...</label><br />
		<img src="<?php echo($templateURL); ?>/images/ajax-loader_over_3.gif">
	</div>
</div>
<div id="desktop">
	<div id="desktopHeader">
		<div id="desktopTitlebarWrapper">
			<div id="desktopTitlebar">
				<label style="float: left;">Sistema</label>
				<label><?php echo $f->session->titular['_id']; ?></label>
				<label style="float: right;" id="menUser">
					<ul style="float: right;">
						<li class="tahoma bg"><a href="#">Cambiar Tema</a>
							<ul id="themes">
								<li class="tahome"><a href="jquery-ui.excite-bike.css" title="Excite Bike"><img src="images/themethumbs/themenail_excitebike.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.humanity.css" title="Humanity"><img src="images/themethumbs/theme_90_humanity.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.pepper-grinder.css" title="Pepper Grinder"><img src="images/themethumbs/theme_90_pepper_grinder.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.redmond.css" title="Redmond"><img src="images/themethumbs/theme_redmond.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.smoothness.css" title="Smoothness"><img src="images/themethumbs/themenail_smoothness.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.south-street.css" title="South Street"><img src="images/themethumbs/theme_90_south_street.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.sunny.css" title="Sunny"><img src="images/themethumbs/theme_90_sunny.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.trontastic.css" title="Trontastic"><img src="images/themethumbs/theme_90_trontastic.png" alt="image" width="80" height="90" /></a></li>
								<li class="tahome"><a href="jquery-ui.ui-lightness.css" title="Lightness"><img src="images/themethumbs/theme_90_ui_light.png.gif" alt="image" width="80" height="90" /></a></li>
							</ul>
						</li>
						<li class="tahoma bg"><a href="#">Ayuda</a>
							<ul>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.1-Introduccion.pdf">Introduccion</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.3-MaestrosGenerales.pdf">3-MaestrosGenerales</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.4-TramiteDocumentario.pdf">4-TramiteDocumentario</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.5-Cementerios.pdf">5-Cementerios</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.6-Inmuebles.pdf">6-Inmuebles</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.7-Logistica.pdf">7-Logistica</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.8-Personal.pdf">8-Personal</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.9-Presupuesto.pdf">9-Presupuesto</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.10-AsesoriaLegal.pdf">10-AsesoriaLegal</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.11-Caja.pdf">11-Caja</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.12-Tesoreria.pdf">12-Tesoreria</a></li>
								<li class="tahome"><a target="_blank" href="ayuda/Manual.13-Contabilidad.pdf">13-Contabilidad</a></li>
							</ul>	
						</li>
						<li class="tahoma bg" id="nameUser"><a href="#"><?php echo $f->session->user['userid']; ?></a>
							<ul>
								<li class="tahoma" id="menUserDocs"><a href="#">Configuraci&oacute;n</a></li>
								<li class="tahoma" id="menUserVar"><a href="#">Informar de un error</a></li>
								<li id="logout" class="tahoma divider"><a href="ci/index/logout">Cerrar sesi&oacute;n</a></li>
							</ul>
						</li>
					</ul>
				</label>
			</div>
		</div>
	</div><!-- desktopHeader end -->

	<div id="dockWrapper">
		<div id="dock" class="dock">
			<div id="dockMini"></div>
			<div id="dockClose"></div>
		</div>
	</div>
	
	<div id="pageWrapper">
		<div id="pageWrapperLeft" class="ui-layout-west tahoma">
			<div class="grid" style="overflow: hidden;">
				<div id="NavBar" class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li style="min-width:197px;max-width:197px" class="ui-button ui-widget ui-state-default ui-button-text-only" filter="_id">
							<label class="tahoma">M&oacute;dulo</label>
							<span class="ui-icon ui-icon-triangle-1-s" style="left: 15px;"></span>
							<ul>
								<!--<li class="tahoma" id="init"><a>Inicio</a></li>-->
								<li class="tahoma" id="ch"><a>Perfil de Usuario</a></li>
								<?php if(isset($f->session->tasks['mg'])){ ?><li class="tahoma" id="mg"><a>Maestros Generales</a></li><?php } ?>
								<?php if(isset($f->session->tasks['po'])){ ?><li class="tahoma" id="po"><a>Porter&iacute;a</a></li><?php } ?>
								<?php if(isset($f->session->tasks['td'])){ ?><li class="tahoma" id="td"><a>Tr&aacute;mite Documentario</a></li><?php } ?>
								<?php if(isset($f->session->tasks['cm'])){ ?><li class="tahoma" id="cm"><a>Cementerio</a></li><?php } ?>
								<?php if(isset($f->session->tasks['in'])){ ?><li class="tahoma" id="in"><a>Inmuebles</a></li><?php } ?>
								<!--<?php if(isset($f->session->tasks['lg'])){ ?><li class="tahoma" id="lg_new"><a>Log&iacute;stica</a></li><?php } ?>
								<?php if($f->session->user['userid']=='admin'){ ?><li class="tahoma" id="lg"><a>Log&iacute;stica (antiguo)</a></li><?php } ?>-->
								<?php if(isset($f->session->tasks['lg'])){ ?><li class="tahoma" id="lg"><a>Log&iacute;stica</a></li><?php } ?>
								<?php if(isset($f->session->tasks['dd'])){ ?><li class="tahoma" id="ac"><a>Archivo Central</a></li><?php } ?>
								<?php if(isset($f->session->tasks['us'])){ ?><li class="tahoma" id="us"><a>U.S.A.</a></li><?php } ?>
								<?php if(isset($f->session->tasks['pr'])){ ?><li class="tahoma" id="pr"><a>Planificaci&oacute;n y Presupuesto</a></li><?php } ?>
								<?php if(isset($f->session->tasks['pe'])){ ?><li class="tahoma" id="pe"><a>Personal</a></li><?php } ?>
								<?php if(isset($f->session->tasks['al'])){ ?><li class="tahoma" id="al"><a>Asesor&iacute;a Legal</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ct'])){ ?><li class="tahoma" id="ct"><a>Contabilidad</a></li><?php } ?>
								<?php if(isset($f->session->tasks['cj'])){ ?><li class="tahoma" id="cj"><a>Caja</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ts'])){ ?><li class="tahoma" id="ts"><a>Tesorer&iacute;a</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ho'])){ ?><li class="tahoma" id="ho"><a>Moises Heresi</a></li><?php } ?>
								<?php if(isset($f->session->tasks['fa'])){ ?><li class="tahoma" id="fa"><a>Farmacia</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ag'])){ ?><li class="tahoma" id="ag"><a>Agua CHAPI</a></li><?php } ?>
								<?php if(isset($f->session->tasks['bj'])){ ?><li class="tahoma" id="bj"><a>Balneario</a></li><?php } ?>
								<?php if(isset($f->session->tasks['re'])){ ?><li class="tahoma" id="re"><a>Recursos Econ&oacute;micos</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ar'])){ ?><li class="tahoma" id="ar"><a>Archivo Digital</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ti'])){ ?><li class="tahoma" id="ti"><a>Inform&aacute;tica</a></li><?php } ?>
								<?php if(isset($f->session->tasks['ti'])){ ?><li class="tahoma" id="ac"><a>Seguridad</a></li><?php } ?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
			<div class="grid">
				<div class="gridBody"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:196px;max-width:196px;"></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="ui-layout-center">
			<div id="titleBar">
				<span class="title"></span>
				<div id="divSpinner">
					<div id="spinnerWrapper"><div id="spinner"></div></div>
				</div>
				<div id="iconNoty" class="iconTitleBar vtip" title="Notificaciones">
					<label>0</label>
					<span class="ui-icon ui-icon-comment"></span>
				</div>
				<div id="iconChat" class="iconTitleBar vtip" title="Chat">
					<label>0</label>
					<span class="ui-icon ui-icon-person"></span>
				</div>
				<div id="cleanData" class="iconTitleBar vtip" title="Limpiar Data">
					<span class="ui-icon ui-icon-trash"></span>
				</div>
				<!--<div id="divSwitcher">
					<div id="switcher"></div>
				</div>-->
			</div>
			<div id="pageWrapperMain"></div>
		</div>
	</div>
</div>
<!-- desktop end -->
<div id="contextMenu">
	<div class="contextMenu" id="conMenWindows">
	    <ul>
	    	<li id="conMenWindows_Res"><span class="ui-icon ui-icon-restore"></span>Restaurar</li>
	    	<li id="conMenWindows_Max"><span class="ui-icon ui-icon-max"></span>Maximizar</li>
	    	<li id="conMenWindows_Min"><span class="ui-icon ui-icon-minusthick"></span>Minimizar</li>
	    	<li id="conMenWindows_Cer"><span class="ui-icon ui-icon-closethick"></span>Cerrar</li>
	    	<li id="conMenWindows_about"><span class="ui-icon ui-icon-info"></span>Acerca de...</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenBody">
	    <ul>
	    	<li id="conMenBody_about"><span class="ui-icon ui-icon-info"></span>Acerca de...</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenList">
	    <ul>
	    	<li id="conMenList_edi"><span class="ui-icon ui-icon-pencil"></span>Editar</li>
	    	<li id="conMenList_eli"><span class="ui-icon ui-icon-trash"></span>Eliminar</li>
	    	<li id="conMenList_imp"><span class="ui-icon ui-icon-print"></span>Imprimir</li>
	    	<li id="conMenList_about"><span class="ui-icon ui-icon-info"></span>Acerca de...</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenListSel">
	    <ul>
	    	<li id="conMenListSel_sel"><span class="ui-icon ui-icon-info"></span>Seleccionar</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenListEd">
	    <ul>
	    	<li id="conMenListEd_ver"><span class="ui-icon ui-icon-search"></span>Ver Detalles</li>
	    	<li id="conMenListEd_edi"><span class="ui-icon ui-icon-pencil"></span>Editar</li>
	    	<li id="conMenListEd_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar</li>
	    	<li id="conMenListEd_des"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenListAp">
	    <ul>
	    	<li id="conMenListAp_ver"><span class="ui-icon ui-icon-search"></span>Ver Detalles</li>
	    	<li id="conMenListAp_edi"><span class="ui-icon ui-icon-pencil"></span>Editar</li>
	    	<li id="conMenListAp_apr"><span class="ui-icon ui-icon-circle-check"></span>Aprobar Permiso</li>
	    	<li id="conMenListAp_anu"><span class="ui-icon ui-icon-circle-close"></span>Anular Permiso</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenGest">
	    <ul>
	    	<li id="conMenGest_agr"><span class="ui-icon ui-icon-folder-collapsed"></span>Nuevo Expediente</li>
	    	<li id="conMenGest_exp"><span class="ui-icon ui-icon-search"></span>Ver Expedientes</li>
	    	<li id="conMenGest_edi"><span class="ui-icon ui-icon-pencil"></span>Editar</li>
	    	<li id="conMenGest_eli"><span class="ui-icon ui-icon-trash"></span>Eliminar</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenExpd">
	    <ul>
	    	<li id="conMenExpd_doc"><span class="ui-icon ui-icon-document"></span>Agregar Documento</li>
	    	<li id="conMenExpd_env"><span class="ui-icon ui-icon-mail-closed"></span>Enviar</li>
	    	<li id="conMenExpd_ext"><span class="ui-icon ui-icon-extlink"></span>Enviar a organizaci&oacute;n externa</li>
	    	<li id="conMenExpd_ret"><span class="ui-icon ui-icon-arrowrefresh-1-w"></span>Retorno de Expediente a la SBPA</li>
	    	<li id="conMenExpd_acp"><span class="ui-icon ui-icon-check"></span>Aceptar</li>
	    	<li id="conMenExpd_acf"><span class="ui-icon ui-icon-check"></span>Recibido</li>
	    	<li id="conMenExpd_rec"><span class="ui-icon ui-icon-closethick"></span>Rechazar</li>
	    	<li id="conMenExpd_rcd"><span class="ui-icon ui-icon-folder-open"></span>Reconsiderar</li>
	    	<li id="conMenExpd_ape"><span class="ui-icon ui-icon-folder-open"></span>Apelar</li>
	    	<li id="conMenExpd_con"><span class="ui-icon ui-icon-circle-check"></span>Concluir</li>
	    	<li id="conMenExpd_ach"><span class="ui-icon ui-icon-folder-collapsed"></span>Archivar</li>
	    	<li id="conMenExpd_imp"><span class="ui-icon ui-icon-print"></span>Imprimir</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCmOper">
	    <ul>
	    	<li id="conMenCmOper_pro"><span class="ui-icon ui-icon-contact"></span>Detalles de Propietario</li>
	    	<li id="conMenCmOper_ocu"><span class="ui-icon ui-icon-contact"></span>Detalles de Ocupante</li>
	    	<li id="conMenCmOper_editPro"><span class="ui-icon ui-icon-pencil"></span>Editar Propietario</li>
	    	<!--  <li id="conMenCmOper_editOcu">Editar Ocupante</li>-->
	    	<li id="conMenCmOper_coc"><span class="ui-icon ui-icon-bookmark"></span>Concesi&oacute;n</li>
	    	<li id="conMenCmOper_asi"><span class="ui-icon ui-icon-tag"></span>Asignaci&oacute;n</li>
	    	<li id="conMenCmOper_cos"><span class="ui-icon ui-icon-home"></span>Construcci&oacute;n</li>
	    	<li id="conMenCmOper_amp"><span class="ui-icon ui-icon-home"></span>Ampliaci&oacute;n</li>
	    	<li id="conMenCmOper_inh"><span class="ui-icon ui-icon-person"></span>Inhumaci&oacute;n</li>
	    	<li id="conMenCmOper_traInt"><span class="ui-icon ui-icon-transfer-e-w"></span>Traslado Interno</li>
	    	<li id="conMenCmOper_traExt"><span class="ui-icon ui-icon-extlink"></span>Traslado Externo (hacia otro cementerio)</li>
	    	<li id="conMenCmOper_traExtExt"><span class="ui-icon ui-icon-extlink"></span>Traslado Externo (desde otro cementerio)</li>
	    	<li id="conMenCmOper_col"><span class="ui-icon ui-icon-copy"></span>Colocaci&oacute;n</li>
	    	<li id="conMenCmOper_trs"><span class="ui-icon ui-icon-bookmark"></span>Traspaso</li>
	    	<li id="conMenCmOper_anc"><span class="ui-icon ui-icon-cancel"></span>Finalizar Concesi&oacute;n</li>
	    	<li id="conMenCmOper_ana"><span class="ui-icon ui-icon-tag"></span>Reasignaci&oacute;n</li>
	    	<li id="conMenCmOper_regiOcup"><span class="ui-icon ui-icon-tag"></span>Registrar Ocupante Anterior</li>
	    	<li id="conMenCmOper_ren"><span class="ui-icon ui-icon-refresh"></span>Renovaci&oacute;n</li>
	    	<li id="conMenCmOper_anu"><span class="ui-icon ui-icon-circle-close"></span>Anular Operaci&oacute;n</li>
	    	<li id="conMenCmOper_con"><span class="ui-icon ui-icon-circle-close"></span>Conversi&oacute;n</li>
	    	<li id="conMenCmOper_rel"><span class="ui-icon ui-icon-circle-close"></span>Ingreso de Ocupantes 2010-2012</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCmOperExe">
	    <ul>
	    	<li id="conMenCmOperExe_eje"><span class="ui-icon ui-icon-clipboard"></span>Ejecutar Operaci&oacute;n</li>
	    	<li id="conMenCmOperExe_cons"><span class="ui-icon ui-icon-clipboard"></span>Ejecutar Construcci&oacute;n</li>
	    	<li id="conMenCmOperExe_amp"><span class="ui-icon ui-icon-clipboard"></span>Ejecutar Ampliaci&oacute;n</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCmOperlist">
	    <ul>
	    	<li id="conMenCmOperList_Det"><span class="ui-icon ui-icon-search"></span>Ver detalles de operaci&oacute;n</li>
	    	<li id="conMenCmOperExe_eje"><span class="ui-icon ui-icon-clipboard"></span>Ejecutar Operaci&oacute;n</li>
	    	<li id="conMenCmOperExe_cons"><span class="ui-icon ui-icon-clipboard"></span>Ejecutar Construcci&oacute;n</li>
	    	<li id="conMenCmOperExe_amp"><span class="ui-icon ui-icon-clipboard"></span>Ejecutar Ampliaci&oacute;n</li>
	    	<li id="conMenCmOperList_ope"><span class="ui-icon ui-icon-pencil"></span>Nueva Operaci&oacute;n</li>
	    	<li id="conMenCmOperList_eli"><span class="ui-icon ui-icon-pencil"></span>Eliminar Operaci&oacute;n</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCmConc">
	    <ul>
	    	<li id="conMenCmConc_ver"><span class="ui-icon ui-icon-search"></span>Ver detalles de operaci&oacute;n</li>
	    	<li id="conMenCmConc_reg"><span class="ui-icon ui-icon-search"></span>Registrar Cobro de Servicio</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCmEspa">
	    <ul>
	    	<li id="conMenCmEspa_ver"><span class="ui-icon ui-icon-search"></span>Ver detalles</li>
	    	<li id="conMenCmEspa_edi"><span class="ui-icon ui-icon-pencil"></span>Editar</li>
	    	<li id="conMenCmEspa_eli"><span class="ui-icon ui-icon-clipboard"></span>Eliminar del Mapa</li>
	    	<li id="conMenCmEspa_ope"><span class="ui-icon ui-icon-pencil"></span>Nueva Operaci&oacute;n</li>
	    	<li id="conMenCmEspa_con"><span class="ui-icon ui-icon-pencil"></span>Nueva Concesi&oacute;n</li>
	    	<li id="conMenCmEspa_reg"><span class="ui-icon ui-icon-pencil"></span>Registrar Ocupante Anterior</li>
	    	<li id="conMenCmEspa_cob"><span class="ui-icon ui-icon-clipboard"></span>Registrar Cobro Administrativo</li>
	    	<li id="conMenCmEspa_his"><span class="ui-icon ui-icon-clipboard"></span>Registro Hist&oacute;rico</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCmOcup">
	    <ul>
	    	<li id="conMenCmOcup_ver"><span class="ui-icon ui-icon-search"></span>Ver detalles</li>
	    	<li id="conMenCmOcup_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Datos de Entidad</li>
	    	<li id="conMenCmOcup_ope"><span class="ui-icon ui-icon-pencil"></span>Nueva Operaci&oacute;n</li>
	    	<li id="conMenCmOcup_cob"><span class="ui-icon ui-icon-clipboard"></span>Registrar Cobro Administrativo</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPeTrab">
	    <ul>
	    	<li id="conMenPeTrab_ver"><span class="ui-icon ui-icon-search"></span>Ver Detalles</li>
	    	<li id="conMenPeTrab_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Trabajador</li>
	    	<li id="conMenPeTrab_ent"><span class="ui-icon ui-icon-person"></span>Editar Entidad</li>
	    	<li id="conMenPeTrab_act"><span class="ui-icon ui-icon-refresh"></span>Actualizar ficha</li>
	    	<li id="conMenPeTrab_vle"><span class="ui-icon ui-icon-search"></span>Ver Legajo</li>
	    	<li id="conMenPeTrab_impleg"><span class="ui-icon ui-icon-print"></span>Imprimir Legajo</li>
	    	<li id="conMenPeTrab_leg"><span class="ui-icon ui-icon-gear"></span>Actualizar Legajo</li>
	    	<li id="conMenPeTrab_agr"><span class="ui-icon ui-icon-gear"></span>Agregar Bonos</li>
	    	<li id="conMenPeTrab_bon"><span class="ui-icon ui-icon-gear"></span>Ver Bonos</li>
	    	<li id="conMenPeTrab_his"><span class="ui-icon ui-icon-calendar"></span>Ver Hist&oacute;rico de Trabajador</li>
	    	<li id="conMenPeTrab_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar</li>
	    	<li id="conMenPeTrab_imp"><span class="ui-icon ui-icon-print"></span>Imprimir Ficha</li>
	    	<li id="conMenPeTrab_des"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar</li>
	    	<li id="conMenPeTrab_eli"><span class="ui-icon ui-icon-trash"></span>Eliminar Trabajador</li>
	    	<li id="conMenPeTrab_ret"><span class="ui-icon ui-icon-trash"></span>Retenci&oacute;n Judicial</li>
	    	<li id="conMenPeTrab_tip"><span class="ui-icon ui-icon-circle-check"></span>Actualizaci&oacute;n de Tipo de Contrato</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPeSubs">
	    <ul>
	    	<li id="conMenPeSubs_ver"><span class="ui-icon ui-icon-search"></span>Ver Subsidio</li>
	    	<li id="conMenPeSubs_imp"><span class="ui-icon ui-icon-print"></span>Imprimir Subsidio</li>
	    	<li id="conMenPeSubs_eli"><span class="ui-icon ui-icon-circle-close"></span>Eliminar Subsidio</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPePres">
	    <ul>
	    	<li id="conMenPePres_ver"><span class="ui-icon ui-icon-search"></span>Ver Presupuesto</li>
	    	<li id="conMenPePres_edi"><span class="ui-icon ui-icon-print"></span>Editar Presupuesto</li>
	    	<li id="conMenPePres_apr"><span class="ui-icon ui-icon-circle-check"></span>Aprobar Presupuesto</li>
	    	<li id="conMenPePres_vig"><span class="ui-icon ui-icon-circle-check"></span>Establecer como vigente</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPeCuad">
	    <ul>
	    	<li id="conMenPeCuad_ver"><span class="ui-icon ui-icon-search"></span>Ver Cuadro</li>
	    	<li id="conMenPeCuad_edi"><span class="ui-icon ui-icon-print"></span>Editar Cuadro</li>
	    	<li id="conMenPeCuad_apr"><span class="ui-icon ui-icon-circle-check"></span>Aprobar Cuadro</li>
	    	<li id="conMenPeCuad_vig"><span class="ui-icon ui-icon-circle-check"></span>Establecer como vigente</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPeAsis">
	    <ul>
	    	<li id="conMenPeAsis_con"><span class="ui-icon ui-icon-search"></span>Consultar Asistencia</li>
	    	<li id="conMenPeAsis_mar"><span class="ui-icon ui-icon-pencil"></span>Resolver Marcaciones</li>
	    	<li id="conMenPeAsis_reg"><span class="ui-icon ui-icon-circle-check"></span>Registrar Asistencia</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPeInci">
	    <ul>
	    	<li id="conMenPeInci_vac"><span class="ui-icon ui-icon-clipboard"></span>Ver Vacaciones</li>
	    	<li id="conMenPeInci_lic"><span class="ui-icon ui-icon-clipboard"></span>Ver Licencias</li>
	    	<li id="conMenPeInci_per"><span class="ui-icon ui-icon-clipboard"></span>Ver Permisos</li>
	    	<li id="conMenPeInci_tol"><span class="ui-icon ui-icon-clipboard"></span>Ver Tolerancias</li>
	    	<li id="conMenPeInci_tar"><span class="ui-icon ui-icon-clipboard"></span>Ver Tardanzas</li>
	    	<li id="conMenPeInci_ina"><span class="ui-icon ui-icon-clipboard"></span>Ver Inasistencias</li>
	    	<li id="conMenPeInci_com"><span class="ui-icon ui-icon-clipboard"></span>Ver Compensaciones</li>
	    	<li id="conMenPeInci_tie"><span class="ui-icon ui-icon-clipboard"></span>Ver Tiempos extras</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPePlan">
	    <ul>
	    	<li id="conMenPePlan_ver"><span class="ui-icon ui-icon-clipboard"></span>Ver</li>
	    	<li id="conMenPePlan_anu"><span class="ui-icon ui-icon-clipboard"></span>Anular</li>
	    	<li id="conMenPePlan_pag"><span class="ui-icon ui-icon-clipboard"></span>Pagar</li>
	    	<li id="conMenPePlan_imp"><span class="ui-icon ui-icon-clipboard"></span>Imprimir</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrEstr">
	    <ul>
	    	<li id="conMenPrEstr_ediFunc"><span class="ui-icon ui-icon-pencil"></span>Editar Funcion</li>
	    	<li id="conMenPrEstr_habFunc"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Funcion</li>
	    	<li id="conMenPrEstr_desFunc"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Funcion</li>
	    	<li id="conMenPrEstr_prog"><span class="ui-icon ui-icon-plusthick"></span>Nueva Divisi&oacute;n Funcional</li>
	    	<li id="conMenPrEstr_ediProg"><span class="ui-icon ui-icon-pencil"></span>Editar Divisi&oacute;n Funcional</li>
	    	<li id="conMenPrEstr_habProg"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Divisi&oacute;n Funcional</li>
	    	<li id="conMenPrEstr_desProg"><span class="ui-icon ui-icon-circle-close"></span>Desabilitar Divisi&oacute;n Funcional</li>
	        <li id="conMenPrEstr_subProg"><span class="ui-icon ui-icon-plusthick"></span>Nuevo Grupo Funcional</li>
			<li id="conMenPrEstr_ediSubProg"><span class="ui-icon ui-icon-pencil"></span>Editar Grupo Funcional</li>
			<li id="conMenPrEstr_habSubProg"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Grupo funcional</li>
			<li id="conMenPrEstr_desSubProg"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Grupo funcional</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrEprog">
	    <ul>
	    	<li id="conMenPrEprog_ediCat"><span class="ui-icon ui-icon-pencil"></span>Editar Categor&iacute;a</li>
	    	<li id="conMenPrEprog_habCat"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Categor&iacute;a</li>
	    	<li id="conMenPrEprog_desCat"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Categor&iacute;a</li>
	    	<li id="conMenPrEprog_prog"><span class="ui-icon ui-icon-plusthick"></span>Nuevo Programa</li>
	    	<li id="conMenPrEprog_ediProg"><span class="ui-icon ui-icon-pencil"></span>Editar Programa</li>
	    	<li id="conMenPrEprog_habProg"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Programa</li>
	    	<li id="conMenPrEprog_desProg"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Programa</li>
	        <li id="conMenPrEprog_proy"><span class="ui-icon ui-icon-plusthick"></span>Nuevo Proyecto</li>
			<li id="conMenPrEprog_ediproy"><span class="ui-icon ui-icon-pencil"></span>Editar Proyecto</li>
			<li id="conMenPrEprog_habproy"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Proyecto</li>
			<li id="conMenPrEprog_desproy"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Proyecto</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrActi">
	    <ul>
	    	<li id="conMenPrActi_ediActi"><span class="ui-icon ui-icon-pencil"></span>Editar Actividad</li>
	    	<li id="conMenPrActi_comp"><span class="ui-icon ui-icon-check"></span>Nuevo Componente</li>
	    	<li id="conMenPrActi_habActi"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Actividad</li>
	    	<li id="conMenPrActi_desActi"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Actividad</li>
	        <li id="conMenPrActi_ediComp"><span class="ui-icon ui-icon-pencil"></span>Editar Componente</li>
			<li id="conMenPrActi_habComp"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Componente</li>
			<li id="conMenPrActi_desComp"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Componente</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrClas">
	    <ul>
	    	<li id="conMenPrClas_ediClas"><span class="ui-icon ui-icon-pencil"></span>Editar Clasificador</li>
	    	<li id="conMenPrClas_ord"><span class="ui-icon ui-icon-pencil"></span>Ordenar Subpartidas</li>
	    	<li id="conMenPrClas_Clas"><span class="ui-icon ui-icon-plusthick"></span>Nuevo Clasificador</li>
	    	<li id="conMenPrClas_verClas"><span class="ui-icon ui-icon-search"></span>Ver Clasificador</li>
	    	<li id="conMenPrClas_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Clasificador</li>
	    	<li id="conMenPrClas_des"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Clasificador</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrFuen">
	    <ul>
	    	<li id="conMenPrFuen_ediFuen"><span class="ui-icon ui-icon-pencil"></span>Editar Fuente</li>
	    	<li id="conMenPrFuen_habFuen"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Fuente</li>
	    	<li id="conMenPrFuen_desFuen"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Fuente</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrUnid">
	    <ul>
	    	<li id="conMenPrUnid_ediUnid"><span class="ui-icon ui-icon-pencil"></span>Editar Unidad</li>
	    	<li id="conMenPrUnid_eliUnid"><span class="ui-icon ui-icon-closethick"></span>Eliminar Unidad</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrPresAper">
	    <ul>
	    	<li id="conMenPrPresAper_ediPart"><span class="ui-icon ui-icon-pencil"></span>Editar Partida</li>
	    	<li id="conMenPrPresAper_eliPart"><span class="ui-icon ui-icon-closethick"></span>Eliminar Partida</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrPlanProg">
	    <ul>
	    	<li id="conMenPrPlanProg_ediAct"><span class="ui-icon ui-icon-pencil"></span>Editar Actividad</li>
	    	<li id="conMenPrPlanProg_eliAct"><span class="ui-icon ui-icon-closethick"></span>Eliminar Actividad</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrPlanEjec">
	    <ul>
	    	<li id="conMenPrPlanEjec_Ingr"><span class="ui-icon ui-icon-plusthick"></span>Ingresar Metas</li>
	    	<li id="conMenPrPlanEjec_Cerr"><span class="ui-icon ui-icon-pencil"></span>Cerrar Trimestre</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrRese">
	    <ul>
	    	<li id="conMenPrRese_ver"><span class="ui-icon ui-icon-search"></span>Ver Certificacion</li>
	    	<li id="conMenPrRese_eli"><span class="ui-icon ui-icon-trash"></span>Eliminar Reserva</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenPrMeta">
	    <ul>
	    	<li id="conMenPrMeta_edi"><span class="ui-icon ui-icon-search"></span>Editar</li>
	    	<li id="conMenPrMeta_hab"><span class="ui-icon ui-icon-trash"></span>Habilitar</li>
	    	<li id="conMenPrMeta_des"><span class="ui-icon ui-icon-trash"></span>Deshabilitar</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenAlExpdAct">
	    <ul>
	    	<li id="conMenAlExpdAct_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Expediente</li>
	    	<li id="conMenAlExpdAct_ver"><span class="ui-icon ui-icon-search"></span>Ver Expediente</li>
	    	<li id="conMenAlExpdAct_inm"><span class="ui-icon ui-icon-home"></span>Consultar Inmueble</li>
			<li id="conMenAlExpdAct_arch"><span class="ui-icon ui-icon-folder-collapsed"></span>Archivar</li>
			<li id="conMenAlExpdAct_eli"><span class="ui-icon ui-icon-trash"></span>Eliminar Expediente</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenAlContFav">
	    <ul>
	    	<li id="conMenAlContFav_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Contigencia</li>
	    	<li id="conMenAlContFav_eli"><span class="ui-icon ui-icon-trash"></span>Eliminar Contingencia</li>
	    	<li id="conMenAlContFav_ver"><span class="ui-icon ui-icon-search"></span>Ver Contingencia</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenAlDili">
	    <ul>
	    	<li id="conMenAlDili_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Diligencia</li>
	    	<li id="conMenAlDili_susp"><span class="ui-icon ui-icon-cancel"></span>Suspender Diligencia</li>
	    	<li id="conMenAlDili_verExpd"><span class="ui-icon ui-icon-search"></span>Ver Expediente</li>
	    	<li id="conMenAlDili_eli"><span class="ui-icon ui-icon-search"></span>Eliminar</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenctPcon">
	    <ul>
	    	<li id="conMenctPcon_ediCuen"><span class="ui-icon ui-icon-pencil"></span>Editar Cuenta</li>
	    	<li id="conMenctPcon_ord"><span class="ui-icon ui-icon-pencil"></span>Ordenar Subcuentas</li>
	    	<li id="conMenctPcon_Cuen"><span class="ui-icon ui-icon-plusthick"></span>Nueva Cuenta</li>
	    	<li id="conMenctPcon_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Cuenta</li>
	    	<li id="conMenctPcon_des"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Cuenta</li>
	    </ul>
    </div>
    <div class="contextMenu" id="conMenctNotc">
	    <ul>
	    	<li id="conMenctNotc_ver"><span class="ui-icon ui-icon-document"></span>Ver Nota Contable</li>
	    	<li id="conMenctNotc_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Nota Contable</li>
	    </ul>
    </div>
    <div class="contextMenu" id="conMenctCban">
	    <ul>
	    	<li id="conMenctCban_ver"><span class="ui-icon ui-icon-search"></span>Ver Conciliaci&oacute;n</li>
	    	<li id="conMenctCban_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Conciliaci&oacute;n</li>
	    	<li id="conMenctCban_fin"><span class="ui-icon ui-icon-check"></span>Finalizar Conciliaci&oacute;n</li>
	    </ul>
    </div>
    <div class="contextMenu" id="conMenCtLidi">
	    <ul>
	    	<li id="conMenCtLidi_agr"><span class="ui-icon ui-icon-pencil"></span>Agregar Folio del Mayor</li>
		</ul>
	</div>
    <div class="contextMenu" id="conMenCtLima">
	    <ul>
	    	<li id="conMenCtLima_agr"><span class="ui-icon ui-icon-pencil"></span>Agregar Folio del Diario</li>
		</ul>
	</div>
    <div class="contextMenu" id="conMenCtLisu">
	    <ul>
	    	<li id="conMenCtLisu_comp"><span class="ui-icon ui-icon-pencil"></span>Completar los Datos</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenCtOrde">
	    <ul>
	    	<li id="conMenCtOrde_ver"><span class="ui-icon ui-icon-clipboard"></span>Ver Orden</li>
	    	<li id="conMenCtOrde_edi"><span class="ui-icon ui-icon-pencil"></span>Ingresar Informacion Contable</li>
	    	<li id="conMenCtOrde_apr"><span class="ui-icon ui-icon-pencil"></span>Aprobar y Crear Auxiliares</li>
		</ul>
	</div>
    <div class="contextMenu" id="conMenCjCuen">
	    <ul>
	    	<li id="conMenCjCuen_ver"><span class="ui-icon ui-icon-note"></span>Ver Cuenta por Cobrar</li>
	    	<li id="conMenCjCuen_ori"><span class="ui-icon ui-icon-note"></span>Ver Operaci&oacute;n de Origen</li>
	    	<li id="conMenCjCuen_anu"><span class="ui-icon ui-icon-closethick"></span>Anular Cuenta por Cobrar</li>
	    </ul>
    </div>
    <div class="contextMenu" id="conMenCjComp">
	    <ul>
	    	<li id="conMenCjComp_anu"><span class="ui-icon ui-icon-circle-close"></span>Anular Comprobante</li>
	    	<li id="conMenCjComp_cam"><span class="ui-icon ui-icon-pencil"></span>Cambio de nombre de Cliente en Comprobante</li>
	    	<li id="conMenCjComp_vou"><span class="ui-icon ui-icon-pencil"></span>Modificar Voucher</li>
	    	<li id="conMenCjComp_imp"><span class="ui-icon ui-icon-print"></span>Imprimir</li>
	    	<li id="conMenCjComp_eli"><span class="ui-icon ui-icon-print"></span>Eliminar</li>
	    </ul>
    </div>
    <div class="contextMenu" id="conMenTsCtBan">
	    <ul>
	    	<li id="conMenTsCtBan_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Cuenta</li>
	    	<li id="conMenTsCtBan_ver"><span class="ui-icon ui-icon-search"></span>Ver Cuenta</li>
	    	<li id="conMenTsCtBan_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar Cuenta</li>
	    	<li id="conMenTsCtBan_des"><span class="ui-icon ui-icon-circle-close"></span>Deshabilitar Cuenta</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsCjCh">
	    <ul>
	    	<li id="conMenTsCjCh_edi"><span class="ui-icon ui-icon-pencil"></span>Editar caja chica</li>
	    	<li id="conMenTsCjCh_ver"><span class="ui-icon ui-icon-search"></span>Ver caja chica</li>
	    	<li id="conMenTsCjCh_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar caja chica</li>
	    	<li id="conMenTsCjCh_des"><span class="ui-icon ui-icon-closethick"></span>Deshabilitar caja chica</li>
	    	<li id="conMenTsCjCh_act"><span class="ui-icon ui-icon-trash"></span>Actualizar monto</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsConc">
	    <ul>
	    	<li id="conMenTsConc_edi"><span class="ui-icon ui-icon-pencil"></span>Editar concepto</li>
	    	<li id="conMenTsConc_ver"><span class="ui-icon ui-icon-search"></span>Ver Historial del Concepto</li>
	    	<li id="conMenTsConc_hab"><span class="ui-icon ui-icon-circle-check"></span>Habilitar concepto</li>
	    	<li id="conMenTsConc_des"><span class="ui-icon ui-icon-closethick"></span>Deshabilitar concepto</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsCtpp">
	    <ul>
	    	<li id="conMenTsCtpp_ver"><span class="ui-icon ui-icon-search"></span>Ver cuenta por pagar</li>
	    	<li id="conMenTsCtpp_anu"><span class="ui-icon ui-icon-trash"></span>Anular cuenta por pagar</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsPoli">
	    <ul>
	    	<li id="conMenTsPoli_ver"><span class="ui-icon ui-icon-search"></span>Ver P&oacute;liza Contable</li>
	    	<li id="conMenTsPoli_edi"><span class="ui-icon ui-icon-pencil"></span>Editar P&oacute;liza Contable</li>
	    	<li id="conMenTsPoli_cta"><span class="ui-icon ui-icon-check"></span>Registrar en Libro Mov. Cta Cte</li>
	    	<li id="conMenTsPoli_anu"><span class="ui-icon ui-icon-circle-close"></span>Anular P&oacute;liza Contable</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsComp">
	    <ul>
	    	<li id="conMenTsComp_ver"><span class="ui-icon ui-icon-search"></span>Ver Comprobante de Pago</li>
	    	<li id="conMenTsComp_edi"><span class="ui-icon ui-icon-pencil"></span>Editar Comprobante de Pago</li>
	    	<li id="conMenTsComp_pag"><span class="ui-icon ui-icon-circle-close"></span>Pagar Comprobante de Pago</li>
	    	<li id="conMenTsComp_anu"><span class="ui-icon ui-icon-circle-close"></span>Anular Comprobante de Pago</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsSald">
	    <ul>
	    	<li id="conMenTsSald_ver"><span class="ui-icon ui-icon-search"></span>Ver Saldo</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsSaldAll">
	    <ul>
	    	<li id="conMenTsSaldAll_cer"><span class="ui-icon ui-icon-search"></span>Cerrar Periodo</li>
		</ul>
	</div>
	<div class="contextMenu" id="conMenTsRein">
	    <ul>
	    	<li id="conMenTsRein_ver"><span class="ui-icon ui-icon-search"></span>Ver Recibo de Ingresos</li>
	    	<li id="conMenTsRein_anu"><span class="ui-icon ui-icon-search"></span>Anular Recibo de Ingresos</li>
	    	<li id="conMenTsRein_cta"><span class="ui-icon ui-icon-search"></span>Registrar en Libro Mov. Cta. Cte.</li>
	    	<li id="conMenTsRein_mov"><span class="ui-icon ui-icon-search"></span>Registrar en Libro Mov. Efectivo</li>
	    	<li id="conMenTsRein_rec"><span class="ui-icon ui-icon-search"></span>Recepcionar Recibo de Ingresos</li>
		</ul>
	</div>
</div>
<div id="box_autocomp" class="ui-widget-content" style="z-index:999999;display:none;position:absolute;height:auto;background-color:#ffffff;border:1px solid #dddddd;">
	<div class="grid">
	</div>
</div>
<script src="<?=$baseURL?>scripts/plugins/socket.io-1.2.0.js"></script>
<script data-main="<?=$baseURL?>scripts/app.js" src="<?=$baseURL?>scripts/require.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/mainWindow-init.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/mg/navg.js"></script>
<?php if(isset($f->session->tasks['td'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/td/navg.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/td/repo.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['cm'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/cm/navg.js"></script>
<!--<script type="text/javascript" src="<?=$baseURL?>scripts/cm/comp.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cm/comppen.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cm/compall.js"></script>-->
<script type="text/javascript" src="<?=$baseURL?>scripts/cm/repo.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['in'])){ ?>
<!--<script type="text/javascript" src="<?=$baseURL?>scripts/in/navg.js"></script>-->
<script type="text/javascript" src="<?=$baseURL?>scripts/in_old/loca.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['pr'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/navg.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/estr.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/eprog.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/acti.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/fuen.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/unid.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/presaper.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/presmodi.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/planejec.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/planprog.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/nota.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/cred.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/meta.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/mefi.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/rese.js"></script>
<!--<script type="text/javascript" src="<?=$baseURL?>scripts/pr/sald.js"></script>-->
<script type="text/javascript" src="<?=$baseURL?>scripts/pr/repo.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['al'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/navg.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/expdtipos.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/expdacti.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/expdarch.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/contfav.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/contcontr.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/diliprog.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/diliejec.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/dilisusp.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/al/repo.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['pe'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/navg.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/perm.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/entitrab.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/coashora.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/coasprog.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/coasasis.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/coasinci.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/pres.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/cuad.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/vaca.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/lice.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/entipra.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/prop.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/repoinci.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/planvei.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/plantre.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/planvac.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/planenf.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/plansep.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/planmat.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/pe/planqui.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['cj'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/navg.js"></script>
<!--<script type="text/javascript" src="<?=$baseURL?>scripts/cj/inmu.js"></script>-->
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/caja.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/conc.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/cuen.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/cuenpor.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/cuentod.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/enticlie.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/enticaje.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/comp.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/compfac.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/compbol.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/comprec.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/comppen.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/cj/repo.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['ct'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/ct/navg.js"></script>
<?php } ?>
<?php if(isset($f->session->tasks['ts'])){ ?>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/navg.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/ctban.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/conc.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/ctpppen.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/ctppall.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/mocjdep.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/mocjall.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/cjch.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/compnue.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/compall.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/poli.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/polipor.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/politod.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/moviefe.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/movicue.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/moviban.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/movicjb.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/tipo.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/saldefe.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/saldcue.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/saldban.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/rein.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/reinpor.js"></script>
<script type="text/javascript" src="<?=$baseURL?>scripts/ts/reintod.js"></script>
<?php } ?>
<script type="text/javascript">
K.session = {
	user: <?php echo json_encode($f->session->user); ?>,
	enti: <?php echo json_encode($f->session->enti); ?>,
	titular: <?php echo json_encode($f->session->titular); ?>,
	tasks: <?php echo json_encode($f->session->tasks); ?>
};
Object.freeze(K.session.tasks);
Object.freeze(K.session.user);
Object.freeze(K.session.enti);
Object.freeze(K.session.titular);
$('#themes a').click(function(){
	$('head [name=css_jquery_ui]').remove();
	$('head [name=css_kunan]').remove();
	var theme = $(this).attr('href');
	$('head').append('<link name="css_jquery_ui" href="<?=$templateURL?>/css/'+ theme +'" rel="Stylesheet" type="text/css" />');
	$('head').append('<link name="css_kunan" href="<?=$templateURL?>/css/kunan.css" rel="Stylesheet" type="text/css" />');
	$.jStorage.set('theme',theme)
	return false;
});
</script>
<script type="text/javascript" src="<?=$baseURL?>scripts/node.js"></script>
</body>
</html>