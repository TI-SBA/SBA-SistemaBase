<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Periodo</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
			<input type="text" class="form-control" name="periodo" />
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
			<select class="form-control" name="fuente"></select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Observaciones</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<textarea cols="30" rows="3" class="form-control" name="observ"></textarea>
		</div>
	</div>
</form>
<hr />
<div name="grid"></div>
<hr />