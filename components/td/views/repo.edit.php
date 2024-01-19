<table>
	<tr>
		<td><b>Desde</b></td>
		<td><input type="text" name="desde"></td>
	</tr>
	<tr>
		<td><b>Hasta</b></td>
		<td><input type="text" name=hasta></td>
	</tr>
	<tr>
		<td><b>Usuario</b></td>
		<td><span name="usuario"></span> <button name="btnUsu">Buscar</button></td>
	</tr>
	<tr>
		<td><b>Oficina</b></td>
		<td><span name="oficina"></span> <button name="btnOfi">Buscar</button></td>
	</tr>
	<tr>
		<td><b>Procedimiento TUPA</b></td>
		<td><span name="proc"></span> <button name="btnProc">Buscar</button></td>
	</tr>
	<tr>
		<td>Por vencimiento de plazo Seg&uacute;n TUPA</td>
		<td><span name="rbtnVenc">
			<input id="rbtnVenc_1" type="radio" name="venc" value="1"><label for="rbtnVenc_1">Si</label>
			<input id="rbtnVenc_0" type="radio" name="venc" checked="checked" value="0"><label for="rbtnVenc_0">No</label>
		</td>
	</tr>
	<tr>
		<td>Por procedimientos no atendidos</td>
		<td><span name="rbtnNoaten">
			<input id="rbtnNoaten_1" type="radio" name="noaten" value="1"><label for="rbtnNoaten_1">Si</label>
			<input id="rbtnNoaten_0" type="radio" name="noaten" checked="checked" value="0"><label for="rbtnNoaten_0">No</label>
		</span></td>
	</tr>
</table>