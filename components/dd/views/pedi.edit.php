<form class="form-horizontal" role="form">
	
	<div class="form-group">
		<label class="col-sm-3 control-label">Numero de Pedido:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="num" required disabled style="width:300px">
			</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-3 control-label">Documento:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="nomb" required disabled style="width:300px">

			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Direccion:</label>
			<div class="col-sm-9">
				
				<input type="text" class="form-control" name="dire" required disabled style="width:300px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Oficina:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="ofic" required disabled style="width:300px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Movimiento</label>
			<div class="col-sm-8">
				<select class="form-control" name="movi" type = "text" style="width:300px" required>
					<option value="0">En Proceso</option>
					<option value="1">Documento Entregado</option>
					<option value="2">Devuelto a Archivo Central</option>
					
				</select>
			</div>
	</div>
	

</form>