<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Local</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Datos Generales</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Valores</li>
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
		<legend>Local</legend>
		<table>
			<tr>
				<td><label>Nombre</label></td>
				<td><span name="spLocalNomb"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n</label></td>
				<td><span name="spLocalDirec"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td><label>Descripci&oacute;n</label></td>
				<td><input type="text" name="descr"></td>
			</tr>
			<tr>
				<td><label>Referencia</label></td>
				<td><input type="text" name="ref"></td>
			</tr>
			<tr>
				<td><label>Uso</label></td>
				<td><select name="cboUso">
					<option value="TI">Tiendas</option>
					<option value="OF">Oficina</option>
					<option value="HO">Hotel</option>
					<option value="ST">Stand</option>
					<option value="CI">Cine</option>
					<option value="ES">Espacio</option>
					<option value="CO">Cochera</option>
					<option value="VI">Casa Habitaci&oacute;n</option>
					<option value="OT">Otros</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Detalle de Uso</label></td>
				<td><input type="text" size="40" name="detalle_uso" /></td>
			</tr>
			<tr>
				<td><label>Estado de conservaci&oacute;n</label></td>
				<td><select name="cboConserv">
					<option value="B">Bueno</option>
					<option value="R">Regular</option>
					<option value="M">Malo</option>
				</select></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea del terreno</label></td>
				<td><input type="text" size="5" name="arterr"></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea construida</label></td>
				<td><input type="text" size="5" name="arcons"></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero de Medidor de Agua</label></td>
				<td><input type="text" size="8" name=medidor_agua></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero de Medidor de Luz</label></td>
				<td><input type="text" size="8" name="medidor_luz"></td>
			</tr>
			<tr>
				<td><label>C&oacute;digo de Arbitrios</label></td>
				<td><input type="text" size="10" name="cod_arbitrios"></td>
			</tr>
			<tr>
				<td><label>Habilitado</label></td>
				<td name="tdHab">
					<input type="radio" name="rbtnHab" id="rbtnHabSi" value="1" checked="checked"><label for="rbtnHabSi">Si</label>
					<input type="radio" name="rbtnHab" id="rbtnHabNo" value="0"><label for="rbtnHabNo">No</label>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3">
		<legend>Valores</legend>
		<table>
			<tr>
				<td><label>Renta</label></td>
				<td><input type="text" size="6" name="renta"></td>
			</tr>
			<tr>
				<td><label>Garant&iacute;a</label></td>
				<td><input type="text" size="6" name="garant"></td>
			</tr>
			<tr>
				<td><label>Moneda</label></td>
				<td><select name="cboMond">
					<option value="S">Nuevos Soles</option>
					<option value="D">D&oacute;lares</option>
				</select></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section4">
		<legend>Acta de Inmueble</legend>
		<button name="btnAgregar">Agregar</button>
		<div class="grid" style="width: 480px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:40px;max-width:40px;">&nbsp;</li>
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
					<li style="min-width:170px;max-width:170px"><input type="text" name="mobDescr"></li>
					<li style="min-width:90px;max-width:90px"><select name="cboMobConserv">
						<option value="B">Bueno</option>
						<option value="R">Regular</option>
						<option value="M">Malo</option>
					</select></li>
					<li style="min-width:80px;max-width:80px"><input type="text" size="4" name="mobCant"></li>
					<li style="min-width:170px;max-width:170px"><input type="text" name="mobObserv"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>