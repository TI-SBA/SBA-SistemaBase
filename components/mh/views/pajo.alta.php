<?php global $f;?>
<form class="form-horizontal" role="form">
	<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>Tipo</label>
				<span class="form-control" type="text" name="tipo_hosp"></span>
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
				<input type="text" class="form-control" name="cant">
			</div>	
		</div>
	</div>
	<!-- SALTO DE LINEA -->
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label>Fecha Inicial</label>
				<input type="text" class="form-control" name="fec_inicio">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Fecha alta</label>
				<input type="text" class="form-control" name="fec_alta">
			</div>
		</div>
	</div>
	<!-- SALTO DE LINEA -->
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>Tipo de Alta</label>
				<select class="form-control" name="talta">
					<option value="MEDICA">Alta Medica</option>
					<option value="ADMINISTRATIVA">Alta Administrativa</option>
				</select>
			</div>
		</div>
	</div>
	<div name="autorizado"><?php $f->response->view('mg/enti.mini'); ?></div>
</form>