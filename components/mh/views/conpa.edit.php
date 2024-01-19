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
		<label class="col-sm-2 control-label">Modulo</label>
			<div class="col-md-3">
				<span class="form-control" name="modulo"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Categoria</label>
			<div class="col-md-3">
				<span class="form-control" name="cate"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Cie10</label>
			<div class="col-md-3">
				<span class="form-control" name="cie10"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Diagnostico</label>
			<div class="col-md-3">
				<span class="form-control" name="diag"  style="width:250px"></span>
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
	<div class="form-group">
			<label class="col-sm-2 control-label">Sala</label>
				<div class="col-sm-8">
				<select class="form-control" name="sala" type = "text" style="width:300px" required>
					<option value="V">Varones</option>
					<option value="M">Muejeres</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Tipo de Hospitalizacion</label>
			<div class="col-md-3">
				<span class="form-control" name="tipo_hosp"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-3 control-label">Fecha de Inicio de Hospitalizacion: </label>
		<div class="col-sm-7 input-group">
			<input type="text" class="form-control"  name="fec_inicio" style="width:300px" required>
		</div>
	</div>
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-3 control-label">Fecha de Fin de Hospitalizacion: </label>
		<div class="col-sm-7 input-group">
			<input type="text" class="form-control"  name="fec_fin" style="width:300px" required>
		</div>
	</div>
	
	
</form>

