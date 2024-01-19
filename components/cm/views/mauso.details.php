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
					<li style="min-width:146px;max-width:146px;">Construcci&oacute;n</li>
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
	<fieldset name="section1" style="height:360px;">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td><label>Denominaci&oacute;n</label></td>
				<td><span name="spNomb"></span></td>
			</tr>
			<tr>
				<td><label>Lote</label></td>
				<td><span name="spLote"></span></td>
			</tr>
			<tr>
				<td><label>Capacidad</label></td>
				<td><span name="spCapa"></span></td>
			</tr>
			<tr>
				<td><label>Referencia</label></td>
				<td><span name="spRef"></span></td>
			</tr>
			<tr>
				<td><label>Tipo</label></td>
				<td><span name="spTipo"></span></td>
			</tr>
			<tr>
				<td><label>Registrado</label></td>
				<td><span name="spFecreg"></span></td>
			</tr>
			<tr>
				<td><label>Estado</label></td>
				<td><span name="spEstado"></span></td>
			</tr>
			<tr>
				<td><label>Propietario</label></td>
				<td><a name="prop"></a></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2" style="height:360px;overflow-y: auto;">
		<legend>Construcci&oacute;n</legend>
		<table>
			<tr>
				<td><label>Fecha de Registro</label></td>
				<td><span name="spConsFecreg"></span></td>
			</tr>
			<tr>
				<td><label>Fecha Programada</label></td>
				<td><span name="spConsFecprog"></span></td>
			</tr>
			<tr>
				<td><label>Fecha de Vencimiento</label></td>
				<td><span name="spConsFecven"></span></td>
			</tr>
			<tr>
				<td><label>Capacidad</label></td>
				<td><span name="spConsCapa"></span></td>
			</tr>
			<tr>
				<td><label>Largo</label></td>
				<td><span name="spConsLargo"></span></td>
			</tr>
			<tr>
				<td><label>Ancho</label></td>
				<td><span name="spConsAncho"></span></td>
			</tr>
			<tr>
				<td><label>Altura 1</label></td>
				<td><span name="spConsAlt1"></span></td>
			</tr>
			<tr>
				<td><label>Altura 2</label></td>
				<td><span name="spConsAlt2"></span></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td><span name="spConsObserv"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3" style="height:360px;overflow-y: auto;">
		<legend>Ocupantes</legend>
		<div style="height:340px;overflow-y: auto;width:102%;">
		<table class="tableRefOcup" style="display:none;">
			<tr>
				<td colspan="4"><span name="spOcupNomb"></span></td>
			</tr>
			<tr>
				<td><label>Asignado</label></td>
				<td><span name="spOcupFecasig"></span></td>
				<td><label>Inhumado</label></td>
				<td><span name="spOcupFecinh"></span></td>
			</tr>
		</table>
		</div>
	</fieldset>
	<fieldset name="section4" style="height:360px;overflow-y: auto;">
		<legend>Historial de Operaciones</legend>
		<div class="grid" style="height:340px;overflow-y: auto;width:102%;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:100px;max-width:100px; text-decoration:underline;"></li>
					<li style="min-width:80px;max-width:80px;color:#666666;" ></li>
					<li style="min-width:170px;max-width:170px;color:#666666;" ></li>
					<li style="min-width:180px;max-width:180px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>