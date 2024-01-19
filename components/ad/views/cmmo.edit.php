<form class="form-horizontal" role="form">
	<!--<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Paciente</label>
				<div class="col-md-3">
					<span class="form-control" name="paciente" ></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnDiag" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	-->
	<div class="form-group">
		<!--	<label class="col-sm-2 control-label">Paciente</label>-->
			<div class="col-sm-10">
				<div class="input-group">
					<span class="form-control" name="paciente" ></span>
					<span class="input-group-btn">
						<button class="btn btn-primary" name="btnDiag" type="button">Buscar Paciente</button>
					</span>
				</div>
			</div>
	</div>

	<div class="form-group" >
		
	<div class="col-sm-8">
			<input class="form-control" name="_id" disabled="disabled" type="hidden"  style="width:250px">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Numero de Cama: </label>
		<div class="col-sm-8">
			<input class="form-control" name="cama" disabled="=disabled"  style="width:250px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">ubicacion: </label>
			<div class="col-sm-8">
				<input class="form-control" name="ubicacion" disabled="=disabled"  style="width:250px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Pabellon: </label>
			<div class="col-sm-8">
				<input class="form-control" name="pabellon" disabled="=disabled"  style="width:250px">
			</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Sala: </label>
			<div class="col-sm-8">
				<input class="form-control" name="sala" disabled="=disabled"  style="width:250px">
			</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Estado: </label>
			<div class="col-sm-8">
				<input class="form-control" name="estado" disabled="=disabled"  style="width:250px">
			</div>
	</div>
</form>