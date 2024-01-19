<div class="panel">
    <div class="panel-heading">
        <div class="panel-title m-b-md"><h4>Datos Generales</h4></div>
    </div>
    <div class="panel-body">
    	<form class="form-horizontal" role="form">
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Producto</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="producto"></span>
					<span class="input-group-btn">
						<button name="btnProd" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">C&oacute;digo del Producto</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="cod_prod"></span>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Descripci&oacute;n</label>
				<div class="input-group col-sm-8">
					<textarea rows="2" class="form-control" name="cod_prod"></textarea>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">C&oacute;digo Patrimonial (General)</label>
				<div class="input-group col-sm-8">
					<input type="text" class="form-control" name="cod_patr">
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Tipo de Bien</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="tipo">
						<option value="AE">aaaaaa</option>
						<option value="AF">aaaaaa</option>
						<option value="BE">aaaaaa</option>
						<option value="EA">aaaaaa</option>
						<option value="BND">aaaaaa</option>
						<option value="BPD">aaaaaa</option>
						<option value="o">aaaaaa</option>
					</select>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Valor Neto (Actual)</label>
				<div class="input-group col-sm-8">
					<input type="text" class="form-control" name="valor_actual"/>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Cuenta Contable</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="cuenta"></span>
					<span class="input-group-btn">
						<button name="btnCta" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Depreciaci&oacute;n Anual</label>
				<div class="input-group col-sm-8">
					<input type="number" class="form-control" name="depre">
					<span class="input-group-btn">
						<i class="">%</i>
					</span>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Estado de conservaci&oacute;n</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="estado">
						<option value="B1">B1 - En uso</option>
						<option value="B2">B2 - Sin uso</option>
						<option value="B3">B3 - Excedente</option>
						<option value="B4">B4 - Sin movimiento</option>
						<option value="R1">R1 - En uso</option>
						<option value="R2">R2 - Sin uso</option>
						<option value="R3">R3 - Excedente</option>
						<option value="R4">R4 - Reparable</option>
						<option value="M1">M1 - Para tr&aacute;mite de baja</option>
						<option value="M2">M2 - Baja anterior</option>
					</select>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Tipo de Responsable</label>
				<div class="input-group col-sm-8">
					<select class="form-control" name="tipo_resp">
						<option value="E">Entidad</option>
						<option value="T">Texto</option>
					</select>
				</div>
			</div>
			<div class="form-group col-md-6" data-resp="E">
				<label class="col-sm-4 control-label">Entidad Responsable</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="entidad"></span>
					<span class="input-group-btn">
						<button name="btnEnt" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-6" data-resp="E">
				<label class="col-sm-4 control-label">Oficina Relacionada</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="oficina"></span>
					<span class="input-group-btn">
						<button name="btnOrga" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-6" data-resp="E">
				<label class="col-sm-4 control-label">Programa</label>
				<div class="input-group col-sm-8">
					<span class="form-control" name="programa"></span>
					<span class="input-group-btn">
						<button name="btnProg" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-6" data-resp="T">
				<label class="col-sm-4 control-label">Responsable</label>
				<div class="input-group col-sm-8">
					<input type="text" class="form-control" name="responsable"/>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label class="col-sm-4 control-label">Ubicaci&oacute;n</label>
				<div class="input-group col-sm-8">
					<textarea rows="2" class="form-control" name="ubicacion"></textarea>
				</div>
			</div>
    	</form>
    </div>
</div>