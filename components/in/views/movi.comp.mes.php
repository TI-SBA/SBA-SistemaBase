<?php global $f; ?>
<div class="panel col-md-4">
    <div class="panel-heading">
    	<form class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-sm-4 control-label">Tipo de Local</label>
				<span class="form-control" name="tipo"></span>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Sublocal</label>
				<span class="form-control" name="sublocal"></span>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Inmueble</label>
				<span class="form-control" name="inmueble"></span>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Titular</label>
				<span class="form-control" name="titular"></span>
			</div>
			<?php $f->response->view('mg/enti.mini'); ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Fecha de Emisi&oacute;n</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input class="form-control" name="fecemi" type="text" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Caja</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
					<select class="form-control" name="caja"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Comprobante</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="comp"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Serie</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="serie"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">N&uacute;mero</label>
				<div class="input-group col-sm-8">
					<input class="form-control" name="num" type="number" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Observaciones</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
					<textarea rows="3" class="form-control" name="observ"></textarea>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="col-md-8">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">Inmueble en Comprobante</label>
			<div class="input-group col-sm-8">
				<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
				<input class="form-control" name="texto_inmueble" type="text" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Glosa del Comprobante</label>
			<div class="input-group col-sm-8">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<textarea rows="3" class="form-control" name="texto_pago"></textarea>
			</div>
		</div>
	</form>
	<fieldset>
		<legend>Detalle de Pago</legend>
		<div name="gridServ"></div>
	</fieldset>
	<fieldset>
		<legend>Forma de Pago</legend>
		<div name="gridForm"></div>
	</fieldset>
</div>
<hr />