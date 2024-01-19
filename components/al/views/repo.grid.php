<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:196px;max-width:196px;">Diligencias Programadas</li>
				</ul>
			</a>
			<!--<a class="item" name="section2">
				<ul>
					<li style="min-width:196px;max-width:196px;">Expedientes Activos</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:196px;max-width:196px;">Expedientes Archivados</li>
				</ul>
			</a>-->
			<a class="item" name="section4">
				<ul>
					<li style="min-width:196px;max-width:196px;">Contingencias</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:196px;max-width:196px;">Convenios</li>
				</ul>
			</a>
			<a class="item" name="section6">
				<ul>
					<li style="min-width:196px;max-width:196px;">Expedientes</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset id="section1">
		<legend>Diligencias Programadas</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lista de diligencias programadas en el a&ntilde;o.
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
				<td>Diligencias</td>
				<td><select name="estado">
					<option value="P">Programadas</option>
					<option value="E">Ejecutadas</option>
					<option value="S">Suspendidas</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<!--<fieldset id="section2">
		<legend>Expedientes Activos</legend>
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
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section3">
		<legend>Expedientes Archivados</legend>
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
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>-->
	<fieldset id="section4">
		<legend>Contingencias</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lista de contingencias a favor o en contra de un periodo determinado.
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
				<td>Diligencias</td>
				<td><select name="clasificacion">
					<option value="F">A FAVOR</option>
					<option value="C">EN CONTRA</option>
				</select></td>
			</tr>
			<tr>
				<td><button name="btnImprimir">Imprimir</button></td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="section5">
		<legend>Convenios</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Lista de convenios entre la SBPA y otras entidades.
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
	<fieldset id="section6">
		<legend>Expedientes</legend>
		<legend>Descripci&oacute;n</legend>
		<table>
			<tr>
				<td>
				Listado de los expedientes legales activos o archivados.
				</td>
			</tr>
		</table>
		<legend>Par&aacute;metros</legend>
		<table>
			<tr>
				<td>Expedientes</td>
				<td><select name="archivado">
					<option value="false">Activo</option>
					<option value="true">Archivado</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Tipo</label></td>
				<td><select name="tipo">
					<option selected="selected" value="0">Todos</option>
					<option value="C">Civiles</option>
					<option value="P">Penales</option>
					<option value="A">Administrativos</option>
					<option value="L">Laborales</option>
					<option value="T">Contesioso Administrativo</option>
					<option value="S">Sucesion Intestada</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Encargado</label></td>
				<td><select name="encargado" class="">
					<option selected="selected" value="B">Beneficencia</option>
					<option value="P">Procuradoria</option>
					<option value="M">Mimdes</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Materia</label></td>
				<td><input type="text" name="materia"></td>
			</tr>
			<tr>
				<td colspan="2"><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
	</fieldset>
</div>