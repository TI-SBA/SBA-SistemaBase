<fieldset>
	<legend>Datos de Hospitalizaci&oacute;n</legend>
	<form class="form-horizontal" role="form">
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Paciente</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="paciente"></span>
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">N&deg; Historia</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="hist_cli"></span>
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Modalidad</label>
			<div class="input-group col-sm-8">
				<select class="form-control" name="modalidad">
					<option value="M">Mensual</option>
					<option value="D">Diario</option>
				</select>
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Cantidad</label>
			<div class="input-group col-sm-8">
				<input type="number" class="form-control" name="cant">
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Tipo Hospitalizaci&oacute;n</label>
			<div class="input-group col-sm-8">
				<select class="form-control" name="tipo_hosp">
					<option value="S">S/E</option>
					<option value="C">Completa</option>
					<option value="P">Parcial</option>
				</select>
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Fecha Inicial</label>
			<div class="input-group col-sm-8">
				<input type="text" class="form-control" name="fecini">
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Fecha Final</label>
			<div class="input-group col-sm-8">
				<input type="text" class="form-control" name="fecfin">
			</div>
		</div>
		<!--<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Fecha Alta</label>
			<div class="input-group col-sm-8">
				<input type="text" class="form-control" name="fecalt">
			</div>
		</div>-->
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Categor&iacute;a</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="categoria"></span>
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Importe</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="importe"></span>
			</div>
		</div>
	</form>
</fieldset>
<hr />
<fieldset>
	<legend>Datos de Facturaci&oacute;n</legend>
	<form class="form-horizontal" role="form">
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Fecha</label>
			<div class="input-group col-sm-8">
				<input type="text" class="form-control" name="fecpag">
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">N&deg;</label>
			<div class="input-group col-sm-8">
				<input type="number" class="form-control" name="num">
			</div>
		</div>
	</form>
</fieldset>
<hr />