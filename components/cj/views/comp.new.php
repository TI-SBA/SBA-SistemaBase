<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Cliente</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Comprobante</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Servicios</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Forma de Pago</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Cliente</legend>
		<button name="btnAgr">Agregar</button><button name="btnSel">Buscar</button>
		<table>
			<tr>
				<td width="72"><label>Nombre:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>Apellidos:</label></td>
				<td><span name="apell"></span></td>
			</tr>
			<tr>
				<td><label>DNI/RUC:</label></td>
				<td><span name="dni"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
			<tr>
				<td><label>Tel&eacute;fono:</label></td>
				<td><span name="telef"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Comprobante</legend>
		<table>
			<tr>
				<td><label>Fecha de emisi&oacute;n</label></td>
				<td><input type="text" name="fecreg"></td>
			</tr>
			<tr>
				<td><label>Caja</label></td>
				<td><select name="caja"></select></td>
			</tr>
			<tr>
				<td><label>Comprobante</label></td>
				<td><select name="comp"></select></td>
			</tr>
			<tr>
				<td><label>Serie del Recibo</label></td>
				<td><select name="serie"></select></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero del Recibo</label></td>
				<td><span name="num"></span></td>
			</tr>
			<tr>
				<td><label>Observaci&oacute;n</label></td>
				<td><textarea cols="40" rows="2" name="observ"></textarea></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3">
		<legend>Servicios</legend>
		<div class="grid payment" style="overflow: hidden;width: 470px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:30px;max-width:30px;">N&deg;</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:220px;max-width:220px;">Servicios</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Importe Total</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">SubTotal</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 580px;width: 470px;">
			<div class="gridBody" width="470px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:30px;max-width:30px;"></li>
					<li style="min-width:220px;max-width:220px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section4">
		<legend>Forma de Pago</legend>
		<div class="grid payment" style="max-height: 580px;width: 500px;">
			<div class="gridBody" width="470px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:130px;max-width:130px;"></li>
					<li style="min-width:130px;max-width:130px;"></li>
					<li style="min-width:130px;max-width:130px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>