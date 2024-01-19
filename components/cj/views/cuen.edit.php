<fieldset>
	<legend>Cliente</legend>
	<table>
		<tr>
			<td colspan="2"><button name="btnSelEnt">Buscar</button>&nbsp;<button name="btnAgrEnt">Agregar</button></td>
		</tr>
		<tr>
			<td><label>Nombre</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td><label>Apellidos</label></td>
			<td><span name="apel"></span></td>
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
			<td><span name="serv"></span>&nbsp;<button name="btnServ">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Fecha de vencimiento</label></td>
			<td><input type="text" size="11" name="fecven"></td>
		</tr>
		<tr>
			<td><label>Hora de Evento</label></td>
			<td><input type="text" size="11" name="evento"></td>
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
				<li style="min-width:280px;max-width:280px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
			</ul>
		</div>
	</div>
</fieldset>