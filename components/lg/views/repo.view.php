<div class="container-fluid">

    <div class="col-md-3">
        <div class="full-height-scroll">
            <div class="list-group">
                <a data-toggle="tab" href="#section1" class="list-group-item">
                    <strong>Saldos por partida/cuenta</strong>
                    <div class="small m-t-xs">
                        <p>
                            Saldos por partida/cuenta.
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
                            <h3>Saldos por partida/cuenta</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tr>
                                            <td><label>Almacen</label></td>
                                            <td><select name="almacen"></select></td>
                                        </tr>
                                        <tr>
                                            <td><label>Partida</label></td>
                                            <td><span name="clasif"></span> <button type="button" class="btn btn-success" name="btnSelectClasif">Partida</button></td>
                                        </tr>
                                        <tr>
                                            <td><label>Cuenta</label></td>
                                            <td><span name="cuenta"></span> <button type="button" class="btn btn-success" name="btnSelectCuenta">Cuenta</button></td>
                                        </tr>
                                        <tr>
                                            <td><label>Saldo al</label></td>
                                            <td><input type="text" name="fecfin"  class="form-control"></td>
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
                </div>
            </div>
        </div>
    </div>
</div>