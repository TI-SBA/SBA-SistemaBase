<fieldset>
	<legend>Trabajador</legend>
	<table>
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
	<legend>Beneficios Sociales</legend>
	<input name="elemento1" type="checkbox" value="1" checked="checked" disabled="disabled" /> Se incluyen Beneficios Sociales<br />
	<div name="beneficios">
		<label>De conformidad con lo dispuesto en el D.Leg. 276 y su modificatoria D.L. 25224, al recurrente le corresponde percibir el 50% de su remuneraci&oacute;n principal por cada a&ntilde;o de servicios prestados en la Sociedad de Beneficencia P&uacute;blica de Arequipa, por periodo mayor de 06 meses.</label><br />
		<table>
			<tr>
				<td width="240px">Remuneraci&oacute;n Principal (Inafecto)</td>
				<td width="170px"><span name="bene_remu"></span> / 2 X 12 a&ntilde;os = </td>
				<td><span name="bene_total"></span>
			</tr>
		</table>
	</div>
</fieldset>
<fieldset>
	<legend>Vacaciones Truncas</legend>
	<table>
		<tr>
			<td>Periodo Vacacional</td>
			<td><span name="periodo"></span></td>
		</tr>
		<tr>
			<td>Meses</td>
			<td><span name="meses"></span></td>
		</tr>
		<tr>
			<td>D&iacute;as</td>
			<td><span name="dias"></span></td>
		</tr>
	</table>
	<table>
		<tr>
			<td width="270px">&nbsp;</td>
			<td width="140px">(Referencia)</td>
			<td width="140px">&nbsp;</td>
			<td>Periodo <span name="vaca_periodo"></span></td>
		</tr>
		<tr>
			<td>Remuneraci&oacute;n Total Mensual</td>
			<td><span name="vaca_remu"></span></td>
			<td><span name="vaca_mes"></span></td>
			<td><span name="total_mes"></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><span name="vaca_dia"></span></td>
			<td><span name="total_dia"></span></td>
		</tr>
		<tr>
			<td>TOTAL VACACIONES TRUNCAS</td>
			<td>&nbsp;</td>
			<td>TOTAL</td>
			<td><span name="total_pre"></span></td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Retenciones</legend>
	<div class="gridCont">
        <div class="grid" style="width: 730px;">
			<div class="gridBody" width="730px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:370px;max-width:370px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
					<li style="min-width:60px;max-width:60px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
				</ul>
			</div>
		</div>
    </div>
    <table>
    	<tr>
    		<td width="420px">&nbsp;</td>
    		<td width="140px">&nbsp;</td>
    		<td>(-) <span name="total_desc">0</span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>Total a Pagar</td>
    		<td><span name="total"></span></td>
    	</tr>
    </table>
</fieldset>
<fieldset>
	<legend>Aportaciones Patronales</legend>
	<div class="gridCont">
        <div class="grid" style="width: 730px;">
			<div class="gridBody" width="730px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:370px;max-width:370px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
					<li style="min-width:60px;max-width:60px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
				</ul>
			</div>
		</div>
    </div>
</fieldset>