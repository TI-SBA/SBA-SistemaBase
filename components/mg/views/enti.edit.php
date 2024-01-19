<div class="ui-layout-west">
	<ul class="list">
		<li class="ui-state-highlight"><a href="section1">Datos Generales</a></li>
		<!--<li><a href="section2">Domicilios</a></li>-->
		<li><a href="section3">Tel&eacute;fonos</a></li>
		<li><a href="section4">Correo electr&oacute;nico</a></li>
		<li><a href="section5">Sitios Web</a></li>
	</ul>
</div>
<div class="ui-layout-center" style="overflow: hidden;">
	<div id="section1" style="height:400px;">
		<fieldset>
			<legend>Datos Generales</legend>
			<table>
				<tbody>
					<tr>
						<td><label>Tipo de entidad</label></td>
						<td><div id="rbtnTipo">
							<input type="radio" id="rbtnAgEntNat" value="P" name="rbtnTipo" checked="checked" /><label for="rbtnAgEntNat">Persona Natural</label>
							<input type="radio" id="rbtnAgEntJur" value="E" name="rbtnTipo" /><label for="rbtnAgEntJur">Persona Jur&iacute;dica</label>
						</div></td>
					</tr>
					<tr name="rowNat">
						<td><label>DNI</label></td>
						<td><input type="text" name="dni" size="65" maxlength="8" placeholder="Ingrese el Documento Nacional de Identidad." /></td>
					</tr>
					<tr name="rowNat">
						<td><label>Nombre</label></td>
						<td><input type="text" name="nomb" size="65" placeholder="Ingrese el nombre de la persona." /></td>
					</tr>
					<tr name="rowNat">
						<td><label>Apellido Paterno</label></td>
						<td><input type="text" name="appat" size="65" placeholder="Ingrese el apellido paterno de la persona." /></td>
					</tr>
					<tr name="rowNat">
						<td><label>Apellido Materno</label></td>
						<td><input type="text" name="apmat" size="65" placeholder="Ingrese el apellido materno de la persona." /></td>
					</tr>
					<tr name="rowJur">
						<td><label>RUC</label></td>
						<td><input type="text" name="ruc" size="65" maxlength="11" placeholder="Ingrese el Registro &Uacute;nico del Contribuyente de la empresa." /></td>
					</tr>
					<tr name="rowJur">
						<td><label>Raz&oacute;n social</label></td>
						<td><input type="text" name="rsocial" size="65" placeholder="Ingrese la raz&oacute;n social de la empresa." /></td>
					</tr>
					<!--<tr>
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
				</tr>-->
			</tbody></table>
		<!--</fieldset>
	</div>
	<div id="section2" style="height:400px;overflow-y: auto;overflow-x: hidden;">
		<fieldset>
			<legend>Domicilios</legend>-->
			<table name="row" style="display:none;">
				<tbody><tr>
					<td width="120px"><label>Descripci&oacute;n</label></td>
					<td><input name="descr" size="65" type="text"></td>
				</tr>
				<tr>
					<td><label>Direcci&oacute;n</label></td>
					<td><textarea type="text" name="direc" cols="60" rows="2"></textarea></td>
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
	<div id="section3" style="height:400px; overflow-y: auto;overflow-x: hidden;">
		<fieldset>
			<legend>Tel&eacute;fonos</legend>
			<table name="row" style="display:none">
				<tbody><tr>
					<td><input name="val" size="35" type="text"></td>
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
	<div id="section4" style="height:400px; overflow-y: auto;overflow-x: hidden;">
		<fieldset>
			<legend>Correos electr&oacute;nicos</legend>
			<table name="row" style="display:none">
				<tbody><tr>
					<td><input name="val" size="35" type="text"></td>
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
	<div id="section5" style="height:400px; overflow-y: auto;overflow-x: hidden;">
		<fieldset>
			<legend>Sitios Web</legend>
			<table name="row" style="display:none">
				<tbody><tr>
					<td><input name="val" size="35" type="text"></td>
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