<?php global $f; ?>
<form class="form-horizontal" role="form">
<div class="form-group">
		<label class="col-sm-4 control-label">Nro de Parte Diario: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="num" required style="width:300px">
		</div>
	</div>
	<!--<div class="form-group">
		<label class="col-sm-4 control-label">Doctor</label>
			<div class="col-sm-8">
				<select class="form-control" name="doct" type = "text" style="width:300px" required>
						<option value="Oscar Wilfredo Cabrera Huaco">Oscar Wilfredo Cabrera Huaco</option>
						<option value="Alvarado Aco Jose Eliseo">Alvarado Aco Jose Eliseo</option>
						<option value="Carla Malaga Pinto">Carla Malaga Pinto</option>
						<option value="Maribel Chuquipalla zamalloa">Maribel Chuquipalla zamalloa</option>
						<option value="JOSE LUIS RONDON DE LA JARA">JOSE LUIS RONDON DE LA JARA</option>
				</select>
			</div>
	</div>-->
	<div name="medico"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-4 control-label">Fecha de Registro: </label>
		<div class="col-sm-7.5 input-group">
			<input type="text" class="form-control"  name="fech" style="width:300px" required>
		</div>
	</div>

</form>