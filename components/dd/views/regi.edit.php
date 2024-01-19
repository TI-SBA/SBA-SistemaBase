<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-3 control-label">Titulo:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="titu" style="width:250px" required>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Numero de Documento:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="ndoc" style="width:250px" required>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Descripcion:</label>
		<div class="col-sm-9">
			<TEXTAREA type="text" class="form-control"  name="desc" style="width:350px" ></TEXTAREA>
		</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Direccion</label>
				<div class="col-md-4">
					<span class="form-control" name="dire"  style="width:400px"></span>
				</div>
				<div class="col-ms-5">
					<span class="input-group-btn">
						<button name="btnDire" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group">
					<ddiv>
						<label class="col-sm-3 control-label">Oficina</label>
							<div class="col-md-3">
								<span class="form-control" name="ofic"  style="width:250px"></span>
							</div>
							<div class="col-sm-5">
								<span class="input-group-btn">
									<button name="btnOfic" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
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
						<button name="btnTido" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group" >
		<label class="col-sm-3 control-label">Fecha de Emision: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="femi"   style="width:300px"  >
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