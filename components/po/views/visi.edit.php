<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha de Visita</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fecent">
		</div>
	</div>
	<div class="form-group" data-provide="datetimepicker">
		<label class="col-sm-4 control-label">Hora de Entrada</label>
		<div class='input-group date' name="ent">
				<input type='text' class="form-control" />
				<span class="input-group-addon">
					<span class="fa fa-clock-o"></span>
				</span>
        </div>
	</div>
	<div class="form-group" data-provide="datetimepicker">
		<label class="col-sm-4 control-label">Hora de Salida</label>
		<div class='input-group date' name="sal">
				<input type='text' class="form-control" />
				<span class="input-group-addon">
					<span class="fa fa-clock-o"></span>
				</span>
        </div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Visitante</label>
		<div class="col-sm-8">
			<div name="visitante"><?php $f->response->view('mg/enti.mini'); ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Representa a Empresa</label>
		<div class="col-sm-8">
			<input type="checkbox" class="form-control" name="empresa">
		</div>
	</div>
	<div class="form-group" name="noempresa_ent">
		<label class="col-sm-4 control-label">Entidad</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="entidad_text">
		</div>
	</div>
	<div class="form-group" name="empresa_ent">
		<label class="col-sm-4 control-label">Empresa</label>
		<div class="col-sm-8">
			<div name="entidad_emp"><?php $f->response->view('mg/enti.mini'); ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Motivo</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="motivo">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Empleado P&uacute;blico</label>
		<div class="col-sm-8">
			<div name="trabajador"><?php $f->response->view('mg/enti.mini'); ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Lugar de reuni&oacute;n</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
			<span class="form-control" name="oficina"></span>
			<span class="input-group-btn">
				<button name="btnOfi" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Otros</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="lugar">
		</div>
	</div>
</form>