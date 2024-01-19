<div class="container-fluid">

    <div class="col-md-3">
        <div class="full-height-scroll">
            <div class="list-group">
                <a data-toggle="tab" href="#section1" class="list-group-item">
                    <strong>Lista de Documentos</strong>
                    <div class="small m-t-xs">
                        <p>
                            Lista de Documentos
                        </p>
                    </div>
                </a>
            </div>
            <div class="list-group">
                <a data-toggle="tab" href="#section7" class="list-group-item">
                    <strong>Documentos Historicos</strong>
                    <div class="small m-t-xs">
                        <p>
                            Reporte de Documentos Historicos
                        </p>
                    </div>
                </a>
            </div>
            <div class="list-group">
                <a data-toggle="tab" href="#section2" class="list-group-item">
                    <strong>Recepcion de Documentos</strong>
                    <div class="small m-t-xs">
                        <p>
                            Recepcion de Documentos
                        </p>
                    </div>
                </a>
            </div>
            <div class="list-group">
                <a data-toggle="tab" href="#section3" class="list-group-item">
                    <strong>Estadisticas de Documentos Recibidos por Año</strong>
                    <div class="small m-t-xs">
                        <p>
                            Estadisticas de Documentos Recibidos por Año
                        </p>
                    </div>
                </a>
            </div>
            <div class="list-group">
                <a data-toggle="tab" href="#section4" class="list-group-item">
                    <strong>Recepcion de Documentos por Direcciones</strong>
                    <div class="small m-t-xs">
                        <p>
                            Recepcion de Documentos por Direcciones
                        </p>
                    </div>
                </a>
            </div>
            <div class="list-group">
                <a data-toggle="tab" href="#section5" class="list-group-item">
                    <strong>Depuraciones</strong>
                    <div class="small m-t-xs">
                        <p>
                            Depuraciones
                        </p>
                    </div>
                </a>
            </div>
            <div class="list-group">
                <a data-toggle="tab" href="#section6" class="list-group-item">
                    <strong>Estadistica de Documentos Registrados por Año</strong>
                    <div class="small m-t-xs">
                        <p>
                            Estadistica de Documentos Registrados por Año
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
                            <h3>Lista de Documentos</h3>
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
                    <!--************************-->
                    <!--************************-->
                    <div id="section7" class="tab-pane">
                        <form>
                            <h3>Documentos Historicos</h3>
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
                    <!--************************-->
                    
                    <div id="section2" class="tab-pane">
                        <form>
                            <h3>Recepcion de Documentos</h3>
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
                       <div id="section3" class="tab-pane">
                        <form>
                            <h3>Estadistica de Documentos recibidos por Año</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>A&ntilde;o</label>
                                        <select name="ano" class="form-control">
                                            <option value="2015">2015</option>
                                            <option value="2016" selected>2016</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                            <option value="2021">2021</option>
                                        </select>
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
                     <div id="section4" class="tab-pane">
                        <form>
                            <h3>Recepcion de Documentos por Direcciones</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>A&ntilde;o</label>
                                        <select name="ano" class="form-control">
                                            <option value="2015">2015</option>
                                            <option value="2016" selected>2016</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                            <option value="2021">2021</option>
                                        </select>
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
                    
                     <!--************************-->
                     
                    <div id="section5" class="tab-pane">
                        <form>
                            <h3>Depuraciones</h3>
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
                                        
                    <div id="section6" class="tab-pane">
                        <form>
                            <h3>Estadistica de Documentos Registrados por Año</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>A&ntilde;o</label>
                                        <select name="ano" class="form-control">
                                            <option value="2015">2015</option>
                                            <option value="2016" selected>2016</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                            <option value="2021">2021</option>
                                        </select>
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
</div>