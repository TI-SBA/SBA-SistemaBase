<table>
	<tr>
		<td><label>Nombre</label></td>
		<td><input type="text" name="nomb"></td>
	</tr>
	<tr>
		<td><label>Descripci&oacute;n</label></td>
		<td><textarea rows="2" cols="40" name="descr"></textarea></td>
	</tr>
	<tr>
		<td><label>C&oacute;digo</label></td>
		<td><input type="text" name="cod"></td>
	</tr>
	<tr>
		<td><label>IGV</label></td>
		<td><select name="igv">
			<option value="gravada">Operacion gravada</option>
			<option value="inafecto">Operacion inafecta</option>
			<option value="exonerada">Operacion exonerada</option>
		</select></td>
	</tr>
	<tr>
		<td><label>F&oacute;rmula</label></td>
		<td><span name="form"></span>&nbsp;<button name="btnForm">Ingresar</button></td>
	</tr>
	<tr>
		<td><label>Enlace</label></td>
		<td>
			<input type="radio" id="rbtnEnla1" name="rbtnEnla" value="CL" checked="checked" /><label for="rbtnEnla1">Clasificador</label>
			<input type="radio" id="rbtnEnla2" name="rbtnEnla" value="CU" /><label for="rbtnEnla2">Cuenta</label>
		</td>
	</tr>
	<tr>
		<td><label>Clasificador</label></td>
		<td><span name="clasif"></span>&nbsp;<button name="btnClasi">Seleccionar</button></td>
	</tr>
	<tr>
		<td><label>Cuenta Contable</label></td>
		<td><span name="cuenta"></span>&nbsp;<button name="btnCuen">Seleccionar</button></td>
	</tr>
</table>
<div class="grid" style="overflow: hidden;width: 450px;">
	<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
		<ul>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:380px;max-width:380px;">Servicio</li>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">&nbsp;</li>
		</ul>
	</div>
</div>
<div class="grid" style="height: 100px;width: 470px;">
	<div class="gridBody" width="450px"></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:380px;max-width:380px;"><span name="serv"></span>&nbsp;<button name="btnServ">Seleccionar</button></li>
			<li style="min-width:65px;max-width:65px;"><button name="btnEli">Eliminar</button>&nbsp;<button name="btnAgre">Agregar</button></li>
		</ul>
	</div>
</div>