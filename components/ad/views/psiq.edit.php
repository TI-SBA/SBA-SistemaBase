<?php global $f; ?>
<form class="form-horizontal" role="form">
				<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Historia Clinica: </label>
					<div class="col-sm-8">
						<span class="form-control" name="clini" requerid style="width:250px"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Informantes</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="info"></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label">Motivo de la Consulta</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="moti"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Historia de la Enfermedad</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="hien"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Antecedentes Personales</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="anpe"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Antecedentes Familiares</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="anfa"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Examen Mental:</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="exmen"></textarea>
					</div>
				</div>
			<div class="form-horizontal" role="form" >
				<div class="form-group">
					<label class="col-sm-4 control-label">Diagnostico:</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="diag"></textarea>
					</div>
				</div>
			<div class="form-group">
					<label class="col-sm-4 control-label">Tratamiento:</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="trat"></textarea>
					</div>
			</div>
				
			</div>
			<!--ñ
			<div class="form-group">
				<div name="gridReceta"></div>
			</div>
			-->
			<div class="form-group">
				<div name="gridEvol"></div>
			</div>
			
</form>


