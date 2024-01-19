<fieldset>
	<legend>Trabajador</legend>
	<table>
		<tr>
			<td colspan="3"><button name="btnSelEnt">Seleccionar</button></td>
		</tr>
		<tr>
			<td rowspan="6"><img name="foto" width="100" height="100"></td>
			<td><label>Nombre</label></td>
			<td><div name="nomb" class="ellipsis-text" style="width: 170px;"></div></td>
			<td><label>DNI</label></td>
			<td><span name="dni"></span></td>
		</tr>
		<tr>
			<td><label>Organizaci&oacute;n</label></td>
			<td><div name="orga" class="ellipsis-text" style="width: 170px;"></div></td>
			<td><label>Cargo</label></td>
			<td><span name="cargo"></span></td>
		</tr>
		<tr>
			<td><label>Actividad</label></td>
			<td><span name="actividad">--</span></td>
			<td><label>Componente</label></td>
			<td><span name="componente">--</span></td>
		</tr>
		<tr>
			<td><label>Nivel Remunerativo</label></td>
			<td><span name="nivel"></span></td>
			<td><label>Carnet de ESSALUD</label></td>
			<td><span name="essalud"></span></td>
		</tr>
		<tr>
			<td><label>Sistema de Pensi&oacute;n</label></td>
			<td><span name="pension"></span></td>
			<td><label>C.U.I.</label></td>
			<td><span name="cod_aportante"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Remuneraci&oacute;n</legend>
    <table>
    	<tr>
        	<td><label>Periodo</label></td>
            <td><input type="text" name="periodo"></td>
        </tr>
    	<tr>
        	<td><label>Fecha</label></td>
            <td>Desde: <input type="text" name="ini" size="10">&nbsp;Hasta: <input type="text" name="fin" size="10"></td>
        </tr>
        <tr>
        	<td>Vacaciones</td>
        	<td><input type="checkbox" name="vacaciones" /></td>
        </tr>
        <tr>
        	<td>D&iacute;as Trabajados</td>
        	<td><input type="text" name="dias_trab" /></td>
        </tr>
    </table>
    <div name="tabs">
	    <ul>
	        <li><a href="#tabs-1">Pagos</a></li>
	        <li><a href="#tabs-2">Descuentos</a></li>
	        <li><a href="#tabs-3">Aportes</a></li>
	    </ul>
	    <div id="tabs-1" class="gridCont">
	    	<div name="gridPag"></div>
	    </div>
	    <div id="tabs-2" class="gridCont">
	    	<div name="gridDes"></div>
	    </div>
	    <div id="tabs-3" class="gridCont">
	    	<div name="gridApo"></div>
	    </div>
	</div>
</fieldset>
<fieldset>
	<legend>A pagar</legend>
    <table>
    	<tr>
        	<td width="40px"><label>Neto</label></td>
            <td width="110px"><span name="neto"></span></td>
        	<td width="80px"><label>Redondeo</label></td>
            <td width="110px"><span name="redondeo">S/.0.00</span></td>
        	<td width="70px"><label>Neto a pagar</label></td>
            <td><span name="neto_pagar"></span></td>
        </tr>
    </table>
</fieldset>