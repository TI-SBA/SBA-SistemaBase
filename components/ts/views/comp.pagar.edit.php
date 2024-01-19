<table class="table table-bordered">
	<tbody>
		<tr>
			<td colspan="2"><b><label>Comprobante de Pago N&deg; <span name="cod"></span></label></b></td>
		</tr>
		<tr>
			<td><label>Nombre</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td><label>Son</label></td>
			<td><span name="monto_string"></span><span style="display:none;" name="monto"></span></td>
		</tr>
		<tr>
			<td><label>Descripci&oacute;n</label></td>
			<td><span name="descr" ></span></td>
		</tr>
	</tbody>
</table>
<fieldset>
	<legend>Detalle del Gasto</legend>
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td style="text-align:right;" colspan="3">Total Pago</td>
				<td style="width:120px;"><input type="text" name="conc_total_pago" class="form-control" value="0.00" disabled></td>
			</tr>

			<tr>
				<td style="text-align:right;" colspan="3">Total Descuento</td>
				<td style="width:120px;"><input type="text" name="conc_total_desc" class="form-control" value="0.00" disabled></td>
			</tr>

			<tr>
				<td style="text-align:right;" colspan="3">Total Neto</td>
				<td style="width:120px;"><input type="text" name="conc_total_neto" class="form-control" value="0.00" disabled></td>
			</tr>
		</tbody>
	</table>
</fieldset>
<fieldset>
	<legend>Movimiento de la Cuenta Corriente</legend>
	<table>
		<tr>
			<td><label>C&oacute;digo de la Operaci&oacute;n</label></td>
			<td><span name="cod_oper"></span></td>
		</tr>
		<tr>
			<td><label>Fecha de la Operaci&oacute;n</label></td>
			<td><span name="fec_oper"></span></td>
		</tr>
		<tr>
			<td><label>Medio de Pago</label></td>
			<td><select name="medio_pago" class="form-control"></select></td>
		</tr>
		<tr>
			<td><label>Descripci&oacute;n</label></td>
			<td><textarea name="mov_descr"></textarea></td>
		</tr>
		<tr>
			<td><label>Apellidos y Nombres, Denominaci&oacute;n o Raz&oacute;n Social</label></td>
			<td><span name="mov_bene"></span></td>
		</tr>
		<tr>
			<td><label>Tipo de Documento de Identidad</label></td>
			<td><select name="mov_tdoc" class="form-control"></select></td>
		</tr>
		<tr>
			<td><label>N&uacute;mero de documento de Identidad:</label></td>
			<td><span name="mov_ndoc"></span></td>
		</tr>
		<tr>
			<td><label>Forma de Pago</label></td>
			<td><span name="forma_pago"></span></td>
		</tr>
		<tr>
			<td><label>Cuenta Bancaria</label></td>
			<td><span name="cuen_ban"></span></td>
		</tr>
		<tr>
			<td><label>Documento Sustentatorio</label></td>
			<td><span name="doc_sust"></span></td>
		</tr>
		<tr name="tdoc_sust_tr">
			<td><label>Tipo de Documento Sustentatorio</label></td>
			<td><select name="tdoc_sust" class="form-control"></select></td>
		</tr>
		<tr name="doc_sust_trans_tr">
			<td><label>Documento Sustentatorio</label></td>
			<td><input type="text" name="doc_sust_trans" size="38"></td>
		</tr>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><input type="text" name="cuenta" size="25" placeholder="Cuenta Contable" style="float: left;"><span name="result-cuen" class="ui-icon ui-icon-circle-close" style="float: left;"></span>&nbsp;<button name="btnCuen" style="float: left;">Seleccionar</button></td>
		</tr>
	</table>
</fieldset>