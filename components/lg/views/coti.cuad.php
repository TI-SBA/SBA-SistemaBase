<?php global $f; ?>
<fieldset class="row">
	<legend>Referencia del Cotizante</legend>
	<div class="col-sm-6">
		<div class="form-group">
			<label class="col-sm-4 control-label">Proveedor</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-users fa-fw"></i></span>
				<select class="form-control" name="proveedor"></select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Propuesta N&deg;</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-file-text-o fa-fw"></i></span>
				<span class="form-control" name="num"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Fecha de entrega ofrecida</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
				<span class="form-control" name="fecentofer"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Documento de Referencia</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<span class="form-control" name="ref"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Observaciones</label>
			<div class="input-group col-sm-8">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<span class="form-control" name="observ"></span>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<?=$f->response->view('mg/enti.mini');?>
	</div>
	<button class="btn btn-block btn-danger btn-lg" name="btnEli"><i class="fa fa-trash-o"></i> ELIMINAR PROPUESTA SELECCIONADA</button>
</fieldset>
<fieldset>
	<legend>Items a Cotizar</legend>
	<hr />
	<h4>Productos de la cotizaci&oacute;n</h4>
	<hr />
	<div name="grid_prod"></div>
	<hr />
	<h4>Servicios de la cotizaci&oacute;n</h4>
	<hr />
	<div name="grid_serv"></div>
	<hr />
</fieldset>