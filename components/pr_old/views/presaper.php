<div>
	<table>
		<tr>
			<td><label>Periodo</label></td>
			<td><input type="text" name="periodo" style="width: 50px"></td>
			<td><label>Mes</label></td>
			<td><select name="mes">
				<option value="0">Todos</option>
				<option value="1" selected>Enero</option>
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
				<option value="12">Diciembre</option>
			</select></td>
			<td><label name="organomb"></label></td>
			<td name="FilOrga">
			<input type="hidden" name="organizacion" value="">
			<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
			<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
			</td>
			<td>&nbsp;<button name="btnAgregar">Partida</button></td>		
			<td>&nbsp;<button name="btnImprimir">Imprimir</button></td>	
			<td>&nbsp;<button name="btnExportar">Exportar</button></td>	
		</tr>
		<tr>
			<td>Clasificador: </td>
			<td><select name="FilTipo">
			<option value="I" selected>Ingresos</option>
			<option value="G">Gastos</option>
			</select>
			</td>
			<td><label name="clasnomb"></label></td>
			<td name="FilClas">
			<input type="hidden" name="clasificador" value="">
			<input type="radio" name="rbtnClas" id="rbtnClasSelect" value="C"><label for="rbtnClasSelect">Seleccionar</label>
			<input type="radio" name="rbtnClas" id="rbtnClasX" value="X" checked="checked"><label for="rbtnClasX">X</label>
			</td>
		</tr>
	</table>
</div>
	<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
		
		<ul>		
			<li class="ui-button ui-widget ui-button-text-only" style="min-width:30px;max-width:30px;">&nbsp;</li>
			<li class="ui-button ui-widget ui-button-text-only" style="min-width:400px;max-width:400px;">Clasificador</li>
			<li class="ui-button ui-widget ui-button-text-only" style="min-width:450px;max-width:450px;">Fuente de Financiamiento</li>
		</ul>
	</div>

