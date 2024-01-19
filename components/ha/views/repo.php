<div class="wrapper wrapper-content animated fadeInRight gray-bg">
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
					<!--<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>-->
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
					<!--<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>-->
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- ********* REPORTE DE RECIBOS DE CAJA POR COMCEPTOS*******************-->
     	
        <!--
		   <div class="col-lg-6">
		            <div class="contact-box">
		            	<h3>Reporte de Recibos por Conceptos</h3>
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
							<button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
							
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>

        -->

        <!--*********************************************************************-->
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
					<!--<button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>-->
					<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
            	</form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>



<div class="container-fluid">

    <div class="col-md-3">
        <div class="full-height-scroll">
            <div class="list-group">
                <a data-toggle="tab" href="#section1" class="list-group-item">
                    <strong>Reporte de Recibos por Conceptos</strong>
                    <div class="small m-t-xs">
                        <p>
                            Reporte por Conceptos
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="full-height-scroll white-bg border-left">
            <div class="element-detail-box">
                <div class="tab-content">
                <!--************************-->
                    <div id="section1" class="tab-pane">
                        <form>
                            <h3>Reporte de Recibos por Conceptos</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de inicio</label>
                                        <input type="text" name="fecini" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de fin</label>
                                        <input type="text" name="fecfin" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnImprimir" class="btn btn-primary">Imprimir</button>
                                </div>
                            </div>
                        </form>
                    </div>
              </div>
        </div>
    </div>
</div>