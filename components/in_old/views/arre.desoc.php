<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Arrendatario</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Inmueble</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Desocupaci&oacute;n</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Acta de Inmueble</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Arrendatario</legend>
		<table>
			<tr>
				<td width="80"><label>Arrendatario:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>DNI/RUC:</label></td>
				<td><span name="dni"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Inmueble</legend>
		<table>
			<tr>
				<td width="130"><label>Inmueble Matriz:</label></td>
				<td><span name="local"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
			<tr>
				<td><label>Descripci&oacute;n:</label></td>
				<td><span name="descr"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3">
		<legend>Desocupaci&oacute;n</legend>
		<table>
			<tr>
				<td width="170px"><label>Motivo</label></td>
				<td><select name="cboMotivo">
					<option value="FC">Fin del Contrato</option>
					<option value="DS">Desalojo</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Conservaci&oacute;n del Inmueble</label></td>
				<td><select name="cboConserv">
					<option value="B">Bueno</option>
					<option value="R">Regular</option>
					<option value="M">Malo</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Fecha de desocupaci&oacute;n</label></td>
				<td><input type="text" name="fecdes"></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td><input type="text" name="observ"></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section4">
		<legend>Acta de Inmueble</legend>
		<button name="btnAgregar">Agregar</button>
		<div class="grid" style="width: 480px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:40px;max-width:40px;"></li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Descripci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">Conservaci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:80px;max-width:80px;">Cantidad</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Observaciones</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 480px;height: 140px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:40px;max-width:40px"><button name="btnEli">Eliminar</button></li>
					<li style="min-width:170px;max-width:170px"><input type="text" name="descr"></li>
					<li style="min-width:90px;max-width:90px"><select name="conserv">
						<option value="B">Bueno</option>
						<option value="R">Regular</option>
						<option value="M">Malo</option>
					</select></li>
					<li style="min-width:80px;max-width:80px"><input type="text" size="4" name="cant"></li>
					<li style="min-width:170px;max-width:170px"><input type="text" name="observ"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>