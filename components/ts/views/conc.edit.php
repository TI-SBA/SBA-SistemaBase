<table>
	<tbody>
		<tr>
			<td><label>Tipo</label></td>
			<td><select name="tipo">
				<option value="P">Pago</option>
				<option value="D">Descuento</option>
			</select></td>
		</tr>
		<tr>
			<td><label>Nombre</label></td>
			<td><input type="text" name="nomb" size="38"></td>
		</tr>
		<tr>
			<td><label>Descripci&oacute;n</label></td>
			<td><textarea name="descr" cols="35"></textarea></td>
		</tr>
		<tr>
			<td><label>Enlace</label></td>
			<td><span name="RadEnlace">
				<input type="radio" name="rbtnEnlace" id="rbtnEnlaceClas" value="C"><label for="rbtnEnlaceClas">Clasificador</label>
				<input type="radio" name="rbtnEnlace" id="rbtnEnlaceCuen" value="N" checked="checked"><label for="rbtnEnlaceCuen">Cuenta</label>
			</span></td>
		</tr>
		<tr name="tr_cuen">
			<td><label>Cuenta Contable</label></td>
			<td><input type="text" name="cuenta" size="25" placeholder="Cuenta Contable" style="float: left;"><span name="result-cuen" class="ui-icon ui-icon-circle-close" style="float: left;"></span>&nbsp;<button name="btnCuen" style="float: left;">Seleccionar</button></td>
		</tr>
		<tr name="tr_clas" style="display:none;">
			<td><label>Clasificador</label></td>
			<td><input type="text" name="clasif" size="25" placeholder="Clasificador" style="float: left;"><span name="result-clas" class="ui-icon ui-icon-circle-close" style="float: left;"></span>&nbsp;<button name="btnClas" style="float: left;">Seleccionar</button></td>
		</tr>
	</tbody>
</table>