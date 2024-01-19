<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:196px;max-width:196px;">Estad&iacute;sticos</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:196px;max-width:196px;">Expedientes Tramitados</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:196px;max-width:196px;">Documentos Adjuntados</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:196px;max-width:196px;">Documentos Archivados</li>
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
				Estad&iacute;sticas basadas en el n&uacute;mero de Expedientes tramitados.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td><label>Fecha del estad&iacute;stico</label></td>
				<td><span name="fec"></span></td>
			</tr>
			<tr>
				<td><label>Responsable del estad&iacute;stico</label></td>
				<td><span name="resp"></span></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea Responsable</label></td>
				<td><span name="area"></span></td>
			</tr>
			<tr>
				<td><label>Periodo</label></td>
				<td><select name="periodo">
					<option value="1">Mensual</option>
					<option value="2">Trimestral</option>
					<option value="6">Semestral</option>
					<option value="12">Anual</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Meses de Inicio</label></td>
				<td><input type="text" name="inicio" /></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button> <button name="btnGenerar">Generar Reporte</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section2">
		<legend>Otros</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de expedientes tramitados.
				</td>
			</tr>
		</table>
		<legend>Expedientes Tramitados</legend>
		<table>
			<tr>
				<td><b>Desde</b></td>
				<td><input type="text" name="desde"></td>
			</tr>
			<tr>
				<td><b>Hasta</b></td>
				<td><input type="text" name=hasta></td>
			</tr>
			<tr>
				<td><b>Usuario</b></td>
				<td><span name="usuario"></span> <button name="btnUsu">Buscar</button></td>
			</tr>
			<tr>
				<td><b>Oficina</b></td>
				<td><span name="oficina"></span> <button name="btnOfi">Buscar</button></td>
			</tr>
			<tr>
				<td><b>Procedimiento TUPA</b></td>
				<td><span name="proc"></span> <button name="btnProc">Buscar</button></td>
			</tr>
			<tr>
				<td>Por vencimiento de plazo Seg&uacute;n TUPA</td>
				<td><span name="rbtnVenc">
					<input id="rbtnVenc_1" type="radio" name="venc" value="1"><label for="rbtnVenc_1">Si</label>
					<input id="rbtnVenc_0" type="radio" name="venc" checked="checked" value="0"><label for="rbtnVenc_0">No</label>
				</td>
			</tr>
			<tr>
				<td>Por procedimientos no atendidos</td>
				<td><span name="rbtnNoaten">
					<input id="rbtnNoaten_1" type="radio" name="noaten" value="1"><label for="rbtnNoaten_1">Si</label>
					<input id="rbtnNoaten_0" type="radio" name="noaten" checked="checked" value="0"><label for="rbtnNoaten_0">No</label>
				</span></td>
			</tr>
			<tr>
				<td>Expedientes</td>
				<td><select name="tipo">
					<option value="E">Externos</option>
					<option value="I">Internos</option>
				</select></td>
			</tr>
			<tr>
				<td>Estado</td>
				<td><select name="estado">
					<option value="">Todos</option>
					<option value="P">Pendiente</option>
					<option value="C">Concluido</option>
					<option value="V">Vencido</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section3">
		<legend>Documentos Adjuntados</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de Documentos Adjuntados a Expedientes.
				</td>
			</tr>
		</table>
		<legend>Documentos Adjuntados</legend>
		<table>
			<tr>
				<td><label>Periodo</label></td>
				<td><select name="periodo">
					<option value="1">Mensual</option>
					<!--<option value="3">Trimestral</option>-->
				</select></td>
			</tr>
			<tr>
				<td><label>Meses de Inicio</label></td>
				<td><input type="text" name="inicio" /></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section4">
		<legend>Documentos Archivados</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de expedientes archivados.
				</td>
			</tr>
		</table>
		<legend>Expedientes Archivados</legend>
		<table>
			<tr>
				<td><b>Desde</b></td>
				<td><input type="text" name="desde"></td>
			</tr>
			<tr>
				<td><b>Hasta</b></td>
				<td><input type="text" name=hasta></td>
			</tr>
			<tr>
				<td><b>Archivado</b></td>
				<td><input type="text" name=archivado value="S"></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
</div> 