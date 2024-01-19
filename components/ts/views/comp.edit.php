<?php global $f; ?>
<div class="col-sm-8" style="width:90%;">
	<h2>Comprobante de Pago N&deg; <!-- <span name="cod"> </span>--> <input type="text" class="form-control" name="cod"></h2>
	<div class="form-group">
		<label>Nombre</label>
		<input type="text" name="nomb" class="form-control">
	</div>
	<div class="form-group">
		<label>Descripci&oacute;n</label>
		<textarea name="desc" class="form-control"></textarea>
	</div>
	<!--<div class="form-group">
		<label>Tipo de Referencia</label>
		<select name="tipo_referencia" class="form-control">
			<option value="TEXTO_PLANO">TEXTO PLANO</option>
			<option value="COMPRA">COMPRA</option>
			 <option value="SERVICIO_TELEFONICO">SERVICIO TELEFONICO</option> 
		</select>
	</div>-->
	<div class="form-group">
		<label>Tipo de Referencia</label>
		<select name="tipo_referencia" class="form-control">
			<option value="TEXTO_PLANO">TEXTO PLANO</option>
			<option value="COMPRA">COMPRA</option>
			<option value="SERVICIO">SERVICIO</option>
			<option value="LOCACION">LOCACION</option>
		</select>
	</div>
	<div class="form-group">
		<label>Contenido de Referencia</label>
		<div id="referencia_format"></div>
	</div>
	<!-- <div class="form-group">
		<label>Contenido de Concepto</label>
		<div id="moldeable_format"></div>
	</div> -->
	<fieldset>
	<legend>Detalle del Gasto</legend>
		<div name="grid_det">
		</div>
		<div name="grid_det2">
		</div>
	</fieldset>
	<fieldset>
	<legend>Estadistica Objeto del Gasto</legend>
		<div name="grid_est" ></div>
	</fieldset>
	<fieldset>
	<legend>Codificaci&oacute;n Program&aacute;tica</legend>
		<div name="grid_cod" ></div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Sector</label>
					<input type="text" name="cod_prog_sector" class="form-control" disabled>
				</div>
				<div class="form-group">
					<label>Pliego</label>
					<input type="text" name="cod_prog_pliego" class="form-control" disabled>
				</div>
				<div class="form-group">
					<label>Programa</label>
					<input type="text" name="cod_prog_programa" class="form-control" disabled>
				</div>
				<div class="form-group">
					<label>Sub-programa</label>
					<input type="text" name="cod_prog_subprograma" class="form-control" disabled>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Proyecto</label>
					<input type="text" name="cod_prog_proyecto" class="form-control" disabled>
				</div>
				<div class="form-group">
					<label>Obra</label>
					<input type="text" name="cod_prog_obra" class="form-control" disabled>
				</div>
				<div class="form-group">
					<label>Fuente de financiamiento</label>
					<select class="form-control" name="cod_prog_fuente">
						<!--<option value="51a6316b4d4a132807000002">RECURSOS DIRECTAMENTE RECAUDADOS</option>
						<option value="51a631924d4a132807000003">DONACIONES Y TRANSFERENCIAS</option>-->
					</select>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
	<legend>Contabilidad Presupuestal</legend>
		<div name="grid_cont_pres" ></div>
	</fieldset>
	<fieldset>
	<legend>Contabilidad Patrimonial</legend>
		<div name="grid_cont_patr" ></div>
	</fieldset>
	<fieldset>
	<legend>Retenciones y/o Deducciones</legend>
		<div name="grid_rete_dedu" ></div>
	</fieldset>
	<fieldset>
	<legend>Forma de Pago</legend>
		<div class="form-group">
			<label>Forma</label>
			<select name="forma_pago" class="form-control">
				<option value="C">Cheque</option>
				<option value="T">Transferencia</option>
			</select>
		</div>
		<div class="form-group">
			<label>Cuenta Bancaria</label>
			<select name="pag_cuenta" class="form-control">
			</select>
		</div>
		<div name="grid_form_pago" ></div>
	</fieldset>
</div>
<!--<iframe class="col-sm-4" src="ts/comp/preview" style="min-height:750px;height:100%"></iframe>-->