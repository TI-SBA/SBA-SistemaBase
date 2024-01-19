<?php global $f; ?>
<form class="form-horizontal" role="form">
				<div name="paciente"><?php $f->response->view('mg/enti.mini'); ?></div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Historia Clinica: </label>
					<div class="col-sm-8">
						<span class="form-control" name="his_cli" requerid style="width:250px"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Motivo de la Consulta</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="moti"></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label">Referencia Familiar</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="refe"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Referencia Personal</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="repa"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Historia Individual</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="his"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Organicidad</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="orga"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Inteligencia:</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="inte"></textarea>
					</div>
				</div>

			<div class="col-lg-12">
					<div name="gridFami"></div>
			</div>
			<div class="form-horizontal" role="form" >
				<div class="form-group">
					<label class="col-sm-4 control-label">Personalidad:</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="perso"></textarea>
					</div>
				</div>
			<div class="form-group">
					<label class="col-sm-4 control-label">Conclusiones:</label>
					<div class="col-sm-8">
						<textarea cols="30" rows="3" class="form-control" name="conclu"></textarea>
					</div>
			</div>
				
			</div>
			<div class="form-group">
				<div name="gridEvol"></div>
			</div>
			
</form>

