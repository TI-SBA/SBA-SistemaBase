<?php global $f; ?>
<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detalle" aria-controls="detalle" role="tab" data-toggle="tab">General</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Revisiones</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="detalle">
			<?php global $f; ?>
			<form class="form-horizontal" role="form">
				<?=$f->response->view('mg/enti.mini')?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Referencia</label>
					<div class="col-sm-8">
						<span class="form-control" name="ref"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Fuentes de Financiamiento</label>
					<div class="col-sm-8">
						<span class="form-control" name="fuente"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Observaciones</label>
					<div class="col-sm-8">
						<span class="form-control" name="observ"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Lugar</label>
					<div class="col-sm-8">
						<span class="form-control" name="lugar"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Fecha estimada de ejecucion</label>
					<div class="col-sm-8 input-group">
						<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
						<span class="form-control" name="feceje"></span>
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
			<fieldset>
				<legend>Auxiliar</legend>
				<div name="gridAuxi"></div>
			</fieldset>
			<hr />
		</div>
		<div role="tabpanel" class="tab-pane fade" id="profile">
			<div class="ibox-content inspinia-timeline"></div>
		</div>
	</div>
</div>