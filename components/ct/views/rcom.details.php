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
			<td width="130px"><label>Fecha de emisi&oacute;n</label></td>
			<td width="180px"><span name="fecemi"></span></td>
			<td width="140px"><label>Fecha de vencimiento</label></td>
			<td><span name="fecven"></span></td>
		</tr>
		<tr>
			<td><label>Tipo de Comprobante de Pago</label></td>
			<td><span name="tico"></span></td>
			<td><label>Serie</label></td>
			<td><span name="serie"></span></td>
		</tr>
		<tr>
			<td><label>A&ntilde;o DUA/DSI</label></td>
			<td><span name="ano"></span></td>
			<td><label>N&uacute;mero</label></td>
			<td><span name="num"></span></td>
		</tr>
		<tr>
			<td><label>Importe total sin cr&eacute;dito fiscal</label></td>
			<td><span name="importe"></span></td>
			<td><label>Documento de Referencia</label></td>
			<td><span name="docref"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Proveedor</legend>
	<table>
		<tr>
			<td width="145px"><label>Apellidos y Nombres, denominaci&oacute;n o raz&oacute;n social</label></td>
			<td width="210px"><span name="proveedor"></span></td>
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
	<legend>Adquisiciones</legend>
	<ul>
		<ol><b>Adquisici&oacute;n tipo 1</b>: Adquisiciones gravadas  que dan derecho a cr&eacute;dito fiscal y/o saldo a favor de exportaci&oacute;n, destinadas exclusivamente a operaciones gravadas y/o de exportaci&oacute;n.</ol>
		<ol><b>Adquisici&oacute;n tipo 2</b>: Adquisiciones gravadas  que dan derecho a cr&eacute;dito fiscal y/o saldo a favor de exportaci&oacute;n, destinadas exclusivamente a operaciones gravadas y/o de  exportaci&oacute;n y a operaciones no gravadas.</ol>
		<ol><b>Adquisici&oacute;n tipo 3</b>: Adquisiciones gravadas que no dan derecho a cr&eacute;dito fiscal y/o saldo a favor de exportaci&oacute;n, por no estar destinadas a operaciones gravadas y/o de exportaci&oacute;n.</ol>
	</ul>
	<div>
		<input type="radio" id="rbtnAdq1" name="rbtnAdq" value="1" checked="checked" /><label for="rbtnAdq1">Adquisici&oacute;n tipo 1</label>
		<input type="radio" id="rbtnAdq2" name="rbtnAdq" value="2" /><label for="rbtnAdq2">Adquisici&oacute;n tipo 2</label>
		<input type="radio" id="rbtnAdq3" name="rbtnAdq" value="3" /><label for="rbtnAdq3">Adquisici&oacute;n tipo 3</label>
	</div>
	<table>
		<tr>
			<td width="160px"><label>Base imponible de adquisici&oacute;n tipo 1</label></td>
			<td width="120px"><span name="adq1"></span></td>
			<td width="180px"><label>IGV/IPM adquisici&oacute;n tipo 1</label></td>
			<td><span name="igv1"></span></td>
		</tr>
		<tr>
			<td><label>Base imponible de adquisici&oacute;n tipo 2</label></td>
			<td><span name="adq2"></span></td>
			<td><label>IGV/IPM adquisici&oacute;n tipo 2</label></td>
			<td><span name="igv2"></span></td>
		</tr>
		<tr>
			<td><label>Base imponible de adquisici&oacute;n tipo 3</label></td>
			<td><span name="adq3" size="4"></span></td>
			<td><label>IGV/IPM adquisici&oacute;n tipo 3</label></td>
			<td><span name="igv3" size="4"></span></td>
		</tr>
		<tr>
			<td><label>Valor de adquisiones no gravadas</label></td>
			<td><span name="valor_adq"></span></td>
			<td><label>Monto del ISC</label></td>
			<td><span name="monto_isc"></span></td>
		</tr>
		<tr>
			<td><label>Otros tributos y cargos</label></td>
			<td><span name="otros"></span></td>
			<td><label>Importe total de las adquisiones registradas</label></td>
			<td><span name="importe_tot"></span></td>
		</tr>
		<tr>
			<td><label>Tipo de cambio</label></td>
			<td><span name="tc"></span></td>
			<td><label>N&uacute;mero del CP emitido por sujeto no domiciliado</label></td>
			<td><span name="num_cp"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Detracci&oacute;n</legend>
	<table>
		<tr>
			<td width="160px"><label>Fecha de constancia</label></td>
			<td width="130px"><span name="feccon"></span></td>
			<td width="190px"><label>N&uacute;mero de constancia</label></td>
			<td><span name="num_cons"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Marca del comprobante de pago sujeto a retenci&oacute;n</legend>
	<div>
		<input type="radio" id="rbtnMar1" name="rbtnMar" value="0" checked="checked" /><label for="rbtnMar1">0</label>
		<input type="radio" id="rbtnMar2" name="rbtnMar" value="1" /><label for="rbtnMar2">1</label>
	</div>
</fieldset>
<fieldset>
	<legend>Estado</legend>
	<div>
		<input type="radio" id="rbtnEst1" name="rbtnEst" value="1" checked="checked" /><label for="rbtnEst1">1</label>
		<input type="radio" id="rbtnEst2" name="rbtnEst" value="6" /><label for="rbtnEst2">6</label>
		<input type="radio" id="rbtnEst3" name="rbtnEst" value="7" /><label for="rbtnEst3">7</label>
		<input type="radio" id="rbtnEst4" name="rbtnEst" value="9" /><label for="rbtnEst4">9</label>
	</div>
</fieldset>
<fieldset>
	<legend>Comprobante de Pago que se modifica</legend>
	<table>
		<tr>
			<td width="120px"><label>Fecha de emisi&oacute;n</label></td>
			<td width="140px"><span name="fecemi_mod"></span></td>
			<td width="130px"><label>Tipo de comprobante de pago</label></td>
			<td><span name="tico"></span></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de serie</label></td>
			<td><span name="num_ser"></span></td>
			<td><label>N&uacute;mero</label></td>
			<td><span name="num_mod"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Programa</legend>
	<table>
		<tr>
			<td width="100px"><label>Organizaci&oacute;n</label></td>
			<td><span name="orga"></span></td>
		</tr>
	</table>
</fieldset>