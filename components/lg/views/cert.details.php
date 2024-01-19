<?php global $f; ?>
<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detalle" aria-controls="detalle" role="tab" data-toggle="tab">General</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Revisiones</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="detalle">
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="col-sm-4 control-label">Periodo</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
						<span class="form-control" name="periodo"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Programa</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-sitemap fa-fw"></i></span>
						<span class="form-control" name="programa"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Oficina</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-sitemap fa-fw"></i></span>
						<span class="form-control" name="oficina"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Trabajador que Registra</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
						<span class="form-control" name="trabajador"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Fuente de Financiamiento</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
						<span class="form-control" name="fuente"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Observaciones</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
						<span class="form-control" name="observ"></span>
					</div>
				</div>
			</form>
			<div name="grid"></div>
			<hr />
		</div>
		<div role="tabpanel" class="tab-pane fade" id="profile">
			<div class="ibox-content inspinia-timeline"></div>
		</div>
	</div>
</div>