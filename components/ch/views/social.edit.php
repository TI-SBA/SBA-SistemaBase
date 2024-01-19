<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<ddiv>
			<label class="col-sm-1 control-label">Historia Clinica</label>
			<div class="col-md-3">
				<span class="form-control" name="his"  style="width:250px"></span>
			</div>
		</ddiv>
		<ddiv>
			<label class="col-sm-3 control-label">Triaje</label>
			<div class="col-md-3">
				<input class="form-control" name="tria"  style="width:250px">
			</div>
		</ddiv>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Modulo: </label>
			<div class="col-sm-8">
				<span class="form-control" name="modulo" requerid style="width:250px"></span>
			</div>
	</div>
	<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group">
		
		<label class="col-sm-1 control-label">Telefono</label>
			<div class="col-md-3">
				<input class="form-control" name="fono"  style="width:250px">
	</div>
		
	<ddiv>
			<label class="col-sm-1 control-label">Edad</label>
			<div class="col-md-3">
				<span class="form-control" name="edad"  style="width:250px"></span>
			</div>
		</ddiv>
	</div>

	<div class="form-group">
		<ddiv>
			<label class="col-sm-1 control-label">Sexo</label>
			<div class="col-md-3">
				<span class="form-control" name="sexo"  style="width:250px"></span>
			</div>
		</ddiv>
		<ddiv>
			<label class="col-sm-1 control-label">Grado de Instruccion</label>
			<div class="col-md-3">
				<span class="form-control" name="grad"  style="width:250px"></span>
			</div>
		
		</ddiv>

	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-3 control-label">Atencedentes</label>
			<div class="col-md-3">
				<textarea cols="30" rows="3" class="form-control" name="domi" style="width:500px"></textarea>
			</div>
		
		</ddiv>
	</div>
	<div class="col-lg-12">
		<div name="gridFami"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Rol del Paciente en la familia</label>
			<div class="col-sm-8">
				<select class="form-control" name="rol" type = "text" style="width:200px" >
							<option value="1">Padre</option>
							<option value="2">Madre</option>
							<option value="3">Hijo(a)</option>
							<option value="4">Conyugue</option>
							<option value="5">Hermano(a)</option>
							<option value="4">Otro</option>
							
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo de Conformacion Familiar</label>
			<div class="col-sm-8">
				<select class="form-control" name="tfam" type = "text" style="width:200px" >
							<option value="1">Nuclear Completa</option>
							<option value="2">Nuclear Incompleta</option>
							<option value="3">Extendida</option>
							<option value="4">Agregada</option>
							
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Persona Responsable del Paciente</label>
			<div class="col-sm-8">
				<select class="form-control" name="pres" type = "text" style="width:200px" >
							<option value="1">Madre</option>
							<option value="2">Padre</option>
							<option value="3">Hermano(a)</option>
							<option value="4">Conyugue</option>
							<option value="5">Hijo(a)</option>
							<option value="6">Otro(a)</option>
							
				</select>
			</div>
	</div>
	<div name="apoderado"><?php $f->response->view('mg/enti.mini'); ?></div> </div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Dinamica Familiar</label>
			<div class="col-sm-8">
				<select class="form-control" name="dina" type = "text" style="width:200px" >
							<option value="1">Armoniosa</option>
							<option value="2">Inestable</option>
							<option value="3">Confictiva</option>
							<option value="4">Armoniosa - Inestable</option>
							<option value="5">Inestable - Confictiva</option>
							<option value="6">Confictiva -Armoniosa</option>
							<option value="7">Confictiva -Armoniosa - Inestable	</option>
				</select>
			</div>
	</div>
	<!---  - - - - - - - - - - - - - - CUADRO- - - - - - - - - - - - - - - -->
	<div class="form-group">
		<label class="col-sm-4 control-label">¿SATISFECHO CON LA AYUDA QUE RECIBE DE SU FAMILIA CUANDO USTED TIENE PROBLEMAS?</label>
			<div class="col-sm-8">
				<select class="form-control" name="p1" type = "text" style="width:200px" >
							<option value="SI">SI</option>
							<option value="NO">NO</option>
							<option value="A VECES">A VECES</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">¿CONVERSAN ENTRE USTEDES LOS PROBLEMAS QUE TIENEN EN CASA?</label>
			<div class="col-sm-8">
				<select class="form-control" name="p2" type = "text" style="width:200px" >
							<option value="SI">SI</option>
							<option value="NO">NO</option>
							<option value="A VECES">A VECES</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">¿LAS DECISIONES IMPORTANTES SE TOMAN EN CONJUNTO EN CASA?</label>
			<div class="col-sm-8">
				<select class="form-control" name="p3" type = "text" style="width:200px" >
							<option value="SI">SI</option>
							<option value="NO">NO</option>
							<option value="A VECES">A VECES</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">¿LOS FINES DE SEMANA SON COMPARTIDOS POR TODOS LOS DE LA CASA?</label>
			<div class="col-sm-8">
				<select class="form-control" name="p4" type = "text" style="width:200px" >
							<option value="SI">SI</option>
							<option value="NO">NO</option>
							<option value="A VECES">A VECES</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">¿SIENTE QUE SU FAMILIA LO QUIERE?</label>
			<div class="col-sm-8">
				<select class="form-control" name="p5" type = "text" style="width:200px" >
							<option value="SI">SI</option>
							<option value="NO">NO</option>
							<option value="A VECES">A VECES</option>
				</select>
			</div>
	</div>
	<!---  - - - - - - - - - - - - - - CUADRO- - - - - - - - - - - - - - - -->
	<div class="form-group">
		<label class="col-sm-3 control-label">Soporte Socio - Familiar</label>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo</label>
			<div class="col-sm-8">
				<select class="form-control" name="tipo" type = "text" style="width:200px" >
							<option value="1">Material</option>
							<option value="2">Economico</option>
							<option value="3">Emocional</option>
							<option value="4">Emocional - Material</option>
							<option value="5">Emocional - Economico</option>
							<option value="6">Material - Economico</option>
							<option value="7">Material - Economico - Emocional</option>

							
							
				</select>
			</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Numero de Miembros economicamente Activos</label>
			<div class="col-md-3">
				<input class="form-control" name="nmie"  style="width:250px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Carga Familiar</label>
			<div class="col-sm-8">
				<select class="form-control" name="cfami" type = "text" style="width:200px" >
							<option value="0">SI</option>
							<option value="1">NO</option>
							
				</select>
			</div>
	</div>


	<div class="form-group">
		<label class="col-sm-4 control-label">Ingreso Economico Familiar</label>
			<div class="col-md-3">
				<input class="form-control" name="ingr"  style="width:250px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Vivienda</label>
			<div class="col-sm-8">
				<select class="form-control" name="vivi" type = "text" style="width:200px" >
							<option value="1">Propia</option>
							<option value="2">Alquilada</option>
							<option value="3">Alquiler venta</option>
							<option value="4">Por Invasion</option>
							<option value="5">Alojado</option>
							<option value="6">Otro</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Material de Construccion</label>
			<div class="col-sm-8">
				<select class="form-control" name="cons" type = "text" style="width:200px" >
							<option value="1">Noble</option>
							<option value="2">Rustica</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Numero de Habitaciones</label>
			<div class="col-md-3">
				<input class="form-control" name="nhab"  style="width:250px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Diagnostico Social Preliminar</label>
			<div class="col-md-3">
				<textarea cols="30" rows="3" class="form-control" name="dsoc" style="width:500px"></textarea>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tratamiento Social</label>
			<div class="col-sm-8">
				<select class="form-control" name="tsoc" type = "text" style="width:200px" >
							<option value="1">Terapia de Apoyo</option>
							<option value="2">Intervencion en crisis</option>
							<option value="3">Consejeria</option>
							<option value="4">Orientacion</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Pronostico Social</label>
			<div class="col-md-3">
				<textarea cols="30" rows="3" class="form-control" name="psoc" style="width:500px"></textarea>
			</div>
	</div>

				
</form>


