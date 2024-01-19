<div class="container-fluid">

    <div class="col-md-3">
        <div class="full-height-scroll">
            <div class="list-group">
                <a data-toggle="tab" href="#section1" class="list-group-item">
                    <strong>Conceptos</strong>
                    <div class="small m-t-xs">
                        <p>
                            conceptos.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section2" class="list-group-item">
                    <strong>Conceptos de trabajadores</strong>
                    <div class="small m-t-xs">
                        <p>
                            conceptos de trabajadores por programa.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section3" class="list-group-item">
                    <strong>Lista de trabajadores</strong>
                    <div class="small m-t-xs">
                        <p>
                            lista de trabajadores.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section4" class="list-group-item">
                    <strong>AFPNET</strong>
                    <div class="small m-t-xs">
                        <p>
                            AFPNET.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section5" class="list-group-item">
                    <strong>Escolaridad</strong>
                    <div class="small m-t-xs">
                        <p>
                            escolaridad.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section6" class="list-group-item">
                    <strong>PDT 0601/PLAME</strong>
                    <div class="small m-t-xs">
                        <p>
                            PDT plame.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section7" class="list-group-item">
                    <strong>Planilla electronica</strong>
                    <div class="small m-t-xs">
                        <p>
                            Planilla electronica.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section8" class="list-group-item">
                    <strong>Leyes Sociales</strong>
                    <div class="small m-t-xs">
                        <p>
                            leyes sociales.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section9" class="list-group-item">
                    <strong>Descuentos</strong>
                    <div class="small m-t-xs">
                        <p>
                            permisos particulares, faltas y tardanzas.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section10" class="list-group-item">
                    <strong>Otros descuentos</strong>
                    <div class="small m-t-xs">
                        <p>
                            descuentos afectos e inafectos a leyes.
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section11" class="list-group-item">
                    <strong>Asistencia</strong>
                    <div class="small m-t-xs">
                        <p>
                            asistencia
                        </p>
                    </div>
                </a>
                <a data-toggle="tab" href="#section13" class="list-group-item">
                    <strong>Turnos</strong>
                    <div class="small m-t-xs">
                        <p>
                            Lista de Turnos de Trabajadores
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
                    <div id="section1" class="tab-pane active">
                        <form>
                            <h3>CONCEPTOS</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td><label>Tipo de Contrato</label></td>
                                            <td><select name="tipo"></select></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnExportar" class="btn btn-primary">Imprimir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section2" class="tab-pane">
                        <form>
                            <h3>Conceptos de Trabajadores por Programa</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Concepto</td>
                                            <td colspan="2"><span name="conc"></span> <button type="button" class="btn btn-success" name="btnConc">Seleccionar</button¨></td>
                                        </tr>
                                    </table>
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
                            <h3>Lista de Trabajadores</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnExportar" class="btn btn-primary">Exportar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section4" class="tab-pane">
                        <form>
                            <h3>AFPNET</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnExportar" class="btn btn-primary">Exportar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section5" class="tab-pane">
                        <form>
                            <h3>Escolaridad</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
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
                            <h3>PDT 0601/PLAME</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnExportar" class="btn btn-primary">Exportar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section7" class="tab-pane">
                        <form>
                            <h3>Planilla electronica</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnExportar" class="btn btn-primary">Exportar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section8" class="tab-pane">
                        <form>
                            <h3>Leyes Sociales</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tipo</td>
                                            <td><select name="tipo">
                                                <option value="SNP">SNP</option>
                                                <option value="AFP">AFP</option>
                                                <option value="ESS">ESSALUD</option>
                                            </select></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnImprimir" class="btn btn-primary">Imprimir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section9" class="tab-pane">
                        <form>
                            <h3>Descuentos</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnImprimir" class="btn btn-primary">Imprimir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section10" class="tab-pane">
                        <form>
                            <h3>Otros Descuentos</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Periodo</td>
                                            <td><input type="text" name="ano"></td>
                                            <td>
                                                <select name="mes">
                                                    <option value="01">Enero</option>
                                                    <option value="02">Febrero</option>
                                                    <option value="03">Marzo</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Mayo</option>
                                                    <option value="06">Junio</option>
                                                    <option value="07">Julio</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnImprimir" class="btn btn-primary">Imprimir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section11" class="tab-pane">
                        <form>
                            <h3>Asistencia</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td>Fecha de inicio</td>
                                            <td><input type="text" name="fecini" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td>Fecha de fin</td>
                                            <td><input type="text" name="fecfin" class="form-control"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnImprimir" class="btn btn-primary">Imprimir</button>
                                    <button type="button" name="btnImprimir2" class="btn btn-primary">Imprimir marcaciones</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="section13" class="tab-pane">
                        <form>
                            <h3>Lista de Horarios</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" name="btnExportar" class="btn btn-primary">Exportar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>