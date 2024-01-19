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
					<li style="min-width:146px;max-width:146px;">Detalle</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Anulados</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Estructura Funcional</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:146px;max-width:146px;">Contabilidad Patrimonial</li>
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
				<td><label>Iniciales</label></td>
				<td><input type="text" name="iniciales" size="6" /></td>
			</tr>
			<tr>
				<td width="95px"><label>Fecha Inicio</label></td>
				<td><input type="text" name="fec" size="12" /></td>
			</tr>
			<tr>
				<td><label>Fecha Fin</label></td>
				<td><input type="text" name="fecfin" size="12" /></td>
			</tr>
			<tr>
				<td><label>Organizaci&oacute;n</label></td>
				<td><span name="orga"></span>&nbsp;<button name="btnOrga">Seleccionar</button></td>
			</tr>
			<tr>
				<td><label>Responsable</label></td>
				<td><span name="respo"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Detalle</legend>
		<table>
			<tr>
				<td><label>Observaciones</label></td>
				<td><textarea rows="2" cols="40" name="observ"></textarea></td>
			</tr>
		</table>
		<div class="grid payment" style="overflow: hidden;min-width: 750px;max-width:100%">
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
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Comprobante</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Concepto</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Importe (S/.)</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;min-width: 750px;max-width:100%">
			<div class="gridBody" width="870px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:120px;max-width:120px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section3">
		<legend>Anulados</legend>
		<div class="grid payment" style="overflow: hidden;width: 450px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Comprobante</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Cliente</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;width: 450px;">
			<div class="gridBody" width="450px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:250px;max-width:250px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section4">
		<legend>Codificaci&oacute;n de la Contabilidad Presupuestal  y Clasificaci&oacute;n Program&aacute;tica del gasto P&uacute;blico</legend>
		<div class="grid payment" style="overflow: hidden;width: 750px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Pliego</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Programa</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">SubPrograma</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Proyecto</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Obra</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Actividad</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Componente</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Fuente de Financiamiento</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;width: 750px;">
			<div class="gridBody" width="800px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
					<li style="min-width:100px;max-width:100px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>Pagos</legend>
		<div class="grid payment" style="overflow: hidden;min-width: 750px;max-width: 100%;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Pagos</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Cuenta Bancaria</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Monto</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Monto en Soles</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Cliente</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;min-width: 750px;max-width: 100%;">
			<div class="gridBody" width="940px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:250px;max-width:250px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:120px;max-width:120px;"></li>
					<li style="min-width:120px;max-width:120px;"></li>
					<li style="min-width:250px;max-width:250px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section5">
		<legend>Contabilidad Patrimonial</legend>
		<div class="grid payment" style="overflow: hidden;width: 600px;">
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
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Importe del Debe</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;width: 600px;">
			<div class="gridBody" width="570px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
				</ul>
			</div>
		</div>
		<br />
		<div class="grid payment" style="overflow: hidden;width: 600px;">
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
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Importe del Haber</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid payment" style="max-height: 380px;width: 600px;">
			<div class="gridBody" width="570px"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:200px;max-width:200px;"></li>
					<li style="min-width:150px;max-width:150px;"></li>
					<li style="min-width:70px;max-width:70px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>