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
				<td><b><label>Cuadro de: </label></b><select name="tipo">
					<option value="C">Compromisos</option>
					<option value="E">Ejecuci&oacute;n</option>
				</select></td>
				<td><b><label>Estado: </label></b> <span name="estado"></span></td>
				<td><button name="btnCerrarperiodo">Cerrar Periodo</button> <button name="btnGuardar">Guardar</button></td>				
			</tr>			
		</table>		
	<div class="grid">
		<div class="gridHeader ui-state-default ui-jqgrid-hdiv" style="position: relative;">
			<ul>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:150px;max-width:150px;">Denominaci&oacute;n</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:1080px;max-width:1080px;">
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:1080px;max-width:1080px;">Organizaciones</li>
					</ul>
					<ul style="display:block">
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">AD</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">AL</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">GA</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">41</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">42</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">43</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">44</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">45</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">46</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">47</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">50</li>
						<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:90px;max-width:90px;">51</li>
					</ul>
				</li>	
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Pensionistas</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Reh/Contr</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Total</li>
				<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:60px;max-width:60px;">&nbsp;</li>
			</ul>
		</div>
	</div>
	<div class="grid" style="height: 150px;">
	<div class="gridBody" ></div>
	<div class="gridReference">
		<ul>
			<li style="min-width:150px;max-width:150px;"><input type="text" size="16" name="item_1"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_1"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_2"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_3"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_4"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_5"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_6"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_7"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_8"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_9"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_10"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_11"></li>
			<li style="min-width:90px;max-width:90px;"><input type="text" id="item" size="8" name="item_12"></li>
			<li style="min-width:100px;max-width:100px;"><input type="text" id="item" size="10" name="item_1"></li>
			<li style="min-width:100px;max-width:100px;"><input type="text" id="item" size="10" name="item_1"></li>
			<li style="min-width:100px;max-width:100px;"></li>
			<li style="min-width:60px;max-width:60px;"><button name="btnEli">Eliminar</button><button name="btnAdd">Agregar</button></li>
		</ul>
	</div>
	</div>