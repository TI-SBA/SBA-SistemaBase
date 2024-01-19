<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Comprobante</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Propietario</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Programaci&oacute;n</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Comprobante</legend>
		<table>
			<tr>
				<td width="150px"><label>Serie</label></td>
				<td><span name=serie></span></td>
				<td><label>N&uacute;mero</label></td>
				<td><span name="numero"></span></td>
			</tr>
			<tr>
				<td>Propietario</td>
				<td colspan="3"><span name="entidad"></span></td>
			</tr>
		</table>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:70px;max-width:70px;">N&deg;</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:390px;max-width:390px;">Servicios</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:68px;max-width:68px"></li>
					<li style="min-width:388px;max-width:388px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section2">
		<legend>Propietario</legend>
		<label>OCUPANTES</label>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">DIFUNTO</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">ESPACIOS</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:50px;max-width:50px"></li>
					<li style="min-width:120px;max-width:120px"></li>
				</ul>
			</div>
		</div>
		<label>ESPACIOS</label>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">TIPO</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">UBICACI&Oacute;N</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:50px;max-width:50px"></li>
					<li style="min-width:120px;max-width:120px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section3">
		<legend>Programaci&oacute;n</legend>
		<button name="btnOper">Agregar Operaci&oacute;n</button>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">TIPO</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">UBICACI&Oacute;N</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:50px;max-width:50px"></li>
					<li style="min-width:120px;max-width:120px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>