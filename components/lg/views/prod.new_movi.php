<div class="row">
	<div class="form-group col-sm-6">
		<label class="col-sm-4 control-label">Fecha</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fec" />
		</div>
	</div>
	<div class="form-group col-sm-6">
		<label class="col-sm-4 control-label">Programa</label>
		<div class="col-sm-8">
			<span class="form-control" name="organizacion"></span>
			<span class="input-group-btn">
				<button class="btn btn-info" type="button" name="btnProg"><i class="fa fa-search"></i> Seleccionar</button>
			</span>
		</div>
	</div>
	<div class="form-group col-sm-6">
		<label class="col-sm-4 control-label">Tipo Doc.</label>
		<div class="col-sm-8">
			<select class="form-control" name="tipo">
				<option value="PEB" data-mov="E">PEB - P&oacute;liza de Entrada de Bienes</option>
				<option value="PSB" data-mov="S">PSB - P&oacute;liza de Salida de Bienes</option>
				<option value="PEB" data-mov="E">PAE - P&oacute;liza de Entrada de Almac&eacute;n</option>
				<option value="PSB" data-mov="S">PAS - P&oacute;liza de Salida de Almac&eacute;n</option>
				<option value="NCE" data-mov="E">NCE - Nota Contabilidad Entrada</option>
				<option value="NCS" data-mov="S">NCE - Nota Contabilidad Salida</option>
				<option value="IFE" data-mov="E">IFE - Informe Entrada (Traslado)</option>
				<option value="IFS" data-mov="S">IFS - Informe Salida (Traslado)</option>
			</select>
		</div>
	</div>
	<div class="form-group col-sm-6">
		<label class="col-sm-4 control-label">Nro</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="num" />
		</div>
	</div>
</div>
<hr />
<div name="grid"></div>
<hr />
<div class="row">
	<div class="form-group col-sm-6">
		<label class="col-sm-4 control-label">Precio Unitario</label>
		<div class="col-sm-8">
			<span class="form-control" name="precio"></span>
		</div>
	</div>
	<div class="form-group col-sm-6">
		<label class="col-sm-4 control-label">Costo Promedio</label>
		<div class="col-sm-8">
			<span class="form-control" name="costo" /></span>
		</div>
	</div>
</div>
<hr />