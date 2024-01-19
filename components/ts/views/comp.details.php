<table>
	<tbody>
		<tr>
			<td colspan="2"><b><label>Comprobante de Pago N&deg; <span name="cod"></span></label></b></td>
		</tr>
		<tr>
			<td><label>Nombre</label></td>
			<td><span name="nomb"></span></td>
		</tr>
		<tr>
			<td><label>Son</label></td>
			<td><span name="monto_string"></span></td>
		</tr>
		<tr>
			<td><label>Descripci&oacute;n</label></td>
			<td><span name="descr" ></span></td>
		</tr>
		<tr>
			<td><label>Referencias</label></td>
			<td><span name="ref"></span></td>
		</tr>
		<tr name="compr">
			<td><label>Comprobante</label></td>
			<td><select name="compr">
			<option value="F">Factura</option>
			<option value="B">Boleta de Venta</option>
			</select>&nbsp;&nbsp;Serie: <input type="text" name="serie" size="9">&nbsp;&nbsp;N&uacute;mero: <input type="text" name="num" size="5"></td>
		</tr>
	</tbody>
</table>
<fieldset>
<legend>Detalle del Gasto</legend>
<br />
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Cuentas por Pagar</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200x;">Actividad / Componente</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100x;">Subtotal (S/.)</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_det"></div>
				<div style="display:none;">
					<div class="gridReference_det">
						<ul>
							<li style="min-width:50px;max-width:50px;"></li>
							<li style="min-width:200px;max-width:200px;"></li>
							<li style="min-width:200px;max-width:200px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
						</ul>
					</div>
				</div>
				</div>
				
	<div id="tabs_conceptos">
    <ul>
        <li><a href="#tab_p">Pagos</a></li>
        <li><a href="#tab_d">Descuentos</a></li>
    </ul>
    <div id="tab_p">
            	<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuentas por Pagar</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200x;">Subtotal (S/.)</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="tab_p" ></div>
				</div>
    </div>
    <div id="tab_d">
    			<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuentas por Pagar</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200x;">Subtotal (S/.)</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="tab_d" ></div>
				</div>
   	 			</div>
		    	<div class="hidden" style="display:none;">
				<div class="gridReference_conc">
							<ul>
								<li style="min-width:50px;max-width:50px;"></li>
								<li style="min-width:350px;max-width:350px;"></li>
								<li style="min-width:200px;max-width:200px;"></li>
							</ul>
						</div>
				</div>
</div>
<div class="grid">
	<div class="gridBody">
		<div id="totales" style="margin-top:20px;">
			<a class="item pago">
				<ul style="font-weight: normal;">
					<li style="min-width: 350px; max-width: 350px; font-weight: normal;" class="ui-state-default ui-button-text-only">Total pagos</li>
					<li style="min-width: 100px; max-width: 100px; font-weight: normal;"></li>
				</ul>
			</a>
			<a class="item desc">
				<ul style="font-weight: normal;">
					<li style="min-width: 350px; max-width: 350px; font-weight: normal;" class="ui-state-default ui-button-text-only">Total descuentos</li>
					<li style="min-width: 100px; max-width: 100px; font-weight: normal;"></li>
				</ul>
			</a>
			<a class="item total">
				<ul style="font-weight: normal;">
					<li style="min-width: 350px; max-width: 350px; font-weight: normal;" class="ui-state-default ui-button-text-only">Total</li>
					<li style="min-width: 100px; max-width: 100px; font-weight: normal;"></li>
				</ul>
			</a>
		</div>
	</div>
</div>
</fieldset>
<fieldset>
<legend>Estadistica Objeto del Gasto</legend>
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Clasificador de Gasto</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Descripci&oacute;n</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Importe</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" id="grid_est" ></div>
				</div>
   	 			</div>
		    	<div class="hidden" style="display:none;">
				<div class="gridReference_est">
							<ul>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:150px;max-width:150px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
							</ul>
						</div>
				</div>
</fieldset>
<fieldset>
<legend>Codificaci&oacute;n Program&aacute;tica</legend>
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Pliego</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Programa</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">SubPrograma</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Proyecto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Obra</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;">Fuente de Financiamiento</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_cod" ></div>
				</div>
   	 			</div>
		    	<div class="hidden" style="display:none;">
				<div class="gridReference_cod">
							<ul>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:210px;max-width:210px;"><span name="fuente"></span></li>
							</ul>
						</div>
				</div>
</fieldset>
<fieldset>
<legend>Contabilidad Presupuestal</legend>
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Cuenta Contable</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Descripci&oacute;n</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Importe</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Debe</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Haber</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">&nbsp;</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_cont_pres" ></div>
				</div>
   	 			</div>
</fieldset>
<fieldset>
<legend>Contabilidad Patrimonial</legend>
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Cuenta Contable</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Descripci&oacute;n</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Importe</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Debe</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Haber</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">&nbsp;</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_cont_patr" ></div>
				</div>
   	 			</div>
</fieldset>
<fieldset>
<legend>Retenciones y/o Deducciones</legend>
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Cuenta Contable</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">C&oacute;digo</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Descripci&oacute;n</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Importe</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_rete_dedu" ></div>
				<div class="hidden" style="display:none;">
				<div class="gridReference_rete_dedu">
							<ul>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:150px;max-width:150px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
							</ul>
				</div>
				</div>
				</div>
   	 			</div>
</fieldset>
<fieldset>
<legend>Forma de Pago</legend>
	<table>
		<tbody>
			<tr>
				<td><label>Cuenta</label></td>
				<td><span name="pag_cuenta"></span></td>
			</tr>
		</tbody>
	</table>
	<div id="tabs_forma">
    <ul>
        <li><a href="#tab_c">Cheque</a></li>
        <li><a href="#tab_t">Transferencia</a></li>
    </ul>
    <div id="tab_c">
            	<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Beneficiarios</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100x;">Subtotal (S/.)</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:110px;max-width:110x;">Cheque</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="tab_c" ></div>
				<div class="hidden" style="display:none;">
						<div class="gridReference_forma_cheque">
							<ul>
								<li style="min-width:50px;max-width:50px;"></li>
								<li style="min-width:250px;max-width:250px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:110px;max-width:110px;"></li>
							</ul>
						</div>
				</div>
				</div>
    </div>
    <div id="tab_t">
    			<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Beneficiarios</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100x;">Subtotal (S/.)</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="tab_t" ></div>
				<div class="hidden" style="display:none;">
						<div class="gridReference_forma_trans">
							<ul>
								<li style="min-width:50px;max-width:50px;"></li>
								<li style="min-width:250px;max-width:250px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
							</ul>
						</div>
				</div>
				</div>
   	</div>
	</div>
</fieldset>
		    	<div class="hidden" style="display:none;">
				<div class="gridReference_cont">
							<ul>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:150px;max-width:150px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:100px;max-width:100px;"></li>
								<li style="min-width:50px;max-width:50px;"></li>
							</ul>
				</div>
				</div>