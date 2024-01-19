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
		<label class="col-sm-3 control-label">Descripcion de la Oficina:</label>
		<div class="col-sm-9">
			<TEXTAREA type="text" class="form-control"  name="desc" style="width:350px" ></TEXTAREA>
		</div>
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
	
</form>