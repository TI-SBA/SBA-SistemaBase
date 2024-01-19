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
			<td><input type="text" name="fecemi" size="11" /></td>
			<td><label>Fecha de vencimiento</label></td>
			<td><input type="text" name="fecven" size="11" /></td>
		</tr>
		<tr>
			<td><label>Tipo de Comprobante de Pago</label></td>
			<td><select name="tico"></select></td>
			<td><label>Serie</label></td>
			<td><input type="text" name="serie" size="8"/></td>
		</tr>
		<tr>
			<td><label>A&ntilde;o DUA/DSI</label></td>
			<td><input type="text" name="ano" size="5" /></td>
			<td><label>N&uacute;mero</label></td>
			<td><input type="text" name="num" size="10" /></td>
		</tr>
		<tr>
			<td><label>Importe total sin cr&eacute;dito fiscal</label></td>
			<td><input type="text" name="importe" size="8" /></td>
			<td><label>Documento de Referencia</label></td>
			<td><input type="text" name="docref"/></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Cliente</legend>
	<table>
		<tr>
			<td width="145px"><label>Apellidos y Nombres, denominaci&oacute;n o raz&oacute;n social</label></td>
			<td><span name="proveedor"></span>&nbsp;<button name="btnProv">Seleccionar</button>&nbsp;<button name="btnAgrProv">Agregar</button></td>
			<td><label>Tipo de Documento de Identidad</label></td>
			<td><select name="tdoc" style="width: 110px;" disabled="disabled">
				<option value="0">0 : Otros tipos de Documentos</option>
				<option value="1">1: Documento Nacional de Identidad</option>
				<option value="4">4: Carnet de Extranjer&iacute;a</option>
				<option value="6" selected="selected">6: RUC</option>
				<option value="7">7: Pasaporte</option>
				<option value="A">A : C&eacute;dula Diplom&aacute;tica de Identidad</option>
			</select></td>
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
			<td><label>Base imponible de adquisici&oacute;n tipo 1</label></td>
			<td><div><input type="text" name="adq1" size="4" /></div><span name="def">0.00</span></td>
			<td><label>IGV/IPM adquisici&oacute;n tipo 1</label></td>
			<td><div><input type="text" name="igv1" size="4" /></div><span name="def">0.00</span></td>
		</tr>
		<tr>
			<td><label>Base imponible de adquisici&oacute;n tipo 2</label></td>
			<td><div><input type="text" name="adq2" size="4" /></div><span name="def">0.00</span></td>
			<td><label>IGV/IPM adquisici&oacute;n tipo 2</label></td>
			<td><div><input type="text" name="igv2" size="4" /></div><span name="def">0.00</span></td>
		</tr>
		<tr>
			<td><label>Base imponible de adquisici&oacute;n tipo 3</label></td>
			<td><div><input type="text" name="adq3" size="4" /></div><span name="def">0.00</span></td>
			<td><label>IGV/IPM adquisici&oacute;n tipo 3</label></td>
			<td><div><input type="text" name="igv3" size="4" /></div><span name="def">0.00</span></td>
		</tr>
		<tr>
			<td><label>Valor de adquisiones no gravadas</label></td>
			<td><input type="text" name="valor_adq" size="4" /></td>
			<td><label>Monto del ISC</label></td>
			<td><input type="text" name="monto_isc" size="4" /></td>
		</tr>
		<tr>
			<td><label>Otros tributos y cargos</label></td>
			<td><input type="text" name="otros" size="4" /></td>
			<td><label>Importe total de las adquisiones registradas</label></td>
			<td><input type="text" name="importe_tot" size="4" /></td>
		</tr>
		<tr>
			<td><label>Tipo de cambio</label></td>
			<td><input type="text" name="tc" size="4" /></td>
			<td><label>N&uacute;mero del CP emitido por sujeto no domiciliado</label></td>
			<td><input type="text" name="num_cp" size="6" /></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Detracci&oacute;n</legend>
	<table>
		<tr>
			<td><label>Fecha de constancia</label></td>
			<td><input type="text" name="feccon" size="11" value="01/01/0001" /></td>
			<td><label>N&uacute;mero de constancia</label></td>
			<td><input type="text" name="num_cons" size="10" value="0" /></td>
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
			<td><label>Fecha de emisi&oacute;n</label></td>
			<td><input type="text" name="fecemi_mod" size="11" value="01/01/0001"></td>
			<td><label>Tipo de comprobante de pago</label></td>
			<td><select name="tico"></select></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de serie</label></td>
			<td><input type="text" name="num_ser"></td>
			<td><label>N&uacute;mero</label></td>
			<td><input type="text" name="num_mod"></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Programa</legend>
	<table>
		<tr>
			<td><label>Organizaci&oacute;n</label></td>
			<td><span name="orga"></span>&nbsp;<button name="btnOrg">Seleccionar</button></td>
		</tr>
	</table>
</fieldset>