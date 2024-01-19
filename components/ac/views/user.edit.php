<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Cuenta</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
			<span class="form-control" name="userid"></span>
		</div>
	</div>
	<div class="form-group">
		<div class="input-group col-sm-8 col-md-offset-4">
			<span class="input-group-addon">
				<input type="checkbox" name="iCheck" checked="checked">
			</span>
			<span class="form-control">¿Desea actualizar su contrase&ntilde;a?</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Contrase&ntilde;a</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
			<input type="password" class="form-control" name="pass1" autocomplete="off">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Repetir contrase&ntilde;a</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
			<input type="password" class="form-control" name="pass2" autocomplete="off">
			<span class="input-group-btn">
				<button name="btnPass" type="button" class="btn btn-danger" style="cursor:default;"><i class="fa fa-close"></i></button>
			</span>
		</div>
	</div>
	<?php $f->response->view('mg/enti.mini'); ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Organizaci&oacute;n</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-sitemap fa-fw"></i></span>
			<span class="form-control" name="organizacion"></span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Programa</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
			<span class="form-control" name="programa"></span>
			<span class="input-group-btn">
				<button name="btnPro" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Oficina</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
			<span class="form-control" name="oficina"></span>
			<span class="input-group-btn">
				<button name="btnOfi" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Funci&oacute;n</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
			<span class="form-control" name="funcion"></span>
		</div>
	</div>
</form>
<div name="gridGrup"></div>