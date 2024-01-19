<fieldset>
	<legend>Expediente</legend>
	<table>
		<tbody>
			<tr><td></td></tr>
		</tbody>
	</table>
	<table>
		<tbody>
			<tr>
				<td><label>Tipo</label></td>
				<td><select name="tipo_expd">
					<option value="N">Normal</option>
					<option value="C">Circular</option>
				</select></td>
			</tr>
		</tbody>
	</table>
</fieldset>
<fieldset style="padding: 4px;">
	<legend>Gestor</legend>
	<table>
		<tr>
			<td rowspan="3"><button name="btnBusGestor">Buscar Gestor</button><br />
			<button name="btnNewGestor">Agregar Gestor</button></td>
			<td><label>Nombre</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td><label>Apellidos</label></td>
			<td><span name="apell"></span></td>
		</tr>
		<tr>
			<td><label name="lblDocIdent">DNI</label></td>
			<td><span name="dni"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset style="padding: 4px;">
	<legend>Asunto del Expediente</legend>
	<!--<label>C&oacute;digo de Expediente&nbsp;</label><input type="text" name="cod_expd" placeholder="Ejem: GG-0215, etc.">-->
	<label>Observaciones del Expediente&nbsp;</label>
	<textarea type="text" name="observ_expd" cols="62" rows="3"></textarea>
	<div>
		<ul>
			<li><a href="#tabs-ExpdNew1">TUPA</a></li>
			<li><a href="#tabs-ExpdNew2">Otros</a></li>
		</ul>
		<div id="tabs-ExpdNew1">
			<table>
				<tr><td colspan="2"><input name="textPros" placeholder="Ingrese nombre del Procedimiento" style="width:400px;"><button name="btnTupa" style="float:right;">Buscar TUPA</button></td></tr>
				<tr>
					<!--  <td rowspan="3" width="130"></td>-->
					<td><label>Procedimiento</label></td>
					<td><span name="proc"></span></td>
				</tr>
				<tr>
					<td><label>Modalidad</label></td>
					<td><span name="mod"></span></td>
				</tr>
				<tr>
					<td><label>Plazo</label></td>
					<td><span name="plazo_tupa"></span>&nbsp;d&iacute;as</td>
				</tr>
			</table>
		</div>
		<div id="tabs-ExpdNew2">
			<table>
				<tr>
					<td><label>Asunto</label></td>
					<td><input type="text" name="concepto" size="65"></td>
				</tr>
				<tr>
					<td><label>Plazo</label></td>
					<td><input type="text" name="plazo" style="width: 40px;">&nbsp;d&iacute;as</td>
				</tr>
			</table>
		</div>
	</div>
</fieldset>
<fieldset style="padding: 2px;">
	<legend>Documentos</legend>
	<button name="btnAgregarDoc">Agregar Documento</button>
	<div class="grid" style="width: 587px;">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">&nbsp;</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">Num</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:200px;max-width:200px;">Tipo</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">Folios</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">Registrado</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:20px;max-width:20px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="width: 587px;height: 110px;">
		<div class="gridBody"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:50px;max-width:50px"></li>
				<li style="min-width:120px;max-width:120px"></li>
				<li style="min-width:200px;max-width:200px"></li>
				<li style="min-width:120px;max-width:120px"></li>
				<li style="min-width:120px;max-width:120px"></li>
			</ul>
		</div>
	</div>
</fieldset>