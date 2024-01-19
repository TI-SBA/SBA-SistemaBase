<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-3 control-label">Titulo:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="titu" style="width:250px" readonly="readonly">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Numero de Documento:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="ndoc" style="width:250px" readonly="readonly">
			</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Direccion</label>
				<div class="col-md-3">
					<span class="form-control" name="dire"  style="width:250px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<!--<button name="btnDire" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>-->
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Tipo de Documento</label>
				<div class="col-md-3">
					<span class="form-control" name="docu"  style="width:250px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<!--<button name="btnTido" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>-->
					</span>
				</div>
		</ddiv>
	</div>
	<!--
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-4 control-label">Fecha de Emision: </label>
		<div class="col-sm-7.5 input-group">
			<input type="text" class="form-control"  name="femi" style="width:300px" readonly="readonly" >
		</div>
	</div>
	-->
	<div class="form-group">
		<label class="col-sm-4 control-label">Archivo Relacionado</label>
		<div class="col-sm-8">
			<span class="form-control" name="file"></span>
			<span class="input-group-btn">
				<button name="btnFile" type="button" class="btn btn-info"><i class="fa fa-picture-o"></i> Seleccionar archivo</button>
			</span>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<span type="text" class="form-control"  name="id_dire" style="visibility:hidden;" style="width:250px" required></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<span type="text" class="form-control"  name="id_ofic" style="visibility:hidden;" style="width:250px" required></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<span type="text" class="form-control"  name="id_docu" style="visibility:hidden;" style="width:250px" required></span>
			</div>
	</div>

</form>