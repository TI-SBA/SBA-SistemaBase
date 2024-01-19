<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="nomb">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo</label>
		<div class="col-sm-8">
			<select class="form-control" name="tipo">
				<option value="VA">Vacaciones</option>
	            <option value="LI">Licencia</option>
	            <option value="PE">Permiso</option>
	            <option value="TO">Tolerancia</option>
	            <option value="TA">Tardanza</option>
	            <option value="IN">Inasistencia</option>
	            <option value="CO">Compensaci&oacute;n</option>
	            <option value="TE">Tiempo Extra</option>
	            <option value="JO">Jornada Normal</option>
	            <option value="SU">Suspensi&oacute;n</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">C&oacute;digo SUNAT</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="cod_sunat">
		</div>
	</div>
	<div class="input-group">
		<span class="input-group-addon">
			<input type="checkbox" name="goce" id="goceTipInc" value="1">
		</span>
		<label for="goceTipInc" class="form-control">Con goce de haber</label>
	</div>
	<div class="input-group">
		<span class="input-group-addon">
			<input type="checkbox" name="cuenta" id="cuentaTipInc" value="1">
		</span>
		<label for="cuentaTipInc" class="form-control">A cuenta de vacaciones</label>
	</div>
	<div class="input-group">
		<span class="input-group-addon">
			<input type="checkbox" name="subsi" id="subsi" value="1">
		</span>
		<label for="subsi" class="form-control">Subsidiado</label>
	</div>
	<div class="input-group">
		<span class="input-group-addon">
			<input type="checkbox" name="todo" id="todo" value="1">
		</span>
		<label for="todo" class="form-control">Todo el d&iacute;a</label>
	</div>
</form>