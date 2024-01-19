<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">Datos Generales</a></li>
		<li role="presentation"><a href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">Documentos V&aacute;lidos</a></li>
		<li role="presentation"><a href="#tab-3" aria-controls="tab-3" role="tab" data-toggle="tab">S&oacute;lo Aplica para</a></li>
		<li role="presentation"><a href="#tab-4" aria-controls="tab-4" role="tab" data-toggle="tab">Historial de Cambios</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="tab-1">
			<div class="form-group">
				<label class="col-sm-4 control-label">Tipo</label>
				<div class="col-sm-8">
					<select class="form-control" name="tipo" disabled="disabled">
						<option value="P">Pago</option>
						<option value="D">Descuento</option>
						<option value="A">Aporte</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Impresion en boleta</label>
				<div class="col-sm-8">
					<select class="form-control" name="imprimir" disabled="disabled">
						<option value="1">Si</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Tipo de Contrato</label>
				<div class="col-sm-8">
					<select class="form-control" name="tipo_contrato" disabled="disabled"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Nombre</label>
				<div class="col-sm-8">
					<span class="form-control" name="nomb"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Descripci&oacute;n</label>
				<div class="col-sm-8">
					<span class="form-control" name="descr"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">C&oacute;digo</label>
				<div class="col-sm-8">
					<span class="form-control" name="cod"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Clasificador</label>
				<div class="col-sm-8">
					<span class="form-control" name="clasif"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Cuenta Contable</label>
				<div class="col-sm-8">
					<span class="form-control" name="cuenta"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">F&oacute;rmula</label>
				<div class="col-sm-8">
					<span class="form-control" name="form"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">C&oacute;digo SUNAT</label>
				<div class="col-sm-8">
					<span class="form-control" name="cod_sunat"></span>
				</div>
			</div>
			<div class="form-group" name="row_bene">
				<label class="col-sm-4 control-label">Beneficiario</label>
				<div class="col-sm-8">
					<select class="form-control" name="cbo_bene" disabled="disabled">
						<option value="E">Persona o Empresa</option>
						<option value="RJ">Retenci&oacute;n Judicial</option>
				    	<option value="AFP">AFP</option>
					</select>
				</div>
			</div>
			<div class="form-group" name="row_bene">
				<label class="col-sm-4 control-label">Persona o Empresa</label>
				<div class="col-sm-8">
					<span class="form-control" name="bene"></span>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab-2">
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkBole" id="chkBole" value="1" disabled="disabled">
				</span>
				<label for="chkBole" class="form-control">Boletas de Pago</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkComp" id="chkComp" value="1" disabled="disabled">
				</span>
				<label for="chkComp" class="form-control">Compensaci&oacute;n Vacacional</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkCts" id="chkCts" value="1" disabled="disabled">
				</span>
				<label for="chkCts" class="form-control">CTS</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkGra" id="chkGra" value="1" disabled="disabled">
				</span>
				<label for="chkGra" class="form-control">Gravidez</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkEnf" id="chkEnf" value="1" disabled="disabled">
				</span>
				<label for="chkEnf" class="form-control">Enfermedad o Accidente</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkSub" id="chkSub" value="1" disabled="disabled">
				</span>
				<label for="chkSub" class="form-control">Subsidio por Gastos de Sepelio</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkLut" id="chkLut" value="1" disabled="disabled">
				</span>
				<label for="chkLut" class="form-control">Subsidio por Luto</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkEda" id="chkEda" value="1" disabled="disabled">
				</span>
				<label for="chkEda" class="form-control">Por 25 y 30 a&ntilde;os de Servicio</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkRen" id="chkRen" value="1" disabled="disabled">
				</span>
				<label for="chkRen" class="form-control">Renta de Quinta Categor&iacute;a</label>
			</div>
			<div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" name="chkLiq" id="chkLiq" value="1" disabled="disabled">
				</span>
				<label for="chkLiq" class="form-control">Liquidaci&oacute;n por Servicios Sociales</label>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab-3">
			<div name="gridFilt"></div>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab-4">
			<div name="gridHist"></div>
		</div>
	</div>
</div>