<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Comprobante Original</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Nuevo Cliente</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Servicio</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Comprobante</li>
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
		<legend>Comprobante Original</legend>
		<table>
			<tr>
				<td width="165"><label>Caja</label></td>
				<td><span name="caja"></span></td>
			</tr>
			<tr>
				<td><label>Comprobante</label></td>
				<td><span name="comp"></span></td>
			</tr>
			<tr>
				<td><label>Serie del Comprobante</label></td>
				<td><span name="serie"></span></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero del Comprobante</label></td>
				<td><span name="num"></span></td>
			</tr>
			<tr>
				<td><label>Nombre:</label></td>
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
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Nuevo Cliente</legend>
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
	<fieldset name="section3">
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
	<fieldset name="section4">
		<legend>Comprobante por la operaci&oacute;n de Cambio de Nombre</legend>
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
		</table>
	</fieldset>
	<fieldset name="section5">
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