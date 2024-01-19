<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<div name="responsable"><?php $f->response->view('mg/enti.mini'); ?></div>
	</div>
	<div class="form-group">
		<div>
			<label class="col-sm-4 control-label">Caja: </label>
				<div class="col-sm-3">
					<span class="form-control" name="caja"  style="width:200px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnCaja" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Deber anterior: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="deber_anterior" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Haber anterior: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="haber_anterior" style="width:200px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Saldo anterior: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="saldo_anterior" style="width:200px">
		</div>
	</div>



	<div class="form-group" name="rendicion">
		<div><label class="col-sm-1 control-label">Rendici&oacute;n: </label></div>
		<br>
		<div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Tipo de rendici&oacute;n: </label>
				<div class="col-sm-4">
					<select class="form-control" name="rendicion_tipo" type = "text" style="width:200px" >
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
			<div class="form-group"  data-provide="datepicker">
				<label class="col-sm-4 control-label">Fecha de rendicion: </label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="rendicion_fec" style="width:200px">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Numero de Rendicion: </label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="rendicion_num" style="width:200px">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Monto de rendicion: </label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="rendicion_monto" style="width:200px">
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Saldo inicial: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="saldo_inicial" style="width:200px">
		</div>
	</div>



	<!--<div class="form-group">
		<label class="col-sm-4 control-label">Estado: </label>
			<div class="col-sm-8">
				<select class="form-control" name="estado" type = "text" style="width:200px" >
							<option value="0">Aperturado</option>
							<option value="1">Cerrado</option>
							
				</select>
			</div>
	</div>
	-->
	
	
	
</form>