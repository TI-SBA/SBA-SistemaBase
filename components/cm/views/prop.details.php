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
					<li style="min-width:146px;max-width:146px;">Espacios</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Ocupantes</li>
				</ul>
			</a>
			<a class="item" name="section4">
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
				<td><label>Registrado</label></td>
				<td><span name="spFecreg"></span></td>
			</tr>
			<tr>
				<td><label>Nombre</label></td>
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
			<tr>
				<td><label>Tel&eacute;fono</label></td>
				<td><span name="spTelef"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n</label></td>
				<td><span name="spDirec"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2" style="height:360px;overflow-y: auto;">
		<legend>Espacios</legend>
		<div style="height:355px;overflow-y: auto;width:102%;">
		<table class="tableRefEspa" style="display:none;">
			<tr>
				<td colspan="4"><span name="spEspaNomb"></span></td>
			</tr>
			<tr>
				<td><label>Concedido</label></td>
				<td><span name="spFecConce"></span></td>
				<td><label>Vencimiento</label></td>
				<td><span name="spFecVenc"></span></td>
			</tr>
		</table>
		</div>
	</fieldset>
	<fieldset name="section3" style="height:360px;overflow-y: auto;">
		<legend>Ocupantes</legend>
		<div style="height:355px;overflow-y: auto;width:102%;">
		<table class="tableRefOcup" style="display:none;">
			<tr>
				<td colspan="4"><span name="spOcupNomb"></span></td>
			</tr>
			<tr>
				<td><label>Espacio</label></td>
				<td colspan="3"><span name="spEspaNomb"></span></td>
			</tr>
			<tr>
				<td><label>Asignado</label></td>
				<td><span name="spFecAsig"></span></td>
				<td><label>Inhumado</label></td>
				<td><span name="spFecInh"></span></td>
			</tr>
		</table>
		</div>
	</fieldset>
	<fieldset name="section4" style="height:360px;overflow-y: auto;">
		<legend>Historial de Operaciones</legend>
		<div class="grid" style="height:355px;overflow-y: auto;width:102%;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:80px;max-width:80px"></li>
					<li style="min-width:220px;max-width:220px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>