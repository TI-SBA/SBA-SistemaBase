<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:196px;max-width:196px;">Ficha de Asignacion de bienes en uso</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:196px;max-width:196px;">Depreciación Acumulada (Ajustes de los Estados Financieros)</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:196px;max-width:196px;">Inventario Físico de Activos Fijos (Formato 14)</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:196px;max-width:196px;">Resumen por Partidas - Movimiento</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:196px;max-width:196px;">Lista de Saldos Mensualizados por Partida</li>
				</ul>
			</a>
			<!--<a class="item" name="section6">
				<ul>
					<li style="min-width:196px;max-width:196px;">Saldo de Movimientos por Salidas Kardex</li>
				</ul>
			</a>-->
			<a class="item" name="section7">
				<ul>
					<li style="min-width:196px;max-width:196px;">Servicio Telef&oacute;nico</li>
				</ul>
			</a>
			<a class="item" name="section8">
				<ul>
					<li style="min-width:196px;max-width:196px;">DAOT</li>
				</ul>
			</a>
			<a class="item" name="section9">
				<ul>
					<li style="min-width:196px;max-width:196px;">Movimientos por Dependencia</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset id="section1">
		<legend>Ficha de Asignacion de bienes en uso</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Bienes asignados a un trabajador de la instituci&oacute;n.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td style="width:120px;"><label>Trabajador</label></td>
				<td><label name="trab_nomb"></label><input type="hidden" name="trab">&nbsp;<button name="btnTrab">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section2">
		<legend>Depreciación Acumulada (Ajustes de los Estados Financieros)</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Depreciaci&oacute;n acumulada de los bienes.
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
	<fieldset id="section3">
		<legend>Inventario Físico de Activos Fijos (Formato 14)</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Inventario de productos.
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
				<td>Cuenta Contable</td>
				<td colspan="2"><label name="cuen_cod"></label><input type="hidden" name="cuen">&nbsp;<button name="btnCuen">Seleccionar</button></td>
			</tr>
			<tr>
				<td>Organizacion</td>
				<td colspan="2"><label name="orga_nomb"></label><input type="hidden" name="orga">&nbsp;<button name="btnOrga">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section4">
		<legend>Resumen - Movimiento</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Resumen - Movimiento.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Saldo</td>
				<td><select name="saldo"></select></td>
			</tr>
			<tr>
				<td>Almac&eacute;n</td>
				<td><span name="almc"></span>&nbsp;<button name="btnSel">Seleccionar</button></td>
			</tr>
			<tr>
				<td>Partidas o Cuentas</td>
				<td><select name="tipo">
					<option value="CU">Cuentas Contables</option>
					<option value="CL">Partidas Presupuestales</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section5">
		<legend>Listado de Saldos Mensualizados por Partida</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de Saldos Mensualizados por Partida
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Saldo</td>
				<td><select name="saldo"></select></td>
			</tr>
			<tr>
				<td>Almac&eacute;n</td>
				<td colspan="2"><span name="almc"></span>&nbsp;<button name="btnSel">Seleccionar</button></td>
			</tr>
			<tr>
				<td>Tipo</td>
				<td><select name="tipo">
					<option value="0">F&iacute;sico</option>
					<option value="1">Valorizado</option>
					<option value="2">Ambos</option>
				</select></td>
			</tr>
			<tr>
				<td>Cuenta</td>
				<td colspan="2"><span name="cuenta"></span>&nbsp;<button name="btnSel">Seleccionar</button></td>
			</tr>
			<tr>
				<td>Partida</td>
				<td colspan="2"><span name="clasif"></span>&nbsp;<button name="btnSel">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<!--<fieldset id="section6">
		<legend>Saldo de Movimientos por Salidas Kardex</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Saldo de Movimientos por Salidas Kardex.
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
				<td>Almac&eacute;n</td>
				<td colspan="2"><span name="almc"></span>&nbsp;<button name="btnSel">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>-->
	<fieldset id="section7">
		<legend>Servicio Telef&oacute;nico</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Servicio Telef&oacute;nico.
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
						<option value="0">Todos</option>
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
				<td>Organizaci&oacute;n</td>
				<td colspan="2"><span name="orga"></span>&nbsp;<button name="btnSelect">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section8">
		<legend>DAOT</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				DECLARACION ANUAL DE OPERACIONES CON TERCEROS - LOGISTICA.
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
				<td colspan="2"><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section9">
		<legend>Movimientos por Dependencia</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Movimientos por Dependencia.
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
						<option value="0">Todos</option>
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
				<td>Organizaci&oacute;n</td>
				<td colspan="2"><span name="orga"></span>&nbsp;<button name="btnSelect">Seleccionar</button></td>
			</tr>
			<tr>
				<td>Almac&eacute;n</td>
				<td colspan="2"><span name="almc"></span>&nbsp;<button name="btnSel">Seleccionar</button></td>
			</tr>
			<tr>
				<td colspan="3"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
</div>