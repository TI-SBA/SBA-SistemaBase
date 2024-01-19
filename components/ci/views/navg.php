<? global $f; ?>
<div id="sidebar-left" class="col-xs-2 col-sm-2">
	<ul class="nav main-menu">
		<?php if(isset($f->session->tasks['mg'])){ ?>
		<li>
			<a href="ajax/dashboard.html" class="active ajax-link">
				<i class="fa fa-dashboard"></i>
				<span class="hidden-xs">Maestros Generales</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['td'])){ ?>
		<li>
			<a href="ajax/dashboard.html" class="ajax-link">
				<i class="fa fa-dashboard"></i>
				<span class="hidden-xs">Tr&aacute;mite Documentario</span>
			</a>
			<ul class="dropdown-menu">
				<?php if(isset($f->session->tasks['td.tdoc'])){ ?>
	            <li>
	                <a name="tdTdocs">Tipos de Documentos</a>
	            </li>
				<?php } ?>
				<?php if(isset($f->session->tasks['td.orga'])){ ?>
	            <li>
	                <a name="tdOrga">&Oacute;rganos Externos para el TUPA</a>
	            </li>
				<?php } ?>
				<?php if(isset($f->session->tasks['td.orga'])){ ?>
	            <li>
	                <a name="tdComi">Comites</a>
	            </li>
				<?php } ?>
				<?php if(isset($f->session->tasks['td.tupa'])){ ?>
	            <li>
	                <a name="tdTupa">TUPA</a>
	            </li>
				<?php } ?>
			</ul>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['cm'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-bar-chart-o"></i>
				<span class="hidden-xs">Cementerio</span>
			</a>
			<ul class="dropdown-menu">
				<?php if(isset($f->session->tasks['cm.oper.conc'])){ ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-plus-square"></i>
						<span class="hidden-xs">Concesiones</span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="#">Recientes</a></li>
						<li><a href="#">Vencidas</a></li>
						<li><a href="#">Por Vencer</a></li>
						<li><a href="#">Todas</a></li>
					</ul>
				</li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.oper'])){ ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-plus-square"></i>
						<span class="hidden-xs">Operaciones</span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="#" name="cmOperPro">Programadas</a></li>
						<li><a href="#" name="cmOperAll">Todas</a></li>
					</ul>
				</li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.ctas'])){ ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">
						<i class="fa fa-plus-square"></i>
						<span class="hidden-xs">Cuentas</span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="#">Ocupantes</a></li>
						<li><a href="#">Propietarios</a></li>
					</ul>
				</li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.espc'])){ ?>
				<li><a class="ajax-link" name="cmPabe">Pabellones</a></li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.espc'])){ ?>
				<li><a class="ajax-link" name="cmEspa">Espacios</a></li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.espc'])){ ?>
				<li><a class="ajax-link" href="ajax/charts_xcharts.html">Mapa</a></li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.accs'])){ ?>
				<li><a class="ajax-link" name="cmAcce">Accesorios</a></li>
				<?php } ?>
				<?php if(isset($f->session->tasks['cm.reps'])){ ?>
				<li><a class="ajax-link" href="ajax/charts_xcharts.html">Reportes</a></li>
				<?php } ?>
			</ul>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['lg'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-table"></i>
				 <span class="hidden-xs">Log&iacute;stica</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['pe'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-pencil-square-o"></i>
				 <span class="hidden-xs">Personal</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['in'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" name="in">
				<i class="fa fa-desktop"></i>
				 <span class="hidden-xs">Inmuebles</span>
			</a>
			<ul class="dropdown-menu">
	            <li><a name="inTipo">Tipo de Local</a></li>
	            <li><a name="inSubl">SubLocal</a></li>
	            <li><a name="inInmu">Inmuebles</a></li>
	            <li><a name="inMoti">Motivos de Contrato</a></li>
	            <li><a name="inCalp">Calendario de Pagos</a></li>
	            <li><a name="inCalv">Vencimiento de Letras</a></li>
	            <li><a name="inConf">Configuraci&oacute;n de Caja</a></li>
	            <li><a name="inMovi">Movimientos</a></li>
	            
	            
	            
	            
	            
	            <li><a name="inActa">Generar Acta de Conciliaci&oacute;n</a></li>
	            
	            
	            
	            
	            
	            
	            <li><a name="inPlay">Playas</a></li>
	            <li><a name="inImpl">Importar Playas</a></li>
	            <li><a name="inComp">Comprobantes de Pago</a></li>
	            <li><a name="inRein">Recibos de Ingresos</a></li>
			</ul>
		</li>
		<?php } ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" name="us">
				<i class="fa fa-cutlery"></i>
				<span class="hidden-xs">USA</span>
			</a>
			<ul class="dropdown-menu">
	            <li><a name="usCoci">Cocinas</a></li>
	       		<li><a name="usUnid">Unidades</a></li>
	            <li><a name="usIngr">Ingredientes</a></li>
	            <li><a name="usPedi">Pedido de Raciones</a></li>
	            <li><a name="usRepe">Recepci&oacute;n de Pedidos</a></li>
	            <li><a name="usRece">Recetario</a></li>
	            <li><a name="usProg">Programaci&oacute;n Semanal</a></li>
	            <li><a name="usCons">Consumo de Insumos</a></li>
	            <li><a name="usRepo">Reportes</a></li>
			</ul>
	    </li>
		<?php if(isset($f->session->tasks['ts'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-list"></i>
				 <span class="hidden-xs">Tesorer&iacute;a</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['cj'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-map-marker"></i>
				<span class="hidden-xs">Caja</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['al'])){ ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle">
				<i class="fa fa-picture-o"></i>
				 <span class="hidden-xs">Asesor&iacute;a Legal</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['pr'])){ ?>
		<li>
			 <a class="ajax-link" href="ajax/typography.html">
				 <i class="fa fa-font"></i>
				 <span class="hidden-xs">Presupuesto</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['ct'])){ ?>
		<li>
			 <a class="ajax-link" href="ajax/typography.html">
				 <i class="fa fa-font"></i>
				 <span class="hidden-xs">Contabilidad</span>
			</a>
		</li>
		<?php } ?>
		<?php if(isset($f->session->tasks['ac'])){ ?>
		 <li>
			<a class="ajax-link" href="ajax/calendar.html">
				 <i class="fa fa-calendar"></i>
				 <span class="hidden-xs">Seguridad</span>
			</a>
		 </li>
		<?php } ?>
	</ul>
</div>