<?php global $f; ?>
<div class="panel col-md-4">
    <div class="panel-heading">
        <div class="panel-title m-b-md"><h4>Comprobante</h4></div>
        <?php $f->response->view('mg/enti.mini'); ?>
    	<form class="form-horizontal" role="form">
    		<div class="form-group">
				<label class="col-sm-4 control-label">Fecha</label>
				<div class="input-group col-sm-8">
					<input type="text" class="form-control" name="fec">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Tipo de Comprobante</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="tipo">
						<option value="B">Boleta de Venta</option>
						<option value="F">Factura</option>
						<option value="R">Recibo de Caja</option>
					</select>
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
					<input type="number" class="form-control" name="num">
				</div>
			</div>
    		<div class="form-group">
				<label class="col-sm-4 control-label">Servicio</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="servicio"></span>
					<span class="input-group-btn">
						<button name="btnServ" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Moneda</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="moneda">
						<option value="S">Soles</option>
						<option value="D">D&oacute;lares</option>
					</select>
				</div>
			</div>
    		<div class="form-group">
				<label class="col-sm-4 control-label">Observaciones</label>
				<div class="input-group col-sm-8">
					<textarea rows="2" class="form-control" name="observ"></textarea>
				</div>
			</div>
    	</form>
    </div>
</div>
<div class="panel col-md-8">
    <div class="panel-heading">
        <div class="panel-title m-b-md"><h4>Detalle del Cobro</h4></div>
        <div name="gridCob"></div>
	</div>
	<hr />
    <div class="panel-heading">
        <div class="panel-title m-b-md"><h4>Formas de Pago</h4></div>
        <div name="gridPag"></div>
	</div>
</div>