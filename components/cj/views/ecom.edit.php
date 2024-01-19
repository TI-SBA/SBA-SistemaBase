<div class="row">
    <div class="col-lg-4">
        <div class="widget style1">
            <div class="row">
                <div class="col-xs-4 text-center">
                    <i class="fa fa-money fa-5x"></i>
                </div>
                <div class="col-xs-8 text-right">
                    <span name="header_tipo">COMPROBANTE DE PAGO</span>
                    <h2 name="header_numero" class="font-bold">Borrador</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="widget style1 navy-bg" name="btnSelectCaja" style="cursor: pointer">
            <div class="row">
                <div class="col-xs-4">
                    <i class="fa fa-cloud fa-5x"></i>
                </div>
                <div class="col-xs-8 text-right">
                    <span> Caja</span>
                    <h2 name="header_caja" class="font-bold">No detectado</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="widget style1 lazur-bg">
            <div class="row">
                <div class="col-xs-4">
                    <i class="fa fa-user fa-5x"></i>
                </div>
                <div class="col-xs-8 text-right">
                    <span> Responsable</span>
                    <h2 name="header_responsable" class="font-bold">No detectado</h2>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group"><label>Tipo de comprobante:</label>
                    <select name="tipo" class="form-control">
                        <option value="F" docu="FE">FACTURA ELECTRONICA</option>
                        <option value="B" docu="BE" selected="selected">BOLETA ELECTRONICA</option>
                        <option value="NC" docu="NCE">NOTA DE CREDITO ELECTRONICA</option>
                        <option value="ND" docu="NDE">NOTA DE DEBITO ELECTRONICA</option>
                        <!-- <option value="F" docu="F">FACTURA MANUAL</option> -->
                        <!-- <option value="B" docu="B">BOLETA DE VENTA MANUAL</option> -->
                        <!-- <option value="NC" docu="NC">NOTA DE CREDITO MANUAL</option> -->
                        <!-- <option value="ND" docu="ND">NOTA DE DEBITO MANUAL</option> -->
                        <!-- <option value="R" docu="R">RECIBO DE CAJA</option> -->
                        <!-- <option value="RD" docu="RD">RECIBO DEFINITIVO</option> -->
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group"><label>Serie:</label>
                    <select name="serie" class="form-control">
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group"><label>N&uacute;mero:</label>
                    <input type="text" name="numero" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group"><label>Moneda:</label>
                    <select name="moneda" class="form-control">
                        <option value="PEN">SOLES</option>
                        <option value="USD">DOLARES</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group"><label>Tipo de cambio:</label>
                    <input type="text" name="tipo_cambio" class="form-control">
                </div>
            </div> 
            <div class="col-sm-3">
                <div class="form-group"><label>Porcentaje IGV:</label>
                    <input type="text" name="porcentaje_igv" class="form-control">
                </div>
            </div>
            <!--EDWARD COMENTO ESTO, NO DESCOMENTAR HASTA QUE SE SEPA LO QUE ES-->
            <!--<div class="col-sm-3">
                <div class="form-group"><label>Detraccion:</label>
                    <input type="text" name="porcentaje_detraccion" class="form-control">
                </div>
            </div>-->
        </div>

        <div class="row" name="logistica" style="display:none;">
            <div class="col-sm-4">
                <div class="form-group"><label>Almacen:</label>
                    <select name="almacen" class="form-control">
                    </select>
                </div>
            </div>
        </div>
        <div class="row" name="notas_cd" style="display:none;"> 
            <div class="col-sm-5">
                <!--INJECTAR MOTIVO DE LA NOTA-->
                <div class="form-group"><label>Motivo de la nota:</label>
                    <select name="motivo_nota" class="form-control">
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <!--ESCRIBIR DESCRIPCION DE LA NOTA-->
                <div class="form-group"><label>Descripción de la nota:</label>
                    <input type="text" name="desc_nota" class="form-control">
                </div>
            </div>           
        </div>
        <!---******************-->
        <div class="row" name="notas_NC" style="display:none;"> 
            <div class="col-sm-5">
                <!--INJECTAR MOTIVO DE LA NOTA-->
                <div class="form-group"><label>Motivo de la nota:</label>
                <select name="motivo_nota_NC" class="form-control">
                        <option value="01">Anulacion de la operacion</option>
                        <option value="02">Anulacion por error en el RUC</option>
                        <option value="03">Correccion por error en la descripcion</option>
                        <option value="04">Descuento global</option>
                        <option value="05">Descuento por item</option>
                        <option value="06">Devolucion total</option>
                        <option value="07">Devolucion por item</option>
                        <option value="08">Bonificacion</option>
                        <option value="09">Disminucion en el valor</option>
                        <option value="10">Otros Conceptos</option>

                </select>
                </div>
            </div>
            <div class="col-sm-6">
                <!--ESCRIBIR DESCRIPCION DE LA NOTA-->
                <div class="form-group"><label>Descripción de la nota:</label>
                    <input type="text" name="desc_nota_NC" class="form-control">
                </div>
            </div>           
        </div>
        <!--******************--->
        <!---******************-->
        <div class="row" name="notas_ND" style="display:none;"> 
            <div class="col-sm-5">
                <!--INJECTAR MOTIVO DE LA NOTA-->
                <div class="form-group"><label>Motivo de la nota:</label>
                        <select name="motivo_nota_ND" class="form-control">
                         <option value="01">Intereses por mora</option>
                         <option value="02">Aumento en el valor</option>
                         <option value="03">Penalidades/ otros conceptos</option>
                        </select>
                </div>
            </div>
            <div class="col-sm-6">
                <!--ESCRIBIR DESCRIPCION DE LA NOTA-->
                <div class="form-group"><label>Descripción de la nota:</label>
                    <input type="text" name="desc_nota_ND" class="form-control">
                </div>
            </div>           
        </div>
        <!--******************--->
        <div class="row" name="refnotas_cd" style="display:none;"> 
            <div class="col-sm-3">
                <!--ESCRIBIR TIPO DE COMPROBANTE-->
                <div class="form-group"><label>Tipo de comprobante:</label>
                    <select name="tipo_paranota" class="form-control">
                        <option value="F" docu="F">FACTURA ELECTRONICA</option>
                        <option value="B" docu="B" selected="selected">BOLETA ELECTRONICA</option>
                        <option value="F" docu="F">FACTURA MANUAL</option>
                        <option value="B" docu="B">BOLETA MANUAL</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <!--ESCRIBIR SERIE DE BOLETA-->
                <div class="form-group"><label>Serie del comprobante:</label>
                    <input type="text" name="serie_paranota" class="form-control">
                </div>
            </div>  
            <div class="col-sm-3">
                <!--ESCRIBIR NUMERO DE BOLETA-->
                <div class="form-group"><label>Numero del comprobante:</label>
                    <input type="text" name="numero_paranota" class="form-control">
                </div>
            </div>         
        </div>
    </div>

    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group"><label>Tipo de documento:</label>
                    <select name="cliente_tipo_documento" class="form-control">
                        <option value="0">- VARIOS - VENTAS MENORES A S/.700.00 Y OTROS</option>
                        <option value="RUC">6 RUC - REGISTRO ÚNICO DE CONTRIBUYENTE</option>
                        <option value="DNI">1 DNI - DOC. NACIONAL DE IDENTIDAD</option>
                        <option value="CAE">4 CARNET DE EXTRANJERÍA</option>
                        <option value="PAS">7 PASAPORTE</option>
                        <option value="CED">A CÉDULA DIPLOMATICA DE IDENTIDAD</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group"><label>Num. de documento:</label>
                    <input type="text" name="cliente_num_documento" class="form-control">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group"><label>Selecci&oacute;n:</label>
                    <div class="btn-group btn-block">
                        <button type="button" class="btn btn-success" name="btnSelectCliente">CLIENTE</button>
                        <button class="btn btn-danger" type="button" name="btnClearSelectCliente"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group"><label>Razon Social / Nombres completos</label>
                    <input type="text" name="cliente_nomb" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group"><label>Direcci&oacute;n</label>
                    <input type="text" name="cliente_direccion" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group"><label>Email principal</label>
                    <input type="text" name="cliente_email_1" class="form-control">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group"><label>Email secundario</label>
                    <input type="text" name="cliente_email_2" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group"><label>Fecha de emision:</label> <input type="text" class="form-control" name="fecemi" disabled></div>
            </div>
            <div class="col-sm-4">
                <div class="form-group"><label>Fecha de vencimiento:</label> <input type="text" class="form-control" name="fecven" disabled></div>
            </div>
            <div class="col-sm-4">
                <div class="form-group"><label>Estado:</label>
                    <select name="estado_cancelado" class="form-control">
                        <option value="C">Cancelado</option>
                        <option value="P">No Cancelado</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive m-t">
    <table class="table invoice-table" name="grid">
        <thead>
            <tr>
                <th colspan="9">
                    <button name="btnAddRowAdmin" class="btn btn-success">Agregar Fila (administrador)</button>  
                    <button name="btnAddRow" class="btn btn-success" id="ver">Agregar Fila</button></th>
                </tr>
            <tr>
                <th style="width:80px;">Producto / Servicio</th>
                <th style="width:450px;">Descripci&oacute;n</th>
                <th style="width:150px;">Unidad</th>
                <th style="width:100px;">Cantidad</th>
                <th style="width:100px;">Valor Unitario</th>
                <th style="width:100px;">IGV</th>
                <th style="width:100px;">PV. Unitario</th>
                <th style="width:100px;">Precio Total</th>
                <th style="width:100px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
        <tfoot style="display:none">
            <tr id="rowReference">
                <td><button type="button" name="btnSettingsRow" class="btn btn-primary btn-lg"><i class="fa fa-cogs"></i></button></td>
                <!-- <td><textarea style="width:100%" rows="1" name="descripcion" class="input-lg form-control"></textarea></td>
                <td> -->
                <!-- LA SUNAT YA NO PERMITE SALTOS DE LINEA DESDE EL 21/12/2018, comunicado y subido el 27/12/2018 -->
                <td><input style="width:100%" type="text" name="descripcion" class="input-lg form-control"></input></td>
                <td>
                    <!--<button type="button" class="btn btn-primary btn-lg"><i class="fa fa-home"></i></button>
                    <button type="button" class="btn btn-success btn-lg"><i class="fa fa-car"></i></button>
                    <button type="button" class="btn btn-danger btn-lg"><i class="fa fa-medkit"></i></button>
                    <button type="button" class="btn btn-warning btn-lg"><i class="fa fa-hospital-o"></i></button>-->
                    <select class="input-lg form-control" name="unidad">
                        <option value="ZZ">ZZ</option>
                        <option value="NIU">NIU</option>
                    </select>
                </td>
                <!-- SE DESBLOQUEO LOS CAMPOS DE DISABLED PARA LA MANIPULACION DEL USUARIO -->
                <td><input style="width:100px;" type="text" name="cantidad" class="input-lg form-control" disabled></td>
                <td><input style="width:100px;" type="text" name="valor_unitario" class="input-lg form-control" disabled></td>
                <td><input style="width:100px;" type="text" name="igv" class="input-lg form-control" disabled></td>
                <td><input style="width:100px;" type="text" name="precio_venta_unitario" class="input-lg form-control" disabled></td>
                <td><input style="width:100px;" type="text" name="precio_total" class="input-lg form-control" disabled></td>
                <!--<td><input style="width:100px;" type="text" name="cantidad" class="input-lg form-control"></td>
                <td><input style="width:100px;" type="text" name="valor_unitario" class="input-lg form-control"></td>
                <td><input style="width:100px;" type="text" name="igv" class="input-lg form-control"></td>
                <td><input style="width:100px;" type="text" name="precio_venta_unitario" class="input-lg form-control"></td>
                <td><input style="width:100px;" type="text" name="precio_total" class="input-lg form-control"></td>-->
                <td><button class="btn btn-danger btn-lg" name="btnDeleteRow" type="button"><i class="fa fa-trash"></i></button></td>
            </tr>
        </tfoot>
    </table>
</div><!-- /table-responsive -->

<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-12">
                <textarea name="observ" class="form-control"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <br />
                <select name="porcentaje_detraccion" class="form-control">
                    <option value="0">SIN DETRACCION</option>
                    <option value="10">DETRACCION DEL 10%</option>
                </select>
            </div>
        </div>
        <table class="table table-striped table-bordered table-condensed table-hover" name="gridForm">
            <thead class="table-header">
                <tr class="warning">
                    <th style="text-align:center;">Forma de Pago</th>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">Abono</th>
                    <th style="text-align:center;">&nbsp;</th>
                </tr>
            </thead>
            <tbody style="overflow: auto">
            </tbody>
        </table>
        <div class="row">
            <div class="col-sm-6">
                <br />
                <select name="POSselector" class="form-control">
                </select>
            </div>
        </div>
        <div class="row" name="POSForm" style="display:none;">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary" name="btnPagarPOS">Calcular pago con POS</button>
                <fieldset>
                    <legend>Datos del POS</legend>
                    <input type="text" name="monto_base" class="form-control">
                    <table>
                        <tr>
                            <td><label>% Comisi&oacute;n</label></td>
                            <td width="130px"><a name="comision" style="text-decoration: underline;cursor: pointer;"></a></td>
                            <td><label>Monto comisionado</label></td>
                            <td><span name="monto_comision"></span></td>
                        </tr>
                        <tr>
                            <td><label>Proveedor</label></td>
                            <td width="130px"><a name="proveedor" style="text-decoration: underline;cursor: pointer;"></a></td>
                            <td><label>Total a imputar en el POS</label></td>
                            <td><span name="monto_total"></span></td>
                        </tr>
                        <tr>
                            <td><label>Cuenta Bancaria</label></td>
                            <td width="130px"><a name="ctban_descr" style="text-decoration: underline;cursor: pointer;"></a></td>
                            <td><label>C&oacute;digo</label></td>
                            <td><span name="ctban_cod"></span></td>
                        </tr>
                        <tr>
                            <td><label>Cuenta Contable de la comisi&oacute;n</label></td>
                            <td width="130px"><a name="pcon_descr" style="text-decoration: underline;cursor: pointer;"></a></td>
                            <td><label>C&oacute;digo</label></td>
                            <td><span name="pcon_cod"></span></td>
                        </tr>
                        <tr>
                            <td><label>Concepto del POS</label></td>
                            <td width="130px"><a name="conc_descr" style="text-decoration: underline;cursor: pointer;"></a></td>
                            <td><label>Servicio del POS</label></td>
                            <td width="130px"><a name="serv_descr" style="text-decoration: underline;cursor: pointer;"></a></td>
                        </tr>
                    </table>
                </fieldset>
                <label>Observaci&oacute;n del POS</label>
                <textarea name="observ" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <table class="table invoice-total">
            <tbody>
            <tr>
                <td><strong>Operaciones exoneradas:</strong></td>
                <td><input type="text" name="total_exoneradas" class="form-control" disabled></td>
            </tr>
            <tr>
                <td><strong>Operaciones inafectas:</strong></td>
                <td><input type="text" name="total_inafectas" class="form-control" disabled></td>
            </tr>
            <tr>
                <td><strong>Operaciones gravadas:</strong></td>
                <td><input type="text" name="total_gravadas" class="form-control" disabled></td>
            </tr>
            <tr>
                <td><strong>Operaciones gratuitas:</strong></td>
                <td><input type="text" name="total_gratuitas" class="form-control" disabled></td>
            </tr>
            <tr>
                <td><strong>IGV (<span name="igv_porc"></span>):</strong></td>
                <td><input type="text" name="total_igv" class="form-control" disabled></td>
            </tr>
            <tr>
                <td><strong>ISC:</strong></td>
                <td><input type="text" name="total_isc" class="form-control" value="0.00" disabled></td>
            </tr>
            <tr>
                <td><strong>Otros cargos:</strong></td>
                <td><input type="text" name="total_otros_cargos" class="form-control" value="0.00" disabled></td>
            </tr>
            <tr>
                <td><strong>Total Descuento global:</strong></td>
                <td><input type="text" name="descuento_global" class="form-control" value="0.00" disabled></td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td><input type="text" name="total" class="form-control" disabled></td>
            </tr>
            <tr style="display:none;">
                <td><strong>Total Detraccion:</strong></td>
                <td><input type="text" name="total_detraccion" class="form-control" value="0.00"></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>