<div class="wrapper wrapper-content animated fadeInRight gray-bg">
    <div class="row">
        <div class="col-lg-6">
            <div class="contact-box">
            	<h3>Registro de Acciones de Usuario</h3>
                <form class="form-horizontal" role="form">
		    		<div class="form-group">
						<label class="col-sm-4 control-label">M&oacute;dulo</label>
						<div class="input-group col-sm-8">
							<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
							<select class="form-control" name="modulo">
								<option value="0">Todos</option>
								<option value="MG">Maestros Generales</option>
								<option value="PO">Porter&iacute;a</option>
								<option value="TD">Tramite Documentario</option>
								<option value="CM">Cementerio</option>
								<option value="IN">Inmuebles</option>
								<option value="LG">Logistica</option>
								<option value="PR">Planificacion y Presupuesto</option>
								<option value="PE">Personal</option>
								<option value="AL">Asesoria Legal</option>
								<option value="CT">Contabilidad</option>
								<option value="CJ">Caja</option>
								<option value="MH">MOISES HERESI</option>
								<option value="TS">Tesoreria</option>
								<option value="AC">Seguridad</option>
								<option value="RE">Recursos Econ&oacute;micos</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Fecha de Inicio</label>
						<div class="input-group col-sm-8">
							<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
							<input type="text" class="form-control" name="desde">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Fecha de Fin</label>
						<div class="input-group col-sm-8">
							<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
							<input type="text" class="form-control" name="hasta">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Trabajador</label>
						<div class="input-group col-sm-8">
							<span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
							<span class="form-control" name="trabajador"></span>
							<span class="input-group-btn">
								<button name="btnSelect" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</div>
					<!--<button class="btn btn-info" data-type="graph"><i class="fa fa-bar-chart"></i> Generar Gr&aacute;fico</button>
					<button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>-->
					<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
				<!--<canvas id="myChart"></canvas>-->
            </div>
        </div>
    </div>
</div>