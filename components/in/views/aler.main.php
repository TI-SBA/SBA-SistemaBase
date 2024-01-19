<div class="panel panel-success">
	<div class="panel-heading">
		<i class="fa fa-home"></i> Seleccione su Inmueble
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">Tipo de Local</span>
					<select class="form-control" name="tipo"></select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">SubLocal</span>
					<select class="form-control" name="sublocal"></select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">Inmueble</span>
					<select class="form-control" name="inmueble"></select>
					<span class="input-group-btn">
						<button name="btnRefresh" type="button" class="btn btn-info"><i class="fa fa-refresh"></i></button>
					</span>
					<span class="input-group-btn">
						<button name="btnExport" type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i></button>
					</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="panel-body">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<i class="fa fa-money"></i> Pagos ya vencidos
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-6" name="gridVenc"></div>
							<div class="col-lg-6" name="gridInfo"></div>
						</div>
						<div class="row">
							<div class="col-lg-6" name="gridConts"></div>
							<div class="col-lg-6" name="gridDeta"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<i class="fa fa-money"></i> Contratos sin partida registral
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-8" name="gridInmu"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-warning">
	<div class="panel-heading">
		<i class="fa fa-user"></i> Pagos proximos a vencer en 28 días
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-6" name="gridProx"></div>
			<div class="col-lg-6" name="formInfo">
				<h2 name="nomb" style="display: inline;"></h2><span name="fecreg" style="float: right;display: inline;"></span>
				<fieldset>
					<legend>Datos Generales del Arrendatario</legend>
					<table>
						<tr>
							<td><label>Arrendatario:</label></td>
							<td colspan="3"><span name="arrendatario"></span></td>
						</tr>
						<tr>
							<td><label>Tipo de entidad:</label></td>
							<td colspan="3"><span name="tipo_entidad"></span></td>
						</tr>
						<tr>
							<td><label>Doc. Identidad:</label></td>
							<td colspan="3"><span name="docidents"></span></td>
						</tr>
						<tr>
							<td><label>Domicilios:</label></td>
							<td colspan="3"><span name="domicilios"></span></td>
						</tr>
						<tr>
							<td><label>Telefonos:</label></td>
							<td colspan="3"><span name="telefonos"></span></td>
						</tr>
						<tr>
							<td><label>Emails:</label></td>
							<td colspan="3"><span name="emails"></span></td>
						</tr>
						<tr>
							<td><label>Websites:</label></td>
							<td colspan="3"><span name="websites"></span></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>Datos Generales del Inmueble</legend>
					<table>
						<tr>
							<td><label>Direcci&oacute;n del Inmueble:</label></td>
							<td colspan="3"><span name="inmu_direccion"></span></td>
						</tr>
						<tr>
							<td><label>Sublocal:</label></td>
							<td colspan="3"><span name="inmu_sublocal"></span></td>
						</tr>
						<tr>
							<td><label>Tipo:</label></td>
							<td colspan="3"><span name="inmu_tipo"></span></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>Datos Generales del Contrato</legend>
					<table>
						<tr>
							<td><label>Fecha Inicial:</label></td>
							<td colspan="3"><span name="cont_fecini"></span></td>
						</tr>
						<tr>
							<td><label>Fecha de Fin:</label></td>
							<td colspan="3"><span name="cont_fecfin"></span></td>
						</tr>
						<tr>
							<td><label>Situacion del inmueble por el contrato:</label></td>
							<td colspan="3"><span name="cont_situacion"></span></td>
						</tr>
						<tr>
							<td><label>Importe (Base Imponible):</label></td>
							<td colspan="3"><span name="cont_importe"></span></td>
						</tr>
						<tr>
							<td><label>Motivo del contrato:</label></td>
							<td colspan="3"><span name="cont_motivo"></span></td>
						</tr>
						<tr>
							<td><label>Ultima fecha de modificaci&oacute;n:</label></td>
							<td colspan="3"><span name="cont_fecmod"></span></td>
						</tr>
						<tr>
							<td><label>Ultimo en modificar:</label></td>
							<td colspan="3"><span name="cont_trabajador"></span></td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6"></div>
			<div class="col-lg-6" name="gridCont"></div>
		</div>
	</div>
</div>

