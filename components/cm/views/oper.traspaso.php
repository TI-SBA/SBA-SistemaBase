<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Propietario</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Espacio</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Ocupante</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Nuevo Propietario</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1" style="height:65px;">
		<legend>Propietario</legend>
		<table>
			<tr>
				<td rowspan="2" width="176"><button name="btnBusPro">Buscar Propietario</button><br />
				<button name="btnNewPro">Agregar Propietario</button></td>
				<td width="72"><label>Nombre:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td width="72"><label>Apellidos:</label></td>
				<td><span name="apell"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2" style="height:160px;">
		<legend>Espacio</legend>
		<table>
				<tr>
					<td width="176"><select name="cboSelEspacio"></select></td>
				</tr>
		</table>
		<div><table name="det"></table></div>
	</fieldset>
	<fieldset name="section3">
		<legend>Ocupantes</legend>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">N&deg;</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:350px;max-width:350px;">Ocupante</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:50px;max-width:50px"></li>
					<li style="min-width:350px;max-width:350px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section4" style="height:65px;">
		<legend>Nuevo Propietario</legend>
		<table>
			<tr>
				<td rowspan="2" width="176"><button name="btnBusCuent">Buscar Cuenta</button><br />
				<button name="btnNewCuent">Agregar Cuenta</button></td>
				<td width="72"><label>Nombre:</label></td>
				<td><span name="nombCuent"></span></td>
			</tr>
			<tr>
				<td width="72"><label>Apellidos:</label></td>
				<td><span name="apellCuent"></span></td>
			</tr>
		</table>
	</fieldset>
</div>