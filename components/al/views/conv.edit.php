<?php global $f; ?>
<div class="col-lg-6">
	<?php $f->response->view('mg/enti.mini'); ?>
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">Fecha Inicio</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="fecini">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Fecha Fin</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="fecfin">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Aportes SBPA</label>
			<div class="col-sm-8">
				<textarea rows="4" class="form-control" name="aportesbene"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Aportes Entidad</label>
			<div class="col-sm-8">
				<textarea rows="4" class="form-control" name="aportesenti"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Comisi&oacute;n</label>
			<div class="col-sm-8">
				<textarea rows="4" class="form-control" name="comision"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Adenda</label>
			<div class="col-sm-8">
				<textarea rows="4" class="form-control" name="adenda"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Digital del Convenio</label>
			<div class="col-sm-8">
				<span class="form-control" name="file"></span>
				<span class="input-group-btn">
					<button name="btnFile" type="button" class="btn btn-info"><i class="fa fa-picture-o"></i> Seleccionar archivo</button>
				</span>
			</div>
		</div>
	</form>
</div>
<div class="col-lg-6">
	<object width="100%" height="900px" type="application/pdf" data="">
</div>