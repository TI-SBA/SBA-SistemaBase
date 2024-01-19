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
					<li style="min-width:146px;max-width:146px;">Concesiones</li>
				</ul>
			</a>
			<a class="item" name="section3">
				<ul>
					<li style="min-width:146px;max-width:146px;">Ocupantes</li>
				</ul>
			</a>
			<a class="item" name="section4">
				<ul>
					<li style="min-width:146px;max-width:146px;">Historial de Operaciones</li>
				</ul>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-center">
	<fieldset name="section1" style="height:360px;">
		<legend>Datos Generales</legend>
		<table name="general" class="table">
			<tr>
				<td><label>Pabell&oacute;n</label></td>
				<td><span name="spPabe"></span></td>
			</tr>
			<tr>
				<td><label>Piso</label></td>
				<td><span name="spPiso"></span></td>
			</tr>
			<tr>
				<td><label>Fila</label></td>
				<td><span name="spFila"></span></td>
			</tr>
			<tr>
				<td><label>N&uacute;mero</label></td>
				<td><span name="spNum"></span></td>
			</tr>
			<tr name="capas">
				<td><label>Capacidad</label></td>
				<td><span name="spCapa"></span></td>
			</tr>			
			<tr>
				<td><label>Tipo</label></td>
				<td><span name="spTipo"></span></td>
			</tr>
			<tr>
				<td><label>Registrado</label></td>
				<td><span name="spReg"></span></td>
			</tr>
			<tr>
				<td><label>Registrado por</label></td>
				<td><span name="spTrab"></span></td>
			</tr>
			<tr>
				<td><label>Estado</label></td>
				<td><span name="spEstado"></span></td>
			</tr>
			<tr name="prop">
				<td><label>Propietario</label></td>
				<td><a><span name="spProp"></span></a></td>
			</tr>
		</table>
	</fieldset>
	<fieldset name="section2" style="height:360px;overflow-y: auto;">
		<legend>Concesiones</legend>
		<table name="concesion" class="table">
			
		</table>
	</fieldset>
	<fieldset name="section3" style="height:360px;overflow-y: auto;">
		<legend>Ocupantes</legend>
		<div name="operOcup" style="height:340px;overflow-y: auto;width:102%;">
		 	<table class="tableRefOcup table" style="display:none;">
			<tr>
				<td colspan="4"><a><span name="spOcupNomb"></span></a></td>
			</tr>
			<tr>
				<td><label style="font-weight:bold;">Inhumado</label></td>
				<td style="padding-right:20px"><span name="spOcupFecinh"></span></td>
				<td><label name="spOper" style="font-weight:bold;"></label></td>
				<td><span name="spOcupFecasig"></span></td>				
			</tr>
		</table> 
		</div>
	</fieldset>
	<fieldset name="section4" style="height:360px;overflow-y: auto;">
		<legend>Historial de Operaciones</legend>
		<div class="grid" style="height:340px;overflow-y: auto;width:102%;">
			<div class="gridBody">			
			</div>
			<div class="gridReference">
				<ul>
					<li style="min-width:100px;max-width:100px; text-decoration:underline;"></li>
					<li style="min-width:80px;max-width:80px;color:#666666;" ></li>
					<li style="min-width:170px;max-width:170px;color:#666666;" ></li>
					<li style="min-width:180px;max-width:180px;"></li>
				</ul>
			</div>
		</div>
	</fieldset>
</div>
<style>
	.table tr td a{text-decoration : underline;}
	.table{max-width:700px;}
	.table tr td{padding-right:10px;padding-bottom:10px;}	
	.table tr td label{max-width:20px !important;}
	
</style>