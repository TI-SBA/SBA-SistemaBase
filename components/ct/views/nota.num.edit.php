<table>
	<tr>
		<td><label>Periodo</label></td>
		<td><input type="text" name="periodo" /></td>
	</tr>
	<tr>
		<td><label>N&uacute;mero de Nota Literal</label></td>
		<td><span name="num"></span>&nbsp;<button name="btnNum">Seleccionar</button></td>
	</tr>
	<tr>
		<td><label>Nombre de la Nota Literal</label></td>
		<td><span name="nomb"></span></td>
	</tr>
	<tr>
		<td><label>Tipo de Nota</label></td>
		<td>
			<input type="radio" id="rbtnTipo1" name="rbtnTipo" value="A" checked="checked" /><label for="rbtnTipo1">Activo</label>
			<input type="radio" id="rbtnTipo2" name="rbtnTipo" value="O" /><label for="rbtnTipo2">Otro</label>
		</td>
	</tr>
	<tr>
		<td><label>Documento</label></td>
		<td>
			<select name="documento">
				<option value="S">Situaci&oacute;n</option>
				<option value="G">Gesti&oacute;n</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label>Clase</label></td>
		<td>
			<select name="clase">
				<option value="A">Activo</option>
				<option value="P">Pasivo y Patrimonio</option>
				<option value="I">Ingresos</option>
				<option value="G">Costos y Gastos</option>
				<option value="O">Otros Ingresos y Gastos</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label>Subclase</label></td>
		<td>
			<select name="subclase">
				<option value="C">Corriente</option>
				<option value="N">No Corriente</option>
				<option value="P">Patrimonio</option>
				<option value="">--</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Seleccionar Cuenta</b></td>
		<td><button name="btnAddCuen">Seleccionar</button></td>
	</tr>
</table>
<div name="tipoO">
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Cuenta Contable</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:50px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:150px;">Descripci&oacute;n</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:110px;max-width:110px;">Importe</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:30px;max-width:30px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="max-height:220px;">
		<div class="gridBody" ></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:250px;max-width:250px;"></li>
				<li style="min-width:110px;max-width:110px;"></li>
				<li style="min-width:30px;max-width:30px;"><button name="btnEliCuen">Eliminar</button></li>
			</ul>
		</div>
	</div>
</div>
<div name="tipoA">
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Cuenta Contable</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:50px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:150px;">Descripci&oacute;n</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Valor Bruto</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Depreciaci&oacute;n Acumulada</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Valor Neto</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:30px;max-width:30px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="max-height:220px;">
		<div class="gridBody" ></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:150px;max-width:150px;"></li>
				<li style="min-width:250px;max-width:250px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:120px;max-width:120px;"></li>
				<li style="min-width:30px;max-width:30px;"><button name="btnEliCuen">Eliminar</button></li>
			</ul>
		</div>
	</div>
</div>