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
				<td><button name="btnCerrarperiodo">Cerrar Periodo</button> <button name="btnSumar">Generar Reporte</button></td>				
			</tr>
			<tr>
				<td><b><label>Organizaci&oacute;n: </label></b> <input type="text" name="depen"><button name="btnOrga">Seleccionar</button></td>
				<td colspan="2"><b><label>Tipo: </label></b> <select name="tipo">
					<option value="I">Ingresos</option>
					<option value="G" selected="selected">Gastos</option>
				</select></td>
			</tr>
			<tr>
				<td><b><label>C&oacute;digo</label></b> <span name="codigo"></span></td>
				<td><b><label>Nombre</label></b> <span name="nombre"></span></td>
				<td><b><label>Fuente de Financiamiento</label></b><select name="fuen"></select></td>
			</tr>
			<tr>
				<td colspan="3"><b><label>Descripci&oacute;n</label></b> <span name="descr"></span></td>
			</tr>
		</table>		
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">&nbsp;</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">PPTO. AUTORIZADO PIM</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Compromiso del Mes</li>		
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Acumulado</li>	
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Ejecuci&oacute;n del mes</li>	
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Acumulado</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Saldo de Presupuesto</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="height: 150px;">
	<div class="gridBody" ></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:250px;max-width:250px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
		</ul>
	</div>
	</div>