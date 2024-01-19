<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Historia Clinica</label>
			<div class="col-md-3">
				<span class="form-control" name="his"  style="width:250px"></span>
			</div>
		</ddiv>
	</div>
	<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Modulo: </label>
			<div class="col-sm-8">
				<span class="form-control" name="modulo" requerid style="width:250px"></span>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Pabellon</label>
			<div class="col-sm-8">
				<select class="form-control" name="pabe" type = "text" style="width:300px" required>
					<option value="Intermedio">Intermedio</option>
					<option value="Intensivo">Intensivo</option>
				</select>
			</div>
	</div>

				<div class="form-group">
					<label class="col-sm-4 control-label">Numero de Ingreso: </label>
						<div class="col-sm-8 input-group">
							<input type="text" class="form-control" name="ning"   style="width:300px" required >
						</div>
				</div>
				
				<div class="form-group" >
					<label class="col-sm-4 control-label">Fecha de Ingreso: </label>
						<div class="col-sm-8 input-group">
							<input type="text" class="form-control" name="fecini"   style="width:300px"  >
						</div>
				</div>
				<div class="form-group" >
					<label class="col-sm-4 control-label">Fecha de Egreso: </label>
						<div class="col-sm-8 input-group">
							<input type="text" class="form-control" name="fegr"   style="width:300px"  >
						</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Tipo de Hospitalizacion</label>
						<div class="col-sm-8">
							<select class="form-control" name="tipo" type = "text" style="width:300px" required>
								<option value="C">Completa</option>
								<option value="P">Parcial</option>
							</select>
						</div>
				</div>
				<div class="form-group">
					<ddiv>
						<label class="col-sm-4 control-label">Diagnostico Inicial</label>
							<div class="col-md-3">
								<span class="form-control" name="diag"  style="width:250px"></span>
							</div>
							<div class="col-sm-5">
								<span class="input-group-btn">
									<button name="btnDiagini" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
								</span>
							</div>
					</ddiv>
				</div>
				<div class="form-group" >
					<label class="col-sm-4 control-label">CIE.10: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="cie10"   style="width:300px" required ></span>
						</div>
				</div>
	
				<div class="form-horizontal" role="form" >
					<div class="form-group">
						<label class="col-sm-4 control-label">Motivo de la Consulta:</label>
							<div class="col-sm-8">
								<textarea cols="30" rows="3" class="form-control" name="moti"></textarea>
							</div>
					</div>
				</div>
			
				<div class="form-group" >
					<label class="col-sm-4 control-label">Fecha de Nacimiento: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="fena"   style="width:300px" required style="display:none"></span>
						</div>
				</div>
				
								<div class="form-group"  style="display:none;"  >
					<label class="col-sm-4 control-label">Domicilio: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="domi"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group"  style="display:none;" >
					<label class="col-sm-4 control-label">Religion: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="reli"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group" style="display:none;"  >
					<label class="col-sm-4 control-label">Estado Civil: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="es_civil"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group" style="display:none;"  >
					<label class="col-sm-4 control-label">Ocupacion: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="ocup"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group"  style="display:none;"  >
					<label class="col-sm-4 control-label">Timpo de Desocupacion: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="deso"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group"  style="display:none;"  >
					<label class="col-sm-4 control-label">Tiempo de Residencia: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="resi"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group" style="display:none;"   >
					<label class="col-sm-4 control-label">Grado de Instruccion: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="instr"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group" style="display:none;"  >
					<label class="col-sm-4 control-label">Idioma: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="idio"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
								<div class="form-group" >
					<label class="col-sm-4 control-label">Referido Por: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="refe"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
 				<div name="apoderado"><?php $f->response->view('mg/enti.mini'); ?></div> </div>
				
								<div class="form-group"  style="display:none;"  >
					<label class="col-sm-4 control-label">Direccion: </label>
						<div class="col-sm-8 input-group">
							<span type="text" class="form-control" name="dire"   style="width:300px" required style="visibility:hidden"></span>
						</div>
				</div>
				<div class="form-group"  style="display:none;"  >
					<label class="col-sm-4 control-label">Telefono: </label>
						<div class="col-sm-8 input-group"> 
							<span type="text" class="form-control" name="tele"   style="width:300px" required style="visibility:hidden" ></span>
						</div>
				</div>
			

</form>

