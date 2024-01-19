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
					<li style="min-width:146px;max-width:146px;">Ocupante</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Espacio</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Nuevo Ocupante</li>
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
	<fieldset name="section2">
		<legend>Ocupante</legend>
		<select style="width:250px;" name="ocupante"></select>
	</fieldset>
	<fieldset name="section3">
		<legend>Espacio</legend>
		<div><table name="espacio" cellpadding="3"></table></div>
	</fieldset>
	<fieldset name="section4">
		<legend>Nuevo Ocupante y Observaciones</legend>
		<table>
			<tr>
				<td rowspan="2" width="176"><button name="btnBusOcu">Buscar Ocupante</button><br />
				<button name="btnNewOcu">Agregar Ocupante</button></td>
				<td width="72"><label>Nombre:</label></td>
				<td><span name="ocu_nomb"></span></td>
			</tr>
			<tr>
				<td width="72"><label>Apellidos:</label></td>
				<td><span name="ocu_apell"></span></td>
			</tr>
		</table>
		<table>
			<tr>
				<td><label>Observaciones</label></td>
			</tr>
			<tr>
				<td><textarea name="observ" cols="60" rows="2"></textarea></td>
			</tr>
		</table>
	</fieldset>
</div>