<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Programa</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="programa" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Oficina</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="oficina" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Trabajador</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="trabajador" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">JUSTIFICACION DE LA CONTRATACION</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="justificacion_contrato"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">DESCRIPCION BASICA O CARACTERISTICA DEL SERVICIO</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="descripcion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">GRADO DE CALIFICACION</label>
		<div class="col-sm-8">
			<div class="row">
				<div class="col-md-6">
					<label class="control-label">Profesional</label>
					<input type="text" name="grado_profesional" class="form-control">
				</div>
				<div class="col-md-6">
					<label class="control-label">Tecnico</label>
					<input type="text" name="grado_tecnico" class="form-control">
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">PROFESION Y/O ESPECIALIDAD</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="profesion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">CAPACITACION Y/O CONOCIMIENTOS</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="capacitacion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">EXPERIENCIA EN AÑOS</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="experiencia">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">DURACION DEL SERVICIO</label>
		<div class="col-sm-8">
			<div class="row">
				<div class="col-md-6">
					<label class="control-label">Inicio</label>
					<input type="text" name="fecini" class="form-control">
				</div>
				<div class="col-md-6">
					<label class="control-label">Termino</label>
					<input type="text" name="fecfin" class="form-control">
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">VALOR REFERENCIAL</label>
		<div class="col-sm-8">
			<div class="row">
				<div class="col-md-6">
					<label class="control-label">Monto total</label>
					<input type="text" name="monto_total" class="form-control">
				</div>
				<div class="col-md-6">
					<label class="control-label">Monto Mensual</label>
					<input type="text" name="monto_mensual" class="form-control">
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">EXPEDIENTE TRAMITE DOCUMENTARIO</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="text" class="form-control" name="expediente" disabled="disabled">
				<span class="input-group-btn">
					<button name="btnExp" type="button" class="btn btn-info"><i class="fa fa-home"></i></button>
				</span>	
			</div>
		</div>
	</div>
</form>