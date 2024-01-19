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
    <h1>Datos primarios</h1>
    <fieldset>
		<div class="row">
			<div class="col-sm-8">
				<label class="col-sm-2 control-label" for="name">Nº de contrato *</label>
				<input id="name" name="name" type="text" class="required">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 control-label">Ubigeo</label>
			<div class="col-sm-8">
				<div class="row">
					<div class="col-md-4">
						<label for="direccion_depa">Departamento *</label>
						<select id="direccion_depa" class="form-control" name="direccion_depa" type = "text" required>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<label class="col-sm-4 control-label"></label>
					<div class="col-sm-8">
						<label for="direccion_prov">Provincia *</label>
						<select id="direccion_prov" class="form-control" name="direccion_prov" type = "text" required>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<label class="col-sm-4 control-label"></label>
					<div class="col-sm-8">
						<label for="direccion_dist">Distrito *</label>
						<select id="direccion_dist" class="form-control" name="direccion_dist" type = "text" required>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 control-label">Area (M2)</label>
			<div class="col-sm-8">
				<div class="row">
					<label for="terreno">Terreno *</label>
					<input id="terreno" name="terreno" type="text" required>
				</div>
				<div class="row">
					<label for="construido">Constru&iacute;do *</label>
					<input id="construido" name="construido" type="text" required>
				</div>
			</div>
			<div class="col-sm-8">
				<label for="val_autovaluo">Valor de Autoval&uacute;o *</label>
				<input id="val_autovaluo" name="val_autovaluo" type="text" required>
			</div>
		</div>
		<p>(*) Obligatorio</p>
    </fieldset>
 
    <h1>Tiendas</h1>
    <fieldset>
        <legend>Lista de tiendas</legend>
        <div class="col-lg-12" id="gridTienda" name="gridTienda"></div>
    </fieldset>

    <h1>Oficinas</h1>
    <fieldset>
        <legend>Lista de Oficinas</legend>
        <div class="col-lg-12" id="gridOficina" name="gridOficina"></div>
    </fieldset>

    <h1>Casas Habitaci&oacute;n</h1>
    <fieldset>
        <legend>Lista de Casas Habitaci&oacute;n</legend>
        <div class="col-lg-12" id="gridCasaHabitacion" name="gridCasaHabitacion"></div>
    </fieldset>

    <h1>Stands</h1>
    <fieldset>
        <legend>Lista de Stands</legend>
        <div class="col-lg-12" id="gridStands" name="gridStands"></div>
    </fieldset>

    <h1>Espacios</h1>
    <fieldset>
        <legend>Lista de Espacios</legend>
        <div class="col-lg-12" id="gridEspacios" name="gridEspacios"></div>
    </fieldset>

    <h1>Advertencia</h1>
    <fieldset>
        <legend>Eres muy joven</legend>
        <p>Please go away ;-)</p>
    </fieldset>
 
    <h1>Finalizar</h1>
    <fieldset>
        <legend>Terminos y condiciones</legend>
        <input id="acceptTerms" name="acceptTerms" type="checkbox" class="required"> <label for="acceptTerms">Entiendo que revise la informaci&oacute;n imputada y que es correcta para que el inmueble pueda activarse.</label>
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
