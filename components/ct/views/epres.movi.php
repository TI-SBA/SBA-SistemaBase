	<table>
			<tr>			
				<td><b><label>Periodo: </label></b> <select name="mes"><option value="1">Enero</option>
				<option value="2">Febrero</option>
				<option value="3">Marzo</option>
				<option value="4">Abril</option>
				<option value="5">Mayo</option>
				<option value="6">Junio</option>
				<option value="7">Julio</option>
				<option value="8">Agosto</option>
				<option value="9">Setiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6"></td>
				<td><b><label>Estado: </label></b> <span name="estado"></span></td>
				<td><button name="btnEstructura">Estructura</button> <button name="btnCerrar">Cerrar</button> <button name="btnGuardar">Guardar</button> <button name="btnExportar">Exportar</button></td>				
			</tr>
		</table>		
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Saldo Mes Anterior</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Haber</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Movimientos Mes Actual</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Haber</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Sumas Acumuladas</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Haber</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Saldo Presente Mes</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Haber</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="height: 150px;">
	<div class="gridBody" ></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:250px;max-width:250px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"><input type="text" name="monto_debe" size="10"></li>
			<li style="min-width:100px;max-width:100px;"><input type="text" name="monto_haber" size="10"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
		</ul>
	</div>
	</div>