<?php global $f; ?>
<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detalle" aria-controls="detalle" role="tab" data-toggle="tab">General</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Revisiones</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="detalle">
			<?php global $f; ?>
			<form class="form-horizontal" role="form">
				<?=$f->response->view('mg/enti.mini')?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Motivo</label>
					<div class="col-sm-8">
						<select name="motivo" class="form-control">
							<option value="Bonificación">Bonificación</option>
							<option value="Imprenta">Imprenta</option>
							<option value="Embargo">Embargo</option>
							<option value="Donación">Donación</option>
							<option value="InventarioInicial">Inventario Inicial</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Seg&uacute;n</label>
					<div class="col-sm-8">
						<span class="form-control" name="segun"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Con destino a:</label>
					<div class="col-sm-8 input-group">
						<span class="form-control" name="almacen"></span>
					</div>
				</div>
			</form>
			<fieldset>
				<legend>Productos</legend>
				<div name="gridProd"></div>
			</fieldset>
			<hr />
		</div>
		<div role="tabpanel" class="tab-pane fade" id="profile">
			<div class="ibox-content inspinia-timeline"></div>
		</div>
	</div>
</div>