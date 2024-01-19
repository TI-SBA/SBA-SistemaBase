<table>
	<tr>
		<td width="70px"><label>Periodo</label></td>
		<td width="220px"><span name="periodo"></span></td>
		<td width="120px"><label>C&oacute;digo Operaci&oacute;n</label></td>
		<td><span name="cod"></span></td>
	</tr>
</table>
<fieldset>
	<legend>Comprobante de Pago</legend>
	<table>
		<tr>
			<td><label>Fecha de emisi&oacute;n</label></td>
			<td><span name="fecemi"></span></td>
			<td><label>Fecha de vencimiento</label></td>
			<td><span name="fecven"></span></td>
		</tr>
		<tr>
			<td><label>Tipo de Comprobante de Pago</label></td>
			<td><span name="tipo_doc"></span></td>
			<td><label>Serie</label></td>
			<td><span name="serie"></span></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero</label></td>
			<td><span name="num"></span></td>
			<td><label>Importe total (Tickets)</label></td>
			<td><span name="ticket"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Operaciones</legend>
	<table>
		<tr>
			<td width="250px"><label>Valor facturado de la exportaci&oacute;n:</label></td>
			<td width="60px"><span name="valor_facturado"></span></td>
			<td width="250px"><label>Base imponible de la operaci&oacute;n gravada:</label></td>
			<td><span name="bi"></span></td>
		</tr>
		<tr>
			<td><label>Importe total de la operaci&oacute;n exonerada:</label></td>
			<td><span name="importe_exonerada"></span></td>
			<td><label>Importe total de la operaci&oacute;n inafecta:</label></td>
			<td><span name="importe_inafecta"></span></td>
		</tr>
		<tr>
			<td><label>ISC:</label></td>
			<td><span name="isc"></span></td>
			<td><label>IGV/IPM:</label></td>
			<td><span name="igv"></span></td>
		</tr>
		<tr>
			<td><label>Base imponible (arroz):</label></td>
			<td><span name="bi_arroz"></span></td>
			<td><label>Impuesto a las Ventas del arroz:</label></td>
			<td><span name="impuesto_arroz"></span></td>
		</tr>
		<tr>
			<td><label>Otros tributos y cargos</label></td>
			<td><span name="otros_tributos"></span></td>
			<td><label>Importe total del comprobante</label></td>
			<td><span name="importe_total"></span></td>
		</tr>
		<tr>
			<td><label>Tipo de cambio</label></td>
			<td colspan="3"><span name="tc"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Proveedor</legend>
	<table>
		<tr>
			<td width="145px"><label>Apellidos y Nombres, denominaci&oacute;n o raz&oacute;n social</label></td>
			<td><span name="proveedor"></span></td>
			<td><label>Tipo de Documento de Identidad</label></td>
			<td><span name="tdoc"></span></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de Documento de Identidad</label></td>
			<td colspan="3"><span name="docident"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Estado</legend>
	<div>
		<input type="radio" id="rbtnEst1" name="rbtnEst" value="1" checked="checked" /><label for="rbtnEst1">1</label>
		<input type="radio" id="rbtnEst2" name="rbtnEst" value="2" /><label for="rbtnEst2">2</label>
		<input type="radio" id="rbtnEst3" name="rbtnEst" value="8" /><label for="rbtnEst3">8</label>
		<input type="radio" id="rbtnEst4" name="rbtnEst" value="9" /><label for="rbtnEst4">9</label>
	</div>
</fieldset>
<fieldset>
	<legend>Comprobante de Pago que se modifica</legend>
	<table>
		<tr>
			<td><label>Fecha de emisi&oacute;n</label></td>
			<td><span name="fecemi_mod"></span></td>
			<td><label>Tipo de comprobante de pago</label></td>
			<td><span name="tipo_doc_mod"></span></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de serie</label></td>
			<td><span name="ser_doc_mod"></span></td>
			<td><label>N&uacute;mero</label></td>
			<td><span name="num_doc_mod"></span></td>
		</tr>
	</table>
</fieldset>