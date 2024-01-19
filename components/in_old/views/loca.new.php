<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Informaci&oacute;n General</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Caracter&iacute;sticas</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Registros P&uacute;blicos</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Informaci&oacute;n General</legend>
		<table>
			<tr>
				<td><label>Imagen</label></td>
				<td><div class="picture-box" style="width: 270px;height: 220px;padding: 0px;margin: 2px;">
					<div style="display: none;" class="changepicture">
						<input type="hidden" name="foto">
						<span>Cambiar imagen</span>
					</div>
					<img width="270" height="220" src="images/cm-upload-big_img.png" class="img-picture">
				</div>
				<div id="btnLocaInUpload"></div></td>
			</tr>
			<tr>
				<td><label>Tipo de Local</label></td>
				<td><select name="cboTipoLocal">
					<option value="CH">Complejo Habitacional</option>
					<option value="ED">Edificio</option>
					<option value="PG">Programa</option>
					<option value="OT">Otros</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Nombre</label></td>
				<td><input type="text" name="nomb"></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n</label></td>
				<td><input type="text" name="direc"></td>
			</tr>
			<tr>
				<td><label>Distrito</label></td>
				<td><input type="text" name="distrito"></td>
			</tr>
			<tr>
				<td><label>Provincia</label></td>
				<td><input type="text" name="provincia"></td>
			</tr>
			<tr>
				<td><label>Departamento</label></td>
				<td><input type="text" name="dpto"></td>
			</tr>
			<tr>
				<td><label>Referencia</label></td>
				<td><input type="text" name="ref"></td>
			</tr>
			<tr>
				<td><label>Habilitado</label></td>
				<td name="tdHab">
					<input type="radio" name="rbtnHab" id="rbtnHabSi" value="1" checked="checked"><label for="rbtnHabSi">Si</label>
					<input type="radio" name="rbtnHab" id="rbtnHabNo" value="0"><label for="rbtnHabNo">No</label>
				</td>
			</tr>
			<tr>
				<td><label>Propietario</label></td>
				<td><span name="prop"></span>&nbsp;<button name="btnSelProp">Seleccionar</button>&nbsp;<button name="btnAgrProp">Agregar</button></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td><input type="text" name="observ"></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Caracter&iacute;sticas</legend>
		<table>
			<tr>
				<td><label>Estado de conservaci&oacute;n</label></td>
				<td><select name="cboConser">
					<option value="B">Bueno</option>
					<option value="R">Regular</option>
					<option value="M">Malo</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Antig&uuml;edad</label></td>
				<td><input type="text" size="4" name="antig"><label>&nbsp;A&ntilde;os</label></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea del terreno</label></td>
				<td><input type="text" size="12" name="arterr"></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea construida</label></td>
				<td><input type="text" size="12" name="arcons"></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section3">
		<legend>Registros P&uacute;blicos</legend>
		<table>
			<tr>
				<td><label>Ficha Registral</label></td>
				<td><input type="text" name="ficreg"></td>
			</tr>
			<tr>
				<td><label>Partida Registral</label></td>
				<td><input type="text" name="parreg"></td>
			</tr>
			<tr>
				<td><label>Valor Autovaluo</label></td>
				<td><input type="text" name="autovaluo"></td>
			</tr>
		</table>
	</fieldset>
</div>