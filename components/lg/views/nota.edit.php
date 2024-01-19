<?php global $f; ?>
<div class="row">
	<div class="col-sm-6">
		<form class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-sm-4 control-label">Fecha</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
					<input type="text" class="form-control" name="fec" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Motivo</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-file-text-o fa-fw"></i></span>
					<select class="form-control" name="motivo">
						<option value="Bonificación">Bonificación</option>
						<option value="Imprenta">Imprenta</option>
						<option value="Embargo">Embargo</option>
						<option value="Donación">Donación</option>
						<option value="InventarioInicial">Inventario Inicial</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Con Destino a</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-home fa-fw"></i></span>
					<select class="form-control" name="almacen"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Programa</label>
				<div class="input-group col-sm-8">
					<span class="input-group-addon"><i class="fa fa-sitemap fa-fw"></i></span>
					<select class="form-control" name="programa" disabled="disabled"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Seg&uacute;n</label>
				<div class="col-sm-8 input-group">
					<span class="input-group-addon"><i class="fa fa-file-text-o fa-fw"></i></span>
					<textarea cols="30" rows="3" class="form-control" name="segun"></textarea>
				</div>
			</div>
		</form>
	</div>
	<div class="col-sm-6">
		<?=$f->response->view('mg/enti.mini',array('data'=>array(
			'cabecera'=>'Procedencia (quien lo entrega)',
			'btn_select'=>'Elegir entidad de Procedencia'
		)))?>
	</div>
</div>
<fieldset>
	<legend>Productos</legend>
	<div name="gridProd"></div>
</fieldset>