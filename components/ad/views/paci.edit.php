<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group" name="gridCli" >
		<label class="col-sm-4 control-label">Historia Clinica: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="his_Cli"   style="width:300px" required >
			</div>
	</div>
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-4 control-label">Fecha de Registro: </label>
		<div class="col-sm-7.5 input-group">
			<input type="text" class="form-control"  name="fe_regi" style="width:300px" required>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Categoria</label>
			<div class="col-sm-8">
				<select class="form-control" name="categoria" type = "text" style="width:300px" required>
					<option value="10">Nuevo</option>
					<!--<option value="11">Continuador</option>-->
					<option value="8">Indigente</option>
					<option value="9">Privado/Empresa</option>
					
				</select>
			</div>
	</div>
	<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Edad: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="edad"   style="width:300px" required >
			</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Pais: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="pais"   style="width:300px" required >
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Sexo</label>
			<div class="col-sm-8">
				<select class="form-control" name="sexo" type = "text" style="width:300px" required>
					<option value="0">Femenino</option>
					<option value="1">Masculino</option>
				</select>
			</div>
	</div>
	<!--<div class="form-group">
		<label class="col-sm-4 control-label">Domicilio: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="domi"   style="width:300px" required >
			</div>
	</div>-->
	<div class="form-group">
		<label class="col-sm-2 control-label">Lugar de Procedencia</label>
		<div class="col-sm-8">
			<ddiv class="row">
				<div class="col-md-4">
					<select class="form-control" name="procede_depa" type = "text" required>
					</select>
				</div>
			<div class="col-md-4">
				<label class="col-sm-4 control-label"></label>
				<div class="col-sm-8">
					<select class="form-control" name="procede_prov" type = "text" required>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<label class="col-sm-4 control-label"></label>
				<div class="col-sm-8">
					<select class="form-control" name="procede_dist" type = "text" required>
					</select>
				</div>
			</div>
			</ddiv>
		</div>
	</div>
	<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-4 control-label">Fecha de Nacimiento: </label>
		<div class="col-sm-7.5 input-group">
			<input type="text" class="form-control"  name="fecha_na" style="width:300px" required>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Lugar de Procedencia</label>
		<div class="col-sm-8">
			<ddiv class="row">
				<div class="col-md-4">
					<select class="form-control" name="luna_depa" type = "text" required>
					</select>
				</div>
				<div class="col-md-4">
					<label class="col-sm-4 control-label"></label>
					<div class="col-sm-8">
						<select class="form-control" name="luna_prov" type = "text" required>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<label class="col-sm-4 control-label"></label>
					<div class="col-sm-8">
						<select class="form-control" name="luna_dist" type = "text" required>
						</select>
					</div>
				</div>
			</ddiv>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Estado Civil</label>
			<div class="col-sm-8">
				<select class="form-control" name="es_civil" type = "text" style="width:300px" required>
					<option value="1">S/E</option>
					<option value="2">Soltero(a)</option>
					<option value="3">Casado(a)</option>
					<option value="4">Viudo(a)</option>
					<option value="5">Divorciado(a)</option>
					<option value="6">Conviviente</option>
					<option value="7">Separado(a)</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Religion</label>
			<div class="col-sm-8">
				<select class="form-control" name="reli" type = "text" style="width:300px" required>
					<option value="1">S/E</option>
					<option value="2">Cristiana</option>
					<option value="3">Catolica</option>
					<option value="4">Mormona</option>
					<option value="5">Adventista</option>
					<option value="6">Testigos Jehova</option>
					<option value="7">Ateo</option>
					<option value="8">EVANGELISTA</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Idioma</label>
			<div class="col-sm-8">
				<select class="form-control" name="idio" type = "text" style="width:300px" required>
					<option value="1">S/E</option>
					<option value="2">Castellano</option>
					<option value="3">Quechua</option>
					<option value="4">Ingles</option>
					<option value="5">AYMARA</option>
					<option value="6">QUECHUA-CASTELLANO</option>
					<option value="7">Portugues</option>
					<option value="8">otros</option>
				</select>
			</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Grado de Instruccion</label>
			<div class="col-sm-8">
				<select class="form-control" name="instr" type = "text" style="width:300px" required>
					<option value="1">S/E</option>
					<option value="2">C/Primaria</option>
					<option value="3">C/Secundaria</option>
					<option value="4">C/Tecnica</option>
					<option value="5">C/Superior</option>
					<option value="6">C/Universitaria</option>
					<option value="7">C/Jardin</option>
					<option value="8">Ed Especial</option>
				</select>
			</div>
	</div>
	<!--<div class="form-group">
		<label class="col-sm-4 control-label">Telefono: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="tele"   style="width:300px" required >
			</div>
	</div>-->
	<div class="form-group">
		<label class="col-sm-4 control-label">Referido Por: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="refe"   style="width:300px" required >
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Ocupacion</label>
			<div class="col-sm-3">
				<select class="form-control" name="ocupa" type = "text" required>
					<option value="1">S/E</option>
					<option value="2">DESOCUPADO</option>
					<option value="3">EMPLEADO</option>
					<option value="4">OBRERO</option>
					<option value="5">ESTUDIANTE</option>
					<option value="6">PROFESIONAL</option>
					<option value="7">TECNICO</option>
					<option value="8">SU CASA</option>
					<option value="9">INDEPENDIENTE</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tiempo de Desocupacion: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="t_deso"   style="width:300px" placeholder="en meses" required >
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Meses de Residencia: </label>
			<div class="col-sm-8 input-group">
				<input type="text" class="form-control" name="m_resi"   style="width:300px" placeholder="en meses" required >
			</div>
	</div>
	<div name="apoderado"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-horizontal" role="form" >
		<div class="form-group">
			<label class="col-sm-4 control-label">Motivo de la Consulta:</label>
				<div class="col-sm-8">
					<textarea cols="30" rows="3" class="form-control" name="m_consu"></textarea>
				</div>
		</div>
	</div>
</form>



