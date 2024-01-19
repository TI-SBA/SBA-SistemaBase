<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Datos Generales</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Efectivo Recaudado</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Contabilidad Patrimonial del Efectivo en Soles</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Contabilidad Patrimonial del Efectivo en D&oacute;lares</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:146px;max-width:146px;">Vouchers de detracci&oacute;n</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td colspan="2"><h3>Recibo de Ingresos N&deg;<span name="num"></span></h3></td>
			</tr>
			<tr>
				<td width="95px"><label>Fecha</label></td>
				<td><span name="fec"></span></td>
			</tr>
			<tr>
				<td><label>Organizaci&oacute;n</label></td>
				<td><span name="orga"></span></td>
			</tr>
			<tr>
				<td><label>Responsable</label></td>
				<td><span name="respo"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Efectivo Recaudado</legend>
		<table>
			<tr>
				<td><label>Fecha del dep&oacute;sito</label></td>
				<td><input type="text" name="fecdep" /></td>
			</tr>
		</table>
		<div class="payment">
			<div class="grid" style="overflow: hidden;width: 450px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Pagos</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Monto en S/.</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Cuenta Bancaria</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Voucher del dep&oacute;sito</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:140px;max-width:140px;">Medio de Pago</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n - Libro Bancos</li>
					</ul>
				</div>
			</div>
			<div class="grid" style="max-height: 380px;width: 450px;">
				<div class="gridBody" width="1290px"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:140px;max-width:140px;"></li>
						<li style="min-width:250px;max-width:250px;"></li>
					</ul>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset name="section3">
		<legend>Contabilidad Patrimonial del Efectivo en Soles</legend>
		<div class="payment">
			<div class="grid" style="overflow: hidden;width: 450px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Saldo Deudor</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Comprobantes</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n - Libro Movimientos Cta. Cte.</li>
					</ul>
				</div>
			</div>
			<div class="grid" style="max-height: 380px;width: 450px;">
				<div class="gridBody" width="1000px"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:250px;max-width:250px;"></li>
						<li style="min-width:250px;max-width:250px;"></li>
					</ul>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset name="section4">
		<legend>Contabilidad Patrimonial del Efectivo en D&oacute;lares</legend>
		<table>
			<tr>
				<td><label>Tipo de Cambio</label></td>
				<td><input type="text" name="tc" size="5" /></td>
			</tr>
		</table>
		<div class="payment">
			<div class="grid" style="overflow: hidden;width: 450px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:350px;max-width:350px;">Cuenta Contable</li>
							</ul>
							<ul style="display:block">
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">C&oacute;digo</li>
								<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Descripci&oacute;n</li>
							</ul>
						</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Saldo Deudor ($)</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Saldo Deudor (S/.)</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Comprobantes</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n - Libro Movimientos Cta. Cte.</li>
					</ul>
				</div>
			</div>
			<div class="grid" style="max-height: 380px;width: 450px;">
				<div class="gridBody" width="1000px"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
						<li style="min-width:250px;max-width:250px;"></li>
						<li style="min-width:250px;max-width:250px;"></li>
					</ul>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset name="section5">
		<legend>Vouchers de detracci&oacute;n</legend>
		<div class="payment">
			<div class="grid" style="overflow: hidden;width: 450px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Pagos</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Cuenta Bancaria</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Monto en Soles</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Cliente</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Fecha de Voucher</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Cuenta Contable</li>
					</ul>
				</div>
			</div>
			<div class="grid" style="max-height: 380px;width: 450px;">
				<div class="gridBody" width="940px"></div>
				<div class="gridReference">
					<ul>
						<li style="min-width:250px;max-width:250px;"></li>
						<li style="min-width:200px;max-width:200px;"></li>
						<li style="min-width:120px;max-width:120px;"></li>
						<li style="min-width:120px;max-width:120px;"></li>
						<li style="min-width:250px;max-width:250px;"></li>
						<li style="min-width:120px;max-width:120px;"></li>
						<li style="min-width:150px;max-width:150px;"></li>
					</ul>
				</div>
			</div>
		</div>
	</fieldset>
</div>