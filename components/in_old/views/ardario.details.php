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
					<li style="min-width:146px;max-width:146px;">Arrendamientos</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td><label>Registrado</label></td>
				<td width="250px"><span name="fecreg"></span></td>
				<td rowspan="6"><img src="images/admin.png" style="width:100px;height:100px;"></td>
			</tr>
			<tr>
				<td><label>Nombre</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>Apellido Paterno</label></td>
				<td><span name="appat"></span></td>
			</tr>
			<tr>
				<td><label>Apellido Materno</label></td>
				<td><span name="apmat"></span></td>
			</tr>
			<tr>
				<td><label>Documento de Identidad</label></td>
				<td><span name="dni"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Arrendamientos</legend>
		<div name="divCop" style="display: none;">
			<table>
				<tr>
					<td><label>Espacio</label></td>
					<td colspan="3"><span name="espacio"></span></td>
				</tr>
				<tr>
					<td><label>Fecha de ocupaci&oacute;n</label></td>
					<td><span name="fecocu"></span></td>
					<td><label>Fecha de desocupaci&oacute;n</label></td>
					<td><span name="fecdes"></span></td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>