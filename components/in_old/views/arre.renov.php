<div class="ui-layout-west">
	<div class="grid">
		<div class="gridBody">
			<a class="item" name="section1">
				<ul>
					<li style="min-width:146px;max-width:146px;">Arrendatario</li>
				</ul>
			</a>
			<a class="item" name="section2">
				<ul>
					<li style="min-width:146px;max-width:146px;">Aval</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Representante Legal</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Inmueble</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:146px;max-width:146px;">Arrendamiento</li>
				</ul>
			</a>
			<a class="item" name="section6">
				<ul>
					<li style="min-width:146px;max-width:146px;">Acta de Inmueble</li>
				</ul>
			</a>
			<a class="item" name="section7">
				<ul>
					<li style="min-width:146px;max-width:146px;">Rentas</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Arrendatario</legend>
		<table>
			<tr>
				<td width="80"><label>Arrendatario:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>DNI/RUC:</label></td>
				<td><span name="dni"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Aval</legend>
		<div class="grid" style="width: 650px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:250px;max-width:250px;">Nombre</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">DNI</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:200px;max-width:200px;">Direcci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:120px;max-width:120px;">&nbsp;</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:10px;max-width:10px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="max-height: 400px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:250px;max-width:250px"></li>
					<li style="min-width:90px;max-width:90px"></li>
					<li style="min-width:200px;max-width:200px"></li>
					<li style="min-width:120px;max-width:120px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section3">
		<legend>Representante Legal</legend>
		<table>
			<tr>
				<td rowspan="3" width="90"><button name="btnBus">Buscar</button><br />
				<button name="btnNew">Nuevo</button></td>
				<td width="130"><label>Representante Legal:</label></td>
				<td><span name="nomb"></span></td>
			</tr>
			<tr>
				<td><label>DNI/RUC:</label></td>
				<td><span name="dni"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section4">
		<legend>Inmueble</legend>
		<table>
			<tr>
				<td width="130"><label>Inmueble Matriz:</label></td>
				<td><span name="local"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n:</label></td>
				<td><span name="direc"></span></td>
			</tr>
			<tr>
				<td><label>Descripci&oacute;n:</label></td>
				<td><span name="descr"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section5">
		<legend>Arrendamiento</legend>
		<table>
			<tr>
				<td width="150px"><label>Tipo</label></td>
				<td><span>Renovaci&oacute;n</span></td>
			</tr>
			<tr>
				<td><label>Condici&oacute;n</label></td>
				<td><select name="condic">
					<option value="CT">Nuevo</option>
					<option value="RE">Renovaci&oacute;n</option>
					<option value="CV">Convenio</option>
					<option value="CU">Cesi&oacute;n en Uso</option>
					<option value="CM">Comodato</option>
					<option value="AC">Acta de Conciliaci&oacute;n</option>
					<option value="RS">Por Ocupaci&oacute;n</option>
					<option value="AU">Autorizaci&oacute;n</option>
					<option value="PE">Penalidades</option>
					<option value="TR">Traspaso</option>
					<option value="AD">Audiencias</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Contrato N&deg;</label></td>
				<td><input type="text" name="contrato"></td>
			</tr>
			<tr>
				<td><label>Fecha de Registro de Contrato</label></td>
				<td><input type="text" name="feccon" size="11"></td>
			</tr>
			<tr>
				<td><label>Ocupaci&oacute;n</label></td>
				<td><span>De </span><input type="text" size="11" name="fecini" readonly="readonly"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><span>A </span><input type="text" size="11" name="fecfin" readonly="readonly"></td>
			</tr>
			<tr>
				<td><label>Moneda</label></td>
				<td><select name="cboMoneda">
					<option value="S" sign="S/.">Nuevos Soles</option>
					<option value="D" sign="$">D&oacute;lares</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Renta</label></td>
				<td><span name="spMon"></span><input type="text" size="7" name="renta"></td>
			</tr>
			<tr>
				<td><label>Moneda de la Garant&iacute;a</label></td>
				<td><select name="cboMoneda_gar">
					<option value="S" sign="S/.">Nuevos Soles</option>
					<option value="D" sign="$">D&oacute;lares</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Garant&iacute;a</label></td>
				<td><span name="spMon_gar"></span><input type="text" size="7" name="garantia"></td>
			</tr>
			<tr>
				<td><label>Inmueble en garant&iacute;a</label></td>
				<td><input type="text" name="inmueble"></td>
			</tr>
			<tr>
				<td><label>Uso</label></td>
				<td><select name="cboUso">
					<option value="TI">Tiendas</option>
					<option value="OF">Oficina</option>
					<option value="HO">Hotel</option>
					<option value="ST">Stand</option>
					<option value="CI">Cine</option>
					<option value="ES">Espacio</option>
					<option value="CO">Cochera</option>
					<option value="VI">Casa Habitaci&oacute;n</option>
					<option value="OT">Otros</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Detalle de Uso</label></td>
				<td><input type="text" size="40" name="detalle_uso" /></td>
			</tr>
			<tr>
				<td><label>Estado de conservaci&oacute;n</label></td>
				<td><select name="cboConserv">
					<option value="B">Bueno</option>
					<option value="R">Regular</option>
					<option value="M">Malo</option>
				</select></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td><textarea name="observ" cols="35" rows="3"></textarea></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section6">
		<legend>Acta de Inmueble</legend>
		<button name="btnAgregar">Agregar</button>
		<div class="grid" style="width: 480px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:40px;max-width:40px;"></li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Descripci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">Conservaci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:80px;max-width:80px;">Cantidad</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:170px;max-width:170px;">Observaciones</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 480px;max-height: 180px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:40px;max-width:40px"><button name="btnEli">Eliminar</button></li>
					<li style="min-width:170px;max-width:170px"><input type="text" name="descr"></li>
					<li style="min-width:90px;max-width:90px"><select name="conserv">
						<option value="B">Bueno</option>
						<option value="R">Regular</option>
						<option value="M">Malo</option>
					</select></li>
					<li style="min-width:80px;max-width:80px"><input type="text" size="4" name="cant"></li>
					<li style="min-width:170px;max-width:170px"><input type="text" name="observ"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section7">
		<legend>Rentas</legend>
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
		<div class="grid payment" style="max-height: 200px;width: 420px;">
			<div class="gridBody" width="480px"></div>
			<div class="gridReference"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:280px;max-width:280px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>