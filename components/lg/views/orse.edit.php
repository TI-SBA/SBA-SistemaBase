<?php global $f; ?>
<form class="form-horizontal" role="form">
	<?=$f->response->view('mg/enti.mini')?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Referencia</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="ref">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Fuentes de Financiamiento</label>
		<div class="col-sm-8">
			<select class="form-control" name="fuente"></select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Observaciones</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="observ"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Lugar</label>
		<div class="col-sm-8 input-group">
			<input type="text" name="lugar" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha estimada de ejecucion</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
			<input type="text" class="form-control" name="feceje">
		</div>
	</div>
</form>
<fieldset>
	<legend>Productos</legend>
	<div name="gridProd"></div>
</fieldset>
<fieldset>
	<legend>Afectaci&oacute;n Presupuestaria</legend>
	<div name="gridPres"></div>
</fieldset>
<hr />