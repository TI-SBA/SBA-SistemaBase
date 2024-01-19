<?php
global $f;
?>
<form class="form-horizontal" role="form">
	<div name="medico"><?php $f->response->view('mg/enti.mini'); ?></div>
	<div class="form-group" >
		<label class="col-sm-4 control-label">Fecha: </label>
		<div class="col-sm-8 input-group">
			<span type="text" class="form-control" name="part"   style="width:300px" required style="display:none"></span>
		</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Paciente</label>
			<div class="col-md-3">
				<span class="form-control" name="paciente" requerid style="width:285px"></span>
			</div>
			<div class="col-sm-5">
				<span class="input-group-btn">
				<button name="btnSelPaci" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</ddiv>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Historia Clinica: </label>
		<div class="col-sm-8">
			<span class="form-control" name="ap" requerid style="width:285px"></span>
		</div>
	</div>
	<div class="form-group" style="display:none;" >
		<label class="col-sm-4 control-label">Sexo: </label>
		<div class="col-sm-8">
			<span class="form-control" name="sexo" requerid style="width:285px"></span>
		</div>
	</div>
	<div class="form-group" style="display:none;" >
		<label class="col-sm-4 control-label">Edad: </label>
		<div class="col-sm-8">
			<span class="form-control" name="edad" requerid style="width:285px"></span>
		</div>
	</div>
	<div class="form-group" style="display:none;" >
		<label class="col-sm-4 control-label">Procedencia: </label>
		<div class="col-sm-8">
			<span class="form-control" name="proce" requerid style="width:285px"></span>
		</div>
	</div>
	<h3>Resumen General</h3>
	<div class="form-group">
		<label class="col-sm-4 control-label">Estado</label>
			<div class="col-sm-8">
				<select class="form-control" name="esta" type = "text" style="width:300px" required>
					<option value="2">Nuevo</option>
					<option value="3">Continuador</option>
					<option value="5">Inter-Consulta</option>
				</select>
			</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-4 control-label">Categoria</label>
			<div class="col-sm-8">
				<select class="form-control" name="cate" type = "text" style="width:200px" >
						<option value="10">Nuevo</option>
						<option value="11">Continuador</option>
						<option value="8">Indigente</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Diagnostico Inicial</label>
				<div class="col-md-3">
					<span class="form-control" name="cie10" requerid style="width:285px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
					<button name="btnDiagini" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>


	<div class="form-group">
		<label class="col-sm-4 control-label">Nuevos: </label>
			<div class="col-sm-8">
				<input class="form-control" name="nuevo" disabled="" type="text" requerid style="width:85px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Continuadores: </label>
			<div class="col-sm-8">
				<input class="form-control" name="conti" disabled="" type="text" requerid style="width:85px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Reingresantes: </label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="reing" disabled=""  required style="width:85px">
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Total: </label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="total" disabled=""  required style="width:85px">
			</div>
	</div>


	<div class="form-group">
		<label class="col-sm-2 control-label"> </label>
		<div class="col-sm-8">
			<ddiv class="row">
				<div class="col-md-5">
					<label class="col-sm-4 control-label">Varones: </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="varon"  disabled="" required style="width:85px">
						</div>
				</div>
				<div class="col-md-5">
					<label class="col-sm-4 control-label">Mujeres: </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="mujer" disabled=""  required style="width:85px">
						</div>
				</div>
			</ddiv>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"> </label>
		<div class="col-sm-12">
			<ddiv class="row">
				<div class="col-md-3">
					<label class="col-sm-4 control-label">00-10: </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="pri" disabled="" required style="width:85px">
						</div>
				</div>
				<div class="col-md-3">
					<label class="col-sm-4 control-label">11-17: </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="seg" disabled="" required style="width:85px">
						</div>
				</div>
				<div class="col-md-3">
					<label class="col-sm-4 control-label">18-60: </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="ter" disabled="" required style="width:85px">
						</div>
				</div>
				<div class="col-md-3">
					<label class="col-sm-4 control-label">60 a +: </label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="cuar" disabled="" required style="width:85px">
						</div>
				</div>
			</ddiv>
		</div>
	</div>

	<div class="form-group" name="gridList">
		
	</div>
</form>

