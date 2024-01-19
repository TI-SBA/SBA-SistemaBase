<fieldset>
	<legend>Datos Generales</legend>
	<table>
		<tr>
			<td width="100px"><label>Arrendatario</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td><label>DNI/RUC</label></td>
			<td><span name="dni"></span></td>
		</tr>
		<tr>
			<td><label>Inmueble Matriz</label></td>
			<td><span name="inmueble_matriz"></span></td>
		</tr>
		<tr>
			<td><label>Inmueble</label></td>
			<td><span name="inmueble"></span></td>
		</tr>
		<tr>
			<td><label>Fecha</label></td>
			<td><span name="fec"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Servicio</legend>
	<table>
		<tr>
			<td><label>Servicio</label></td>
			<td><span name="serv"></span>&nbsp;<button name="btnServ">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Fecha de vencimiento</label></td>
			<td><input type="text" size="11" name="fecven"></td>
		</tr>
	</table>
	<div class="grid payment" style="overflow: hidden;width: 480px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:380px;max-width:380px;">Concepto</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Precio</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="/*max-height: 110px;*/width: 480px;">
		<div class="gridBody" width="480px"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:280px;max-width:280px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
			</ul>
		</div>
	</div>
</fieldset>