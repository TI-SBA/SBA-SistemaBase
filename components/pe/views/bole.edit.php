<div class="panel panel-primary">
	<div class="panel-heading">
		<i class="fa fa-user"></i> Datos del Trabajador
	</div>
	<div class="panel-body">
		<form class="form-horizontal row" role="form">
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Trabajador</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
					<span class="form-control" name="nomb"></span>
					<span class="input-group-btn">
						<button name="btnSelEnt" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">DNI</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-credit-card fa-fw"></i></span>
					<span class="form-control" name="dni"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Organizaci&oacute;n</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-sitemap fa-fw"></i></span>
					<span class="form-control" name="organizacion"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Cargo</label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="cargo"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Actividad</label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="actividad"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Componente</label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="componente"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Nivel Remunerativo</label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="nivel"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Carnet ESSALUD</label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="nomb"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Sistema de Pensi&oacute;n</label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="pension"></span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">C.U.I. </label>
				<div class="col-sm-8 input-group">
					<span class="form-control" name="cui"></span>
				</div>
			</div>
			<div class="form-group col-sm-12">
				<label class="col-sm-4 control-label">Periodo</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input type="text" class="form-control" name="periodo">
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Fecha Inicio</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input type="text" class="form-control" name="fecini">
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">Fecha Fin</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input type="text" class="form-control" name="fecfin">
				</div>
			</div>
			<div class="form-group col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">
						<input type="checkbox" class="i-checks" name="vacaciones" value="1">
					</span>
					<span class="form-control">Vacaciones</span>
				</div>
			</div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4 control-label">D&iacute;as Trabajados</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-briefcase fa-fw"></i></span>
					<input type="text" class="form-control" name="dias_trab">
				</div>
			</div>
		</form>
	</div>
</div>
<button name="btnRefresh" type="button" class="btn btn-primary btn-lg btn-block"><i class="fa fa-refresh"></i> Haga click aqu&iacute; para refrescar los c&aacute;lculos!</button>
<div class="panel panel-warning">
	<div class="panel-heading">
		<i class="fa fa-file-text-o"></i> Conceptos del Documento
	</div>
	<div class="panel-body">
		<div>
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#pagos" aria-controls="pagos" role="tab" data-toggle="tab"><i class="fa fa-plus"></i> Pagos</a></li>
				<li role="presentation"><a href="#descuentos" aria-controls="descuentos" role="tab" data-toggle="tab"><i class="fa fa-minus"></i> Descuentos</a></li>
				<li role="presentation"><a href="#aportes" aria-controls="aportes" role="tab" data-toggle="tab"><i class="fa fa-bank"></i> Aportes</a></li>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane fade in active" id="pagos">
					<div name="gridPago"></div>
				</div>
				<div role="tabpanel" class="tab-pane fade" id="descuentos">
					<div name="gridDesc"></div>
				</div>
				<div role="tabpanel" class="tab-pane fade" id="aportes">
					<div name="gridApor"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-success">
	<div class="panel-heading">
		<i class="fa fa-credit-card"></i> A pagar
	</div>
	<div class="panel-body">
		<div class="form-group col-sm-4">
			<label class="col-sm-4 control-label">Neto</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="neto"></span>
			</div>
		</div>
		<div class="form-group col-sm-4">
			<label class="col-sm-4 control-label">Redondeo</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="redondeo">S/.0.00</span>
			</div>
		</div>
		<div class="form-group col-sm-4">
			<label class="col-sm-4 control-label">Neto a pagar</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="neto_pagar"></span>
			</div>
		</div>
	</div>
</div>