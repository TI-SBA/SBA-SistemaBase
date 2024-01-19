<fieldset>
	<legend>Tipo de Emisi&oacute;n</legend>
	<table>
		<tr>
			<td>
				<select name="tipo">
					<option value="CN">Cambio de Nombre en Recibo de Caja</option>
					<option value="CO">Colocacion</option>
					<option value="CS">Concesi&oacute;n</option>
					<option value="AS">Concesi&oacute;n de Nicho en Vida</option>
					<option value="CT">Construccion</option>
					<option value="CV">Conversion</option>
					<!--<option value="AD">Reducci&oacute;n de restos</option>-->
					<option value="IN">Inhumacion</option>
					<option value="TE">Traslado Externo</option>
					<option value="TEO">Traslado Externo (Desde Otro Cementerio)</option>
					<option value="TI">Traslado Interno</option>
					<option value="TP">Traspaso</option>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
<fieldset id="CS" style="display:none;">
	<legend>Datos de Concesi&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Condici&oacute;n</label></td>
			<td><textarea name="cond" cols="35"></textarea></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="CT" style="display:none;">
	<legend>Datos de Construcci&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Capacidad</label></td>
			<td><input type="text" name="capa" size="20"></td>
		</tr>
		<tr>
			<td><label>Largo</label></td>
			<td><input type="text" name="larg" size="20"></td>
		</tr>
		<tr>
			<td><label>Ancho</label></td>
			<td><input type="text" name="anch" size="20"></td>
		</tr>
		<tr>
			<td><label>Altura 1</label></td>
			<td><input type="text" name="alt1" size="20"></td>
		</tr>
		<tr>
			<td><label>Altura 2</label></td>
			<td><input type="text" name="alt2" size="20"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="AS" style="display:none;">
	<legend>Datos de Nicho en Vida</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante 1</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI Ocupante 1</label></td>
			<td><input type="text" name="dniocup" size="11"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="AD" style="display:none;">
	<legend>Datos de Adjuntaci&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="TP" style="display:none;">
	<legend>Datos de Traspaso</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Nuevo Propietario</label></td>
			<td><input type="text" name="new_prop" size="38"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="IN" style="display:none;">
	<legend>Datos de Inhumaci&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td colspan="3"><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td colspan="3"><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td colspan="3"><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td colspan="3"><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Empaste</label></td>
			<td colspan="3"><select name="empaste">
				<option value="0">No</option>
				<option value="1">Si</option>
			</select></td>
		</tr>
		<tr>
			<td><label>Temporalidad</label></td>
			<td><select name="temporalidad">
				<option value="P">Permanente</option>
				<option value="T">Temporal</option>
			</select></td>
			<td><label>Fecha de Vencimiento</label></td>
			<td><input type="text" name="fecven" size="20"></td>
		</tr>
		<tr>
			<td><label>Funeraria</label></td>
			<td colspan="3"><input type="text" name="fune" size="38"></td>
		</tr>
		<tr>
			<td><label>Fecha de Ejecuci&oacute;n de Entierro</label></td>
			<td colspan="3"><input type="text" name="feceje" size="20"></td>
		</tr>
		<tr>
			<td><label>Reducci&oacute;n de</label></td>
			<td colspan="3"><input type="text" name="ocup2" size="38"></td>
		</tr>
		<!--<tr>
			<td><label>DNI Ocupante 2</label></td>
			<td><input type="text" name="dniocup2" size="11"></td>
		</tr>-->
		<tr>
			<td><label>Ocupante</label></td>
			<td colspan="3"><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI Ocupante</label></td>
			<td colspan="3"><input type="text" name="dniocup" size="11"></td>
		</tr>
		<tr>
			<td><label>Partida de Defunci&oacute;n</label></td>
			<td colspan="3"><input type="text" name="part" size="38"></td>
		</tr>
		<tr>
			<td><label>Municipalidad</label></td>
			<td colspan="3"><input type="text" name="muni" size="38"></td>
		</tr>
		<tr>
			<td><label>Edad</label></td>
			<td colspan="3"><input type="text" name="edad" size="20"></td>
		</tr>
		<tr>
			<td><label>Causa</label></td>
			<td colspan="3"><input type="text" name="causa" size="38"></td>
		</tr>
		<tr>
			<td><label>Fecha de Defunci&oacute;n</label></td>
			<td colspan="3"><input type="text" name="fecdef" size="20"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="TI" style="display:none;">
	<legend>Datos de Traslado Interno</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Espacio Destino</label></td>
			<td><span name="espacio_dest"></span><button name="btnDest">Espacio</button></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="TE" style="display:none;">
	<legend>Datos de Traslado Externo</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Cementerio</label></td>
			<td><input type="text" name="ceme" size="38"></td>
		</tr>
		<tr>
			<td><label>Ubicaci&oacute;n</label></td>
			<td><input type="text" name="ubic" size="38"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="CO" style="display:none;">
	<legend>Datos de Colocaci&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Ocupante 2</label></td>
			<td><input type="text" name="ocup2" size="38"></td>
		</tr>
		<tr>
			<td><label>Accesorios</label></td>
			<td><textarea name="acce" cols="35"></textarea></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="CV" style="display:none;">
	<legend>Datos de Conversi&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="TEO" style="display:none;">
	<legend>Datos de Conversi&oacute;n</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>Cementerio de Origen</label></td>
			<td><input type="text" name="cement_orig" size="38"></td>
		</tr>
		<tr>
			<td><label>Ubicacion de Origen</label></td>
			<td><input type="text" name="ubic_orig" size="38"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>
<fieldset id="CN" style="display:none;">
	<legend>Datos de Cambio de Nombre en Recibo de Caja</legend>
	<table>
		<tr>
			<td><label>Recibo</label></td>
			<td><input type="text" name="recibo_origen" size="38"></td>
		</tr>
		<tr>
			<td><label>Nuevo Recibo Generado para Cambio de Nombre</label></td>
			<td><input type="text" name="recibo" size="10"></td>
		</tr>
		<tr>
			<td><label>Fecha de Emisi&oacute;n</label></td>
			<td><input type="text" name="fecoper" size="20"></td>
		</tr>
		<tr>
			<td><label>Referencia</label></td>
			<td><input type="text" name="referencia" size="30"></td>
		</tr>
		<tr>
			<td><label>Propietario</label></td>
			<td><input type="text" name="prop" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Propietario</label></td>
			<td><input type="text" name="dni_prop" size="20"></td>
		</tr>
		<tr>
			<td><label>Ocupante</label></td>
			<td><input type="text" name="ocup" size="38"></td>
		</tr>
		<tr>
			<td><label>DNI/RUC Ocupante</label></td>
			<td><input type="text" name="dni_ocup" size="20"></td>
		</tr>
		<tr>
			<td><label>Archivo</label></td>
			<td><input type="text" name="url_imagen" size="38"></td>
		</tr>
	</table>
</fieldset>