<fieldset>
	<legend>Trabajador</legend>
	<table>
		<tr>
			<td><label>Nombre</label></td>
			<td><span name="trab"</span> <button name="btnSelectTrab">Seleccionar</button></td>
		</tr>
		<tr>
			<td><label>DNI</label></td>
			<td><span name="trab_dni"></span></td>
		</tr>
		<tr>
			<td><label>Nivel Remunerativo</label></td>
			<td><span name="trab_niv"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Beneficiario</legend>
	<table>
		<tr>
			<td><label>Nombre</label></td>
			<td><input type="text" name="bene_nomb"></td>
		</tr>
		<tr>
			<td><label>DNI</label></td>
			<td><input type="text" name="bene_dni"></td>
		</tr>
		<tr>
			<td><label>Parentesco</label></td>
			<td><input type="text" name="bene_pare"></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Datos del Difunto <i>(<label>El Difunto es el Trabajador <input type="checkbox" name="check_difu"></label>)</i></legend>
	<table name="difunto">
		<tr>
			<td><label>Nombre</label></td>
			<td><input type="text" name="difu_nomb"></td>
		</tr>
		<tr>
			<td><label>DNI</label></td>
			<td><input type="text" name="difu_dni"></td>
		</tr>
	</table>
	<table>
		<tr>
			<td><label>Fecha de Fallecimiento</label></td>
			<td><input type="text" name="difu_fec"></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Pago</legend>
	<table>
		<tr>
			<td><label>REMUNERACION TOTAL</label></td>
			<td><b><span name="remu"></span></b></td>
		</tr>
		<tr>
			<td><label>Tres remuneraciones mensuales totales por subsidio por fallecimiento</label></td>
			<td><span name="subs_fall"></span></td>
		</tr>
		<tr>
			<td><label><input type="checkbox" name="sep_check"> Dos Remuneraciones mensuales totales por subsidio gastos por sepelio</label></td>
			<td><span name="subs_sepe"></span></td>
		</tr>
		<tr>
			<td><b><label>TOTAL</label></b></td>
			<td><span name="total_pagar"></span></td>
		</tr>
	</table>
</fieldset>