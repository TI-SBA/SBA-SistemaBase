<?php global $f; ?>
<form class="form-horizontal" role="form">
<div class="form-group">
		<label class="col-sm-4 control-label">Nro de Parte Diario: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="num" required style="width:300px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Modulo</label>
			<div class="col-sm-8">
				<select class="form-control" name="pabe" type = "text" style="width:300px" required>
					<option value="MH">Salud Mental</option>
					<option value="AD">Adicciones</option>
				</select>
			</div>
	</div>
	<div name="medico"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-4 control-label">Fecha de Registro: </label>
		<div class="col-sm-7.5 input-group">
			<input type="text" class="form-control"  name="fech" style="width:300px" required>
		</div>
	</div>

</form>