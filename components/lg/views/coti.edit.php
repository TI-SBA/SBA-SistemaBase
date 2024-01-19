<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Concurso</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="cod"></span>
		</div>
	</div>
	<?=$f->response->view('mg/enti.mini')?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha requerida de Cierre</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
			<input type="text" class="form-control" name="fecent" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha de Cierre</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
			<input type="text" class="form-control" name="feccie" />
		</div>
	</div>
</form>
<h4>Requerimientos</h4>
<hr />
<div name="grid_req"></div>
<hr />
<h4>Productos de la cotizaci&oacute;n</h4>
<hr />
<div name="grid_prod"></div>
<hr />
<h4>Servicios de la cotizaci&oacute;n</h4>
<hr />
<div name="grid_serv"></div>
<hr />