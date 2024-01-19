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
					<li style="min-width:146px;max-width:146px;">Contabilidad Patrimonial</li>
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
				<td colspan="2"><h3>Recibo de Ingresos N&deg;<span name="num"></span></h3></td>
			</tr>
			<tr>
				<td width="95px"><label>Fecha</label></td>
				<td><span name="fec"></span></td>
			</tr>
			<tr>
				<td><label>Organizaci&oacute;n</label></td>
				<td><span name="orga"></span></td>
			</tr>
			<tr>
				<td><label>Responsable</label></td>
				<td><span name="respo"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Contabilidad Patrimonial</legend>
		<div class="grid payment" style="overflow: hidden;width: 450px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
						<ul style="display:block">
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
						</ul>
						<ul style="display:block">
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
						</ul>
					</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Importe del Debe</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;width: 450px;">
			<div class="gridBody" width="570px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:150px;max-width:150px;"></li>
				</ul>
			</div>
		</div>
		<br />
		<div class="grid payment" style="overflow: hidden;width: 450px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
						<ul style="display:block">
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
						</ul>
						<ul style="display:block">
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
						</ul>
					</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Importe del Haber</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n &#45; Libro Movimientos del Efectivo</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;width: 450px;">
			<div class="gridBody" width="570px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:250px;max-width:250px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>