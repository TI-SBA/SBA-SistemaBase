<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Propietario</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Espacio</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Difuntos</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Accesorios</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:146px;max-width:146px;">Programaci&oacute;n</li>
				</ul>
			</a>
			<a class="item" name="section6">
				<ul>
					<li style="min-width:146px;max-width:146px;">Cotizaci&oacute;n</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1" style="">
		<legend>Propietario</legend>
		<table>
			<tr>
				<td colspan="2"><button name="btnBusPro">Buscar Propietario</button><button name="btnEdiPro">Editar Propietario</button></td>
			</tr>
			<tr>
				<td width="72"><label>Nombre:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>DNI/RUC:</label></td>
				<td><span name="doc"></span></td>
			</tr>
			<tr>
				<td><label>Tel&eacute;fono:</label></td>
				<td><span name="tel"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2" style="height:160px;">
		<legend>Espacio</legend>
		<table>
				<tr>
					<td width="176"><select name="cboSelEspacio"></select></td>
				</tr>
		</table>
		<div><table name="det"></table></div>
	</fieldset>
	<fieldset name="section3">
		<legend>Difuntos</legend>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">&nbsp;</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:300px;max-width:240px;">Difunto</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div name="griddif" class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:50px;max-width:50px"></li>
					<li style="min-width:240px;max-width:300px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section4">
		<legend>Accesorios</legend>
		<div class="grid" style="width:460px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:50px;max-width:50px;">&nbsp;</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:240px;max-width:240px;">Accesorio</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width:460px;height:110px;">
			<div name="gridacce" class="gridBody"></div>
			<div class="gridReference1">
				<ul>
					<li style="min-width:50px;max-width:50px"></li>
					<li style="min-width:240px;max-width:240px"></li>
				</ul>
			</div>
		</div>
		<button name="btnIncAcc">Incluir Accesorio:</button>
		<button name="btnAgreAcc">Agregar Accesorio</button>
	</fieldset>
	<fieldset name="section5">
		<legend>Fecha de Programaci&oacute;n</legend>
		<table cellpadding="2">
				<tr>
					<td><label>Fecha Programada:</label></td>
					<td><input type="text" name="fecprog"></td>
				</tr>
				<tr>
					<td><label>Observaciones:</label></td>
					<td><textarea type="text" name="observ" cols="40" rows="5"></textarea></td>
				</tr>
		</table>
	</fieldset>
	<fieldset name="section6">
		<legend>Cotizaci&oacute;n</legend>
		<div name="tabs">
			<ul>
				<li><a href="#tabsConcPayment-1">Contado</a></li>
				<li><a href="#tabsConcPayment-2">Cuotas</a></li>
				<li><a href="#tabsConcPayment-3">Sin Cobro</a></li>
			</ul>
			<div id="tabsConcPayment-1">
				<table>
					<tr>
						<td><label>Servicio</label></td>
						<td><span name="serv1"></span>&nbsp;<button name="btnServ1">Seleccionar</button></td>
					</tr>
					<tr>
						<td><label>Fecha de Vencimiento</label></td>
						<td><input type="text" name="fecven_pag" size="12"></td>
					</tr>
				</table>
				<div class="grid payment" style="overflow: hidden;width: 420px;">
					<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
						<ul>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:380px;max-width:380px;">Concepto</li>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Precio</li>
						</ul>
					</div>
				</div>
				<div class="grid payment" style="height: 150px;width: 420px;">
					<div class="gridBody" width="480px"></div>
					<div class="gridReference">
						<ul>
							<li style="min-width:280px;max-width:280px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
						</ul>
					</div>
				</div>
			</div>
			<div id="tabsConcPayment-2">
				<table>
					<tr>
						<td><label>Servicio</label></td>
						<td><span name="serv2"></span>&nbsp;<button name="btnServ2">Seleccionar</button></td>
					</tr>
					<tr>
						<td><label>N&uacute;mero de Cuotas</label></td>
						<td><input type="text" name="cuotas" size="2"></td>
					</tr>
				</table>
				<div class="grid payment" style="overflow: hidden;width: 420px;">
					<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
						<ul>
							<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:380px;max-width:380px;">Concepto</li>
						</ul>
					</div>
				</div>
				<div class="grid payment" style="height: 150px;width: 420px;">
					<div class="gridBody" width="480px"></div>
					<div class="gridReference"></div>
					<div class="gridReference">
						<ul>
							<li style="min-width:280px;max-width:280px;"></li>
						</ul>
					</div>
				</div>
			</div>
			<div id="tabsConcPayment-3">
				<label>Esta operaci&oacute;n se realiza sin cobro alguno</label>
			</div>
		</div>
	</fieldset>
</div>