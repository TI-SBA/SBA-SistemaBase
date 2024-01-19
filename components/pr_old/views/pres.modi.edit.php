	<fieldset>
	<table>
	<tr>
		<td><label>Periodo</label></td>
		<td><b><span name="periodo"></span></b>&nbsp;<select name="mes">
				<option value="1" selected>Enero</option>
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
			</select></td>
	</tr>
	<tr>
		<td><label>Numero de Credito Suplementario</label></td>
		<td><input type="text" name="num_credito" style="width:50px;" readonly="readonly"></td>
	</tr>
	</table>
	</fieldset>
	<fieldset>
	<legend>Ampliaciones</legend>
	<button name="btnAmpli" style="float: left;">Nueva Ampliaci&oacute;n</button><br />
	<div class="grid">
	<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
		<ul>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Organizaci&oacute;n</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Clasificador</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Fuente</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Importe</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:30px;max-width:30px;">&nbsp;</li>
		</ul>
	</div>
	<div class="gridBody" name="gridampli"></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:200px;max-width:200px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:200px;max-width:200px;"></li>
			<li style="min-width:30px;max-width:30px;"><button name="btnEliAmp">Eliminar</button></li>
		</ul>
	</div>
	</div>
	</fieldset>