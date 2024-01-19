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
	<div class="row" id="section1">
    	<div class="col-lg-6">
            <div class="contact-box">
            	<h3>Reporte de Kardex</h3>
	            <form class="form-horizontal" role="form">
					<div class="form-group">
						<label>Producto</label>
						<div class="input-group">
							<input type="text" name="producto" placeholder="Buscar Producto" class="form-control" disabled="">
							<span class="input-group-btn">
								<button name="btnSelectProd" type="button" class="btn btn-primary">Buscar!</button>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Almacen</label>
						<div class="input-group col-sm-8">
							<select name="almacen" class="form-control">
								
							</select>
						</div>
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
					<!-- <button class="btn btn-success" name="btnImprimir"><i class="fa fa-file-excel-o"></i> Imprimir</button> -->
					<button class="btn btn-success" name="btnKardex"><i class="fa fa-file-excel-o"></i> Imprimir</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
	</div>
    <div class="row">
    	<!-- <div class="col-lg-6">
            <div class="contact-box">
            	<h3>Reporte de Movimientos</h3>
                <form class="form-horizontal" role="form">
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
					<button class="btn btn-success" name="btnMovimientos" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button> -->
					<!-- <button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button> -->
            	<!-- </form>
                <div class="clearfix"></div>
            </div>
        </div> -->
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
					<button class="btn btn-success" name="btnVentas" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
	</div>
</div>