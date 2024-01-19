<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Numero de Cama: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="cama" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Ubicacion: </label>
			<div class="col-sm-8">
				<select class="form-control" name="ubicacion" type = "text" style="width:200px" >
							<option value="PRINCIPAL">PRINCIPAL</option>
							<option value="ADICCIONES">ADICCIONES</option>
							
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Pabellon: </label>
			<div class="col-sm-8">
				<select class="form-control" name="pabellon" type = "text" style="width:200px" >
							<option value="INTENSIVO">INTENSIVO</option>
							<option value="INTERMEDIO">INTERMEDIO</option>
							
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Sala: </label>
			<div class="col-sm-8">
				<select class="form-control" name="sala" type = "text" style="width:200px" >
							<option value="VARONES">VARONES</option>
							<option value="DAMAS">DAMAS</option>
							
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Estado: </label>
			<div class="col-sm-8">
				<select class="form-control" name="estado" type = "text" disabled="disabled" style="width:200px" >
							<option value="0" selected>VACIA</option>
							<option value="1">OCUPADA</option>
							
				</select>
			</div>
	</div>
</form>