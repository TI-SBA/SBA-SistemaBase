<div class="row">
	<div class="col-md-12">
		<form class="form-horizontal" method="get" action="ct/auxs/save">
			<div id="content_errors">
			</div>
			<fieldset>
				<legend>Datos Generales</legend>
				<div class="form-group">
					<label>Periodo</label>
					<span name="periodo"></span>
				</div>
				<div class="form-group">
					<label>Cuenta</label>
					<span name="cuenta"></span>
				</div>
				<div class="form-group">
					<label>Programa</label>
					<span name="programa">--</span>
				</div>
				<div class="form-group">
					<label>Inmueble</label>
					<span name="inmueble">--</span>
				</div>
				<div class="form-group">
					<label>Arrendatario</label>
					<span name="arrendatario">--</span>
				</div>
				<!--<table class="table">
					<tr>
						<td><label>Periodo</label></td>
						<td><span name="periodo"></span></td>
					</tr>
					<tr>
						<td><label>Cuenta</label></td>
						<td><span name="cuenta"></span></td>
					</tr>
					<tr>
						<td><label>Programa</label></td>
						<td><span name="programa">--</span></td>
					</tr>
					<tr>
						<td><label>Inmueble</label></td>
						<td><span name="inmueble">--</span></td>
					</tr>
					<tr>
						<td><label>Arrendatario</label></td>
						<td><span name="arrendatario">--</span></td>
					</tr>
				</table>-->
			</fieldset>
			<fieldset>
				<legend>Comprobante</legend>
				<div class="form-group">
					<label>Fecha</label>
					<input type="text" size="12" name="fec" class="form-control" required />
				</div>
				<div class="form-group">
					<label>Clase</label>
					<select name="clase" class="form-control" required>
						<option value="">Seleccionar clase</option>
						<option value="CP">Comprobante de Pago</option>
						<option value="NC">Nota de Contabilidad</option>
						<option value="RI">Recibo de Ingresos</option>
						<option value="PCF">Poliza Contable de Fondos</option>
						<option value="OS">Ordenes de Servicio</option>
						<option value="OC">Ordenes de Compra</option>
					</select>
				</div>
				<div class="form-group">
					<label>N&uacute;mero</label>
					<input type="text" size="8" name="num" class="form-control" required />
				</div>
				<div class="form-group">
					<label>Detalle</label>
					<textarea rows="2" cols="35" name="detalle" class="form-control" required></textarea>
				</div>
			</fieldset>
			<fieldset>
				<legend>Movimiento</legend>
				<div class="form-group">
					<label>Tipo</label>
					<select name="tipo" class="form-control">
						<option value="D">Debe</option>
						<option value="H">Haber</option>
					</select>
				</div>
				<div class="form-group">
					<label>Monto</label>
					<input type="text" size="8" name="monto" class="form-control" required />
				</div>
			</fieldset>
		</form>	
	</div>
</div>