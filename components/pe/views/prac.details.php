<ul>
	<li><a href="#tabs-1">Entidad</a></li>
	<li><a href="#tabs-2">Historial de Propinas</a></li>
</ul>
<div id="tabs-1">
	<fieldset>
		<legend>Practicante</legend>
		<table>
			<tr>
				<td rowspan="4"><img name="foto" width="100" height="100"></td>
				<td><label>Nombre</label></td>
				<td><div name="nomb" class="ellipsis-text" style="width: 270px;"></div></td>
			</tr>
			<tr>
				<td><label>Documento de Identidad</label></td>
				<td><div name="docident" class="ellipsis-text" style="width: 180px;"></div></td>
			</tr>
			<tr>
				<td><label>Domicilio</label></td>
				<td><div name="direc" class="ellipsis-text" style="width: 270px;"></div></td>
			</tr>
			<tr>
				<td><label>Tel&eacute;fono</label></td>
				<td><div name="telf" class="ellipsis-text" style="width: 180px;"></div></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Propina</legend>
		<table>
			<tr>
				<td><label>Monto</label></td>
				<td><span name="monto"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Ingreso a SBPA</legend>
		<table>
			<tr>
				<td><label>Fecha de ingreso</label></td>
				<td><span name="fec"></span></td>
			</tr>
			<tr>
				<td><label>Organizaci&oacute;n</label></td>
				<td><span name="orga"></span></td>
			</tr>
		</table>
	</fieldset>
</div>
<div id="tabs-2">
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Periodo</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:80px;max-width:80px;">Monto</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Registrado por</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Fecha de Registro</li>
			</ul>
		</div>
		<div class="gridBody"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:80px;max-width:80px;"></li>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
			</ul>
		</div>
	</div>
</div>