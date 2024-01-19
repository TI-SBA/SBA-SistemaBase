<fieldset>
	<legend>Movimiento de la Cuenta Corriente</legend>
	<table>
		<tr>
			<td><label>C&oacute;digo de la operaci&oacute;n</label></td>
			<td><span name="cod"></span></td>
		</tr>
		<tr>
			<td><label>Fecha de la operaci&oacute;n</label></td>
			<td><span name="fec"></span></td>
		</tr>
		<tr>
			<td><label>Medio de Pago</label></td>
			<td><select name="tmed"></select></td>
		</tr>
		<tr>
			<td><label>Descripci&oacute;n</label></td>
			<td><input type="text" name="descr" size="40"></td>
		</tr>
		<tr>
			<td><label>Apellidos y Nombres, Denominaci&oacute;n o Raz&oacute;n Social</label></td>
			<td><span name="entidad"></span><button name="btnEnti">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Tipo de Documento de Identidad</label></td>
			<td><select name="tdoc"></select></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de documento de Identidad</label></td>
			<td><span name="num"></span></td>
		</tr>
		<tr>
			<td><label>Cuenta Bancaria</label></td>
			<td><select name="ctban"></select></td>
		</tr>
		<tr>
			<td><label>N&deg; de Control Interno</label></td>
			<td><input type="text" name="control" size="30"></td>
		</tr>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><span name="cuenta"></span><button name="btnCta">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Tipo</label></td>
			<td>
				<input type="radio" id="rbtnTipo1" name="rbtnTipo" value="D" checked="checked" /><label for="rbtnTipo1">Debe</label>
				<input type="radio" id="rbtnTipo2" name="rbtnTipo" value="T" /><label for="rbtnTipo2">Haber</label>
			</td>
		</tr>
		<tr>
			<td><label>Monto</label></td>
			<td><input type="text" name="monto" size="6"></td>
		</tr>
	</table>
</fieldset>