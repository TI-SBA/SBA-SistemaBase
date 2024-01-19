<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="row" rel="solicitud" style="display: none;">
		<div class="col-md-6">
			<label>Etapa</label>
			<select class="form-control" name="etapa" disabled="disabled">
				<option value="SOL">SOLICITUD DE CERTIFICACION</option>
				<option value="CER">CERTIFICACION PRESUPUESTARIA</option>
				<option value="ORD">ORDEN DE COMPRA</option>
				<option value="ORS">ORDEN DE SERVICIO</option>
				<option value="REC">RECEPCION ORDEN</option>
			</select>
		</div>
		<div class="col-md-6">
			<label>Talonario</label>
			<select class="form-control" name="talonario">
			</select>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	
	<div class="form-group">
		<label class="col-sm-4 control-label">Cotizaci&oacute;n</label>
		<div class="col-sm-8">
			<div class="input-group">
				<span class="form-control" name="cotizacion"></span>
				<span class="input-group-btn">
					<button name="btnCot" type="button" class="btn btn-info"><i class="fa fa-home"></i></button>
				</span>
			</div>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	
	<div class="row" rel="solicitud" style="display: none;">
		<div class="col-md-3">
			<label>Nro. Solicitud</label>
			<input type="text" class="form-control" name="solicitud_num" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de registro de solicitud</label>
			<input type="text" class="form-control" name="solicitud_fecreg" disabled="disabled">
			<input type="text" class="form-control" name="solicitud_autreg" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de envio de solicitud</label>
			<input type="text" class="form-control" name="solicitud_fecenv" disabled="disabled">
			<input type="text" class="form-control" name="solicitud_autenv" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de recepcion de solicitud</label>
			<input type="text" class="form-control" name="solicitud_fecrec" disabled="disabled">
			<input type="text" class="form-control" name="solicitud_autrec" disabled="disabled">
		</div>
	</div>
	<div class="hr-line-dashed" rel="solicitud" style="display: none;"></div>
	
	<div class="row" rel="certificacion" style="display: none;">
		<div class="col-md-3">
			<label>Nro. Certificacion</label>
			<input type="text" class="form-control" name="certificacion_num" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de registro de certificacion</label>
			<input type="text" class="form-control" name="certificacion_fecreg" disabled="disabled">
			<input type="text" class="form-control" name="certificacion_autreg" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de envio de certificacion</label>
			<input type="text" class="form-control" name="certificacion_fecenv" disabled="disabled">
			<input type="text" class="form-control" name="certificacion_autenv" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de recepcion de certificacion</label>
			<input type="text" class="form-control" name="certificacion_fecrec" disabled="disabled">
			<input type="text" class="form-control" name="certificacion_autrec" disabled="disabled">
		</div>
	</div>
	<div class="hr-line-dashed" rel="certificacion" style="display: none;"></div>
	
	<div class="row" rel="orden" style="display: none;">
		<div class="col-md-3">
			<label>Nro. Orden de compra</label>
			<input type="text" class="form-control" name="orden_num" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de registro de orden</label>
			<input type="text" class="form-control" name="orden_fecreg" disabled="disabled">
			<input type="text" class="form-control" name="orden_autreg" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de aprobacion de orden</label>
			<input type="text" class="form-control" name="orden_fecapr" disabled="disabled">
			<input type="text" class="form-control" name="orden_autapr" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de envio de orden</label>
			<input type="text" class="form-control" name="orden_fecenv" disabled="disabled">
			<input type="text" class="form-control" name="orden_autenv" disabled="disabled">
		</div>
	</div>
	<div class="hr-line-dashed" rel="orden" style="display: none;"></div>

	<div class="row" rel="orden_servicio" style="display: none;">
		<div class="col-md-3">
			<label>Nro. Orden de servicio</label>
			<input type="text" class="form-control" name="orden_servicio_num" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de registro de orden de servicio</label>
			<input type="text" class="form-control" name="orden_servicio_fecreg" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de aprobacion de orden de servicio</label>
			<input type="text" class="form-control" name="orden_servicio_fecapr" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de envio de orden de servicio</label>
			<input type="text" class="form-control" name="orden_servicio_fecenv" disabled="disabled">
		</div>
	</div>
	<div class="hr-line-dashed" rel="orden" style="display: none;"></div>
	
	<div class="row" rel="recepcion" style="display: none;">
		<div class="col-md-3">
			<label>Fecha de registro de recepcion</label>
			<input type="text" class="form-control" name="recepcion_fecreg" disabled="disabled">
		</div>
		<div class="col-md-3">
			<label>Fecha de aprobacion de recepcion</label>
			<input type="text" class="form-control" name="recepcion_fecapr" disabled="disabled">
		</div>
	</div>
	<div class="hr-line-dashed" rel="recepcion" style="display: none;"></div>
	
	<div class="row" rel="orden" style="display: none;">
		<div class="col-md-12">
			<?=$f->response->view('mg/enti.mini',array(
				'data'=>array(
					'cabecera'=>'Datos del proveedor',
					'btn_select'=>'Seleccionar proveedor'
				)
			))?>
		</div>
	</div>
	<div class="hr-line-dashed" rel="orden" style="display: none;"></div>
	<div class="form-group" rel="orden" style="display: none;">
		<label class="col-sm-4 control-label">Lugar de Almacenamiento</label>
		<div class="col-sm-8">
			<div class="input-group">
				<span class="form-control" name="almacen"></span>
				<span class="input-group-btn">
					<button name="btnAlm" type="button" class="btn btn-info"><i class="fa fa-home"></i></button>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group" rel="certificacion" style="display: none;">
		<label class="col-sm-4 control-label">Fuentes de Financiamiento</label>
		<div class="col-sm-8">
			<select class="form-control" name="fuente"></select>
		</div>
	</div>
	<div class="form-group" rel="solicitud" style="display: none;">
		<label class="col-sm-4 control-label">Fecha estimada de entrega</label>
		<div class="col-sm-8">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
				<input type="text" class="form-control" name="fecent">
			</div>
		</div>
	</div>
	<div class="form-group" rel="solicitud" style="display: none;">
		<label class="col-sm-4 control-label">Observaciones de solicitud</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="solicitud_observ" rel="observ"></textarea>
		</div>
	</div>
	<div class="form-group" rel="certificacion" style="display: none;">
		<label class="col-sm-4 control-label">Observaciones de certificacion</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="certificacion_observ" rel="observ"></textarea>
		</div>
	</div>
	<div class="form-group" rel="orden" style="display: none;">
		<label class="col-sm-4 control-label">Observaciones de orden de compra</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="orden_observ" rel="observ"></textarea>
		</div>
	</div>
	<div class="form-group" rel="orden_servicio" style="display: none;">
		<label class="col-sm-4 control-label">Observaciones de orden de servicio</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="orden_servicio_observ" rel="observ"></textarea>
		</div>
	</div>
	<div class="form-group" rel="recepcion" style="display: none;">
		<label class="col-sm-4 control-label">Observaciones de recepcion</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="3" class="form-control" name="recepcion_observ" rel="observ"></textarea>
		</div>
	</div>
</form>
<fieldset>
	<legend>Productos</legend>
	<div name="gridProd"></div>
</fieldset>
<fieldset>
	<legend>Afectaci&oacute;n Presupuestaria</legend>
	<div name="gridPres"></div>
</fieldset>
<fieldset>
	<legend>Especifica del Gasto</legend>
	<div name="gridEsp"></div>
</fieldset>
<hr />