<table>
	<tr>
		<td colspan="2"><label>P&oacute;liza Contable N&deg; </label><input type="text" name="cod" size="8"></td>
	</tr>
	<tr>
		<td><label>Descripci&oacute;n</label></td>
		<td><textarea rows="1" cols="30" name="descr"></textarea></td>
	</tr>
</table>
<fieldset>
	<legend>Contabilidad Patrimonial</legend>
	<table>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><span name="cuenta"></span>&nbsp;<button name="btnCuenta"></button></td>
		</tr>
		<tr>
			<td><label>Debe/Haber</label></td>
			<td>
				<input type="radio" id="rbtnPoli1" name="rbtnPoli" value="D" checked="checked" /><label for="rbtnPoli1">D</label>
				<input type="radio" id="rbtnPoli2" name="rbtnPoli" value="H" /><label for="rbtnPoli2">H</label>
			</td>
		</tr>
		<tr>
			<td><label>Moneda</label></td>
			<td><span>Nuevos Soles</span></td>
		</tr>
		<tr>
			<td><label>Importe</label></td>
			<td><input type="text" name="importe" size="7"></td>
		</tr>
		<tr>
			<td colspan="2"><button name="btnAgre">Agregar</button></td>
		</tr>
	</table>
	<div class="grid payment" style="overflow: hidden;width: 470px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">Importe</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Haber</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="max-height: 110px;width: 470px;">
		<div class="gridBody" width="470px"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:200px;max-width:200px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:70px;max-width:70px;"><button name="btnEli">Eliminar</button></li>
			</ul>
		</div>
	</div>
</fieldset>