<?php global $f; ?>
<div name="grid" class="col-sm-4"></div>
<div class="col-sm-8" name="details">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-3 control-label">Cliente</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
				<span class="form-control" name="cliente"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Generado desde</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-gears fa-fw"></i></span>
				<span class="form-control" name="tipo"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Inmueble</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
				<span class="form-control" name="inmueble"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Playa</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
				<span class="form-control" name="playa"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Subtotal</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="subtotal"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">IGV</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="igv"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Moras</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="moras"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Total</label>
			<div class="col-sm-9 input-group">
				<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
				<span class="form-control" name="total"></span>
			</div>
		</div>
	</form>
	<hr />
	<div class="embed-responsive embed-responsive-16by9">
		<iframe class="embed-responsive-item" src="…"></iframe>
		<div name="loading">
			<img src="<?=$f->request->root?>images/loading.gif" class="img-responsive col-xs-12">
		</div>
	</div>
</div>