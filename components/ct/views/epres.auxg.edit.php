<fieldset>
<legend>Datos Generales</legend>
	<table>
		<tr>
			<td><b><label>Periodo</label></b></td>
			<td><select name="mes">
				<option value="1">Enero</option>
				<option value="2">Febrero</option>
				<option value="3">Marzo</option>
				<option value="4">Abril</option>
				<option value="5">Mayo</option>
				<option value="6">Junio</option>
				<option value="7">Julio</option>
				<option value="8">Agosto</option>
				<option value="9">Setiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option>
			</select> - <span name="periodo"></span></td>
		</tr>
		<tr>
			<td><b><label>Clasificador</label></b></td>
			<td><span name="clasif"></span></td>
		</tr>
		<tr>
			<td><b><label>Organizaci&oacute;n</label></b></td>
			<td><span name="orga"></span></td>
		</tr>
		<tr id="meta" style="display:none;">
			<td><b><label>Meta</label></b></td>
			<td><span name="meta"></span></td>
		</tr>
		<tr>
			<td><b><label>Fuente de Financiamiento</label></b></td>
			<td><span name="fuen"></span></td>
		</tr>
		<tr>
			<td><b><label>Fecha</label></b></td>
			<td><input type="text" name="fec1" size="15"> <input type="text" name="fec2" size="15"></td>
		</tr>
	</table>
</fieldset>
<fieldset>
<legend>Comprobante</legend>
	<table>
		<tr>
			<td><b><label>Clase</label></b></td>
			<td>
			<select name="clase">
				<option value="RI">Recibo de Ingresos</option>
				<option value="RES">Resolución</option>
				<option value="PPTO">Credito Suplementario</option>
				<option value="NC">Nota de Contabilidad</option>
				<option value="CP">Comprobante de Pago</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b><label>N&uacute;mero</label></b></td>
			<td><input type="text" name="numero"></td>		
		</tr>
		<tr>
			<td><b><label>Detalle</label></b></td>
			<td><textarea name="detalle"></textarea></td>
		</tr>
		<tr>
			<td><b><label>Sub-detalles</label></b></td>
			<td> </td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="grid">
					<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
						<ul>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n</li>	
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Tipo</li>		
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">&nbsp;</li>	
						</ul>
					</div>
					<div class="gridBody"></div>
					<div class="gridReference">
						<ul>
							<li style="min-width:250px;max-width:250px;"><input type="text" name="descr" size="30"></li>
							<li style="min-width:100px;max-width:100px;"><input type="text" name="monto" size="10"></li>
							<li style="min-width:100px;max-width:100px;">
									<input type="radio" id="TipoPos" value="P" checked="checked"><label for="TipoPos">Pos</label>
									<input type="radio" id="TipoNeg" value="N"><label for="TipoNeg">Neg</label>
							</li>
							<li style="min-width:50px;max-width:50px;"><button name="btnEli">Eliminar</button><button name="btnAdd">Agregar</button></li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<fieldset>
<legend>Ejecuci&oacute;n de Presupuesto</legend>
	<table>
		<tr>
			<td><b><label>Tipo</label></b></td>
			<td>
			<span name="RadEjePres">
				<input type="radio" name="rbtnRadEjePres" id="RadEjePresDebe" value="D" checked="checked"><label for="RadEjePresDebe">Debe</label>
				<input type="radio" name="rbtnRadEjePres" id="RadEjePresHaber" value="H"><label for="RadEjePresHaber">Haber</label>
			</span>
			</td>
		</tr>
		<tr>
			<td><b><label>Monto</label></b></td>
			<td><input type="text" name="monto_ejec_pres"></td>				
		</tr>
	</table>
</fieldset>
<fieldset>
<legend>Asignaciones Comprometidas</legend>
	<table>
		<tr>
			<td><b><label>Tipo</label></b></td>
			<td>
			<span name="RadAsign">
				<input type="radio" name="rbtnRadAsign" id="RadAsignDebe" value="D" checked="checked"><label for="RadAsignDebe">Debe</label>
				<input type="radio" name="rbtnRadAsign" id="RadAsignHaber" value="H"><label for="RadAsignHaber">Haber</label>
			</span>
			</td>
		</tr>
		<tr>
			<td><b><label>Monto</label></b></td>
			<td><input type="text" name="monto_asig"></td>
		</tr>
	</table>
</fieldset>
<fieldset>
<legend>Ejecuci&oacute;n de Gastos</legend>
	<table>
		<tr>
			<td><b><label>Tipo</label></b></td>
			<td>
			<span name="RadEjeGast">
				<input type="radio" name="rbtnRadEjeGast" id="RadEjeGastDebe" value="D" checked="checked"><label for="RadEjeGastDebe">Debe</label>
				<input type="radio" name="rbtnRadEjeGast" id="RadEjeGastHaber" value="H"><label for="RadEjeGastHaber">Haber</label>
			</span>
			</td>
		</tr>
		<tr>
			<td><b><label>Monto</label></b></td>
			<td><input type="text" name="monto_ejec_gast"></td>
		</tr>
	</table>
</fieldset>
<fieldset>
<legend>Saldos</legend>
	<table>
		<tr>
			<td>
			<input type="radio" name="saldos" value="O1" checked="checked">Opci&oacute;n 1: Saldo actual = Saldo anterior – Haber + Debe<br>
			<input type="radio" name="saldos" value="O2">Opci&oacute;n 2: Saldo actual = Saldo anterior – Debe + Haber
			</td>
		</tr>
	</table>
</fieldset>