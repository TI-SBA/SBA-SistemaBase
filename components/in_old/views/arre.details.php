<?php
$classRowTitle = 'ui-button ui-widget ui-state-default ui-button-text-only';
?>
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
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Rentas</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Acta de Inmueble</li>
				</ul>
			</a>
			<a class="item" name="section5">
				<ul>
					<li style="min-width:146px;max-width:146px;">Actualizaciones</li>
				</ul>
			</a>
			<a class="item" name="section6">
				<ul>
					<li style="min-width:146px;max-width:146px;">Desocupaci&oacute;n</li>
				</ul>
			</a>
			<a class="item" name="section7">
				<ul>
					<li style="min-width:146px;max-width:146px;">Acta de Inmueble Final</li>
				</ul>
			</a>
			<a class="item" name="section8">
				<ul>
					<li style="min-width:146px;max-width:146px;">Servicios</li>
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
				<td><label>Arrendatario</label></td>
				<td width="250px"><span name="arre"></span></td>
				<td colspan="2"><span name="tipo"></span></td>
			</tr>
			<tr>
				<td><label>Representante</label></td>
				<td colspan="3"><span name="repr"></span></td>
			</tr>
			<tr>
				<td><label>Inmueble Matriz</label></td>
				<td><span name="local"></span></td>
				<td><label>Direcci&oacute;n</label></td>
				<td><span name="direc"></span></td>
			</tr>
			<tr>
				<td><label>Inmueble</label></td>
				<td><span name="espacio"></span></td>
				<td><label>Registro del Arrendamiento</label></td>
				<td><span name="fecreg"></span></td>
			</tr>
			<tr>
				<td><label>Condici&oacute;n</label></td>
				<td><span name="condic"></span></td>
				<td><label>Registrado por</label></td>
				<td><span name="reg"></span></td>
			</tr>
			<tr>
				<td><label>Contrato</label></td>
				<td><span name="contrato"></span></td>
				<td><label>Registro del Contrato</label></td>
				<td><span name="feccon"></span></td>
			</tr>
			<tr>
				<td><label>Fecha de ocupaci&oacute;n</label></td>
				<td><span name="fecocu"></span></td>
				<td><label>Fecha de Vencimiento</label></td>
				<td><span name="fecven"></span></td>
			</tr>
			<tr name="trTrasp">
				<td><label>Traspaso</label></td>
				<td colspan="3"><span></span></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td colspan="3"><span name="observ"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2">
		<legend>Garant&iacute;as</legend>
		<table>
			<tr>
				<td><label>Inmueble en garant&iacute;a</label></td>
				<td><span name="inmueble"></span></td>
			</tr>
		</table>
		<label>Aval</label>
		<div class="grid" style="width: 550px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:250px;max-width:250px;">Nombre</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:90px;max-width:90px;">DNI</li>
					<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="text-align: center;min-width:200px;max-width:200px;">Direcci&oacute;n</li>
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
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section3">
		<legend>Rentas</legend>
		<div class="grid" style="width: 560px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:70px;max-width:70px;">N&deg;</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:170px;max-width:170px;">Letra</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:120px;max-width:120px;">Fecha de Vencimiento</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:120px;max-width:120px;">Fecha de Pago</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:120px;max-width:120px;">Fecha de Protesto</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:100px;max-width:100px;">Importe</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:100px;max-width:100px;">Estado</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 560px;max-height: 140px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:70px;max-width:70px"></li>
					<li style="min-width:170px;max-width:170px"></li>
					<li style="min-width:120px;max-width:120px"></li>
					<li style="min-width:120px;max-width:120px"></li>
					<li style="min-width:120px;max-width:120px"></li>
					<li style="min-width:100px;max-width:100px"></li>
					<li style="min-width:100px;max-width:100px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section4">
		<legend>Acta de Inmueble</legend>
		<div class="grid" style="width: 560px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:250px;max-width:250px;">Descripci&oacute;n</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:90px;max-width:90px;">Conservaci&oacute;n</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:180px;max-width:180px;">Observaciones</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 560px;height: 140px;">
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
	<fieldset name="section5">
		<legend>Actualizaciones</legend>
		<div class="grid" style="width: 560px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:170px;max-width:170px"></li>
					<li style="min-width:90px;max-width:90px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section6">
		<legend>Desocupaci&oacute;n</legend>
		<table>
			<tr>
				<td><label>Fecha de desocupaci&oacute;n</label></td>
				<td><span name="fecdes"></span></td>
				<td><label>Motivo</label></td>
				<td><span name="motivo"></span></td>
			</tr>
			<tr>
				<td><label>Observaciones</label></td>
				<td colspan="3"><span name="observDes"></span></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section7">
		<legend>Acta de Inmueble Final</legend>
		<table>
			<tr>
				<td><label>Actualizaci&oacute;n de Ficha</label></td>
				<td><span name="fecact"></span></td>
				<td><label>Actualizado por</label></td>
				<td><span name="act"></span></td>
			</tr>
		</table>
		<div class="grid" style="width: 560px;">
			<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
				<ul>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:170px;max-width:170px;">Descripci&oacute;n</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:90px;max-width:90px;">Conservaci&oacute;n</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:120px;max-width:120px;">Observaciones</li>
					<li class="<?=$classRowTitle?>" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
				</ul>
			</div>
		</div>
		<div class="grid" style="width: 560px;height: 140px;">
			<div class="gridBody"></div>
			<div class="gridReference">
				<ul>
					<li style="min-width:170px;max-width:170px"></li>
					<li style="min-width:90px;max-width:90px"></li>
					<li style="min-width:120px;max-width:120px"></li>
				</ul>
			</div>
		</div>
	</fieldset>
	<fieldset name="section8">
		<legend>Servicios</legend>
		<div name="divClon" style="display: none;">
			<label></label>
			<div class="grid" style="width: 560px;">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
					<ul>
						<li class="<?=$classRowTitle?>" style="text-align: center;min-width:120px;max-width:120px;">Fecha de Vencimiento</li>
						<li class="<?=$classRowTitle?>" style="text-align: center;min-width:100px;max-width:100px;">Importe</li>
						<li class="<?=$classRowTitle?>" style="text-align: center;min-width:100px;max-width:100px;">Estado</li>
						<li class="<?=$classRowTitle?>" style="text-align: center;min-width:120px;max-width:120px;">Comprobante</li>
						<li class="<?=$classRowTitle?>" style="text-align: center;min-width:70px;max-width:70px;">&nbsp;</li>
					</ul>
				</div>
			</div>
			<div class="grid" style="width: 560px;max-height: 140px;">
				<div class="gridBody"></div>
			</div>
			<hr>
		</div>
		<div class="gridReference">
			<ul>
				<li style="min-width:120px;max-width:120px"></li>
				<li style="min-width:100px;max-width:100px"></li>
				<li style="min-width:100px;max-width:100px"></li>
				<li style="min-width:120px;max-width:120px"></li>
				<li style="min-width:100px;max-width:100px"></li>
			</ul>
		</div>
	</fieldset>
</div>