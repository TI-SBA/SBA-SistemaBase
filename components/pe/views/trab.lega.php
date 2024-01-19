<ul>
	<li><a href="#tabs-1">Vacaciones</a></li>
	<li><a href="#tabs-2">Licencias</a></li>
	<li><a href="#tabs-3">M&eacute;ritos</a></li>
	<li><a href="#tabs-4">Dem&eacute;ritos</a></li>
	<li><a href="#tabs-5">Comisiones</a></li>
	<li><a href="#tabs-6">Declaraciones</a></li>
</ul>
<div id="tabs-1"></div>
<div id="tabs-2"></div>
<div id="tabs-3"></div>
<div id="tabs-4"></div>
<div id="tabs-5"></div>
<div id="tabs-6"></div>
<div name="clone" style="display: none;">
	<fieldset>
		<legend></legend>
		<table>
			<tr>
				<td><label>Descripci&oacute;n</label></td>
				<td><span name="descr"></span></td>
			</tr>
			<tr>
				<td style="text-align: rigth;" colspan="2"><button name="btnEli">Borrar</button></td>
			</tr>
		</table>
		<hr>
	</fieldset>
</div>
<div name="init" style="display: none;">
	<table>
		<tr>
			<td><label>Fecha</label></td>
			<td><input type="text" size="14" name="fecha"></td>
		</tr>
		<tr>
			<td><label>Descripci&oacute;n</label></td>
			<td><textarea name="descr" cols="25" rows="2"></textarea></td>
		</tr>
	</table>
	<hr>
	<div style="height: 190px;width: 450px;overflow: auto;" name="cont"></div>
</div>