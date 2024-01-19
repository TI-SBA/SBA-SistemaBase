<table width="100%">
	<tr>
		<td><label>Periodo</label></td>
		<td><input type="text" size="11" name="periodo"><label>C&oacute;digo de la Cuenta Corriente</label><select name="ctban"></select></td>
		<td><button name="btnExportar">Exportar</button></td>
	</tr>
</table>
<div class="grid" style="overflow: hidden;width: 100%;">
	<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
		<ul>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Fecha</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Documento Sustentario</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Documento Originario</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:300px;max-width:300px;">Detalle</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:360px;max-width:360px;">
				<ul style="display:block">
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:360px;max-width:360px;">Movimiento</li>
				</ul>
				<ul style="display:block">
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Debe</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Haber</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Saldo</li>
				</ul>
			</li>
		</ul>
	</div>
</div>
<div class="grid" style="width: 100%;">
	<div class="gridBody" width="1080px"></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:120px;max-width:120px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:300px;max-width:300px;"></li>
			<li style="min-width:120px;max-width:120px;"></li>
			<li style="min-width:120px;max-width:120px;"></li>
			<li style="min-width:120px;max-width:120px;"></li>
		</ul>
	</div>
</div>