<?php global $f; ?>
<div class="row">
	<div class="col-sm-6">
		<?=$f->response->view('mg/enti.mini',array('data'=>array(
			'cabecera'=>'Solicito entregar a',
			'btn_select'=>'Elegir persona que recibira lo solicitado'
		)))?>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<label class="col-sm-4 control-label">Fecha</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
				<input type="text" class="form-control" name="fec" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Con destino a</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
				<input type="text" class="form-control" name="destino_a" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Recoger del almac&eacute;n</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-file-text-o fa-fw"></i></span>
				<select class="form-control" name="almacen"></select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Referencias</label>
			<div class="col-sm-8 input-group">
				<span class="input-group-addon"><i class="fa fa-file-text-o fa-fw"></i></span>
				<textarea cols="30" rows="3" class="form-control" name="ref"></textarea>
			</div>
		</div>
	</div>
</div>
<div name="gridProd"></div>