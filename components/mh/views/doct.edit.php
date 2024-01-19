<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div name="medico"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Codigo de colegiatura: </label>
		<div class="col-sm-8 input-group">
			<input type="text" class="form-control" name="colegiatura">
		</div>
	</div>
</form>