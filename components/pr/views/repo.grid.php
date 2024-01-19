<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:196px;max-width:196px;">Programaci&oacute;n Mensual del POI</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:196px;max-width:196px;">Programaci&oacute;n Mensualizada de ingresos/gastos</li>
				</ul>
			</a>			
			<a class="item" name="section3">
				<ul>
					<li style="min-width:196px;max-width:196px;">Estructura Programatica y Funcional (Anexo 1)</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:196px;max-width:196px;">Programaci&oacute;n Mensualizada de Servicios Productivos</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:196px;max-width:196px;">POI/POE - Formato 01</li>
				</ul>
			</a>
			<a class="item" name="section6">
				<ul>
					<li style="min-width:196px;max-width:196px;">Cierre y Conciliacion del Presupuesto - Formato C-1</li>
				</ul>
			</a>
			<a class="item" name="section7">
				<ul>
					<li style="min-width:196px;max-width:196px;">Cierre y Conciliacion del Presupuesto - Formato C-3</li>
				</ul>
			</a>
			<a class="item" name="section8">
				<ul>
					<li style="min-width:196px;max-width:196px;">Cierre y Conciliacion del Presupuesto - Formato C-4</li>
				</ul>
			</a>
			<a class="item" name="section9">
				<ul>
					<li style="min-width:196px;max-width:196px;">Cierre y Conciliacion del Presupuesto - Formato C-5</li>
				</ul>
			</a>
			<a class="item" name="section10">
				<ul>
					<li style="min-width:196px;max-width:196px;">Metas Fisicas</li>
				</ul>
			</a>
			<a class="item" name="section11">
				<ul>
					<li style="min-width:196px;max-width:196px;">PPTO: PIA, PIM y Ejecución (Grafico de Barras)</li>
				</ul>
			</a>
			<a class="item" name="section12">
				<ul>
					<li style="min-width:196px;max-width:196px;">PPTO: PIA, PIM y Ejecución (Grafico Pie)</li>
				</ul>
			</a>
			<a class="item" name="section13">
				<ul>
					<li style="min-width:196px;max-width:196px;">PPTO: Graficos Mensualizados</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset id="section1">
		<legend>Programaci&oacute;n Mensual del POI</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Plan Operativo Institucional por cada dependencia de la SBPA.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section2">
		<legend>Programaci&oacute;n Mensualizada de Ingresos y Gastos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Programaci&oacute;n mensualizada de los ingresos y gastos
				consolidado o por organizaci&oacute;n.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td colspan="2"><input type="text" name="ano"></td>
			</tr>
			<!--<tr>
				<td>Etapa</td>
				<td colspan="2"><select name="etapa">
					<option value="A">PIA</option>
					<option value="">PIM</option>
				</select></td>
			</tr>-->
			<tr>
				<td>Tipo</td>			
				<td colspan="2"><select name="tipo">
					<option value="I">Ingresos</option>
					<option value="G">Gastos</option>
				</select></td>
			</tr>
			<tr>
				<td>Fuente de Financiamiento</td>
				<td colspan="2">
					<select name="fuente"></select>
				</td>
			</tr>
			<tr>
				<td>Organizaci&oacute;n</td>
				<td><span name="orga"></span><button name="btnSelectOrga">Seleccionar</button><button name="btnLimpiar">Limpiar</button></td>
			</tr>
			<!--<tr>
				<td>Actividades/Obras</td>			
				<td colspan="2"><select name="meta">
					<option>Todos</option>
					<option value="1">Solo Actividades</option>
					<option value="2">Solo Obras</option>
				</select></td>
			</tr>-->
			<tr>
				<td>Imprimir</td>
				<td><button name="btnImprimir">Imprimir PIA</button></td>
				<td><button name="btnImprimir">Imprimir PIM</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section3">
		<legend>Estructura Programatica y Funcional (Anexo 1)</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Diagrama de la estructura program&aacute;tica y funcional.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section4">
		<legend>Programaci&oacute;n Mensualizada de Servicios Productivos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Programaci&oacute;n de Servicios Productivos.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section5">
		<legend>POI/POE - Formato 01</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Formato POI/POE.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Filtro</td>
				<td><select name="filtro">
					<option value="0">Anual</option>
					<option value="1">I Semestre</option>
					<option value="2">II Semestre</option>
					<option value="3">I Trimestre</option>
					<option value="4">II Trimestre</option>
					<option value="5">III Trimestre</option>
					<option value="6">IV Trimestre</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section6">
		<legend>Cierre y Conciliacion del Presupuesto - Formato C-1</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Cierre y Conciliacion del Presupuesto - Formato C-1.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Fuente de Financiamiento</td>
				<td><select name="fuente"></select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section7">
		<legend>Cierre y Conciliacion del Presupuesto - Formato C-3</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Cierre y Conciliacion del Presupuesto - Formato C-3.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Fuente de Financiamiento</td>
				<td><select name="fuente"></select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section8">
		<legend>Cierre y Conciliacion del Presupuesto - Formato C-4</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Cierre y Conciliacion del Presupuesto - Formato C-4.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Fuente de Financiamiento</td>
				<td><select name="fuente"></select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section9">
		<legend>Cierre y Conciliacion del Presupuesto - Formato C-5</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Cierre y Conciliacion del Presupuesto - Formato C-5.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section10">
		<legend>Metas Fisicas</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Metas Fisicas.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Etapa</td>
				<td><select name="etapa">
					<option value="P">Programacion</option>
					<option value="E">Ejecucion</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
		<legend>Cuadro Comparativo de Metas Fisicas Ejecutadas vs. Programadas</legend>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Filtro</td>
				<td><select name="filtro">
					<option value="1">Enero</option>
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
					<option value="13">Anual</option>
					<option value="14">I Trimestre</option>
					<option value="15">II Trimestre</option>
					<option value="16">III Trimestre</option>
					<option value="17">IV Trimestre</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section11">
		<legend>PPTO: PIA, PIM y Ejecución (Grafico de Barras)</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				PPTO: PIA, PIM y Ejecución (Grafico de Barras).
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Tipo</td>
				<td><select name="tipo">
					<option value="I">Ingresos</option>
					<option value="G">Gastos</option>
				</select></td>
			</tr>
			<tr>
				<td><button name="btnImprimir">Imprimir</button></td>
				<td><button name="btnImprimir2">Imprimir Por Genericas</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section12">
		<legend>PPTO: PIA, PIM y Ejecución (Grafico Pie)</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				PPTO: PIA, PIM y Ejecución (Grafico Pie).
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Etapa</td>
				<td><select name="etapa">
					<option value="PIA">PIA</option>
					<option value="PIM">PIM</option>
					<option value="EJE">Ejecucion</option>
				</select></td>
			</tr>
			<tr>
				<td>Tipo</td>
				<td><select name="tipo">
					<option value="I">Ingresos</option>
					<option value="G">Gastos</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section13">
		<legend>PPTO: Graficos Mensualizados</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				PPTO: Graficos Mensualizados.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
			</tr>
			<tr>
				<td>Etapa</td>
				<td><select name="etapa">
					<option value="PIA">PIA</option>
					<option value="PIM">PIM</option>
					<option value="EJE">Ejecucion</option>
				</select></td>
			</tr>
			<tr>
				<td>Tipo</td>
				<td><select name="tipo">
					<option value="I">Ingresos</option>
					<option value="G">Gastos</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
</div>