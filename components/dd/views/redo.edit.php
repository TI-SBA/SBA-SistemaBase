<form class="form-horizontal" role="form">
<div class="form-group">
		<label class="col-sm-3 control-label">Cantidad:</label>
			<div class="col-sm-9">
				<input type="number" class="form-control"  name="cant" style="width:100px" required>
			</div>
</div>
<div class="form-group">
		<label class="col-sm-3 control-label">Nro:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="nro" style="width:100px" required readonly="readonly">
			</div>
</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Titulo:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="titu" style="width:250px" required>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Remitente:</label>
		<div class="col-sm-9">
			<input type="text" class="form-control"  name="remi" style="width:250px" required>
		</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Direccion u Oficina</label>
				<div class="col-md-3">
					<span class="form-control" name="dire"  style="width:250px"></span>
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
						<label class="col-sm-3 control-label">Tipo de Serie Documental</label>
							<div class="col-md-3">
								<span class="form-control" name="tise"  style="width:250px"></span>
							</div>
							<div class="col-sm-5">
								<span class="input-group-btn">
									<button name="btnTise" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
								</span>
							</div>
					</ddiv>
				</div>

	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Tipo</label>
				<div class="col-md-3">
					<span class="form-control" name="tipo_"  style="width:250px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnTipo" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	
	<div class="form-group">
		<label class="col-sm-3 control-label">Ubicacion:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control"  name="ubic" style="width:250px" required>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Observaciones:</label>
			<div class="col-sm-9">
				<TEXTAREA type="text" class="form-control"  name="obse" style="width:350px" ></TEXTAREA>
			</div>
	</div>

	<div class="col-lg-12">
		<div name="gridYear"></div>
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
				<span type="text" class="form-control"  name="id_tise" style="visibility:hidden;" style="width:250px" required></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<span type="text" class="form-control"  name="id_tipo_" style="visibility:hidden;" style="width:250px" required></span>
			</div>
	</div>


</form>
