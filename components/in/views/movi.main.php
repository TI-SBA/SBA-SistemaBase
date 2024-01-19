<div class="panel panel-primary">
	<div class="panel-heading">
		<i class="fa fa-home"></i> Seleccione su Inmueble
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">Tipo de Local</span>
					<select class="form-control" name="tipo"></select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">SubLocal</span>
					<select class="form-control" name="sublocal"></select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">Inmueble</span>
					<select class="form-control" name="inmueble"></select>
					<span class="input-group-btn">
						<button name="btnRefresh" type="button" class="btn btn-info"><i class="fa fa-refresh"></i></button>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-warning">
	<div class="panel-heading">
		<i class="fa fa-user"></i> Arrendatarios y sus contratos del Inmueble
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-6" name="gridArre"></div>
			<div class="col-sm">
				<div class="col-lg-6" name="gridCont"></div>
				<div class="col-lg-6">
					Observaciones:
					<span class="label label-default" name="obsrv"></span>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-success">
	<div class="panel-heading">
		<i class="fa fa-money"></i> Pagos realizados del Contrato
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-6" name="gridPag"></div>
			<div class="col-lg-6" name="gridCta"></div>
		</div>
	</div>
</div>