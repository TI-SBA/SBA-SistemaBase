<div class="container-fluid">

    <div class="col-md-3">
        <div class="full-height-scroll">
            <div class="list-group">
                <a data-toggle="tab" href="#section2" class="list-group-item">
                    <strong>Recibos Emitidos</strong>
                    <div class="small m-t-xs">
                        <p>
                            Recibos Emitidos
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
                    <div id="section2" class="tab-pane">
                        <form>
                            <h3>REPORTE DE RECIBOS EMITIDOS</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>A&ntilde;o</label>
                                        <select name="ano" class="form-control">
                                            <option value="2023" selected>2023</option>
											<option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mes</label>
                                        <select name="mes" class="form-control">
                                            <option value="01">ENERO</option>
                                            <option value="02">FEBRERO</option>
                                            <option value="03">MARZO</option>
                                            <option value="04">ABRIL</option>
                                            <option value="05">MAYO</option>
                                            <option value="06">JUNIO</option>
                                            <option value="07">JULIO</option>
                                            <option value="08">AGOSTO</option>
                                            <option value="09">SETIEMBRE</option>
                                            <option value="10">OCTUBRE</option>
                                            <option value="11">NOVIEMBRE</option>
                                            <option value="12">DICIEMBRE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Oficina</label>
                                        <select name="oficina" class="form-control">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnImprimir" class="btn btn-primary">Generar</button>
                                </div>
                            </div>
                            <div class="row" name="gridRecibos"></div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row" class="center-block" >
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="form-check-label" class="center-block">Aprobados</label>
                                            <input type="text" class="form-control" class="center-block" position="absolute" name="aprobado" disabled="disabled">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-check-label">Registrados</label>
                                            <input type="text" class="form-control" class="center-block" name="registrado" disabled="disabled">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>