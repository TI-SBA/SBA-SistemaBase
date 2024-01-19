<table>
	<tr>
		<td><label>N&uacute;mero de rendici&oacute;n</label></td>
		<td><span name="num"></span></td>
	</tr>
	<tr>
		<td><label>Caja Chica</label></td>
		<td><span name="caja"></span></td>
	</tr>
</table>
<fieldset>
	<legend>Movimientos</legend>
	<div class="grid payment" style="overflow: hidden;width: 595px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">N&deg;</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Comprobante</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Fecha</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Tipo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">N&deg;</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Detalle de Gasto</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Beneficiario</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Concepto del Gasto</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Organizaci&oacute;n</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Clasificador de Gasto</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="max-height: 250px;width: 595px;">
		<div class="gridBody" style="width: 1150px;"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:50px;max-width:50px;"></li>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:250px;max-width:250px;"></li>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
			</ul>
		</div>
	</div>
	<br />
	<div class="grid">
		<div class="gridBody">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;text-align: left;">Saldo Anterior</li>
				<li style="min-width:120px;max-width:120px;"></li>
			</ul>
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;text-align: left;">Incremento al Fondo</li>
				<li style="min-width:120px;max-width:120px;"></li>
			</ul>
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;text-align: left;">Total</li>
				<li style="min-width:120px;max-width:120px;"></li>
			</ul>
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;text-align: left;">Importe de la Presente Rendici&oacute;n</li>
				<li style="min-width:120px;max-width:120px;"></li>
			</ul>
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;text-align: left;">Saldo Actual</li>
				<li style="min-width:120px;max-width:120px;"></li>
			</ul>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Estad&iacute;stica del Objeto de Gasto</legend>
	<div class="grid payment" style="overflow: hidden;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;width: 595px;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Partida Presupuestal</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:230px;max-width:230px;">Nombre</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" name="ul_orga">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only">Organizaciones</li>
					</ul>
					<ul style="display:block"></ul>
				</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="max-height: 300px;width: 595px;">
		<div class="gridBody"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:230px;max-width:230px;"></li>
			</ul>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Contabilidad del Proceso Financiero o Patrimonial</legend>
	<table>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><span name="cuenta"></span>&nbsp;<button name="btnCuenta"></button></td>
		</tr>
		<tr>
			<td><label>Debe/Haber</label></td>
			<td>
				<input type="radio" id="rbtnPoli1" name="rbtnPoli" value="D" checked="checked" /><label for="rbtnPoli1">D</label>
				<input type="radio" id="rbtnPoli2" name="rbtnPoli" value="H" /><label for="rbtnPoli2">H</label>
			</td>
		</tr>
		<tr>
			<td><label>Moneda</label></td>
			<td><span>Nuevos Soles</span></td>
		</tr>
		<tr>
			<td><label>Importe</label></td>
			<td><input type="text" name="importe" size="7"></td>
		</tr>
		<tr>
			<td colspan="2"><button name="btnAgre">Agregar</button></td>
		</tr>
	</table>
	<div class="grid payment" style="overflow: hidden;width: 570px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">Importe</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Haber</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="max-height: 110px;width: 570px;">
		<div class="gridBody" width="570px"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:200px;max-width:200px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:70px;max-width:70px;"><button name="btnEli">Eliminar</button></li>
			</ul>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Contabilidad del Proceso Presupuestario</legend>
	<table>
		<tr>
			<td><label>Cuenta Contable</label></td>
			<td><span name="cuenta"></span>&nbsp;<button name="btnCuenta"></button></td>
		</tr>
		<tr>
			<td><label>Debe/Haber</label></td>
			<td>
				<input type="radio" id="rbtnPoli1_1" name="rbtnPoli_1" value="D" checked="checked" /><label for="rbtnPoli1_1">D</label>
				<input type="radio" id="rbtnPoli2_1" name="rbtnPoli_1" value="H" /><label for="rbtnPoli2_1">H</label>
			</td>
		</tr>
		<tr>
			<td><label>Moneda</label></td>
			<td><span>Nuevos Soles</span></td>
		</tr>
		<tr>
			<td><label>Importe</label></td>
			<td><input type="text" name="importe" size="7"></td>
		</tr>
		<tr>
			<td colspan="2"><button name="btnAgre">Agregar</button></td>
		</tr>
	</table>
	<div class="grid payment" style="overflow: hidden;width: 570px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:240px;max-width:240px;">Importe</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Haber</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid payment" style="max-height: 110px;width: 570px;">
		<div class="gridBody" width="570px"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:200px;max-width:200px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:70px;max-width:70px;"><button name="btnEli">Eliminar</button></li>
			</ul>
		</div>
	</div>
</fieldset>