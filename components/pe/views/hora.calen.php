<div class="ui-layout-west">
	<div class="ui-layout-north">
		<input type="text" name="fecref">
		<!-- <span class="fc-button fc-button-prev ui-state-default ui-corner-left"><span class="fc-button-inner"><span class="fc-button-content"><span class="fc-icon-wrap"><span class="ui-icon ui-icon-circle-triangle-w"></span></span></span><span class="fc-button-effect"><span></span></span></span></span><span class="fc-button fc-button-next ui-state-default ui-corner-right"><span class="fc-button-inner"><span class="fc-button-content"><span class="fc-icon-wrap"><span class="ui-icon ui-icon-circle-triangle-e"></span></span></span><span class="fc-button-effect"><span></span></span></span></span><span class="fc-header-space"></span><span class="fc-button fc-button-today ui-state-default ui-corner-left ui-corner-right ui-state-disabled"><span class="fc-button-inner"><span class="fc-button-content">Hoy</span><span class="fc-button-effect"><span></span></span></span></span> -->
	</div>
	<div class="ui-layout-center">
		<button name="btnTra">Seleccionar</button>
		<button name="btnGuardar">Guardar</button>
		<table>
			<tr>
				<td><label>Nombre</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>Cargo</label></td>
				<td><span name="cargo"></span></td>
			</tr>
			<tr>
				<td><label>Tarjeta</label></td>
				<td><span name="tarjeta"></span></td>
			</tr>
			<tr>
				<td><label>Local</label></td>
				<td><span name="local"></span></td>
			</tr>
		</table>
	</div>
	<div class="ui-layout-south">
		<fieldset>
			<legend>Leyenda</legend>
			<div class="grid">
				<div class="gridBody"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:30px;max-width:30px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
					</ul>
				</div>
			</div>
		</fieldset>
	</div>
</div>
<div name="calendar" class="ui-layout-center"></div>