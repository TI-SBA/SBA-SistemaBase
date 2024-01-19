<div class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-2 control-label">N&uacute;mero</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="num">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Tipo</label>
		<div class="col-sm-10">
			<select class="form-control" name="tipo"></select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Periodo</label>
		<div class="col-sm-10">
			<div class="row">
				<div class="col-md-6">
					<select class="form-control" name="mes">
						<option value="1">Enero</option>
						<option value="2">Febrero</option>
						<option value="3">Marzo</option>
						<option value="4">Abril</option>
						<option value="5">Mayo</option>
						<option value="6">Junio</option>
						<option value="7">Julio</option>
						<option value="8">Agosto</option>
						<option value="9">Setiembre</option>
						<option value="10">Octubre</option>
						<option value="11">Noviembre</option>
						<option value="12">Diciembre</option>
					</select>
				</div>
				<div class="col-md-6">
					<select class="form-control" name="ano">
						<option value="2016">2016</option>
						<option value="2017">2017</option>
						<option value="2018">2018</option>
						<option value="2019">2019</option>
						<option value="2020">2020</option>
						<option value="2021">2021</option>
						<option value="2022">2022</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div>
		<label>Concepto</label>
		<div class="form-group">
			<div name="grid_detalle"></div>
		</div>
	</div>
	<div>
		<label>Contenido Adicional</label>
		<div class="form-group">
			<div name="grid_observ">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th colspan="7"><button class="btn btn-primary" name="btnObservAddRow">Agregar Fila</button></th>
						</tr>
						<tr>
							<th style="width:80px"><input type="text" name="observ_header_1" class="form-control" /></th>
							<th style="width:120px"><input type="text" name="observ_header_2" class="form-control" /></th>
							<th style="width:250px"><input type="text" name="observ_header_3" class="form-control" /></th>
							<th style="width:80px"><input type="text" name="observ_header_4" class="form-control" /></th>
							<th style="width:80px"><input type="text" name="observ_header_5" class="form-control" /></th>
							<th style="width:80px"><input type="text" name="observ_header_6" class="form-control" /></th>
							<th style="width:50px">&nbsp;</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>