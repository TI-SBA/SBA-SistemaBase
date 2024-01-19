<?php global $f;?>
<form class="form-horizontal" role="form">
	<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>Tipo</label>
				<select class="form-control" name="tipo_hosp">
					<option value="S">S/E</option>
					<option value="C">Completa</option>
					<option value="P">Parcial</option>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Modalidad</label>
				<select class="form-control" name="modalidad">
					<option value="M">Mensual</option>
					<option value="D">Diario</option>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Cantidad (Dias/Meses)</label>
				<input type="text" class="form-control" name="tipo_hosp">
			</div>	
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label>Fecha Inicial</label>
				<input type="text" class="form-control" name="fecini">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Fecha alta</label>
				<input type="text" class="form-control" name="fecalta">
			</div>
		</div>
	</div>
	<div name="autorizado"><?php $f->response->view('mg/enti.mini'); ?></div>
</form>