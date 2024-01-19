<!-- Lo de Favio --> 
<!--
<form class="form-horizontal col-lg-6" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de Equipo</label>
		<div class="col-sm-8">
			<select class="form-control" name="tipo">
				<option value="PC">Computadora/Laptop</option>
				<option value="RE">Reloj de Marcaci&oacute;n</option>
				<option value="MO">Router o Modem</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Local (*)</label>
		<div class="input-group col-sm-8">
			<span class="form-control" name="local"></span>
			<span class="input-group-btn">
				<button name="btnLocal" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Oficina (*)</label>
		<div class="col-sm-8">
			<input type="text" class="form-control typeahead" name="oficina">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Persona Encargada (*)</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="encargado">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre del Equipo</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="nomb">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">MAC Addres</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="mac">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Direcci&oacute;n IP (*)</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="ip">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Fecha de Adquisici&oacute;n</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fec">
		</div>
	</div>
</form>
<form class="form-horizontal col-lg-6" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de Proxy</label>
		<div class="col-sm-8">
			<select class="form-control" name="proxy">
				<option value="R">Restricci&oacute;n</option>
				<option value="P">Permitido</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Windows</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="windows">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Licencia Windows</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="windows_lic">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Office</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="office">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Licencia Office</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="office_lic">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">ID TeamViewer</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="team_id">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Password TeamViewer</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="team_pass">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Observaciones</label>
		<div class="col-sm-8">
			<textarea class="form-control" name="observ"></textarea>
		</div>
	</div>
</form>
<hr />
-->
<style type="text/css">
	@import "compass/css3";
	.container{
		display: flex;
	}
	.rTabla{
		border: #404040 1px;
	}
	.rTabla thead tr th{
		background:#404040;
		color:#eee;		
	}
	.rCabecera{
		display: flex;
		justify-content:center;
		align-items:center;		
	}
	.rCabecera div{
		padding-left:10px;
	}
	.rTabla{
		width: 100%;
	}
	.rBotonEditar{
		width: 100%;
		text-align: right;
		margin-top:15px;
		margin-bottom:15px;
	}
	.rHeader{
		text-align:center;
		background:#83E5C2;
		color: black;
	}
	.rCelda{
		display: flex;
		width: 100%;
	}
	
	.rCelda div{
		width: 50%;
	/*	border:1px red solid; */
		text-align:center;
	}
	.rCeldaIzq{
		display: flex;
		justify-content:flex-end;
		padding-right:5px;
		font-weight:bold;
	}
	.rCeldaDer{
		display: flex;
		justify-content:flex-start;
		padding-left:5px;
	}
	.rverde{
		color:#1ab394;
	}
	tbody{
		border-top:1px red solid;
	}
	.rFooter div{
		width: 100%;
		min-height:100px;
		max-height: auto;
		margin-top:5px;
		border:2px #83E5C2 solid;
	}
	</style>

<div class="rBotonEditar">
	<button>Editar</button>
</div>
	<div class="rContainer">
		<table class="rTabla table-bordered">
			<thead>
				<tr>
					<th>
						<div class="rCabecera rCa1">
							<div class=""><h3><strong>ID</strong></h3></div>
							<div class="rverde"><h3>245792314</h3></div>
						</div>
					</th>
					<th>
						<div class="rCabecera rCa1">
							<div><h3><strong>Local</strong></h3></div>
							<div class="rCabecera" style="flex-direction: column">
								<div style="display: flex;">
									<div><strong>Edificio :</strong></div>
									<div class="rverde">Administracion Central</div>
								</div>
								<div style="display: flex;">
									<div><strong>Piso :</strong></div>
									<div class="rverde">2</div>
								</div>
							</div>
						</div>
					</th>
					<th class="rCabecera rCa3">
						<div class="rCabecera rCa1">
							<div class=""><h3><strong>IP</strong></h3></div>
							<div class="rverde"><h3>192.168.1.12</h3></div>
						</div>
					</th>
				</tr>
				
			</thead>
			<!-- Cuerpo -->
			<tbody>
				<!-- Cabecera despues -->
				<tr class="rHeader">
					<td><h4>PERSONAL</h4></td>
					<td><h4>HARDWARE</h4></td>
					<td><h4>SOFTWARE</h4></td>
				</tr>
				<!-- Primera Fila -->
				<tr class="rFilas rf1">
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">A Cargo de:</div>
							<div class="rCeldaDer">Ricardo</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Procesador:</div>
							<div class="rCeldaDer">Core Duo</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Sistema Operativo:</div>
							<div class="rCeldaDer">Win10</div>							
							<div style="text-align:center">XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</div>
						</div>
					</td>
				</tr>
				<!-- Segunda Fila -->
				<tr class="rFilas rf2">
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Usuario:</div>
							<div class="rCeldaDer">Murillo</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">RAM:</div>
							<div class="rCeldaDer">8GB</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Antivirus:</div>
							<div class="rCeldaDer">Fsecure</div>							
							<div style="text-align:center">XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</div>
						</div>
					</td>
				</tr>
				<!-- Tercera Fila -->
				<tr class="rFilas rf3">
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">AD:</div>
							<div class="rCeldaDer">rllerena</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">CASE:</div>
							<div class="rCeldaDer">ASUS</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Office:</div>
							<div class="rCeldaDer">2010</div>							
							<div style="text-align:center">XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</div>
						</div>
					</td>
				</tr>
				<!-- Cuarta Fila -->
				<tr class="rFilas rf4">
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Oficina:</div>
							<div class="rCeldaDer">Estadistica e informatica</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Disco Duro:</div>
							<div class="rCeldaDer">1TB</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Software extra:</div>
							<div class="rCeldaDer">Otro software</div>							
							<div style="text-align:center">XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</div>
						</div>
					</td>
				</tr>
				<!-- Quinta Fila -->
				<tr class="rFilas rf5">
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">Tipo de Contrato:</div>
							<div class="rCeldaDer">Locacion</div>
						</div>
					</td>
					<td>
						<div class="rCelda">
							<div class="rCeldaIzq">IP</div>
							<div class="rCeldaDer">192.168.1.12</div>
						</div>
					</td>
				</tr>
				<!-- Sexta Fila Fila -->
				<tr class="rFilas rf6">
					<td>
				
					</td>
				</tr>
				<!-- Septima Fila Fila -->
			</tbody>
			</table>
			<div class="rFooter">
				<div><h3>Observaciones</h3></div>
			</div>
			<div class="rFooter">
				<div><h3>Historial</h3></div>
			</div>
	</div>
