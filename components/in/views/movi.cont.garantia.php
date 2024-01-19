<form class="form-horizontal" role="form">
	<div class="form-group col-lg-12">
		<label class="col-sm-4 control-label">Documento</label>
		<div class="col-sm-8">
			<select class="form-control" name="tipo"></select>
		</div>
	</div>
	<div class="form-group col-lg-6">
		<label class="col-sm-4 control-label">Nro</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="num">
		</div>
	</div>
	<div class="form-group col-lg-6">
		<label class="col-sm-4 control-label">Fecha Garan&iacute;a</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fec">
		</div>
	</div>
	<div class="form-group col-lg-6">
		<label class="col-sm-4 control-label">Moneda</label>
		<div class="col-sm-8">
			<select class="form-control" name="moneda">
				<option value="S">Soles (S/.)</option>
				<option value="D">D&oacute;lares ($)</option>
			</select>
		</div>
	</div>
	<div class="form-group col-lg-6">
		<label class="col-sm-4 control-label">Importe Garant&iacute;a</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="importe">
		</div>
	</div>
</form>
<fieldset>
	<legend>Devoluci&oacute;n</legend>
	<form class="form-horizontal" role="form">
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Importe</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="dev_importe">
			</div>
		</div>
		<div class="form-group col-lg-6">
			<label class="col-sm-4 control-label">Fecha</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="dev_fec">
			</div>
		</div>
	</form>
</fieldset>