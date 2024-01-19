<fieldset>
	<legend>Trabajador</legend>
	<table>
		<tr>
			<td rowspan="6"><img name="foto" width="100" height="100"></td>
			<td width="125px"><label>Nombre</label></td>
			<td><div name="nomb" class="ellipsis-text" style="width: 170px;"></div></td>
			<td><label>Organizaci&oacute;n</label></td>
			<td><div name="orga" class="ellipsis-text" style="width: 170px;"></div></td>
		</tr>
		<tr>
			<td><label>Cargo</label></td>
			<td><span name="cargo"></span></td>
			<td><label>Regimen Laboral</label></td>
			<td><span name="contrato"></span></td>
		</tr>
		<tr>
			<td><label>Nivel Remunerativo de Carrera</label></td>
			<td><span name="nivel_carrera"></span></td>
			<td><label>R&eacute;gimen Pensionario</label></td>
			<td><span name="pension"></span></td>
		</tr>
		<tr>
			<td><label>Nivel Remunerativo designado</label></td>
			<td colspan="3"><span name="nivel"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Remuneraci&oacute;n</legend>
	<table>
		<tr>
			<td width="140px"><label>Descripci&oacute;n</label></td>
			<td colspan="3"><span name="descr"></span></td>
		</tr>
		<tr>
			<td><label>Resoluci&oacute;n de Otorgamiento</label></td>
			<td colspan="3"><span name="reso"></span></td>
		</tr>
		<tr>
			<td><label>Desde</label></td>
			<td width="90px"><span name="fecini"></span></td>
			<td width="60px"><label>Hasta</label></td>
			<td><span name="fecfin"></td>
		</tr>
		<tr>
			<td><label>Tiempo de Servicios</label></td>
			<td colspan="3"><span name="tiempo"></span></td>
		</tr>
	</table>
	<div name="tabs">
	    <ul>
	        <li><a href="#tabs-1">Pagos</a></li>
	    </ul>
	    <div id="tabs-1" class="gridCont">
	        <div class="grid" style="overflow: hidden;width: 600px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Concepto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Monto</li>
					</ul>
				</div>
			</div>
	        <div class="grid" style="height: 200px;width: 620px;">
				<div class="gridBody" width="630px"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:400px;max-width:400px;"></li>
						<li style="min-width:180px;max-width:180px;"></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>A Pagar</legend>
	<table>
		<tr>
			<td><label>Neto a pagar</label></td>
			<td><span name="neto"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Data Adicional</legend>
	<table>
		<tr>
			<td><label>Nota</label></td>
			<td><span name="nota"></span></td>
		</tr>
	</table>
</fieldset>