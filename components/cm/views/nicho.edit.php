<table cellpadding="10">
    <tr>
        <td width="112"><label>Capacidad:</label></td>
        <td><input type="text" name="capa" size="37"></td>
    </tr>
    <tr>
        <td><label>Fila:</label></td>
        <td><input type="text" name="nomb" size="37"></td>
    </tr>
    <tr>
        <td><label>Piso:</label></td>
        <td><input type="text" name="pisos" size="37"></td>
    </tr>
    <tr>
        <td><label>N&uacute;mero:</label></td>
        <td><input type="text" name="num" size="37"></td>
    </tr>
    <tr>
        <td>Tipo:</td>
        <td>
            <div name="rbtn_tipos" class="buttonRow" style="text-align:left;">
                <input type="radio" value="N" name="rbtn_tipo" id="rbtn_norma" checked="checked"><label for="rbtn_norma">Normal</label>
                <input type="radio" value="P" name="rbtn_tipo" id="rbtn_parvu"><label for="rbtn_parvu">P&aacute;rvulo</label>
            </div>
        </td>
    </tr>
    <tr>
        <td><label>Pabell&oacute;n:</label></td>
        <td><span name="pabellon"></span><button name="btnPabellon">Seleccionar</button></td>
    </tr>
    <tr>
        <td><label>Cuadrante:</label></td>
        <td><span name="sector"></span></td>
    </tr>
    <tr>
        <td><label>Costo:</label></td>
        <td><input type="text" name="precio" size="6"></td>
    </tr>
</table>
<fieldset>
    <legend>Precios</legend>
    <table>
        <tr>
            <td width="112"><label>Concesi&oacute;n Temporal</label></td>
            <td><input type="text" name="precio_temp" size="6"></td>
        </tr>
        <tr>
            <td><label>Concesi&oacute;n Permanente</label></td>
            <td><input type="text" name="precio_perp" size="6"></td>
        </tr>
        <tr>
            <td><label>Concesi&oacute;n en vida</label></td>
            <td><input type="text" name="precio_vid" size="6"></td>
        </tr>
    </table>
</fieldset>