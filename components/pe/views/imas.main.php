<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Formato excel por programa</label>
		<div class="col-sm-2">
			<input type="text" name="periodo" class="form-control" placeholder="Periodo">
		</div>
		<div class="input-group col-sm-6">
			<select name="programa" class="form-control">
			</select>
			<span class="input-group-btn">
				<button name="btnPlan" type="button" class="btn btn-info"><i class="fa fa-file-excel-o"></i> Descargar</button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Equipo de Marcaci&oacute;n</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="equipo"></span>
			<span class="input-group-btn">
				<button name="btnEquipo" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
</form>
<hr />
<div class="col-sm-6">
	<div class="panel panel-info">
		<div class="panel-heading">
			<i class="fa fa-home"></i> Suba su rol de Turnos!
		</div>
		<div class="panel-body">
			<div name="div_file2">
				<div class="form-group">
					<label>Importar Archivo de Turnos</label>
					<input id="file_upload2" name="file_upload2" type="file">
				</div>
				<hr />
			</div>
		</div>
	</div>
</div>
<div class="col-sm-6">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<i class="fa fa-home"></i> Suba su archivo de reloj para ingresar las asistencias!
		</div>
		<div class="panel-body">
			<div name="div_file">
				<div class="form-group">
					<label>Importar Archivo de Asistencias</label>
					<input id="file_upload" name="file_upload" type="file" data-preview-file-type="text">
				</div>
				<hr />
			</div>
		</div>
	</div>
</div>