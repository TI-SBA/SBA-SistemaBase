<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section11">
				<ul>
					<li style="min-width:196px;max-width:196px;">Recibo de Ingresos</li>
				</ul>
			</a>
			<a class="item" name="section12">
				<ul>
					<li style="min-width:196px;max-width:196px;">Registro de Ventas</li>
				</ul>
			</a>
			<!-- <a class="item" name="section1">
				<ul>
					<li style="min-width:196px;max-width:196px;">Deudores - Venta de Nichos y Regularizaciones</li>				
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:196px;max-width:196px;">Deudores - Analisis del Cementerio General</li>
				</ul>
			</a>-->
			<a class="item" name="section3">
				<ul>
					<li style="min-width:196px;max-width:196px;">Nichos en Vida</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:196px;max-width:196px;">Información de DAOT - Cementerio General</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:196px;max-width:196px;">Información de DAOT - Inmuebles</li>
				</ul>
			</a>
			<a class="item" name="section7">
				<ul>
					<li style="min-width:196px;max-width:196px;">Gráfico de Ingresos - Inmuebles</li>
				</ul>
			</a>
			<!--<a class="item" name="section6">
				<ul>	
					<li style="min-width:196px;max-width:196px;">Planilla de Recibo de Ingresos de Inmuebles</li>
				</ul>
			</a>			
			<a class="item" name="section8">
				<ul>
					<li style="min-width:196px;max-width:196px;">Record de Pagos</li>
				</ul>
			</a>-->
			<a class="item" name="section9">
				<ul>
					<li style="min-width:196px;max-width:196px;">Estado de Deudores</li>
				</ul>
			</a>
			<a class="item" name="section10">
				<ul>
					<li style="min-width:196px;max-width:196px;">Control de la Deuda</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset id="section11">
		<legend>Recibo de Ingresos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Impresi&oacute;n de Recibo de Ingresos
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>D&iacute;a</td>
				<td><span name="recibo" style="color:green;"></span>&nbsp;<button name="btnRec">Elegir Recibo</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section12">
		<legend>Registro de Ventas</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Impresi&oacute;n de Registro de Ventas Mensual
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="periodo"></td>
			</tr>
			<tr>
				<td>Organizaci&oacute;n</td>
				<td><span name="orga" style="color:green;"></span>&nbsp;<button name="btnOrga">Elegir Organizaci&oacute;n</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section3">
		<legend>Nichos en Vida</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lista de nichos otorgados en vida.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
				<td>
					<select name="mes">
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
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section4">
		<legend>Información de DAOT - Cementerio General</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Declaraci&oacute;n anual de operaciones con terceros: Cementerio General.
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
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section5">
		<legend>Información de DAOT - Inmuebles</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Declaraci&oacute;n anual de operaciones con terceros: Inmuebles.
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
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section7">
		<legend>Gráfico de Ingresos - Inmuebles</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Grafic&oacute;s que muestran los ingresos de la actividad de Inmuebles.
				</td>
			</tr>
		</table>
		<legend>Ingresos Anuales</legend>
		<table>
		<tr>
			<td>Periodo</td>
			<td><input type="text" name="ano"></td>
		</tr>
		<tr>
			<td colspan="2"><button name="btnImprimir1">Imprimir</button></td>
		</tr>
		</table>
			<legend>Ingresos Comparativos Anuales</legend>
			<table>
		<tr>
			<td>A&ntilde;o 1</td>
			<td><input type="text" name="ano1"></td>
		</tr>
		<tr>
			<td>A&ntilde;o 2</td>
			<td><input type="text" name="ano2"></td>
		</tr>
		<tr>
			<td colspan="2"><button name="btnImprimir2">Imprimir</button></td>
		</tr>
		</table>	
	</fieldset>
	<fieldset id="section9">
		<legend>Estado de Deudores</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lorem ipsum onsectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt
				ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis
				nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo
				consequat.
				</td>
			</tr>
		</table>
		<legend>Parametros</legend>
		<table>
			<tr>
				<td>Organizaci&oacute;n</td>
				<td><span name="orga"></span> <button name="btnSelect">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>	
	</fieldset>
	<fieldset id="section10">
		<legend>Control de la Deuda - Inmuebles</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lorem ipsum onsectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt
				ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis
				nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo
				consequat.
				</td>
			</tr>
		</table>
		<legend>Parametros</legend>
		<table>
			<tr>
				<td>Espaicio</td>
				<td><span name="espa"></span> <button name="btnSelectEspa">Seleccionar</button></td>
			</tr>
			<tr>
				<td>Arrendatario</td>
				<td><span name="arre"></span> <button name="btnSelectArre">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>	
	</fieldset>
</div>