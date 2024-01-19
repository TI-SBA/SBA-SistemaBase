<form>
	<div class="form-group">
		<label>Tipo</label>
		<select name="tipo" class="form-control">
			<option value="A">Activo</option>
			<option value="P">Pasivo</option>
			<option value="PT">Patrimonio</option>
			<option value="I">Ingresos</option>
			<option value="G">Gastos</option>
			<option value="R">Resultados</option>
			<option value="PR">Presupuesto</option>
			<option value="O">Orden</option>
		</select>
	</div>
	<div class="form-group">
		<label>C&oacute;digo</label>
		<td><b><span name="parentcod"></span></b><input type="text" name="cod" size="28" placeholder="Ingrese el c&oacute;digo de cuenta contable" class="form-control">
		</td>
		<td><span name="confir"></span></td>
		<td><span name="confirtxt"></span></td>
	</div>
	<div class="form-group">
		<label>Descripci&oacute;n</label>
		<textarea name="nomb" class="form-control"></textarea>
	</div>
</form>