<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<div name="beneficiario"><?php $f->response->view('mg/enti.mini'); ?></div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Seleccionar Sesion: </label>
				<div class="col-sm-3">
					<span class="form-control" name="sesion"  style="width:200px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnSesion" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group"  data-provide="datepicker">
		<label class="col-sm-4 control-label">Fecha: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fecdoc" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Tipo: </label>
			<div class="col-sm-8">
				<select class="form-control" name="tidoc" type = "text" style="width:200px" >
					<option value="0">B.V.</option>
					<option value="1">FACT</option>
					<option value="2">REC</option>
					<option value="3">TICK</option>
					<option value="4">R.H</option>
					<option value="5">REC.C</option>
					<option value="6">F.E</option>
					<option value="7">B.E</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Numero: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="num" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Concepto: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="conce" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Programa: </label>
				<div class="col-sm-3">
					<span class="form-control" name="prog"  style="width:200px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnProg" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Oficina: </label>
				<div class="col-sm-3">
					<span class="form-control" name="ofic"  style="width:200px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnOfic" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Monto: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="mont" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Partida Presupuestaria: </label>
				<div class="col-sm-3">
					<span class="form-control" name="papre"  style="width:200px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnParti" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<!--
	<div class="form-group">
		<label class="col-sm-4 control-label">Monto Inicial: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="mini" style="width:200px">
		</div>
	</div>
	-->
</form>