<?php global $f; ?>
<h2 name="cod"></h2>
<form class="form-horizontal" role="form">
	<div class="col-sm-6">
		<div class="form-group">
			<label class="col-sm-4 control-label">Fecha de entrega m&aacute;xima</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
				<span class="form-control" name="fecent"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Fecha de entrega ofrecida</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
				<input type="text" class="form-control" name="fecentofer" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Documento de Referencia</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<input type="text" class="form-control" name="ref" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Observaciones</label>
			<div class="input-group col-sm-8">
				<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
				<textarea cols="30" rows="" class="form-control" name="observ"></textarea>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<?=$f->response->view('mg/enti.mini')?>
	</div>
</form>
<hr />
<h4>Productos de la cotizaci&oacute;n</h4>
<hr />
<div name="grid_prod"></div>
<hr />
<h4>Servicios de la cotizaci&oacute;n</h4>
<hr />
<div name="grid_serv"></div>
<hr />