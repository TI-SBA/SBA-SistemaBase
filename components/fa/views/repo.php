<!--<div class="wrapper wrapper-content animated fadeInRight gray-bg">
    <div class="row">
    	<div class="col-lg-6">
            <div class="contact-box">
            	<h3>Reporte de Hospitalizaciones</h3>
                <form class="form-horizontal" role="form">
					<div class="form-group">
						<label class="col-sm-4 control-label">Tipo</label>
						<div class="input-group col-sm-8">
							<select class="form-control" name="tipo">
								<option value="P">Parcial</option>
								<option value="C">Completa</option>
							</select>
						</div>
					</div>
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Inicio</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="ini">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Final</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="fin">
						</div>
					</div>
					<button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
    	<div class="col-lg-6">
            <div class="contact-box">
            	<h3>Reporte de Altas</h3>
                <form class="form-horizontal" role="form">
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Inicio</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="ini">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Final</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="fin">
						</div>
					</div>
					<button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="contact-box">
            	<h3>Registro de Ventas</h3>
                <form class="form-horizontal" role="form">
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Periodo</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="periodo">
						</div>
					</div>
					<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>-->
<div class="wrapper wrapper-content animated fadeInRight gray-bg">
    <div class="row">
    	<div class="col-lg-6" name="repo1">
            <div class="contact-box">
            	<h3>Reporte de Movimientos</h3>
                <form class="form-horizontal" role="form">
		<div class="input-group">
			<span class="input-group-addon">Producto</span>
			<span class="form-control" name="prod"></span>
			<span class="input-group-btn">
				<button class="btn btn-info" type="button" name="btnProd"><i class="fa fa-search"></i> Seleccionar</button>
			</span>
		</div>
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Inicio</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="fecini">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Final</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="fecfin">
						</div>
					</div>
					<button class="btn btn-success" data-type="xls" name="btnGenerar"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6" name="repo2">
            <div class="contact-box">
            	<h3>Registro de Ventas</h3>
                <form class="form-horizontal" role="form">
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Periodo</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="periodo">
						</div>
					</div>
					<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6" name="repo3">
            <div class="contact-box">
            	<h3>Listado de Productos</h3>
                <form class="form-horizontal" role="form">
					<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
	</div>
</div>