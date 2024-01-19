<fieldset>
	<legend></legend>
	<table>
		<tr>
			<td><label>Tipo de Inmueble Matriz</label></td>
			<td><span name="tipo"></span></td>
			<td rowspan="3"><img src="images/admin.png" style="width:100px;height:100px;"></td>
		</tr>
		<tr>
			<td><label>Direcci&oacute;n</label></td>
			<td><span name="direc"></span></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><span name="ref"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Inmuebles</legend>
	<button name="btnAgregar">Nuevo Inmueble</button>
	<div class="grid" style="width: 560px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Descripci&oacute;n</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Referencia</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">Uso</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:100px;max-width:100px;">Estado</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="width: 560px;height: 140px;">
		<div class="gridBody"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:170px;max-width:170px"></li>
				<li style="min-width:170px;max-width:170px"></li>
				<li style="min-width:120px;max-width:120px"></li>
				<li style="min-width:100px;max-width:100px"></li>
			</ul>
		</div>
	</div>
</fieldset>