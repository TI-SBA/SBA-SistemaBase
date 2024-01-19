<table>
	<tr>
		<td><b><label>Periodo</label></b></td>
		<td><span name="mes"></span> - <span name="ano"></span></td>
		
		<td><b><label>Cuenta Bancaria</label></b></td>
		<td><select name="c_ban"></select></td>
	</tr>
	<tr>
		<td><b><label>Moneda</label></b></td>
		<td><span name=moneda></span></td>
		
		<td><b><label>Banco</label></b></td>
		<td><span name="banco"></span></td>
	</tr>
</table>
<fieldset>
	<table>
		<tr>
			<td><b><label>A. SALDO SEG&Uacute;N LIBRO DE BANCOS</label></b></td>
			<td><input type="text" name="saldo" size="6"></td>
		</tr>
		<tr>
			<td><b><label>B. CHEQUES PENDIENTES DE PAGO EN BANCOS</label></b></td>
			<td><span name="pendi"></span></td>
		</tr>
		<tr>
			<td colspan="2"><button name="btnSeleccionarCheques">Seleccionar Cheques</button></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Fecha</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Cheque</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Detalle</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_cheques"></div>
				<div style="display:none;">
					<div class="gridReference_cheques">
						<ul>
							<li style="min-width:100px;max-width:100px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
							<li style="min-width:150px;max-width:150px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
						</ul>
					</div>
				</div>
				</div>
			</td>
		</tr>
		<tr>
			<td><b><label>C. DEP&Oacute;SITOS NO REGISTRADOS EN LIBROS</label></b></td>
			<td><span name="dep_no_reg"></span></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Fecha</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Deposito N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Agencia</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">&nbsp;</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_dep_no_reg"></div>
				<div style="display:none;">
					<div class="gridReference_dep_no_reg">
						<ul>
							<li style="min-width:120px;max-width:120px;"><input type="text" name="fec1" size="9"></li>
							<li style="min-width:130px;max-width:180px;"><input type="text" name="dep1" size="10"></li>
							<li style="min-width:150px;max-width:150px;"><input type="text" name="age1" size="15"></li>
							<li style="min-width:100px;max-width:100px;"><input type="text" name="mon1" size="9"></li>
							<li style="min-width:50px;max-width:50px;"><button name="btnEli1">Eliminar</button><button name="btnAdd1">Agregar</button></li>
						</ul>
					</div>
				</div>
				</div>
			</td>
		</tr>
		<tr>
			<td><b><label>D. GASTOS NO REGISTRADOS EN LIBROS</label></b></td>
			<td><span name="gast_no_reg"></span></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:120px;max-width:120px;">Fecha</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:250px;max-width:250px;">Descripci&oacute;n</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:50px;max-width:50px;">&nbsp;</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_gast_no_reg"></div>
				<div style="display:none;">
					<div class="gridReference_gast_no_reg">
						<ul>
							<li style="min-width:120px;max-width:120px;"><input type="text" name="fec2" size="9"></li>
							<li style="min-width:250px;max-width:250px;"><input type="text" name="des2" size="30"></li>
							<li style="min-width:100px;max-width:100px;"><input type="text" name="mon2" size="9"></li>
							<li style="min-width:50px;max-width:50px;"><button name="btnEli2">Eliminar</button><button name="btnAdd2">Agregar</button></li>
						</ul>
					</div>
				</div>
				</div>
			</td>
		</tr>
		<tr>
			<td><b><label>E. SALDO EN BANCOS (A+B+C-D)</label></b></td>			
			<td><span name="sald_bancos"></span></td>
		</tr>		
	</table>
</fieldset>

<fieldset>
	<legend>Conciliaci&oacute;n</legend>
	<table>
		<tr>
			<td>Saldo seg&uacute;n extracto bancario <span name="moneda_simbol"></span></td>
			<td><input type="text" name="sald_seg_extr" size="7"></td>
		</tr>
		<tr>
			<td>Saldo seg&uacute;n libro bancos <span name="moneda_simbol"></span></td>
			<td><span name="sald_seg_libr"></span></td>
		</tr>
		<tr>
			<td>Diferencia <span name="moneda_simbol"></span></td>
			<td><span name="dif_simb"></span></td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>Observaciones</legend>
	<table>
		<tr>
			<td><textarea name="observ" cols="45"></textarea></td>
		</tr>
		<tr>
			<td><button name="btnRectificar">Rectificar Cheques</button></td>
		</tr>
		<tr>
			<td>
				<div class="grid">
				<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
					<ul>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Cheque N&deg;</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Monto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">CH/Extracto</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Diferencia</li>
					</ul>
				</div>
				</div>
				<div class="grid" style="height:auto;">
				<div class="gridBody" name="grid_rect"></div>
				<div style="display:none;">
					<div class="gridReference_rect">
						<ul>
							<li style="min-width:100px;max-width:100px;"></li>
							<li style="min-width:150px;max-width:250px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
							<li style="min-width:100px;max-width:100px;"></li>
						</ul>
					</div>
				</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>