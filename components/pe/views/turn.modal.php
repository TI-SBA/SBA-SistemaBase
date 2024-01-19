<div class="col-sm-6">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">Tipo</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-clock-o fa-fw"></i></span>
				<select class="form-control" name="tipo">
					<option value="M">Ma&ntilde;ana</option>
					<option value="T">Tarde</option>
					<option value="N">Noche</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Inicio</label>
			<div class='input-group date'>
                <input type='text' class="form-control" name="ini" />
                <span class="input-group-addon">
                    <span class="fa fa-clock-o"></span>
                </span>
            </div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Fin</label>
			<div class='input-group date'>
                <input type='text' class="form-control" name="fin" />
                <span class="input-group-addon">
                    <span class="fa fa-clock-o"></span>
                </span>
            </div>
		</div>
	</form>
</div>
<div class="col-sm-6" style="display:none;">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">Equipo</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="equipo"></span>
				<span class="input-group-btn">
					<button name="btnEquipo" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Local</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="local"></span>
				<span class="input-group-btn">
					<button name="btnLocal" type="button" class="btn btn-info"><i class="fa fa-home"></i></button>
				</span>
			</div>
		</div>
	</form>
</div>
<div class="col-sm-6">
	<p>Recuerda lo importante que es ingresar el horario correcto del trabajador, ya que esto influye directamente en su remuneraci&oacute;n!</p>
	<div class="row">
		<img src="images/worker-clock.jpg" class="img-responsive" />
	</div>
</div>