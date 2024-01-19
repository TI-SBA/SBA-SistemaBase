<?php global $f; ?>
<div class="row">
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Tipo de Local</span>
			<span class="form-control" name="tipo"></span>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">SubLocal</span>
			<span class="form-control" name="sublocal"></span>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Inmueble</span>
			<span class="form-control" name="inmueble"></span>
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-6" name="arrendatario">
		<?php $f->response->view('mg/enti.mini'); ?>
	</div>
	<div class="col-lg-6" name="aval">
		<?php $f->response->view('mg/enti.mini'); ?>
	</div>
</div>
<div class="row">
	<div class="col-lg-6" name="gridRep"></div>
	<div class="col-lg-6" name="gridGar"></div>
</div>
<div class="row">
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Situaci&oacute;n</span>
			<select name="situacion" class="form-control"></select>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Base Imponible</span>
			<select class="form-control" name="moneda">
				<option value="S">Soles (S/.)</option>
				<option value="D">D&oacute;lares ($)</option>
			</select>
			<input type="text" name="importe" class="form-control" size="8" />
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">
				<input type="checkbox" class="i-checks" name="rbtnDias" value="1">
			</span>
			<span class="form-control">Contrato por d&iacute;as</span>
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-6" name="gridLet"></div>
	<div class="col-lg-6">
		<div class="input-group">
			<span class="input-group-addon">Fecha de Desocupaci&oacute;n</span>
			<input type="text" name="fecdes" class="form-control">
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">
				<input type="checkbox" class="i-checks" name="rbtnDes" value="1">
			</span>
			<span class="form-control">Desalojo</span>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">
				<input type="checkbox" class="i-checks" name="rbtnOds" value="2">
			</span>
			<span class="form-control">ODSD</span>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">
				<input type="checkbox" class="i-checks" name="rbtnInf" value="3">
			</span>
			<span class="form-control">INFOCORP</span>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">
				<input type="checkbox" class="i-checks" name="rbtnAse" value="4">
			</span>
			<span class="form-control">As. Externa</span>
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">Nro. de Contrato</span>
			<input type="text" name="nro_contrato" class="form-control">
		</div>
	</div>
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">N&deg; Cuenta SEDAPAR</span>
			<input type="text" name="sedapar" class="form-control">
		</div>
	</div>
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">N&deg; Cuenta SEAL</span>
			<input type="text" name="seal" class="form-control">
		</div>
	</div>
	<div class="col-lg-3">
		<div class="input-group">
			<span class="input-group-addon">Cod. Arbitrios</span>
			<input type="text" name="arbitrios" class="form-control">
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Motivo Contrato</span>
			<select name="motivo" class="form-control"></select>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Vigente desde</span>
			<input type="text" name="fecini" class="form-control">
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Vigente hasta</span>
			<input type="text" name="fecfin" class="form-control">
		</div>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Se paga</span>
			<select name="con_mora" class="form-control">
				<option value="1">Con Moras</option>
				<option value="0">Sin Moras</option>
			</select>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Compensaci&oacute;n</span>
			<select name="compensacion" class="form-control">
				<option value="0">No</option>
				<option value="1">Si</option>
			</select>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon">Porcentaje</span>
			<input type="text" name="porcentaje_comp" class="form-control" value="0">
		</div>
	</div>
</div>
<hr />
<div name="gridPag"></div>
<hr />
