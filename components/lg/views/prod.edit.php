<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">C&oacute;digo</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="cod">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Clasificador</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="clasif"></span>
			<span class="input-group-btn">
				<button name="btnClasi" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
			<span class="input-group-btn">
				<button name="btnEliClasi" type="button" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Cuenta Contable</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="cuenta"></span>
			<span class="input-group-btn">
				<button name="btnCuenta" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
			<span class="input-group-btn">
				<button name="btnEliCuenta" type="button" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="nomb">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Descripci&oacute;n</label>
		<div class="col-sm-8">
			<textarea rows="2" class="form-control" name="descr"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Unidad de medida</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="unid"></span>
			<span class="input-group-btn">
				<button name="btnUnid" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Precio Referencial</label>
		<div class="col-sm-8">
			<!--<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" lang="en-150" class="form-control" name="precioref" title="The number input must start with a number and use either comma or a dot as a decimal character." formnovalidate 
			/> -->
			<!-- -->
			<input type="text" class="form-control" name="precioref">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo</label>
		<div class="col-sm-8">
			<select class="form-control" name="tipo">
				<option value="P">Producto Simple</option>
				<option value="A">Activo</option>
				<option value="N">Bien No Despreciable</option>
				<option value="U">Bien Auxiliar</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Modulo</label>
		<div class="col-sm-8">
			<select class="form-control" name="modulo">
				<option value="--">--</option>
				<option value="LG">LOGISTICA</option>
				<option value="FA">FARMACIA</option>
				<option value="AG">VENTA DE AGUA</option>
				<option value="US">UNIDAD DE SERVICIOS ALIMENTARIOS</option>
				<option value="BJ">BALNEARIO DE JESUS</option>
			</select>
		</div>
	</div>




	<div class="form-group">
		<label class="col-sm-4 control-label">Otros Campos</label>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Precio de Venta (Cajas)</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="precioVenta">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre Genérico (Farmacias)</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="generico">
		</div>
	</div>
</form>