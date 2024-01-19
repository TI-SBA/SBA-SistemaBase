<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fec">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Monto</label>
		<div class="col-sm-8">
			<input type="number" class="form-control" name="monto">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Descripci&oacute;n</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="descr">
		</div>
	</div>
	<div class="form-group">
		<div name="beneficiario"><?php $f->response->view('mg/enti.mini'); ?></div>
	</div>
	<!-- <div class="form-group">
		<label class="col-sm-4 control-label">Entidad</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="entidad"></span>
			<span class="input-group-btn">
				<button name="btnEnti" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div> -->
</form>