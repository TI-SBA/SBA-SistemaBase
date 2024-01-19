<fieldset name="section1" style="height:65px;">
	<legend>Propietario</legend>
	<table>
		<tr>
			<td width="72"><label>Nombre:</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td width="72"><label>Apellidos:</label></td>
			<td><span name="apell"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset name="section2">
	<legend>Espacio</legend>
	<table>
			<tr>
				<td><label>Mausoleo</label></td>
				<td colspan=3><span name="espacio"></span></td>
			</tr>
			<tr>
				<td><label>Recepcionado por</label></td>
				<td colspan=3><span name="recibido"></span>&nbsp;<button name="btnSelEnti">Seleccionar</button><button name="btnAgrEnti">Agregar</button></td>
			</tr>
			<tr>
				<td><label>Capacidad</label></td>
				<td width="176"><input type="text" name="txtCantidad"></td>
				<td><label>Tipo</label></td>
				<td width="176"><select name="cboSelTipo" style="width:160px;">
					<option value="B">Nicho-B&oacute;veda</option>
					<option value="C">Capilla</option>
					<option value="R">Cripta</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Ancho</label></td>
				<td width="176"><input type="text" name="txtAncho"></td>
				<td><label>Largo</label></td>
				<td width="176"><input type="text" name="txtLargo"></td>
			</tr>
			<tr>
				<td><label>Altura 1</label></td>
				<td width="176"><input type="text" name="txtAltura1"></td>
				<td><label>Altura 2</label></td>
				<td width="176"><input type="text" name="txtAltura2"></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td colspan="3"><textarea name="observ" cols="30" rows="3"></textarea></td>
			</tr>
	</table>
</fieldset>