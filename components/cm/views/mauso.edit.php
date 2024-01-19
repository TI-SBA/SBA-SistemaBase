<table>
	<tr>
		<td width="112"><label>Capacidad:</label></td>
		<td colspan="4"><input type="text" name="capa" size="37"></td>
	</tr>
    <tr>
        <td><label>Lote:</label></td>
        <td colspan="4"><input type="text" name="lote" size="37"></td>
    </tr>
 	<tr>
 		<td><label>Cuadrante:</label></td>
 		<td colspan="4"><select name="sector">
 			<option value="A">Cuadrante A</option>
 			<option value="B">Cuadrante B</option>
 			<option value="C">Cuadrante C</option>
 			<option value="D">Cuadrante D</option>
 			<option value="E">Cuadrante E</option>
 			<option value="F">Cuadrante F</option>
 			<option value="G">Cuadrante G</option>
 		</select></td>
 	</tr>
    <tr>
        <td width="112"><label>Denominaci&oacute;n:</label></td>
        <td colspan="4"><input type="text" name="deno" size="37"></td>
    </tr>
    <tr>
        <td width="112">Tipo de Zona:</td>
        <td colspan="4">
            <div name="rbtn_zonas" class="buttonRow" style="text-align:left;">
                <input type="radio" value="N" name="rbtn_zona" id="rbtn_zona_n" checked="checked"><label for="rbtn_zona_n">Normal</label>
                <input type="radio" value="P" name="rbtn_zona" id="rbtn_zona_p"><label for="rbtn_zona_p">Preferencial</label>
                <input type="radio" value="A" name="rbtn_zona" id="rbtn_zona_a"><label for="rbtn_zona_a">A</label>
                <input type="radio" value="B" name="rbtn_zona" id="rbtn_zona_b"><label for="rbtn_zona_b">B</label>
                <input type="radio" value="C" name="rbtn_zona" id="rbtn_zona_c"><label for="rbtn_zona_c">C</label>
            </div>
        </td>
    </tr>
    <tr>
        <td width="112">Tipo de Mausoleo:</td>
        <td colspan="4">
            <div name="rbtn_tipos_mau" class="buttonRow" style="text-align:left;">
                <input type="radio" value="B" name="rbtn_tip_mau" id="rbtn_bove" checked="checked"><label for="rbtn_bove">B&oacute;veda</label>
                <input type="radio" value="C" name="rbtn_tip_mau" id="rbtn_capi"><label for="rbtn_capi">Capilla</label>
                <input type="radio" value="R" name="rbtn_tip_mau" id="rbtn_crip"><label for="rbtn_crip">Cripta</label>
            </div>
        </td>
    </tr>
    <tr>
        <td rowspan="2"  width="112"><label>Medidas(metros)</label></td>
        <td><label>Largo</label></td>
        <td><label>Ancho</label></td>
        <td><label>Altura 1</label></td>
        <td><label>Altura 2</label></td>
    </tr>
    <tr>
        <td><input type="text" name="largo" size="7"></td>
        <td><input type="text" name="ancho" size="7"></td>
        <td><input type="text" name="alt1" size="7"></td>
        <td><input type="text" name="alt2" size="7"></td>
    </tr>
    <tr>
    	<td><label>Medida Total</label></td>
    	<td colspan="4"><input type="text" name="medida_total" size="7"></td>
    </tr>
    <tr>
        <td width="112"><label>Referencia:</label></td>
        <td colspan="4"><input type="text" name="refm" size="37"></td>
    </tr>
</table>
<fieldset>
	<legend>Precios</legend>
	<table>
		<tr>
			<td><label>Concesi&oacute;n Permanente</label></td>
			<td><input type="text" name="precio" size="6"></td>
		</tr>
	</table>
</fieldset>