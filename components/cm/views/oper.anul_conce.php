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
					<li style="min-width:146px;max-width:146px;">Ocupantes</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Traslado y Observaciones</li>
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
		<legend>Espacio</legend>
		<select style="width:250px;" name="cboEspacio"></select>
		<div name="det"></div>
	</fieldset>
	<fieldset name="section3">
		<legend>Ocupantes</legend>
		<div name="divAsig">
			<div class="grid" style="width:460px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:350px;max-width:350px;">Asignados</li>
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
		</div>
		<div name="divDifu">
			<div class="grid" style="width:460px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:350px;max-width:350px;">Difuntos</li>
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
		</div>
	</fieldset>
	<fieldset name="section4">
		<legend>Traslado y Observaciones</legend>
		<table>
			<tr>
				<td><label>Espacio destino</label></td>
				<td><span>Osario</span></td>
			</tr>
			<tr>
				<td><label>Fecha programada</label></td>
				<td><input type="text" name="fecprog"></td>
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