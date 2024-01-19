<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-0 control-label">Datos del Documumento Solicitado</label>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Numero de Documento:</label>
			<div class="col-sm-9">
				<span class="form-control" name="ndoc"  style="width:250px"></span>
			</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-3 control-label">Documento:</label>
			<div class="col-sm-9">
				<span class="form-control" name="docu"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Direccion:</label>
			<div class="col-sm-9">
				<span class="form-control" name="dire"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Oficina:</label>
			<div class="col-sm-9">
				<span class="form-control" name="ofic"  style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Oficina:</label>
			<div class="col-sm-9">
				<span class="form-control" name="tipo"  style="width:250px"></span>
			</div>
	</div>

	<div class="form-group">
		<label class="col-sm-0 control-label">Datos del Solicitante</label>
	</div>


	 <div class="form-group">
		<label class="col-sm-3 control-label">Numero Documento Solicitado:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="nsol" style="width:100px" required readonly="readonly">
			</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Direccion Solicitante</label>
				<div class="col-md-3">
					<span class="form-control" name="disol"  style="width:250px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnDire" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group">
					<ddiv>
						<label class="col-sm-3 control-label">Oficina Solicitante</label>
							<div class="col-md-3">
								<span class="form-control" name="ofsol"  style="width:250px"></span>
							</div>
							<div class="col-sm-5">
								<span class="input-group-btn">
									<button name="btnOfic" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
								</span>
							</div>
					</ddiv>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Asunto:</label>
		<div class="col-sm-9">
			<TEXTAREA type="text" class="form-control"  name="asun" style="width:350px" ></TEXTAREA>
		</div>
	</div>
</form>