<div class="ui-layout-west">
	<div class="grid">
		<div style="line-height: 30px;height: 30px;text-align: center;">
			<button name="btnGuardar">Guardar</button>
		</div>
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
			<ul><li style="text-align: center;min-width: 164px;max-width: 164px;">Saltar a</li></ul>
		</div>
		<div class="gridBody">
			<a class="item" href="section1"><ul class="ui-state-highlight"><li style="min-width: 164px;max-width: 164px;">Datos Generales</li></ul></a>
			<a class="item" href="section2"><ul><li style="min-width: 164px;max-width: 164px;">Locales</li></ul></a>
			<a class="item" href="section3"><ul><li style="min-width: 164px;max-width: 164px;">Tel&eacute;fonos</li></ul></a>
			<a class="item" href="section4"><ul><li style="min-width: 164px;max-width: 164px;">Correo electr&oacute;nico</li></ul></a>
			<a class="item" href="section5"><ul><li style="min-width: 164px;max-width: 164px;">Sitios Web</li></ul></a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<div id="section1">
		<fieldset>
			<legend>Datos Generales</legend>
			<table>
				<tbody><tr>
					<td width="120px"><label>RUC</label></td>
					<td><input name="ruc" size="75" maxlength="11" type="text"></td>
				</tr>
				<tr>
					<td><label>Raz&oacute;n social</label></td>
					<td><input name="nomb" size="75" type="text"></td>
				</tr>
				<tr>
          <td>Imagen</td>
          <td>
            <div class="picture-box">
              <div style="display: none;" class="changepicture">
                <input type="hidden" name="foto">
                <span>Cambiar imagen</span>
              </div>
              <img width="150" height="150" src="images/admin.png" class="img-picture">
            </div>
            <div id="buttonUpload"></div>
          </td>
				</tr>
			</tbody></table>
		</fieldset>
	</div>
	<div id="section2">
		<fieldset>
			<legend>Locales</legend>
			<table name="row" style="display:none;">
				<tbody><tr>
					<td width="120px"><label>Descripci&oacute;n</label></td>
					<td><input name="descr" size="75" type="text"></td>
				</tr>
				<tr>
					<td><label>Direcci&oacute;n</label></td>
					<td><textarea type="text" name="direc" cols="70" rows="2"></textarea></td>
				</tr>
				<tr>
					<td colspan="4">
						<button name="btnAgregar">Agregar</button>
						<button name="btnEliminar">Eliminar</button>
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div id="section3">
		<fieldset>
			<legend>Tel&eacute;fonos</legend>
			<table name="row" style="display:none">
				<tbody><tr>
					<td><input name="val" size="50" type="text"></td>
					<td><select name="descr" class="editableSelect">
						<option value="telf">Tel&eacute;fono fijo</option>
						<option value="movil">M&oacute;vil</option>
						<option value="fax">Fax</option>
					</select></td>
					<td><button name="btnEliminar">Eliminar</button></td>
				</tr>
				<tr>
					<td colspan="3" name="tdBtn">
						<button name="btnAgregar">Agregar</button>
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div id="section4">
		<fieldset>
			<legend>Correos electr&oacute;nicos</legend>
			<table name="row" style="display:none">
				<tbody><tr>
					<td><input name="val" size="50" type="text"></td>
					<td><select name="descr" class="editableSelect">
						<option value="Personal">Personal</option>
						<option value="Trabajo">Trabajo</option>
						<option value="Otro">Otro</option>
					</select></td>
					<td><button name="btnEliminar">Eliminar</button></td>
				</tr>
				<tr>
					<td colspan="3" name="tdBtn">
						<button name="btnAgregar">Agregar</button>
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div id="section5">
		<fieldset>
			<legend>Sitios Web</legend>
			<table name="row" style="display:none">
				<tbody><tr>
					<td><input name="val" size="50" type="text"></td>
					<td><select name="descr" class="editableSelect">
						<option value="Personal">Personal</option>
						<option value="Trabajo">Trabajo</option>
						<option value="Otro">Otro</option>
					</select></td>
					<td><button name="btnEliminar">Eliminar</button></td>
				</tr>
				<tr>
					<td colspan="3" name="tdBtn">
						<button name="btnAgregar">Agregar</button>
					</td>
				</tr>
			</tbody></table>
		</fieldset>
	</div>
</div>