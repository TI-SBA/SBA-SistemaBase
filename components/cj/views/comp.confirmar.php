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
					<li style="min-width:146px;max-width:146px;">Comprobante</li>
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
</div>