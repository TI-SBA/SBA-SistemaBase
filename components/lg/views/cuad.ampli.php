<fieldset>
	<table>
		<tr>
			<td width="90px"><label>Dependencia</label></td>
			<td width="430px"><h2 name="dependencia"></h2></td>
			<td width="110px"><label>Periodo</label></td>
			<td><span name="periodo"></span></td>
		</tr>
		<tr>
			<td><label>Trabajador</label></td>
			<td><span name="trabajador"></span></td>
			<td><label>Fecha de registro</label></td>
			<td><span name="fecreg"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset style="padding: 0 0;margin: 0 0;">
	<legend>Listado de Productos</legend>
	<div class="grid" style="width: 840px;overflow: hidden">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;width: 2260px;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">N&deg;</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:300px;max-width:300px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:300px;max-width:300px;">Clasificador</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Nombre</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Producto</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Unidad</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:1200px;max-width:1200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:1200px;max-width:1200px;">Calendario de Entrega</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Ene</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Feb</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Mar</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Abr</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">May</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Jun</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Jul</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Ago</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Set</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Oct</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Nov</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Dic</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:300px;max-width:300px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:300px;max-width:300px;">Ajuste</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Cantidad</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Precio</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">SubTotal</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:40px;max-width:40px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="max-height: 235px; width: 840px">
		<div class="gridBody" style="width: 2260px"></div>
		<div class="gridReference">
			<ul>
				<li style="min-width:70px;max-width:70px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:200px;max-width:200px;"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:200px;max-width:200px;"></li>
				<li style="min-width:95px;max-width:95px;"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes1" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes2" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes3" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes4" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes5" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes6" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes7" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes8" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes9" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes10" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes11" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:98px;max-width:98px;"><input type="text" name="mes12" size="8" style="width: 70px" value="0"></li>
				<li style="min-width:100px;max-width:100px;"></li>
				<li style="min-width:100px;max-width:100px;"><input type="text" name="precio" size="10" style="width: 70px" value="0.00"></li>
				<li style="min-width:100px;max-width:100px;"></li>
			</ul>
		</div>
	</div>
</fieldset>