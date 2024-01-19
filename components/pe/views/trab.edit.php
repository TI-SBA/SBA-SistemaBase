<?php global $f;
$f->response->view('mg/enti.mini'); ?>
<form>
	<div class="form-group">
		<label class="col-sm-4 control-label">Programa</label>
		<div class="input-group col-sm-8">
			<select name="programa" class="form-control"></select>
		</div>
	</div>
	<!--<div class="form-group">
		<label class="col-sm-4 control-label">R&eacute;gimen</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<span class="form-control" name="regimen">276</span>
		</div>
	</div>-->
	<div class="form-group" name="campo1">
		<label class="col-sm-4 control-label">RUC</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<input type="text" class="form-control" name="ruc">
		</div>
	</div>
	<div class="form-group" name="campo2">
		<label class="col-sm-4 control-label">Cargo</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="cargo"></span>
			<span class="input-group-btn">
				<button name="btnSelCar" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group" name="campo4">
		<label class="col-sm-4 control-label">Nivel Designado</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="nivel"></span>
			<span class="input-group-btn">
				<button name="btnSelNiv" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group" name="campo5">
		<label class="col-sm-4 control-label">Nivel de Carrera</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="nivel2"></span>
			<span class="input-group-btn">
				<button name="btnSelNiv2" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group" name="campo6">
		<label class="col-sm-4 control-label">Salario</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<input type="text" class="form-control" name="salario">
		</div>
	</div>
	<div class="form-group" name="campo7">
		<label class="col-sm-4 control-label">Modalidad de Ingreso</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<select class="form-control" name="mod">
				<option value="CP">Convocatoria P&uacute;blica - CAS</option>
				<option value="SP">Sustituci&oacute;n de Contrato</option>
				<option value="DD">Designado Directamente</option>
				<option value="OT">Otros</option>
			</select>
		</div>
	</div>
	<fieldset name="campo8">
		<legend>Local de Trabajo</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">Descripci&oacute;n</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="descr"></span>
				<span class="input-group-btn">
					<button name="btnSelLoc" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Direcci&oacute;n</label>
			<span class="form-control" name="direccion"></span>
		</div>
	</fieldset>
	<fieldset name="campo9">
		<legend>Tarjeta</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">C&oacute;digo de Tarjeta</label>
			<input type="text" size="10" name="tarjeta" class="form-control">
		</div>
	</fieldset>
	<fieldset name="campo10">
		<legend>Turno de Trabajo</legend>
		<div class="form-group" name="campo10">
			<label class="col-sm-4 control-label">Turno</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="turno"></span>
				<span class="input-group-btn">
					<button name="btnSelTur" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
	</fieldset>
	<fieldset name="campo11">
		<legend>Cargo Clasificado</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">Nombre</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="clas"></span>
				<span class="input-group-btn">
					<button name="btnSelCla" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">C&oacute;digo</label>
			<span class="form-control" name="codclas"></span>
		</div>
	</fieldset>
	<fieldset name="campo12">
		<legend>Grupo Ocupacional</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">Nombre</label>
			<div class="input-group col-sm-8">
				<span class="form-control" name="grup"></span>
				<span class="input-group-btn">
					<button name="btnSelGru" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">C&oacute;digo</label>
			<span class="form-control" name="codgrup"></span>
		</div>
	</fieldset>
	<div class="form-group" name="campo13">
		<label class="col-sm-4 control-label">C&oacute;digo de ESSALUD</label>
		<input type="text" size="10" name="essalud" class="form-control">
	</div>
	<fieldset name="campo14">
		<legend>Sistema de Pensi&oacute;n</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">Sistema de Pensi&oacute;n</label>
			<select name="sist" class="form-control"></select>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">C&oacute;digo de Aportante</label>
			<input type="text" name="cod_apor" class="form-control">
		</div>
	</fieldset>
	<div class="form-group" name="campo15">
		<label class="col-sm-4 control-label">Tipo de Trabajador</label>
		<select name="rbtnTipTra" class="form-control">
			<option value="N">Nombrado</option>
			<option value="C">Contratado</option>
			<option value="SN">Salud Nombrado</option>
			<option value="SC">Salud Contratado</option>
		</select>
	</div>
	<div class="form-group" name="campo16">
		<label class="col-sm-4 control-label">Afiliaci&oacute;n EPS</label>
		<select name="rbtnAfiEps" class="form-control">
			<option value="1">Afiliado a EPS</option>
			<option value="0">No Afiliado</option>
		</select>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de Comisi&oacute;n</label>
		<select name="comision" class="form-control">
			<option value="">-</option>
			<option value="F">Flujo</option>
			<option value="M">Mixta</option>
		</select>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Oficina</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="oficina"></span>
			<span class="input-group-btn">
				<button name="btnSelOfi" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Actividad</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="actividad"></span>
			<span class="input-group-btn">
				<button name="btnSelActi" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	
</form>