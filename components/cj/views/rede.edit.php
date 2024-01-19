<fieldset>
	<legend>Detalles del Documento</legend>
	<table>
		<tr>
			<td><label>Fecha de Registro</label></td>
			<td><input type"text" name="fec" /></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero</label></td>
			<td><input type="text" name="num" /></td>
		</tr>
		<tr>
			<td><label>A nombre de </label></td>
			<td><span name="entidad"></span>&nbsp;<button name="btnEnti">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Concepto</label></td>
			<td><textarea name="concepto" rows="2" cols="40"></textarea></td>
		</tr>
		<tr>
			<td><label>Total</label></td>
			<td><input type="text" name="total" /></td>
		</tr>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><span name="cuenta"></span>&nbsp;<button name="btnCta">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>Documento Adjunto</label></td>
			<td><input type="text" name="origen" /></td>
		</tr>
	</table>
</fieldset>
<fieldset>
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