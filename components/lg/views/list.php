<div class="wrapper wrapper-content animated fadeInRight gray-bg">
    <div class="row">
        <div class="col-lg-6">
            <div class="contact-box">
            	<h3>Resumen</h3>
                <form class="form-horizontal" role="form">
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Periodo</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="periodo">
						</div>
					</div>
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Mostrar por</label>
						<div class="input-group col-sm-8">
							<select class="form-control" name="mostrar">
								<option value="C">Cuentas Contables</option>
								<option value="P">Clasificadores de Gasto</option>
							</select>
						</div>
					</div>
					<button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
					<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="contact-box">
                <h3>Resumen - Validaci&oacute;n</h3>
                <form class="form-horizontal" role="form">
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Periodo</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="periodo">
						</div>
					</div>
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Entradas</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="periodo">
						</div>
					</div>
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Salidas</label>
						<div class="input-group col-sm-8">
							<input type="text" class="form-control" name="periodo">
						</div>
					</div>
		    		<div class="form-group">
						<label class="col-sm-4 control-label">Cuenta Contable</label>
						<div class="input-group col-sm-8">
							<span class="form-control" name="cuenta"></span>
							<span class="input-group-btn">
								<button name="btnCta" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</div>
					<button class="btn btn-success" name="btnPdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>