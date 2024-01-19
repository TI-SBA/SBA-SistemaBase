<form class="form-horizontal" role="form">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="col-sm-4 control-label">N&uacute;mero de Recibo</label>
				<div class="col-sm-7">
					<input type="text" class="form-control" name="num">
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="col-sm-4 control-label">Fecha de emisi&oacute;n</label>
				<div class="col-sm-7">
					<input type="text" class="form-control" name="fecemi">
				</div>
			</div>	
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Historia</label>
		<div class="input-group col-sm-7">
			<input type="text" class="form-control" name="cod">
			<span class="input-group-btn">
				<button name="btnCod" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Cliente</label>
		<div class="input-group col-sm-7">
			<span class="form-control" name="cliente"></span>
			<span class="input-group-btn">
				<button name="btnCli" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Modulo</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-wrench fa-fw"></i></span>
			<select class="form-control" name="modulo">
				<option value="MH">Salud Mental</option>
				<option value="AD">Adicciones</option>
				<!--<option value="LM">Laboratorio Mu&ntilde;oz</option>-->
				<!--<option value="TD">Turno Tarde</option>-->
			</select>
		</div>
	</div>
</form>
<div name="grid"></div>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Observaciones</label>
		<div class="col-sm-7">
			<textarea cols="30" rows="2" class="form-control" name="observ"></textarea>
		</div>
	</div>
</form>