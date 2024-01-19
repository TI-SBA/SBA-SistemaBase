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
	<legend>Remuneraci&oacute;n</legend>
    <table>
    	<tr>
        	<td><label>Periodo</label></td>
            <td><span name="periodo"></span></td>
        </tr>
    	<tr>
        	<td><label>Fecha</label></td>
            <td>Desde: <span name="ini"></span>&nbsp;Hasta: <span name="fin"></span></td>
        </tr>
    </table>
    <div name="tabs">
	    <ul>
	        <li><a href="#tabs-1">Pagos</a></li>
	        <li><a href="#tabs-2">Descuentos</a></li>
	        <li><a href="#tabs-3">Aportes</a></li>
	    </ul>
	    <div id="tabs-1" class="gridCont">
	        <div class="grid" style="overflow: hidden;width: 600px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Concepto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Glosa</li>
					</ul>
				</div>
			</div>
	        <div class="grid" style="height: 300px;width: 600px;">
				<div class="gridBody"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:400px;max-width:400px;"></li>
						<li style="min-width:100px;max-width:100px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
					</ul>
				</div>
			</div>
	    </div>
	    <div id="tabs-2" class="gridCont">
	        <div class="grid" style="overflow: hidden;width: 600px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Concepto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Glosa</li>
					</ul>
				</div>
			</div>
	        <div class="grid" style="height: 300px;width: 600px;">
				<div class="gridBody"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:400px;max-width:400px;"></li>
						<li style="min-width:100px;max-width:100px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
					</ul>
				</div>
			</div>
	    </div>
	    <div id="tabs-3" class="gridCont">
	        <div class="grid" style="overflow: hidden;width: 600px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Concepto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Glosa</li>
					</ul>
				</div>
			</div>
	        <div class="grid" style="height: 300px;width: 600px;">
				<div class="gridBody"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:400px;max-width:400px;"></li>
						<li style="min-width:100px;max-width:100px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
					</ul>
				</div>
			</div>
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