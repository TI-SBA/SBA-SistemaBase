<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
			<select class="form-control" name="tipo">
				<option value="I">Ingreso</option>
				<option value="G">Gasto</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Organizaci&oacute;n</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-sitemap fa-fw"></i></span>
			<span class="form-control" name="orga"></span>
			<span class="input-group-btn">
				<button name="btnSel" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<input type="text" class="form-control" name="nomb">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Descripci&oacute;n</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-paragraph fa-fw"></i></span>
			<textarea rows="3" class="form-control" name="descr"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Abreviatura</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-paragraph fa-fw"></i></span>
			<textarea rows="3" class="form-control" name="abrev"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Servicio Aplicable a</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-wrench fa-fw"></i></span>
			<select class="form-control" name="aplicacion">
				<option value="O">Operaci&oacute;n</option>
				<option value="A">Cobro Administrativo</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Modulo</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-wrench fa-fw"></i></span>
			<select class="form-control" name="modulo">
				<option value="AD">Moises Heresi - Adicciones</option>
				<option value="MH">Moises Heresi - Salud Mental</option>
				<option value="IN">Inmuebles</option>
				<option value="LM">Laboratorio Mu&ntilde;oz</option>
				<option value="AG">Agua Chapi</option>
				<option value="PL">Playas</option>
				<option value="PA">Playas Azules</option>
				<option value="FM">Farmacia</option>
			</select>
		</div>
	</div>
</form>