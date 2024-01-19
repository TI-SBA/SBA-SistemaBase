<?php global $f; ?>
<div class="panel col-md-4">
    <div class="panel-heading">
    	<form class="form-horizontal" role="form">
			<!-- INFORMACION ADICIONAL DEL MODULO -->
			<?php $f->response->view('mg/enti.mini',array(
				'cabecera'=>'Cliente',
				'btn_select'=>'Elegir otro cliente'
			)); ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Fecha de Emisi&oacute;n</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input class="form-control" name="fecemi" type="text" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Caja</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
					<select class="form-control" name="caja"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Comprobante</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="comp"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Serie</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="serie"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">N&uacute;mero</label>
				<div class="input-group col-sm-8">
					<input class="form-control" name="num" type="number" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Moneda</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="moneda"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Almacen</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
					<select class="form-control" name="almacen"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Observaciones</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
					<textarea rows="3" class="form-control" name="observ"></textarea>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="col-md-8">
	<fieldset>
		<legend>Detalle de Pago</legend>
		<div name="gridServ"></div>
	</fieldset>
	<fieldset>
		<legend>Forma de Pago</legend>
		<div name="gridForm"></div>
	</fieldset>
</div>
<hr />