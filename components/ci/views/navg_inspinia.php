<?php global $f;
$baseURL = $f->request->root;
$nomb = $f->session->enti['fullname'];
if(isset($f->session->enti['roles']['trabajador']['organizacion']['nomb'])){
	$orga = $f->session->enti['roles']['trabajador']['organizacion']['nomb'];
}else{
	$orga = '';
}

$img = $baseURL."images/logo.jpg";
/*if(isset($f->session->enti['imagen'])){
	if(isset($f->session->enti['imagen']->{'$id'}))
		$img = $baseURL.'ci/files/get?id='.$f->session->enti['imagen']->{'$id'};
	else
		$img = 'https://www.sbparequipa.gob.pe/files_sist'.$f->session->enti['imagen'];
}*/
?>
<ul style="display: block;" class="nav" id="side-menu">
	<li class="nav-header" style="padding: 15px 24px;">
        <div class="dropdown profile-element"> <span>
            <img alt="image" class="img-circle" src="<?=$img?>" width="48" height="48">
             </span>
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?=$nomb;?></strong>
            <!--</span> <span class="text-muted text-xs block"><?=$orga;?> <b class="caret"></b></span> </span> </a>-->
            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                <li><a href="#" name="menPerfil"><i class="fa fa-user"></i> Perfil</a></li>
                <li><a href="#" name="menPass"><i class="fa fa-key"></i> Modificar Contrase&ntilde;a</a></li>
                <li><a href="#"><i class="fa fa-phone"></i> Contacts</a></li>
                <li><a href="javascript:ciHelper.windowCumple();" name="menCump"><i class="fa fa-birthday-cake"></i> <?php
                	echo sizeof($cump);
                ?> Cumplea&ntilde;eros</a></li>
                <li class="divider"></li>
                <li><a href="ci/index/logout"><i class="fa fa-sign-out"></i> Cerrar Sesi&oacute;n</a></li>
            </ul>
        </div>
        <div class="logo-element">
            SBPA
        </div>
    </li>
	<?php //if(isset($f->session->tasks['ge'])){ ?>
	 <li>
		<a name="da">
			<i class="fa fa-dashboard"></i>
			<span class="nav-label">Resumen</span>
		</a>
	</li>
	<?php //} ?>
	<?php if(isset($f->session->tasks['mg'])){ ?>
	 <li>
        <a name="mg">
        	<i class="fa fa-th-large"></i>
        	<span class="nav-label">Maestros Generales</span>
        	<span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse in">
			<?php if(isset($f->session->tasks['mg.titu'])){ ?>
            <li><a name="mgEnti"><i class="fa fa-users"></i> Entidades</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.orga'])){ ?>
            <li><a name="mgOrga"><i class="fa fa-sitemap"></i> Estructura Organizacional</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.orga'])){ ?>
            <li><a name="mgOfic"><i class="fa fa-building"></i> Oficinas</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.orga'])){ ?>
            <li><a name="mgProg"><i class="fa fa-building"></i> Programas</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.titu'])){ ?>
            <li><a name="mgVari"><i class="fa fa-percent"></i> Variables Globales</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.titu'])){ ?>
            <li><a name="mgServ"><i class="fa fa-industry"></i> Servicios</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.titu'])){ ?>
            <li><a name="mgMult"><i class="fa fa-picture-o"></i> Gestor Multimedia</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['mg.titu'])){ ?>
            <li><a name="mgTitu"><i class="fa fa-user-plus"></i> Titular</a></li>
			<?php } ?>
		</ul>
    </li>
	<?php } ?>
	<!--<?php if(isset($f->session->tasks['po'])){ ?>
	 <li>
        <a name="po">
        	<i class="fa fa-th-large"></i>
        	<span class="nav-label">Visitas</span>
        	<span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse in">
			<li><a name="poVisi"><i class="fa fa-users"></i> Visitas</a></li>
			<?php //} ?>
		</ul>
    </li>
	<?php } ?>
	<?php if(isset($f->session->tasks['ge'])){ ?>
	 <li>
		<a name="ge">
			<i class="fa fa-tasks"></i>
			<span class="nav-label">Gesti&oacute;n de Proyectos</span>
		</a>
	</li>
	<?php } ?>
	<?php if(isset($f->session->tasks['ti'])){ ?>
	 <li>
		<a name="ti">
			<i class="fa fa-usb"></i>
			<span class="nav-label">Inform&aacute;tica</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <?php if(isset($f->session->tasks['ti.comp'])){ ?>
	        <li><a name="tiComp"><i class="fa fa-laptop"></i> Inventario de Computadoras</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ti.back'])){ ?>
	        <li><a name="tiBack"><i class="fa fa-database"></i> Copia de Seguridad</a></li>
	        <?php } ?>

	        <li><a name="tiDash"><i class="fa fa-laptop"></i> Estado del sistema</a></li>

	        <?php if(isset($f->session->tasks['ti.erro'])){ ?>
	        <li><a name="tiErro"><i class="fa fa-warning"></i> Reporte de Errores</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ti.edit'])){ ?>
	        <li><a name="tiEdit"><i class="fa fa-code"></i> Editor</a></li>
	        <?php } ?>
		</ul>
	</li>
	<?php } ?>-->
	<?php if(isset($f->session->tasks['td'])){ ?>
	<li>
        <a name="td">
			<i class="fa fa-book"></i>
			<span class="hidden-xs">Tr&aacute;mite Documentario</span>
        	<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
			<!--
			<?php if(isset($f->session->tasks['td.expd.gest.int']) || isset($f->session->tasks['td.expd.gest.ext'])){ ?>
            <li>
                <a>Cuentas <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <li><a href="#">Recientes</a></li>
                    <li><a href="#">Vencidas</a></li>
                </ul>
            </li>
			<?php } ?>-->
			<?php if(isset($f->session->tasks['td.tupa'])){ ?>
            <li>
                <a name="tdTupa"><i class="fa fa-book"></i> TUPA</a>
            </li>
			<?php } ?>
			<?php if(isset($f->session->tasks['td.expd'])){ ?>
            <li>
                <a name="tdExpd"><i class="fa fa-folder-open"></i> Expedientes</a>
            </li>
            <?php } ?>
			<!--<?php if(isset($f->session->tasks['td.repo'])){ ?>
            <li>
                <a name="tdRepo">Reportes</a>
            </li>
			<?php } ?>-->
			<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
					<?php if(isset($f->session->tasks['td.tdoc'])){ ?>
		            <li><a name="tdTdocs"><i class="fa fa-file"></i> Tipos de Documentos</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['td.orga'])){ ?>
		            <li><a name="tdOrga"><i class="fa fa-external-link"></i> &Oacute;rganos Externos para el TUPA</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['td.orga'])){ ?>
		            <li><a name="tdComi"><i class="fa fa-users"></i> Comites</a></li>
					<?php } ?>
				</ul>
			</li>
		</ul>
	</li>
	<?php } ?>
	<?php if(isset($f->session->tasks['cm'])){ ?>
	 <li>
        <a name="cm">
        	<i class="fa fa-university"></i>
        	<span class="nav-label">Cementerio</span>
        	<span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse in">
			<?php if(isset($f->session->tasks['cm.oper'])){ ?>
            <li><a name="cmOper"><i class="fa fa-pencil"></i> Operaciones</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.oper'])){ ?>
            <li><a name="cmHope"><i class="fa fa-book"></i> Registro Hist&oacute;rico</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.espc'])){ ?>
            <li><a name="cmEspa"><i class="fa fa-bank"></i> Espacios</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.ctas'])){ ?>
            <li><a name="cmOcup"><i class="fa fa-heart-o"></i> Ocupantes</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.ctas'])){ ?>
            <li><a name="cmProp"><i class="fa fa-users"></i> Propietarios</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.espc'])){ ?>
            <li><a name="cmPabe"><i class="fa fa-building"></i> Pabellones</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.accs'])){ ?>
            <li><a name="cmAcce"><i class="fa fa-puzzle-piece"></i> Accesorios</a></li>
			<?php } ?>
			<!--<?php if(isset($f->session->tasks['cm.espc'])){ ?>
            <li><a name="cmMapa">Mapa</a></li>
			<?php } ?>-->
			<?php if(isset($f->session->tasks['cm.conf'])){ ?>
            <li><a name="cmConf"><i class="fa fa-wrench"></i> Configuraci&oacute;n</a></li>
			<?php } ?>
			<!--<?php if(isset($f->session->tasks['cm.reps'])){ ?>
            <li><a name="cmRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
			<?php } ?>-->

			<?php if(isset($f->session->tasks['cm.oper'])){ ?>
            <li><a name="cmRehi"><i class="fa fa-book"></i>Registro Hist&oacute;rico (fotos)</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['cm.oper'])){ ?>
            <li><a name="cmTerr"><i class="fa fa-wheelchair-alt"></i>Circuito de Terror</a></li>
			<?php } ?>

        </ul>
    </li>
	<?php } ?>
	<?php if(isset($f->session->tasks['cj'])){ ?>
	<li>
        <a name="cj">
        	<i class="fa fa-university"></i>
        	<span class="nav-label">Caja</span>
        	<span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse in">
        	<li><a name="cjCaja"><i class="fa fa-pencil"></i> Cajas recaudadoras</a></li>
        	<li><a name="cjTalo"><i class="fa fa-pencil"></i> Talonarios</a></li>
            <li><a name="cjEcom"><i class="fa fa-pencil"></i> Comprobantes de pago</a></li>
            <li><a name="cjCuco"><i class="fa fa-pencil"></i> Cuentas por cobrar</a></li>
        </ul>
    </li>
	<?php } ?>
	<?php if(isset($f->session->tasks['in'])){ ?>
	 <li>
        <a name="in">
			<i class="fa fa-home"></i>
			<span class="nav-label">Inmuebles</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
					  <li><a name="inAler"><i class="fa fa-exclamation-triangle"></i> Alerta de Pagos (En construccion)</a></li>
            <li><a name="inMovi"><i class="fa fa-money"></i> Movimientos</a></li>
            <li><a name="inActa"><i class="fa fa-gavel"></i> Actas de Conciliaci&oacute;n</a></li>
            <!-- <li><a name="inCotr"><i class="fa fa-file"></i> Contratos (En Desarrollo) </a></li>
            <li><a name="inMarq"><i class="fa fa-file"></i> Marquesi de Inmuebles (En Desarrollo) </a></li> -->
            <li><a name="inPlay"><i class="fa fa-car"></i> Playas</a></li>
            <li><a name="inImpl"><i class="fa fa-upload"></i> Importar Playas</a></li>
            <li><a name="inComp"><i class="fa fa-file-o"></i> Comprobantes de Pago</a></li>
            <li><a name="inRein"><i class="fa fa-file-text-o"></i> Recibos de Ingresos</a></li>
            <li><a name="inRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
			<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
		            <li><a name="inTipo"><i class="fa fa-code-fork"></i> Tipo de Local</a></li>
		            <li><a name="inSubl"><i class="fa fa-building-o"></i> SubLocal</a></li>
		            <li><a name="inInmu"><i class="fa fa-home"></i> Inmuebles</a></li>
		            <li><a name="inMoti"><i class="fa fa-list-ol"></i> Motivos de Contrato</a></li>
		            <li><a name="inCalp"><i class="fa fa-calendar"></i> Calendario de Pagos</a></li>
		            <li><a name="inCalv"><i class="fa fa-exclamation-triangle"></i> Vencimiento de Letras</a></li>
           			<li><a name="inConf"><i class="fa fa-gears"></i> Configuraci&oacute;n de Caja</a></li>
                </ul>
            </li>
		</ul>
    </li>
	<?php } ?>
	<!--<?php if(isset($f->session->tasks['lg'])){ ?>
	 <li>
		<a name="lg">
			<i class="fa fa-barcode"></i>
			<span class="nav-label">Log&iacute;stica</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <li><a name="lgList"><i class="fa fa-user"></i> Listados</a></li>
	        <li><a name="lgAjus"><i class="fa fa-wrench"></i> Ajustes de Inventario</a></li>
	        <?php if(isset($f->session->tasks['lg.cnec.dep'])){ ?>
			<li><a name="lgCuadPord"><i class="fa fa-user"></i> Cuadro de Necesidades</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.cnec.org'])){ ?>
			<li><a name="lgCuadToda"><i class="fa fa-users"></i> Consolidado de Cuadros de Necesidades</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.pedi'])){ ?>
			<li><a href="#"><i class="fa fa-commenting-o"></i> Requerimiento <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <?php if(isset($f->session->tasks['lg.pedi.nuev'])){ ?>
					<li><a name="lgPedi_nuev"><i class="fa fa-user"></i> Mis requerimientos</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.pedi.bien'])){ ?>
					<li><a name="lgPedi_bien"><i class="fa fa-clock-o"></i> Req. Bienes pendientes</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.pedi.serv'])){ ?>
					<li><a name="lgPedi_serv"><i class="fa fa-clock-o"></i> Req. Servicios pendientes</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.pedi.loca'])){ ?>
					<li><a name="lgPedi_loca"><i class="fa fa-clock-o"></i> Req. Locacion pendientes</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.pedi.todo'])){ ?>
					<li><a name="lgPedi_todo"><i class="fa fa-file"></i> Todos los Pedidos</a></li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.coti.todo'])){ ?>
			<li><a name="lgCoti"><i class="fa fa-money"></i> Cotizaciones</a></li>
			<?php } ?>

			<?php if(isset($f->session->tasks['lg.soli'])){ ?>
			<li><a name="lgCert"><i class="fa fa-file-text"></i> Solicitud de certificaci&oacute;n<span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <?php if(isset($f->session->tasks['lg.soli.nuev'])){ ?>
					<li><a name="lgSoli_nue"><i class="fa fa-user"></i> Nuevos</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.soli.envi'])){ ?>
					<li><a name="lgSoli_env"><i class="fa fa-clock-o"></i> Enviados</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.soli.rece'])){ ?>
					<li><a name="lgSoli_rec"><i class="fa fa-file"></i> Recepcionados</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.soli.apro'])){ ?>
					<li><a name="lgSoli_apr"><i class="fa fa-clock-o"></i> Aprobados</a></li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>

			<?php if(isset($f->session->tasks['lg.cert'])){ ?>
			<li><a name="lgCert"><i class="fa fa-file-text"></i> Certificaci&oacute;n Presupuestaria <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <?php if(isset($f->session->tasks['lg.cert.nuev'])){ ?>
					<li><a name="lgCert_nue"><i class="fa fa-user"></i> Nuevas</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.cert.apro'])){ ?>
					<li><a name="lgCert_apr"><i class="fa fa-clock-o"></i> Aprobados</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.cert.envi'])){ ?>
					<li><a name="lgCert_env"><i class="fa fa-clock-o"></i> Enviados</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.cert.rece'])){ ?>
					<li><a name="lgCert_rec"><i class="fa fa-file"></i> Recepcionados</a></li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.orde'])){ ?>
			<li><a name="lgOrde"><i class="fa fa-file-text"></i> Orden de Compra <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <?php if(isset($f->session->tasks['lg.orde.nuev'])){ ?>
					<li><a name="lgOrde_nue"><i class="fa fa-user"></i> Nuevas</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.orde.apro'])){ ?>
					<li><a name="init_apro"><i class="fa fa-check"></i> Aprobadas</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.orde.envi'])){ ?>
					<li><a name="lgOrde_env"><i class="fa fa-file"></i> Enviados</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.orde.rece'])){ ?>
					<li><a name="lgOrde_rec"><i class="fa fa-file"></i> Recepcionados</a></li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.orse'])){ ?>
			<li><a ><i class="fa fa-file-text-o"></i> Orden de Servicio <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <?php if(isset($f->session->tasks['lg.orse.nuev'])){ ?>
					<li><a name="lgOrse"><i class="fa fa-user"></i> Nuevas</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.orse.apro'])){ ?>
					<li><a name="lgOrse_apr"><i class="fa fa-clock-o"></i> Aprobadas</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.orse.envi'])){ ?>
					<li><a name="lgOrse_env"><i class="fa fa-clock-o"></i> Enviados</a></li>
					<?php } ?>
					<?php if(isset($f->session->tasks['lg.orse.conf'])){ ?>
					<li><a name="lgOrse_rec"><i class="fa fa-file"></i> Ejecutados</a></li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.pcsa'])){ ?>
			<li><a name="lgNotn"><i class="fa fa-shopping-basket"></i> Notas de Entrada</a>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.pcsa.edit'])){ ?>
			<li><a name="lgPeca"><i class="fa fa-shopping-basket"></i> PECOSAs <span class="fa fa-user"></span></a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['lg.pcsa.ent'])){ ?>
			<li><a name="lgPecl"><i class="fa fa-shopping-basket"></i> PECOSAs (Almc) <span class="fa fa-home"></span></a></li>
			<li><a name="lgRepo"><i class="fa fa-print"></i> Reportes <span class="fa fa-home"></span></a></li>
			<?php } ?>
			<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Maestros Generales <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
					<li><a name="lgAlma"><i class="fa fa-home"></i> Almacenes</a></li>
					<li><a name="lgUnid"><i class="fa fa-percent"></i> Unidades</a></li>
			        <li><a name="lgProd"><i class="fa fa-shopping-cart"></i> Productos</a></li>
			        <li><a name="lgBien"><i class="fa fa-building"></i> Bienes</a></li>
			        <li><a name="lgCuen"><i class="fa fa-calculator"></i> Cuentas Contables</a></li>
			        <li><a name="lgConf"><i class="fa fa-gears"></i> Config. Inicial</a></li>
			    </ul>
			</li>
	    </ul>
    </li>
	<?php } ?>
	<?php if(isset($f->session->tasks['pe'])){ ?>
	 <li>
		<a name="pe">
			<i class="fa fa-users"></i>
			<span class="nav-label">Personal</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <?php if(isset($f->session->tasks['pe.plan'])){ ?>
	        <li><a name="peBole"><i class="fa fa-wpforms"></i> Boletas de Pago</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['pe.plan'])){ ?>
	        <li><a name="peImas"><i class="fa fa-upload"></i> Importar Asistencias</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['pe.plan'])){ ?>
	        <li><a href="#"><i class="fa fa-clock-o"></i> Control de Asistencia</a>
	        	<ul class="nav nav-third-level collapse">
					<li><a name="peHora"><i class="fa fa-calculator"></i> Horarios</a></li>
					<li><a name="peProi"><i class="fa fa-calculator"></i> Programacion de incidencias</a></li>
					<li><a name="peAsis"><i class="fa fa-calculator"></i> Asistencia</a></li>
					<li><a name="peInci"><i class="fa fa-calculator"></i> Incidencias</a></li>
				</ul>
	        </li>
	        <li><a name="peAsis"><i class="fa fa-clock-o"></i> Control de Asistencia</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['pe.plan'])){ ?>
			<li><a name="pePlan"><i class="fa fa-wpforms"></i> Planillas</a></li>
			<?php } ?>
	        <?php if(isset($f->session->tasks['pe.repo'])){ ?>
            <li><a name="peRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
	        <?php } ?>
			<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
			        <?php if(isset($f->session->tasks['pe.ctas.trab.276'])||isset($f->session->tasks['pe.ctas.trab.cas'])){ ?>
			        <li><a name="peTrab"><i class="fa fa-users"></i> Trabajadores</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.conc'])){ ?>
			        <li><a name="peConc"><i class="fa fa-calculator"></i> Conceptos</a></li>

					<?php if(isset($f->session->tasks['pe.conc'])){ ?>
			        <li><a name="peDesc"><i class="fa fa-minus-circle"></i> Descuentos</a></li>
			        <?php } ?>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.grup'])){ ?>
			        <li><a name="peGrup">Grupos Ocupacionales</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.carc'])){ ?>
			        <li><a name="peClas">Cargos Clasificados</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.carg'])){ ?>
			        <li><a name="peCarg">Cargos</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.nivr'])){ ?>
			        <li><a name="peNive">Niveles Remunerativos</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.tinc'])){ ?>
			        <li><a name="peTipo">Tipos de Incidencia</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.pens'])){ ?>
			        <li><a name="peSist">Sistemas de Pensiones</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.tcon'])){ ?>
			        <li><a name="peCont">Tipos de Contrato</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.equi'])){ ?>
			        <li><a name="peEqui"><i class="fa fa-camera-retro"></i> Equipos</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.feri'])){ ?>
			        <li><a name="peFeri"><i class="fa fa-calendar"></i> Feriados</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['pe.turn'])){ ?>
			        <li><a name="peTurn"><i class="fa fa-calendar-check-o"></i> Turnos</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['fa.conf'])){ ?>
			        <li><a name="peConf"><i class="fa fa-cog"></i> Configuraci&oacute;n</a></li>
			        <?php } ?>
			    </ul>
			</li>
		</ul>
    </li>
	<?php } ?>
	<?php if(isset($f->session->tasks['us'])){ ?>
	<li>
		<a name="us">
			<i class="fa fa-cutlery"></i>
			<span class="nav-label">DSA</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
			<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
			        <?php if(isset($f->session->tasks['us.coci'])){ ?>
			        <li><a name="usCoci">Cocinas</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['us.unid'])){ ?>
			        <li><a name="usUnid">Unidades</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['us.ingr'])){ ?>
			        <li><a name="usIngr">Ingredientes</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['us.rece'])){ ?>
		            <li><a name="usRece">Recetario</a></li>
			        <?php } ?>
			    </ul>
			</li>
	        <?php if(isset($f->session->tasks['us.pedi'])){ ?>
            <li><a name="usPedi">Pedido de Raciones</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['us.rece'])){ ?>
            <li><a name="usRepe">Recepci&oacute;n de Pedidos</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['us.prog'])){ ?>
            <li><a name="usProg">Programaci&oacute;n Semanal</a></li>
	        <?php
                    } ?>
	        <?php if (isset($f->session->tasks['us.cons'])) {
                        ?>
            <li><a name="usCons">Consumo de Insumos</a></li>
	        <?php
                    } ?>
	        <?php if (isset($f->session->tasks['us.repo'])) {
                        ?>
            <li><a name="usRepo">Reportes</a></li>
	        <?php } ?>
		</ul>
    </li>
	<?php } ?>-->
	<?php if(isset($f->session->tasks['ho'])){ ?>
	 <li>
		<a name="mh">
			<i class="fa fa-h-square"></i>
			<span class="nav-label">Moises Heresi</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
		<li>
			<!--<a target="_blank" href="https://docs.google.com/document/d/1yo532RdSCHPm-n70uiiaFzPmCHbmvJZETDNAqx6cQZU/edit">Manual</a> -->
		</li>
	        <!--<li class ="active">-->
	        <li >
		        <a name=""><i class="fa fa-hospital-o"></i> <span class="fa arrow"></span>Pacientes</a>
				<ul class="nav nav-third-level collapse in" style="">
					<li><a name="mhPaci">Fichas Frontal</a></li>
					<li><a name="mhSocial">Ficha Social</a></li>
					<li><a name="mhPsic">Fichas Psicologicas</a></li>
					<li><a name="mhHospi">Hospitalizaciones </a></li>
					<li><a name="mhPadi">Parte Diario</a></li>
					<li><a name="mhChar">Charlas</a></li>
				</ul>
			</li>
			<li>
		        <a name=""><i class="fa fa-bed" aria-hidden="true"></i> <span class="fa arrow"></span>Control de Pacientes </a>
				<ul class="nav nav-third-level collapse in">
					<li><a name="mhPaho">Pacientes Salud Mental</a></li>
					<?php if(isset($f->session->tasks['ho.cont'])){ ?>
					<li><a name="mhPajo">Pacientes Adicciones</a></li>
			        <li><a name="hoCont"><i class="fa fa-medkit"></i> Control de Medicinas</a></li>
			        <?php } ?>

				</ul>
			</li>
			<li>
		        <a name=""><i class="fa fa-money"></i> <span class="fa arrow"></span>Caja</a>
				<ul class="nav nav-third-level collapse in" style="">
					<?php if(isset($f->session->tasks['ho.cont'])){ ?>
			        <li><a name="hoCont"><i class="fa fa-medkit"></i> Control de Medicinas</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.pend'])){ ?>
			        <li><a name="hoPend"><i class="fa fa-list-ol"></i> Pendientes Salud Mental</a></li>
			        <?php } ?>
					<?php if(isset($f->session->tasks['ho.pend'])){ ?>
			        <li><a name="haPend"><i class="fa fa-list-ol"></i> Pendientes Adicciones</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.hosp'])){ ?>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.reci'])){ ?>
			        <li><a name="hoReci"><i class="fa fa-cart-plus"></i> Recibos de Caja</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.rein'])){ ?>
			        <li><a name="hoRein"><i class="fa fa-file"></i> Recibos de Ingresos</a></li>
			        <?php } ?>
				</ul>
			</li>
			<li>
		        <a name=""><i class="fa fa-hospital-o"></i>
		           <span class="fa arrow"></span>Reportes  </a>
					   <ul class="nav nav-third-level collapse in" style="">
								<?php if(isset($f->session->tasks['ho.repo'])){ ?>
								<li>
									<a name="mhRepo"> Reportes Generales</a>
								</li>
								<li>
									<a name="mhDash"><i class="fa fa-line-chart"></i> Dashboard</a>
								</li>
								<?php } ?>
								<?php if(isset($f->session->tasks['ho.repo'])){ ?>
						        <li><a name="hoRepo"><i class="fa fa-line-chart"></i> Caja</a></li>
						        <?php } ?>
						        <!--<?php if(isset($f->session->tasks['ho.repo'])){ ?>
						        <li><a name="haRepo"><i class="fa fa-line-chart"></i> Caja</a></li>
						        <?php } ?>-->
			       	  </ul>
			   </li>
		    	<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
					<li><a name="mhCama">Camas</a></li>
			        <li><a name="mhDini">Diagnostico Inicial</a></li>
			        <li><a name="mhCons">Consulta Medica</a></li>
			        <li><a name="mhEvol">Evolucion Medica</a></li>
			        <li><a name="mhDoct">Medicos</a></li>
			        <?php if(isset($f->session->tasks['ho.conf'])){ ?>
			        <li><a name="hoConf"><i class="fa fa-cog"></i> Configuraci&oacute;n</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.tari'])){ ?>
			        <li><a name="hoTari"><i class="fa fa-money"></i> Tarifario de Hospitalizaciones</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.tari'])){ ?>
			        <li><a name="haTari"><i class="fa fa-money"></i> Tarifario de Hospitalizaciones</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.tari'])){ ?>
			        <li><a name="hoTara"><i class="fa fa-lemon-o"></i> Tarifario de Productos Agr&iacute;colas</a></li>
			        <?php } ?>
			        <?php if(isset($f->session->tasks['ho.tari'])){ ?>
			        <li><a name="hoTarg"><i class="fa fa-cutlery"></i> Tarifario de Productos Ganaderos</a></li>
			        <?php } ?>
			    </ul>
			</li>

		</ul>


	</li>

	<?php } ?>
	<!-- ************ -->
	<!--<?php if(isset($f->session->tasks['ch'])){ ?>
	 <li>
		<a name="ch">
			<i class="fa fa-h-square"></i>
			<span class="nav-label">Chilpinilla</span>
			<span class="fa arrow"></span>
		</a>
		  <ul class="nav nav-second-level collapse in">

	        <li >
		        <a name=""><i class="fa fa-hospital-o"></i> <span class="fa arrow"></span>Pacientes</a>
				<ul class="nav nav-third-level collapse in" style="">
					<li><a name="chPaci">Fichas Frontal</a></li>
					<li><a name="chSocial">Ficha Social</a></li>
					<li><a name="chPsic">Fichas Psicologicas</a></li>
					<li><a name="chPsiq">Fichas Psicologicas</a></li>
					<li><a name="chHospi">Hospitalizaciones </a></li>
				</ul>
			</li>
			<li>
		        <a name=""><i class="fa fa-bed" aria-hidden="true"></i> <span class="fa arrow"></span>Control de Pacientes </a>
				<ul class="nav nav-third-level collapse in">
					<li><a name="chPaho">Pacientes Hospitalizados</a></li>
					<?php if(isset($f->session->tasks['ch.cont'])){ ?>
					<li><a name="chCont"><i class="fa fa-medkit"></i> Control de Medicinas</a></li>
			        <?php } ?>

				</ul>
			</li>
			<li>
		        <a name=""><i class="fa fa-hospital-o"></i>
		           <span class="fa arrow"></span>Reportes  </a>
					   <ul class="nav nav-third-level collapse in" style="">
								<?php if(isset($f->session->tasks['ch.repo'])){ ?>
								<li>
									<a name="chRepo"> Reportes Generales</a>
								</li>
								<?php } ?>
								<?php if(isset($f->session->tasks['ch.repo'])){ ?>
						        <li><a name="hoRepo"><i class="fa fa-line-chart"></i> Caja</a></li>
						        <?php } ?>
			       	  </ul>
			   </li>
		    	<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
					<li><a name="chDini">Diagnostico Inicial</a></li>
			        <li><a name="chDoct">Medicos</a></li>
			    </ul>
			</li>

		</ul>
      </li>

	<?php } ?> -->
	<!-- ************ -->
	<?php if(isset($f->session->tasks['dd'])){ ?>
	 <li>
		<a name="dd">
			<i class="fa fa-file-archive-o" aria-hidden="true"></i>
			<span class="nav-label">Archivo Central</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <!-- PEDIDOS -->

	        <li class ="">
		        <a name=""><i class="fa fa-book" aria-hidden="true"></i>
		           <span class="fa arrow"></span> Archivos </a>
					   <ul class="nav nav-third-level collapse in" style="">

								<li class ="" name="ddPear" >
								        <a > Pedido de Documentos</a>
								</li>


			       	  </ul>
			   </li>

	        <!-- ************* -->
	        <li class ="">
		        <a name=""><i class="fa fa-file-archive-o" aria-hidden="true"></i>
		           <span class="fa arrow"></span> Documentaci&oacute;n Archivo </a>
					   <ul class="nav nav-third-level collapse in" style="">

								<li class ="" name="ddRegi" >
								        <a > Registro de Documentos</a>
								</li>
								<li class ="" name="ddDepu">
										<a > Depuraciones</a>
								</li>

								<li class ="" name="ddRedo" >
								        <a > Recepci&oacute;n Documentaria</a>
								</li>
								<li class ="" name="ddPedi" >
								        <a > Pedidos</a>
								</li>
								<li class ="" name="ddForm" >
								        <a > Formatos de Documentos</a>
								</li>

			       	  </ul>
			   </li>
			   <li class ="">
		        <a name=""><i class="fa fa-file-word-o" aria-hidden="true"></i>
		           <span class="fa arrow"></span> Archivos Historicos </a>
					   <ul class="nav nav-third-level collapse in" style="">

								<li class ="" name="ddDohi" >
								        <a > Documentos Hist&oacute;ricos</a>
								</li>
								<!--
								<li class ="" name="ddRedh">
										<a > Reporte de Documentos Historicos</a>
								</li>
								-->

								<li class ="" name="ddCondo" >
								        <a > Consultas de Documentos Hist&oacute;ricos</a>
								</li>

			       	  </ul>
			   </li>

			    <li class ="">
		        <a name=""><i class="fa fa-line-chart" aria-hidden="true"></i>
		           <span class="fa arrow"></span>Reportes  </a>
					   <ul class="nav nav-third-level collapse in" style="">

								<li class ="" name="ddRped" >
								        <a > Pedidos</a>
								</li>
								<li class ="" name="ddRepo" >
								        <a > Registrados</a>
								</li>
							<!--
								<li class ="" name="ddRrhh" >
								        <a > R.R.H.H</a>
								</li>
								<li class ="" name="ddOtro" >
								        <a > OTROS </a>
								</li>
								-->

			       	  </ul>
			   </li>

		    	<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">

			        <li><a name="ddOfic">Oficinas</a></li>

			        <li><a name="ddDire">Direcciones</a></li>

			        <li><a name="ddTipo">Tipos</a></li>

			        <li><a name="ddTido">Tipos Documentos</a></li>

			        <li><a name="ddTise">Tipos Serie Documental</a></li>

			    </ul>
			</li>

		</ul>
	<?php //} ?>

	<?php } ?>
	<!--<?php if(isset($f->session->tasks['fa'])){ ?>
	 <li>
		<a name="fa">
			<i class="fa fa-medkit"></i>
			<span class="nav-label">Farmacia</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <?php if(isset($f->session->tasks['fa.conf'])){ ?>
	        <li><a name="faConf"><i class="fa fa-cog"></i> Configuraci&oacute;n</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.prod'])){ ?>
	        <li><a name="faProd"><i class="fa fa-archive"></i> Inventario</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.guia'])){ ?>
	        <li><a name="faGuia"><i class="fa fa-file-text-o"></i> Guias de Remisi&oacute;n</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.lote'])){ ?>
	        <li><a name="faLote"><i class="fa fa-database"></i> Lotes</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.vent'])){ ?>
	        <li><a name="faVent"><i class="fa fa-shopping-cart"></i> Ventas</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.comp'])){ ?>
	        <li><a name="faComp"><i class="fa fa-money"></i> Comprobantes de Pago manuales</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.comp'])){ ?>
	        <li><a name="faRein"><i class="fa fa-file"></i> Recibos de Ingresos</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['fa.repo'])){ ?>
	        <li><a name="faRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
	        <?php } ?>
		</ul>
	</li>
	<?php } ?>
	<?php if(isset($f->session->tasks['ag'])){ ?>
	 <li>
		<a name="ag">
			<i class="fa fa-tint"></i>
			<span class="nav-label">Venta de Agua</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <?php if(isset($f->session->tasks['ag.conf'])){ ?>
	        <li><a name="agConf"><i class="fa fa-cog"></i> Configuraci&oacute;n</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.prod'])){ ?>
	        <li><a name="agProd"><i class="fa fa-archive"></i> Inventario</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.guia'])){ ?>
	        <li><a name="agGuia"><i class="fa fa-file-text-o"></i> Guias de Remisi&oacute;n</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.lote'])){ ?>
	        <li><a name="agLote"><i class="fa fa-database"></i> Lotes</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.vent'])){ ?>
	        <li><a name="agVent"><i class="fa fa-shopping-cart"></i> Ventas</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.comp'])){ ?>
	        <li><a name="agComp"><i class="fa fa-money"></i> Comprobantes de Pago</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.comp'])){ ?>
	        <li><a name="agRein"><i class="fa fa-file"></i> Recibos de Ingresos</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ag.repo'])){ ?>
	        <li><a name="agRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
	        <?php } ?>
		</ul>
	</li>
	<?php } ?>
	<?php if(isset($f->session->tasks['re'])){ ?>
	 <li>
		<a name="re">
			<i class="fa fa-bitcoin"></i>
			<span class="nav-label">Recursos econ&oacute;micos</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	         <?php if(isset($f->session->tasks['re.repo'])){ ?>
	        	<li><a name="reRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
	        <?php } ?>
					<?php if(isset($f->session->tasks['re.repo'])){ ?>
					 <li><a name="reDash"><i class="fa fa-line-chart"></i> Dashboard</a></li>
				 <?php } ?>
	        	<li><a name="rePosc"><i class="fa fa-line-chart"></i> Configuraci&oacute;n de POS</a></li>
		</ul>



	</li>
	<?php } ?>-->

	<!-- ************ -->
	<!--
	 <li>
		<a name="dd">
			<i class="fa fa-credit-card" aria-hidden="true"></i>
			<span class="nav-label">Recibos de Caja</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <li class ="">
		        <a name=""><i class="fa fa-book" aria-hidden="true"></i>
		           <span class="fa arrow"></span> Recibos </a>
					   <ul class="nav nav-third-level collapse in" style="">

								
								<li><a name="tsRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
						</ul>
			   </li>
			</li>

		</ul>
	
	 </li>
	
					-->
	
	<!-- CAJA CHICA -->
	<!-- TESORERIA -->
	<?php if(isset($f->session->tasks['ts'])){ ?>
	<li>
		<a name="ts">
			<i class="fa fa-diamond"></i>
			<span class="nav-label">Caja Chica</span>
			<span class="fa arrow"></span>
		</a>
		<ul class="nav nav-second-level collapse in">
			<?php if(isset($f->session->tasks['ts.reci'])){ ?>
				<li><a name="tsReca"> <i class="fa fa-files-o fa-fw"></i>Registro de Recibos</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['ts.aprob'])){ ?>
				<li><a name="tsRepo"> <i class="fa fa-check-square"></i>Aprobación de Recibos</a></li>
			<?php } ?>
		</ul>
	</li>
	<?php } ?>
					<!-- TESORERIA -->
	<?php if(isset($f->session->tasks['ts'])){ ?>
	<li>
		<a name="ts">
			<i class="fa fa-money"></i>
			<span class="nav-label">Tesorer&iacute;a</span>
			<span class="fa arrow"></span>
		</a>
		<ul class="nav nav-second-level collapse in">

		<?php if(isset($f->session->tasks['ts.rede'])){ ?>
			<!--<li><a name="tsRede"> <i class="fa fa-files-o fa-fw"></i>Recibos Definitivos</a></li>-->
			
		<?php } ?>
		<?php if(isset($f->session->tasks['ts.reci'])){ ?>
			<li><a name="tsReca"> <i class="fa fa-files-o fa-fw"></i>Registro de Recibos</a></li>
		<?php } ?>
		<?php if(isset($f->session->tasks['ts.aprob'])){ ?>
			<li><a name="tsRepo"> <i class="fa fa-check-square"></i>Aprobación de Recibos</a></li>
		<?php } ?>
		<?php if(isset($f->session->tasks['ts.comp'])){ ?>
		    <li><a href="#"><i class="fa fa-barcode fa-fw"></i> Comprobantes de pago <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
					<li><a name="tsComp">Comprobantes de Pago</a></li>
			        <li><a name="tsCupa">Cuentas por pagar</a></li>
			        <?php if(isset($f->session->tasks['ts.cheq'])){ ?>
	        			<li><a name="tsCheq">Cheques</a></li>
	        		<?php } ?>
			    </ul>
			</li>
			<?php } ?>

			

			<?php if(isset($f->session->tasks['ts.rein'])){ ?>
				<!--<li><a href="#"><i class="fa fa-paperclip fa-fw"></i> Cajas Chicas <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level collapse">
						<li><a name="tsCjdo">Documentos</a></li>
				        <li><a name="tsCjse">Sesiones</a></li>
				    </ul>
				</li>-->
				<li><a href="#"><i class="fa fa-book fa-fw"></i> Libro Bancos y Caja Bancos <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level collapse">
						<li><a name="tsRein">Recibos de Ingreso</a></li>
				        <!--<li><a name="tsLiba">Libro Bancos --- antiguo</a></li>-->
						<!--<li><a name="tsLibo">Libro Bancos</a></li>-->
				    </ul>
				</li>
				<li><a href="#"><i class="fa fa-gears fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
					<ul class="nav nav-third-level collapse">
						<li><a name="tsCjch">Cajas Chicas</a></li>
						<li><a name="tsConc">Conceptos</a></li>
						<li><a name="tsCtban">Cuentas Bancarias</a></li>
				        <li><a name="tsTipo">Tipos de medio de Pagos </a></li>
				    </ul>
				</li>
			<?php } ?>
			<!-- <?php if(isset($f->session->tasks['ts.cheq'])){ ?>
			<li><a name="tsCheq">Cheques</a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['ts.comp'])){ ?>
			<li><a name="tsComp">Comprobantes de Pago</a></li>
			<li><a name="tsCupa">Cuentas por pagar</a></li>
			<?php } ?> -->
			<!--<?php if(isset($f->session->tasks['ts.rein'])){ ?>
			 <li><a name="tsRein">Recibos de Ingreso</a></li>
			<li><a name="tsCjdo"> Caja Chica - Documentos</a></li>
			<li><a name="tsCjse"> Caja Chica - Sesiones</a></li>
			<li><a name="tsLiba">Libro Bancos --- antiguo</a></li>
			<li><a name="tsLibo">Libro Bancos</a></li>
			<?php } ?> -->
		</ul>
	</li>
	<?php } ?>
	<?php if(isset($f->session->tasks['ct'])){ ?>
	<li>
		<a name="ct">
			<i class="fa fa-cutlery"></i>
			<span class="nav-label">Contabilidad</span>
			<span class="fa arrow"></span>
		</a>
		<ul class="nav nav-second-level collapse in">
			<li><a href="#"><i class="fa fa-gears fa-spin fa-fw"></i> Config. Inicial <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level collapse">
			        <?php //if(isset($f->session->tasks['pe.grup'])){ ?>
			        <li><a name="ctPcon">Plan contable</a></li>
			        <?php //} ?>
			        <?php //if(isset($f->session->tasks['pe.grup'])){ ?>
			        <li><a name="ctTnot">Tipos de Nota</a></li>
			        <?php //} ?>
			    </ul>
			</li>
			<li><a name="ctVeoc">Verificar O/C</a></li>
			<li><a name="ctVeos">Verificar O/S</a></li>
			<li><a name="ctNotc">Notas de Contabilidad</a></li>
			<li><a name="ctAuxs">Auxiliares Standard</a></li>
			<li><a name="ctCntg"><i class="fa fa-life-ring"></i> Contingencia</a></li>
			<li><a name="ctRepo">Reportes</a></li>
		</ul>
    </li>
	<?php } ?>
	<!--<?php if(isset($f->session->tasks['cj'])){ ?>
	 <li>
		<a><i class="fa fa-dashboard"></i> <span class="nav-label">Caja</span> <span class="fa arrow"></span></a>
    </li>
	<?php } ?>-->
	<!--<?php if(isset($f->session->tasks['al'])){ ?>
	 <li>
		<a name="al">
			<i class="fa fa-balance-scale"></i>
			<span class="nav-label">Asesor&iacute;a Legal</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <?php if(isset($f->session->tasks['al.dilg'])){ ?>
            <li>
                <a>Diligencias <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <li><a name="alDiliProg"><i class="fa fa-clock-o"></i> Programadas</a></li>
                    <li><a name="alDiliEjec"><i class="fa fa-check-circle-o"></i> Ejecutadas</a></li>
                    <li><a name="alDiliSusp"><i class="fa fa-ban"></i> Suspendidas</a></li>
                </ul>
            </li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['al.expd'])){ ?>
            <li>
                <a>Expedientes <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <li><a name="alExpdActi"><i class="fa fa-check-square-o"></i> Activos</a></li>
                    <li><a name="alExpdArch"><i class="fa fa-archive"></i> Archivados</a></li>
                </ul>
            </li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['al.cont'])){ ?>
            <li>
                <a>Contigencias <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level collapse">
                    <li><a name="alContFav"><i class="fa fa-check"></i> A Favor</a></li>
                    <li><a name="alContCont"><i class="fa fa-close"></i> En Contra</a></li>
                </ul>
            </li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['al.conv'])){ ?>
	        <li><a name="alConv"><i class="fa fa-thumbs-o-up"></i> Convenios</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['al.reps'])){ ?>
	        <li><a name="alRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
	        <?php } ?>
		</ul>
	</li>
	<?php } ?>-->
	<?php if(isset($f->session->tasks['pr'])){ ?>
	 <li>
		<a name="pr">
			<i class="fa fa-dashboard"></i>
			<span class="nav-label">Presupuesto</span>
			<span class="fa arrow"></span>
		</a>
		<!-- <ul class="nav nav-second-level collapse in">
			<?php if(isset($f->session->tasks['pr.plan.prog'])){ ?>
            <li><a name="mgEnti"><i class="fa fa-users"></i> Plan Operativo - Programaci&oacute;n </a></li>
			<?php } ?>
			<?php if(isset($f->session->tasks['pr.plan.ejec'])){ ?>
            <li><a name="mgOrga"><i class="fa fa-sitemap"></i> Plan Operativo - Ejecuci&oacute;n </a></li>
			<?php } ?>
		</ul> -->
    </li>
	<?php } ?>
	<!--<?php if(isset($f->session->tasks['ct'])){ ?>
	 <li>
		<a><i class="fa fa-dashboard"></i> <span class="nav-label">Contabilidad</span> <span class="fa arrow"></span></a>
    </li>
	<?php } ?>-->
	<?php if(isset($f->session->tasks['ac'])){ ?>
	 <li>
		<a name="ac">
			<i class="fa fa-user-secret"></i>
			<span class="nav-label">Seguridad</span>
			<span class="fa arrow"></span>
		</a>
        <ul class="nav nav-second-level collapse in">
	        <?php if(isset($f->session->tasks['ac.logs'])){ ?>
	        <li><a name="acLogs"><i class="fa fa-book"></i> Registro de Acciones</a></li>
	        <?php } ?>
	        <li><a name="acLogp"><i class="fa fa-hdd-o"></i> Registro Personal</a></li>
	        <?php if(isset($f->session->tasks['ac.groups'])){ ?>
	        <li><a name="acGrup"><i class="fa fa-users"></i> Grupos</a></li>
	        <?php } ?>
	        <?php if(isset($f->session->tasks['ac.users'])){ ?>
	        <li><a name="acUser"><i class="fa fa-user"></i> Usuarios</a></li>
	        <?php } ?>
	        <li><a name="acRepo"><i class="fa fa-line-chart"></i> Reportes</a></li>
		</ul>
	</li>
	<?php } ?>
</ul>