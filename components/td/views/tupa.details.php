<div class="tmp_tupa">
	<fieldset>
		<div class="row">
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">C&oacute;digo</span>
					<span class="form-control" name="item"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">Dependencia</span>
					<span class="form-control" name="organizacion"></span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="input-group">
					<span class="input-group-addon">T&iacute;tulo</span>
					<span class="form-control" name="titulo"></span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">Modalidades</span>
					<select class="form-control" name="modalidades">
						<option value="0">Sin modalidades</option>
						<option value="1">Con modalidades</option>
					</select>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">Observaciones</span>
					<span class="form-control" name="notas"></span>
				</div>
			</div>
		</div>
	</fieldset>
	<hr />
	<div role="tabpanel" name="div_mods">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#mod1" aria-controls="mod1" role="tab" data-toggle="tab">Modalidad 1</a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="mod1">
				<div class="row">
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon">Modalidad</span>
							<span class="form-control" name="desc_pro"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="input-group">
							<span class="input-group-addon">Item</span>
							<span class="form-control" name="item_pro"></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="input-group">
							<span class="input-group-addon">URL de descarga</span>
							<span class="form-control" name="url_pro"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon">Calificaci&oacute;n</span>
							<select class="form-control" name="calificacion">
								<option value="A">Autom&aacute;tica</option>
								<option value="P">Silencio positivo</option>
								<option value="N">Silencio negativo</option>
							</select>
						</div>
					</div>
				</div>
				<hr />
				<div role="tabpanel">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#tabs-1" aria-controls="tabs-1" role="tab" data-toggle="tab">Inicio y Aprobaci&oacute;n</a></li>
						<li role="presentation"><a href="#tabs-2" aria-controls="tabs-2" role="tab" data-toggle="tab">Reconsideraci&oacute;n y Apelaci&oacute;n</a></li>
						<li role="presentation"><a href="#tabs-3" aria-controls="tabs-3" role="tab" data-toggle="tab">Bases legales</a></li>
						<li role="presentation"><a href="#tabs-4" aria-controls="tabs-4" role="tab" data-toggle="tab">Requisitos</a></li>
					</ul>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="tabs-1">
							<fieldset class="col-sm-6">
								<legend>Inicio de tr&aacute;mite</legend>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">&Oacute;rgano</span>
										<span class="form-control" name="inicio_tramite"></span>
									</div>
								</div>
							</fieldset>
							<fieldset class="col-sm-6">
								<legend>Aprobaci&oacute;n de tr&aacute;mite</legend>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">&Oacute;rgano</span>
										<span class="form-control" name="aprueba_tramite"></span>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">Plazo (d&iacute;as)</span>
										<span class="form-control" name="plazo_apr"></span>
									</div>
								</div>
							</fieldset>
							<hr />
						</div>
						<div role="tabpanel" class="tab-pane" id="tabs-2">
							<fieldset class="col-sm-6">
								<legend>Reconsideraci&oacute;n de tr&aacute;mite</legend>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">&Oacute;rgano</span>
										<span class="form-control" name="reclamacion_tramite"></span>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">Plazo Presentaci&oacute;n (d&iacute;as)</span>
										<span class="form-control" name="plazo_rec_pre"></span>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">Plazo Resoluci&oacute;n (d&iacute;as)</span>
										<span class="form-control" name="plazo_rec_res"></span>
									</div>
								</div>
							</fieldset>
							<fieldset class="col-sm-6">
								<legend>Apelaci&oacute;n de tr&aacute;mite</legend>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">&Oacute;rgano</span>
										<span class="form-control" name="apelacion_tramite"></span>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">Plazo Presentaci&oacute;n (d&iacute;as)</span>
										<span class="form-control" name="plazo_ape_pre"></span>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="input-group">
										<span class="input-group-addon">Plazo Resoluci&oacute;n (d&iacute;as)</span>
										<span class="form-control" name="plazo_ape_res"></span>
									</div>
								</div>
							</fieldset>
						</div>
						<div role="tabpanel" class="tab-pane" id="tabs-3">
							<div name="gridBleg"></div>
						</div>
						<div role="tabpanel" class="tab-pane" id="tabs-4">
							<div name="gridReqs"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<hr />