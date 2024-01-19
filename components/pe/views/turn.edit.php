<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<input type="text" class="form-control" name="nomb">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Programa</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="programa"></span>
			<span class="input-group-btn">
				<button name="btnProg" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de Turno</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-wrench fa-fw"></i></span>
			<select class="form-control" name="tipo">
				<option value="N">Normal</option>
				<option value="R">Rotativo</option>
			</select>
		</div>
	</div>
</form>
<div name="calendar"></div>