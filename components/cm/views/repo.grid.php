<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:196px;max-width:196px;">Traslados Internos y Externos</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:196px;max-width:196px;">Ficha Inventario de Mausoleo</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:196px;max-width:196px;">Trabajo de Campo-Constatación in Situ</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:196px;max-width:196px;">Relación de Comprobantes de Sepelios Atendidos</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:196px;max-width:196px;">Inventario-Estado de Trámite Mausoleos</li>
				</ul>
			</a>
			<a class="item" name="section6">
				<ul>
					<li style="min-width:196px;max-width:196px;">Estado de Deudores</li>
				</ul>
			</a>
			<a class="item" name="section7">
				<ul>
					<li style="min-width:196px;max-width:196px;">Espacios Vendidos</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset id="section1">
		<legend>Traslados Internos y Externos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lista de traslado internos y externos.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
				<td><select name="mes">
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
			</select></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section2">
		<legend>Ficha Inventario de Mausoleo</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Ficha que describe las caracter&iacute;sticas de un Mausoleo.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Mausoleo</td>
				<td><span name="espa"></span>&nbsp;<button name="btnSelEspa">Seleccionar Espacio</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section3">
		<legend>Trabajo de Campo-Constatación in Situ</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Ficha de constataci&oacute;n.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Mausoleo</td>
				<td><span name="espa"></span>&nbsp;<button name="btnSelEspa">Seleccionar Espacio</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section4">
		<legend>Relación de Comprobantes de Sepelios Atendidos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de sepelios atendidos.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
				<td><select name="mes">
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
			</select></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section5">
		<legend>Inventario-Estado de Trámite Mausoleos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de tr&aacute;mites de mausoleos.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
				<td><select name="mes">
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
			</select></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section6">
		<legend>Estado de Deudores</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Estado de Deudores
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section7">
		<legend>Espacios Vendidos</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de espacios vendidos durante un periodo.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Tipo de Espacio</td>
				<td><select name="tipo">
					<option value="N">Nicho</option>
					<option value="M">Mausoleo</option>
				</select></td>
			</tr>
			<tr>
				<td>Periodo</td>
				<td><input type="text" name="ano"></td>
				<td><select name="mes">
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
			</select></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
</div>