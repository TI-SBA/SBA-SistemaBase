	<table>
			<tr>			
				<td><b><label>Periodo: </label></b> <select name="mes"><option value="1">Enero</option>
				<option value="2">Febrero</option>
				<option value="3">Marzo</option>
				<option value="4">Abril</option>
				<option value="5">Mayo</option>
				<option value="6">Junio</option>
				<option value="7">Julio</option>
				<option value="8">Agosto</option>
				<option value="9">Setiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6"></td>
				<td><b><label>Estado: </label></b> <span name="estado"></span></td>
				<td><button name="btnCerrarperiodo">Cerrar Periodo</button> <button name="btnAgregar">Agregar Registro</button></td>				
			</tr>
			<tr>
				<td><b><label>Dependencia: </label></b> <span name="depen"></span><button name="btnOrga">Seleccionar</button></td>
				<td colspan="2"><b><label>Clasificaci&oacute;n Econ&oacute;mica: </label></b> <select name="fuen"></select></td>
			</tr>
			<tr>				
				<td colspan="2"><b><label>Sub-Especif&iacute;ca</label></b> <span name="clas_sub"></span><button name="btnSub">Seleccionar</button></td>
				<td><label>Meta</label> <span name="meta"></span> <button name="btnMeta">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="3"><b><label>Descripci&oacute;n</label></b> <span name="descr_sub"></span></td>
			</tr>
		</table>		
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:30px;max-width:30px;">&nbsp;</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Fecha</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Mes</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Dia</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Comprobante</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Clase</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">N&deg;</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">Detalle</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">Ejecuci&oacute;n de Presupuesto</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Haber</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Saldo</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">Ejecuci&oacute;n de Ingresos</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Haber</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Saldo</li>
					</ul>
				</li>	
			</ul>
		</div>
	</div>
	<div class="grid" style="height: 150px;">
	<div class="gridBody" ></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:30px;max-width:30px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>			
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:240px;max-width:240px;"></li>
			<li style="min-width:80px;max-width:80px;"></li>
			<li style="min-width:80px;max-width:80px;"></li>
			<li style="min-width:80px;max-width:80px;"></li>
			<li style="min-width:80px;max-width:80px;"></li>
			<li style="min-width:80px;max-width:80px;"></li>
			<li style="min-width:80px;max-width:80px;"></li>
		</ul>
	</div>
	</div>