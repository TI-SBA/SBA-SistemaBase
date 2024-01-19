<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Historia Clinica</label>
			<div class="col-md-3">
				<span class="form-control" name="hist_cli"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
			<label class="col-sm-2 control-label">Pabellon</label>
				<div class="col-sm-8">
				<select class="form-control" name="pabellon" type = "text" style="width:300px" required>
					<option value="Intermedio">Intermedio</option>
					<option value="Intensivo">Intensivo</option>
					<option value="Parcial">Parcial</option>
				</select>
			</div>
	</div>

	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-3 control-label">Fecha de Traslado: </label>
		<div class="col-sm-7 input-group">
			<input type="text" class="form-control"  name="fec_tras" style="width:300px" required>
		</div>
	</div>
</form>

