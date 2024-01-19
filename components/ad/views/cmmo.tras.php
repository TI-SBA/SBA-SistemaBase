<form class="form-horizontal" role="form">
	<div class="form-group">
		<div class="col-sm-10">
			<div class="input-group">
				<span class="form-control" name="paciente" ></span>
				<span class="input-group-btn">
					<button class="btn btn-primary" name="btnDiag" type="button">Buscar Paciente en Cama</button>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Numero de Cama: </label>
			<div class="col-sm-8">
				<input class="form-control" name="cama" disabled="=disabled"  style="width:250px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Ubicacion: </label>
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

	<div class="form-group" >
		
		<div class="col-sm-8">
			<input class="form-control" name="_id" style="visibility:hidden;"   style="width:250px">
		</div>
	</div>
	<div class="form-group" >
		<div class="col-sm-8">
			<span class="form-control" name="id_old" style="visibility:hidden;"  style="width:250px"></span>
		</div>
	</div>

</form>