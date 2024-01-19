<div class="col-lg-4">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">Tipo de Entidad</label>
			<div class="col-sm-8">
				<div class="iradio">
					<input value="P" type="radio" name="tipo_enti" id="tipo_enti_1" checked> <label for="tipo_expd_1">Persona</label><br />
					<span>(Se requerir&aacute;n los apellidos y su DNI)</span>
				</div><br />
				<div class="iradio">
					<input value="E" type="radio" name="tipo_enti" id="tipo_enti_2"> <label for="tipo_expd_2">Empresa</label><br />
					<span>(Se solicitar&aacute; el RUC de la misma)</span>
				</div>
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
			<label class="col-sm-4 control-label">Apellido Paterno</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<input type="text" class="form-control" name="appat">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Apellido Materno</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<input type="text" class="form-control" name="apmat">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Domicilio Fiscal</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<textarea class="form-control" name="direc_fis" placeholder="Direccion segun SUNAT."></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Domicilio Personal</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<textarea class="form-control" name="direc_per" placeholder="Direccion referencial. Puede ser la del DNI."></textarea>
			</div>
		</div>
	</form>
</div>
<div class="col-lg-4">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">DNI</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-credit-card fa-fw"></i></span>
				<input type="text" class="form-control" name="dni">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">RUC</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-credit-card-alt fa-fw"></i></span>
				<input type="text" class="form-control" name="ruc">
			</div>
		</div>
	</form>
	<img src="images/logo.jpg" class="img-responsive img-thumbnail center-block">
	<button name="btnFoto" class="btn btn-info btn-lg btn-block"><i class="fa fa-picture"></i> Subir Foto para Entidad</button>
</div>
<div class="col-lg-4">
	<div name="gridTele"></div>
	<div name="gridMail"></div>
	<div name="gridSiti"></div>
</div>