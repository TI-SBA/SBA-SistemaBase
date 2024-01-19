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
		<label class="col-sm-4 control-label">DENOMINACION DE LA CONTRATACION</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="denominacion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">ANTECEDENTES Y JUSTIFICACION DE LA CONTRATACION</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="antecedentes"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">TERMINOS DE REFERENCIA PARA LA CONTRATACION DEL SERVICIO</label>
		<div class="col-sm-8">
			<div class="row">
				<div class="col-md-6">
					<textarea name="terminos" class="form-control"></textarea>
				</div>
				<div class="col-md-6">
					<label class="control-label">Visitas y Muestras</label>
					<textarea name="visitas" class="form-control"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<label class="control-label">Actividades (Que?)</label>
					<textarea name="actividades" class="form-control"></textarea>
				</div>
				<div class="col-md-6">
					<label class="control-label">Procedimiento (Como?)</label>
					<textarea name="procedimiento" class="form-control"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<label class="control-label">Recursos a ser provistos por el proveedor</label>
					<textarea name="recursos_proveedor" class="form-control"></textarea>
				</div>
				<div class="col-md-6">
					<label class="control-label">Recursos y facilidades a ser provistos por le entidad</label>
					<textarea name="recursos_entidad" class="form-control"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">REQUISITOS</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="requisitos"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">ENTREGABLES</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="entregables"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">LUGAR DE EJECUCION</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="lugar_ejecucion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">PLAZO DE EJECUCION</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="plazo_ejecucion">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">FORMA DE PAGO</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="forma_pago"></textarea>
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