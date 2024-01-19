<table>
	<tr>
		<td><b>A&ntilde;o</b></td>
		<td><input type="text" name="ano" style="width:80px;"></td>
		<td><b>Credito Suplementario</b></td>
		<td><select name="cred"></select></td>
	</tr>
	<tr>
		<td colspan="2"><button name="btnGenerar">Buscar</button></td>
		<td><button name="btnImprimir1">Imprimir Anexo A</button></td>
		<td><button name="btnImprimir2">Imprimir Anexo B</button></td>
	</tr>
</table>
<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:600px;max-width:600px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:600px;max-width:600px;">Partida</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Codigo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:450px;max-width:450px;">Partida</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Importe</li>
			</ul>
		</div>
</div>
<div class="grid">
	<div class="gridBody" name="gridtrans"></div>
	<div class="gridReference2">
		<ul>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:450px;max-width:450px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
		</ul>
	</div>
</div>