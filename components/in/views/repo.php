<div class="wrapper wrapper-content animated fadeInRight gray-bg">
    <div class="row">
        <div class="col-sm-4 col-lg-6">
            <div class="contact-box">
                <h3>Resumen de Ingresos por Playas</h3>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Periodo</label>
                        <div class="input-group col-sm-8">
                            <input type="text" class="form-control" name="periodo">
                        </div>
                    </div>
                    <button class="btn btn-info" data-type="graph"><i class="fa fa-bar-chart"></i> Generar Gr&aacute;fico</button>
                    <button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
                    <!--<button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>-->
                </form>
                <div class="clearfix"></div>
                <canvas id="myChart"></canvas>
            </div>
        </div>
        <div class="col-sm-4 col-lg-6">
            <div class="contact-box">
            	<h3>Listado de Comprobantes por Playas</h3>
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
        <div class="col-sm-4 col-lg-6">
    		<div class="contact-box" id="section3">
    			<h3>Generar Liquidaci&oacute;n</h3>
    			<div class="form-horizontal">
    				<div class="form-group">
    					<label>Seleccionar Arrendatario</label>
    					<div class="input-group">
    						<input type="text" name="arrendatario" placeholder="Buscar Arrendatario" class="form-control" disabled="">
    						<span class="input-group-btn">
    							<button name="btnSelectEnti" class="btn btn-primary">Buscar!</button>
    						</span>
    					</div>
    				</div>
    				<div class="form-group">
    					<label>Seleccionar Inmueble</label>
    					<select name="inmueble" class="form-control">
    					</select>
    				</div>
                    <div class="form-group">
                        <label>Pagos Desde: </label>
                        <input type="text" name="fecini_pagos" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Pagos Hasta: </label>
                        <input type="text" name="fecfin_pagos" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Moras Hasta: </label>
                        <input type="text" name="fecfin_mora" class="form-control">
                    </div>
    				<div class="form-group">
    					<button name="btnGeneratePDF" class="btn btn-primary"> Generar Reporte</button>
    				</div>
    			</div>
    		</div>
    	</div>
        <div class="col-sm-4 col-lg-6">
            <div class="contact-box" id="section4">
                <h3>Record de Pagos</h3>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label>Seleccionar Arrendatario</label>
                        <div class="input-group">
                            <input type="text" name="arrendatario" placeholder="Buscar Arrendatario" class="form-control" disabled="">
                            <span class="input-group-btn">
                                <button name="btnSelectEnti" class="btn btn-primary">Buscar!</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Inmueble</label>
                        <select name="inmueble" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Contrato</label>
                        <select name="contrato" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Formato de Salida</label>
                        <select name="formato" class="form-control">
                            <option value="pdf">PDF</option>
                            <option value="xls">XLS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button name="btnGeneratePDF" class="btn btn-primary"> Generar Reporte</button>
                    </div>
                </div>
            </div>
            <div class="contact-box" id="section4_1">
                <h3>Record de Pagos (Actas)</h3>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label>Seleccionar Arrendatario</label>
                        <div class="input-group">
                            <input type="text" name="arrendatario" placeholder="Buscar Arrendatario" class="form-control" disabled="">
                            <span class="input-group-btn">
                                <button name="btnSelectEnti" class="btn btn-primary">Buscar!</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Acta</label>
                        <select name="contrato" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Formato de Salida</label>
                        <select name="formato" class="form-control">
                            <option value="pdf">PDF</option>
                            <!--<option value="xls">XLS</option>-->
                        </select>
                    </div>
                    <div class="form-group">
                        <button name="btnGeneratePDF" class="btn btn-primary"> Generar Reporte</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-lg-6">
            <div class="contact-box" id="section5">
                <h3>Situaci&oacute;n Actual de los Inmuebles</h3>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label>Tipo de Local</label>
                        <select name="tipo" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>SubLocal</label>
                        <select name="sublocal" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <button name="btnGeneratePDF" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Generar Reporte PDF</button>
                    </div>
                    <div class="form-group">
                        <button name="btnGenerateXLS" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Generar Reporte Excel</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-lg-6">
            <div class="contact-box" id="section6">
                <h3>Reporte de Deudores</h3>
                <div class="form-horizontal">
                    <!--<div class="form-group">
                        <label class="col-sm-4 control-label">Desde</label>
                        <div class="input-group col-sm-8">
                            <input type="text" class="form-control" name="periodo">
                        </div>
                    </div>-->
                    <div class="form-group">
                        <button name="btnGeneratePDF" class="btn btn-primary"> Generar Reporte</button>
                    </div>
                </div>
            </div>
            <div class="contact-box" id="section7">
                <h3>Contratos por vencer</h3>
                <div id="grid_cont_x_venc"></div>
            </div>
        </div>
        <div class="col-sm-4 col-lg-6" name="repoListInmu">
            <div class="contact-box" id="section7">
                <h3>Listado de Inmuebles</h3>
                <form class="form-horizontal" role="form">
                    <!--<button class="btn btn-info" data-type="graph"><i class="fa fa-bar-chart"></i> Generar Gr&aacute;fico</button>
                    <button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>-->
                    <button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-sm-4 col-lg-6" name="repoListArreInmu">
            <div class="contact-box" id="section8">
                <h3>Listado de Arrendatarios por Inmuebles</h3>
                <form class="form-horizontal" role="form">
                    <button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6" name="repoRegiVentas">
            <div class="contact-box" id="section9">
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
        <div class="col-lg-6" name="repoDaot">
            <div class="contact-box" id="section10">
                <h3>DAOT</h3>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Periodo</label>
                        <div class="input-group col-sm-8">
                            <input type="text" class="form-control" name="ano">
                        </div>
                    </div>
                    <button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button>
                    <button class="btn btn-success" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- REPORTE DE CONTINUIDAD DE COMPROBANTES ELECTRÓNICOS Y MANUALES -->
        <div class="col-lg-6" name="repoContComp">
            <div class="contact-box" id="section_continuidad">
                <h3>Reporte de continuidad de comprobantes (continuidad)</h3>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Periodo inicial</label>
                        <div class="input-group col-sm-8">
                            <input type="text" class="form-control" name="fecini_comp">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Periodo final</label>
                        <div class="input-group col-sm-8">
                            <input type="text" class="form-control" name="fecfin_comp">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Tipo de caja</label>
                        <div class="input-group col-sm-8">
                            <select name="tipo_caja" class="form-control">
                                <option value="A">Alquileres</option>
                                <option value="P">Playas</option>
                            </select>
                        </div>
                    </div>
                    <!-- <button class="btn btn-success" data-type="pdf"><i class="fa fa-file-pdf-o"></i> Generar PDF</button> -->
                    <button class="btn btn-success" name="btnGenerateXLS" data-type="xls"><i class="fa fa-file-excel-o"></i> Generar Excel</button>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6" name="repoContComp">
            <div class="contact-box" id="section_contingencia">
                <form>
                    <h3>GENERACION DEL REPORTE DE CONTINGENCIA MANUAL - PEI</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha</label>
                                <div class="input-group col-sm-16">
                                    <input type="text" class="form-control" name="fecha">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" name="btnImprimir" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6" name="repoEstaDeud>
            <div class="contact-box" id="section_estadodeudor">
                <form>
                    <h3>*GENERACION DEL REPORTE DE ESTADO DE DEUDORES</h3>
                    <!-- <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha</label>
                                <div class="input-group col-sm-16">
                                    <input type="text" class="form-control" name="fecha">
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" name="btnImprimir" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>