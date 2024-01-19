<?php global $f; ?>
<!-- Utilizar el Jquery Steps -->
<link rel="stylesheet" type="text/css" href="https://rawgit.com/rstaib/jquery-steps/master/demo/css/jquery.steps.css">
<style type="text/css">
.wizard .content {
    min-height: 600px;
}
.wizard .content > .body {
    width: 100%;
    height: auto;
    padding: 15px;
    position: relative;
}
</style>
<form id="form" action="#">
    <h1>Parametro inicial del contrato</h1>
    <fieldset>
		<div class="row">
		<legend>Tipo de arrendamiento</legend>
			<label for="motivo">Motivo del contrato *</label>
			<!-- <input id="userName" name="userName" type="text" class="required"> -->
			<select id="motivo" name="motivo" class="required">
			</select> 
		</div>
		<div class="row">
		<legend>Inmueble</legend>
			<div>
				<div class="input-group">
					<label for="tipo">Tipo de Local</label>
					<select id="tipo" name="tipo" class="required"></select>
				</div>
			</div>
			<div >
				<div class="input-group">
					<label for="sublocal">SubLocal</span>
					<select id="sublocal" name="sublocal" class="required"></select>
				</div>
			</div>
			<div>
				<div class="input-group">
					<label for="inmueble">Inmueble</span>
					<select id="inmueble" name="inmueble" class="required"></select>
				</div>
			</div>
		</div>
		
		<p>(*) Obligatorio</p>
    </fieldset>
 
    <h1>Entidades del contrato</h1>
    <fieldset>
        <legend>Profile Information</legend>
		<div class="row" id="entidad">
			<div class="col-lg-4" id="arrendatario" name="arrendatario">
				<?php $f->response->view('mg/enti.mini'); ?>
			</div>
			<div class="col-lg-4" id="aval" name="aval">
				<?php $f->response->view('mg/enti.mini'); ?>
			</div>
			<div class="col-lg-4" id="representante" name="representante">
				<?php $f->response->view('mg/enti.mini'); ?>
			</div>
		</div>

		<div class="row" id="direccion">
			<div class="col-lg-6" name="gridRep"></div>
			<div class="col-lg-6" name="gridAva"></div>
		</div>

		<label for="name">First name *</label>
        <input id="name" name="name" type="text">
        <label for="surname">Last name *</label>
        <input id="surname" name="surname" type="text">
        <label for="email">Email *</label>
        <input id="email" name="email" type="text">
        <label for="address">Address</label>
        <input id="address" name="address" type="text">
        <label for="age">Age (The warning step will show up if age is less than 18) *</label>
        <input id="age" name="age" type="text">
        <p>(*) Mandatory</p>
		<div class="row" id="entidad">
			<div class="col-lg-4" id="arrendatario" name="arrendatario">
				<?php $f->response->view('mg/enti.mini'); ?>
			</div>
			<div class="col-lg-4" id="aval" name="aval">
				<?php $f->response->view('mg/enti.mini'); ?>
			</div>
			<div class="col-lg-4" id="representante" name="representante">
				<?php $f->response->view('mg/enti.mini'); ?>
			</div>
		</div>
    </fieldset>
 
    <h1>Warning</h1>
    <fieldset>
        <legend>You are to young</legend>
 
        <p>Please go away ;-)</p>
    </fieldset>
 
    <h1>Finish</h1>
    <fieldset>
        <legend>Terms and Conditions</legend>
 
        <input id="acceptTerms" name="acceptTerms" type="checkbox" class="required"> <label for="acceptTerms">I agree with the Terms and Conditions.</label>
    </fieldset>
</form>
	<!--
	<div class="row" id="inmueble">
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
	<div class="row" id=arrendamiento >
		<div class="row" id="tipo_arrendamiento">
			<button class="tablinks" name="acta_conciliacion">Acta de conciliaci&oacute;n</button>
			<button class="tablinks" name="autorizacion">Autorizaci&oacute;n</button>
			<button class="tablinks" name="nuevo_contrato">Nuevo contrato</button>
			<button class="tablinks" name="renovacion">Renovaci&oacute;n</button>
			<button class="tablinks" name="sin_contrato">Sin contrato</button>
			<button class="tablinks" name="convenio">Convenio</button>
			<button class="tablinks" name="comodato">Comodato</button>
		</div>
		<div class="row" id="extra_arrendamiento">
			<button class="col-lg-4" name="comodato">Comodato</button>
		</div>
	</div>

	<div class="row" id="entidad">
		<div class="col-lg-4" name="arrendatario">
			<?php $f->response->view('mg/enti.mini'); ?>
		</div>
		<div class="col-lg-4" name="aval">
			<?php $f->response->view('mg/enti.mini'); ?>
		</div>
		<div class="col-lg-4" name="representante">
			<?php $f->response->view('mg/enti.mini'); ?>
		</div>
	</div>

	<div class="row" id="direccion">
		<div class="row" id="inquilino">
			<button class="col-lg-4" name="comodato">Comodato</button>
		</div>

		<div class="col-lg-6" name="gridRep"></div>
		<div class="col-lg-6" name="gridGar"></div>
		<div class="col-lg-6" name="gridInq"></div>
		<div class="col-lg-6" name="gridDir"></div>
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
					<input type="checkbox" class="i-checks" name="contrato_dias" value="1">
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
		<div class="col-lg-4">
			<div class="input-group">
				<span class="input-group-addon">N&deg; Cuenta SEDAPAR</span>
				<input type="text" name="sedapar" class="form-control">
			</div>
		</div>
		<div class="col-lg-4">
			<div class="input-group">
				<span class="input-group-addon">N&deg; Cuenta SEAL</span>
				<input type="text" name="seal" class="form-control">
			</div>
		</div>
		<div class="col-lg-4">
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
	-->
