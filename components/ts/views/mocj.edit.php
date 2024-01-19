<table>
	<tbody>
		<tr>
			<td><label>Documento</label></td>
			<td><select name="doc">
				<option value="F">Factura</option>
				<option value="B">Boleta de Venta</option>
				<option value="T">Ticket</option>
				<option value="R">Recibo definitivo</option>
				<option value="H">Recibo por Honorarios</option>
			</select></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de documento</label></td>
			<td><input type="text" name="num" size="5"></td>
		</tr>
		
		<tr>
			<td><label>Fecha de emision</label></td>
			<td><input type="text"  name="fecreg"></td>
		</tr>
		<tr>
			<td><label>Beneficiario</label></td>
			<td><span name="beneficiario"></span>&nbsp;<button name="btnEnti" style="float: left;">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Concepto del Gasto</label></td>
			<td><input type="text" name="conc_g" size="38"></td>
		</tr>
		<tr>
			<td><label>Organizaci&oacute;n</label></td>
			<td><span name="orga"></span>&nbsp;<button name="btnOrga" style="float: left;">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Monto</label></td>
			<td><input type="text" name="monto" size="5"></td>
		</tr>
		<tr>
			<td><label>Clasificador</label></td>
			<td><span name="clasif"></span>&nbsp;<button name="btnClas" style="float: left;">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><span name="cuenta"></span>&nbsp;<button name="btnCta" style="float: left;">Seleccionar</button></td>
		</tr>
	</tbody>
</table>