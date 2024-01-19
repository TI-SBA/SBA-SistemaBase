<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Actual</a></li>
		<li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Hist&oacute;rico</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="tab1">
			<fieldset>
				<legend>Informaci&oacute;n General</legend>
				<form class="form-horizontal" role="form">
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Dependencia</label>
						<div class="col-sm-8">
							<span class="form-control" name="dependencia"></span>
						</div>
					</div>
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Periodo</label>
						<div class="col-sm-8">
							<span class="form-control" name="periodo"></span>
						</div>
					</div>
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Trabajador</label>
						<div class="col-sm-8">
							<span class="form-control" name="trabajador"></span>
						</div>
					</div>
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Fecha de registro</label>
						<div class="col-sm-8">
							<span class="form-control" name="fecreg"></span>
						</div>
					</div>
					<div class="form-group col-lg-offset-6 col-lg-6">
						<label class="col-sm-4 control-label">Fecha de Vigencia</label>
						<div class="col-sm-8">
							<span class="form-control" name="fecvig"></span>
						</div>
					</div>
				</form>
			</fieldset>
			<hr />
			<div name="grid"></div>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab2">
			<fieldset>
				<legend><select class="form-control" name="historico"></select></legend>
				<form class="form-horizontal" role="form">
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Dependencia</label>
						<div class="col-sm-8">
							<span class="form-control" name="dependencia"></span>
						</div>
					</div>
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Periodo</label>
						<div class="col-sm-8">
							<span class="form-control" name="periodo"></span>
						</div>
					</div>
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Trabajador</label>
						<div class="col-sm-8">
							<span class="form-control" name="trabajador"></span>
						</div>
					</div>
					<div class="form-group col-lg-6">
						<label class="col-sm-4 control-label">Estado</label>
						<div class="col-sm-8">
							<span class="form-control" name="estado"></span>
						</div>
					</div>
				</form>
			</fieldset>
			<hr />
			<div name="gridHist"></div>
		</div>
	</div>
</div>
<hr />
<br/>