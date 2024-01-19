<?php global $f; ?>
<div class="col-sm-3">
	<?php $f->response->view('mg/enti.mini'); ?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-gears"></i> Fecha a programar y Leyendas
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-4 control-label">Fecha a programar</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="fecha">
				</div>
			</div>
			<hr />
			<br />
			<p class="bg-primary">Programados</p>
			<p class="bg-success">Turno</p>
		</div>
	</div>
</div>
<div class="col-sm-9">
	<div class="panel panel-success">
		<div class="panel-heading">
			<i class="fa fa-calendar"></i> Horarios Programados
		</div>
		<div class="panel-body">
			<div name="calendar"></div>
		</div>
	</div>
</div>