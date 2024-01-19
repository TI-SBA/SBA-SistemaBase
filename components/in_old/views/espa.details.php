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
					<li style="min-width:146px;max-width:146px;">Garant&iacute;as</li>
				</ul>
			</a>
		</div>
	</div>
	<div><button name="btnMostrar">Mostrar Fichas</button></div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td><label>Inmueble Matriz</label></td>
				<td width="250px"><span name="local"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n</label></td>
				<td><span name="direc"></span></td>
			</tr>
			<tr>
				<td><label>Descripci&oacute;n</label></td>
				<td><span name="descr"></span></td>
			</tr>
			<tr>
				<td><label>Referencia</label></td>
				<td><span name="ref"></span></td>
			</tr>
			<tr>
				<td><label>Registrado</label></td>
				<td><span name="fecreg"></span></td>
			</tr>
			<tr>
				<td><label>Uso</label></td>
				<td><span name="uso"></span></td>
			</tr>
			<tr>
				<td><label>Conservaci&oacute;n</label></td>
				<td><span name="conserv"></span></td>
			</tr>
			<tr>
				<td><label>Habilitado</label></td>
				<td><span name="habilitado"></span></td>
			</tr>
			<tr>
				<td><label>Estado</label></td>
				<td><span name="estado"></span></td>
			</tr>
			<tr>
				<td><label>Renta Base</label></td>
				<td><span name="renta"></span></td>
			</tr>
			<tr>
				<td><label>Garant&iacute;a</label></td>
				<td><span name="garantia"></span></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea del terreno</label></td>
				<td><span name="arterr"></span></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea construida</label></td>
				<td><span name="arcons"></span></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero de Medidor de Agua</label></td>
				<td><span name=medidor_agua></span></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero de Medidor de Luz</label></td>
				<td><span name="medidor_luz"></span></td>
			</tr>
			<tr>
				<td><label>C&oacute;digo de Arbitrios</label></td>
				<td><span name="cod_arbitrios"></span></td>
			</tr>
			<tr>
				<td><label>Arrendatario</label></td>
				<td><span name="arren"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Acta de Inmueble</legend>
		<div class="grid" style="width: 355px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:250px;max-width:250px;">Descripci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">Conservaci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:80px;max-width:80px;">Cantidad</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:180px;max-width:180px;">Observaciones</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 355px;max-height: 200px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:250px;max-width:250px"></li>
					<li style="min-width:90px;max-width:90px"></li>
					<li style="min-width:80px;max-width:80px"></li>
					<li style="min-width:180px;max-width:180px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>
<div class="ui-layout-east">
	<fieldset style="text-align: center;">
		<label style="text-align: center;font-size: 12px;font-weight: bold;">Fichas de Actualizaci&oacute;n</label><br />
		<button name="btnActPri">Actualizaci&oacute;n m&aacute;s antigua</button>&nbsp;<button name="btnActAnt">Anterior actualizaci&oacute;n</button>&nbsp;
		<span name="fec"></span>&nbsp;
		<button name="btnActPos">Siguiente actualizaci&oacute;n</button>&nbsp;<button name="btnActRec">Actualizaci&oacute;n m&aacute;s reciente</button>&nbsp;
	</fieldset>
	<fieldset name="section1">
		<legend>Datos Generales</legend>
		<table>
			<tr>
				<td><label>Inmueble Matriz</label></td>
				<td width="250px"><span name="local"></span></td>
			</tr>
			<tr>
				<td><label>Direcci&oacute;n</label></td>
				<td><span name="direc"></span></td>
			</tr>
			<tr>
				<td><label>Descripci&oacute;n</label></td>
				<td><span name="descr"></span></td>
			</tr>
			<tr>
				<td><label>Referencia</label></td>
				<td><span name="ref"></span></td>
			</tr>
			<tr>
				<td><label>Registrado</label></td>
				<td><span name="fecreg"></span></td>
			</tr>
			<tr>
				<td><label>Uso</label></td>
				<td><span name="uso"></span></td>
			</tr>
			<tr>
				<td><label>Conservaci&oacute;n</label></td>
				<td><span name="conserv"></span></td>
			</tr>
			<tr>
				<td><label>Habilitado</label></td>
				<td><span name="habilitado"></span></td>
			</tr>
			<tr>
				<td><label>Estado</label></td>
				<td><span name="estado"></span></td>
			</tr>
			<tr>
				<td><label>Renta Base</label></td>
				<td><span name="renta"></span></td>
			</tr>
			<tr>
				<td><label>Garant&iacute;a</label></td>
				<td><span name="garantia"></span></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea del terreno</label></td>
				<td><span name="arterr"></span><label>&nbsp;m&sup2;</label></td>
			</tr>
			<tr>
				<td><label>&Aacute;rea construida</label></td>
				<td><span name="arcons"></span><label>&nbsp;m&sup2;</label></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero de Medidor de Agua</label></td>
				<td><span name=medidor_agua></span></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero de Medidor de Luz</label></td>
				<td><span name="medidor_luz"></span></td>
			</tr>
			<tr>
				<td><label>C&oacute;digo de Arbitrios</label></td>
				<td><span name="cod_arbitrios"></span></td>
			</tr>
			<tr>
				<td><label>Arrendatario</label></td>
				<td><span name="arren"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Acta de Inmueble</legend>
		<div class="grid" style="width: 360px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:250px;max-width:250px;">Descripci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">Conservaci&oacute;n</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:180px;max-width:180px;">Observaciones</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 360px;max-height: 200px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:250px;max-width:250px"></li>
					<li style="min-width:90px;max-width:90px"></li>
					<li style="min-width:180px;max-width:180px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>