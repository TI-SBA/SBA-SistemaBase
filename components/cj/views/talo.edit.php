<div class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-2 control-label">Caja</label>
		<div class="col-sm-10">
			<select name="caja" class="form-control"></select>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Tipo</label>
		<div class="col-sm-10">
			<select name="tipo" class="form-control">
				<option value="F">FACTURA DE VENTA</option>
				<option value="R">RECIBO DE CAJA</option>
				<option value="RD">RECIBO DEFINITIVO</option>
				<option value="B">BOLETA DE VENTA</option>

				<option value="ECOM_FACT">FACTURA DE VENTA ELECTRONICA</option>
				<option value="ECOM_BOLE">BOLETA DE VENTA ELECTRONICA</option>
				<option value="ECOM_NOCR">NOTA DE CREDITO ELECTRONICA</option>
				<option value="ECOM_NODE">NOTA DE DEBITO ELECTRONICA</option>
			</select>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Serie</label>
		<div class="col-sm-10">
			<input type="text" name="serie" class="form-control">
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Prefijo</label>
		<div class="col-sm-10">
			<input type="text" name="prefijo" class="form-control">
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Sufijo</label>
		<div class="col-sm-10">
			<input type="text" name="sufijo" class="form-control">
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Limita / Fin</label>
		<div class="col-sm-10">
			<div class="row">
				<div class="col-md-6">
					<input type="text" name="ini" class="form-control">	
				</div>
				<div class="col-md-6">
					<input type="text" name="fin" class="form-control">	
				</div>
			</div>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Actual</label>
		<div class="col-sm-10">
			<input type="text" name="actual" class="form-control">
		</div>
	</div>
</div>