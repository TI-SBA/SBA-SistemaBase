<fieldset>
	<legend>Cliente</legend>
	<table>
		<tr>
			<td width="70px"><label>Nombre</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td><label>DNI/RUC</label></td>
			<td><span name="dni"></span></td>
		</tr>
		<tr>
			<td><label>Direcci&oacute;n</label></td>
			<td><span name="direc"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Servicio</legend>
	<table>
		<tr>
			<td><label>Servicio</label></td>
			<td><span name="serv"></span></td>
		</tr>
		<tr>
			<td><label>Fecha de vencimiento</label></td>
			<td><span name="fecven"></span></td>
		</tr>
	</table>
	<div class="grid payment" style="overflow: hidden;width: 500px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:380px;max-width:380px;">Concepto</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Precio</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="/*max-height: 110px;*/width: 500px;">
		<div class="gridBody" width="480px"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:380px;max-width:380px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
			</ul>
		</div>
	</div>
</fieldset>