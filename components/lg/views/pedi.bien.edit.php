<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Programa</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="programa" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Oficina</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="oficina" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Trabajador</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="trabajador" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">DENOMINACION DE LA CONTRATACION</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="denominacion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">ANTECEDENTES</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="antecedentes"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">ESPECIFICACIONES TECNICAS DE LOS BIENES A CONTRATAR</label>
		<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-12">
					<div class="row">
						<div class="col-md-12">
							<textarea name="especificaciones" class="form-control"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<label class="control-label">Caracteristicas Tenicas</label>
							<textarea name="caracteristicas" class="form-control"></textarea>
						</div>
						<div class="col-md-6">
							<label class="control-label">Normas Tecnicas</label>
							<textarea name="normas" class="form-control"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label class="control-label">Acondicionamiento, montaje o instalacion</label>
							<textarea name="acondicionamiento" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">LUGAR DE ENTREGA</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="lugar_entrega"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">PLAZO DE ENTREGA</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="plazo_entrega">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">FORMA DE PAGO</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="forma_pago"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">GARANTIA</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="garantia"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">VISITAS Y MUESTRAS</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="visitas"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">CAPACITACION</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="capacitacion"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">EXPEDIENTE TRAMITE DOCUMENTARIO</label>
		<div class="col-sm-8">
			<div class="input-group">
				<input type="text" class="form-control" name="expediente" disabled="disabled">
				<span class="input-group-btn">
					<button name="btnExp" type="button" class="btn btn-info"><i class="fa fa-home"></i></button>
				</span>	
			</div>
		</div>
	</div>
</form>