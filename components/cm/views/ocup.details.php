<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Datos Generales</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Inhumaci&oacute;n</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Historial de Operaciones</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1" style="height:450px;">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td><label>Propietario Responsable</label></td>
				<td><a name="titular"></a></td>
			</tr>
			<tr>
				<td><label>Ubicaci&oacute;n</label></td>
				<td><a name="ubicacion"></a></td>
			</tr>
			<tr>
				<td><label>Registrado</label></td>
				<td><span name="spFecreg"></span></td>
			</tr>
			<tr>
				<td><label>Asignado</label></td>
				<td><span name="spFecasig"></span></td>
			</tr>
			<tr>
				<td><label>Nombre de Fallecido</label></td>
				<td><span name="spNomb"></span></td>
			</tr>
			<tr>
				<td><label>Apellido Paterno</label></td>
				<td><span name="spAppat"></span></td>
			</tr>
			<tr>
				<td><label>Apellido Materno</label></td>
				<td><span name="spApmat"></span></td>
			</tr>
			<tr>
				<td><label>Documento de Identidad</label></td>
				<td><span name="spIdent"></span></td>
			</tr>
			<tr>
				<td><label>Fecha de Nacimiento</label></td>
				<td><span name="spFecnac"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2" style="height:450px;">
		<legend>Inhumaci&oacute;n</legend>
		<table>
			<tr>
				<td><label>Fecha de Defunci&oacute;n</label></td>
				<td><span name="spFecdef"></span></td>
			</tr>
			<tr>
				<td><label>Edad</label></td>
				<td><a name="edad"></a></td>
			</tr>
			<tr>
				<td><label>Causa</label></td>
				<td><a name="causa"></a></td>
			</tr>
			<tr>
				<td><label>N&deg; Partida</label></td>
				<td><span name="spNum"></span></td>
			</tr>
			<tr>
				<td><label>Municipalidad</label></td>
				<td><a name="municipalidad"></a></td>
			</tr>
			<tr>
				<td><label>Funeraria</label></td>
				<td><a name="funeraria"></a></td>
			</tr>
			<tr>
				<td><label>Fecha de Inhumaci&oacute;n</label></td>
				<td><span name="spFecinh"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3" style="height:450px;overflow-y: auto;">
		<legend>Historial de Operaciones</legend>
		<div class="grid" style="width:450px;height:450px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:90px;max-width:90px;"></li>
					<li style="min-width:120px;max-width:120px"></li>
					<li style="min-width:140px;max-width:140px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>