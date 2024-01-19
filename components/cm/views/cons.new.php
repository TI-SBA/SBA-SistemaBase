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
					<li style="min-width:146px;max-width:146px;">Cotizaci&oacute;n</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1" style="height:65px;">
		<legend>Propietario</legend>
		<table>
			<tr>
				<td rowspan="2"><button name="btnBusPro">Buscar Propietario</button></td>
				<td width="72"><label>Nombre:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td width="72"><label>Apellidos:</label></td>
				<td><span name="apell"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Espacio</legend>
		<table>
			<tr>
				<td><label>Mausoleo</label></td>
				<td colspan=3><select name="cboSelEspacio" style="width:370px;"></select></td>
			</tr>
			<tr>
				<td><label>Cantidad</label></td>
				<td width="176"><input type="text" name="txtCantidad"></td>
				<td><label>Tipo</label></td>
				<td width="176"><select name="cboSelTipo" style="width:160px;">
				<option value="B">Nicho-B&oacute;veda</option>
				<option value="C">Capilla</option>
				<option value="R">Cripta</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Ancho</label></td>
				<td width="176"><input type="text" name="txtAncho"></td>
				<td><label>Largo</label></td>
				<td width="176"><input type="text" name="txtLargo"></td>
			</tr>
			<tr>
				<td><label>Altura 1</label></td>
				<td width="176"><input type="text" name="txtAltura1"></td>
				<td><label>Altura 2</label></td>
				<td width="176"><input type="text" name="txtAltura2"></td>
			</tr>
			<tr>
				<td><label>Fecha de Vencimiento</label></td>
				<td><input type="text" name="fecven" size="10"></td>
				<td><label>Decreto y/o resoluci&oacute;n</label></td>
				<td><input type="text" name="decreto" size="18"></td>
			</tr>
			<tr>
				<td><label>Fecha de Decreto</label></td>
				<td colspan="3"><input type="text" name="fecdec" size="10"></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td colspan="3"><textarea name="observ" cols="40" rows="5"></textarea></td>
			</tr>
		</table>
		<div><table name="det"></table></div>
	</fieldset>
	<fieldset name="section3">
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