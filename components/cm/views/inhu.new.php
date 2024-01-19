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
					<li style="min-width:146px;max-width:146px;">Difunto</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Espacio</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Programaci&oacute;n</li>
				</ul>
			</a>
			<a class="item" name="section5">
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
				<td rowspan="2" width="176"><button name="btnBusPro">Buscar Propietario</button><br />
				<button name="btnNewPro">Agregar Propietario</button></td>
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
		<legend>Difunto</legend>
		<table cellpadding="2">
				<tr>
					<td><label>Nombre:</label></td>
					<td><select style="width:250px;" name="difunto"></select></td>
				</tr>
				<tr>
					<td><label>Fecha de Defunci&oacute;n:</label></td>
					<td><input type="text" name="fecdef"></td>
				</tr>
				
				<tr>
					<td><label>Edad</label></td>
					<td><input type="text" name="edad"></td>
				</tr>
				<tr>
					<td><label>Folio</label></td>
					<td><input type="text" name="folio"></td>
				</tr>
				<tr>
					<td><label>Causa de Fallecimiento</label></td>
					<td><input type="text" name="causa"></td>
				</tr>
			<!-- 	<tr>
					<td><label>Fecha de Nacimiento:</label></td>
					<td><input type="text" name="fecnac"></td>
				</tr> -->
				<tr>
					<td></td>
					<td colspan="2"><button name="btnBusFun">Buscar Funeraria</button>
					<button name="btnNewFun">Agregar Funeraria</button></td>
				</tr>
				<tr>
					<td width="72"><label>Funeraria:</label></td>
					<td><span name="funeraria"></span></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2" ><button name="btnBusMuni">Buscar Municipalidad</button>
					<button name="btnNewMuni">Agregar Municipalidad</button></td>
				</tr>
				<tr>
					<td width="72"><label>Municipalidad:</label></td>
					<td><span name="muni"></span></td>
				</tr>
				<tr>
					<td><label>Ingreso por la Puerta:</label></td>
					<td><select name="puerta">
						<option value="Principal">Principal</option>
						<option value="Posterior">Posterior</option>
					</select></td>
				</tr>
				<!--<tr>
					<td><label>Partida de Defunci&oacute;n:</label></td>
					<td><input type="text" name="partdef" size="25"/></td>
				</tr>-->
		</table>
	</fieldset>
	<fieldset name="section3">
		<legend>Espacio</legend>
		<div><table name="espacio" cellpadding="3"></table></div>
	</fieldset>
	<fieldset name="section4">
		<legend>Fecha de Programaci&oacute;n</legend>
		 <div id="rbtnAuto">
			<input type="radio" id="rbtnAuto1" name="rbtnAuto" value="1">
			<label for="rbtnAuto1">Inhumaci&oacute;n Autom&aacute;tica</label>
			<input type="radio" id="rbtnAuto0" name="rbtnAuto" value="0" checked="checked">
			<label for="rbtnAuto0">Proceso Regular</label>
		</div>
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
		<div name="auto_inh">
			<br />
			<label>Fecha cuando se ejecut&oacute;:</label><input type="text" name="fec_auto"><br />
			<label>Observaciones:</label><textarea type="text" name="observ_auto" cols="40" rows="5"></textarea>
		</div>
	</fieldset>
	<fieldset name="section5">
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