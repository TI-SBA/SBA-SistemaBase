<table>
	<tbody>
		<tr>
			<td><label>Beneficiario</label></td>
			<td><span name="beneficiario"></span></td>
		</tr>
		<tr>
			<td>Motivo</td>
			<td><span name="motivo"></span></td>
		</tr>
	</tbody>
</table>
<fieldset>
<legend>Conceptos</legend>
<div id="tabs_conceptos">
    <ul>
        <li><a href="#tab_p">Pagos</a></li>
        <li><a href="#tab_d">Descuentos</a></li>
    </ul>
    <div id="tab_p">
            	<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">
						<ul style="display:block">
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:400px;max-width:400px;">Conceptos de Pago</li>
						</ul>
						<ul style="display:block">
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Distribuci&oacute;n</li>
						</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto (S/.)</li>						
					</ul>
				</div>
				</div>
				<div class="grid" style="height:250px;">
				<div class="gridBody" name="tab_p" ></div>
				</div>
    </div>
    <div id="tab_d">
    			<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Concepto de Descuento</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto (S/.)</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Beneficiario</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:250px;">
				<div class="gridBody" name="tab_d" ></div>
				<div class="hidden" style="display:none;">
								<div class="gridReference_desc">
									<ul>
										<li style="min-width:200px;max-width:200px;"></li>
										<li style="min-width:100px;max-width:100px;"><span name="monto"></span></li>
										<li style="min-width:200px;max-width:200px;"><span name="bene"></span></li>
									</ul>
								</div>
				</div>
				</div>
    </div>
</div>
<div id="afectaciones" style="display:none;"></div>
<div style="display:none;">
	<div class="ref_afec">
		<ul>
			<li></li>
			<li>Monto</li>
		</ul>
	</div>
</div>
</fieldset>
<div class="hidden" style="display:none;">
<div class="gridReference">
					<ul>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:100px;max-width:100px;"><span name="monto"></span></li>
					</ul>
				</div>
</div>