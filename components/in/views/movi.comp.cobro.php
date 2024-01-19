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
					<input class="form-control" name="fecemi" type="text" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Caja</label>
				<div class="input-group col-sm-8">
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
					<textarea rows="3" class="form-control" name="observ"></textarea>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="col-md-8">
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