<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de Hospitalizaci&oacute;n</label>
		<div class="col-sm-8">
			<select class="form-control" name="tipo_hosp">
				<option value="S">S/E</option>
				<option value="C">Completa</option>
				<option value="P">Parcial</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Categor&iacute;a</label>
		<div class="col-sm-8">
			<select class="form-control" name="categoria">
				<option value="14">Categoria A</option>	
				<option value="12">Categoria B</option>
				<option value="13">Categoria C</option>
				<option value="8">Indigente</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Mensual</label>
		<div class="col-sm-8">
			<input type="number" class="form-control" name="mensual">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Diario</label>
		<div class="col-sm-8">
			<input type="number" class="form-control" name="diario">
		</div>
	</div>
</form>