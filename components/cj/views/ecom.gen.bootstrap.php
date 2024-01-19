<fieldset>
	<legend>Datos Generales</legend>
	<div class="row">
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">Recibo de Ingresos</span>
				<input type="text" class="form-control" name="num" />
			</div>
		</div>
		<div class="col-sm-6" name="div_ini">
			<div class="input-group">
				<span class="input-group-addon">Iniciales</span>
				<input type="text" class="form-control" name="iniciales" />
			</div>
		</div>
		<div class="col-sm-6" name="div_tipo">
			<div class="input-group">
				<span class="input-group-addon">Tipo de Filtrado</span>
				<select class="form-control" name="tipo_inm">
					<option value="C">Filtrar por Caja</option>
					<option value="S">Filtrar por Series</option>
				</select>
			</div>
		</div>
		<div class="col-sm-6" name="div_filt">
			<div class="input-group">
				<span class="input-group-addon">Caja</span>
				<span class="form-control" name="caja"></span>
				<span class="input-group-btn">
					<button class="btn btn-info" type="button" name="btnCaja"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">Fecha de Inicio</span>
				<input type="text" class="form-control" name="fec" />
			</div>
		</div>
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">Fecha de Fin</span>
				<input type="text" class="form-control" name="fecfin" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">Organizaci&oacute;n</span>
				<span class="form-control" name="orga"></span>
				<span class="input-group-btn">
					<button class="btn btn-info" type="button" name="btnOrga"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">Responsable</span>
				<span class="form-control" name="respo"></span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">Planilla Asociada</span>
				<input type="text" class="form-control" name="planilla" />
			</div>
		</div>
	</div>
	<div class="row">
		<div>
			<div class="input-group">
				<span class="input-group-addon">Observaciones</span>
				<textarea class="form-control" name="observ"></textarea>
			</div>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Detalle</legend>
	<div name="gridComp"></div>
</fieldset>
<fieldset>
	<legend>Anulados</legend>
	<div name="gridAnu"></div>
</fieldset>
<fieldset>
	<legend>Codificaci&oacute;n de la Contabilidad Presupuestal  y Clasificaci&oacute;n Program&aacute;tica del gasto P&uacute;blico</legend>
	<div name="gridCod"></div>
</fieldset>
<fieldset>
	<legend>Pagos</legend>
	<div name="gridPag"></div>
</fieldset>
<fieldset>
	<legend>Contabilidad Patrimonial</legend>
	<div name="gridCont"></div>
</fieldset>
<hr />