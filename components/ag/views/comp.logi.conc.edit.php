<?php
global $f;
?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-3 control-label">Cuenta Contable</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="cuenta"></span>
			<span class="input-group-btn">
				<button name="btnCuenta" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
			<!-- <span class="input-group-btn">
				<button name="btnEliCuenta" type="button" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>
			</span> -->
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Concepto</label>
		<div class="col-sm-8">
			<span type="text" rows="2" class="form-control" name="concepto"></span>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Almacen</label>
		<div class="col-sm-2">
			<select class="form-control" name="almacen" disabled>

			</select>
		</div>
		<label class="col-sm-2 control-label">Modulo</label>
		<div class="col-sm-1">
			<input type="text" class="form-control" name="modulo" disabled>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Producto </label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="producto"></span>
			<span class="input-group-btn">
				<button name="btnProducto" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
			<!-- <span class="input-group-btn">
				<button name="btnEliProducto" type="button" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>
			</span> -->
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Valor Unitario</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" name="valUnit" disabled>
		</div>
		<label class="col-sm-2 control-label">Cantidad</label>
		<div class="col-sm-1">
			<input type="text" class="form-control" name="cant">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Monto</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" name="monto">
		</div>
		<label class="col-sm-1 control-label">Monto Original</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" name="monto_original" disabled>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">IGV</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" name="igv" disabled>
		</div>
		<label class="col-sm-1 control-label">IGV Original</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" name="igv_original" disabled>
		</div>
	</div>
		<div class="form-group">
		<label class="col-sm-3 control-label">Total</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" name="total">
		</div>
		<label class="col-sm-1 control-label">Total Original</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" name="total_original" disabled>
		</div>
	</div>



	<div class="form-group" name="gridList">
		
	</div>
	<div class="form-group" name="gridItemList">
		
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Nº de Item </label>
		<div class="input-group col-sm-1">
			<input type="text" class="form-control" name="nitem" disabled>
		</div>
	</div>

</form>

