<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Ocupantes</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Ubicaci&oacute;n</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Asignaci&oacute;n</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Ocupantes</legend>
		<div class="grid" style="width:630px;overflow: hidden;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">DNI</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Nombre</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:130px;max-width:130px;">Apelido Paterno</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:130px;max-width:130px;">Apellido Materno</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">Fecha de Nacimiento</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:630px;">
			<div class="gridBody" style="width:730px;max-height:250px;"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:90px;max-width:90px"><input type="text" name="dni" size="8"></li>
					<li style="min-width:170px;max-width:170px"><input type="text" name="nomb" size="18"></li>
					<li style="min-width:130px;max-width:130px"><input type="text" name="appat" size="14"></li>
					<li style="min-width:130px;max-width:130px"><input type="text" name="apmat" size="14"></li>
					<li style="min-width:120px;max-width:120px"><input type="text" name="fecnac" size="10"></li>
					<li style="min-width:90px;max-width:90px"><button name="btnEli">Eliminar</button>&nbsp;<button name="btnAgr">Agregar</button></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section2">
		<legend>Ubicaci&oacute;n</legend>
		<button name="btnEspacio">Buscar Espacio</button>
		<div name="det"></div>
		<hr>
		<table>
			<tr>
				<td width="90px"><label>Propietario</label></td>
				<td><span name="spPropDef"></span></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><label>Propietario</label></td>
				<td name="trProp"><span name="spProp"></span><br/>
				<input type="radio" name="rbtnProp" id="rbtnPropBen" value="0" checked="checked"><label for="rbtnPropBen">Beneficencia</label>
				<input type="radio" name="rbtnProp" id="rbtnPropOtr" value="1"><label for="rbtnPropOtr">Asignar</label></td>
			</tr>
			<tr class="trConProp">
				<td><label>Propietario seleccionado</label></td>
				<td><span name="spPropSel"></span>&nbsp;<button name="btnBuscarProp">Buscar</button><button name="btnAgregarProp">Agregar</button></td>
			</tr>
			<tr class="trConProp">
				<td><label>Tipo de concesi&oacute;n</label></td>
				<td name="trConc">
					<input type="radio" name="rbtnConc" id="rbtnConcTemp" value="T" checked="checked"><label for="rbtnConcTemp">Temporal</label>
					<input type="radio" name="rbtnConc" id="rbtnConcPerp" value="P"><label for="rbtnConcPerp">Permanente</label>
				</td>
			</tr>
			<tr class="trConPropTemp">
				<td><label>A&ntilde;os</label></td>
				<td><input type="number" name="anos"></td>
			</tr>
			<tr class="trConPropTemp">
				<td><label>Fecha de vencimiento</label></td>
				<td><span name="spFecven"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3">
		<legend>Asignaci&oacute;n</legend>
		<label>Registrar como </label><div name="divAsig">
			<input type="radio" id="rbtnAsig" name="rbtnAsig" value="0" checked="checked" /><label for="rbtnAsig">Asignado</label>
			<input type="radio" id="rbtnInh" name="rbtnAsig"  value="1"/><label for="rbtnInh">Inhumado</label>
		</div>
		<table>
			<tr>
				<td><label>Fecha de defunci&oacute;n</label></td>
				<td><input type="text" name="fecdef"></td>
			</tr>
			<tr>
				<td><label>Edad</label></td>
				<td><input type="number" name="edad"></td>
			</tr>
			<tr>
				<td><label>Causa de fallecimiento</label></td>
				<td><input type="text" name="causa"></td>
			</tr>
			<tr>
				<td><label>Funeraria</label></td>
				<td><span name="funeraria"></span><button name="btnFune">Buscar</button></td>
			</tr>
			<tr>
				<td><label>Municipalidad</label></td>
				<td><span name="municipalidad"></span><button name="btnMuni">Buscar</button></td>
			</tr>
			<tr>
				<td><label>Partida de defunci&oacute;n</label></td>
				<td><input type="text" name="partida"></td>
			</tr>
			<tr>
				<td><label>Fecha de inhumaci&oacute;n</label></td>
				<td><input type="text" name="fecinh"></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td><input type="text" name="observ"></td>
			</tr>
		</table>
	</fieldset>
</div>