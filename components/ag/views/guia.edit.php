<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">N&uacute;mero de Documento</label>
		<!-- <label class="col-sm-4 control-label">N&uacute;mero de Gu&iacute;a de Remisi&oacute;n</label> -->
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-barcode fa-fw"></i></span>
			<input type="text" class="form-control" name="num">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de documento</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-barcode fa-fw"></i></span>
			<select name="tipo_doc" class="form-control">
						<option value="GR" docu="GR" selected="selected">GUIA DE REMISION</option>
						<option value="PEB" docu="PEB">POLIZA DE ENTRADA</option>
						<option value="CR" docu="CR">CORTE DE REQUERIMIENTO</option>
						<option value="PSB" docu="PSB">PECOSA DE SALIDA</option>
						<option value="F" docu="F">FACTURA MANUAL</option>
						<option value="B" docu="B">BOLETA MANUAL</option>
						<option value="DO" docu="DO">DONACION</option>
        	</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha de Ingreso</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
			<input type="text" class="form-control" name="fec">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Local</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
			<select class="form-control" name="local"></select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Almacen Origen</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
			<select class="form-control" name="almacen_origen"></select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Almacen Destino</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
			<select class="form-control" name="almacen_destino"></select>
		</div>
	</div>
</form>
<div name="grid"></grid>