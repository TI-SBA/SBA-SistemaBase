<table>
	<input type="hidden" name="lastdata">
	<tr>
		<td><label>N&uacute;mero</label></td>
		<td><span name="numero"></span></td>
	</tr>
	<tr>
		<td><label>Demandante</label></td>
		<td><input type="text" name="demandante" size="38"></td>
	</tr>
	<tr>
		<td><label>Demandado</label></td>
		<td><input type="text" name="demandado" size="38"></td>
	</tr>
	<tr>
		<td><label>Tipo</label></td>
		<td><span name="tipo"></span></td>
	</tr>
	<tr>
		<td><label>Materia</label></td>
		<td><input type="text" name="materia" size="38"></td>
	</tr>
	<tr>
		<td><label>Juzgado</label></td>
		<td><input type="text" name="juzgado" size="38"></td>
	</tr>
	<tr>
		<td><label>Estado</label></td>
		<td><textarea name="estado" cols="35"></textarea></td>
	</tr>
	<tr>
		<td><label>Ubicaci&oacute;n</label></td>
		<td><textarea name="ubicacion" cols="35"></textarea></td>
	</tr>
	<tr>
		<td><label>Observaciones</label></td>
		<td><textarea name="observ" cols="35"></textarea></td>
	</tr>
	<tr>
		<td><label>Relacionado con Inmuebles:</label></td>
		<td><label name="labinmu"></label><span name="FilIn">
			<input type="hidden" name="inmueble">
			<input type="radio" name="rbtnTipo" id="rbtnSelectS" value="S"><label for="rbtnSelectS">Si</label>
			<input type="radio" name="rbtnTipo" id="rbtnSelectN" value="N" checked="checked"><label for="rbtnSelectN">No</label>
		</span>
		</td>
	</tr>
</table>