<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<span class="form-control" name="nomb"></span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Descripci&oacute;n</label>
		<div class="col-sm-8">
			<span class="form-control" name="descr"></span>
		</div>
	</div>
</form>
<fieldset>
	<legend>Metas del Proyecto</legend>
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-4 control-label">Meta</label>
			<div class="input-group col-sm-8">
				<input type="text" class="form-control" name="meta" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Descripci&oacute;n de la Meta</label>
			<div class="input-group col-sm-8">
				<textarea cols="30" rows="3" class="form-control" name="meta_descr"></textarea>
			</div>
		</div>
		<button type="button" class="btn btn-success btn-block" name="btnMet"><i class="fa fa-plus"></i> Agregar Meta</button>
	</form>
	<div class="col-lg-12">
		<div class="wrapper wrapper-content animated fadeInUp">
			<ul class="notes"></ul>
	    </div>
	</div>
</fieldset>