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
	<legend>Referencias</legend>
	<div class="gridCont">
		<label>Subsidiados por ESSALUD dentro del mes</label>
        <div class="grid" style="width: 740px;">
			<div class="gridBody" width="740px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:440px;max-width:440px;"></li>
					<li style="min-width:60px;max-width:60px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
				</ul>
			</div>
		</div>
    </div>
</fieldset>
<fieldset>
	<legend>C&aacute;lculo del Subsidio</legend>
	<div class="gridCont">
		<label>12 &uacute;ltimas remuneraciones</label>
        <div class="grid" style="overflow: hidden;width: 310px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Periodo</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Valor</li>
				</ul>
			</div>
		</div>
        <div class="grid" style="height: 325px;width: 310px;">
			<div class="gridBody" width="300px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:150px;max-width:150px;"></li>
				</ul>
			</div>
		</div>
    </div>
    <table>
    	<tr>
    		<td>Promedio Diario = </td>
    		<td><span name="total_remus"></span>/<span name="divi_1"></span> = </td>
    		<td><span name="total_remus_div"></span>/<span name="divi_2"></span> = </td>
    		<td><span name="total_dia"></span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>Subsidio Diario</td>
    		<td>D&iacute;as Subsidiados</td>
    		<td>Total Subsidiado</td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td><span name="subs_dia"></span> x </td>
    		<td><span name="subs_dias"></span> = </td>
    		<td><span name="subs_total"></span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>Subtotal</td>
    		<td><span name="subs_total_prev"></span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>Por redondeo</td>
    		<td><span name="redondeo"></span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>Total</td>
    		<td><span name="total"></span></td>
    	</tr>
    	<tr>
    		<td colspan="4">Descuentos</td>
    	</tr>
    	<tr>
    		<td colspan="4">
	    		<div class="gridCont">
			        <div class="grid" style="width: 740px;">
						<div class="gridBody" width="740px"></div>
						<div class="gridReference">
							<ul>
								<li style="min-width:440px;max-width:440px;"></li>
								<li style="min-width:60px;max-width:60px;"></li>
								<li style="min-width:70px;max-width:70px;"></li>
							</ul>
						</div>
					</div>
				</div>
    		</td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>TOTAL</td>
    		<td><span name="total_pagar_prev"></span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>Por redondeo</td>
    		<td><span name="redondeo_prev"></span></td>
    	</tr>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>TOTAL A PAGAR</td>
    		<td><span name="total_pagar"></span></td>
    	</tr>
    </table>
</fieldset>