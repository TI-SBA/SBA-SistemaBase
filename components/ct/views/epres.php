	<table>
			<tr>			
				<td><b><label>Periodo: </label></b> <select name="mes"><option value="1">Enero</option>
				<option value="2">Febrero</option>
				<option value="3">Marzo</option>
				<option value="4">Abril</option>
				<option value="5">Mayo</option>
				<option value="6">Junio</option>
				<option value="7">Julio</option>
				<option value="8">Agosto</option>
				<option value="9">Setiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option></select> - <input type="text" name="ano" size="6"></td>
				<td><b><label>Estado: </label></b> <span name="periodo"></span></td>
				<td><button name="btnCerrarperiodo">Cerrar Periodo</button> <button name="btnAgregar">Agregar Registro</button></td>				
			</tr>
			<tr>
				<td><b><label>Dependencia: </label></b> <input type="text" name="depen"><button name="btnOrga">Seleccionar</button></td>
				<td colspan="2"><b><label>Clasificaci&oacute;n Econ&oacute;mica: </label></b> <select name="fuen"></select></td>
			</tr>
			<tr>
				<td><b><label>Genen&eacute;rica</label></b> <input type="text" name="clas_gen"><button name="btnGen">Seleccionar</button></td>
				<td><b><label>Especif&iacute;ca</label></b> <input type="text" name="clas_esp"><button name="btnEsp">Seleccionar</button></td>
				<td><b><label>Sub-Especif&iacute;ca</label></b> <input type="text" name="clas_sub"><button name="btnSub">Seleccionar</button></td>
			</tr>
			<tr>
				<td><b><label>Descripci&oacute;n</label></b> <span name="descr_gen"></span></td>
				<td><b><label>Descripci&oacute;n</label></b> <span name="descr_esp"></span></td>
				<td><b><label>Descripci&oacute;n</label></b> <span name="descr_sub"></span></td>
			</tr>
		</table>		
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:30px;max-width:30px;">&nbsp;</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Fecha</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Mes</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Dia</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Comprobante</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Clase</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">N&deg;</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Detalle</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;">Ejecuci&oacute;n de Presupuesto</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">Haber</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">Saldo</li>
					</ul>
				</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:210px;max-width:210px;">Ejecuci&oacute;n de Ingresos</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">Debe</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">Haber</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:70px;max-width:70px;">Saldo</li>
					</ul>
				</li>	
			</ul>
		</div>
	</div>
	<div class="grid" style="height: 150px;">
	<div class="gridBody" ></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:30px;max-width:30px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:150px;max-width:150px;"></li>
			<li style="min-width:70px;max-width:70px;"></li>
			<li style="min-width:70px;max-width:70px;"></li>
			<li style="min-width:70px;max-width:70px;"></li>
			<li style="min-width:70px;max-width:70px;"></li>
			<li style="min-width:70px;max-width:70px;"></li>
			<li style="min-width:70px;max-width:70px;"></li>
		</ul>
	</div>
	</div>